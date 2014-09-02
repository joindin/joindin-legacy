<?php
/**
 * Main pages controller.
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
 * Main pages controller.
 *
 * Responsible for displaying the main pages which do not belong to
 * another controller, for example: the frontpage.
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
class Main extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function Main()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Displays the frontpage with the hot events, upcoming events,
     * latest blog post and more.
     *
     * @return void
     */
    function index()
    {
        $this->load->helper('form');
        $this->load->model('talks_model');
        $this->load->model('event_model');
        $this->load->model('user_attend_model');
        $this->load->helper('reqkey');

        $reqkey = buildReqKey();

        $arr = array(
            'talks'           => $this->talks_model->getPopularTalks(),
            'hot_events'      => $this->event_model->getHotEvents(7),
            'logged'          => $this->user_model->isAuth(),
            'reqkey'          => $reqkey,
            'seckey'          => buildSecFile($reqkey)
        );

        // now add the attendance data for the hot events
        $uid = $this->user_model->getID();
        foreach ($arr['hot_events'] as $e) {
            $e->user_attending = ($uid)
                ? $this->user_attend_model->chkAttend($uid, $e->ID)
                : false;
        }

        $events = $this->event_model->getCurrentCfp(true);
        $this->template->parse_view(
            'sidebar2',
            'event/_event-cfp-sidebar',
            array('events'=>$events)
        );
        
        $this->template->write_view('content', 'main/index', $arr, true);
        $this->template->render();
    }
}

