function addGiftMenu(giftElem) {
	currentGiftElem = giftElem;
	// "Deselect" the categories
	$('.category_list_element_inner').removeClass('selectedCat ui-corner-all');
	$('#cat_actions_menu').hide();

	$('.gift_list_element').not(giftElem).removeClass('selectedGift ui-corner-all');
	giftElem.addClass('selectedGift ui-corner-all');
	if(giftElem.data('canmarkbought')) {
		$('#action_show_buy').show();
	} else {
		$('#action_show_buy').hide();
	}
	if(giftElem.data('canmarkreceived')) {
		$('#action_mark_received').show();
	} else {
		$('#action_mark_received').hide();
	}
	if(giftElem.data('canmarkgiven')) {
		$('#action_mark_given').show();
	} else {
		$('#action_mark_given').hide();
	}
	if(giftElem.data('canedit')) {
		$('#action_edit_gift').show();
		$('#action_delete_gift').show();
	} else {
		$('#action_edit_gift').hide();
		$('#action_delete_gift').hide();
	}
	$('#gift_actions_menu').show();
	$('#gift_actions_menu a:visible:not(:first)').css('margin-left', '10px');
	giftElem.append($('#gift_actions_menu'));
}

function addCatMenu(catElem) {
	currentCatElem = catElem;
	// "Deselect" the gifts
	$('.gift_list_element').removeClass('selectedGift ui-corner-all');
	$('#gift_actions_menu').hide();

	$('.category_list_element_inner').not(catElem).removeClass('selectedCat ui-corner-all');
	catElem.addClass('selectedCat ui-corner-all');
	if(catElem.data('canedit')) {
		$('#action_delete_cat').show();
	} else {
		$('#action_delete_cat').hide();
	}
	catElem.append($('#cat_actions_menu'));
}

function selectGift(giftElem) {
	currentGiftId = giftElem.data('giftid');
	currentCatId = giftElem.parent().find('.category_list_element_inner').data('catid');
}

function selectCat(catElem) {
	currentCatId = catElem.data('catid');
	if(catElem.data('canedit')) {
		$('#action_delete_cat').show();
	} else {
		$('#action_delete_cat').hide();
	}
	$('#cat_actions_menu').show();
	catElem.append($('#cat_actions_menu'));
}

function showGiftDetailsWindow()
{
	$.ajax({
		url: bwURL + '/a_elem_show.php?listId=' + currentListId + '&type=gift&id=' + currentGiftId,
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(typeof returnedData !== 'undefined') {
				var giftDetails = returnedData;
				$('#gift_details_name').text(giftDetails.name);
				$('#gift_details_added').text(date(bwLng.dateFormat, strtotime(giftDetails.addedDate)));
				if(giftDetails.moreDetail != null && giftDetails.moreDetail.length > 0) {
					$('#gift_details_more').show();
					if(urlPattern.test(giftDetails.moreDetail)) {
						$('#gift_details_more_text').html('<a id="gift_link_url" href="' + giftDetails.moreDetail + '" target="_blank">' + bwLng.visitGiftUrl + '</a>');
						$('#gift_link_url').button();
					} else {
						$('#gift_details_more_text').text(giftDetails.moreDetail);
					}

				}
				if(giftDetails.isBought) {
					$('#gift_details_buy').show();
					$('#gift_details_buy_who').text(giftDetails.boughtByName);
					if(giftDetails.purchaseDate != null) {
						$('#gift_details_buy_date').text(date(bwLng.dateFormat, strtotime(giftDetails.purchaseDate)));
					}
					if(typeof giftDetails.purchaseComment !== 'undefined' && giftDetails.purchaseComment != null && giftDetails.purchaseComment.length > 0) {
						$('#gift_details_buy_comment').show();
						$('#gift_details_buy_comment_text').text(giftDetails.purchaseComment);
					} else {
						$('#gift_details_buy_comment').hide();
					}
				} else {
					$('#gift_details_buy').hide();
				}
				giftDetailsDialog.dialog('open');
			} else {
				showFlashMessage('error', 'Internal error');
			}
		}
	});
}

