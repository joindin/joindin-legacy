<?php
/**
 * Talk pages controller.
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
 * Talk pages controller.
 *
 * Responsible for displaying talk related pages.
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
class Talk extends Controller
{
    var $auth = false;

    /**
     * Constructor, checks whether the user is logged in and passes this to
     * the template.
     *
     * @return void
     */
    function Talk()
    {
        parent::Controller();
        $this->auth = ($this->user_model->isAuth()) ? true : false;

        // check login status and fill the 'logged' parameter in the template
        $this->user_model->logStatus();
    }

    /**
     * Displays a list of popular and recent talks.
     *
     * @return void
     */
    function index()
    {
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->model('talks_model');

        $talks = array(
            'popular' => $this->talks_model->getPopularTalks(),
            'recent'  => $this->talks_model->getRecentTalks()
        );

        $this->template->write_view(
            'content', 'talk/main', array(
                'talks' => $talks
            ), true
        );
        $this->template->render();
    }

    /**
     * Displays the add and edit page and processes the submit.
     *
     * @param mixed|null $id  Either the string 'event' or the id of the talk
     * @param mixed|null $opt Id of the event if $id = 'event'
     *
     * @return void
     */
    function add($id = null, $opt = null)
    {
        $pass   = true;
        $tracks = array();

        $this->load->model('talks_model');
        $this->load->model('event_model');
        $this->load->model('categories_model');
        $this->load->model('lang_model');
        $this->load->helper('form');
        $this->load->library('validation');
        $this->load->library('timezone');
        $this->load->model('event_track_model', 'eventTracks');
        $this->load->model('talk_track_model', 'talkTracks');
        $this->load->model('talk_speaker_model', 'talkSpeakers');

        // check to see if they're supposed to be here
        if (!$this->auth) {
            redirect('user/login', 'refresh');
        }

        $currentUserId = $this->session->userdata('ID');
        if (isset($id) && $id == 'event') {
            $eid = $opt;
            $id  = null;

            if (!$this->user_model->isAdminEvent($eid)) {
                redirect();
            }
        } elseif ($id) {
            $this->edit_id = $id;

            $det = $this->talks_model->getTalks($id);
            $eid = $det[0]->eid;

            // see if they have access to the talk (claimed user,
            // site admin, event admin)
            if ($this->user_model->isAdminEvent($eid)
                || $this->talkSpeakers->hasPerm($currentUserId, $id, 'talk')
            ) {
                // fine, let them through
            } else {
                redirect();
            }
        } elseif (!$id && !$opt) {
            //no options specified!
            redirect();
        }

        $cats  = $this->categories_model->getCats();
        $langs = $this->lang_model->getLangs();

        $rules = array(
            'event_id'     => 'required',
            'talk_title'   => 'required',
            'talk_desc'    => 'required',
            'session_type' => 'required',
            'session_lang' => 'required',
            'given_mo'     => 'callback_given_mo_check'
        );
        $fields = array(
            'event_id'     => 'Event Name',
            'talk_title'   => 'Talk Title',
            'given_mo'     => 'Given Month',
            'given_day'    => 'Given Day',
            'given_yr'     => 'Given Year',
            'given_hour'   => 'Given Hour',
            'given_min'    => 'Given Minute',
            'slides_link'  => 'Slides Link',
            'talk_desc'    => 'Talk Description',
            'session_type' => 'Session Type',
            'session_lang' => 'Session Language',
            'session_track' => 'Session Track'
        );
        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        // if we have the event ID in our option...
        if ($id == null && $opt != null) {
            $tracks = $this->eventTracks->getEventTracks($opt);
        }

        if ($id) {
            $thisTalk = $det[0];
            $det      = $this->talks_model->getTalks($id);

            $events = $this->event_model->getEventDetail($thisTalk->event_id);
            $tracks = $this->eventTracks->getEventTracks($thisTalk->eid);

            $thisTalksEvent = (isset($events[0])) ? $events[0] : array();
            $thisTalksTrack = (isset($tracks[0])) ? $tracks[0] : array();

            $track_info = $this->talkTracks->getSessionTrackInfo($thisTalk->ID);
            $is_private = ($thisTalksEvent->private == 'Y') ? true : false;
            $this->validation->session_track = (empty($track_info))
                ? null : $track_info[0]->ID;

            foreach ($thisTalk as $k => $v) {
                $this->validation->$k = $v;
            }

            // set our speaker information
            $this->validation->speaker
                = $this->talkSpeakers->getSpeakerByTalkId($id);

            $this->validation->eid       = $thisTalk->eid;
            $this->validation->given_day = $this->timezone
                ->formattedEventDatetimeFromUnixtime(
                    $thisTalk->date_given,
                    $thisTalk->event_tz_cont . '/' . $thisTalk->event_tz_place,
                    'd'
                );
            $this->validation->given_mo = $this->timezone
                ->formattedEventDatetimeFromUnixtime(
                    $thisTalk->date_given,
                    $thisTalk->event_tz_cont . '/' . $thisTalk->event_tz_place,
                    'm'
                );
            $this->validation->given_yr = $this->timezone
                ->formattedEventDatetimeFromUnixtime(
                    $thisTalk->date_given,
                    $thisTalk->event_tz_cont . '/' . $thisTalk->event_tz_place,
                    'Y'
                );
            $this->validation->given_hour = $this->timezone
                ->formattedEventDatetimeFromUnixtime(
                    $thisTalk->date_given,
                    $thisTalk->event_tz_cont . '/' . $thisTalk->event_tz_place,
                    'H'
                );
            $this->validation->given_min = $this->timezone
                ->formattedEventDatetimeFromUnixtime(
                    $thisTalk->date_given,
                    $thisTalk->event_tz_cont . '/' . $thisTalk->event_tz_place,
                    'i'
                );

            $this->validation->talkDate = $this->validation->given_yr . '-' .
                $this->validation->given_mo . '-' .
                $this->validation->given_day;

            $this->validation->session_lang = $thisTalk->lang_id;
            $this->validation->session_type = $thisTalk->tcid;
        } else {
            $events         = $this->event_model->getEventDetail($eid);
            $thisTalksEvent = $events[0];
            $det            = array();

            //set the date to the start date of the event
            $this->validation->given_mo   = date('m', $thisTalksEvent->event_start);
            $this->validation->given_day  = date('d', $thisTalksEvent->event_start);
            $this->validation->given_yr   = date('Y', $thisTalksEvent->event_start);
            $this->validation->given_hour = date('H', $thisTalksEvent->event_start);
            $this->validation->given_min  = date('i', $thisTalksEvent->event_start);

            $this->validation->session_track = null;
            $this->validation->speaker       = array();

            // If we have an error but have posted speakers, load them...
            if ($posted_speakers = $this->input->post('speaker_row')) {
                foreach ($posted_speakers as $speaker) {
                    $obj = new stdClass();

                    $obj->speaker_name           = $speaker;
                    $this->validation->speaker[] = $obj;
                    unset($obj);
                }
            }

            $is_private = false;
        }

        if (isset($eid)) {
            $this->validation->event_id = $eid;
        }

        if ($this->validation->run() != false) {
            if (!empty($thisTalksEvent->event_tz_cont)
                && !empty($thisTalksEvent->event_tz_place)
            ) {
                $talk_timezone = new DateTimeZone(
                    $thisTalksEvent->event_tz_cont . '/' .
                        $thisTalksEvent->event_tz_place
                );
            } else {
                $talk_timezone = new DateTimeZone('UTC');
            }

            $talk_datetime = new DateTime($this->input->post('talkDate') . ' ' .
                $this->input->post('given_hour') . ':' .
                $this->input->post('given_min'), $talk_timezone
            );

            // how much wrong will ->format("U") be if I do it now,
            // due to DST changes?
            // only needed until PHP Bug #51051 delivers a better method
            $unix_offset1    = $talk_timezone->getOffset($talk_datetime);
            $unix_offset2    = $talk_timezone->getOffset(new DateTime());
            $unix_correction = $unix_offset1 - $unix_offset2;
            $unix_timestamp  = $talk_datetime->format("U") - $unix_correction;

            $arr = array(
                'talk_title'  => $this->input->post('talk_title'),
                'slides_link' => $this->input->post('slides_link'),
                'date_given'  => $unix_timestamp,
                'event_id'    => $this->input->post('event_id'),
                'talk_desc'   => $this->input->post('talk_desc'),
                'active'      => '1',
                'lang'        => $this->input->post('session_lang')
            );

            if ($id) {
                // update the speaker information
                $this->talkSpeakers->handleSpeakerData(
                    $id, $this->input->post('speaker_row')
                );

                $this->db->where('id', $id);
                $this->db->update('talks', $arr);

                // remove the current reference for the talk category and
                // add a new one
                $this->db->delete(
                    'talk_cat', array(
                        'talk_id' => $id
                    )
                );

                $this->validation->speaker = $this->talkSpeakers
                    ->getTalkSpeakers($id);

                // check to see if we have a track and it's not the "none"
                if ($this->input->post('session_track') != 'none') {
                    $curr_track = (isset($track_info[0]->ID))
                        ? $track_info[0]->ID : null;
                    $new_track  = $this->input->post('session_track');
                    $this->talkTracks->updateSessionTrack(
                        $id, $curr_track, $new_track
                    );
                    $this->validation->session_track = $new_track;
                } elseif ($this->input->post('session_track') == 'none') {
                    //remove the track for the session
                    if (is_array($track_info) && count($track_info) > 0
                        && is_object($thisTalksTrack)
                    ) {
                        $curr_track = $thisTalksTrack->ID;
                        $this->talkTracks->deleteSessionTrack($id, $curr_track);
                    }
                }

                $tc_id = $id;
                $msg   = 'Talk information successfully updated! ' .
                    '<a href="/talk/view/' . $id . '">Return to talk</a>';
                $pass  = true;
            } else {
                //check to be sure its unique
                $q   = $this->db->get_where('talks', $arr);
                $ret = $q->result();

                // check to be sure that all of the talk information is new
                $this->talks_model->isTalkDataUnique(
                    $arr, $this->input->post('speaker_row')
                );

                if (count($ret) == 0) {
                    $this->db->insert('talks', $arr);
                    $tc_id = $this->db->insert_id();

                    // Add the new speakers
                    $this->talkSpeakers->handleSpeakerData(
                        $tc_id, $this->input->post('speaker_row')
                    );
                    $this->validation->speaker = $this->talkSpeakers
                        ->getTalkSpeakers($tc_id);

                    // check to see if we have a track and it's not the "none"
                    if ($this->input->post('session_track') != 'none') {
                        $this->talkTracks->setSessionTrack(
                            $tc_id, $this->input->post('session_track')
                        );
                    }

                    $msg  = 'Talk information successfully added!</br>' .
                        '<a href="/talk/add/event/' . $thisTalksEvent->ID . '">' .
                        'Add another</a> ';
                    $msg .= 'or <a href="/event/view/' . $thisTalksEvent->ID .
                        '">View Event</a>';
                    $pass = true;
                } else {
                    $err  = 'There was an error adding the talk information! ' .
                        '(Duplicate talk)<br/>';
                    $err .= '<a href="/event/view/' . $thisTalksEvent->ID .
                        '">View Event</a>';
                    $pass = false;
                }
            }
            if ($pass) {
                //now make the link between the talk and the category
                $tc_arr = array(
                    'talk_id' => $tc_id,
                    'cat_id'  => $this->input->post('session_type')
                );
                $this->db->insert('talk_cat', $tc_arr);

                if ($id) {
                    redirect('talk/view/' . $id);
                } else {
                    redirect('talk/view/' . $tc_id);
                }
            }
        } 

        $det = $this->talks_model->setDisplayFields($det);
        $out = array(
            'msg'            => (isset($msg)) ? $msg : '',
            'err'            => (isset($err)) ? $err : '',
            'events'         => $events,
            'cats'           => $cats,
            'langs'          => $langs,
            'detail'         => $det,
            'evt_priv'       => $is_private,
            'tracks'         => $tracks,
            'thisTalksEvent' => $thisTalksEvent
        );
        $this->template->write_view('content', 'talk/add', $out, true);
        $this->template->render();
    }

    /**
     * Displays and processes the edit action.
     *
     * Actually uses the add action.
     *
     * @param integer $id The id of the talks
     *
     * @see Talk::add()
     *
     * @return void
     */
    function edit($id)
    {
        $this->add($id);
    }

    /**
     * Displays and processes the delete action.
     *
     * @param integer $id The id of the talks
     *
     * @return void
     */
    function delete($id)
    {
        $this->load->model('talks_model');
        $this->load->model('user_model');
        $this->load->model('talk_track_model', 'talkTracks');

        // check to see if they're supposed to be here
        if (!$this->auth) {
            redirect('/user/login', 'refresh');
        }

        $talk_detail = $this->talks_model->getTalks($id);
        if (empty($talk_detail)) {
            redirect('talk');
        }

        $arr = array(
            'error' => ''
        );

        if ($this->user_model->isAdminEvent($talk_detail[0]->eid)) {
            $this->load->helper('form');
            $this->load->library('validation');
            $this->load->model('talks_model');

            $arr['tid'] = $id;
            if (isset($_POST['answer']) && ($_POST['answer'] == 'yes')) {
                $this->talks_model->deleteTalk($id);

                // delete any records in the tracks table too
                $this->talkTracks->deleteSessionTrack($id);
                unset($arr['tid']);
            }
        } else {
            $arr['error'] = 'No event administration rights';
        }

        $this->template->write_view('content', 'talk/delete', $arr, true);
        $this->template->render();
    }

    /**
     * Displays the details for a talk.
     *
     * @param integer     $id      the id of the talk
     * @param string|null $add_act if 'claim' tries to claim the talk
     * @param string|null $code    code to claim talk with
     *
     * @return void
     */
    function view($id, $add_act = null, $code = null)
    {
        $this->load->model('talks_model');
        $this->load->model('event_model');
        $this->load->model('invite_list_model', 'ilm');
        $this->load->model('user_attend_model');
        $this->load->model('talk_track_model', 'talkTracks');
        $this->load->model('talk_comments_model', 'tcm');
        $this->load->model('talk_speaker_model', 'talkSpeakers');
        $this->load->helper('form');
        $this->load->helper('events');
        $this->load->helper('talk');
        $this->load->helper('reqkey');
        $this->load->plugin('captcha');
        $this->load->library('defensio');
        $this->load->library('spam');
        $this->load->library('validation');
        $this->load->library('timezone');
        $this->load->library('sendemail');

        $msg          = '';

        // filter it down to just the numeric characters
        if (preg_match('/[0-9]+/', $id, $m)) {
            $id = $m[0];
        } else {
            redirect('talk');
        }

        $currentUserId = $this->session->userdata('ID');
        $talk_detail   = $this->talks_model->getTalks($id);
        if (empty($talk_detail)) {
            redirect('talk');
        }

        if ($talk_detail[0]->private == 'Y') {
            if (!$this->user_model->isAuth()) {
                // denied!
                redirect('event/view/' . $talk_detail[0]->eid);
            }

            // if the event for this talk is private, be sure that
            // the user is allowed
            if (!$this->ilm->isInvited($talk_detail[0]->eid, $currentUserId)
                && !$this->user_model->isAdminEvent($talk_detail[0]->eid)
            ) {
                redirect('event/view/' . $talk_detail[0]->eid);
            }
        }

        $claim_status = false;
        $claim_msg    = '';
        if (isset($add_act) && $add_act == 'claim') {
            // be sure they're loged in first...
            if (!$this->user_model->isAuth()) {
                //redirect to the login form
                $this->session->set_userdata(
                    'ref_url', '/talk/view/' . $id . '/claim/' . $code
                );
                redirect('user/login');
            } else {
                $sp = explode(',', $talk_detail[0]->speaker);

                $codes = array();

                //loop through the speakers to make the codes
                foreach ($sp as $k => $v) {
                    // we should be logged in now... lets check and
                    // see if the code is right

                    $str = buildCode(
                        $id, $talk_detail[0]->event_id,
                        $talk_detail[0]->talk_title, trim($v)
                    );

                    $codes[] = $str;
                }

                if (in_array($code, $codes)) {
                    //TODO: linking on the display side to the right user
                    $uid = $this->session->userdata('ID');
                    $ret = $this->talks_model->linkUserRes(
                        $uid, $id, 'talk', $code
                    );
                    if (!$ret) {
                        $claim_status = false;
                        $claim_msg    = 'There was an error claiming your talk!';
                    } else {
                        $claim_status = true;
                        $claim_msg    = 'Talk claimed successfully!';
                    }
                } else {
                    $claim_status = false;
                    $claim_msg   = 'There was an error claiming your talk!';
                }
            }
        }

        $cl = ($r = $this->talks_model->talkClaimDetail($id)) ? $r : false;

        $rules = array(
            'rating' => $cl && $cl[0]->userid == $currentUserId
                ? null : 'required'
        );
        $fields = array(
            'comment' => 'Comment',
            'rating'  => 'Rating'
        );

        // if it's past time for the talk, they're required
        // all other times they're not required...
        if (time() >= $talk_detail[0]->date_given) {
            $rules['comment'] = 'required';
        }

        // this is for the CAPTACHA - it was disabled for authenticated users
        if (!$this->user_model->isAuth()) {
            $rules['cinput']	= 'required|callback_cinput_check';
            $fields['cinput']	= 'Captcha';
        }

        $this->validation->set_rules($rules);
        $this->validation->set_fields($fields);

        if ($this->validation->run() == false) {
            // vote processing code removed
        } else {
            $is_auth = $this->user_model->isAuth();
            $arr = array(
                'comment_type'    => 'comment',
                'comment_content' => $this->input->post('your_com')
            );

            $priv = $this->input->post('private');
            $priv = (empty($priv)) ? 0 : 1;

            $anonymous = $this->input->post('anonymous');
            $anonymous = (empty($anonymous)) ? 0 : 1;

            if (!$is_auth) {
                $sp_ret = $this->spam->check(
                    'regex', $this->input->post('comment')
                );
                error_log('sp: ' . $sp_ret);

                if ($is_auth) {
                    $ec['user_id'] = $this->session->userdata('ID');
                    $ec['cname']   = $this->session->userdata('username');
                } else {
                    $ec['user_id'] = 0;
                    $ec['cname']   = $this->input->post('cname');
                }

                $ec['comment'] = $this->input->post('comment');
                $def_ret       = $this->defensio->check(
                    $ec['cname'], $ec['comment'], $is_auth, '/talk/view/' . $id
                );

                $is_spam = (string) $def_ret->spam;
            } else {
                // They're logged in, let their comments through
                $is_spam = false;
                $sp_ret  = true;
            }

            if ($is_spam != 'true' && $sp_ret == true) {

                $arr = array(
                    'talk_id'   => $id,
                    'rating'    => $this->input->post('rating'),
                    'comment'   => $this->input->post('comment'),
                    'date_made' => time(), 'private' => $priv,
                    'active'    => 1,
                    'user_id'   => ($this->user_model->isAuth() && !$anonymous)
                        ? $this->session->userdata('ID') : '0'
                );

                $out = '';
                if ($this->input->post('edit_comment')) {
                    $cid = $this->input->post('edit_comment');
                    $uid = $this->session->userdata('ID');

                    // be sure they have the right to update the comment
                    $com_detail = $this->tcm->getCommentDetail($cid);
                    if (isset($com_detail[0])
                        && ($com_detail[0]->user_id == $uid)
                    ) {
                        $this->db->where('ID', $cid);
                        $this->db->update('talk_comments', $arr);
                        $out = 'Comment updated!';
                    } else {
                        $out = 'Error on updating comment!';
                    }
                } else {
                    $this->db->insert('talk_comments', $arr);
                    $out = 'Comment added!';
                }

                //send an email when a comment's made
                $msg = '';
                $arr['spam'] = ($is_spam == 'false') ? 'spam' : 'not spam';
                foreach ($arr as $ak => $av) {
                    $msg .= '[' . $ak . '] => ' . $av . "\n";
                }

                //if its claimed, be sure to send an email to the person to tell them
                if ($cl) {
                    $this->sendemail->sendTalkComment(
                        $id, $cl[0]->email, $talk_detail, $arr
                    );
                }

                $this->session->set_flashdata('msg', $out);
            }
            redirect(
                'talk/view/' . $talk_detail[0]->tid . '#comments', 'location', 302
            );
        }

        $captcha=create_captcha();
        $this->session->set_userdata(array('cinput'=>$captcha['value']));

        $reqkey      = buildReqKey();
        $talk_detail = $this->talks_model->setDisplayFields($talk_detail);

        // catch this early...if it's not a valid session...
        if (empty($talk_detail)) {
            redirect('talk');
        }

        $is_talk_admin = $this->user_model->isAdminTalk($id);

        // Retrieve ALL comments, then Reformat and filter out private comments
        $all_talk_comments = $this->talks_model->getTalkComments($id, null, true);
        $talk_comments = splitCommentTypes(
            $all_talk_comments, $is_talk_admin, $this->session->userdata('ID')
        );

        // also given only makes sense if there's a speaker set
        if (!empty($talk_detail[0]->speaker)) {
            $also_given = $this->talks_model->talkAlsoGiven(
                $id, $talk_detail[0]->event_id
            );
            $also_given = array(
                'talks' => $also_given,
                'title' => 'Talk Also Given At...'
            );
        }

                $user_id = ($this->user_model->isAuth())
            ? $this->session->userdata('ID') : null;
                $speakers = $this->talkSpeakers->getSpeakerByTalkId($id);
                // check if current user is one of the approved speakers
                $is_claim_approved = false;
                foreach ( $speakers as $speaker ) {
                    if ( $speaker->speaker_id && $speaker->speaker_id == $user_id ) {
                        $is_claim_approved = true;
                    }
                }
                
        $arr = array(
            'detail'         => $talk_detail[0],
            'comments'       => (isset($talk_comments['comment']))
                ? $talk_comments['comment'] : array(),
            'admin'          => ($is_talk_admin) ? true : false,
            'site_admin'     => ($this->user_model->isSiteAdmin()) ? true : false,
            'auth'           => $this->auth,
            'claimed'        => $this->talks_model->talkClaimDetail($id),
            'claim_status'   => $claim_status, 'claim_msg' => $claim_msg,
            'is_claimed'	   => $this->talks_model->hasUserClaimed($id) || $is_claim_approved,
            'speakers'       => $speakers,
            'reqkey'         => $reqkey, 'seckey' => buildSecFile($reqkey),
            'user_attending' => ($this->user_attend_model->chkAttend(
                $currentUserId, $talk_detail[0]->event_id
            )) ? true : false,
            'msg'            => $msg,
            'track_info'     => $this->talkTracks->getSessionTrackInfo($id),
            'user_id'        => ($this->user_model->isAuth())
                ? $this->session->userdata('ID') : null,
            'captcha'        => $captcha
        );

        $this->template->write('feedurl', '/feed/talk/' . $id);
        if (!empty($also_given['talks'])) {
            $this->template->write_view(
                'sidebar2', 'talk/_also_given', $also_given, true
            );
        }
        
        if (!isTalkClaimFull($arr['speakers'])) {
            $this->template->write_view('sidebar3','main/_sidebar-block',
                array(
                    'title'=>'Claiming Talks',
                    'content'=>'<p>Claiming a talk you let us know that you were the speaker 
                    for it. When you claim it (and it\'s approved by the event admins) it will 
                    be linked to your account.</p><p>You\'ll also receive emails when new comments 
                    are posted to 	it.</p>'
                    )
                );
        }
        
        if ($is_talk_admin)
        {
            $this->template->write_view('sidebar3', 'talk/modules/_talk_howto', $arr);
        }
        
        $this->template->write_view('content', 'talk/detail', $arr, true);
        $this->template->render();
    }

    /**
     * Claims a talk with the currently logged in user.
     *
     * @return void
     */
    function claim($talkId, $claimId=null)
    {
        if ($claimId == null) {
            $claimId = $this->input->post('claim_name_select');
        }
    
        $this->load->model('talk_speaker_model','talkSpeaker');
        
        $this->load->model('pending_talk_claims_model','pendingClaims');
        
        $this->pendingClaims->addClaim($talkId, $claimId);
        
        $this->session->set_flashdata('msg', 'Thanks for claiming this talk! You will be emailed when the claim is approved!');
        redirect('talk/view/'.$talkId);
        
        return false;
        
        // OLD CODE IS BELOW......
        
        if (!$this->user_model->isAuth()) {
            redirect('talk/view/'.$talkId);
        }

        $userId 		= $this->session->userdata('ID');
        $speakerName 	= $this->session->userdata('full_name');
        
        // Ie we have no $claimId, look in post for it
        if ($claimId == null) {
            $claimId = $this->input->post('claim_name_select');
        }
        
        if ($this->talkSpeaker->isTalkClaimed($talkId)) {
            $errorData = array(
                'msg' => sprintf('
                    This talk has already been claimed! If you believe
                    this is in error, please contact the please <a style="color:#FFFFFF" href="/event/contact/">contact 
                    this event\'s admins</a>.
                ')
            );
            $this->template->write_view('content', 'msg_error', $errorData);
            $this->template->render();
        }

        // look at the claimId (talk_speaker.id) and talkId for a speaker
        $where = array(
            'ID' 		=> $claimId,
            'talk_id' 	=> $talkId,
            'status'	=> null
        );
        $query = $this->db->get_where('talk_speaker', $where);
        $speakerRecord = $query->result();
        
        // if we found a row, update it with the ID of the currently 
        // logged in user and set it to pending
        if (count($speakerRecord) == 1) {
            
            $updateData = array(
                'status'		=> 'pending',
                'speaker_id'	=> $userId
            );
            $this->db->where('ID', $claimId);
            $this->db->update('talk_speaker', $updateData);
            
            $this->session->set_flashdata('msg', 'Thanks for claiming this talk! You will be emailed when the claim is approved!');
            redirect('talk/view/'.$talkId);
            
        } else {
            $errorData = array(
                'msg'=>sprintf('
                    There was an error in your attempt to claim the talk ID #%s
                    <br/>
                    There might already be a pending claim for this session.
                    <br/><br/>
                    If you would like more information on this error, please <a style="color:#FFFFFF" href="/event/contact/">contact 
                    this event\'s admins</a>.'
                , $talkId)
            );
            $this->template->write_view('content', 'msg_error', $errorData);
            $this->template->render();
        }
    }

    /**
     * Validates whether the given date is within the event's period.
     *
     * @param string $str The string to validate.
     *
     * @return bool
     */
    function given_mo_check($str)
    {
        $t = mktime(
            $this->input->post('given_hour'),
            $this->input->post('given_min'),
            0,
            $this->input->post('given_mo'),
            $this->input->post('given_day'),
            $this->input->post('given_yr')
        );

        //get the duration of the selected event
        $det = $this->event_model->getEventDetail($this->validation->event_id);
        $thisTalk = $det[0];

        $day_start = mktime(
            0, 0, 0,
            date('m', $thisTalk->event_start),
            date('d', $thisTalk->event_start),
            date('Y', $thisTalk->event_start)
        );
        $day_end = mktime(
            23, 59, 59,
            date('m', $thisTalk->event_end),
            date('d', $thisTalk->event_end),
            date('Y', $thisTalk->event_end)
        );

        if (($t >= $day_start) && ($t <= $day_end)) {
            return true;
        } else {
            $this->validation->set_message(
                'given_mo_check', 'Talk date must be during the event!'
            );

            return false;
        }
    }

    /**
     * Validates whether the captcha is correct.
     *
     * @param string $str The captcha input string
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
}

?>
