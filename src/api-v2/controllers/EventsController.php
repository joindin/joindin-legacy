<?php

class EventsController extends ApiController {
    public function handle($request, $db) {
        // only GET is implemented so far
        if($request->verb == 'GET') {
            return $this->getAction($request, $db);
        } elseif ($request->verb == 'POST') {
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
        $talk['event_id'] = $this->getItemId($request);
        if(empty($talk['event_id'])) {
            throw new BadRequestException("POST expects a talk representation sent to a specific event URL", 400);
        }
        $talk['title'] = filter_var($request->getParameter('talk_title'), FILTER_SANITIZE_STRING);
        if(empty($talk['title'])) {
            throw new BadRequestException("The talk title field is required", 400);
        }
        $talk['description'] = filter_var($request->getParameter('talk_description'), FILTER_SANITIZE_STRING);
        if(empty($talk['description'])) {
            throw new BadRequestException("The talk description field is required", 400);
        }

        $talk_mapper = new TalkMapper($db, $request);
        $new_id = $talk_mapper->save($talk);

        header("Location: " . $request->base . $request->path_info .'/' . $new_id);
        return $talk;
        exit;
    }
}
