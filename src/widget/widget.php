<?php
	header("Content-type: text/javascript; charset=utf8");
	header("Cache-control: public, max-age=10000");
	header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));
	define('BASEPATH', 'something');
	$config_filename = dirname(__FILE__).'/../system/application/config/config.php';
	if (is_readable($config_filename)) {
		require($config_filename);
		$siteBase = $config['base_url'];
		$apiBase  = $config['api_base_url'];
	} else {
		$siteBase = '//joind.in';
		$apiBase  = '//api.joind.in';
	}
?>
var joindin = function(){};

joindin.urlBase_website = "<?php echo $siteBase; ?>";
joindin.urlBase_api     = "<?php echo $apiBase; ?>";

joindin.embedStyle      = true;
joindin.draw = function(talkId, node) {
	if (!node) {
		var rndm = parseInt(Math.random() * 9999999);
		document.write('<div id="joindin-content-placeholder-' + rndm + '"></div>');
		node = document.getElementById("joindin-content-placeholder-" + rndm);
	} else if (typeof node == "string") {
		node = document.getElementById(node);
    }
	if (typeof jQuery == 'undefined') {
		// TODO: Attempt to auto-load jQuery, then relaunch the widget when jQuery is available
		if (typeof console.log != "undefined") {
			console.log("No jQuery available - not proceeding with joind.in widget");
			return;
		}
	}
	jQuery.getJSON(joindin.urlBase_api + '/v2.1/talks/' + talkId + '?format=json&callback=?', {talk:talkId}, function(data){joindin.gotData(data, node);});
}

joindin.gotData = function(data, node) {
	if (data.length < 1) {
		// No content returned, do nothing
		return;
	}
	data = data.talks[0];
	var content = "";
	if (!joindin.gotData.embeddedStyles && joindin.embedStyle) {
		joindin.gotData.embeddedStyles = true;
		var headTag = document.getElementsByTagName('head')[0];
		var styleTag = document.createElement("link");
		styleTag.setAttribute("rel", "stylesheet");
		styleTag.setAttribute("href", joindin.urlBase_website + "/widget/widget.css");
		headTag.appendChild(styleTag);
	}

	content += "<div class='joindin-content-insert'>";

	var timeNow  = Date.UTC();
	var timeTalk = Date.parse(data.start_date);

	if (timeNow > timeTalk) {
		data.state = "future";
	} else if (data.comments_enabled) {
		data.state = "recent";
	} else {
		data.state = "past";
	}

	switch (data.state) {
		case 'future':
			content += '<div class="joindin-content-insert-future">';
			content += '<a href="' + data.website_uri + '">View on joind.in</a>';
			content += '</div>';
			break;
		case 'recent':
			content += '<div class="joindin-content-insert-recent">';
			if (data.average_rating != "") {
				content += '<p><img src="//joind.in/inc/img/rating-' + data.average_rating + '.gif" width="75" /> (' + data.comment_count + ')</p>';
			}
			content += '<a href="' + data.website_uri + '">Comment on joind.in</a>';
			content += '</div>';
			break;
		case 'past':
			content += '<div class="joindin-content-insert-past">';
			if (data.average_rating != "") {
				content += '<p><img src="//joind.in/inc/img/rating-' + data.average_rating + '.gif" width="75" /> (' + data.comment_count + ')</p>';
			}
			content += '<a href="' + data.website_uri + '">View on joind.in</a>';
			content += '</div>';
			break;
		default:
			// Unknown talk, do nothing
			break;
	}
	joindin.writeContent(content, node);
}

joindin.writeContent = function(content, node) {
	jQuery(node).append(content);
}

