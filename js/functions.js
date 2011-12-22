function showGiftDetailsWindow(giftDetails)
{
	if(typeof giftDetails !== 'undefined') {
		$('#gift_details_name').text(giftDetails.name);
		$('#gift_details_added').text(date(bwLng.dateFormat, strtotime(giftDetails.addedDate)));
		if(giftDetails.isBought) {
			$('#gift_details_buy').show();
			$('#gift_details_buy_who').text(giftDetails.boughtByName);
			if(giftDetails.boughtDate != null) {
				$('#gift_details_buy_date').text(date(bwLng.dateFormat, strtotime(giftDetails.boughtDate)));
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

function addCat(listId)
{
	listId = parseInt(listId);
	catName = $('#cat_name').val();
	if(catName.length < 2) {
		// Category name too short
		showFlashMessage('error', bwLng.catNameTooShort);
	} else {
		$.ajax({
			type: 'POST',
			url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=add',
			data: {type: 'cat', name: catName},
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.success) {
					showFlashMessage('info', returnedData.message);
					$('#cat_name').val('');
					$('#section_add_cat').hide();
					reloadList(listId);
					reloadCatsList(listId);
				} else {
					showFlashMessage('error', returnedData.message);
				}
			}
		});
		
	}
}

function deleteCat(listId, catId)
{
	listId = parseInt(listId);
	catId = parseInt(catId);
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=del',
		data: {type: 'cat', id: catId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.success) {
				showFlashMessage('info', returnedData.message);
				$('#cat_' + catId).remove();
				reloadCatsList(listId);
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function deleteGift()
{
	alert('TODO: deleteGift');
}

function editGift(canEdit)
{
	if(!canEdit) {
		showFlashMessage('error', lbwLng.maxEditsReached);
		return false;
	}
	alert('TODO: editGift');
}

function markGiftAsBought()
{
	alert('TODO: markGiftAsBought');
}

function markGiftAsReceived()
{
	alert('TODO: markGiftAsReceived');
}

function reloadList(listId) {
	listId = parseInt(listId);
	$.ajax({
		type: 'GET',
		url: bwURL + '/a_list_show.php',
		data: {id: listId},
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			$('#div_complete_list').effect('fade', 200, function() {setTimeout(function() {
				$( "#div_complete_list" ).removeAttr( "style" ).hide().fadeIn();
			}, 200 );});
			$('#div_complete_list').html(returnedData);
		}
	});
}

function reloadCatsList(listId) {
	listId = parseInt(listId);
	$.ajax({
		type: 'GET',
		url: bwURL + '/a_cats_show.php',
		data: {id: listId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if (typeof returnedData !== "undefined" && returnedData != null) {
				// Replace the categories list content
				var giftCat = $('.gift_cats_list');
				giftCat.find('option').remove();
				for(var i = 0; i < returnedData.length; i++)
				{
					giftCat.append('<option value="' + returnedData[i].id + '">' + returnedData[i].name + '</option>')
				}
				giftCat.val(returnedData[0].id);
			}
		}
	});
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