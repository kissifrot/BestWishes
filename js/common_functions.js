function showFlashMessage(type, message) {
	var spanContent = '<span class="ui-icon floatingIcon ';
	var $flashMessageDiv = $('#flash_message').find('> div');
	if(type == 'error') {
		spanContent += 'ui-icon-alert">';
		if(!$flashMessageDiv.hasClass('ui-state-error')) {
			// We showed previously an info message
			$flashMessageDiv.removeClass('ui-state-highlight');
			$flashMessageDiv.addClass('ui-state-error');
		}
	} else {
		spanContent += 'ui-icon-info">';
		if(!$flashMessageDiv.hasClass('ui-state-highlight')) {
			// We showed previously an error message
			$flashMessageDiv.removeClass('ui-state-error');
			$flashMessageDiv.addClass('ui-state-highlight');
		}
	}
	spanContent += '</span>';
	$('#flash_message').find('> div > p').html(spanContent + message);
	if(type == 'error') {
		$('#flash_message').show();
	} else {
		$('#flash_message').show('fast', function () {setTimeout('fadeOutMessage()', 2000) });
	}
}

function fadeOutMessage()
{
	$('#flash_message').fadeOut(500);
}
