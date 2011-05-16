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
        if(isset($request->parameters['verbose'])
                && $request->parameters['verbose'] == 'yes') {
            $verbose = true;
        } else {
            $verbose = false;
        }

        // pagination settings
        $page = $request->parameters['page'];
        $resultsperpage = $request->parameters['resultsperpage'];

        if(isset($request->url_elements[4])) {
            switch($request->url_elements[4]) {
                case 'talks':
                            $list = TalkModel::getTalksByEventId($db, $event_id, $resultsperpage, $page, $verbose);
                            $list = TalkModel::addHypermedia($list, $request->host);
                            break;
                case 'comments':
                            $list = EventCommentModel::getEventCommentsByEventId($db, $event_id, $resultsperpage, $page, $verbose);
                            break;
                default:
                            throw new InvalidArgumentException('Unknown Subrequest', 404);
                            break;
            }
        } else {
            if($event_id) {
                $list = EventModel::getEventById($db, $event_id, $verbose);
            } else {
                $list = EventModel::getEventList($db, $resultsperpage, $page, $verbose);
            }
            // add links
            $list = EventModel::addHypermedia($list, $request->host);
        }

        // TODO pagination will be required
        return $list;
	}
}
