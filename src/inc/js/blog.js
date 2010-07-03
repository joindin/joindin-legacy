
if(!blog){ var blog = {} }

blog = function (){
	
	var _removePostComment = function(){
		$('.delete-comment-btn').click(function(){
			var obj={ "cid": this.id, "bid": 0 };
			apiRequest('blog','deletecomment',obj, function(obj) {
				$('#comment-'+this.id).remove();
				alert('Comment removed!'); return false;
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