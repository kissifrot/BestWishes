function isTextNode(){
	return( this.nodeType === 3 );
}

function showFlashMessage(type, message) {
	if(type == 'error') {
		if(!$('#flash_message > div').hasClass('ui-state-error')) {
			// We showed previously an info message
			$('#flash_message > div').removeClass('ui-state-highlight');
			$('#flash_message > div').addClass('ui-state-error');
			$('#flash_message > div > p > span').removeClass('ui-icon-info');
			$('#flash_message > div > p > span').addClass('ui-icon-error');
		}
	} else {
		if(!$('#flash_message > div').hasClass('ui-state-highlight')) {
			// We showed previously an error message
			$('#flash_message > div').removeClass('ui-state-error');
			$('#flash_message > div').addClass('ui-state-highlight');
			$('#flash_message > div > p > span').removeClass('ui-icon-error');
			$('#flash_message > div > p > span').addClass('ui-icon-info');
		}
	}
	if($('#flash_message > div > p').contents().filter(isTextNode).length > 0) {
		$('#flash_message > div > p').contents().filter(isTextNode).replaceWith(document.createTextNode(message));
	} else {
		$('#flash_message > div > p > span').after(message);
	}
	if(type == 'error') {
		$('#flash_message').show();
	} else {
		$('#flash_message').show('fast', function () {setTimeout('fadeOutMessage()', 2000) });
	}
}

function fadeOutMessage()
{
	$('#flash_message').effect('fade', null, 500);
}
