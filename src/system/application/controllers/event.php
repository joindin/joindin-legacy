<?php

/**
 * Event pages controller.
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
 * Event pages controller.
 *
 * Responsible for displaying all pages related to events and processing of
 * other HTTP requests concerning events (i.e. AJAX).
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
class Event extends Controller
{

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * This controller also checks for branding and applies it when necessary.
     *
     * @return void
     */
    function Event()
    {
        parent::Controller();

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();

        // Check to see if they need a custom CSS layout
        $this->load->model('event_themes_model', 'eventThemes');

        $ret      = explode('/', $_SERVER['REQUEST_URI']);
        $event_id = (isset($ret[3]) && is_numeric($ret[3])) ? $ret[3] : null;
        $theme    = $this->eventThemes->getActiveTheme($event_id);

        // has active theme...use it!
        if ($event_id && $theme) {
            $this->template->write('css', '/inc/css/event/' . $theme[0]->css_file);
        }
    }

    /**
     * Finds event by slug (name) and redirects to the correct view page.
     *
     * Outputs error in case no event with that slug / name could be found.
     *
     * @param string $in Name of the event to redirect to.
     *
     * @see Event::view()
     *
     * @return void
     */
    function cust($in)
    {
        $this->load->helper('url');
        $this->load->model('event_model');
        $id = $this->event_model->getEventIdByName($in);

        if (isset($id[0]->ID)) {
            redirect('event/view/' . $id[0]->ID);
        } else {
            echo 'error';
        }
    }

    /**
     * Helper method to display a selection of special lists (all, hot,
     * upcoming, past).
     *
     * @param string $type    Type of list to show, may be either hot, upcoming,
     *                        past. Anything else will result in all events
     *                        being shown.
     * @param bool   $pending Flag indicating whether to show pending or active
     *                        events.
     *
     * @return void
     */
    function _runList($type, $pending = false)
    {
        //$prefs = array(
        //    'show_next_prev' => TRUE, 'next_prev_url' => '/event'
        //);

        $this->load->helper('form');
        $this->load->helper('reqkey');
        $this->load->library('timezone');
        $this->load->model('event_model');
        $this->load->model('user_attend_model');
        $this->load->helper('mycal');
        //$this->load->library('calendar',$prefs);

        switch ($type) {
        case 'hot':
            $events = $this->event_model->getHotEvents(null);
            break;
        case 'upcoming':
            $events = $this->event_model->getUpcomingEvents(null);
            break;
        case 'past':
            $events = $this->event_model->getPastEvents(null);
            break;
        default:
            $events = $this->event_model->getEventDetail(
                null, null, null, $pending
            );
            break;
        }

        // now add the attendance data
        $uid = $this->user_model->getID();
        foreach ($events as $e) {
            $e->user_attending = ($uid)
                ? $this->user_attend_model->chkAttend($uid, $e->ID)
                : false;
        }

        $reqkey = buildReqKey();

        $arr = array(
            'type'   => $type,
            'events' => $events,
            'month'  => null,
            'day'    => null,
            'year'   => null,
            'all'    => true,
            'reqkey' => $reqkey,
            'seckey' => buildSecFile($reqkey)
            //'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false
        );

        $this->template->write_view('content', 'event/main', $arr, true);
        $this->template->render();
    }

    /**
     * Displays a list of all pending or upcoming events.
     *
     * @param bool $pending Flag indicating whether to show pending or upcoming
     *                      events
     *
     * @return bool
     */
    function index($pending = false)
    {
        if (apache_getenv('USE_EID')) {
            $this->view(apache_getenv('USE_EID'));
            return true;
        }

        $type = ($pending) ? 'pending' : 'upcoming';
        $this->_runList($type, $pending);
    }

    /**
     * Displays an overview of all events.
     *
     * @param bool $pending Flag indicating whether to show active or
     *                      pending events.
     *
     * @return void
     */
    function all($pending = false)
    {
        $this->_runList('index', $pending);
    }

    /**
     * Displays an overview of all hot events.
     *
     * @param bool $pending Flag indicating whether to show active or
     *                      pending events.
     *
     * @return void
     */
    function hot($pending = false)
    {
        $this->_runList('hot', $pending);
    }

    /**
     * Displays an overview of all upcoming events.
     *
     * @param bool $pending Flag indicating whether to show active or
     *                      pending events.
     *
     * @return void
     */
    function upcoming($pending = false)
    {
        $this->_runList('upcoming', $pending);
    }

    /**
     * Displays an overview of all past events.
     *
     * @param bool $pending Flag indicating whether to show active or
     *                      pending events.
     *
     * @return void
     */
    function past($pending = false)
    {
        $this->_runList('past', $pending);
    }

    /**
     * Displays an overview of all pending events.
     *
     * @return void
     */
    function pending()
    {
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->index(true);
    }

    /**
     * Displays a list of events in a specific time period with calendar.
     *
     * @param integer $year  The year to show
     * @param integer $month The month to show
     * @param integer $day   The day to show
     *
     * @return void
     */
    function calendar($year = null, $month = null, $day = null)
    {
        $this->load->model('event_model');
        $this->load->model('user_attend_model');
        $this->load->helper('reqkey');
        $this->load->helper('mycal');
        $this->load->library('timezone');

        if (!$year) {
            $year = date('Y');
        }

        if (!$month) {
            $month = date('m');
        }

        $checkDay = $day === null ? 1 : $day;

        if (!checkdate((int) $month, (int) $checkDay, (int) $year)) {
            $day   = null;
            $month = date('m');
            $year  = date('Y');
        }

        $start  = mktime(0, 0, 0, $month, $day === null ? 1 : $day, $year);
        $end    = mktime(
            23, 59, 59, $month, $day === null ? date('t', $start) : $day, $year
        );
        $events = $this->event_model->getEventDetail(null, $start, $end);

        // now add the attendance information
        $uid = $this->user_model->getID();
        foreach ($events as $e) {
            $e->user_attending = ($uid)
                ? $this->user_attend_model->chkAttend($uid, $e->ID)
                : false;
        }

        $reqkey = buildReqKey();

        $arr = array(
            'events' => $events,
            'month'  => $month,
            'day'    => $day,
            'year'   => $year,
            'reqkey' => $reqkey,
            'seckey' => buildSecFile($reqkey)
        );

        $this->template->write_view('content', 'event/main', $arr, true);
        $this->template->render();
    }

    /**
     * Updates an existing event or adds a new one for Site Admins.
     *
     * Note that add() actually does edit, and submit() does add for users.
     *
     * @param integer|null $id The ID of the event to edit (optional)
     *
     * @return void
     */
    function add($id = null)
    {
        //check for admin
        if ($id) {
            if (!$this->user_model->isAdminEvent($id)) {
                redirect();
            }
        } else {
            if (!$this->user_model->isSiteAdmin()) {
                redirect();
            }
        }

        if ($id) {
            $this->edit_id = $id;
        }

        $this->load->helper('form');
        $this->load->helper('custom_timezone');
        $this->load->library('validation');
        $this->load->library('timezone');
        $this->load->model('event_model');

        $config = array(
          'upload_path'   => $_SERVER['DOCUMENT_ROOT'] . '/inc/img/event_icons',
          'allowed_types' => 'gif|jpg|png',
          'max_size'      => '100',
          'max_width'     => '90',
          'max_heigth'    => '90'
        );
        $this->load->library('upload', $config);

        $rules = array(
            'event_name'     => 'required',
            'event_loc'      => 'required',
            'event_tz_cont'  => 'required',
            'event_tz_place' => 'required',
            'start_mo'       => 'callback_start_mo_check',
            'end_mo'         => 'callback_end_mo_check',
            'event_stub'     => 'callback_stub_check'
        );
        $this->validation->set_rules($rules);

        $fields = array(
            'event_name'     => 'Event Name',
            'start_mo'       => 'Start Month',
            'start_day'      => 'Start Day',
            'start_yr'       => 'Start Year',
            'end_mo'         => 'End Month',
            'end_day'        => 'End Day',
            'end_yr'         => 'End Year',
            'event_loc'      => 'Event Location',
            'event_lat'      => 'Latitude',
            'event_long'     => 'Longitude',
            'event_desc'     => 'Event Description',
            'event_tz_cont'  => 'Event Timezone (Continent)',
            'event_tz_place' => 'Event Timezone (Place)',
            'event_href'     => 'Event Link(s)',
            'event_hashtag'  => 'Event Hashtag',
            'event_private'  => 'Private Event',
            'event_stub'     => 'Event Stub'
        );
        $this->validation->set_fields($fields);

        $event_detail = array();
        $min_start_yr = date('Y');
        $min_end_yr   = date('Y');

        if ($this->validation->run() == false) {
            if ($id) {
                $event_detail = $this->event_model->getEventDetail($id);

                if (date('Y', $event_detail[0]->event_start) < $min_start_yr) {
                    $min_start_yr = date('Y', $event_detail[0]->event_start);
                }
                if (date('Y', $event_detail[0]->event_end) < $min_end_yr) {
                    $min_end_yr = date('Y', $event_detail[0]->event_end);
                }

                foreach ($event_detail[0] as $k => $v) {
                    if ($k == 'event_start') {
                        $this->validation->start_mo = $this->timezone
                            ->formattedEventDatetimeFromUnixtime(
                                $v,
                                $event_detail[0]->event_tz_cont . '/' .
                                $event_detail[0]->event_tz_place,
                                'm'
                            );
                        $this->validation->start_day = $this->timezone
                            ->formattedEventDatetimeFromUnixtime(
                                $v,
                                $event_detail[0]->event_tz_cont . '/' .
                                $event_detail[0]->event_tz_place,
                                'd'
                            );
                        $this->validation->start_yr = $this->timezone
                            ->formattedEventDatetimeFromUnixtime(
                                $v,
                                $event_detail[0]->event_tz_cont . '/' .
                                $event_detail[0]->event_tz_place,
                                'Y'
                            );
                    } elseif ($k == 'event_end') {
                        $this->validation->end_mo = $this->timezone
                            ->formattedEventDatetimeFromUnixtime(
                                $v,
                                $event_detail[0]->event_tz_cont . '/' .
                                $event_detail[0]->event_tz_place,
                                'm'
                            );
                        $this->validation->end_day = $this->timezone
                            ->formattedEventDatetimeFromUnixtime(
                                $v,
                                $event_detail[0]->event_tz_cont . '/' .
                                $event_detail[0]->event_tz_place,
                                'd'
                            );
                        $this->validation->end_yr = $this->timezone
                            ->formattedEventDatetimeFromUnixtime(
                                $v,
                                $event_detail[0]->event_tz_cont . '/' .
                                $event_detail[0]->event_tz_place,
                                'Y'
                            );
                    } else {
                        $this->validation->$k = $v;
                    }
                }
                $this->validation->event_private = $event_detail[0]->private;
            }

            $arr = array(
                'detail'       => $event_detail,
                'min_start_yr' => $min_start_yr,
                'min_end_yr'   => $min_end_yr
            );

            $this->template->write_view('content', 'event/add', $arr);
            $this->template->render();
        } else {
            //success...
            $arr = array(
                'event_name'  => $this->input->post('event_name'),
                'event_start' => $this->timezone->UnixtimeForTimeInTimezone(
                    $this->input->post('event_tz_cont') . '/' .
                    $this->input->post('event_tz_place'),
                    $this->input->post('start_yr'),
                    $this->input->post('start_mo'),
                    $this->input->post('start_day'), 0, 0, 0
                ),
                'event_end'   => $this->timezone->UnixtimeForTimeInTimezone(
                    $this->input->post('event_tz_cont') . '/' .
                    $this->input->post('event_tz_place'),
                    $this->input->post('end_yr'), $this->input->post('end_mo'),
                    $this->input->post('end_day'), 23, 59, 59
                ),
                'event_loc'      => $this->input->post('event_loc'),
                'event_lat'      => $this->input->post('event_lat'),
                'event_long'     => $this->input->post('event_long'),
                'event_desc'     => $this->input->post('event_desc'),
                'active'         => '1',
                'event_tz_cont'  => $this->input->post('event_tz_cont'),
                'event_tz_place' => $this->input->post('event_tz_place'),
                'event_href'     => $this->input->post('event_href'),
                'event_hashtag'  => $this->input->post('event_hashtag'),
                'private'        => $this->input->post('event_private'),
                'event_tz_cont'  => $this->input->post('event_tz_cont'),
                'event_tz_place' => $this->input->post('event_tz_place'),
                'event_stub'     => $this->input->post('event_stub'),
                'event_contact_name'  => $this->input->post('event_contact_name'),
                'event_contact_email' => $this->input->post('event_contact_email'),
            );

            if ($this->upload->do_upload('event_icon')) {
                $updata            = $this->upload->data();
                $arr['event_icon'] = $updata['file_name'];
            }

            // edit
            if ($id) {
                $this->db->where('id', $this->edit_id);
                $this->db->update('events', $arr);
                $event_detail = $this->event_model->getEventDetail($id);
            } else {
                $this->db->insert('events', $arr);
                $id = $this->db->insert_id();
            }

            $arr = array(
                'msg'          => 'Data saved! <a href="/event/view/' . $id .
                    '">View event</a>',
                'min_start_yr' => $min_start_yr,
                'min_end_yr'   => $min_end_yr,
                'detail'       => $event_detail
            );

            $this->template->write_view('content', 'event/add', $arr);
            $this->template->render();
        }
    }

    /**
     * Displays the edit page, or updates, an existing event.
     *
     * Actually redirects to the add action.
     *
     * @param integer $id The ID of the event to edit
     *
     * @see Event::add()
     *
     * @return void
     */
    function edit($id)
    {
        if (!$this->user_model->isAdminEvent($id)) {
            redirect();
        }

        $this->add($id);
    }

    /**
     * Displays a detailed overview of a specific event.
     *
     * @param integer     $id     The ID of the event to show
     * @param string|null $opt    filtering type, may be 'track' (optional)
     * @param mixed       $opt_id filter text / id (optional)
     *
     * @return bool
     */
    function view($id, $opt = null, $opt_id = null)
    {
        $this->load->helper('form');
        $this->load->helper('reqkey');
        $this->load->helper('events');
        $this->load->library('validation');
        $this->load->library('defensio');
        $this->load->library('spam');
        $this->load->library('timezone');
        $this->load->library('gravatar');
        $this->load->model('event_model');
        $this->load->model('event_comments_model');
        $this->load->model('user_attend_model', 'uam');
        $this->load->model('talk_track_model', 'ttm');
        $this->load->model('event_track_model', 'etm');
        $this->load->model('talk_comments_model', 'tcm');
        $this->load->model('user_admin_model', 'uadm');
        $this->load->model('talks_model');

        // validate user input (id)
        if (!ctype_digit($id)) {
            show_error('An invalid event id was provided');
        }

        $events     = $this->event_model->getEventDetail($id);
        $evt_admins = $this->event_model->getEventAdmins($id);

        // see if the admins have gravatars
        foreach ($evt_admins as $k => $admin) {
            if ($img = $this->gravatar->displayUserImage($admin->ID, true)) {
                $evt_admins[$k]->gravatar = $img;
            }
        }

        if ($events[0]->private == 'Y') {
            $this->load->model('invite_list_model', 'ilm');

            // Private event! Check to see if they're on the invite list!
            $is_auth    = $this->user_model->isAuth();
            $priv_admin = ($this->user_model->isSiteAdmin() ||
                $this->user_model->isAdminEvent($id))
                ? true : false;

            if ($is_auth) {
                $udata = $this->user_model->getUser($is_auth);
                $is_invite = $this->ilm->isInvited($id, $udata[0]->ID);

                //If they're invited, accept if they haven't already
                if ($is_invite) {
                    $this->ilm->acceptInvite($id, $udata[0]->ID);
                }

                if (!$is_invite && !$priv_admin) {
                    $arr = array(
                        'detail'  => $events,
                        'is_auth' => $is_auth,
                        'admins'  => $evt_admins
                    );

                    $this->template->write_view(
                        'content', 'event/private', $arr, true
                    );

                    // Render the page
                    $this->template->render();
                    return true;
                }
            } else {
                $arr = array(
                    'detail'  => $events,
                    'is_auth' => $is_auth,
                    'admins'  => $evt_admins
                );
                $this->template->write_view('content', 'event/private', $arr, true);

                // Render the page
                $this->template->render();

                return true;
            }
        }

        $talks   = $this->event_model->getEventTalks($id, false);
        $is_auth = $this->user_model->isAuth();

        foreach ($talks as $k => $v) {
            $talks[$k]->tracks = $this->ttm->getSessionTrackInfo($v->ID);

            // if we have a track filter, check it!
            if (strtolower($opt) == 'track' && isset($opt_id)) {
                $has_track = false;
                foreach ($talks[$k]->tracks as $track) {
                    if ($track->ID == $opt_id) {
                        $has_track = true;
                    }
                }

                if (!$has_track) {
                    unset($talks[$k]);
                }
            }
        }

        if ($is_auth) {
            $uid        = $this->session->userdata('ID');
            $chk_attend = ($this->uam->chkAttend($uid, $id)) ? true : false;
        } else {
            $chk_attend = false;
        }

        if (empty($events)) {
            redirect('event');
        }

        if (($events[0]->pending == 1) && !$this->user_model->isSiteAdmin()) {
            $parr = array(
                'detail' => $events
            );
            $this->template->write_view('content', 'event/pending', $parr, true);

            echo $this->template->render();
            return true;
        }

        $talk_stats    = buildTalkStats($this->tcm->getEventComments($id));
        $reqkey        = buildReqKey();
        $attend        = $this->uam->getAttendUsers($id);
        $talks         = $this->talks_model->setDisplayFields($talks);
        $claimed_talks = $this->event_model->getClaimedTalks($id, $talks);

        $claim_detail           = buildClaimDetail($claimed_talks);
        $event_related_sessions = $this->event_model->getEventRelatedSessions($id);

        $arr = array(
            'event_detail'   => $events[0],
            'talks'          => $talks,
            'evt_sessions'   => $event_related_sessions,
            'slides_list'    => buildSlidesList($talks),
            'admin'          => ($this->user_model->isAdminEvent($id))
                ? true : false,
            'claimed'        => $claimed_talks,
            'user_id'        => ($is_auth)
                ? $this->session->userdata('ID') : '0',
            'attend'         => $chk_attend,
            'attend_ct'      => count($attend),
            'reqkey'         => $reqkey, 'seckey' => buildSecFile($reqkey),
            'attending'      => $attend,
            'latest_comment' => $this->event_model->getLatestComment($id),
            'admins'         => $evt_admins,
            'tracks'         => $this->etm->getEventTracks($id),
            'times_claimed'  => $claim_detail['claim_count'],
            'claimed_uids'   => $claim_detail['uids'],
            'claims'         => buildClaims($this->event_model->getEventClaims($id)),
            'talk_stats'     => $talk_stats
            //'attend' =>$this->uam->getAttendCount($id)
            //'started'=>$this->tz->hasEvtStarted($id),
        );

        if ($opt == 'track') {
            $arr['track_filter'] = $opt_id;
            $arr['track_data']   = null;
            foreach ($arr['tracks'] as $tr) {
                if ($tr->ID == $opt_id) {
                    $arr['track_data'] = $tr;
                }
            }
        }

        //our event comment form
        $rules = array(
            'event_comment' => 'required'
        );
        $fields = array(
            'event_comment' => 'Event Comment'
        );
        $this->validation->set_fields($fields);
        $this->validation->set_rules($rules);

        if ($this->validation->run() != true) {
            $ec = array(
                'event_id'  => $id,
                'comment'   => $this->input->post('event_comment'),
                'date_made' => time(), 'active' => 1
            );

            if ($is_auth) {
                $ec['user_id'] = $this->session->userdata('ID');
                $ec['cname']   = $this->session->userdata('username');
            } else {
                $ec['user_id'] = 0;
            }

            // If they're logged in, dont bother with the spam check
            if (!$is_auth) {
                $def_ret = $this->defensio->check(
                    'Anonymous', $ec['comment'], $is_auth,
                    '/event/view/' . $id
                );
                $is_spam = (string) $def_ret->spam;
            } else {
                $is_spam = 'false';
            }

            // $this->spam->check('regex',$ec['comment']);

            if ($is_spam == 'false') {
                $this->db->insert('event_comments', $ec);
                $arr['msg'] = 'Comment inserted successfully!';

                if (isset($def_ret)) {
                    $ec['def_resp_spamn'] = (string) $def_ret->spaminess;
                    $ec['def_resp_spamr'] = (string) $def_ret->spam;
                }

                $to           = array();
                $admin_emails = $this->user_model->getSiteAdminEmail();
                foreach ($admin_emails as $user) {
                    $to[] = $user->email;
                }

                // get whatever email addresses there are for the event
                $admins = $this->event_model->getEventAdmins($id);
                foreach ($admins as $ak => $av) {
                    $to[] = $av->email;
                }

                $content = '';
                $subj    = $this->config->site_url() . ': Event feedback - ' . $id;
                foreach ($ec as $k => $v) {
                    $content .= '[' . $k . '] => ' . $v . "\n\n";
                }

                foreach ($to as $tk => $tv) {
                    $from = 'From: ' . $this->config->item('email_feedback');
                    @mail($tv, $subj, $content, $from);
                }

                $this->session->set_flashdata(
                    'msg', 'Comment inserted successfully!'
                );
            }

            redirect(
                'event/view/' . $events[0]->ID . '#comments', 'location', 302
            );
        }

        $arr['comments'] = $this->event_comments_model->getEventComments($id);

        if (!$is_auth) {
            $info = array(
                'msg' => sprintf(
                    ' <h4 style="color:#3A74C5">New to ' .
                    $this->config->item('site_name') .
                    '?</h4> Find out how we can help you make connections '.
                    'whether you\'re attending or putting on the show. '.
                    '<a href="/about">Click here</a> to learn more!'
                )
            );
            $this->template->write_view('info_block', 'msg_info', $info, true);
        }

        $this->template->write('feedurl', '/feed/event/' . $id);

        if (count($attend) > 0) {
            $this->template->write_view(
                'sidebar3',
                'event/_event_attend_gravatar', array(
                    'attend_list'        => $attend,
                    'gravatar_cache_dir' => $this->config->item('gravatar_cache_dir')
                )
            );
        }

        if ($arr['admin']) {
            $this->template->write_view(
                'sidebar2', 'event/_sidebar-admin',
                array(
                    'eid'         => $id,
                    'is_private'  => $events[0]->private,
                    'evt_admin'   => $this->event_model->getEventAdmins($id),
                    'claim_count' => count(
                        $this->uadm->getPendingClaims_Talks($id)
                    )
                )
            );
        }

        $this->template->write_view('content', 'event/detail', $arr, true);
        $this->template->write_view(
            'sidebar2', 'event/_event_contact', array('eid' => $id)
        );
        $this->template->render();
        //$this->load->view('event/detail',$arr);
    }

    /**
     * Displays the list of attendees for the given event.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function attendees($id)
    {
        $this->load->model('user_attend_model');

        $users = $this->user_attend_model->getAttendees($id);

        $arr = array(
            'users' => $users
        );

        $this->template->write_view('content', 'event/attendees', $arr, true);
        echo $this->template->render('content');
    }

    /**
     * Generates and outputs an ical file of the given event.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function ical($id)
    {
        header('Content-type: text/calendar');
        header('Content-disposition: filename="ical.ics"');

        $this->load->model('event_model');
        $arr = $this->event_model->getEventDetail($id);
        $this->load->view(
            'event/ical', array(
                'data' => $arr
            )
        );
    }

    /**
     * Deletes an event.
     *
     * Only the site of event admin can delete an event.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function delete($id)
    {
        if ($this->user_model->isSiteAdmin()
            || $this->user_model->isAdminEvent($id)
        ) {
            $this->load->helper('form');
            $this->load->library('validation');
            $this->load->model('event_model');

            $arr = array(
                'eid' => $id,
                'details' => $this->event_model->getEventDetail($id)
            );

            $ans = $this->input->post('answer');
            if (isset($ans) && ($ans == 'yes')) {
                $this->event_model->deleteEvent($id);
                $arr = array();
            }

            $this->template->write_view('content', 'event/delete', $arr, true);
            $this->template->render();
            //$this->load->view('event/delete',$arr);
        } else {
            redirect();
        }
    }

    /**
     * ?
     *
     * @param integer $id The id of the event
     *
     * @todo fill in description, I do not know it's function
     *
     * @return void
     */
    function codes($id)
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->library('events');
        $this->load->helper('url');
        $this->load->helper('events');

        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect();
        }

        $rules      = array();
        $fields     = array();
        $codes      = array();
        $full_talks = array();
        $this->load->model('event_model');

        //make our code list for the talks
        $talks = $this->event_model->getEventTalks($id);
        foreach ($talks as $k => $v) {
            $sp = explode(',', $v->speaker);

            foreach ($sp as $sk => $sv) {
                //$str='ec'.str_pad(substr($v->ID,0,2),2,0,STR_PAD_LEFT) .
                //  str_pad($v->event_id,2,0,STR_PAD_LEFT);
                //$str.=substr(md5($v->talk_title.$sk),5,5);
                $str = buildCode($v->ID, $v->event_id, $v->talk_title, trim($sv));

                $codes[] = $str;

                $obj          = clone $v;
                $obj->code    = $str;
                $obj->speaker = trim($sv);
                $full_talks[] = $obj;

                //$rules['email_'.$v->ID]='trim|valid_email';
                $rules['email_' . $v->ID]  = 'callback_chk_email_check';
                $fields['email_' . $v->ID] = 'speaker email';
            }
        }

        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        $cl = $this->event_model->getClaimedTalks($id, $talks);
        foreach ($cl as $k => $v) {
            //$cstr='ec'.str_pad(substr($v->rid,0,2),2,0,STR_PAD_LEFT) .
            //  str_pad($v->tdata['event_id'],2,0,STR_PAD_LEFT);
            //$cstr.=substr(md5($v->tdata['talk_title'].$sk),5,5);
            $sp = explode(',', $v->tdata['speaker']);
            foreach ($sp as $spk => $spv) {
                $code = buildCode(
                    $v->rid, $v->tdata['event_id'], $v->tdata['talk_title'],
                    trim($spv)
                );
                if ($code == $v->rcode) {
                    $cl[$k]->code = $code;
                }
            }
        }

        $arr = array(
            'talks'   => $talks, 'full_talks' => $full_talks, 'codes' => $codes,
            'details' => $this->event_model->getEventDetail($id),
            'claimed' => $cl
        );

        if ($this->validation->run() != false) {
            foreach ($talks as $k => $v) {
                $pv  = $this->input->post('email_' . $v->ID);
                $chk = $this->input->post('email_chk_' . $v->ID);
                if (!empty($pv) && $chk == 1) {
                    //these are the ones we need to send the email to these
                    $this->events->sendCodeEmail(
                        $pv, $codes[$k], $arr['details'], $v->ID
                    );
                }
            }
        } else { /*echo 'fail';*/
        }
        $this->template->write_view('content', 'event/codes', $arr, true);
        $this->template->render();
    }

    /**
     * Handles the user submission of a new event.
     *
     * @return void
     */
    function submit()
    {
        $arr = array();
        $this->load->library('validation');
        $this->load->plugin('captcha');
        $this->load->helper('custom_timezone');
        //$this->load->library('akismet');
        $this->load->library('defensio');
        $this->load->library('timezone');
        $this->load->model('user_admin_model');

        // $cap_arr = array(
        //     'img_path'   => $_SERVER['DOCUMENT_ROOT'] . '/inc/img/captcha/',
        //     'img_url'    => '/inc/img/captcha/', 'img_width' => '130',
        //     'img_height' => '30'
        // );

        $fields = array(
            'event_title'         => 'Event Title',
            'event_contact_name'  => 'Event Contact Name',
            'event_contact_email' => 'Event Contact Email',
            'event_desc'          => 'Event Description',
            'start_mo'            => 'Event Start Month',
            'start_day'           => 'Event Start Day',
            'start_yr'            => 'Event Start Year',
            'is_cfp'              => 'Is CfP',
            'cfp_start_day'       => 'CfP Start Day',
            'cfp_start_mo'        => 'CfP Start Month',
            'cfp_start_yr'        => 'CfP Start Year',
            'cfp_end_day'         => 'CfP End Day',
            'cfp_end_mo'          => 'CfP End Month',
            'cfp_end_yr'          => 'CfP End Year',
            'end_mo'              => 'Event End Month',
            'end_day'             => 'Event End Day',
            'end_yr'              => 'Event End Year',
            'event_loc'           => 'Event Location',
            'event_tz_cont'       => 'Event Timezone (Continent)',
            'event_tz_place'      => 'Event Timezone (Place)',
            'event_stub'          => 'Event Stub',
            //	'cinput'				=> 'Captcha'
        );
        $rules = array(
            'event_title'         => 'required|callback_event_title_check',
            'event_loc'           => 'required',
            'event_contact_name'  => 'required',
            'event_contact_email' => 'required|valid_email',
            'event_tz_cont'       => 'required',
            'event_tz_place'      => 'required',
            'start_mo'            => 'callback_start_mo_check',
            'end_mo'              => 'callback_end_mo_check',
            'cfp_start_mo'        => 'callback_cfp_start_mo_check',
            'cfp_end_mo'          => 'callback_cfp_end_mo_check',
            'event_stub'          => 'callback_stub_check',
            'event_desc'          => 'required',
            //	'cinput'				=> 'required|callback_cinput_check'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        //if we're just loading, give the dates some default values
        if (empty($this->validation->start_mo)) {
            $sel_fields = array(
                'start_mo'      => 'm',
                'start_day'     => 'd',
                'start_yr'      => 'Y',
                'end_mo'        => 'm',
                'end_day'       => 'd',
                'end_yr'        => 'Y',
                'cfp_start_mo'  => 'm',
                'cfp_start_day' => 'd',
                'cfp_start_yr'  => 'Y',
                'cfp_end_mo'    => 'm',
                'cfp_end_day'   => 'd',
                'cfp_end_yr'    => 'Y'
            );
            foreach ($sel_fields as $k => $v) {
                $this->validation->$k = date($v);
            }
            $this->validation->cfp_checked = false;
            $this->validation->is_private = 'n';
        } else {
            $this->validation->cfp_checked = $this->validation->is_cfp;
        }

        if ($this->validation->run() != false) {
            // TODO: add it to our database, but mark it pending

            $tz = $this->input->post('event_tz_cont') . '/' .
                $this->input->post('event_tz_place');

            // Get offset unix timestamp for start of event
            $startUnixTimestamp = $this->timezone->UnixtimeForTimeInTimezone(
                $tz,
                $this->input->post('start_yr'),
                $this->input->post('start_mo'),
                $this->input->post('start_day'), 0, 0, 0
            );

            // Get offset unix timestamp for end of event
            $endUnixTimestamp = $this->timezone->UnixtimeForTimeInTimezone(
                $tz,
                $this->input->post('end_yr'),
                $this->input->post('end_mo'),
                $this->input->post('end_day'), 23, 59, 59
            );

            $sub_arr = array(
                'event_name'     => $this->input->post('event_title'),
                'event_start'    => $startUnixTimestamp,
                'event_end'      => $endUnixTimestamp,
                'event_loc'      => $this->input->post('event_loc'),
                'event_lat'      => $this->input->post('event_lat'),
                'event_long'     => $this->input->post('event_long'),
                'event_desc'     => $this->input->post('event_desc'),
                'active'         => 0,
                'event_stub'     => $this->input->post('event_stub'),
                'event_tz_cont'  => $this->input->post('event_tz_cont'),
                'event_tz_place' => $this->input->post('event_tz_place'),
                'pending'        => 1,
                'private'        => ($this->input->post('is_private') == 'n')
                    ? null : $this->input->post('is_private'),
                'event_contact_name'  => $this->input->post('event_contact_name'),
                'event_contact_email' => $this->input->post('event_contact_email'),
            );

            // check to see if our Call for Papers dates are set...
            $cfp_check = $this->input->post('cfp_start_mo');
            if (!empty($cfp_check)) {
                // Get offset unix timestamp for start of CFP
                $sub_arr['event_cfp_start'] = $this->timezone
                    ->UnixtimeForTimeInTimezone(
                        $tz,
                        $this->input->post('cfp_start_yr'),
                        $this->input->post('cfp_start_mo'),
                        $this->input->post('cfp_start_day'), 0, 0, 0
                    );

                // Get offset unix timestamp for end of CFP
                $sub_arr['event_cfp_end'] = $this->timezone
                    ->UnixtimeForTimeInTimezone(
                        $tz,
                        $this->input->post('cfp_end_yr'),
                        $this->input->post('cfp_end_mo'),
                        $this->input->post('cfp_end_day'), 23, 59, 59
                    );
            }

            $is_auth  = $this->user_model->isAuth();
            $cname    = $this->input->post('event_contact_name');
            $ccomment = $this->input->post('event_desc');
            $def      = $this->defensio->check(
                $cname, $ccomment, $is_auth, '/event/submit'
            );
            $is_spam  = (string) $def->spam;

            if ($is_spam != 'true') {
                //send the information via email...
                $subj = 'Event submission from ' .
                    $this->config->item('site_name');
                $msg  = 'Event Title: ' .
                    $this->input->post('event_title') . "\n\n";
                $msg .= 'Event Description: ' .
                    $this->input->post('event_desc') . "\n\n";
                $msg .= 'Event Date: ' .
                    date('m.d.Y H:i:s', $sub_arr['event_start']) . "\n\n";
                $msg .= 'Event Contact Name: ' .
                    $this->input->post('event_contact_name') . "\n\n";
                $msg .= 'Event Contact Email: ' .
                    $this->input->post('event_contact_email') . "\n\n";
                $msg .= 'Spam check: ' . ($is_spam == 'false')
                    ? 'not spam' : 'spam';

                $admin_emails = $this->user_model->getSiteAdminEmail();
                foreach ($admin_emails as $user) {
                    $from = 'From: ' . $this->config->item('email_submissions');
                    mail($user->email, $subj, $msg, $from);
                }
                $arr['msg'] = sprintf(
                    '<span style="font-size:16px; font-weight:bold;">
                        Event successfully submitted!
                    </span><br/>
					<span style="font-size:14px;">
						Once your event is approved, you (or the contact person
						for the event) will receive an email letting you know
						it\'s been accepted.<br/>
						<br/>
						We\'ll get back with you soon!
					</span>'
                );

                //put it into the database
                $this->db->insert('events', $sub_arr);

                // Check to see if we need to make them an admin of this event
                if ($this->input->post('is_admin')
                    && ($this->input->post('is_admin') == 1)
                ) {
                    $uid  = $this->session->userdata('ID');
                    $rid  = $this->db->insert_id();
                    $type = 'event';
                    $this->user_admin_model->addPerm($uid, $rid, $type);
                }
            } else {
                $arr['msg'] = 'There was an error submitting your event! ' .
                    'Please <a href="' .
                    $this->config->item('email_submissions') .
                    '">send us an email</a> with all the details!';
            }
        } else {
            $this->validation->is_admin = 0;
        }
        $arr['is_auth'] = $this->user_model->isAuth();

        $this->template->write_view('content', 'event/submit', $arr);
        $this->template->write_view('sidebar2', 'event/_submit-sidebar', array());
        $this->template->render();
    }

    /**
     * Export the full event information as a CSV.
     *
     * Including:
     * - Speakers
     * - Sessions
     * - Session ratings/comments
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function export($id)
    {
        $this->load->model('event_model');
        $talks = $this->event_model->getEventFeedback($id);

        $fp = fopen('php://memory', 'w+');
        foreach ($talks as $k => $v) {
            fputcsv($fp, (array) $v);
        }

        rewind($fp);
        $out = stream_get_contents($fp);
        fclose($fp);

        header('Content-type: application/octet-stream');
        header(
            'Content-Disposition: attachment; filename="Event_Comments_' .
            $id . '.csv"'
        );

        echo $out;
    }

    /**
     * Approve a pending event and send emails to the admins (if there are any).
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function approve($id)
    {
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->load->model('event_model');
        $this->load->library('sendemail');
        $this->event_model->approvePendingEvent($id);

        // If we have admins for the event, send them an email to let them know
        $admin_list = $this->event_model->getEventAdmins($id);
        if ($admin_list && count($admin_list) > 0) {
            $evt_detail = $this->event_model->getEventDetail($id);
            $this->sendemail->sendEventApproved($id, $evt_detail, $admin_list);
        }

        // Finally, redirect back to the event!
        redirect('event/view/' . $id);
    }

    /**
     * Allows a user to claim an event.
     *
     * Adds a pending row to the admin table for the site admins to go in
     * and approve.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function claim($id)
    {
        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect('event/view/' . $id);
        }

        $this->load->model('user_admin_model', 'uam');
        $this->load->helper('events_helper');
        $this->load->library('sendemail');

        $claim = $this->input->post('claim');
        $sub   = $this->input->post('sub');

        $msg = array();
        $claims = array();
        foreach ($this->uam->getPendingClaims('talk', $id) as $claim_data) {
            $claims[$claim_data->ua_id] = $claim_data;
        }
        $approved = 0;
        $denied   = 0;

        // If we have claims to process...
        if ($claim && count($claim) > 0 && isset($sub)) {
            foreach ($claim as $k => $v) {
                // be sure it's still a valid claim
                $this->uam->isPendingClaim($k);

                switch (strtolower($v)) {
                case 'approve':
                    $this->db->where('ID', $k);
                    $this->db->update(
                        'user_admin', array('rcode' => '')
                    );

                    $email      = $claims[$k]->email;
                    $evt_name   = $claims[$k]->event_name;
                    $talk_title = $claims[$k]->talk_title;
                    $this->sendemail->claimSuccess(
                        $email, $talk_title, $evt_name
                    );

                    $approved++;
                    break;
                case 'deny':
                    $this->db->delete(
                        'user_admin', array('ID' => $k)
                    );
                    $denied++;
                    break;
                default:
                    /* do nothing, no action taken */
                }

                echo '<br/>';
            }
        }
        if ($approved > 0) {
            $msg[] = $approved . ' approved';
        }
        if ($denied > 0) {
            $msg[] = $denied . ' denied';
        }
        $msg = implode(',', $msg);

        // Data to pass out to the view
        $arr = array(
            'claims' => $this->uam->getPendingClaims('talk', $id),
            'eid'    => $id,
            'msg'    => $msg
        );

        $this->template->write_view('content', 'event/claim', $arr);
        $this->template->render();
    }

    /**
     * Manage the claims that have been made on events.
     *
     * Not the same as the claims on talks in an event.
     *
     * @return void
     */
    function claims()
    {
        if (!$this->user_model->isSiteAdmin()) {
            redirect('event');
        }

        $this->load->model('user_admin_model', 'uam');

        $claims        = $this->uam->getPendingClaims('event');
        $posted_claims = $this->input->post('claim');
        $sub           = $this->input->post('sub');

        if (isset($sub) && !empty($posted_claims)) {
            echo 'sub!';
            foreach ($posted_claims as $uam_key => $claim) {
                switch (strtolower($claim)) {
                case 'approve':
                    // approve the claim
                    echo 'approve';
                    $this->uam->updatePerm(
                        $uam_key, array('rcode' => '')
                    );
                    break;
                case 'deny':
                    // deny the claim - delete it!
                    echo 'deny';
                    $this->uam->removePerm($uam_key);
                    break;
                }
            }
        }

        $claims = $this->uam->getPendingClaims('event');
        $arr = array(
            'claims' => $claims
        );

        $this->template->write_view('content', 'event/claims', $arr);
        $this->template->render();
    }

    /**
     * Import an XML file and push the test information into the table.
     *
     * XML is validated against a document structure in the /inc/xml directory.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function import($id)
    {
        // Be sure they're supposed to be here...
        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect();
        }

        $this->load->library('validation');
        $this->load->library('xmlimport');
        $this->load->library('csvimport');
        $this->load->library('sendemail');
        $this->load->model('event_model', 'em');

        $config['upload_path']   = $_SERVER['DOCUMENT_ROOT'] . '/inc/tmp';
        $config['allowed_types'] = 'csv';
        $this->load->library('upload', $config);

        // Allow them to upload the XML or pull it from another resource
        $rules = array();
        $fields = array(
            'xml_file' => 'File'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        $msg        = null;
        $error_msg  = null;
        $evt_detail = $this->em->getEventDetail($id);

        if (!empty($_POST) && $this->upload->do_upload('xml_file')) {
            // The file's there, lets run our import
            $updata = $this->upload->data();
            $p      = $config['upload_path'] . '/' . $updata['file_name'];
            try {
                $this->csvimport->import($p, $id);
                $msg = 'Import Successful! <a href="/event/view/' . $id .
                    '">View event</a>';

                //send an email to the site admins when it's successful
                $this->sendemail->sendSuccessfulImport($id, $evt_detail);
            } catch (Exception $e) {
                $error_msg = 'Error: ' . $e->getMessage();
            }
            unlink($p);
        } else {
            $error_msg = $this->upload->display_errors();
        }

        $arr = array(
            'details'   => $evt_detail,
            'msg'       => $msg,
            'error_msg' => $error_msg
        );
        $this->template->write_view('content', 'event/import', $arr);
        $this->template->render();
    }

    /**
     * Allows the event/site admins to send and manage invites to their
     * invite-only event.
     *
     * They can see the status of the invites (pending, accepted, requested).
     *
     * @param integer     $id   The id of the event
     * @param string|null $resp Either 'response' or 'request'
     *
     * @return void
     */
    function invite($id, $resp = null)
    {
        $this->load->model('invite_list_model', 'ilm');
        $this->load->library('sendemail');
        $this->load->model('event_model');
        //$this->load->library('validation');

        $msg    = null;
        $detail = $this->event_model->getEventDetail($id);

        $is_auth = $this->user_model->isAuth();
        $user    = ($is_auth) ? $this->user_model->getUser($is_auth) : false;
        $admins  = $this->event_model->getEventAdmins($id);

        if ($resp && $user) {
            switch (strtolower($resp)) {
            case "respond":
                // check their invite, be sure it's an empty status
                $inv = $this->ilm->getInvite($id, $user[0]->ID);
                if (empty($inv[0]->accepted)) {
                    // they're responding to an invite - update the database
                    $this->ilm->acceptInvite($id, $user[0]->ID);
                }

                redirect('event/view/' . $id);
                break;
            case "request":
                // they're requesting an invite, let the admin know!
                $evt_title = $detail[0]->event_name;
                $evt_id    = $detail[0]->ID;
                $this->sendemail->sendInviteRequest(
                    $evt_id, $evt_title, $user, $admins
                );
                $this->ilm->addInvite($id, $user[0]->ID, 'A');

                $arr = array(
                    'detail' => $detail
                );
                $this->template->write_view(
                    'content', 'event/request', $arr
                );
                $this->template->render();
                return;
                break;
            }
        }

        // be sure they're supposed to be here...the rest of this is for admins
        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect();
        }

        $invites = $this->ilm->getEventInvites($id);

        if ($this->input->post('sub')
            && ($this->input->post('sub') == 'Send Invite')
        ) {
            // see if they're adding a username and check to see if it's valid
            $u = $this->input->post('user');
            if (!empty($u)) {
                $ret = $this->user_model->getUser($u);
                if (empty($ret)) {
                    $msg = 'Invalid user <b>' . $u . '</b>!';
                } else {
                    // good user, lets add them to the list (if they're not
                    // there already)
                    $is_invited = $this->ilm->isInvited($id, $ret[0]->ID);
                    if (!$is_invited) {
                        $this->ilm->addInvite($id, $ret[0]->ID);
                        $this->sendemail->sendInvite(
                            $ret[0]->email, $id, $detail[0]->event_name
                        );
                        $msg = 'User <b>' . $u . '</b> has been sent an invite!';
                    } else {
                        $msg = 'User <b>' . $u .
                            '</b> has already been invited to this event!';
                    }
                }
            }
        }

        if ($this->input->post('attend_list')) {
            // managing the list...
            foreach ($invites as $k => $v) {
                // check for... *pending*

                // check to see if we have a delete action
                $del = $this->input->post('del_' . $v->uid);
                if ($del && $del == 'delete') {
                    $this->ilm->removeInvite($id, $v->uid);
                }

                // check to see if there's an "approve" action
                $del = $this->input->post('approve_' . $v->uid);
                if ($del && $del == 'approve') {
                    $this->ilm->updateInviteStatus($id, $v->uid, 'Y');
                }

                // check to see if there's a decline action
                $del = $this->input->post('decline_' . $v->uid);
                if ($del && $del == 'decline') {
                    $this->ilm->removeInvite($id, $v->uid);
                }
            }

            // Refresh the invite list
            $invites = $this->ilm->getEventInvites($id);
            $msg = 'Invite list changes saved!';
        }

        // finally, we send it out to the view....
        $arr = array(
            'eid'        => $id,
            'invites'    => $invites,
            'msg'        => $msg,
            'evt_detail' => $detail
        );

        $this->template->write_view('content', 'event/invite', $arr);
        $this->template->render();
    }

    /**
     * Allow logged in users to send a message to the event admins if any are
     * assigned.
     *
     * Will always send to site admins regardless.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function contact($id)
    {
        // They need to be logged in...
        $is_auth = $this->user_model->isAuth();
        if (!$is_auth) {
            redirect('event/view/' . $id);
        }

        $this->load->model('event_model');
        $this->load->library('validation');
        $this->load->library('sendemail');

        $rules = array(
            'subject'  => 'required',
            'comments' => 'required'
        );
        $this->validation->set_rules($rules);

        $fields = array(
            'subject'  => 'Subject',
            'comments' => 'Comments'
        );
        $this->validation->set_fields($fields);

        $arr = array(
            'detail' => $this->event_model->getEventDetail($id)
        );

        if ($this->validation->run() != true) {
            $user = $this->user_model->getUser($is_auth);

            // grab the event admins
            $admins = $this->event_model->getEventAdmins($id);

            // if there's no event admins, we send it to the site admins
            if (empty($admins)) {
                $admins = $this->user_model->getSiteAdminEmail();
            }

            // push the emails over to the mailer class
            $evt_name = $arr['detail'][0]->event_name;
            $msg      = 'Subject: ' . $this->input->post('subject') . "\n\n";
            $msg     .= $this->input->post('comments');
            $this->sendemail->sendEventContact(
                $id, $evt_name, $msg, $user, $admins
            );

            $arr['msg'] = 'Your comments have been sent to the event '.
                'administrators! They\'ll get back in touch with you soon!';
        } else {
            $arr['msg'] = $this->validation->error_string;
        }

        $this->template->write_view('content', 'event/contact', $arr);
        $this->template->render();
    }

    /**
     * Displays a list of tracks for the given event id.
     *
     * @param integer $id The id of the event
     *
     * @return void
     */
    function tracks($id)
    {
        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect();
        }

        $this->load->model('event_track_model', 'etm');
        $this->load->model('event_model');
        $this->load->helper('reqkey');

        $reqkey = buildReqKey();
        $arr    = array(
            'detail' => $this->event_model->getEventDetail($id),
            'tracks' => $this->etm->getEventTracks($id),
            'admin'  => ($this->user_model->isAdminEvent($id)) ? true : false,
            'reqkey' => $reqkey,
            'seckey' => buildSecFile($reqkey)
        );

        $this->template->write_view('content', 'event/tracks', $arr);
        $this->template->render();
    }

    /**
     * Check the database to be sure we don't have another event by this
     * name, pending or not.
     *
     * @param string $str Name of the event
     *
     * @return bool
     */
    function event_title_check($str)
    {
        $this->load->model('event_model');
        $ret = $this->event_model->getEventIdByTitle($str);
        if (isset($ret[0]->id)) {
            $this->validation->set_message(
                'event_title_check', 'There is already an event by that name!'
            );

            return false;
        }

        return true;
    }

    /**
     * Validate the start date in validation object.
     *
     * @return bool
     */
    function start_mo_check()
    {
        //be sure it's before the end date
        $t = mktime(
            0, 0, 0,
            $this->validation->start_mo,
            $this->validation->start_day,
            $this->validation->start_yr
        );
        $e = mktime(
            0, 0, 0,
            $this->validation->end_mo,
            $this->validation->end_day,
            $this->validation->end_yr
        );

        if ($t > $e) {
            $this->validation->set_message(
                'start_mo_check', 'Start date must be prior to the end date!'
            );

            return false;
        }

        return true;
    }

    /**
     * Validate the end date in validation object.
     *
     * @return bool
     */
    function end_mo_check()
    {
        $st = mktime(
            0, 0, 0,
            $this->validation->start_mo,
            $this->validation->start_day,
            $this->validation->start_yr
        );
        $et = mktime(
            23, 59, 59,
            $this->validation->end_mo,
            $this->validation->end_day,
            $this->validation->end_yr
        );

        if ($et < $st) {
            $this->validation->set_message(
                'end_mo_check', 'End month must be past the start date!'
            );

            return false;
        }

        return true;
    }

    /**
     * Ensure that the date given for the CFP start is before the event date's
     * and that it's before the cfp_end dates.
     *
     * @return bool
     */
    function cfp_start_mo_check()
    {
        $cfp_st = mktime(
            0, 0, 0,
            $this->validation->cfp_start_mo,
            $this->validation->cfp_start_day,
            $this->validation->cfp_start_yr
        );
        $cfp_end = mktime(
            0, 0, 0,
            $this->validation->cfp_end_mo,
            $this->validation->cfp_end_day,
            $this->validation->cfp_end_yr
        );
        $evt_st = mktime(
            0, 0, 0,
            $this->validation->start_mo,
            $this->validation->start_day,
            $this->validation->start_yr
        );

        if ($cfp_st >= $evt_st) {
            $this->validation->set_message(
                'cfp_start_mo_check',
                'Call for Papers must start before the event!'
            );

            return false;
        }

        if ($cfp_st >= $cfp_end) {
            $this->validation->set_message(
                'cfp_start_mo_check', 'Invalid Call for Papers start date!'
            );

            return false;
        }

        return true;
    }

    /**
     * Ensure that the date given for the CFP's end is before the start
     * of the event and that the end date is after the CFP start date.
     *
     * @return bool
     */
    function cfp_end_mo_check()
    {
        $cfp_end = mktime(
            0, 0, 0,
            $this->validation->cfp_end_mo,
            $this->validation->cfp_end_day,
            $this->validation->cfp_end_yr
        );
        $evt_st = mktime(
            0, 0, 0,
            $this->validation->start_mo,
            $this->validation->start_day,
            $this->validation->start_yr
        );

        if ($cfp_end >= $evt_st) {
            $this->validation->set_message(
                'cfp_start_mo_check',
                'Invalid Call for Papers end date! CfP must end before '
                .'event start!'
            );

            return false;
        }

        return true;
    }

    /**
     * Validate e-mail address.
     *
     * @param string $str The e-mail address to be validated
     *
     * @return bool
     */
    function chk_email_check($str)
    {
        $chk_str = str_replace('_', '_chk_', $this->validation->_current_field);
        $val     = $this->input->post($chk_str);

        if (($val == 1) && !$this->validation->valid_email($str)) {
            $this->validation->set_message(
                'chk_email_check', 'Email address invalid!'
            );

            return false;
        }

        return true;
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
        if (($this->input->post('cinput') != $this->session->userdata('cinput'))) {
            $this->validation->_error_messages['cinput_check']
                = 'Incorrect Captcha characters.';

            return false;
        }

        return false;
    }

    /**
     * Checks a stub.
     *
     * @param string $str A stub
     *
     * @todo expand docblock description, currently the function is unclear
     *
     * @return bool
     */
    function stub_check($str)
    {
        if (!empty($str)) {
            $this->load->model('event_model');
            $id  = ($this->uri->segment(3) === false)
                ? null : $this->uri->segment(3);
            $ret = $this->event_model->isUniqueStub($str, $id);

            if (!$ret) {
                $this->validation->set_message(
                    'stub_check',
                    'Please choose another stub - this one\'s already in use!'
                );

                return false;
            }
        }

        return true;
    }
}

?>