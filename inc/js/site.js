function setVote(rate){
	$("input[name='rating']").val(rate);
	$.each($("img[id^='rate_']"),function(k,v){
		mk=k+1;
		if(mk==rate){ 
			v.style.border='1px solid #000000';
		}else{ v.style.border='0px'; }
	});
	return false;
}
function toggleAnon(tid){
	lnk=$('#anonLink').html();
	$.each($("tr[id^='com_anon_"+tid+"']"),function(k,v){
		if(v.style.display=='none'){
			v.style.display='block';
			$('#anonLink').html(lnk.replace(/show/,'hide'));
		}else{
			v.style.display='none';			
			$('#anonLink').html(lnk.replace(/hide/,'show'));
		}
	});
}
function getArea(field){
	obj = document.getElementsByName(field);
	cont= obj[0][obj[0].selectedIndex].value; //alert(cont);
	$.getJSON(
		'/api/tz/'+cont,
		function(data){ //alert(data);
			obj = document.getElementsByName('event_tz_area');
			//clear it out first...
			obj[0].options.length=-1;
			$.each(data,function(k,v){
				//alert(k+' : '+v['area']);
				area=v['area'].replace(/_/,' ');
				obj[0].options[k]=new Option(area,area);
			});
		}
	);
}
//-------------------------
function apiRequest(rtype,raction,data,callback){
	var xml_str='';
	$.each(data,function(k,v){
		xml_str+='<'+k+'>'+v+'</'+k+'>';
	});
	xml_str='<request><action type="'+raction+'" output="json">'+xml_str+'</action></request>';
	gt_url="/api/"+rtype+'?reqk='+reqk+'&seck='+seck;
	
	$.ajax({
		type: "POST",
		url	: gt_url,
		data: xml_str,
		contentType: "text/xml",
		processData: false,
		success: function(rdata){
			//alert(rdata);
			obj=eval('('+rdata+')'); //alert(obj.msg);
			
			//check for the redirect
			if(obj.msg && obj.msg.match('redirect:')){
				goto=obj.msg.replace(/redirect:/,'');
				document.location.href=goto;
			}else{
				//maybe add some callback method here 
				//alert('normal'); 
				if ($.isFunction(callback))
					callback(obj);
			}
		}
		
	});
}
//-------------------------
function delBlogComment(cid,bid){
	var obj=new Object();
	obj.cid=cid;
	obj.bid=bid;
	apiRequest('blog','deletecomment',obj, function(obj) {
		alert('Comment removed!'); return false;
	});
	return false;
}
function delEventComment(cid){ deleteComment(cid,'event'); }
function delTalkComment(cid){ deleteComment(cid,'talk'); }
function deleteComment(cid,rtype){
	var obj=new Object();
	obj.cid=cid;
	apiRequest(rtype,'deletecomment',obj, function(obj) {
		alert('Comment removed!'); return false;
	});
	return false;
}
function commentIsSpam(cid,rtype){
	var obj=new Object();
	obj.cid		= cid;
	obj.rtype	= rtype;
	apiRequest('comment','isspam',obj, function(obj) {
		alert('Thanks for letting us know!'); return false;
	});
	return false;
}
function claimTalk(tid){
	var obj=new Object();
	obj.talk_id=tid;

	apiRequest('talk','claim',obj, function(obj) {
		//alert(obj);
		$('#claim_btn').css('display','none');
		if(obj.msg=='Success'){
			alert("Thanks for claiming this talk! You will be emailed when the claim is approved!");
		}else{
			alert(obj.msg);
		}
		return false;
	});
	return false;
}

function markAttending(el,eid,isPast){
	var $loading;
	if (!$(el).next().is('.loading')) {
		$loading = $('<span class="loading">Loading...</span>');
		var pos = $(el).position();
		$loading.css({left: pos.left + 15, top: pos.top - 30}).hide();
		$(el).after($loading);
		$loading.fadeIn('fast');
	}

	var obj=new Object();
	obj.eid=eid;

	apiRequest('event','attend',obj, function(obj) {
		if ($(el).is('.btn-success')) {
			$(el).removeClass('btn-success');
			link_txt=isPast ? 'I attended' : 'I\'m attending';
			adjustAttendCount(eid, -1);
		} else {
			$(el).addClass('btn-success');
			link_txt=isPast ? 'I attended' : 'I\'m attending';
			adjustAttendCount(eid, 1);
		}

		$(el).html(link_txt);
		
		function hideLoading()
		{
			if ($loading)
				$loading.addClass('loading-complete').html('Thanks for letting us know!').pause(1500).fadeOut(function() { $(this).remove() });
		}

		if ($('#attendees').length == 0 || $('#attendees').is(':hidden')) {
			$('#attendees').data('loaded', false);
			hideLoading();
		} else {
			$('#attendees').load('/event/attendees/' + eid, function() {
				hideLoading()
			});
		}
	});

	return false;
}

