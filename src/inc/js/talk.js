joindin.talk = (function (){
	
	var _addSpeakerLine, _claimTalk, _editTalkComment, _changeAnonymous;

	_addSpeakerLine = function(){
		$('#add_speaker_line').click(function(){
			var speakerCount;
			speakerCount = $("input[name^='speaker_row']").length;
			$('#speaker_row_container').append(
				'<input type="text" name="speaker_row[new_'+(speakerCount+1)+']" class="speaker_row"/>'
			);
		});
	};
	
	// Requires API
	_claimTalk = function(){
		$('#claim_btn').click(function(){
			var obj = { talk_id: $('#talk_id').val() };
			$('#claim_btn').html('Sending Claim >>');

			apiRequest('talk', 'claim', obj, function(obj) {
				//alert(obj);
				$('#claim_btn').css('display','none');
				if (obj.msg=='Success') {
					alert("Thanks for claiming this talk! You will be emailed when the claim is approved!");
				} else {
					alert(obj.msg);
				}
				return false;
			});
			return false;
		});
	};
	
	_editTalkComment = function(){
		$('.edit-talk-comment-btn').click(function(){
			var comment_id, obj;
			comment_id = this.id;
			obj = { cid: comment_id, rtype: "talk" };
			apiRequest('comment', 'getdetail', obj, function(obj) {
				//jump down to the comments block
				window.location.hash="#comment_form";

				// now set the information so they can edit it
				$('#comment').val(obj[0].comment);
				if (obj[0]['private'] != 0) {
					$(':checkbox[name=private]').attr('checked', true); 
				}
				setStars(obj[0].rating);
				$(':input[name=edit_comment]').val(comment_id);
			});
			return false;
		});
	};
	
	_changeAnonymous = function(){
		$('input[name="anonymous"]').click(function(){
			console.debug(this.checked);
			if (this.checked) {
				$('#comment_as_user, #comment_as_user a').css({
					'text-decoration':  'line-through',
					'color':            'silver'
				});
				$('#comment_anonymously').css('display', '');
			} else {
				$('#comment_as_user, #comment_as_user a').css({
					'text-decoration':  '',
					'color':            ''
				});
				$('#comment_anonymously').css('display', 'none');
			}
		});
	};

	return {
		init: function(){
			$(document).ready(function(){
				_addSpeakerLine();
				_claimTalk();
				_editTalkComment();
				_changeAnonymous();
			});
		}
	};
})();