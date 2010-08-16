var widget_template = {
	css: {
		'a' : {
			'color' 			: '#999999',
			'text-decoration' 	: 'none',
			'font-size'			: '11px'
		},
		'.title_link' : {
			'font-size'			: '13px',
			'font-weight'		: 'bold'
		},
		'.event_title_large': {
			'font-size'			: '14px',
			'font-weight'		: 'bold'
		},
		'.content': {
			'color' 			: '#999999',
			'font-size'			: '11px'
		}
	},
	js: ' \
		<script> \
			function goTo(url){ \
				alert(url); \
				window.top.location.href=url; \
			} \
		</script> \
	',
	talk_small: ' \
		<table cellpadding="3" cellspacing="0" border="0"> \
		<tr> \
			<td valign="top"> \
				<a class="title_link" href="#" onClick="goTo(\'{{talk_url}}\')">{{talk_title}}</a> \
				<br/> \
				<a class="event_name_link" href="#" onClick="goTo(\'{{event_url}}\')">@ {{event_name}}</a> \
			</td> \
			<td valign="top"> \
				<a class="talk_title" href="#" onClick="goTo(\'{{talk_url}}\')"> \
					<img src="{{rating_url}}" border="0"/> \
				</a> \
			</td> \
		</tr> \
		</table> \
	',
	talk_large: ' \
		<div style="width:200px;margin:5px"> \
		<a class="title_link" href="#" onClick="goTo(\'{{talk_url}}\')">{{talk_title}}</a><br/> \
		{{speaker_name}} @ \
		<a href="#" class="event_name_link" onClick="goTo(\{{event_url}}\')">{{event_name}}</a> \
		<br/> \
		<a class="talk_title_link" href="#" onClick="goTo(\'{{talk_url}}\')"> \
			<img src="{{rating_url}}" border="0"/> \
		</a> \
		</div> \
	',
	event_small: ' \
		<div style="width:200px;margin:1px;vertical-align:top"> \
		<table cellpadding="3" cellspacing="0" border="0"> \
		<tr> \
			<td><a href="{{event_link}}"<img src="{{event_icon}}" border="0"/></a></td> \
			<td valign="top"> \
				<a class="event_title_large" href="{{event_link}}">{{event_name}}</a><br/> \
				<br/> \
				<span class="content"> \
				{{event_loc}} <br/> \
				{{event_dates}} <br/> \
				</span> \
			</td> \
		</tr> \
		</table> \
		</div> \
	'
}
