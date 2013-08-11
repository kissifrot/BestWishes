function mobileMarkGiftAsBought()
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=mark_bought',
		data: {type: 'gift', id: currentGiftId, purchaseComment: $('#purchase_comment').val()},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			// do something
		},
		success: function(returnedData, textStatus, jqXHR) {
			/*if(returnedData.status == 'success') {
				$('#purchase_comment').val('');
				$('#gift_purchase_dialog').dialog('close');
				showFlashMessage('info', returnedData.message);
				reloadList();
			} else {
				// Reenable the button
				$('#btn-confirm-purchase').button( 'enable' );
				$('#btn-confirm-purchase').button( 'option', 'label', bwLng.confirmPurchase );
				showFlashMessage('error', returnedData.message);
			}*/
		}
	});
}
