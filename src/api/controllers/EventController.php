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

		// /event/<id>/<nested_action> is the possible format, with query params
		if(!empty($request->url_elements[2]) && is_numeric($request->url_elements[2])) {
			$event_list = EventModel::getEventById($db, (int)$request->url_elements[2], $verbose);
		} else {
            $event_list = EventModel::getEventList($db, $verbose);
		}
        return $event_list;
	}
}
