<?php
/**
 * Twitter pages controller.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * Twitter pages controller.
 *
 * Responsible for handling twitter actions, such as oAuth.
 *
 * In order for the oAuth authentication to work you need to have a key and a
 * secret; these can be obtained by registering the Joind.in application with
 * twitter at: https://dev.twitter.com/apps.
 *
 * Do not forget to enter the joind.in access_token url in the 'Callback URL'
 * field in the twitter application's settings page.
 *
 * For test this should be: http://test.joind.in/twitter/access_token
 * For prod this should be: https://joind.in/twitter/access_token
 *
 * Afterwards you can set the twitter_consumer_key and twitter_consumer_secret
 * keys in the configuration.
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 *
 * @property  CI_Config     $config
 * @property  CI_Session    $session
 * @property  CI_Loader     $load
 * @property  CI_Template   $template
 * @property  User_model    $user_model
 * @property  twitter_oauth $twitter_oauth
 */
class Twitter extends Controller
{

    /**
     * Log in via Twitter
     */
    public function request_token()
    {
        $this->loadTwitterLibrary();
        $response = $this->twitter_oauth->get_request_token(
            site_url("twitter/access_token")
        );

        if ($response['token_secret'] === null) {
            show_error(
                'Twitter has returned an error, have you created an application '
                . 'with twitter and entered the correct callback URL, and the '
                . 'resulting key and secret in the configuration?'
            );
        }
        $this->session->set_userdata('twitter_token_secret', $response['token_secret']);
        redirect($response['redirect']);
    }

    public function access_token()
    {
        $this->load->model('user_model');
        $this->loadTwitterLibrary();

        $response = $this->twitter_oauth->get_access_token(
            false, $this->session->userdata('twitter_token_secret')
        );

        if (!isset($response['screen_name']))
        {
            show_error(
                'An error occurred during communication with Twitter, please'
                .' try again later'
            );
        }

        var_dump($response);
        $user_info = json_decode(file_get_contents(
            'https://api.twitter.com/1/users/show.json?screen_name='
            . $response['screen_name'] . '&include_entities=true'
        ));

        $user = current(
            $this->user_model->getUserByTwitter($response['screen_name'])
        );

        if ($user) {
            $this->_login($user);
        }

        $arr = array(
            'username'         => '',
            'password'         => '',
            'email'            => '',
            'full_name'        => $user_info->name,
            'twitter_username' => $response['screen_name'],
            'active'           => 1,
            'last_login'       => time()
        );
        $this->db->insert('user', $arr);

        // now, since they're set up, log them in a push them to the account management page
        $ret = $this->user_model->getUserByTwitter($response['screen_name']);
        $this->session->set_userdata((array)$ret[0]);
        redirect('user/manage');
    }

    protected function loadTwitterLibrary()
    {
        $this->load->library('twitter_oauth', array(
            'key'    => $this->config->item('twitter_consumer_key'),
            'secret' => $this->config->item('twitter_consumer_secret')
        ));
    }

    protected function _login($user)
    {
        $this->session->set_userdata((array)$user);

        //update login time
        $this->db->where('id', $user->ID);
        $this->db->update('user', array('last_login' => time()));

        // send them back to where they came from, either the referer if they have one, or the flashdata
        $referer = $this->input->server('HTTP_REFERER');
        $to = $this->session->flashdata('url_after_login') ? $this->session->flashdata('url_after_login') : $referer;

        // List different routes we don't want to reroute to
        $bad_routes = $this->non_forward_urls;

        foreach ($bad_routes as $route)
        {
            if (strstr($to, $route))
            {
                redirect('user/main');
            }
        }

        // our $to is good, so redirect
        redirect($to);
    }
}