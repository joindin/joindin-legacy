<?php

class EventsController extends ApiController {
    public function handle($request, $db) {
        // only GET is implemented so far
        if($request->verb == 'GET') {
            return $this->getAction($request, $db);
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
}
