<?php

class EventsController extends ApiController {
    public function handle(Request $request, $db) {
        // only GET is implemented so far
        if($request->getVerb() == 'GET') {
            return $this->getAction($request, $db);
        } elseif ($request->getVerb() == 'POST') {
            return $this->postAction($request, $db);
        }
        return false;
    }

	public function getAction($request, $db) {
        $event_id = $this->getItemId($request);

        // verbosity
        $verbose = $this->getVerbosity($request);

        // pagination settings
        $start = $this->getStart($request);
        $resultsperpage = $this->getResultsPerPage($request);

        if(isset($request->url_elements[4])) {
            switch($request->url_elements[4]) {
                case 'talks':
                            $talk_mapper = new TalkMapper($db, $request);
                            $list = $talk_mapper->getTalksByEventId($event_id, $resultsperpage, $start, $request, $verbose);
                            break;
                case 'comments':
                            $event_comment_mapper = new EventCommentMapper($db, $request);
                            $list = $event_comment_mapper->getEventCommentsByEventId($event_id, $resultsperpage, $start, $verbose);
                            break;
                case 'talk_comments':
                            $sort = $this->getSort($request);
                            $talk_comment_mapper = new TalkCommentMapper($db, $request);
                            $list = $talk_comment_mapper->getCommentsByEventId($event_id, $resultsperpage, $start, $verbose, $sort);
                            break;
                default:
                            throw new InvalidArgumentException('Unknown Subrequest', 404);
                            break;
            }
        } else {
            $mapper = new EventMapper($db, $request);
            if($event_id) {
                $list = $mapper->getEventById($event_id, $verbose);
                if(false === $list) {
                    throw new Exception('Event not found', 404);
                }
            } else {
                // check if we're filtering
                if(isset($request->parameters['filter'])) {
                    switch($request->parameters['filter']) {
                        case "hot":
                            $list = $mapper->getHotEventList($resultsperpage, $start, $verbose);
                            break;
                        case "upcoming":
                            $list = $mapper->getUpcomingEventList($resultsperpage, $start, $verbose);
                            break;
                        case "past":
                            $list = $mapper->getPastEventList($resultsperpage, $start, $verbose);
                            break;
                        case "cfp":
                            $list = $mapper->getOpenCfPEventList($resultsperpage, $start, $verbose);
                            break;
                        default:
                            throw new InvalidArgumentException('Unknown event filter', 404);
                            break;
                    }
                } else {
                    $list = $mapper->getEventList($resultsperpage, $start, $verbose);
                }
            }
        }

        return $list;
	}

    public function postAction($request, $db) {
        if(!isset($request->user_id)) {
            throw new Exception("You must be logged in to create data", 400);
        }
        if(isset($request->url_elements[4])) {
            switch($request->url_elements[4]) {
                case 'talks':
                    $talk['event_id'] = $this->getItemId($request);
                    if(empty($talk['event_id'])) {
                        throw new Exception(
                            "POST expects a talk representation sent to a specific event URL", 
                            400
                        );
                    }
                    $event_mapper = new EventMapper($db, $request);
                    $is_admin = $event_mapper->thisUserHasAdminOn($talk['event_id']);
                    if(!$is_admin) {
                        throw new Exception("You do not have permission to add talks to this event", 400);
                    }

                    $talk['title'] = filter_var(
                        $request->getParameter('talk_title'), 
                        FILTER_SANITIZE_STRING
                    );
                    if(empty($talk['title'])) {
                        throw new Exception("The talk title field is required", 400);
                    }
                    $talk['description'] = filter_var(
                        $request->getParameter('talk_description'), 
                        FILTER_SANITIZE_STRING
                    );
                    if(empty($talk['description'])) {
                        throw new Exception("The talk description field is required", 400);
                    }

                    $talk['language'] = filter_var($request->getParameter('language'), FILTER_SANITIZE_STRING);
                    if(empty($talk['language'])) {
                        // default to UK English
                        $talk['language'] = 'English - UK';
                    }

                    $talk['date'] = new DateTime($request->getParameter('start_date'));

                    $speakers = $request->getParameter('speakers');
                    if(is_array($speakers)) {
                        foreach($speakers as $speaker) {
                            $talk['speakers'][] = filter_var($speaker, FILTER_SANITIZE_STRING);
                        }
                    }
                        
                    $talk_mapper = new TalkMapper($db, $request);
                    $new_id = $talk_mapper->save($talk);

                    header("Location: " . $request->base . $request->path_info .'/' . $new_id, NULL, 201);
                    $new_talk = $talk_mapper->getTalkById($new_id);
                    return $new_talk;
                default:
                    throw new Exception("Operation not supported, sorry", 404);
            }
        } else {
            throw new Exception("Operation not supported, sorry", 404);
        }

    }
}
