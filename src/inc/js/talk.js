
if(!talk){ var talk = {} }

talk = function (){
	
	var speaker_row_ct	= 1;

	var _addSpeakerLine = function(){
		$('#add_speaker_line').click(function(){
			var spr='speaker_row[new_'+speaker_row_ct+']';
			$('#speaker_row_container').append(
				'<input type="text" name="'+spr+'" class="speaker_row"/>'
			);
			speaker_row_ct++;
		});
	}
	
	// Requires API
	var _claimTalk = function(){
		$('#claim_btn').click(function(){ alert('here');
			var obj={ "talk_id": $('#talk_id').val() };
			$('#claim_btn').html('Sending Claim >>');

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
		});
	}
	
	var _editTalkComment = function(){
		$('.edit-talk-comment-btn').click(function(){
			var obj={ "cid": this.id, "rtype" : "talk" };			
			apiRequest('comment','getdetail',obj, function(obj) {
				//jump down to the comments block
				window.location.hash="#comment_form";

				// now set the information so they can edit it
				$('#comment').val(obj[0].comment);
				if(obj[0].private!=0){ $(':checkbox[name=private]').attr('checked',true); }
				setStars(obj[0].rating);
				$(':input[name=edit_comment]').val(this.id);
			});
			return false;
		});
	}
	
	return {
		init: function(){
			$(document).ready(function(){
				_addSpeakerLine();
				_claimTalk();
				_editTalkComment();
			});
		}
	}
}();