joindin.blog = (function (){
	var _removePostComment;
	
	_removePostComment = function(){
		$('.delete-comment-btn').click(function(){
			var cid, obj;
			cid = this.id;
			obj = { cid: cid, bid: 0 };
			apiRequest('blog','deletecomment', obj, function(obj) {
				$('#comment-'+cid).remove();
				alert('Comment removed!');
				return false;
			});
			return false;
		});
	};
	
	return {
		init: function(){
			$(document).ready(function(){
				_removePostComment();
			});
		}
	};
})();