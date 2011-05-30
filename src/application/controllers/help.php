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
class Help extends MY_Controller
{

    /**
     * Constructor, responsible for initializing the parent constructor.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Displays the help contents page.
     *
     * @return void
     */
    function index()
    {
        $this->writeContentPage('help/main');
    }

    /**
     * Displays the events help page of the user guide.
     *
     * @return void
     */
    function user_guide_events()
    {
        $this->writeContentPage('help/user_guide_events');
    }

    /**
     * Displays the talks help page of the user guide.
     *
     * @return void
     */
    function user_guide_talks()
    {
        $this->writeContentPage('help/user_guide_talks');
    }

    /**
     * Displays the events help page of the admin guide.
     *
     * @return void
     */
    function event_admin()
    {
        $this->writeContentPage('help/event_admin');
    }

    /**
     * Displays the talks help page of the admin guide.
     *
     * @return void
     */
    function talk_admin()
    {
        $this->writeContentPage('help/talk_admin');
    }

    /**
     * Displays the help page for the user account page.
     *
     * @return void
     */
    function manage_user_acct()
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
    function writeContentPage($view)
    {
        $this->template->write_view('content', $view);
        $this->template->render();
    }

}

?>