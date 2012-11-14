<?php
/**
 * External pages controller.
 *
 * Controller tasked with executing externally triggered scripts, automated
 * sending of twitter messages.
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

/**
 * External pages controller.
 *
 * Controller tasked with executing externally triggered scripts, automated
 * sending of twitter messages.
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
class External extends Controller
{

    /**
     * Sends an update to twitter notifying the world how many events are coming
     * and where to find them.
     *
     * @return void
     */
    public function twitter_event_add()
    {
        // only execute when invoked from a cron job
        if (!defined('IS_CRON')) {
            return false;
        }

        $this->load->library('twitter');
        $this->load->model('event_model');

        $events = $this->event_model->getUpcomingEvents(null);
        $msg    = $this->config->item('site_name') . " Update: There are " .
            count($events) . " great events coming up soon! ";
        $msg   .= "Check them out! " . $this->config->site_url() .
            "event/upcoming";

        // @todo: shorten this URL to help fit inside a Twitter message
        $this->twitter->sendMsg($msg);
    }

    /**
     * Sends an update to twitter notifying the world what the most popular
     * talks currently are and where to find them.
     *
     * @todo populate method
     *
     * @return void
     */
    public function twitter_popular_talks()
    {
        //send a message to twitter with some of the popular talks
    }

    /**
     * Sends an update to twitter notifying the world what the latest blog
     * post currently is and where to find it.
     *
     * @return bool
     */
    public function twitter_latest_blog()
    {
        // only execute when invoked from a cron job
        if (!defined('IS_CRON')) {
            return false;
        }

        $this->load->model('blog_posts_model', 'bpm');

        $detail = $this->bpm->getPostDetail();
        $msg    = $this->config->item('site_name') .
            ' Update: Latest blog post - ' . $detail[0]->title . ' ';
        $msg   .= $this->config->site_url() . 'blog/view/' . $detail[0]->ID;

        $this->twitter->sendMsg($msg);
    }

    /**
     * Grab the pending events and send an email to the event admins 
     * with a list of things that are: 1 day away, 1 week away, etc.
     *
     * @return null
     */
    public function send_pending_event_email()
    {
        $this->load->library('sendemail');
        $this->load->model('event_model');
        
        $events = $this->event_model->getEventDetail(null, null, null, true);
        $this->sendemail->sendPendingEvents($events);
    }
}

