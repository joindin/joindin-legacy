<?php
/**
 * About pages controller.
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
 * About pages controller.
 *
 * Responsible for displaying the about / help pages in the application.
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
 * @property  User_model  $user_model
 */
class About extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function About()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Displays the site-wide Joind.in about page.
     *
     * @return void
     */
    function index()
    {
        $this->load->helper('form');

        $this->template->write_view('content', 'about/main');
        $this->template->write_view('sidebar2', 'about/_facebook-sidebar');
        $this->template->render();
    }

    /**
     * Displays the help page for the event admin section.
     *
     * @return void
     */
    function evt_admin()
    {
        $this->template->write_view('content', 'about/evt_admin');
        $this->template->render();
    }

    /**
     * Displays and process the contact form.
     *
     * @return void
     */
    function contact()
    {
        $this->load->helper('form');
        $this->load->library('akismet');
        $this->load->library('validation');
        $this->load->plugin('captcha');

        $fields = array(
            'your_name'  => 'Name',
            'your_email' => 'Email',
            'your_com'   => 'Comments',
            'cinput'     => 'Verification'
        );

        $rules = array(
            'your_name' => 'required',
            'your_com'  => 'required',
            'your_email'=> 'required|valid_email',
            'cinput'    => 'required|callback_cinput_check'
        );
        
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        // if the form was posted, send the e-mail
        $arr = array();
        if ($this->validation->run() != false) {
            // check the mail with akismet
            $arr = array(
                'comment_type'    => 'comment',
                'comment_author'  => $this->input->post('your_name'),
                'comment_author_email' => $this->input->post('your_email'),
                'comment_content' => $this->input->post('your_com')
            );
            $ret = $this->akismet->send('/1.1/comment-check', $arr);

            // assemble the e-mail
            $subj  = 'Feedback from ' . $this->config->item('site_name');
            $cont  = 'Name: ' . $this->input->post('your_name') . "\n\n";
            $cont .= 'Email: ' . $this->input->post('your_email') . "\n\n";
            $cont .= 'Comment: ' . $this->input->post('your_com') . "\n\n";
            $cont .= 'Spam check: ' . ($ret == 'false')
                ? 'not spam' : 'spam caught';

            // sent the mail to every site admin user
            $admin_emails = $this->user_model->getSiteAdminEmail();
            
            foreach ($admin_emails as $user) {
                $from = 'From: ' . $this->config->item('email_feedback');
                mail($user->email, $subj, $cont, $from);
            }

            // set confirmation message
            $arr = array(
                'msg' => 'Comments sent! Thanks for the feedback!'
            );

            //clear out the values so they know it was sent..
            $this->validation->your_name  = '';
            $this->validation->your_email = '';
            $this->validation->your_com   = '';
        }

        $arr['captcha'] = create_captcha();
        $this->session->set_userdata(array('cinput'=>$arr['captcha']['value']));

        $this->template->write_view('content', 'about/contact', $arr);
        $this->template->render();
    }

    /**
     * Displays the about page for the iPhone support.
     *
     * @return void
     */
    function iphone_support()
    {
        $this->template->write_view('content', 'about/iphone_support');
        $this->template->render();
    }

    /**
     * Displays the about page for the services.
     *
     * @return void
     */
    function services()
    {
        $this->template->write_view('content', 'about/services', array());
        $this->template->render();
    }

    /**
     * Displays information about importing CSV files
     *
     * @return void
     */
    function import()
    {
        $this->template->write_view('content', 'about/import');
        $this->template->render();
    }

    /**
     * Validate captcha input.
     *
     * @param string $str The text of the captcha
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
        }

        return true;
    }

    /**
     * Displays the page about the widgets
     *
     * @return void
     */
    function widgets()
    {
        $this->template->write_view('content', 'about/widgets', array());
        $this->template->render();
    }

}

