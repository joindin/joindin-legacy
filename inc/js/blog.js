
if(!blog){ var blog = {} }

blog = function (){
	
	var _removePostComment = function(){
		$('.delete-comment-btn').click(function(){
			var cid = this.id;
			var obj = { "cid": cid, "bid": 0 };
			apiRequest('blog','deletecomment',obj, function(obj) {
				$('#comment-'+cid).remove();
				notifications.alert('Comment removed!'); return false;
			});
			return false;
		});
	}
	
	return {
		init: function(){
			$(document).ready(function(){
				_removePostComment();
			});
		}
	}
	
}();