function adjustAttendCount(eid, num)
{
	$('.event-attend-count-' + eid).each(function() {
		$(this).text(parseInt($(this).text()) + num);
	});
}

function toggleAttendees(el, eid)
{
	if ($('#attendees').length == 0) {
		$('#ctn .main .detail .header .opts').after('<p id="attendees" style="display:none;">qegegqeg</p>');
	}

	if ($('#attendees').is(':hidden')) {
		if ($('#attendees').data('loaded') == true) {
			$('#attendees').slideDown(function() {
				$(el).html('Hide &laquo;');
			});
		} else {
			var $loading;
			if (!$(el).next().is('.loading')) {
				$loading = $('<span class="loading">Loading...</span>');
				var pos = $(el).position();
				$loading.css({left: pos.left + 15, top: pos.top - 30}).hide();
				$(el).after($loading);
				$loading.fadeIn('fast');
			}
			
			$('#attendees').load('/event/attendees/' + eid, function() {
				$('#attendees').slideDown(function() {
					$(el).html('Hide &laquo;');
				});
				if ($loading)
					$loading.fadeOut(function() { $(this).remove() });
			}).data('loaded', true);
		}
	} else {
		$('#attendees').slideUp(function() {
			$(el).html('Show &raquo;');
		});
	}
	return false;
}
function toggleUserStatus(uid){
	var obj=new Object();
	obj.uid=uid;
	apiRequest('user','status',obj, function(obj) {
		v=$('#status_link_'+uid).html();
		(v=='inact') ? nv='act' : nv='inact';
		$('#status_link_'+uid).html(nv);
	});
}
function removeRole(aid){
	var obj=new Object();
	obj.aid=aid;
	obj.type='remove';
	apiRequest('user','role',obj, function(obj) {
		$('#resource_row_'+aid).css('display','none');
	});
}
function populateEvents(fname){
	var obj=new Object();
	apiRequest('event','getlist',obj, function(obj) {
		$.each(obj,function(k,v){
			$('#'+fname).append('<option value="'+v.ID+'">'+v.event_name);
		});
	});
}
function populateTalks(fname){
	var obj=new Object();
	obj.eid=$('#event_names').val(); 
	apiRequest('event','gettalks',obj, function(obj) {
		$.each(obj,function(k,v){
			$('#'+fname).append('<option value="'+v.ID+'">'+v.talk_title+' ('+v.speaker+')');
		});
	});
}
function chkAdminType(fname){
	v=$('#add_type').val();
	if(v=='talk'){
		$('#talks_row').css('display','table-row');
		populateTalks(fname);
	}
}
function addRole(uid){
	//check to see what kind of role we need to add
	//add_type
	//event_names
	var obj=new Object();
	obj.rid=$('#event_names').val();
	obj.uid=uid;
	tp=$('#add_type').val();
	if(tp=='talk'){
		obj.type='addtalk';
		obj.rid=$('#event_talks').val();		
		apiRequest('user','role',obj, function(obj) { });
	}else if(tp=='event'){
		obj.type='addevent';
		//we dont need to worry about the talk, just the event
		apiRequest('user','role',obj, function(obj) { });
	}
	alert('Role added!');
}
function addEventAdmin(eid){
	var uname	= $('#add_admin_user').val();
	var obj		= new Object();
	obj.eid		= eid;
	obj.username=uname;
	apiRequest('event','addadmin',obj, function(obj) {
		if(obj.msg=='Success'){
			$('#evt_admin_list').append('<li><a href="/user/view/'+uname+'">'+uname+'</a>');
		}else{ alert(obj.msg); }
	});
}
function removeEventAdmin(eid,uname,uid){
	var obj		= new Object();
	obj.eid		= eid;
	obj.username=uname;
	apiRequest('event','rmadmin',obj, function(obj) {
		$('#evt_admin_'+uid).remove();
	});	
}
function toggleCfpDates(){
	
	var sel_fields = new Array(
		'cfp_start_mo','cfp_start_day','cfp_start_yr',
		'cfp_end_mo','cfp_end_day','cfp_end_yr'
	);
	
	// Get the current status of the first one...
	stat=$('#cfp_start_mo').attr("disabled");
	if(stat){
		$.each(sel_fields,function(){
			$('#'+this).removeAttr("disabled");
		});
	}else{
		$.each(sel_fields,function(){
			$('#'+this).attr("disabled","disabled");
		});
	}
}

