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
				$('#gift_details_buy_comment').show();
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
				if(returnedData.status == 'success') {
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

function confirmDeleteCat(listId, catId)
{
	listId = parseInt(listId);
	catId = parseInt(catId);
	$( '#cat_confirm_delete_dialog' ).dialog( 'open' );
	$( '#cat_confirm_delete_dialog' ).dialog({
		resizable: false,
		modal: true,
		title: bwLng.confirmation,
		buttons: [
			{
				text: bwLng.deleteCategory,
				click: function() { 
					deleteCat(listId, catId);
					$(this).dialog('close');
				}
			},
			{
				text: bwLng.cancel,
				click: function() { 
					$(this).dialog('close');
				}
			}
		]
	});
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
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				$('#cat_' + catId).remove();
				reloadCatsList(listId);
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function confirmDeleteGift(giftId, catId, listId) {
	giftId = parseInt(giftId);
	listId = parseInt(listId);
	catId = parseInt(catId);
	$('<div></div>')
	.html(bwLng.confirmGiftDeletion)
	.dialog({
		title: bwLng.confirmation,
		buttons: [
			{
				text: bwLng.deleteIt,
				click: function() { 
					deleteGift(giftId, catId, listId);
					$(this).dialog('close');
				}
			},
			{
				text: bwLng.cancel,
				click: function() { 
					$(this).dialog('close');
				}
			}
		]
	});
}

function deleteGift(giftId, catId, listId)
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=del',
		data: {type: 'gift', catId: catId, id: giftId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				reloadList(listId);
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function editGift(canEdit)
{
	if(!canEdit) {
		showFlashMessage('error', bwLng.maxEditsReached);
		return false;
	}
	alert('TODO: editGift');
}

function addGift(listId, detailedAdd, force) {

	catId = $('#gift_cat').val();
	giftData = null;
	detailedAdd = detailedAdd;
	if(catId.length > 0) {
		listId = parseInt(listId);
		catId = parseInt(catId);
		if(detailedAdd) {
			//giftName =  
		} else {
			giftName = $('#gift_name').val();
			if(giftName.length < 2) {
				showFlashMessage('error', bwLng.giftNameTooShort);
			} else {
				if(force) {
					giftData = {type: 'gift', catId: catId, name: giftName, force: '1'};
				} else {
					giftData = {type: 'gift', catId: catId, name: giftName, force: '0'};
				}
				$.ajax({
					type: 'POST',
					url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=add',
					data: giftData,
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown) {
						showFlashMessage('error', 'An error occured: ' + errorThrown);
					},
					success: function(returnedData, textStatus, jqXHR) {
						if(returnedData.status == 'success') {
							showFlashMessage('info', returnedData.message);
							$('#gift_name').val('');
							$('#section_add_gift').hide();
							reloadList(listId);
						} else {
							if(returnedData.status == 'confirm') {
								// Show a confirmation dialog
								$('<div></div>')
								.html(returnedData.message)
								.dialog({
									title: bwLng.confirmation,
									buttons: [
										{
											text: bwLng.addAnyway,
											click: function() { 
												addGift(listId, detailedAdd, true);
												$(this).dialog('close');
											}
										},
										{
											text: bwLng.cancel,
											click: function() { 
												$(this).dialog('close');
											}
										}
									]
								});
							} else {
								showFlashMessage('error', returnedData.message);
							}
						}
					}
				});
			}
		}
	}
}

function addSurpriseGift(listId, force) {

	catId = $('#surprise_gift_cat').val();
	giftData = null;
	if(catId.length > 0) {
		listId = parseInt(listId);
		catId = parseInt(catId);
		giftName = $('#surprise_gift_name').val();
		if(giftName.length < 2) {
			showFlashMessage('error', bwLng.giftNameTooShort);
		} else {
			if(force) {
				giftData = {type: 'sgift', catId: catId, name: giftName, force: '1'};
			} else {
				giftData = {type: 'sgift', catId: catId, name: giftName, force: '0'};
			}
			$.ajax({
				type: 'POST',
				url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=add',
				data: giftData,
				dataType: 'json',
				error: function(jqXHR, textStatus, errorThrown) {
					showFlashMessage('error', 'An error occured: ' + errorThrown);
				},
				success: function(returnedData, textStatus, jqXHR) {
					if(returnedData.status == 'success') {
						showFlashMessage('info', returnedData.message);
						$('#surprise_gift_name').val('');
						$('#section_add_surprise_gift').hide();
						reloadList(listId);
					} else {
						if(returnedData.status == 'confirm') {
							// Show a confirmation dialog
							$('<div></div>')
							.html(returnedData.message)
							.dialog({
								title: bwLng.confirmation,
								buttons: [
									{
										text: bwLng.addAnyway,
										click: function() { 
											addSurpriseGift(listId, true);
											$(this).dialog('close');
										}
									},
									{
										text: bwLng.cancel,
										click: function() { 
											$(this).dialog('close');
										}
									}
								]
							});
						} else {
							showFlashMessage('error', returnedData.message);
						}
					}
				}
			});
		}
	}
}

function showBuyWindow(giftName, giftId, catId, listId)
{
	giftId = parseInt(giftId);
	listId = parseInt(listId);
	catId = parseInt(catId);
	$('#bought_gift_name').text(giftName);
	$('#gift_purchase_dialog').dialog( 'option', 'buttons', 
	[
		{
			text: bwLng.confirmPurchase,
			id: 'btn-confirm-purchase',
			click: function() { 
				$('#btn-confirm-purchase').button( 'disable' );
				$('#btn-confirm-purchase').button( 'option', 'label', bwLng.pleaseWait );
				markGiftAsBought(giftId, catId, listId);
			}
		},
		{
			text: bwLng.cancel,
			click: function() {
				$('#gift_purchase_dialog').dialog('close');
			}
		}
	]
	);
	$('#gift_purchase_dialog').dialog( 'open' );
}

function markGiftAsBought(giftId, catId, listId)
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=mark_bought',
		data: {type: 'gift', catId: catId, id: giftId, purchaseComment: $('#purchase_comment').val()},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				$('#purchase_comment').val('');
				$('#gift_purchase_dialog').dialog('close');
				showFlashMessage('info', returnedData.message);
				reloadList(listId);
			} else {
				// Reenable the button
				$('#btn-confirm-purchase').button( 'enable' );
				$('#btn-confirm-purchase').button( 'option', 'label', bwLng.confirmPurchase );
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function confirmMarkGiftAsReceived(giftId, catId, listId) {
	giftId = parseInt(giftId);
	listId = parseInt(listId);
	catId = parseInt(catId);
	$('<div></div>')
	.html(bwLng.confirmGiftReceive)
	.dialog({
		title: bwLng.confirmation,
		buttons: [
			{
				text: bwLng.confirm,
				click: function() { 
					markGiftAsReceived(giftId, catId, listId);
					$(this).dialog('close');
				}
			},
			{
				text: bwLng.cancel,
				click: function() { 
					$(this).dialog('close');
				}
			}
		]
	});
}

function markGiftAsReceived(giftId, catId, listId)
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_gifts_mgmt.php?listId=' + listId + '&action=mark_received',
		data: {type: 'gift', catId: catId, id: giftId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				reloadList(listId);
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
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
				$( '#div_complete_list' ).removeAttr( 'style' ).hide().fadeIn();
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
			if (typeof returnedData !== 'undefined' && returnedData != null) {
				// Replace the categories list content
				var giftCat = $('.gift_cats_list');
				giftCat.find('option').remove();
				for(var i = 0; i < returnedData.length; i++)
				{
					giftCat.append('<option value="' + returnedData[i].id + '">' + returnedData[i].name + '</option>');
				}
				giftCat.val(returnedData[0].id);
			}
		}
	});
}

