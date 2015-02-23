function apiRequest(rtype,raction,data,callback){
	var xml_str='';
	$.each(data,function(k,v){
		xml_str+='<'+k+'>'+v+'</'+k+'>';
	});
	xml_str='<request><action type="'+raction+'" output="json">'+xml_str+'</action></request>';
	var gt_url="/api/"+rtype+'?reqk='+reqk+'&seck='+seck;

	$.ajax({
		type: "POST",
		url	: gt_url,
		data: xml_str,
		contentType: "text/xml",
		processData: false,
		dataType: 'json',
		success: function(rdata){
			//notifications.alert(rdata);
			//obj=eval('('+rdata+')'); //notifications.alert(obj.msg);
			/* rdata should be json now ... parsed properly by the browser */
			var obj = rdata;

			//check for the redirect
			if(obj.msg && obj.msg.match('redirect:')){
				var targetLocation=obj.msg.replace(/redirect:/,'');
				document.location.href=targetLocation;
			}else{
				//maybe add some callback method here
				//notifications.alert('normal');
				if ($.isFunction(callback))
					callback(obj);
			}
		}

	});
}
//-------------------------
function delEventComment(cid){ deleteComment(cid,'event'); }
function delTalkComment(cid, eid){
	deleteComment(cid,'talk',eid);
	$('#comment-'+cid).remove();
}
function deleteComment(cid,rtype,eid){
	var obj=new Object();
	obj.cid=cid;
	if (eid) {
		obj.eid = eid;
	}
	apiRequest(rtype,'deletecomment',obj, function(obj) {
		notifications.alert('Comment removed!'); return false;
	});
	return false;
}
function commentIsSpam(cid,tid,rtype){
	var obj=new Object();
	obj.cid		= cid;
	obj.rtype	= rtype;
    obj.tid     = tid;
	apiRequest('comment','isspam',obj, function(obj) {
		notifications.alert('Thanks for letting us know!'); return false;
	});
	return false;
}

function markAttending(el,eid,isPast){
	var $loading;
	var $el = $(el);
	if (!$el.next().is('.loading')) {
		$loading = $('<span class="loading">Loading...</span>');
		var pos = $el.position();
		$loading.css({left: pos.left + 15, top: pos.top - 30}).hide();
		$el.after($loading);
		$loading.fadeIn('fast');
	}

	var obj=new Object();
	obj.eid=eid;

	apiRequest('event','attend',obj, function(obj) {
		if ($el.is('.btn-success')) {
			$el.removeClass('btn-success');
			link_txt=isPast ? 'I attended' : 'I\'m attending';
			adjustAttendCount(eid, -1);
		} else {
			$el.addClass('btn-success');
			link_txt=isPast ? 'I attended' : 'I\'m attending';
			adjustAttendCount(eid, 1);
		}

		$el.html(link_txt);

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
		$('#resource_row_'+obj.aid).css('display','none');
	});
}

function unlinkSpeaker(talk_id, speaker_id, css_row_id){
	var obj=new Object();
	obj.talk_id=talk_id;
	obj.speaker_id=speaker_id;
	obj.css_row_id=css_row_id;
	apiRequest('user', 'unlink', obj, function(obj) {
		$('#resource_row_'+obj.css_row_id).hide();
	});
}

