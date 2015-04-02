<?php
/**
 * Facebook pages controller.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/** Required for inheritance */
require 'AuthAbstract.php';

/**
 * Facebook pages controller.
 *
 * Responsible for handling the oAuth authentication for facebook logins.
 *
 * This controller requires 2 configuration settings to be present in the
 * config.php:
 *
 * - facebook_app_id
 * - facebook_app_secret
 *
 * The values for these settings can be obtained by created a facebook
 * application at: https://developers.facebook.com/apps.
 *
 * To use this controller should the user be directed to
 * the `facebook/request_token` page,.
 *
 * @category  Joind.in
 * @package   Controllers
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2012 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 *
 * @property CI_Config   $config
 * @property CI_Input    $input
 * @property CI_Session  $session
 * @property CI_Loader   $load
 * @property CI_Template $template
 * @property User_model  $user_model
 * @property Curl        $curl
 */
class Facebook extends AuthAbstract
{
    /**
     * oAuth initialization action.
     *
     * This action will send the user to facebook and ask for their credentials.
     * After confirmation will the user be directed to the facebook/access_token
     * action, will the application be authenticated and the user authorized with
     * joind.in.
     *
     * This action uses CSRF protection with a token that is valid for 1
     * page-load only; refreshing of the access_token page will not work.
     *
     * @return void
     */
    public function request_token()
    {
        // http_build_query sanitizes the data and prevents injection attacks
        $query = http_build_query(
            array(
                'client_id'    => $this->config->item('facebook_app_id'),
                'redirect_uri' => site_url('facebook/access_token'),
                'state'        => $this->generateCsrfSecret(),
                'scope'        => 'email'
            )
        );

        redirect('http://www.facebook.com/dialog/oauth?' . $query);
    }

    /**
     * oAuth authorization action.
     *
     * This action will receive a 'code' and 'state' GET variable from facebook.
     * The code is a unique code that can be used to authorize this application
     * where the state variable is used to do a CSRF authentication.
     *
     * After a successful authorization with facebook will joind.in obtain the
     * basic user data from Facebook, sign in the user and redirect to the
     * previous page.
     *
     * @return void
     */
    public function access_token()
    {
        $this->load->model('user_model');

        // facebook returns information as GET parameters but code_ignitor
        // clears the $_GET array. It is safe to assume that spoofing the
        // $_REQUEST will have little security impact.
        $state = $_REQUEST['state'];
        if ($state != $this->getCsrfSecret()) {
            show_error(
                'Aborting authentication: A possible CSRF attack occurred'
            );
        }

        $facebook_user = $this->getFacebookUserdata(
            $this->authenticateAppWithFacebook()
        );

        if(!$facebook_user || empty($facebook_user->email)) {
            show_error('Sorry, something went wrong.  Please try another login method or contact us for help');
        }

        // return the first user with the given e-mail address
        $user = $this->user_model->getUserByEmail($facebook_user->email);

        if (!$user) {
            $this->session->set_flashdata(
                'error_msg',
                'You need to register with Joind.in and with the same email address'
                .' as your Facebook account in order to sign in with Facebook'
            );
            redirect(site_url('user/register'));
        }

        $user = current($user);
        $this->_login($user);
    }

    /**
     * Generates a CSRF secret that is valid for one request.
     *
     * @return string
     */
    protected function generateCsrfSecret()
    {
        $csrf_value = md5(uniqid(rand(), true));
        $this->session->set_userdata('facebook_csrf', $csrf_value);

        return $csrf_value;
    }

    /**
     * Returns the CSRF secret, or empty if none is present.
     *
     * @return string
     */
    protected function getCsrfSecret()
    {
        $token = $this->session->userdata('facebook_csrf');
        $this->session->unset_userdata('facebook_csrf');
        return $token;
    }

    /**
     * Authenticates this app with facebook and returns the access_token.
     *
     * @return string
     */
    protected function authenticateAppWithFacebook()
    {
        $this->load->library('curl');

        // http_build_query sanitizes the data and prevents injection attacks
        $query = http_build_query(
            array(
                'client_id'     => $this->config->item('facebook_app_id'),
                'redirect_uri'  => site_url('facebook/access_token'),
                'client_secret' => $this->config->item('facebook_app_secret'),
                'code'          => $_REQUEST['code'] // CI cleanses $_GET
            )
        );

        $response = $this->curl->simple_get(
            'https://graph.facebook.com/oauth/access_token?' . $query
        );

        if (!$response) {
            show_error(
                'An error occurred during authentication with Facebook, no '
                . 'additional information has been returned'
            );
        }

        $params = array();
        parse_str($response, $params);
        return $params['access_token'];
    }

    /**
     * Retrieves the facebook user object.
     *
     * @param string $access_token Access token
     *
     * @todo consider moving this to a separate model class.
     *
     * @return stdClass
     */
    protected function getFacebookUserdata($access_token)
    {
        $this->load->library('curl');

        return json_decode(
            $this->curl->simple_get(
                'https://graph.facebook.com/me?access_token=' . $access_token
            )
        );
    }

}