/* Options */

function updatePwd() {
	currentPwd = $('#pass').val();
	newPwd = $('#new_pwd').val();
	newPwdRepeat = $('#new_pwd_repeat').val();
	if(currentPwd.length < 6) {
		showFlashMessage('error', bwLng.currentPwdNotSpecified);
	} else {
		if(newPwd.length < 6 || newPwd != newPwdRepeat) {
			showFlashMessage('error', bwLng.bothRepeatPwdNotMatch);
		} else {
			if(currentPwd == newPwd) {
				showFlashMessage('error', bwLng.nothingChange);
			} else {
				// Let the server handle the rest
				$.ajax({
					type: 'POST',
					url: bwURL + '/a_opts_mgmt.php?action=editpwd',
					data: {currentPasswd: currentPwd, newPasswd: newPwd, newPasswdRepeat: newPwdRepeat},
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown) {
						showFlashMessage('error', 'An error occured: ' + errorThrown);
					},
					success: function(returnedData, textStatus, jqXHR) {
						if(returnedData.status == 'success') {
							showFlashMessage('info', returnedData.message);
							$('#pass').val('');
							$('#new_pwd').val('');
							$('#new_pwd_repeat').val('');
						} else {
							showFlashMessage('error', returnedData.message);
							$('#pass').val('');
							$('#new_pwd').val('');
							$('#new_pwd_repeat').val('');
						}
					}
				});
			}
		}
	}
}

function updateRight(listId, rightElement, rightType) {
	listId = parseInt(listId);
	rightElement = rightElement;
	if(rightElement.checked) {
		rElementChecked = true;
		rightData = {rtype: rightType, enabled: '1' };
	} else {
		rElementChecked = false;
		rightData = {rtype: rightType, enabled: '0' };
	}
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_opts_mgmt.php?listId=' + listId + '&action=editrights',
		data: rightData,
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
			} else {
				showFlashMessage('error', returnedData.message);
				// Revert the check status
				if(rElementChecked) {
					rightElement.checked = false;
				} else {
					rightElement.checked = true;
				}
			}
		}
	});
}

/* Admin */
function editListName(id)
{
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