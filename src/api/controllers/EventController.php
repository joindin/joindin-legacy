<?php

class EventController extends ApiController {
	public function handle($request, $db) {
        // verbosity
        if(isset($request->parameters['verbose'])
                && $request->parameters['verbose'] == 'yes') {
            $verbose = true;
        } else {
            $verbose = false;
        }

		if(!empty($request->url_elements[3]) && is_numeric($request->url_elements[3])) {
            $event_id = (int)$request->url_elements[3];
		}

        if(isset($request->url_elements[4])) {
            switch($request->url_elements[4]) {
                case 'talk':
                            $list = TalkModel::getTalksByEventId($db, $event_id, $verbose);
                            break;
                case 'comment':
                            $list = EventCommentModel::getEventCommentsByEventId($db, $event_id, $verbose);
                            break;
                default:
                            throw new InvalidArgumentException('Unknown Subrequest', 404);
                            break;
            }
        } else {
            if(isset($event_id)) {
                $list = EventModel::getEventById($db, $event_id, $verbose);
            } else {
                $list = EventModel::getEventList($db, $verbose);
            }
        }

        // TODO pagination will be required
        return $list;
	}
}
