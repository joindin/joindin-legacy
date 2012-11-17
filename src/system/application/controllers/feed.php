<?php
/**
 * RSS Feed controller.
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
 * RSS Feed controller.
 *
 * Responsible for generating and outputting the RSS Feeds.
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
class Feed extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function Feed()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * The index page redirects the user to the homepage.
     *
     * @return void
     */
    function index()
    {
        redirect();
    }

    /**
     * Outputs a feed with the comments of a specific talk.
     *
     * @param integer $tid The id of the talk
     *
     * @return void
     */
    function talk($tid)
    {
        $this->load->helper('form');
        $this->load->model('talks_model');

        $com = $this->talks_model->getTalkComments($tid, null, null);
        $tlk = $this->talks_model->getTalks($tid);

        foreach ($com as $k => $v) {
            $guid    = $this->config->site_url() . 'talk/view/' . $v->talk_id;
            $items[] = array(
                'guid'        => $guid,
                'title'       => 'Comment on: ' . $tlk[0]->talk_title,
                'link'        => $guid,
                'description' => $v->comment,
                'pubDate'     => date('r', $v->date_made)
            );
        }

        $this->load->view(
            'feed/feed', array(
                'items' => $items
            )
        );
    }

    /**
     * Outputs a feed with the posts of the blog.
     *
     * @return void
     */
    function blog()
    {
        $this->load->model('blog_posts_model', 'bpm');
        $items = array();

        foreach ($this->bpm->getPostDetail() as $k => $v) {
            $guid    = $this->config->site_url() . 'blog/view/' . $v->ID;
            $items[] = array(
                'guid'        => $guid,
                'title'       => $v->title,
                'link'        => $guid,
                'description' => $v->content,
                'pubDate'     => date('r', $v->date_posted)
            );
        }

        $this->load->view(
            'feed/feed', array(
                'items' => $items
            )
        );
    }

    /**
     * Outputs a feed with the comments of a specific event.
     *
     * @param integer $eid The id of the event
     *
     * @return void
     */
    function event($eid)
    {
        $this->load->model('event_model');
        $this->load->model('event_comments_model', 'ecm');

        $ret        = $this->ecm->getEventComments($eid);
        $edata      = $this->event_model->getEventDetail($eid);
        $event_name = $edata[0]->event_name;
        $items      = array();

        foreach ($ret as $k => $v) { //print_r($v);
            $guid = $this->config->site_url() . 'event/view/' . $eid . '#comments';

            $items[] = array(
                'guid'        => $guid,
                'title'       => 'Comment on Event "' . $event_name . '"',
                'link'        => $guid,
                'description' => $v->comment,
                'pubDate'     => date('r', $v->date_made)
            );
        }

        $this->load->view(
            'feed/feed', array(
                'items' => $items,
                'title' => 'Event Comments - "' . $event_name . '"'
            )
        );
    }

    /**
     * Outputs a feed with the event and talk comments of a specific user.
     *
     * @param integer $userId Id of the user
     *
     * @return void
     */
    function user($userId)
    {
        $this->load->model('talks_model');
        $this->load->model('talk_comments_model', 'tcm');
        $this->load->model('event_comments_model', 'ecm');

        $udata    = $this->user_model->getUserById($userId);
        $talks    = array();
        $comments = array();

        if (!empty($udata)) {
            $userId = $udata[0]->ID;

            //get the upcoming talks for this user
            $ret = $this->talks_model->getUserTalks($userId);

            //re-sort them by date_given
            $tmp = array();
            $out = array();
            foreach ($ret as $k => $v) {
                $tmp[$k] = $v->date_given;
            }

            arsort($tmp);
            foreach ($tmp as $k => $v) {
                $out[] = $ret[$k];
            }

            foreach ($out as $k => $v) {
                $talks[] = array(
                    'title'   => $v->talk_title, 'desc' => $v->talk_desc,
                    'speaker' => $v->speaker,
                    'date'    => date('r', $v->date_given), 'tid' => $v->tid,
                    'link'    => $this->config->site_url() . 'talk/view/' . $v->tid
                );
            }

            //on to the comments!
            $ecom = $this->ecm->getUserComments($userId);
            foreach ($ecom as $k => $v) {
                $comments[] = array(
                    'content'  => $v->comment,
                    'date'     => date('r', $v->date_made), 'type' => 'event',
                    'event_id' => $v->event_id
                );
            }

            $tcom = $this->tcm->getUserComments($userId);
            foreach ($tcom as $k => $v) {
                $comments[] = array(
                    'content'  => $v->comment,
                    'date'     => date('r', $v->date_made), 'type' => 'talk',
                    'event_id' => ''
                );
            }
        }

        $data = array(
            'talks'    => $talks,
            'comments' => $comments,
            'username' => $this->session->userdata('username')
        );

        $this->load->view('feed/user', $data);
    }
}

