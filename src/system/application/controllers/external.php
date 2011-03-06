<?php

/**
 * External pages controller.
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
 * External pages controller.
 *
 * Controller tasked with executing externally triggered scripts, automated
 * sending of twitter messages.
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
	 */
	public function send_pending_event_email()
	{
		$this->load->library('sendemail');
		$this->load->model('event_model');
		
		$events = $this->event_model->getEventDetail(null,null,null,true);
		$this->sendemail->sendPendingEvents($events);
	}

	/**
	 * Sorts the twitter search items depending on creation time
	 *
	 * @return int
	 */
	public function twitter_sort($a, $b)
	{
		$tsA = strtotime($a->created_at);
		$tsB = strtotime($b->created_at);

		if ($tsA > $tsB) {
			return 1;
		} else if ($tsA < $tsB) {
			return -1;
		} else {
			return 0;
		}
	}

	/**
	 * Fetches all twitter messages tagged with #joindin since the last
	 * time it has run and processes those for feedback messages in the form:
	 * "talkid rating comment #joindin" such as:
	 * "2 5 I am just testing something here! #joindin"
	 */
	public function twitter_process_feedback()
	{
		// only execute when invoked from a cron job
		if (!defined('IS_CRON')) {
			return false;
		}

		$this->load->library('twitter');
		$this->load->model('user_model');
		$this->load->model('talk_comments_model');

		$search = $this->twitter->querySearchAPI('#joindin', true);
		usort($search[0], array($this, 'twitter_sort'));
		foreach ($search[0] as $item) {
			if (preg_match('/(?P<talk>\d+)\s+(?P<rating>[1-5])\s+(?P<comment>.*)\s+[#@]joindin/', $item->text, $m)) {
				$uid = $this->user_model->getUserIdByTwitter($item->from_user);

				$arr = array(
					'talk_id'   => $m['talk'],
					'rating'    => $m['rating'],
					'comment'   => $m['comment'],
					'date_made' => strtotime($item->created_at), 'private' => false,
					'active'    => 1,
					'user_id'   => $uid,
				);

				if ($this->talk_comments_model->hasUserCommented($m['talk'], $uid)) {
					$this->db->where('user_id', $uid);
					$this->db->update('talk_comments', $arr);
				} else {
					$this->db->insert('talk_comments', $arr);
				}
			}
		}
	}
}

?>
