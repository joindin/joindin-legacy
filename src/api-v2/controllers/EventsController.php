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
                            $list = TalkModel::getTalksByEventId($db, $event_id, $resultsperpage, $start, $request, $verbose);
                            break;
                case 'comments':
                            $list = EventCommentModel::getEventCommentsByEventId($db, $event_id, $resultsperpage, $start, $verbose);
                            break;
                default:
                            throw new InvalidArgumentException('Unknown Subrequest', 404);
                            break;
            }
        } else {
            if($event_id) {
                $list = EventModel::getEventById($db, $event_id, $verbose);
            } else {
                // check if we're filtering
                if(isset($request->parameters['filter'])) {
                    switch($request->parameters['filter']) {
                        case "hot":
                            $list = EventModel::getHotEventList($db, $resultsperpage, $start, $verbose);
                            break;
                        case "upcoming":
                            $list = EventModel::getUpcomingEventList($db, $resultsperpage, $start, $verbose);
                            break;
                        case "past":
                            $list = EventModel::getPastEventList($db, $resultsperpage, $start, $verbose);
                            break;
                        case "cfp":
                            $list = EventModel::getOpenCfPEventList($db, $resultsperpage, $start, $verbose);
                            break;
                        default:
                            throw new InvalidArgumentException('Unknown event filter', 404);
                            break;
                    }
                } else {
                    $list = EventModel::getEventList($db, $resultsperpage, $start, $verbose);
                }
            }
            // add links
            $list = EventModel::addHypermedia($list, $request);
        }

        return $list;
	}
}