//-------------------------

/*# AVOID COLLISIONS #*/
;if(window.jQuery) (function($){
/*# AVOID COLLISIONS #*/
	
	// IE6 Background Image Fix
	if ($.browser.msie) try { document.execCommand("BackgroundImageCache", false, true)} catch(e) { }
	// Thanks to http://www.visualjquery.com/rating/rating_redux.html
	
	// default settings
	$.rating = {
		cancel: 'Cancel Rating',   // advisory title for the 'cancel' link
		cancelValue: '',           // value to submit when user click the 'cancel' link
		split: 0,                  // split the star into how many parts?
		
		// Width of star image in case the plugin can't work it out. This can happen if
		// the jQuery.dimensions plugin is not available OR the image is hidden at installation
		starWidth: 21,
		
		//NB.: These don't need to be defined (can be undefined/null) so let's save some code!
		//half:     false,         // just a shortcut to settings.split = 2
		//required: false,         // disables the 'cancel' button so user can only select one of the specified values
		//readOnly: false,         // disable rating plugin interaction/ values cannot be changed
		//focus:    function(){},  // executed when stars are focused
		//blur:     function(){},  // executed when stars are focused
		//callback: function(){},  // executed when a star is clicked
		
		// required properties:
		groups: {},// allows multiple star ratings on one page
		event: {// plugin event handlers
			fill: function(n, el, settings, state){ // fill to the current mouse position.
				//if(window.console) console.log(['fill', $(el), $(el).prevAll('.star_group_'+n), arguments]);
				this.drain(n);
				$(el).prevAll('.star_group_'+n).andSelf().addClass('star_'+(state || 'hover'));
				// focus handler, as requested by focusdigital.co.uk
				var lnk = $(el).children('a'); val = lnk.text();
				if(settings.focus) settings.focus.apply($.rating.groups[n].valueElem[0], [val, lnk[0]]);
			},
			drain: function(n, el, settings) { // drain all the stars.
				//if(window.console) console.log(['drain', $(el), $(el).prevAll('.star_group_'+n), arguments]);
				$.rating.groups[n].valueElem.siblings('.star_group_'+n).removeClass('star_on').removeClass('star_hover');
			},
			reset: function(n, el, settings){ // Reset the stars to the default index.
				if(!$($.rating.groups[n].current).is('.cancel'))
					$($.rating.groups[n].current).prevAll('.star_group_'+n).andSelf().addClass('star_on');
				// blur handler, as requested by focusdigital.co.uk
				var lnk = $(el).children('a'); val = lnk.text();
				if(settings.blur) settings.blur.apply($.rating.groups[n].valueElem[0], [val, lnk[0]]);
			},
			click: function(n, el, settings){ // Selected a star or cancelled
				$.rating.groups[n].current = el;
				var lnk = $(el).children('a'); val = lnk.text();
				// Set value
				$.rating.groups[n].valueElem.val(val);
				// Update display
				$.rating.event.drain(n, el, settings);
				$.rating.event.reset(n, el, settings);
				// click callback, as requested here: http://plugins.jquery.com/node/1655
				if(settings.callback) settings.callback.apply($.rating.groups[n].valueElem[0], [val, lnk[0]]);
			}      
		}// plugin events
	};
	
	$.fn.rating = function(instanceSettings){
		if(this.length==0) return this; // quick fail
		
		instanceSettings = $.extend(
			{}/* new object */,
			$.rating/* global settings */,
			instanceSettings || {} /* just-in-time settings */
		);
		
		// loop through each matched element
		this.each(function(i){
			
			var settings = $.extend(
				{}/* new object */,
				instanceSettings || {} /* current call settings */,
				($.metadata? $(this).metadata(): ($.meta?$(this).data():null)) || {} /* metadata settings */
			);
			
			////if(window.console) console.log([this.name, settings.half, settings.split], '#');
			
			// Generate internal control ID
			// - ignore square brackets in element names
			var n = (this.name || 'unnamed-rating').replace(/\[|\]+/g, "_");
  
			// Grouping
			if(!$.rating.groups[n]) $.rating.groups[n] = {count: 0};
			i = $.rating.groups[n].count; $.rating.groups[n].count++;
			
			// Accept readOnly setting from 'disabled' property
			$.rating.groups[n].readOnly = $.rating.groups[n].readOnly || settings.readOnly || $(this).attr('disabled');
			
			// Things to do with the first element...
			if(i == 0){
				// Create value element (disabled if readOnly)
				$.rating.groups[n].valueElem = $('<input type="hidden" name="' + n + '" value=""' + (settings.readOnly ? ' disabled="disabled"' : '') + '/>');
				// Insert value element into form
				$(this).before($.rating.groups[n].valueElem);
				
				if($.rating.groups[n].readOnly || settings.required){
					// DO NOT display 'cancel' button
				}
				else{
					// Display 'cancel' button
					$(this).before(
						$('<div class="cancel"><a title="' + settings.cancel + '">' + settings.cancelValue + '</a></div>')
						.mouseover(function(){ $.rating.event.drain(n, this, settings); $(this).addClass('star_on'); })
						.mouseout(function(){ $.rating.event.reset(n, this, settings); $(this).removeClass('star_on'); })
						.click(function(){ $.rating.event.click(n, this, settings); })
					);
				}
			}; // if (i == 0) (first element)
			
			// insert rating option right after preview element
			eStar = $('<div class="star"><a title="' + (this.title || this.value) + '">' + this.value + '</a></div>');
			$(this).after(eStar);
			
			// Half-stars?
			if(settings.half) settings.split = 2;
			
			// Prepare division settings
			if(typeof settings.split=='number' && settings.split>0){
				var stw = ($.fn.width ? $(eStar).width() : 0) || settings.starWidth;
				var spi = (i % settings.split), spw = Math.floor(stw/settings.split);
				$(eStar)
				// restrict star's width and hide overflow (already in CSS)
				.width(spw)
				// move the star left by using a negative margin
				// this is work-around to IE's stupid box model (position:relative doesn't work)
				.find('a').css({ 'margin-left':'-'+ (spi*spw) +'px' })
			};
			
			// Remember group name so controls within the same container don't get mixed up
			$(eStar).addClass('star_group_'+n);
			
			// readOnly?
			if($.rating.groups[n].readOnly)//{ //save a byte!
				// Mark star as readOnly so user can customize display
				$(eStar).addClass('star_readonly');
			//}  //save a byte!
			else//{ //save a byte!
				$(eStar)
				// Enable hover css effects
				.addClass('star_live')
				// Attach mouse events
				.mouseover(function(){ $.rating.event.drain(n, this, settings); $.rating.event.fill(n, this, settings, 'hover'); })
				.mouseout(function(){ $.rating.event.drain(n, this, settings); $.rating.event.reset(n, this, settings); })
				.click(function(){ $.rating.event.click(n, this, settings); });
			//}; //save a byte!
			
			////if(window.console) console.log(['###', n, this.checked, $.rating.groups[n].initial]);
			if(this.checked) $.rating.groups[n].current = eStar;
			
			//remove this checkbox
			$(this).remove();
			
			// reset display if last element
			if(i + 1 == this.length) $.rating.event.reset(n, this, settings);
		
		}); // each element
			
		// initialize groups...
		for(n in $.rating.groups)//{ not needed, save a byte!
			(function(c, v, n){ if(!c) return;
				$.rating.event.fill(n, c, instanceSettings || {}, 'on');
				$(v).val($(c).children('a').text());
			})
			($.rating.groups[n].current, $.rating.groups[n].valueElem, n);
		//}; not needed, save a byte!
		
		return this; // don't break the chain...
	};
	
	
	
	/*
		### Default implementation ###
		The plugin will attach itself to file inputs
		with the class 'multi' when the page loads
	*/
	//$(function(){ $('input[type=radio].star').rating(); });
	
	
	
/*# AVOID COLLISIONS #*/
})(jQuery);
/*# AVOID COLLISIONS #*/


