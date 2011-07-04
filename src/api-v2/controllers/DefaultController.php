<?php

class DefaultController {
	public function handle($request, $db) {
        $retval = array();

        // just add the available methods, with links
        $retval['events'] = 'http://' . $request->host . '/v2/events';
        $retval['hot-events'] = 'http://' . $request->host . '/v2/events?filter=hot';
        $retval['upcoming-events'] = 'http://' . $request->host . '/v2/events?filter=upcoming';
        $retval['past-events'] = 'http://' . $request->host . '/v2/events?filter=past';

        return $retval;
	}
}
