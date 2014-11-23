<?php
/**
 * Abstract controller for authentication services.
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
 * Abstract controller for authentication services.
 *
 * Used as basis for the user, facebook and twitter controllers to add users
 * or do a generic login.
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
 */
abstract class AuthAbstract extends Controller
{
    /**
     * Contains an array with urls we don't want to forward to after login.
     * If a part of the url is in one of these items, it will forward them to
     * their main account page.
     *
     * @var Array
     */
    private $_non_forward_urls = array('user/login', 'user/forgot');

    /**
     * Performs login validation
     *
     * @param unknown $user User to login with
     *
     * @return null
     */
    protected function _login($user)
    {
        $this->session->set_userdata((array)$user);

        //update login time
        $this->db->where('id', $user->ID);
        $this->db->update('user', array('last_login' => time()));

        // send them back to where they came from, either the referer if they
        // have one, or the flashdata
        $referer = $this->input->server('HTTP_REFERER');

        // Only allow the referrer to be on this site - this prevents a loop
        // to Twitter after login, and other possible phishing attacks
        $base = $this->config->item('base_url');
        if (substr($referer, 0, strlen($base)) != $base) {
            $referer = $base;
        }

        $to = $this->session->flashdata('url_after_login')
            ? $this->session->flashdata('url_after_login') : $referer;

        // List different routes we don't want to reroute to
        $bad_routes = $this->_non_forward_urls;

        foreach ($bad_routes as $route) {
            if (strstr($to, $route)) {
                redirect('user/main');
            }
        }

        // our $to is good, so redirect
        redirect($to);
    }

    /**
     * Adds a user to the database
     *
     * @param string $username     Username to add
     * @param string $password     Password for user
     * @param string $email        Email address of user
     * @param string $fullname     Full name of user
     * @param string $twitter_name Twitter name of user
     *
     * @return user model
     */
    protected function _addUser(
        $username, $password, $email, $fullname, $twitter_name
    ) {
        $arr = array(
            'username'         => $username,
            'password'         => password_hash($password, PASSWORD_DEFAULT),
            'email'            => $email,
            'full_name'        => $fullname,
            'twitter_username' => $twitter_name,
            'active'           => 1,
            'verified'         => 1,
            'last_login'       => time()
        );
        $this->db->insert('user', $arr);
        return current($this->user_model->getUserByUsername($arr['username']));
    }
}
