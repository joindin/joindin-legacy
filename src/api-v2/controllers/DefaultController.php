<?php

class DefaultController {
	public function handle($request, $db) {
        $retval = array();

        // just add the available methods, with links
        $retval['events'] = 'http://' . $request->host . '/' . $request->version . '/events';
        $retval['hot-events'] = 'http://' . $request->host . '/' . $request->version . '/events?filter=hot';
        $retval['upcoming-events'] = 'http://' . $request->host . '/' . $request->version . '/events?filter=upcoming';
        $retval['past-events'] = 'http://' . $request->host . '/' . $request->version . '/events?filter=past';
        $retval['open-cfps'] = 'http://' . $request->host . '/' . $request->version . '/events?filter=cfp';

        return $retval;
	}
}
