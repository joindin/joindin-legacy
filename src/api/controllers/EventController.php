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

		if(!empty($request->url_elements[2]) && is_numeric($request->url_elements[2])) {
            $event_id = (int)$request->url_elements[2];
		}

        if(isset($request->url_elements[3])) {
            switch($request->url_elements[3]) {
                case 'talks':
                            $list = TalkModel::getTalksByEventId($db, $event_id, $verbose);
                            break;
                case 'comments':
                            $list = CommentModel::getCommentsByEventId($db, $event_id, $verbose);
                            break;
                default:
                            throw new InvalidArgumentException('Unknown Subrequest', 404);
                            break;
            }
        } else {
            if(isset($event_id)) {
                $list = EventModel::getEventById($db, (int)$request->url_elements[2], $verbose);
            } else {
                $list = EventModel::getEventList($db, $verbose);
            }
        }

        // TODO pagination will be required
        return $list;
	}
}
