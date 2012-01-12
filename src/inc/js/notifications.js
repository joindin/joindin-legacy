if(!notifications){ var notifications = {} }

/**
 * Notification Handler
 * 
 * This object makes available replacements for alert() and prompt() that can
 * be used on joind.in
 * 
 * Usage:
 * 
 * notifications.alert("alert message");
 * 
 */
notifications = {
	
	/**
	 * Simple Alert with an OK button that can be closed with 'esc' and does not
	 * stop user from doing other actions (low impact)
	 */
	alert: function (message) {
		
		$('#jQueryUImessageBox').dialog({
				autoOpen: false,
				closeOnEscape: true,
				resizable: false,
				modal: false,
				title: window.document.title,
				buttons: { Ok: function() { $( this ).dialog( "close" ); }}
		});
		
		$("#jQueryUImessageBox").empty();
		$("#jQueryUImessageBox").append(message);
		$("#jQueryUImessageBox").dialog('open');
	},
	
	/**
	 * Modal alert with simple ok button.
	 * Darkens the rest of the page and centers user focus on it.
	 */
	modalAlert: function (message) {
		
		$('#jQueryUImessageBox').dialog({
				autoOpen: false,
				closeOnEscape: true,
				resizable: false,
				modal: true,
				title: window.document.title,
				buttons: { Ok: function() { $( this ).dialog( "close" ); }}
		});
		
		$("#jQueryUImessageBox").empty();
		$("#jQueryUImessageBox").append(message);
		$("#jQueryUImessageBox").dialog('open');
	},
	
	/**
	 * Modal alert with Yes/No options.
	 * This alert takes two callback funtions to attach to the yes/no buttons
	 */
	prompt: function (message, functionOk, functionCancel) {
				$('#jQueryUImessageBox').dialog({
				autoOpen: false,
				closeOnEscape: true,
				resizable: false,
				modal: true,
				title: window.document.title,
				buttons: { 
							Ok: function() { functionOk(); $( this ).dialog( "close" );}, 
							Cancel: function() { functionCancel(); $( this ).dialog( "close" );} 
						 }
		});
		
		$("#jQueryUImessageBox").empty();
		$("#jQueryUImessageBox").append(message);
		$("#jQueryUImessageBox").dialog('open');
	}
	
}