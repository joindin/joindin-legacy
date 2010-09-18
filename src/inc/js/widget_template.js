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
		},
		'.username': {
			'font-size'			: '14px',
			'font-weight'		: 'bold'
		},
		'.byline': {
			'font-size'			: '10px',
			'color' 			: '#999999',
		},
		'.byline a': {
			'text-decoration'	: 'underline'
		},
		'.rating_img': {
			'padding'			: '0px',
			'margin'			: '0px',
			'border'			: '0px sold #000000'
		},
		'.widget_iframe': {
			'border'			: '0px solid #000000',
			'height'			: '185px',
			'width'				: '250px',
		},
		'.vote': {
			'border'			: '1px solid #CCCCCC',
			'background-color'	: '#FFFFFF',
			'padding'			: '3px',
		}
	},
	js: ' \
		<script> \
			function goTo(url){ \
				alert(url); \
				window.top.location.href=url; \
			} \
			$(\'#joindin_user_back\').click(function(){ \
				alert(\'here\'); \
				return false; \
			});\
			$(\'#btn_vote\').live(\'click\',function(){ \
				$(\'#vote_container\').css(\'display\',\'block\'); \
				$(\'#btn_vote\').css(\'display\',\'none\'); \
				$(\'#btn_cancel\').css(\'display\',\'block\'); \
				return false; \
			});\
			$(\'#btn_cancel\').live(\'click\',function(){ \
				$(\'#vote_container\').css(\'display\',\'none\'); \
				$(\'#btn_vote\').css(\'display\',\'block\'); \
				$(\'#btn_cancel\').css(\'display\',\'none\'); \
				return false; \
			}); \
			$(\'#btn_vote_submit\').live(\'click\',function(){ \
				var comment = $(\'#vote_comment\').val(); \
				var rating	= $(\'#vote_rank\').val(); \
				alert(comment+\' \'+rating); \
				alert("{{post_to}} "+window.location.hostname); \
				$.ajax({ \
					url: "{{post_to}}", \
					type: "POST", \
					dataType: "json", \
					data: { "test":"1"}, \
					processData: false, \
					success: function(data){ \
						alert(data); \
					} \
				}); \
			}); \
			$(\'.rating_img_link\').live(\'mouseover\',function(){ \
				var curr_id=this.id.replace(\'r\',\'\'); \
				var img_url=$(\'#\'+this.id+\' img\').attr(\'src\').replace(/rating-.+\.jpg/,\'\'); \
				for(i=1;i<=5;i++){ \
					if(i<=curr_id){ \
						$(\'#r\'+i+\' img\').attr(\'src\',img_url+\'/rating-on.jpg\'); \
					}else{ \
						$(\'#r\'+i+\' img\').attr(\'src\',img_url+\'/rating-off.jpg\'); \
					} \
				} \
			}); \
			$(\'.rating_img_link\').live(\'mouseout\',function(){ \
				for(i=1;i<=5;i++){ \
					var img_url=$(\'#\'+this.id+\' img\').attr(\'src\').replace(/rating-.+\.jpg/,\'\'); \
					if(!$(\'#vote_rank\').val()){ \
						$(\'#r\'+i+\' img\').attr(\'src\',img_url+\'/rating-off.jpg\'); \
					}else{ \
						if(i<=$(\'#vote_rank\').val()){ \
							$(\'#r\'+i+\' img\').attr(\'src\',img_url+\'/rating-on.jpg\'); \
						}else{ \
							$(\'#r\'+i+\' img\').attr(\'src\',img_url+\'/rating-off.jpg\'); \
						} \
					} \
				} \
			}); \
			$(\'.rating_img_link\').live(\'click\',function(){ \
				var sel_val = this.id.replace(\'r\',\'\'); \
				var img_url = $(\'#\'+this.id+\' img\').attr(\'src\').replace(/rating-.+\.jpg/,\'\'); \
				$(\'#vote_rank\').val(sel_val); \
				for(i=1;i<=sel_val;i++){ \
					$(\'#r\'+i+\' img\').attr(\'src\',img_url+\'/rating-on.jpg\'); \
				} \
			}); \
			\
			\
			$(\'#btn_show_vote\').live(\'click\',function(){ \
				$(\'#container_show_vote\').css(\'display\',\'block\'); \
				$(\'#container_show_vote\').html(\'\'); \
				\
				$(\'#btn_show_vote\').css(\'display\',\'none\'); \
				$(\'#container_show_vote\').append(\' \
					<table cellpadding="2" cellspacing="0" border="0"> \
					<tr><td colspan="2"><a href="{{base_url}}/talk/view/{{talk_id}}" style="text-decoration:none;font-size:14px;color:#7A7A7A">{{talk_title}}</a><br/> \
					<span style="font-size:11px;color:#999999">{{speaker_name}}</span></td></tr> \
					<tr> \
						<td colspan="2"> \
							<textarea cols="30" rows="3" name="comment" id="comment"></textarea> \
						</td> \
					</tr> \
					<tr> \
						<td id="ratings"></td> \
						<td align="right"> \
							<input type="button" id="btn_submit_comment" value="submit" /> \
							<input type="button" id="btn_cancel_comment" value="x" /> \
							<input type="hidden" name="vote_rank" id="vote_rank" /> \
							<input type="hidden" name="talk_id" id="talk_id" value="{{talk_id}}"/> \
						</td> \
					</tr></table> \
				\'); \
				for(i=1;i<=5;i++){ \
					$(\'#ratings\').append(\'<a href="#" class="rating_img_link" id="r\'+i+\'"><img border="0" src="{{base_url}}/inc/img/rating-off.jpg"/></a>\'); \
				} \
			}); \
			$(\'#btn_submit_comment\').live(\'click\',function(){ \
				var comment = $(\'#comment\').val(); \
				var rating  = $(\'#vote_rank\').val(); \
				var talk_id  = $(\'#talk_id\').val(); \
				if(!comment || !rating){ \
					alert(\'You must enter both a rating and comment!\'); \
					return false; \
				} \
				$(\'<script>\',{ \
					src	: "{{base_url}}/widget/talk?callback=voteCallback&rating="+rating+"&talk_id="+talk_id+"&comment="+comment \
				}).appendTo(\'body\'); \
				alert(\'Thanks for the submission!\'); \
			}); \
			$(\'#btn_cancel_comment\').live(\'click\',function(){ \
				$(\'#container_show_vote\').css(\'display\',\'none\'); \
				$(\'#btn_show_vote\').css(\'display\',\'block\'); \
			}); \
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
		</a><br/> \
		<span class="byline" align="right">by <a href="">joind.in</a></span> \
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
				<span class="byline" align="right">by <a href="">joind.in</a></span> \
			</td> \
		</tr> \
		</table> \
		</div> \
	',
	user_large: ' \
		talks: <br/> \
		{{talks}} \
	',
	user_talks_panel: ' \
		{{#talks}} \
			<div style="padding-bottom:3px"> \
			<a href="{{base_url}}/talk/view/{{ID}}">{{talk_title}}</a><br/> \
			{{#tavg}} \
				<img height="12" src="{{base_url}}/inc/img/rating-{{tavg}}.gif"/><br/> \
			{{/tavg}} \
			{{^tavg}} \
				<img height="12" src="{{base_url}}/inc/img/rating-0.gif"/><br/> \
			{{/tavg}} \
			</div> \
		{{/talks}} end\
	',
	vote_small: ' \
		<input type="button" name="btn_show_vote" id="btn_show_vote" value="vote"/> \
		<div class="vote" id="container_show_vote" style="display:none">{{talk_title}}</div> \
	',
	vote_container: ' \
		\
	',
	//user_large: ' \
	//',
}
