<?php
/**
 * User pages controller.
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
 * User pages controller.
 *
 * Responsible for displaying all user related pages.
 *
 * @category  Joind.in
 * @package   Controllers
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 *
 * @property  CI_Config   $config
 * @property  CI_Loader   $load
 * @property  CI_Template $template
 * @property  CI_Input    $input
 * @property  User_model  $user_model
 */
class User extends AuthAbstract
{
    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function User()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Main page redirects to the login page.
     *
     * @return void
     */
    function index()
    {
        $this->load->helper('url');
        redirect('user/login');
    }

    /**
     * Displays the login form and upon submit authenticates the user.
     *
     * @return void
     */
    function login()
    {
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('validation');
        $this->load->model('user_model');
        $this->load->library('SSL');
        $arr = array();

        // Used when someone forgot their password.
        // Cleans up the flow some.
        $arr['msg'] = $this->session->flashdata('forgot_password_reset');

        $this->ssl->sslRoute();

        $fields = array(
            'user' => 'Username',
            'pass' => 'Password'
        );
        $rules  = array(
            'user' => 'required',
            'pass' => 'required|callback_start_up_check'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() == false) {
            // add a for-one-request-only session field
            if ($this->session->flashdata('url_after_login')) {
                // the form submission failed, set the flashdata again so 
                // it's there for the resubmit
                $this->session
                    ->set_flashdata(
                        'url_after_login',
                        $this->session->flashdata('url_after_login')
                    );
            } else {
                $this->session->set_flashdata(
                    'url_after_login',
                    $this->input->server('HTTP_REFERER')
                );
            }

            $this->template->write_view('content', 'user/login', $arr);
            $this->template->render();
        } else {
            // success! get our data and update our login time
            $ret = $this->user_model->getUserByUsername($this->input->post('user'));
            $this->_login($ret[0]);
        }
    }

    /**
     * Logs the current user out and destroys the session.
     *
     * @return void
     */
    function logout()
    {
        $this->load->helper('url');
        $this->session->sess_destroy();
        redirect();
    }

    /**
     * Check if either the email or username is set
     *
     * @param string $str Unused parameter - should be removed
     *
     * @return bool
     */
    function check_forgot_user($str = '')
    {
        if (!($this->input->post('user')) || !($this->input->post('user'))) {
            $this->validation->_error_messages['check_forgot_user']
                = 'Please enter either a username or email address';
            return false;
        } else {
            return true;
        }
    }

    /**
     * Sends an e-mail to the user when they have forgotten their password.
     *
     * @param int     $id           User id
     * @param unknown $request_code Request code
     *
     * @return void
     */
    function forgot($id = null, $request_code = null)
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->library('sendemail');
        $arr = array();

        $fields = array(
            'user'  => 'Username',
            'email' => 'Email Address'
        );
        $rules  = array(
            'user'  => 'trim|xss_clean|callback_check_forgot_user   ',
            'email' => 'trim|xss_clean|valid_email'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        // ID and Request code are given?
        if ($id != null and $request_code != null) {
            $ret = $this->user_model->getUserById($id);
            if (empty($ret) 
                || strcasecmp($ret[0]->request_code, $request_code)
            ) {
                // Could not find the user. Maybe already used, maybe a 
                // false code
                $arr['msg'] = "The request code is already used or is invalid.";
            } else {
                // Code is ok. Reset this user's password

                //generate the new password...
                $sel = array_merge(
                    range('a', 'z'),
                    range('A', 'Z'),
                    range(0, 9)
                );
                shuffle($sel);
                $pass_len = 10;
                $pass     = '';
                $uid      = $ret[0]->ID;
                for ($i = 0; $i < $pass_len; $i++) {
                    $r     = mt_rand(0, count($sel) - 1);
                    $pass .= $sel[$r];
                }
                 $arr = array(
                    'password' => password_hash(md5($pass), PASSWORD_DEFAULT),
                    'request_code' => null

                 );
                 $this->user_model->updateUserInfo($uid, $arr);

                // Send the email...
                $this->sendemail->sendPasswordReset($ret, $pass);

                $arr['msg'] = 'A new password has been sent to your email - ' .
                    'open it, and login below';

                $this->session->set_flashdata('forgot_password_reset', $arr['msg']);

                redirect('user/login');
            }
        }

        if ($this->validation->run() != false) {
            //reset their password and send it out to the account
            $email = $this->input->post('email');
            $login = $this->input->post('user');
            if ($email) {
                $ret = $this->user_model->getUserByEmail($email);
            } elseif ($login) {
                $ret = $this->user_model->getUserByUsername($login);
            }

            if (! empty($ret)) {
                $uid = $ret[0]->ID;

                // Generate request code and add to db
                $request_code = substr(md5(uniqid(true)), 0, 8);
                $arr          = array(
                    'request_code' => $request_code
                );
                $this->user_model->updateUserInfo($uid, $arr);

                // Send the activation email...
                $this->sendemail->sendPasswordResetRequest($ret, $request_code);
            }

            $arr['msg'] = 'If the entered details are correct, instructions '.
                'on how to reset your password will be sent to your email -'.
                ' open it and follow the details to reset your password';
        }

        $this->template->write_view('content', 'user/forgot', $arr);
        $this->template->render();
    }

