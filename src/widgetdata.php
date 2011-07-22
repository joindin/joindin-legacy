<?php
	header("Content-type: text/javascript");
	if (isset($_GET['callback'])) {
		//echo htmlentities($_GET['callback']).'({"state":"future","website_uri":"http://www.google.com"})';
		echo htmlentities($_GET['callback']).'({"state":"recent","website_uri":"http://www.google.com","average_rating":4,"comment_count":18})';
	}

