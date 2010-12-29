<?php

class EventController extends ApiController {
	public function handle($request) {
		// /event/<id>/<nested_action> is the possible format, with query params
		if(!empty($request->url_elements[2]) && is_numeric($request->url_elements[2])) {
			echo "A specific event";
			// $event = EventModel::getEventById((int)$request->url_elements[2]));

		} else {
			echo "A list of events";
		}
	}
}
