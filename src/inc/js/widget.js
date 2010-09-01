// Load javascript
document.write("<script src=\"http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/js/jquery.js\"></script>");
document.write("<script src=\"http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/js/mustache/mustache.js\"></script>");
document.write("<script src=\"http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/js/widget_template.js\"></script>");

var joindin = {
	
	host: 			"<?php echo $_SERVER['SERVER_NAME']; ?>",
	widget_type: 	'large',
	render_div: 	null,
	talk_data: 		new Array(), 
	
	setRenderDiv : function(div_id){
		this.render_div=div_id;
	},
	timestampToDate: function(ts){
		var date = new Date(ts*1000);
		return date.getMonth()+'/'+date.getDate()+'/'+date.getFullYear();
	},
	request_data: function(rid,request_type,display_type){
		$.getJSON(
			'http://'+this.host+'/widget/fetchdata/'+request_type+'/'+rid+'?&jsoncallback=?',
			{
				"display_type" 	: display_type,
				"render_to"		: this.render_div
			}
		);
		this.render_div=null;
	},
	jsonpCallback: function(id,type,disp_type,render_to,data){
		if(render_to.length>0){ this.setRenderDiv(render_to); }
		
		this.talk_data[id]=data;
		
		var func = '_render_'+type;
		this[func](data,disp_type);
	},	
	// render functions....
	display_talk_large: function(talk_id,render_to_div){
		if(render_to_div){ this.setRenderDiv(render_to_div); }
		this.request_data(talk_id,'talk','large');
	},
	display_talk_small: function(talk_id,render_to_div){
		if(render_to_div){ this.setRenderDiv(render_to_div); }
		this.request_data(talk_id,'talk','small');
	},
	display_event_small: function(event_id,render_to_div){
		//stubbed
	},
	display_event_large: function(event_id, render_to_div){
		if(render_to_div){ this.setRenderDiv(render_to_div); }
		this.request_data(event_id,'event','small');
	},
	display_user_large: function(user_id,render_to_div){
		if(render_to_div){ this.setRenderDiv(render_to_div); }
		this.request_data(user_id,'user','large');
	},
	display_vote_small: function(talk_id,render_to_div){
		this.request_data(talk_id,'vote','small');
	},
	_render_event: function(data,size){
		// render event....
		
		var content = {
			event_name	: data[0].event_name,
			event_url	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/event/view/'+data[0].ID,
			event_loc	: data[0].event_loc,
			event_icon	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/img/event_icons/',
			event_dates	: this.timestampToDate(data[0].event_start)+' - '+this.timestampToDate(data[0].event_end)
		}
		if(data[0].event_icon!='null'){
			content.event_icon+=data[0].event_icon;
		}else{  content.event_icon+='none.gif'; }
		
		this._apply_template(content,eval('widget_template.event_'+size));
	},
	_render_talk: function(data,size){
		
		//get the speaker names
		var speaker_data = '';
		$.each(data[0].speaker,function(k,v){
			speaker_data+=v.speaker_name+', ';
		});
		speaker_data=speaker_data.substring(0,speaker_data.length-2);
		
		var content = { 
			talk_url	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/talk/view/'+data[0].ID,
			event_url	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/event/view/'+data[0].event_id,
			talk_title	: data[0].talk_title,
			event_name	: data[0].event_name,
			rating_url	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/img/rating-'+data[0].tavg+'.gif',
			speaker_name: speaker_data
		}
		this._apply_template(content,eval('widget_template.talk_'+size));
		
	},
	_render_vote: function(data,size){

		var img_str	= '';
		var img_path= 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/inc/img';
		var post_to = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/api/talk/addcomment';
		var base_url= 'http://<?php echo $_SERVER['SERVER_NAME']; ?>';
		
		for(i=1;i<=5;i++){
			img_str+='<a href="#" class="rating_img_link" id="r'+i+'"><img class="rating_img" src="'+img_path+'/rating-off.jpg" style="border:0px;margin:0px;padding:0px"></a>';
		}
		
		var vc_data = {
			talk_title		: data[0].talk_title,
			img_path		: img_path,
			rating_images	: img_str,
			base_url		: base_url,
			frame_url		: base_url+'/widget/talk/'+data[0].ID+'/type/'+size
		}
		var content = {
			talk_id			: data[0].ID,
			post_to			: post_to,
			vote_container 	: Mustache.to_html(widget_template.vote_container,vc_data)
		}
		this._apply_template(content,eval('widget_template.vote_'+size));
	},
	_render_user: function(data,size){
		//var content = {
		//	username	: data.username,
		//	full_name	: data.full_name,
		//	talks		: data.talks,
		//	base_url	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>'
		//}
		
		var talk_content = '';
		$.each(data.talks,function(k,v){
			talk_content+=v.talk_title+'|';
		});
		
		var content = {
			talks		: talk_content
		}
		
		this._apply_template(content,eval('widget_template.user_'+size));
	},
	// Apply our data to the Mustache template and CSS
	_apply_template: function(content,template){
		var talk_cont	= $('<div>');
		var talk_obj	= $('<div>');
		talk_obj.css({
			'height'	: 'auto',
			'width'		: 'auto',
			'border'	: '0px solid #999999',
			'overflow'	: 'hidden',
			'display'	: 'inline-block',
			'background-color':'#FFFFFF',
			'color'		: '#999999',
			'font-size'	: '11px',
			'margin'	: '2 2 2 2',
			'id'		: 'joindin_widget'
		});
		talk_obj.append(Mustache.to_html(
			widget_template.js+template,
		content));

		// Apply the CSS from our template file
		$.each(widget_template.css,function(k,v){
			talk_obj.find(k).css(v);
		});
		
		// Append to container and output...
		talk_cont.append(talk_obj);
		if(this.render_div!='null'){
			$('#'+this.render_div).after(talk_cont.html());
		}else{ 
			$('#joindin_widget').after(talk_cont.html()); 
		}
		this.render_div=null;
	}
}