    /**
     * Toggle the user's status between active and inactive.
     *
     * @param integer     $uid  The id of the user
     * @param string|null $from if from is admin then the user is redirected to
     *                          the admin page.
     *
     * @return void
     */
    function changestat($uid, $from = null)
    {
        // Kick them back out if they're not an admin
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->user_model->toggleUserStatus($uid);

        if (isset($from) && ('admin' == $from)) {
            redirect('user/admin');
        } else {
            redirect('user/view/' . $uid);
        }
    }

    /**
     * Toggle the user's admin status between on and off.
     *
     * @param integer     $uid  The id of the user
     * @param string|null $from if from is admin then the user is redirected to
     *                          the admin page.
     *
     * @return void
     */
    function changeastat($uid, $from = null)
    {
        // Kick them back out if they're not an admin
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->user_model->toggleUserAdminStatus($uid);

        if (isset($from) && ('admin' == $from)) {
            redirect('user/admin');
        } else {
            redirect('user/view/' . $uid);
        }
    }

    /**
     * Registers a new user in the system.
     *
     * @return void
     */
    function register()
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('user_model');
        $this->load->plugin('captcha');

        $fields = array(
            'user'             => 'Username',
            'pass'             => 'Password',
            'passc'            => 'Confirm Password',
            'email'            => 'Email',
            'full_name'        => 'Full Name',
            'twitter_username' => 'Twitter Username',
            'cinput'           => 'Captcha'
        );
        $rules  = array(
            'user'  => 'required|trim|callback_usern_check|xss_clean',
            'pass'  => 'required|trim|matches[passc]|md5',
            'passc' => 'required|trim',
            'twitter_username' => 'trim|callback_twitter_check',
            'email' => 'required|trim|valid_email',
            'cinput'=> 'required|callback_cinput_check'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() == false) {
            //$this->load->view('talk/add', array('events'=>$events));
        } else {
            //success!
            $this->session->set_userdata(
                (array)$this->_addUser(
                    $this->input->post('user'),
                    $this->input->post('pass'),
                    $this->input->post('email'),
                    $this->input->post('full_name'),
                    $this->input->post('twitter_username')
                )
            );
            $this->session
                ->set_flashdata('msg', 'Account successfully created!');
            redirect('user/main');
        }

        $captcha = create_captcha();
        $this->session->set_userdata(array('cinput'=>$captcha['value']));

