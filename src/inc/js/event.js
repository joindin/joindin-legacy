
if(!event){ var event = {} }

event = function (){
	
	var _deleteEventComment = function(){
		$('.delete-evt-commment').live('click',function(){
			var p		= this.id.split('_');
			var obj		= new Object();
			obj.cid		= p[0];
			obj.eid		= p[1];
			apiRequest('event','deletecomment',obj, function(obj) {
				$('#comment-'+p[0]).remove();
				alert('Comment removed!'); return false;
			});
		});
		return false;
	}
	
	var _claimEvent = function(){
		$('#claim-event-btn').click(function(){
			alert('here');
			var obj={ "eid": $('#eid').val() };
			apiRequest('event','claim',obj,function(obj){
				alert(obj.msg);
			});
		});
	}
	
	var _markAttending = function(){
		$('#mark-attending').click(function(){
		})
	}
	
	var _toggleAttendees = function(){
		var el = this;
		$('#toggle-attendees').click(function(){
			var el  = this;
			var eid = $('#eid').val();
		
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
		});
	}
	
	return {
		init: function(){
			$(document).ready(function(){
				_deleteEventComment();
				_markAttending();
				_toggleAttendees();
				_claimEvent();
			});
		}
	}
}();