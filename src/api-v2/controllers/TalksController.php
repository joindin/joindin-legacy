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
                $comment_mapper = new TalkCommentMapper($db, $request);
                $list = $comment_mapper->getCommentsByTalkId($talk_id, $resultsperpage, $start, $verbose);
            }
        } else {
            if($talk_id) {
                $mapper = new TalkMapper($db, $request);
                $list = $mapper->getTalkById($talk_id, $verbose);
            } else {
                // listing makes no sense
                return false;
            }
        }

        return $list;
	}
}
