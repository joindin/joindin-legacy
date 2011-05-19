<?php

class DefaultController {
	public function handle($request, $db) {
        $retval = array();

        // just add the available methods, with links
        $retval['events'] = 'http://' . $request->host . '/v2/events';

        return $retval;
	}
}
