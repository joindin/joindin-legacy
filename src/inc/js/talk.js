
if(!talk){ var talk = {} }

talk = function (){
	
	var speaker_row_ct	= 1;

	var _addSpeakerLine = function(){
		$('#add_speaker_line').click(function(){
			var spr_count = $("input[name^='speaker_row']").length+1;
			var spr		  = 'speaker_row[new_'+spr_count+']';
			
			$('#speaker_row_container').append(
				'<input type="text" name="'+spr+'" class="speaker_row"/>'
			);
			speaker_row_ct++;
		});
	}
	
	// Requires API
	var _claimTalk = function(){
		$('#claim_btn').click(function(){
			// see if they're logged in...
			if($('#user_id').val().length<=0){
				window.location.href = '/user/login';
				return false;
			}
			
			if($('#claim_btn').attr('name')=='single'){
				
				$('#claim-dialog').dialog({
					autoOpen: false,
					resizable: false,
					modal: true,
					buttons: {
						"Yes, Proceed": function() {
							window.location.href = $('#claim_btn').attr('href');
							$( this ).dialog( "close" );
						},
						Cancel: function() {
							$( this ).dialog( "close" );
						}
					}
				});
				
				//Open confirmation dialog
				$( "#claim-dialog" ).dialog('open');
				
				//Respond to dialog not link
				return false;
			}
			
			var obj={ 
				"talk_id": $('#talk_id').val(),
				"talk_speaker_id": $('#claim_name_select').val()
			};
			$('#claim_select_div').css('display','block');
			$('#claim_btn').css('display','none');
			return false;
			
			$('#claim_btn').html('Sending Claim >>');

			apiRequest('talk','claim',obj, function(obj) {
				//notifications.alert(obj);
				$('#claim_btn').css('display','none');
				if(obj.msg=='Success'){
					notifications.alert("Thanks for claiming this talk! You will be emailed when the claim is approved!");
					$('#claim_select_div').css('display','none');
				}else{
					notifications.alert(obj.msg);
				}
				return false;
			});
			return false;
		});
		$('#claim-cancel-btn').click(function(){
			$('#claim_select_div').css('display','none');
			$('#claim_btn').css({
				'display'	: 'inline',
				'width'		: '90px'
			});
			return false;
		});
	}
	
	var _editTalkComment = function(){
		$('.edit-talk-comment-btn').click(function(){
			var comment_id	= this.id;
			var obj			= { "cid": comment_id, "rtype" : "talk" };			
			apiRequest('comment','getdetail',obj, function(obj) {
				//jump down to the comments block
				window.location.hash="#comment_form";

				// now set the information so they can edit it
				$('#comment').val(obj[0].comment);
				if(obj[0].private!=0){ $(':checkbox[name=private]').attr('checked',true); }

                if (obj[0].rating > 0) {
                    $('#ratingbar-norating').css('display','none');
                    $('#ratingbar').css('display','block');
                    setStars(obj[0].rating);
                } else {
                    $('#ratingbar-norating').css('display','block');
                    $('#ratingbar').css('display','none');
                }
				$(':input[name=edit_comment]').val(comment_id);
			});
			return false;
		});
	}
	
	var _changeAnonymous = function(){
		$('input[name="anonymous"]').click(function(){
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
	}
	
	/* remove old method from above for claim_btn */
	var _claimButton = function(){
		$('#claim-btn').click(function(){
			var obj=new Object();
			//obj.cid		= cid;
			//obj.rtype	= rtype;
			obj.talk_id = $('#talk_id').val();
			obj.talk_speaker_id = $('#claim_name_select').val();
			apiRequest('talk','claim',obj, function(obj) {
				if(obj.msg=='Success'){
					notifications.alert("Thanks for claiming this talk! You will be emailed when the claim is approved!");
				}else{
					notifications.alert(obj.msg);
				}
				return false;
				return false;
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
                _changeAnonymous();
				_claimButton();
			});
		}
	}
}();
