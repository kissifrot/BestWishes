function showGiftDetailsWindow(giftDetails)
{
	if(typeof giftDetails !== 'undefined') {
		console.log(giftDetails);
		$('#gift_details_name').text(giftDetails.name);
		if(giftDetails.isBought) {
			$('#gift_details_buy').show();
			$('#gift_details_buy_who').text(giftDetails.boughtBy);
			if(giftDetails.boughtDate != null) {
				$('#gift_details_buy_date').text(date(lngDateFormat, strtotime(giftDetails.boughtDate)));
			}
			if(typeof giftDetails.boughtComment !== 'undefined' && giftDetails.boughtComment != null && giftDetails.boughtComment.length > 0) {
				$('#gift_details_buy_comment_text').text(giftDetails.boughtComment);
			} else {
				$('#gift_details_buy_comment').hide();
			}
		} else {
			$('#gift_details_buy').hide();
		}
		giftDetailsDialog.dialog('open');
	}
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
		$('#flash_message > div > p > span').after(message);
		$('#flash_message').show();
	} else {
		if(!$('#flash_message > div').hasClass('ui-state-highlight')) {
			// We showed previously an error message
			$('#flash_message > div').removeClass('ui-state-error');
			$('#flash_message > div').addClass('ui-state-highlight');
			$('#flash_message > div > p > span').removeClass('ui-icon-error');
			$('#flash_message > div > p > span').addClass('ui-icon-info');
		}
		$('#flash_message > div > p > span').after(message);
		$('#flash_message').show('fast', function () {setTimeout('fadeOutMessage()', 2000) });
	}
}

function fadeOutMessage()
{
	$('#flash_message').effect('fade', null, 500);
}

function deleteCat()
{
	alert('TODO: deleteCat');
}

function deleteGift()
{
	alert('TODO: deleteGift');
}

function margGiftAsBought()
{
	alert('TODO: margGiftAsBought');
}

function margGiftAsReceived()
{
	alert('TODO: margGiftAsReceived');
}

/* Admin */
function editListName(id)
{
	console.log($('#edit_list_' + id));
	console.log(id);
	if($('#edit_list_' + id).length > 0) {
		var newListName = $('#edit_list_' + id).val();
		if(newListName.length > 5) {
			if(newListName == currentListName) {
				// No need to edit anything
				$('#name_list_' + id).show();
				$('#edit_list_' + id).remove();
			} else {
				$.ajax({
					url: 'a_adm_lists_mgmt.php?action=edit',
					type: 'POST',
					dataType: 'json',
					data: { listId: id, newName: newListName },
					error: function(jqXHR, textStatus, errorThrown) {
						alert('Error: ' + errorThrown);
					},
					success: function(data, textStatus, jqXHR) {
						if(data.status == 'error') {
							alert('Error: ' + data.statusMessage);
						} else {
							// All OK
							$('#name_list_' + id).text(newListName);
							$('#orig_name_list_' + id).val(newListName);
							$('#name_list_' + id).show();
							$('#edit_list_' + id).remove();
							showFlashMessage('error', 'Un message');
						}
					}
				});
			}
		}
	}
}