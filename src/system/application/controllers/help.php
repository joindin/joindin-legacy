<?php
/**
 * Help pages controller.
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
 * Help pages controller.
 *
 * Responsible for displaying the help pages for the application.
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
class Help extends CI_Controller
{
    /**
     * Displays the help contents page.
     *
     * @return void
     */
    public function index()
    {
        $this->writeContentPage('help/main');
    }

    /**
     * Displays the events help page of the user guide.
     *
     * @return void
     */
    public function user_guide_events()
    {
        $this->writeContentPage('help/user_guide_events');
    }

    /**
     * Displays the talks help page of the user guide.
     *
     * @return void
     */
    public function user_guide_talks()
    {
        $this->writeContentPage('help/user_guide_talks');
    }

    /**
     * Displays the events help page of the admin guide.
     *
     * @return void
     */
    public function event_admin()
    {
        $this->writeContentPage('help/event_admin');
    }

    /**
     * Displays the talks help page of the admin guide.
     *
     * @return void
     */
    public function talk_admin()
    {
        $this->writeContentPage('help/talk_admin');
    }

    /**
     * Displays the help page for the user account page.
     *
     * @return void
     */
    public function manage_user_acct()
    {
        $this->writeContentPage('help/manage_user_acct');
    }

    /**
     * Helper method to provide a generic piece of code for selecting
     * which view to write..
     *
     * @param string $view Name of the view to show
     *
     * @return void
     */
    public function writeContentPage($view)
    {
        $this->template->write_view('content', $view);
        $this->template->render();
    }

}