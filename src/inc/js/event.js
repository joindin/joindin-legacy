
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
	
	return {
		init: function(){
			$(document).ready(function(){
				_deleteEventComment();
			});
		}
	}
}();