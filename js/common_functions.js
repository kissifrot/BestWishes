function isTextNode(){
	return( this.nodeType === 3 );
}

function showFlashMessage(type, message) {
	var spanContent = '<span class="ui-icon floatingIcon ';
	if(type == 'error') {
		spanContent += 'ui-icon-alert">';
		if(!$('#flash_message > div').hasClass('ui-state-error')) {
			// We showed previously an info message
			$('#flash_message > div').removeClass('ui-state-highlight');
			$('#flash_message > div').addClass('ui-state-error');
		}
	} else {
		spanContent += 'ui-icon-info">';
		if(!$('#flash_message > div').hasClass('ui-state-highlight')) {
			// We showed previously an error message
			$('#flash_message > div').removeClass('ui-state-error');
			$('#flash_message > div').addClass('ui-state-highlight');
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
