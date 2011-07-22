<?php

class TalksController extends ApiController {
    public function handle($request, $db) {
        // only GET is implemented
        if($request->verb == 'GET') {
                return $this->getAction($request, $db);
        }
        // should not end up here
        return false;
    }

	public function getAction($request, $db) {
        $talk_id = $this->getItemId($request);

        // verbosity
        $verbose = $this->getVerbosity($request);

        // pagination settings
        $start = $this->getStart($request);
        $resultsperpage = $this->getResultsPerPage($request);

        if(isset($request->url_elements[4])) {
            // sub elements
            if($request->url_elements[4] == "comments") {
                $list = TalkCommentModel::getCommentsByTalkId($db, $talk_id, $resultsperpage, $start, $request, $verbose);
            }
        } else {
            if($talk_id) {
                $list = TalkModel::getTalkById($db, $talk_id, $verbose);
            } else {
                // listing makes no sense
                return false;
            }
            // add links
            $list = TalkModel::addHypermedia($list, $request);
        }

        return $list;
	}
}
