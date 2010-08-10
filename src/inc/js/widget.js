// See if they have a joind.in cookie!

var joindin = {
	
	host: 			"<?php echo $_SERVER['SERVER_NAME']; ?>",
	widget_type: 	'large',
	widget_height: 	0,
	widget_width: 	0,
	render_div: 	null,
	
	setRenderDiv : function(div_id){
		this.render_div=div_id;
	},
	display_talk_large: function(talk_id,render_to_div){
		if(render_to_div){ this.setRenderDiv(render_to_div); }
		
		url	='http://'+this.host+'/widget/talk/'+talk_id+'/type/'+this.widget_type;
		this.widget_height	= 120;
		this.widget_width	= 150; 
		this.render(url);
	},
	display_talk_small: function(talk_id,render_to_div){
		if(render_to_div){ this.setRenderDiv(render_to_div); }
		
		url	='http://'+this.host+'/widget/talk/'+talk_id+'/type/small';
		this.widget_height	= 50;
		this.widget_width	= 270;
		this.render(url);
	},
	render: function(url){
		var content = '<iframe id="ji-widget-frame" src="'+url+'" height="'+this.widget_height+'" \
			width="'+this.widget_width+'" style="border:1px solid #999999;" scrolling="no"></iframe>';
		
		if(this.render_div!=null){
			document.getElementById(this.render_div).innerHTML+=content;
		}else{
			document.write(content);
		}
	}
}