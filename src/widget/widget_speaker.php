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
var joindin_speaker = function(){};

joindin_speaker.urlBase_website = "<?php echo $siteBase; ?>";
joindin_speaker.urlBase_api     = "<?php echo $apiBase; ?>";

joindin_speaker.embedStyle      = true;

// data collectors
joindin_speaker.talkcounter     = 0;
joindin_speaker.ratingcounter   = 0;
joindin_speaker.userId          = null;
joindin_speaker.userFullName    = '';

joindin_speaker.draw = function(userId, node) {
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
	joindin_speaker.userId = userId;
	jQuery.ajax({
	        url: joindin_speaker.urlBase_api + 'v2.1/users/' + userId + '/talks?format=json',
	        dataType: 'jsonp',
	        success: function(json){
	            joindin_speaker.gotData(json, node, userId);
	        }
	});
}

joindin_speaker.gotData = function(json, node, userId) {
	if ((json.length < 1) || (typeof json.meta.next_page == 'undefined')) {
		joindin_speaker.renderWidget(node);
	}
	
	var timeNow  = Date.UTC();
	
	for ( talk in json.talks ) {
	    talkObj = json.talks[talk];
	    var timeTalk = Date.parse(talkObj.start_date);
	    if (timeNow > timeTalk) {
	        continue;
	    }
	    
	    joindin_speaker.talkcounter++;
	    joindin_speaker.ratingcounter += talkObj.average_rating;
	    joindin_speaker.userFullName 
	}
	
	jQuery.ajax({
	        url: json.meta.next_page, 
	        dataType: 'jsonp',
	        success: function(json){
	            joindin_speaker.gotData(json, node, userId);
	        }
	});
}

joindin_speaker.renderWidget = function(node) {
	
    jQuery.ajax({
	        url: joindin_speaker.urlBase_api + 'v2.1/users/' + joindin_speaker.userId + '?format=json', 
	        dataType: 'jsonp',
	        success: function(json){
                var content = "";
                if (!joindin_speaker.gotData.embeddedStyles && joindin_speaker.embedStyle) {
                    joindin_speaker.gotData.embeddedStyles = true;
                    var headTag = document.getElementsByTagName('head')[0];
                    var styleTag = document.createElement("link");
                    styleTag.setAttribute("rel", "stylesheet");
                    styleTag.setAttribute("href", joindin_speaker.urlBase_website + "/widget/widget.css");
                    headTag.appendChild(styleTag);
                }
            
                content += "<div class='joindin-content-insert'>";
            
                var average_rating = Math.round(joindin_speaker.ratingcounter / joindin_speaker.talkcounter);
            
                content += '<div class="joindin-content-insert-past">';
                content += '<p><strong>' + json.users[0].full_name + '</strong></p>';
                content += '<p><img src="//joind.in/inc/img/rating-' + average_rating + '.gif" width="75" /> (' + joindin_speaker.talkcounter + ' talks)</p>';
                content += '<a href="' + joindin_speaker.urlBase_website + 'user/view/' + joindin_speaker.userId + '">View speaker info on joind.in</a>';
                content += '</div>';
                
                jQuery(node).append(content);
            }
    });
}

