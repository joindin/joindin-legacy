// Load the needed javascript files
var source_host = document.getElementById('joindin_widget').getAttribute('src').replace(/\inc\/js\/widget.js/,'');
document.write("<script src=\""+source_host+"/inc/js/jquery.js\"></script>");
document.write("<script src=\""+source_host+"/inc/js/mustache/mustache.js\"></script>");
document.write("<script src=\""+source_host+"/inc/js/widget_template.js\"></script>");

var joindin = {
	
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
	getSourceHost: function(){
		return document.getElementById('joindin_widget').getAttribute('src').replace(/\inc\/js\/widget.js/,'');
	},
	request_data: function(rid,request_type,display_type){
		var host = this.getSourceHost().replace(/http:\/\//,'');
		$.getJSON(
			'http://'+host+'/widget/fetchdata/'+request_type+'/'+rid+'?&jsoncallback=?',
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
	voteCallback: function(vote_container){
		$('#container_show_vote').css('display','none');
		$('#btn_show_vote').css('display','block');
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
			event_url	: this.getSourceHost()+'/event/view/'+data[0].ID,
			event_loc	: data[0].event_loc,
			event_icon	: this.getSourceHost()+'/inc/img/event_icons/',
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
			talk_url	: this.getSourceHost()+'/talk/view/'+data[0].ID,
			event_url	: this.getSourceHost()+'/event/view/'+data[0].event_id,
			talk_title	: data[0].talk_title,
			event_name	: data[0].event_name,
			rating_url	: this.getSourceHost()+'/inc/img/rating-'+data[0].tavg+'.gif',
			speaker_name: speaker_data
		}
		this._apply_template(content,eval('widget_template.talk_'+size));
		
	},
	_render_vote: function(data,size){

		var speaker_data = '';
		$.each(data[0].speaker,function(k,v){
			speaker_data+=v.speaker_name.replace(/\'/,"&rsquo;")+', ';
		});
		speaker_data=speaker_data.substring(0,speaker_data.length-2);

		var content = {
			talk_id			: data[0].ID,
			talk_title		: data[0].talk_title,
			speaker_name	: speaker_data
		}
		this._apply_template(content,eval('widget_template.vote_'+size));
	},
	_render_user: function(data,size){
		var content = {
			username	: data.username,
			full_name	: data.full_name,
			talks		: data.talks,
			base_url	: 'http://<?php echo $_SERVER['SERVER_NAME']; ?>'
		}
		this._apply_template(content,eval('widget_template.user_'+size));
	},
	// Apply our data to the Mustache template and CSS
	_apply_template: function(content,template){
		//content.base_url='http://<?php echo $_SERVER['SERVER_NAME']; ?>';
		content.base_url=this.getSourceHost();
		
		var talk_cont	= $('<div>');
		var talk_obj	= $('<div>');
		talk_obj.css({
			'height'	: 'auto',
			'width'		: 'auto',
			'border'	: '1px solid #999999',
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
