<?php
/**
 * Widget pages controller.
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
 * Widget pages controller.
 *
 * Responsible for building and displaying the remote site widgets..
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
class Widget extends MY_Controller
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
     * Main widget page shows a blank response.
     *
     * @return void
     */
    public function index()
    {
    }

    /**
     * Fetches data to populate the Javascript of the widget with.
     *
     * @param string  $type The type of data to retrieve
     * @param integer $id   The id of the data to retrieve
     *
     * @return void
     */
    public function fetchdata($type, $id)
    {
        $render_to    = $this->input->get('render_to');
        $display_type = $this->input->get('display_type');

        switch (strtolower($type)) {
        case 'talk':
            $this->load->model('talks_model');
            $data = $this->talks_model->getTalks($id);
            break;
        case 'event':
            $this->load->model('event_model');
            $data = $this->event_model->getEventDetail($id);
            break;
        case 'user':
            $this->load->model('talks_model');
            $this->load->model('user_model');
            $user = $this->user_model->getUser($id);
            $data = array(
                'username'  => $user[0]->username,
                'full_name' => $user[0]->full_name,
                'talks'     => $this->talks_model->getUserTalks($id)
            );
            break;
        case 'vote':
            $this->load->model('talks_model');
            $data = $this->talks_model->getTalks($id);
            break;
        }
        echo 'joindin.jsonpCallback(
			' . $id . ',
			"' . strtolower($type) . '",
			"' . $display_type . '",
			"' . $render_to . '",
			' . json_encode($data) . ')';
    }

    /**
     * Writes a comment to the error log.
     *
     * @return void
     */
    public function postdata()
    {
        error_log('vote comment: ' . $this->input->post('vote_comment'));
    }

    /**
     * Displays the details of the given event.
     *
     * @return void
     */
    public function event()
    {
        $this->load->helper('url');
        $this->load->model('event_model', 'event');

        $p            = explode('/', uri_string());
        $event_detail = $this->event->getEventDetail($p[3]);

        $data = array(
            'event' => $event_detail[0]
        );

        $this->load->view('widget/event', $data);
    }

    /**
     * Displays the details of a given talk and inserts data in the error log.
     *
     * @return void
     */
    public function talk()
    {
        $this->load->helper('url');
        $this->load->helper('cookie');
        $this->load->model('talks_model', 'talk');
        $this->load->model('talk_comments_model', 'tcm');
        //$p=explode('/',uri_string());

        //The talk ID is in $p[3]
        //The type is in $p[5]

        error_log('uri: ' . uri_string());
        error_log('cb: ' . $this->input->get('callback'));
        error_log('rating: ' . $this->input->get('rating'));
        error_log('comment: ' . $this->input->get('comment'));

        echo "joindin.voteCallback('test')";

        $arr = array(
            'talk_id'      => $this->input->get('talk_id'),
            'rating'       => $this->input->get('rating'),
            'comment'      => $this->input->get('comment'),
            'date_made'    => time(),
            'user_id'      => ($this->user_model->isAuth())
                ? $this->session->userdata('ID')
                : '0',
            'comment_type' => 'comment',
            'active'       => 1,
            'private'      => 0
        );

        error_log(print_r($arr, true));
        $this->db->insert('talk_comments', $arr);
    }
}

?>