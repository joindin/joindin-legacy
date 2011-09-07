<?php

class Talk_commentsController extends ApiController {
    public function handle($request, $db) {
        // only GET is implemented so far
        if($request->verb == 'GET') {
            return $this->getAction($request, $db);
        }
        return false;
    }

	public function getAction($request, $db) {
        $comment_id = $this->getItemId($request);

        // verbosity
        $verbose = $this->getVerbosity($request);

        // pagination settings
        $start = $this->getStart($request);
        $resultsperpage = $this->getResultsPerPage($request);

        $mapper = new TalkCommentMapper($db, $request);
        if($comment_id) {
            $list = $mapper->getCommentById($comment_id, $verbose);
            return $list;
        } 

        return false;
	}
}