function addCat()
{
	currentCatName = $('#cat_name').val();
	if(currentCatName.length < 2) {
		// Category name too short
		showFlashMessage('error', bwLng.catNameTooShort);
	} else {
		$.ajax({
			type: 'POST',
			url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=add',
			data: {type: 'cat', name: currentCatName},
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.status == 'success') {
					showFlashMessage('info', returnedData.message);
					$('#cat_name').val('');
					$('#section_add_cat').hide();
					reloadList();
					reloadCatsList();
				} else {
					showFlashMessage('error', returnedData.message);
				}
			}
		});
	}
}

function confirmDeleteCat()
{
	if(currentCatElem.data('empty')) {
		deleteCat(currentListId, currentCatId);
	} else {
		$( '#cat_confirm_delete_dialog' ).dialog( 'open' );
		$( '#cat_confirm_delete_dialog' ).dialog({
			width: 400,
			resizable: false,
			modal: true,
			title: bwLng.confirmation,
			buttons: [
				{
					text: bwLng.deleteCategory,
					click: function() { 
						deleteCat(currentListId, currentCatId);
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
}

function deleteCat()
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=del',
		data: {type: 'cat', id: currentCatId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				removeCatElement();
				reloadCatsList();
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function confirmDeleteGift() {
	$('<div></div>')
	.html(bwLng.confirmGiftDeletion)
	.dialog({
		title: bwLng.confirmation,
		buttons: [
			{
				text: bwLng.deleteIt,
				click: function() { 
					deleteGift(currentGiftId, currentListId);
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

function deleteGift(giftId, listId)
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_list_mgmt.php?listId=' + listId + '&action=del',
		data: {type: 'gift', id: giftId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				removeGiftElement();
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function moveCatGiftMenu() {
	$('#gift_actions_menu').hide();
	$('body').append($('#gift_actions_menu'));
	$('#cat_actions_menu').hide();
	$('body').append($('#cat_actions_menu'));
}

/**
 * Removes the gift element
 */
function removeGiftElement() {
	if(currentGiftElem != null) {
		// Move the gift menu elsewhere
		moveCatGiftMenu();
		// Remove the gift element
		currentGiftElem.remove();
		// Reset the variables
		currentGiftElem = null;
		currentGiftId = null;
	}
}

/**
 * Removes the cat element
 */
function removeCatElement() {
	if(currentCatElem != null) {
		// Move the cat and gift menu elsewhere
		moveCatGiftMenu();
		// Remove the gift element
		currentCatElem.parent().remove();
		// currentCatElem.remove();
		// Reset the variables
		currentCatElem = null;
		currentCatId = null;
		currentGiftElem = null;
		currentGiftId = null;
	}
}

function startEditGift()
{
	currentGiftName = currentGiftElem.data('giftname');
	$('<input type="text" id="gift_edit_name" />')
	.insertAfter('#gif_name_' + currentGiftId)
	.val(currentGiftName)
	.focus()
	.blur(function(evt) {
		endEditGift();
	})
	.keyup(function(evt) {
		if(evt.which == 13) {
			endEditGift();
		}
	});
	$('#gif_name_' + currentGiftId).hide();
	$('#actn_edit_gift_' + currentGiftId).hide();
}

function endEditGift()
{
	if(currentGiftId != null) {
		giftName = $('#gift_edit_name').val();
		if(giftName.length < 2) {
			showFlashMessage('error', bwLng.giftNameTooShort);
		} else {
			if(currentGiftName == giftName) {
				// Nothing to change
				$('#gift_edit_name').remove();
				$('#gif_name_' + currentGiftId).show();
				$('#actn_edit_gift_' + currentGiftId).show();
				isEditing = false;
			} else {
				$.ajax({
					type: 'POST',
					url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=edit',
					data: {type: 'gift', catId: currentCatId, id: currentGiftId, newName: giftName},
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown) {
						showFlashMessage('error', 'An error occured: ' + errorThrown);
					},
					success: function(returnedData, textStatus, jqXHR) {
						if(returnedData.status == 'success') {
							showFlashMessage('info', returnedData.message);
							$('#gift_edit_name').remove();
							$('#gif_name_' + currentGiftId).text(giftName).show();
							currentGiftElem.data('giftname', giftName);
							$('#actn_edit_gift_' + currentGiftId).show();
						} else {
							showFlashMessage('error', returnedData.message);
							// Nothing to change
							$('#gift_edit_name').remove();
							$('#gif_name_' + currentGiftId).show();
							$('#actn_edit_gift_' + currentGiftId).show();
						}
					}
				});
			}
		}
	}
}

/*
function addGiftToCat(giftName, catId) {
	var lastElementList = $('#cat_' + catId).find('.gift_list_element').last();
	if(lastElementList.length > 0) {
		clonedElement = lastElementList.clone();
		clonedElement.data('giftname', giftName).appendTo('#cat_' + catId);
		clonedElement.find('.gift_name').text(giftName);
	}
}*/

function addGift(detailedAdd, force) {

	catId = $('#gift_cat').val();
	giftData = null;
	detailedAdd = detailedAdd;
	if(catId.length > 0) {
		currentCatId = parseInt(catId);
		if(detailedAdd) {
			//giftName =  
		} else {
			giftName = $('#gift_name').val();
			var giftDetails = $('#gift_more_detail').val();
			if(giftName.length < 2) {
				showFlashMessage('error', bwLng.giftNameTooShort);
			} else {
				if(force) {
					giftData = {type: 'gift', catId: currentCatId, name: giftName, details: giftDetails, force: '1'};
				} else {
					giftData = {type: 'gift', catId: currentCatId, name: giftName, details: giftDetails, force: '0'};
				}
				$.ajax({
					type: 'POST',
					url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=add',
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
							reloadList();
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
												addGift(detailedAdd, true);
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

function addSurpriseGift(force) {

	catId = $('#surprise_gift_cat').val();
	giftData = null;
	if(catId.length > 0) {
		currentCatId = parseInt(catId);
		giftName = $('#surprise_gift_name').val();
		if(giftName.length < 2) {
			showFlashMessage('error', bwLng.giftNameTooShort);
		} else {
			if(force) {
				giftData = {type: 'sgift', catId: currentCatId, name: giftName, force: '1'};
			} else {
				giftData = {type: 'sgift', catId: currentCatId, name: giftName, force: '0'};
			}
			$.ajax({
				type: 'POST',
				url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=add',
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
						reloadList();
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
											addSurpriseGift(currentListId, true);
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

function showBuyWindow()
{
	var giftName = currentGiftElem.data('giftname');
	$('#bought_gift_name').text(giftName);
	$('#gift_purchase_dialog').dialog( 'option', 'buttons', 
	[
		{
			text: bwLng.confirmPurchase,
			id: 'btn-confirm-purchase',
			click: function() { 
				$('#btn-confirm-purchase').button( 'disable' );
				$('#btn-confirm-purchase').button( 'option', 'label', bwLng.pleaseWait );
				markGiftAsBought();
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

function markGiftAsBought()
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=mark_bought',
		data: {type: 'gift', id: currentGiftId, purchaseComment: $('#purchase_comment').val()},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				$('#purchase_comment').val('');
				$('#gift_purchase_dialog').dialog('close');
				showFlashMessage('info', returnedData.message);
				reloadList();
			} else {
				// Reenable the button
				$('#btn-confirm-purchase').button( 'enable' );
				$('#btn-confirm-purchase').button( 'option', 'label', bwLng.confirmPurchase );
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function showPwdResetWindow()
{
	$('#pwd_reset_dialog').dialog( 'option', 'buttons', 
	[
		{
			text: bwLng.confirm,
			id: 'btn-send-pwd',
			click: function() { 
				var uname = $('#username_rst').val();
				if(uname.length > 3) {
					$('#btn-send-pwd').button( 'disable' );
					$('#btn-send-pwd').button( 'option', 'label', bwLng.pleaseWait );
					askResetPwd(uname);
				} else {
					showFlashMessage('error', bwLng.usernameIncorrect);
				}
			}
		},
		{
			text: bwLng.cancel,
			click: function() {
				$('#pwd_reset_dialog').dialog('close');
			}
		}
	]
	);
	$('#pwd_reset_dialog').dialog( 'open' );
}

function askResetPwd(username)
{
	if(username.length > 2) {
		$.ajax({
			type: 'POST',
			cache: false,
			url: bwURL + '/a_user_mgmt.php?action=resetpwd',
			data: {uname: username},
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.status == 'success') {
					$('#username_rst').val('');
					$('#pwd_reset_dialog').dialog('close');
					showFlashMessage('info', returnedData.message);
				} else {
					// Reenable the button
					$('#btn-send-pwd').button( 'enable' );
					$('#btn-send-pwd').button( 'option', 'label', bwLng.confirm );
					showFlashMessage('error', returnedData.message);
				}
			}
		});
	}
}

function confirmMarkGiftAsReceived() {
	$('<div></div>')
	.html(bwLng.confirmGiftReceive)
	.dialog({
		title: bwLng.confirmation,
		buttons: [
			{
				text: bwLng.confirm,
				click: function() { 
					markGiftAsReceived();
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

function markGiftAsReceived()
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=mark_received',
		data: {type: 'gift', id: currentGiftId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				removeGiftElement();
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}



function confirmMarkGiftAsGiven() {
	$('<div></div>')
	.html(bwLng.confirmGiftGive)
	.dialog({
		title: bwLng.confirmation,
		buttons: [
			{
				text: bwLng.confirm,
				click: function() { 
					markGiftAsGiven();
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

function markGiftAsGiven()
{
	$.ajax({
		type: 'POST',
		url: bwURL + '/a_list_mgmt.php?listId=' + currentListId + '&action=mark_received_s',
		data: {type: 'gift', id: currentGiftId},
		dataType: 'json',
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'success') {
				showFlashMessage('info', returnedData.message);
				removeGiftElement();
			} else {
				showFlashMessage('error', returnedData.message);
			}
		}
	});
}

function reloadList() {
	// Move the menus away to not get them destroyed when rebuilding the list DOM
	moveCatGiftMenu();

	$.ajax({
		type: 'GET',
		cache: false,
		url: bwURL + '/a_elem_show.php',
		data: {listId: currentListId, type: 'list'},
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

function reloadCatsList() {
	$.ajax({
		type: 'GET',
		cache: false,
		url: bwURL + '/a_elem_show.php',
		data: {listId: currentListId, type: 'cats'},
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
				if(returnedData.length > 0)
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
					cache: false,
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

function updateTheme() {
	var currTheme = $('#curr_theme_id').val();
	var newTheme = $('#theme_id').val();
	if(currTheme != newTheme) {
		$.ajax({
			type: 'POST',
			cache: false,
			url: bwURL + '/a_opts_mgmt.php?action=updtheme',
			data: {newTheme: newTheme},
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.status == 'success') {
					showFlashMessage('info', returnedData.message);
					window.setTimeout(function() {
						document.location.reload();
					}, 1000);
				} else {
					showFlashMessage('error', returnedData.message);
				}
			}
		});
	}
}

function updateRight(listId, rightElement, rightType) {
	currentListId = parseInt(listId);
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
		url: bwURL + '/a_opts_mgmt.php?listId=' + currentListId + '&action=editrights',
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
				rightElement.checked = !rElementChecked;
			}
		}
	});
}

