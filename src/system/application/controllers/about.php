<?php
/**
 * About pages controller.
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
 * About pages controller.
 *
 * Responsible for displaying the about / help pages in the application.
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
     * Displays a help page for the import functionality.
     *
     * @return void
     */
    function import()
    {
        $this->template->write_view('content', 'about/import');
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

        $fields = array(
            'your_name'  => 'Name',
            'your_email' => 'Email',
            'your_com'   => 'Comments'
        );

        $rules = array(
            'your_name' => 'required',
            'your_com'  => 'required'
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
            $this->validation->your_name = '';
            $this->validation->your_email = '';
            $this->validation->your_com = '';
        }

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
     * Displays the help page for the widgets.
     *
     * @return void
     */
    function widget()
    {
        $this->template->write_view('content', 'about/widget');
        $this->template->render();
    }

    /**
     * Shows a gravatar collage of 9x7 random users (Who's on Joind in?).
     *
     * @return void
     */
    function who()
    {
        $dir = $this->config->item('gravatar_cache_dir');

        // get a list of gravatars which match the default size
        $default_size = 1323;
        $users = array();
        foreach (new DirectoryIterator($dir) as $file) {
            $file_size = filesize($dir . '/' . $file->getFilename());
            if (!$file->isDot() && ($file_size != $default_size)) {
                if (preg_match('/user([0-9]+)\.jpg/', $file->getFilename(), $m)) {
                    $users[] = $m[1];
                }
            }
        }

        // send the list of users to the template
        $arr = array(
            'users' => $users
        );
        $this->template->write_view('content', 'about/who', $arr);
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
}

?>