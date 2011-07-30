<?php
	header("Content-type: text/javascript");
?>
var joindin = function(){};
joindin.draw = function(talkId, node, postJquery/*=false*/) {
	if (!node) {
		document.write('<div id="joindin-content-placeholder"></div>');
		node = document.getElementById("joindin-content-placeholder");
	}
	if (typeof jQuery == 'undefined') {
		// TODO: Attempt to auto-load jQuery, then relaunch the widget when jQuery is available
		if (typeof console.log != "undefined") {
			console.log("No jQuery available - not proceeding with joind.in widget");
			return;
		}
	}
	// TODO: lookup this URL from the config
	jQuery.getJSON('//kevin.valinor.local/v2/talks/3214?format=json&callback=?', {talk:talkId}, function(data){joindin.gotData(data, node);});
}

joindin.gotData = function(data, node) {
data = data[0];
data.state = "recent";
	switch (data.state) {
		case 'future':
			joindin.writeContent('<div style="font-size:75%" class="joindin-content-insert joindin-content-insert-future"><a href="' + data.uri + '">View this talk on joind.in</a></div>', node);
			break;
		case 'recent':
			joindin.writeContent('<div style="font-size:75%" class="joindin-content-insert joindin-content-insert-recent"><img src="//joind.in/inc/img/rating-' + data.average_rating + '.gif" width="75" /> (' + data.comment_count + ')<br /><a href="' + data.uri + '">Comment on joind.in</a></div>', node);
			break;
		case 'past':
			joindin.writeContent('<div style="font-size:75%" class="joindin-content-insert joindin-content-insert-past"><img src="//joind.in/inc/img/rating-' + data.average_rating + '.gif" width="75" /> (' + data.comment_count + ')<br /><a href="' + data.uri + '">View on joind.in</a></div>', node);
			break;
		default:
			// Unknown talk
			alert("Unknown talk");
			break;
	}
}

joindin.writeContent = function(content, node) {
	jQuery(node).append(content);
}

