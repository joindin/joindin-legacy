
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
		})
	}
	
	return {
		init: function(){
			$(document).ready(function(){
				_addSpeakerLine();
			});
		}
	}
}();