        $this->template->write_view(
            'content',
            'user/register',
            array('captcha' => $captcha)
        );
        $this->template->render();
    }

    /**
     * Displays the user's dashboard / main page.
     *
     * Their list of talks, events attended/attending
     *
     * @return void
     */
    function main()
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('talks_model');
        $this->load->model('event_model');

        $this->load->library('gravatar');
        $imgStr = $this->gravatar
            ->displayUserImage($this->session->userData('ID'), null, 80);

        if (!$this->user_model->isAuth()) {
            redirect('user/login');
        }

        $arr['talks']    = $this->talks_model
            ->getUserTalks($this->session->userdata('ID'));
        $arr['comments'] = $this->talks_model
            ->getUserComments($this->session->userdata('ID'));
        $arr['is_admin'] = $this->user_model->isSiteAdmin();
        $arr['gravatar'] = $imgStr;

        $arr['pending_events'] = $this->event_model->getEventDetail(
            null, null, null, true
        );

        $this->template->write_view('content', 'user/main', $arr);
        $this->template->render();
    }

    /**
     * Displays the details of a user.
     *
     * @param string|integer $uid Either the username or id of the user
     *
     * @return void
     */
    function view($uid)
    {
        $this->load->model('talks_model');
        $this->load->model('pending_talk_claims_model');
        $this->load->model('user_attend_model', 'uam');
        $this->load->model('user_admin_model', 'uadmin');
        $this->load->helper('reqkey');
        $this->load->helper('url');
        $this->load->library('gravatar');

        $reqkey = buildReqKey();

        // see if we have a sort type and apply it
        $p         = explode('/', uri_string());
        $sort_type = (isset($p[4])) ? $p[4] : null;
        $details   = $this->user_model->getUserById($uid);

        // sf the user doesn't exist, redirect!
        if (!isset($details[0])) {
            redirect();
        }

        $imgStr = $this->gravatar->displayUserImage($uid, $details[0]->email, 80);

        if (empty($details[0])) {
            redirect();
        }

        // reset our UID based on what we found...
        $uid       = $details[0]->ID;
        $curr_user = $this->session->userdata('ID');

        $arr = array(
            'details'       => $details,
            'comments'      => $this->talks_model->getUserComments($uid),
            'talks'         => $this->talks_model->getUserTalks($uid),
            'is_admin'      => $this->user_model->isSiteAdmin(),
            'is_attending'  => $this->uam->getUserAttending($uid),
            'my_attend'     => $this->uam->getUserAttending($curr_user),
            'uadmin'        => array(
                'events'        => $this->uadmin
                    ->getUserTypes($uid, array('event')),
                'talks'         => $this->talks_model
                    ->getSpeakerTalks($uid, true),
                'pending_talks' => $this->pending_talk_claims_model
                    ->getTalkClaimsForUser($uid),
            ),
            'reqkey'        => $reqkey,
            'seckey'        => buildSecFile($reqkey),
            'sort_type'     => $sort_type,
            'gravatar'      => $imgStr
        );
        if ($curr_user) {
            $arr['pending_evt'] = $this->uadmin->getUserTypes(
                $curr_user, array('event'), true
            );
        } else {
            $arr['pending_evt'] = array();
        }

        $this->template->write_view('content', 'user/view', $arr);
        $this->template->render();
    }

    /**
     * Manages the name, email and password of the current user.
     *
     * @return void
     */
    function manage()
    {
        // be sure they're logged in
        if (!$this->user_model->isAuth()) {
            $this->session->set_userdata('ref_url', 'user/manage');
            redirect('user/login');
        }

        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('user_admin_model', 'uam');
        $this->load->model('event_model');
        $uid = $this->session->userdata('ID');
        $arr = array(
            'curr_data'      => $this->user_model->getUserById($uid),
            'pending_events' => $this->event_model->getEventDetail(
                null, null, null, true
            ),
        );

        $fields = array(
            'full_name'         => 'Full Name',
            'email'             => 'Email',
            'twitter_username'  => 'Twitter Username',
            'pass'              => 'Password',
            'pass_conf'         => 'Confirm Password'
        );
        $rules  = array(
            'full_name' => 'required',
            'email'     => 'required',
            'pass'      => 'trim|matches[pass_conf]|md5',
            'pass_conf' => 'trim',
            'twitter_username' => 'trim|callback_twitter_check['.$uid.']',
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() != false) {
            $data = array(
                'full_name'         => $this->input->post('full_name'),
                'email'             => $this->input->post('email'),
                'twitter_username'  => $this->input->post('twitter_username'),
            );

            $pass = $this->input->post('pass');
            if (!empty($pass)) {
                $data['password'] = password_hash($this->validation->pass, PASSWORD_DEFAULT);
            }

            $this->db->where('ID', $uid);
            $this->db->update('user', $data);

            $this->session->set_flashdata('msg', 'Changes saved successfully!');
            redirect('user/manage', 'location', 302);
        }

        $this->template->write_view('content', 'user/manage', $arr);
        $this->template->render();
    }

    /**
     * User management page for Site admins.
     *
     * View users listing, enable/disable, etc.
     *
     * @param string  $start  Determine if we are selecting another 
     *                        page of results
     * @param integer $offset Starting index of records to display
     *
     * @return void
     */
    function admin($start = null, $offset = null)
    {
        // The $start parameter exists because we are using a hybrid system
        // of route. Specifically 'enable_query_strings' is set to true, but
        // most of our routing uses segments. As a result, the pagination
        // library will try to add &offset=X to the end of a URL. As we aren't
        // using query params everywhere, we add ?start=1 to ensure that the
        // URL is valid.
        if ($this->config->item('enable_query_strings') === true) {
            $start  = $this->input->get('start');
            $offset = $this->input->get('offset');
        }

        $this->load->library('validation');
        $this->load->library('pagination');
        $this->load->model('user_model');

        // Only admins are allowed
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }


        // In an ideal world, we want to tell the difference between the user
        // selecting page 1 and the user coming to this page via another method.
        // If the user has not specifically selected page 1, then we extract
        // the last page they were on via the session. We need to do this as
        // otherwise toggling the admin setting on a user that is on page 2
        // takes us back to page 1. This test works when 'enable_query_strings'
        // is set to either true or false
        if ($offset === false || $start === null) {
            // retrieve via session
            $offset = (int)$this->session->userdata('user-admin-offset');
        }

        // Retrieve users_per_page from post data or session if not in post
        // and then store to session
        $users_per_page = $this->session->userdata('user-admin-users_per_page');
        if (!$users_per_page) {
            $users_per_page = 10;
        }
        $users_per_page = $this->input->post('users_per_page') ?: $users_per_page;

        // If we change the number of users per page, reset to page 1
        if ($this->input->post('users_per_page')) {
            $offset = 0;
        }

        // Save back to session
        $this->session->set_userdata('user-admin-offset', $offset);
        $this->session->set_userdata('user-admin-users_per_page', $users_per_page);
        
        $this->validation->users_per_page = $users_per_page;

        // Retreive this page's list of users along with total count of users
        $users       = $this->user_model->getAllUsers($users_per_page, $offset);
        $total_users = $this->user_model->countAllUsers();

        $msg    = '';
        $fields = array('user_search' => 'Search Term');
        $this->validation->set_fields($fields);

        if ($this->input->post('sub')) {
            // search call
            $users = $this->user_model->search($this->input->post('user_search'));

        } elseif ($this->input->post('um')) {
            // delete user call
            $selectedUsers = $this->input->post('sel');
            foreach ($selectedUsers as $userId) {
                $this->user_model->deleteUser($userId);
            }
            $msg = count($selectedUsers).' users deleted';
        }

        // The configuration of the pagination library depends on setting
        // of 'enable_query_strings'
        if ($this->config->item('enable_query_strings') === true) {
            $base_url = $this->config->item('base_url') . 'user/admin?start=1';

            $page_query_string = true;
        } else {
            $base_url = $this->config->item('base_url') . 'user/admin/start';

            $page_query_string = false;
        }

        $this->pagination->initialize(
            array(
                'base_url'             => $base_url,
                'uri_segment'          => 4,
                'total_rows'           => $total_users,
                'per_page'             => $users_per_page,
                'cur_page'             => $offset,
                'page_query_string'    => $page_query_string,
                'query_string_segment' => 'offset',
            )
        );

        $arr = array(
            'users'       => $users,
            'paging'      => $this->pagination->create_links(),
            'msg'         => $msg
        );

        $this->load->model('event_model');
        $arr['pending_events'] = $this->event_model->getEventDetail(
            null, null, null, true
        );

        $this->template->write_view('content', 'user/admin', $arr);
        $this->template->render();
    }

    /**
     * Validate the username and password combination.
     *
     * @param string $p The password string
     *
     * @return bool
     */
    function start_up_check($p)
    {
        $u   = $this->input->post('user');
        $ret = $this->user_model->validate($u, $p, false, $this->input);

        if (!$ret) {
            $this->validation->set_message(
                'start_up_check', 'Username/password combination invalid!'
            );
        }

        return $ret;
    }

    /**
     * Validates the captcha.
     *
     * @param string $str The entered captcha.
     *
     * @return bool
     */
    function cinput_check($str)
    {
        $str = $this->input->post('cinput');
        if (! is_numeric($str)) {
            // If the user input is not numeric, convert it to a numeric value
            $this->load->plugin('captcha');
            $digits = captcha_get_digits(true);
            $str    = array_search(strtolower($str), $digits);
        }

        if ($str != $this->session->userdata('cinput')) {
            $this->validation->_error_messages['cinput_check']
                = 'Incorrect captcha.';
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates whether the username already exists.
     *
     * @param string $str The username to test
     *
     * @return bool
     */
    function usern_check($str)
    {
        $ret = $this->user_model->getUserByUsername($str);

        if (!empty($ret)) {
            $this->validation->_error_messages['usern_check']
                = 'Username already exists!';
            return false;
        }

        return true;
    }

    /**
     * Validates whether the twitter name already exists.
     *
     * @param string $str The twitter name to check
     * @param int    $uid User id
     *
     * @return bool
     */
    function twitter_check($str, $uid = -1)
    {
        // Strip away the @ if found
        if ($str[1] == '@') {
            $str = substr($str, 1);
        }
        $user = $this->user_model->getUserbyTwitter($str);
        if (empty($user)) {
            return true;
        }

        if ($uid == -1 || $user[0]->ID != $uid) {
            $this->validation->_error_messages['twitter_check']
                = "This twitter name is already used!";
            return false;
        }

        return true;
    }

    /**
     * Validates whether the given mail address is not already in use.
     *
     * @param string $str The mail address to validate
     *
     * @return bool
     */
    function email_exist_check($str)
    {
        $ret = $this->user_model->getUserByEmail($str);
        if (empty($ret)) {
            $this->validation->_error_messages['email_exist_check']
                = 'Login for that email address does not exist!';
            return false;
        }

        return true;
    }

    /**
     * Validates the username.
     *
     * @param string $str The username to validate
     *
     * @return bool
     */
    function login_exist_check($str)
    {
        $ret = $this->user_model->getUserByUsername($str);

        if (empty($ret)) {
            $this->validation->_error_messages['login_exist_check']
                = 'Invalid username!';
            return false;
        }

        return true;
    }

    /**
     * Validates if there is a user with the given e-mail address.
     *
     * @param string $str E-mail address to check
     *
     * @return bool
     */
    function user_email_match_check($str)
    {
        $ret = $this->user_model->getUserByEmail($str);

        // no email like that on file - error!
        if (empty($ret)) {
            $this->validation->_error_messages['user_email_match_check']
                = 'Invalid user information!';
            return false;
        }

        // see if the username and email we've been given match up
        if ($this->input->post('user') != $ret[0]->username) {
            $this->validation->_error_messages['user_email_match_check']
                = 'Invalid user information!';
            return false;
        }
        return true;
    }

    /**
     * Allow users to grant or deny access for an oauth app
     *
     * Users will land here directly from oauth consuming sites/apps/whatever
     *
     * @return void
     */
    function oauth_allow()
    {
        if (!$this->user_model->isAuth()) {
            // Explicitly set the URL to return to
            // Relying on the referrer being present can cause issues when it
            // isn't present
            $this->session->set_flashdata(
                "url_after_login",
                $this->input->server("REQUEST_URI")
            );
            redirect('user/login', 'refresh');
        }

        $this->load->model('user_admin_model');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('validation');
        $this->load->library('SSL');

        $this->ssl->sslRoute();

        $fields = array(
            'access' => 'Permit access?'
        );
        $rules  = array(
            'access' => 'required'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        $view_data['status'] = null;
        if ($this->validation->run() == false) {
            $api_key  = $this->input->get('api_key');
            $callback = urldecode($this->input->get('callback'));
            $state    = $this->input->get('state');

            if (empty($api_key)) {
                $view_data['status'] = 'keyfail';
            } elseif (empty($callback)) {
                $view_data['status'] = 'callbackfail';
            } elseif ($this->user_admin_model->oauthVerifyApiKey(
                $api_key,
                $callback
            )
            ) {
                $this->session->set_flashdata('api_key', $api_key);
                $this->session->set_flashdata('callback', $callback);
                $this->session->set_flashdata('state', $state);
            } else {
                $view_data['status'] = 'invalid';
            }
        } else {
            $api_key  = $this->session->flashdata('api_key');
            $callback = $this->session->flashdata('callback');
            $state    = $this->session->flashdata('state');

            if ($this->input->post('access') == 'allow') {
                $view_data['status'] = "allow";
                $access_token        = $this->user_admin_model
                    ->oauthAllow($api_key, $this->session->userdata('ID'));
                if (!empty($callback)) {
                    $url = $this->makeOAuthCallbackURL(
                        $callback,
                        $state,
                        $access_token
                    );
                    // add our parameter onto the URL

                    // Don't use the CodeIgniter redirect() call here
                    // as it always prepends the site URL
                    // which is no good for custom URL schemes
                    header('Location: ' . $url);
                    exit; // we shouldn't be here
                }
            } else {
                $view_data['status']       = "deny";
                $view_data['callback_url'] = '';
                if (!empty($callback)) {
                    $url = $this->makeOAuthCallbackURL($callback, $state);

                    $view_data['callback_url'] = $url;
                }
            }
        }
        $this->template->write_view('content', 'user/oauth_allow', $view_data);
        $this->template->render();
    }

    /**
     * Generate a callback URL including access tokens etc
     * for OAuth rqeuests
     *
     * @param string $callback     Supplied callback URL
     * @param string $state        Any user-supplied data to send back to the caller
     * @param string $access_token A valid OAuth access token
     *
     * @return string The full URL to redirect the user to
     */
    function makeOAuthCallbackURL($callback, $state, $access_token = "")
    {
        if (strpos($callback, '?') !== false) {
            $url = $callback . '&';
        } else {
            $url = $callback . '?';
        }

        if (strlen($access_token)) {
            $url .= 'access_token=' . $access_token;
        }
        if (!empty($state)) {
            $url .= "&state=" . $state;
        }

        return $url;
    }

    /**
     * Show this user's API keys, generating them if they don't exist
     * 
     * @access public
     * @return void
     */
    function apikey()
    {
        if (!$this->user_model->isAuth()) {
            redirect('user/login', 'refresh');
        }

        $this->load->model('user_admin_model');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('validation');

        $view_data = array();

        $fields = array(
            'application' => 'application display name',
            'description' => 'application description',
            'callback_url' => 'callback URL'

        );
        $rules  = array(
            'application' => 'required',
            'description' => 'required|min_length[20]',
            'callback_url' => 'required',
        );

        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if (($this->validation->run() == false)) {
            // either we just arrived, or the user sees error messages
        } else {
            // generate new keys
            $this->user_admin_model->oauthGenerateConsumerCredentials(
                $this->session->userdata('ID'),
                $this->input->post('application'),
                $this->input->post('description'),
                $this->input->post('callback_url')
            );
        }

        // fetch all keys
        $view_data['keys'] 
            = $this->user_admin_model->oauthGetConsumerKeysByUser(
                $this->session->userdata('ID')
            );
        $view_data['grants'] 
            = $this->user_admin_model->oauthGetAccessKeysByUser(
                $this->session->userdata('ID')
            );
        
        $this->template->write_view('content', 'user/apikey', $view_data);
        $this->template->render();
    }

    /**
     * Remove the API key record for this user
     * 
     * @return void
     */
    public function apikey_delete()
    {
        if (!$this->user_model->isAuth()) {
            redirect('user/login', 'refresh');
        }

        $this->load->model('user_admin_model');

        $this->user_admin_model
            ->deleteApiKey(
                $this->session->userdata('ID'),
                $this->input->get('id')
            );
        redirect('/user/apikey');
    }

    /**
     * Remove this application authorisation for this user
     * 
     * @return void
     */
    public function revoke_access() 
    {
        if (!$this->user_model->isAuth()) {
            redirect('user/login', 'refresh');
        }

        $this->load->model('user_admin_model');

        $this->user_admin_model->deleteAccessToken(
            $this->session->userdata('ID'),
            $this->input->get('id')
        );
        redirect('/user/apikey');
    }
}
