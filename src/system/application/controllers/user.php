<?php
/**
 * User pages controller.
 *
 * PHP version 5
 *
 * @category  Joind.in
 * @package   Controllers
 * @author    Chris Cornutt <chris@joind.in>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2009 - 2010 Joind.in
 * @license   http://github.com/joindin/joind.in/blob/master/doc/LICENSE JoindIn
 * @link      http://github.com/joindin/joind.in
 */

/**
 * User pages controller.
 *
 * Responsible for displaying all user related pages.
 *
 * @category  Joind.in
 * @package   Controllers
 * @author    Chris Cornutt <chris@joind.in>
 * @author    Mike van Riel <mike.vanriel@naenius.com>
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
class User extends Controller
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

        $this->ssl->sslRoute();

        $fields = array(
            'user' => 'Username',
            'pass' => 'Password'
        );
        $rules = array(
            'user' => 'required',
            'pass' => 'required|callback_start_up_check'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);


        if ($this->validation->run() == false) {
            // add a for-one-request-only session field
            if($this->session->flashdata('url_after_login')) {
                // the form submission failed, set the flashdata again so it's there for the resubmit
                $this->session->set_flashdata('url_after_login', $this->session->flashdata('url_after_login'));
            } else {
                $this->session->set_flashdata('url_after_login', $this->input->server('HTTP_REFERER'));
            }

            $this->template->write_view('content', 'user/login');
            $this->template->render();
        } else {
            // success! get our data and update our login time
            $ret = $this->user_model->getUser($this->input->post('user'));
            $this->session->set_userdata((array) $ret[0]);

            //update login time
            $this->db->where('id', $ret[0]->ID);
            $this->db->update(
                'user', array(
                    'last_login' => time()
                )
            );

            // send them back to where they came from, either the referer if they have one, or the flashdata
            $referer = $this->input->server('HTTP_REFERER');
            $to = $this->session->flashdata('url_after_login') ? $this->session->flashdata('url_after_login') : $referer;
            
			// List different routes we don't want to reroute to
			$bad_routes = array(
				'user/login'
				,'user/forgot'
			);
			foreach($bad_routes as $route)
			{
				if(strstr($to, $route))
				{
					redirect('user/main');
				}
			}
			
			// our $to is good, so redirect
			redirect($to);
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
     * Sends an e-mail to the user when they have forgotten their password.
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
        $rules = array(
            'user'  => 'required|trim|xss_clean',
            'email' => 'required|trim|xss_clean|valid_email'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        // ID and Request code are given?
        if ($id != null and $request_code != null) {
            $ret = $this->user_model->getUser($id);
            if (empty($ret) || strcasecmp($ret[0]->request_code, $request_code)) {
                // Could not find the user. Maybe already used, maybe a false code
                $arr['msg'] = "The request code is already used or is invalid.";
            } else {
                // Code is ok. Reset this user's password

                //generate the new password...
                $sel = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
                shuffle($sel);
                $pass_len = 10;
                $pass = '';
                 $uid = $ret[0]->ID;
                for ($i = 0; $i < $pass_len; $i++) {
                    $r = mt_rand(0, count($sel) - 1);
                    $pass .= $sel[$r];
                }
                 $arr = array(
                    'password' => md5($pass),
                    'request_code' => null

                 );
                 $this->user_model->updateUserInfo($uid, $arr);

                // Send the email...
                $this->sendemail->sendPasswordReset($ret, $pass);

                $arr['msg'] = 'A new password has been sent to your email - ' .
                    'open it and click on the login link to use the new password';
            }
        }

        if ($this->validation->run() != false) {
            //reset their password and send it out to the account
            $email = $this->input->post('email');
            $login = $this->input->post('user');

            $ret = $this->user_model->getUserByEmail($email);
            if (! empty($ret) && $ret[0]->username == $login) {
                $uid = $ret[0]->ID;

                // Generate request code and add to db
                $request_code = substr(md5(uniqid(true)), 0, 8);
                $arr = array(
                    'request_code' => $request_code
                );
                $this->user_model->updateUserInfo($uid, $arr);

                // Send the activation email...
                $this->sendemail->sendPasswordResetRequest($ret, $request_code);
            }

            $arr['msg'] = 'If the entered details are correct, instructions on how to reset your password will ' .
                'be sent to your email - open it and follow the details to reset your password';
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

        /*$this->load->plugin('captcha');
              $cap_arr=array(
                  'img_path'		=>$_SERVER['DOCUMENT_ROOT'].'/inc/img/captcha/',
                  'img_url'		=>'/inc/img/captcha/',
                  'img_width'		=>'130',
                  'img_height'	=>'30'
              );*/

        $fields = array(
            'user'             => 'Username',
            'pass'             => 'Password',
            'passc'            => 'Confirm Password',
            'email'            => 'Email',
            'full_name'        => 'Full Name',
            'twitter_username' => 'Twitter Username'
            //	'cinput'	=> 'Captcha'
        );
        $rules = array(
            'user'  => 'required|trim|callback_usern_check|xss_clean',
            'pass'  => 'required|trim|matches[passc]|md5',
            'passc' => 'required|trim',
            'email' => 'required|trim|valid_email',
            //	'cinput'	=> 'required|callback_cinput_check'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() == false) {
            //$this->load->view('talk/add',array('events'=>$events));
        } else {
            //success!
            $this->session->set_flashdata('msg', 'Account successfully created!');
            $arr = array(
                'username'         => $this->input->post('user'),
                'password'         => $this->input->post('pass'),
                'email'            => $this->input->post('email'),
                'full_name'        => $this->input->post('full_name'),
                'twitter_username' => $this->input->post('twitter_username'),
                'active'           => 1,
                'last_login'       => time()
            );
            $this->db->insert('user', $arr);

            // now, since they're set up, log them in a push them to the main page
            $ret = $this->user_model->getUser($arr['username']);
            $this->session->set_userdata((array) $ret[0]);
            redirect('user/main');
        }

        //$cap=create_captcha($cap_arr);
        //$this->session->set_userdata(array('cinput'=>$cap['word']));
        //$carr=array('captcha'=>$cap);

        $carr = array();
        $this->template->write_view('content', 'user/register', $carr);
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
        $imgStr = $this->gravatar->displayUserImage($this->session->userData('ID'), null, 80);

        if (!$this->user_model->isAuth()) {
            redirect('user/login');
        }

        $arr['talks']    = $this->talks_model->getUserTalks($this->session->userdata('ID'));
        $arr['comments'] = $this->talks_model->getUserComments($this->session->userdata('ID'));
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
        $this->load->model('user_attend_model', 'uam');
        $this->load->model('user_admin_model', 'uadmin');
        $this->load->model('speaker_profile_model', 'spm');
        $this->load->helper('reqkey');
        $this->load->helper('url');
        $this->load->library('gravatar');

        $reqkey = buildReqKey();

        // see if we have a sort type and apply it
        $p         = explode('/', uri_string());
        $sort_type = (isset($p[4])) ? $p[4] : null;
        $details   = $this->user_model->getUser($uid);

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
            'uadmin'        => $this->uadmin->getUserTypes(
                $uid, array('talk', 'event')
            ),
            'reqkey'        => $reqkey,
            'seckey'        => buildSecFile($reqkey),
            'sort_type'     => $sort_type,
            'pub_profile'   => $this->spm->getUserPublicProfile($uid, true),
            'gravatar'      => $imgStr
        );
        if ($curr_user) {
            $arr['pending_evt'] = $this->uadmin->getUserTypes(
                $curr_user, array('event'), true
            );
        } else {
            $arr['pending_evt'] = array();
        }

        $block = array(
            'title'     => 'Other Speakers',
            'content'   => $this->user_model->getOtherUserAtEvt($uid),
            'udata'     => $arr['details'],
            'has_talks' => (count($arr['talks']) == 0) ? false : true
        );

        if(!empty($block['content'])){
			$this->template->write_view('sidebar2', 'user/_other-speakers', $block);
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
        $uid = $this->session->userdata('ID');
        $arr = array(
            'curr_data' => $this->user_model->getUser($uid)
        );

        $fields = array(
            'full_name'         => 'Full Name',
            'email'             => 'Email',
            'twitter_username'  => 'Twitter Username',
            'pass'              => 'Password',
            'pass_conf'         => 'Confirm Password'
        );
        $rules = array(
            'full_name' => 'required',
            'email'     => 'required',
            'pass'      => 'trim|matches[pass_conf]|md5',
            'pass_conf' => 'trim',
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
                $data['password'] = $this->validation->pass;
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
     * @param integer $page Number of the page to handle
     *
     * @return void
     */
    function admin($page = null)
    {
        $this->load->helper('reqkey');
        $this->load->library('validation');

        $reqkey      = buildReqKey();
        $page        = (!$page) ? 1 : $page;
        $rows_in_pg  = 10;
        $offset      = ($page == 1) ? 1 : $page * 10;
        $all_users   = $this->user_model->getAllUsers();
        $all_user_ct = count($all_users);
        $page_ct     = ceil($all_user_ct / $rows_in_pg);
        $users       = array_slice($all_users, $offset, $rows_in_pg);

        $fields = array(
            'user_search' => 'Search Term'
        );
        $rules = array(
            'user_search' => 'required'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() != false) {
            $users = $this->user_model->search($this->input->post('user_search'));
        }

        $arr = array(
            'users'         => $users,
            'all_user_ct'   => $all_user_ct,
            'page_ct'       => $page_ct,
            'page'          => $page,
            'reqkey'        => $reqkey,
            'seckey'        => buildSecFile($reqkey),
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
        $ret = $this->user_model->validate($u, $p);

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
        if ($this->input->post('cinput') != $this->session->userdata('cinput')) {
            $this->validation->_error_messages['cinput_check']
                = 'Incorrect Captcha characters.';
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
        $ret = $this->user_model->getUser($str);

        if (!empty($ret)) {
            $this->validation->_error_messages['usern_check']
                = 'Username already exists!';
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
        $ret = $this->user_model->getUser($str);

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
        $rules = array(
            'access' => 'required'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);
 
        $view_data['status'] = NULL;
        if ($this->validation->run() == false) {
            $request_token = filter_var($this->input->get('request_token'), FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[0-9a-z]*$/')));
            // check for a valid request token 
            if($this->user_admin_model->oauthRequestTokenVerify($request_token)) {
                $this->session->set_flashdata('request_token', $request_token);
            } else {
                $view_data['status'] = "invalid";
            }
        } else {
            $request_token = $this->session->flashdata('request_token');

            if($this->input->post('access') == 'allow') {
                $view_data['status'] = "allow";
                $oauth_info = $this->user_admin_model->oauthAllow($request_token, $this->session->userdata('ID'));
                if($oauth_info->callback == "oob") {
                    // special case, we can't forward the user on so just display verification code
                    $view_data['verification'] = $oauth_info->verification;
                } else {
                    // add our parameter onto the URL
                    if(strpos($oauth_info->callback, '?' !== false)) {
                        $url = $oauth_info->callback . '&';
                    } else {
                        $url = $oauth_info->callback . '?';
                    }
                    $url .= 'oauth_token=' . $oauth_info->verification;
                    redirect($url);
                    exit; // we shouldn't be here 
                }
            } else {
                $view_data['status'] = "deny";
                $this->user_admin_model->oauthDeny($request_token);
            }
        }
        $this->template->write_view('content', 'user/oauth_allow', $view_data);
        $this->template->render();
    }
}

?>
