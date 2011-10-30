<?php

/**
 * Event pages controller.
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
 * Event pages controller.
 *
 * Responsible for displaying all pages related to events and processing of
 * other HTTP requests concerning events (i.e. AJAX).
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
        $this->template->write_view('content', 'error/404', array());
        $this->template->render();
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
    function _runList($type, $pending = false, $per_page = null, $current_page = null)
    {
        //$prefs = array(
        //    'show_next_prev' => TRUE, 'next_prev_url' => '/event'
        //);

        $this->load->helper('form');
        $this->load->helper('reqkey');
        $this->load->library('timezone');
        $this->load->model('event_model');
        $this->load->model('user_attend_model');

        $total_count = null;

        $total_count = null;

        switch ($type) {
        case 'upcoming':
            $events = $this->event_model->getUpcomingEvents(null);
            break;
        case 'past':
            $events = $this->event_model->getPastEvents(null, $per_page, $current_page);
            break;
        case 'pending':
            $events = $this->event_model->getEventDetail(
                null, null, null, $pending
            );
            break;
        case 'hot':
            // hot is the default case
        default: 
            $events = $this->event_model->getHotEvents(null);
            break;
        }
        if (isset($events['total_count'])) {
            $total_count = $events['total_count'];
            unset($events['total_count']);
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
            'type'   		=> $type,
            'events' 		=> $events,
            'month'  		=> null,
            'day'    		=> null,
            'year'   		=> null,
            'all'    		=> true,
            'reqkey' 		=> $reqkey,
            'seckey' 		=> buildSecFile($reqkey),
            'total_count' 	=> $total_count,
            'current_page' 	=> $current_page,
            'view_type'		=> $type
            //'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false
        );
        $this->template->write_view('content', 'event/main', $arr, true);

        $events 	= $this->event_model->getCurrentCfp();
        $this->template->parse_view('sidebar2','event/_event-cfp-sidebar', array('events'=>$events));

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
        $type = ($pending) ? 'pending' : 'hot';
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
     * @return void
     */
    function past($current_page = null)
    {
        // Don't display pending "past" events
        $pending = false;
        $this->_runList('past', $pending, 10, $current_page);
    }

    /**
     * Displays an overview of all pending events.
     *
     * @return void
     */
    function pending()
    {

        if (!$this->user_model->isAuth()) {
            redirect('/user/login', 'refresh');
        }
        if (!$this->user_model->isSiteAdmin()) {
            redirect();
        }

        $this->index(true);
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
        // user needs to log in at least
        if (!$this->user_model->isAuth()) {
            redirect('/user/login', 'refresh');
        }
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
        $this->load->model('tags_events_model','tagsEvents');

        $config = array(
          'upload_path'   => $_SERVER['DOCUMENT_ROOT'] . '/inc/img/event_icons',
          'allowed_types' => 'gif|jpg|png',
          'max_size'      => '100',
          'max_width'     => '90',
          'max_height'    => '90',
          'max_filename'  => '23'
        );
        $this->load->library('upload', $config);

        $rules = array(
            'event_name'     => 'required',
            'event_loc'      => 'required',
            'event_tz_cont'  => 'required',
            'event_tz_place' => 'required',
            'start_mo'       => 'callback_start_mo_check',
            'end_mo'         => 'callback_end_mo_check',
            'event_stub'     => 'callback_stub_check',
            'cfp_end_mo'	 => 'callback_cfp_end_mo_check',
            'cfp_start_mo'	 => 'callback_cfp_start_mo_check',
            'cfp_url'        => 'callback_cfp_url_check',
            'tagged'         => 'callback_tagged_check'
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
            'event_loc'      => 'Event Venue Name',
            'event_lat'      => 'Latitude',
            'event_long'     => 'Longitude',
            'event_desc'     => 'Event Description',
            'event_tz_cont'  => 'Event Timezone (Continent)',
            'event_tz_place' => 'Event Timezone (Place)',
            'event_href'     => 'Event Link(s)',
            'event_hashtag'  => 'Event Hashtag',
            'event_private'  => 'Private Event',
            'event_stub'     => 'Event Stub',
            'addr'           => 'Google address',
            'cfp_start_mo'   => 'Event Call for Papers Start Date',
            'cfp_start_day'  => 'Event Call for Papers Start Date',
            'cfp_start_yr'   => 'Event Call for Papers Start Date',
            'cfp_end_mo'     => 'Event Call for Papers End Date',
            'cfp_end_day'    => 'Event Call for Papers End Date',
            'cfp_end_yr'     => 'Event Call for Papers End Date',
            'cfp_url'        => 'Event Call for Papers URL',
            'tagged'         => 'Tagged With'
        );
        $this->validation->set_fields($fields);

        $event_detail = array();
        $min_start_yr = '2008';
        $min_end_yr   = '2008';

        if ($this->validation->run() == false) {
            if ($id) {
                $event_detail = $this->event_model->getEventDetail($id);

                if (date('Y', $event_detail[0]->event_start) < $min_start_yr) {
                    $min_start_yr = date('Y', $event_detail[0]->event_start);
                }
                if (date('Y', $event_detail[0]->event_end) < $min_end_yr) {
                    $min_end_yr = date('Y', $event_detail[0]->event_end);
                }

                $this->validation->event_cfp_start = $event_detail[0]->event_cfp_start;
                $this->validation->event_cfp_end   = $event_detail[0]->event_cfp_end;
                $this->validation->event_cfp_url   = $event_detail[0]->event_cfp_url;

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
                $this->validation->cfp_checked =
                        ($event_detail[0]->event_cfp_start != null
                            && $event_detail[0]->event_cfp_end != null) ? true : false;

                if ($this->input->post('is_cfp') == null && $id == null) {

                    $this->validation->event_cfp_start = time();
                    $this->validation->event_cfp_end   = time();

                } elseif ($this->input->post('is_cfp') == '1') {

                    $this->validation->cfp_checked   = true;
                    $this->validation->event_cfp_end = mktime(
                            0,0,0,
                            $this->input->post('cfp_end_mo'),
                            $this->input->post('cfp_end_day'),
                            $this->input->post('cfp_end_yr')
                    );
                    $this->validation->event_cfp_start = mktime(
                            0,0,0,
                            $this->input->post('cfp_start_mo'),
                            $this->input->post('cfp_start_day'),
                            $this->input->post('cfp_start_yr')
                    );

                }
            }

            // this section only needed for edit, not add
            if ($id) {
                // be sure that the image for the event actually exists
                $eventIconPath = $_SERVER['DOCUMENT_ROOT'] . '/inc/img/event_icons/'.$event_detail[0]->event_icon;

                if (!is_file($eventIconPath)) {
                    $event_detail[0]->event_icon = 'none.gif';
                }

                // Get Current Tags
                $currentTags = $this->tagsEvents->getTags($id);
                $ctags = array();
                foreach ($currentTags as $tag) {
                    $ctags[] = $tag->tag_value;
                }

                // Get our submitted tags
                $tags = $this->input->post('tagged') ? $this->input->post('tagged') : $ctags;

                // If tags is a string format it to an array
                if (is_string($tags)) {
                    if ($tags != '' && strpos($tags, ',') === false) {
                        $tagList[] = trim($tags);
                    } else {
                        $tagList = (strpos($tags, ',')) ? explode(',', $tags) : array();
                    }
                } else {
                    $tagList = $tags;
                }

                // Remove any duplicate tags
                if (count($tagList) > 1) {
                    function trim_tags(&$tag) {
                        $tag = trim($tag);
                    }
                    array_walk($tagList,'trim_tags');
                    $tagList = array_unique($tagList);
                }

                // Convert array to string
                $this->validation->tagged = (count($tagList) > 0) ? implode(', ', $tagList) : '';
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
                'event_stub'     => $this->input->post('event_stub'),
                'event_contact_name'  => $this->input->post('event_contact_name'),
                'event_contact_email' => $this->input->post('event_contact_email'),
                'event_cfp_url'	      => $this->input->post('cfp_url')
            );

            $is_cfp = $this->input->post('is_cfp');
            if ($is_cfp) {

                $arr['event_cfp_start'] = mktime(
                        0,0,0,
                        $this->input->post('cfp_start_mo'),
                        $this->input->post('cfp_start_day'),
                        $this->input->post('cfp_start_yr')
                );
                $arr['event_cfp_end'] = mktime(
                        0,0,0,
                        $this->input->post('cfp_end_mo'),
                        $this->input->post('cfp_end_day'),
                        $this->input->post('cfp_end_yr')
                );
                $this->validation->cfp_checked     = true;
                $this->validation->event_cfp_end   = $arr['event_cfp_end'];
                $this->validation->event_cfp_start = $arr['event_cfp_start'];
                $this->validation->event_cfp_url   = $this->input->post('cfp_url');
            } else {
                // it's empty, remove any values
                $arr['event_cfp_start'] = null;
                $arr['event_cfp_end']	= null;
                $arr['event_cfp_url']	= null;
            }

            if ($this->upload->do_upload('event_icon')) {
                $updata = $this->upload->data();
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

            // see if we have tags
            //------------------------
            $tags    = explode(',', $this->input->post('tagged'));
            $tagList = '';
            $currentTags = $this->tagsEvents->getTags($id);

            // parse them into an array
            $ctags = array();
            foreach ($currentTags as $ctag) {
                $ctags[$ctag->tag_value] = $ctag;
            }

            foreach ($tags as $tag) {
                $tag = trim($tag);

                // if it already exists, remove it from our array
                if (array_key_exists($tag, $ctags)) {
                    unset($ctags[$tag]);
                }

                $this->tagsEvents->addTag($id, $tag);
                $tagList[] = $tag;
            }

            // see if we have any left overs
            $this->tagsEvents->removeUnusedTags($id, $ctags);

            $this->validation->tagged = implode(array_unique($tagList), ', ');
            //------------------------

            if (!$is_cfp) {
                    $this->validation->event_cfp_start 	= time();
                    $this->validation->event_cfp_end 	= time();
            }

            $arr = array(
                'msg' => 'Data saved! <a href="/event/view/' . $id . '">View event</a>',
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
        // user needs to log in at least
        if (!$this->user_model->isAuth()) {
            redirect('/user/login', 'refresh');
        }
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
        $this->load->helper('tabs');
        $this->load->library('validation');
        $this->load->library('defensio');
        $this->load->library('spam');
        $this->load->library('timezone');
        $this->load->library('gravatar');
        $this->load->plugin('captcha');
        $this->load->model('event_model');
        $this->load->model('event_comments_model');
        $this->load->model('user_attend_model', 'uam');
        $this->load->model('talk_track_model', 'ttm');
        $this->load->model('event_track_model', 'etm');
        $this->load->model('talk_comments_model', 'tcm');
        $this->load->model('user_admin_model', 'uadm');
        $this->load->model('tags_events_model','eventTags');
        $this->load->model('talks_model');
        $this->load->model('Pending_talk_claims_model','pendingTalkClaims');

        // validate user input (id)
        if (!ctype_digit((string)$id)) {
            show_error('An invalid event id was provided');
        }

        $events     = $this->event_model->getEventDetail($id);
        $evt_admins = $this->event_model->getEventAdmins($id);

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
            'talk_stats'     => $talk_stats,
            'tab'			 => '',
            'tags'           => $this->eventTags->getTags($id),
            'prompt_event_comment'	 => false
            //'started'=>$this->tz->hasEvtStarted($id),
        );

        $tabList = array('talks','comments','statistics', 'evt_related', 'slides', 'tracks');
        if ($opt == 'track') {
            $arr['track_filter'] = $opt_id;
            $arr['track_data']   = null;
            foreach ($arr['tracks'] as $tr) {
                if ($tr->ID == $opt_id) {
                    $arr['track_data'] = $tr;
                }
            }
        } elseif (in_array(strtolower($opt), $tabList)) {
            $arr['tab'] = strtolower($opt);
        }

        //our event comment form
        $rules = array(
            'event_comment' => 'required',
            'cinput'        => 'required|callback_cinput_check'
        );
        $fields = array(
            'event_comment' => 'Event Comment',
            'cinput'        => 'Captcha'
        );
        $this->validation->set_fields($fields);
        $this->validation->set_rules($rules);

        if ($this->validation->run() != false) {
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

            // $this->spam->check('regex', $ec['comment']);

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

        $this->gravatar->decorateUsers($attend, 20); // Add 20px gravatar info to $attend

        if (count($attend) > 0) {
            $this->template->write_view(
                'sidebar3',
                'event/_event_attend_gravatar', array(
                    'attend_list'        => $attend,
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
                        $this->pendingTalkClaims->getEventTalkClaims($id)
                        //$this->uadm->getPendingClaim_TalkSpeaker($id)
                    )
                )
            );
        }
        
        // Get the start of the last day for prompting attending users
        // for event level feedback
        $last_day = strtotime(date('Y-m-d', $events[0]->event_end));
        
        // For single day events, we don't want to prompt for a comment
        // until the event is over
        if ($events[0]->event_start + (60 * 60 * 25) >= $last_day)
        {
            $last_day = $events[0]->event_end;
        }
        
        // Requirements for prompting for event comments
        // - logged in
        // - attending
        // - either last day of event, or no later than 3 months from last day
        // - haven't left feedback yet already
        
                $feedback_deadline = strtotime('+3 month', $last_day);
                
        if ($is_auth && $chk_attend && ( time() > $last_day && time() < $feedback_deadline))
        {
            // Check to see if they have left feedback yet.
            $has_commented_event = $this->event_model->hasUserCommentedEvent($id, $arr['user_id']);
            if (!$has_commented_event)
            {
                $this->template->write_view(
                'sidebar3', 'event/_event_prompt_comment_sidebar',
                    array()
                );
                $arr['prompt_event_comment'] = true;
            }
        }

        $arr['captcha']=create_captcha();
        $this->session->set_userdata(array('cinput'=>$arr['captcha']['value']));

        $this->template->write_view('content', 'event/detail', $arr, true);
        // only show the contact button for logged in users
        if ($is_auth) {
            $this->template->write_view(
                'sidebar2', 'event/_event_contact', array('eid' => $id)
            );
        }
        $this->template->render();
        //$this->load->view('event/detail', $arr);
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
            //$this->load->view('event/delete', $arr);
        } else {
            redirect();
        }
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
            'cfp_url'             => 'CfP URL',
            'end_mo'              => 'Event End Month',
            'end_day'             => 'Event End Day',
            'end_yr'              => 'Event End Year',
            'event_loc'           => 'Event Location',
            'event_tz_cont'       => 'Event Timezone (Continent)',
            'event_tz_place'      => 'Event Timezone (Place)',
            'event_lat'			  => 'Event Latitude',
            'event_long'		  => 'Event Longitude',
            'event_stub'          => 'Event Stub',
            'addr'				  => 'Event Address',
            'cinput'				=> 'Captcha'
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
            'cfp_url'             => 'callback_cfp_url_check',
            'event_desc'          => 'required',
            'cinput'              => 'required|callback_cinput_check'
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
                $sub_arr['event_cfp_url'] = $this->input->post('cfp_url');
            }

            $is_auth  = $this->user_model->isAuth();
            $cname    = $this->input->post('event_contact_name');
            $ccomment = $this->input->post('event_desc');
            $def      = $this->defensio->check(
                $cname, $ccomment, $is_auth, '/event/submit'
            );
            $is_spam  = (string) $def->spam;

            $bypassSpamFilter = $this->input->post('bypass_spam_filter');
            if ($bypassSpamFilter == 1) {
                $is_spam = false;
            }

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
                $msg .= 'View Pending Submissions: ' . $this->config->site_url()
                    . 'event/pending' . "\n\n";
                $msg .= 'Spam check: ' . ($is_spam == 'false')
                    ? 'not spam' : 'spam';

                $admin_emails = $this->user_model->getSiteAdminEmail();
                foreach ($admin_emails as $user) {
                    $from = 'From: ' . $this->config->item('email_submissions');
                    mail($user->email, $subj, $msg, $from);
                }
                $arr['msg'] = sprintf(
                    '<span style="font-size:15px; font-weight:bold;">
                        Event successfully submitted!
                    </span><br/>
                    <span style="font-size:13px;">
                        Once your event is approved, you (or the contact person
                        for the event) will receive an email letting you know
                        it\'s been accepted.<br/>
                        <br/>
                        We\'ll get back to you soon!
                    </span>'
                );

                //put it into the database
                $this->db->insert('events', $sub_arr);

                // They're logged in, so set them as an event admin
                $this->user_admin_model->addPerm(
                    $this->session->userdata('ID'),
                    $this->db->insert_id(),
                    'event'
                );
            } else {
                $arr['msg'] = 'There was an error submitting your event! ' .
                    'Please <a href="' .
                    $this->config->item('email_submissions') .
                    '">send us an email</a> with all the details!';
            }
        } else {
            $this->validation->is_admin = 0;
        }
        $arr['is_auth'] 		= $this->user_model->isAuth();
        $arr['is_site_admin'] 	= $this->user_model->isSiteAdmin();

        $arr['captcha']=create_captcha();
        $this->session->set_userdata(array('cinput'=>$arr['captcha']['value']));

        // user must be logged in to submit
        if (!$this->user_model->isAuth()) {
            $arr['msg'] = sprintf('
                <b>Note</b>: you must be logged in to submit an event!<br/><br/>
                If you do not have an account, you can <a href="/user/register">sign up here</a>.
            ');
        }


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

            // if the admin list is empty, use the contact info on the event
            if (empty($admin_list)) {
                $admin_list[]=array(
                    'full_name' => $evt_detail->event_contact_name,
                    'email' 	=> $evt_detail->event_contact_email
                );
            }

            $this->sendemail->sendEventApproved($evt_detail[0], $admin_list);
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

        $this->load->model('user_admin_model', 'userAdmin');
        $this->load->model('event_model','eventModel');
        $this->load->model('pending_talk_claims_model','pendingClaimsModel');
        $this->load->helper('events_helper');
        $this->load->library('sendemail');

        $newClaims = $this->pendingClaimsModel->getEventTalkClaims($id);

        $claim = $this->input->post('claim');
        $sub   = $this->input->post('sub');
        $msg   = array();

        // look at each claim submitted and approve/deny them
        if ($claim) {
            $approved = 0;
            $denied   = 0;

            foreach ($claim as $claimId => $claimStatus) {
                // Retreive the pending claim before approving or denying as
                // it will be removed by approveClaim() or deleteClaim().
                $pendingClaim = $this->pendingClaimsModel->getClaimDetail($claimId);

                if ($claimStatus=='approve') {
                    $approveCheck = $this->pendingClaimsModel->approveClaim($claimId);
                    if ($approveCheck) {
                        $approved++;
                        $this->_sendClaimSuccessEmail($pendingClaim);
                    }
                } elseif ($claimStatus=='deny') {
                    // delete the claim row
                    $denyCheck = $this->pendingClaimsModel->deleteClaim($claimId);
                    if ($denyCheck) { $denied++; }
                }
            }
            if ($approved>0) { $msg[] = $approved.' claim(s) approved'; }
            if ($denied>0) { $msg[] = $denied.' claims(s) denied'; }
        }

        if (count($msg)>0) {
            $msg = implode(',', $msg);
            // refresh the list
            $newClaims = $this->pendingClaimsModel->getEventTalkClaims($id);
        }

        // Data to pass out to the view
        $arr = array(
            'claims' 	=> $this->userAdmin->getPendingClaims('talk', $id),
            'newClaims' => $newClaims,
            'eventId'   => $id,
            'msg'    	=> $msg,
            'event_detail' => $this->eventModel->getEventDetail($id)
        );

        $this->template->write_view('content', 'event/claim', $arr);
        $this->template->render();
    }

    /**
     * Send an "your claim has been approved" email to the speaker
     * 
     * @param pending_talk_claims_model $claim
     * @return boolean result
     */
    protected function _sendClaimSuccessEmail($claim)
    {
        $result = false;
        if (is_array($claim)) {
            $claim = $claim[0];
        }

        $talk_id = $claim->talk_id;
        $this->load->model('talks_model','talkModel');
        $talk = $this->talkModel->getTalks($talk_id);
        if ($talk) {
            $talk = $talk[0];
            $speakers = $talk->speaker;
            foreach ($speakers as $speaker) {
                if ($speaker->speaker_id == $claim->speaker_id) {
                    // found this speaker

                    if ($speaker->email) {
                        $email = $speaker->email;
                        $talk_title = $talk->talk_title;
                        $evt_name = $talk->event_name;
                        $talk_id = $talk->ID;
                        $this->sendemail->claimSuccess($email, $talk_title, $talk_id, $evt_name);
                        $result = true;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Manage the claims that have been made on events.
     *
     * Not the same as the claims on talks in an event.
     *
     * @return void
     */
    function claims($id = null)
    {
        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect('event/view/' . $id);
        }

        $this->load->model('user_admin_model', 'uam');

        $claims        = $this->uam->getPendingClaims('event');
        $posted_claims = $this->input->post('claim');
        $sub           = $this->input->post('sub');

        if (isset($sub) && !empty($posted_claims)) {
            foreach ($posted_claims as $uam_key => $claim) {
                if ($this->user_model->isSiteAdmin() || $this->uam->checkPerm($uam_key, $id, 'event')) {
                    switch (strtolower($claim)) {
                    case 'approve':
                        // approve the claim
                        $this->uam->updatePerm(
                         $uam_key, array('rcode' => '')
                        );
                        break;
                    case 'deny':
                        // deny the claim - delete it!
                        $this->uam->removePerm($uam_key);
                        break;
                    }
                }
            }
        }

        $claims = $this->uam->getPendingClaims('event', $id);
        $arr = array(
            'claims' => $claims,
            'id' => $id
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
        // user needs to log in at least
        if (!$this->user_model->isAuth()) {
            redirect('/user/login', 'refresh');
        }
        // Be sure they're supposed to be here...
        if (!$this->user_model->isSiteAdmin()
            && !$this->user_model->isAdminEvent($id)
        ) {
            redirect();
        }

        $this->load->library('validation');
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
        if (!$this->user_model->isAuth()) {
            redirect('/user/login', 'refresh');
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

        if ($this->validation->run() != false) {
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
        // user needs to log in at least
        if (!$this->user_model->isAuth()) {
            redirect('/user/login', 'refresh');
        }
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
                'cfp_end_mo_check',
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
     * Ensure that the cfp URL given is an URL
     *
     * @return bool
     */
    function cfp_url_check()
    {
        if (! preg_match("|^https?://|", $this->validation->cfp_url)) {
            $this->validation->set_message(
                'cfp_url_check',
                'Call for Papers URL must start with http:// or https://!'
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
        $str = $this->input->post('cinput');
        if (! is_numeric($str)) {
            // If the user input is not numeric, convert it to a numeric value
            $this->load->plugin('captcha');
            $digits = captcha_get_digits(true);
            $str = array_search(strtolower($str), $digits);
        }

        if ($str != $this->session->userdata('cinput')) {
            $this->validation->_error_messages['cinput_check']
                = 'Incorrect captcha.';

            return false;
        }

        return true;
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

            if (!preg_match('/^[A-Z0-9-_]*$/i', $str)) {
                $this->validation->set_message(
                    'stub_check',
                    'Event stubs may only contain letters, numbers, dashes and underscores.'
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Callback check for tag values in form
     * Only alpha-numeric tags allowed
     * 
     * @param string $tagList Listing of tags, comma separated
     * @return bool
     */
    public function tagged_check($tagList)
    {
        foreach (explode(',', $tagList) as $tag) {
            if (!preg_match('/^[a-zA-Z0-9]+$/', trim($tag))) {
                // escape the "%" since it goes to a sprintf()
                $msg = 'Tag <b>"'.str_replace('%','%%', trim($tag)).'"</b> not valid!';
                $this->validation->set_message('tagged_check', $msg);
                return false;
            }
        }
        return true;
    }

    /**
     * Call for Papers method
     *
     * @param null $eventId[optional] Event ID
     * @return void
     */
    public function callforpapers($eventId=null)
    {	
        $this->load->model('event_model','eventModel');
        $this->load->model('user_attend_model');
        
        $this->load->helper('reqkey');
        
        $reqkey = buildReqKey();
    $arr = array(
                'current_cfp' => $this->eventModel->getCurrentCfp(),
        'reqkey' => $reqkey,
        'seckey' => buildSecFile($reqkey)
    );

        // now add the attendance data
        $uid = $this->user_model->getID();
        foreach ($arr['current_cfp'] as $e) {
            $e->user_attending = ($uid)
                ? $this->user_attend_model->chkAttend($uid, $e->ID)
                : false;
        }
        
        $this->template->write_view('content', 'event/callforpapers', $arr);
        $this->template->render();
    }

    /**
     * Tag action method
     * Displays events tagged with $tagData value
     *
     * @param null $tagData[optional] Tag to pull events for
     * @return void
     */
    public function tag($tagData)
    {
        if ($tagData == null) { redirect('/event'); }
        $this->load->model('event_model','eventModel');

        // get events that are tagged with data from url - single value for now
        $viewData = array(
            'eventDetail'   => $this->eventModel->getEventsByTag($tagData),
            'tagString'     => $tagData
        );

        $this->template->write_view('content', 'event/tag', $viewData);
        $this->template->render();
    }
}

?>