function removeTalkClaim(claim_id, css_row_id){
	var obj=new Object();
	obj.claim_id=claim_id;
	obj.css_row_id=css_row_id;
	apiRequest('user', 'removeTalkClaim', obj, function(obj) {
		$('#resource_row_'+obj.css_row_id).hide();
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
	notifications.alert('Role added!');
}
function addEventAdmin(eid){
	var uname	= $('#add_admin_user').val();
	var obj		= new Object();
	obj.eid		= eid;
	obj.username=uname;
	apiRequest('event','addadmin',obj, function(obj) {
		if(obj.msg=='Success'){
			$('#evt_admin_list').append('<li id="evt_admin_'+obj.user.ID+'"><a href="/user/view/'+obj.user.ID+'">'+obj.user.full_name+'</a> [<a onclick="removeEventAdmin('+eid+',\''+obj.user.username+'\','+obj.user.ID+')" href="#">X</a>]');
		}else{
            notifications.alert(obj.msg);
        }
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

function loadUserData(){
	var obj		= new Object();
	obj.uid=$('#uid').val();
	apiRequest('user','getdetail',obj, function(obj) {
		$.each(obj,function(k,v){
			$('#ulist').append('<li>'+v.full_name);
		});
	});
}

function addTrackRow(){
	rid=$('#track_tbl_body tr').length+1;
	$('#track_tbl_body').append('\
		<tr id="rid_'+rid+'">\
			<td>'+rid+'</td>\
			<td style="vertical-align:top">\
				<input type="text" name="name_'+rid+'" id="name_'+rid+'" size="15"/>\
				<div id="disp_name_'+rid+'" style="display:none"></div>\
			</td>\
			<td style="vertical-align:top">\
				<textarea cols="25" rows="4" name="desc_'+rid+'" id="desc_'+rid+'"></textarea>\
				<div id="disp_desc_'+rid+'" style="display:none"></div>\
			</td>\
			<td colspan="2" style="vertical-align:top" align="right" id="ctrl_cell_'+rid+'">\
				<a href="#" class="btn-small" onClick="saveTrackAdd('+rid+')">save</a>\
				<a href="#" class="btn-small" onClick="cancelTrackAdd('+rid+')">cancel</a>\
			</td>\
		</tr>\
	');
}
function cancelTrackAdd(rid){
	$("#track_tbl_body tr[id='rid_"+rid+"']").remove();
}
function cancelTrackEdit(rid){
	//Switch back to display
	switchTrackDisplay(rid);

	var tid = $('#rid_' + rid).attr('data-trackid');

	$('#ctrl_cell_'+rid).html('\
		<a href="#" class="btn-small" onClick="editTrack('+rid+')">edit</a>\
		<a href="#" class="btn-small" onClick="deleteTrack('+rid+','+ tid +')">delete</a>\
	');
}
function saveTrackAdd(rid){
	var obj			= new Object();
	obj.event_id	= $('#event_id').val();
	obj.track_name	= "<![CDATA[" + $("#track_tbl_body input[id='name_"+rid+"']").val() + "]]>";
	obj.track_desc	= "<![CDATA[" + $("#track_tbl_body textarea[id='desc_"+rid+"']").val() + "]]>";

	apiRequest('event','addtrack',obj, function(obj) {
		notifications.alert(obj.msg);
		if(obj.msg=='Success'){
			//Switch back to display
			switchTrackDisplay(rid);

			//And clear out that last cell..
			$('#ctrl_cell_'+rid).html('');
		}
	});
}
function saveTrackUpdate(rid){
	var obj			= new Object();
	obj.event_id	= $('#event_id').val();
	obj.track_name	= "<![CDATA[" + $("#track_tbl_body input[id='name_"+rid+"']").val() + "]]>";
	obj.track_desc	= "<![CDATA[" + $("#track_tbl_body textarea[id='desc_"+rid+"']").val() + "]]>";
	obj.track_id	= $("#track_tbl_body input[id='trackid_"+rid+"']").val();
	obj.track_color	= $("#track_tbl_body input[id='track_color_"+rid+"']").val();

	apiRequest('event','updatetrack',obj, function(obj) {
		notifications.alert(obj.msg);
		if(obj.msg=='Success'){
			//Switch back to display
			switchTrackDisplay(rid);

			//And clear out that last cell..
			$('#ctrl_cell_'+rid).html('');
		}
	});
}
function deleteTrack(rid,tid){
	var obj			= new Object();
	obj.event_id	= $('#event_id').val();
	obj.track_id	= tid;

	apiRequest('event','deletetrack',obj, function(obj) {
		notifications.alert(obj.msg);
		if(obj.msg=='Success'){ $("#track_tbl_body tr[id='rid_"+rid+"']").remove(); }
	});
}
function editTrack(rid){
	$('#disp_name_'+rid).hide(); $('#name_'+rid).show();
	$('#disp_desc_'+rid).hide(); $('#desc_'+rid).show();
	$('#track_color_sel_'+rid).show();
	//Put the "save" and "cancel" buttons in the last column...

	$('#ctrl_cell_'+rid).html('\
		<a href="#" class="btn-small" onClick="saveTrackUpdate(\''+rid+'\')">save</a>\
		<a href="#" class="btn-small" onClick="cancelTrackEdit(\''+rid+'\')">cancel</a>\
	');
}
function switchTrackDisplay(rid){
	//Switch over to the display-only versions...
	$('#name_'+rid).hide();
	$('#disp_name_'+rid).html($("#track_tbl_body input[id='name_"+rid+"']").val());
	$('#disp_name_'+rid).show();

	$('#desc_'+rid).hide();
	$('#disp_desc_'+rid).html($("#track_tbl_body textarea[id='desc_"+rid+"']").val());
	$('#disp_desc_'+rid).show();

	$('#track_color_sel_'+rid).hide();
}

function updateTrackColor(rid,color){
	$('#track_color_'+rid+'_block').css('background-color','#'+color);
	$('#track_color_'+rid).val(color);
}


function setStars(rate){
	$('.rating .star').eq(rate-1).click();
}

$(document).ready(function(){
    $('#showLimit').change(function(){
        $('#userAdminForm').submit();
    });
});
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


function padstring (itemToPad,length,padWith)
{
   if (padWith === undefined || padWith.length == 0) {
       padWith = '0';
   }
   padWith = padWith.toString();
   return (itemToPad.toString().length>length)?itemToPad:(Array(length).join(padWith)+itemToPad).slice(-length);

}

/* Navigation menu for mobile */
jQuery(document).ready(function ($) {
    $("#hd .menu ul").tinyNav({header: 'Navigation'});
});
