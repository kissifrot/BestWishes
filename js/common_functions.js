function isTextNode(){
	return( this.nodeType === 3 );
}

function showFlashMessage(type, message) {
	var spanContent = '<span class="ui-icon floatingIcon ';
	if(type == 'error') {
		if(!$('#flash_message > div').hasClass('ui-state-error')) {
			// We showed previously an info message
			$('#flash_message > div').removeClass('ui-state-highlight');
			$('#flash_message > div').addClass('ui-state-error');
			spanContent += 'ui-icon-error">';
		}
	} else {
		if(!$('#flash_message > div').hasClass('ui-state-highlight')) {
			// We showed previously an error message
			$('#flash_message > div').removeClass('ui-state-error');
			$('#flash_message > div').addClass('ui-state-highlight');
			spanContent += 'ui-icon-info">';
		}
	}
	spanContent += '</span>';
	$('#flash_message > div > p').html(spanContent + message);
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
