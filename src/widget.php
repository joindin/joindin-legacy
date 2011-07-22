<?php
	header("Content-type: text/javascript");
?>


var joindin = function(){};
joindin.draw = function(talkId, node) {
	if (!jQuery) {
		joindin.writeContent('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>', node);
	}
	if (!node) {
		document.write('<div id="joindin-content-placeholder"></div>');
		node = document.getElementById("joindin-content-placeholder");
	}
	jQuery.getJSON('//kevin.valinor.local/widgetdata.php?callback=?', {talk:talkId}, function(data){joindin.gotData(data, node);});
}

joindin.gotData = function(data, node) {
	switch (data.state) {
		case 'future':
			joindin.writeContent('<div style="font-size:75%" class="joindin-content-insert joindin-content-insert-future"><a>View this talk on joind.in</a></div>', node);
			break;
		case 'recent':
			joindin.writeContent('<div style="font-size:75%" class="joindin-content-insert joindin-content-insert-recent"><img src="//joind.in/inc/img/rating-' + data.average_rating + '.gif" width="75" /> (' + data.comment_count + ')<br /><a>View this talk on joind.in</a></div>', node);
			break;
		case 'past':
			joindin.writeContent('<div style="font-size:75%" class="joindin-content-insert joindin-content-insert-past"><a>View this talk on joind.in</a></div>', node);
			break;
		default:
			// Unknown talk
			alert("Unknown talk");
			break;
	}
}

joindin.writeContent = function(content, node) {
console.log(node);
	jQuery(node).append(content);
}

