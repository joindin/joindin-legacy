<?php
/**
 * Help pages controller.
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
 * Help pages controller.
 *
 * Responsible for displaying the help pages for the application.
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
class Help extends Controller
{

    /**
     * Constructor, responsible for initializing the parent constructor.
     */
    function Help()
    {
        parent::Controller();
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

    /**
     * Displays the user guide for event organisers
     * 
     * @return void
     */
    function event_organiser_help() 
    {
        $this->writeContentPage('help/event_organiser_help');
    }

    /**
     * Displays the user guide for speakers
     * 
     * @return void
     */
    function speaker_help() 
    {
        $this->writeContentPage('help/speaker_help');
    }

    /**
     * Displays the user guide for users
     * 
     * @return void
     */
    function user_help() 
    {
        $this->writeContentPage('help/user_help');
    }
}

