/* Admin */
function editListName(id)
{
	var $editList = $('#edit_list_' + id);
	var $nameList = $('#name_list_' + id);
	if($editList.length > 0) {
		var newListName = $editList.val();
		if(newListName.length > 5) {
			if(newListName == currentListName) {
				// No need to edit anything
				$nameList.show();
				$editList.remove();
			} else {
				$.ajax({
					url: 'a_adm_lists_mgmt.php?action=edit',
					type: 'POST',
					dataType: 'json',
					data: { listId: id, newName: newListName },
					error: function(jqXHR, textStatus, errorThrown) {
						showFlashMessage('error', 'An error occured: ' + errorThrown);
					},
					success: function(returnedData, textStatus, jqXHR) {
						if(returnedData.status == 'error') {
							showFlashMessage('error', returnedData.message);
						} else {
							// All OK
							$nameList.text(newListName);
							$('#orig_name_list_' + id).val(newListName);
							$nameList.show();
							$editList.remove();
							showFlashMessage('info', returnedData.message);
						}
					}
				});
			}
		}
	}
}

function deleteList(id) {
	$.ajax({
		url: 'a_adm_lists_mgmt.php?action=del',
		type: 'POST',
		dataType: 'json',
		data: { listId: id },
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'error') {
				showFlashMessage('error', returnedData.message);
			} else {
				// All OK
				$('#list_' + currentListId).remove();
				showFlashMessage('info', returnedData.message);
			}
		}
	});
}

function addList() {
	var lName = $('#add_list_name').val();
	var lBday = $('#add_list_bday').val();
	var lUser = $('#add_list_user').val();
	if(lName.length < 2 || lBday.length != 10 || lUser.length < 1) {
		showFlashMessage('error', bwLng.errorFormFields);
	} else {
		$.ajax({
			url: 'a_adm_lists_mgmt.php?action=add',
			type: 'POST',
			dataType: 'json',
			data: { listName: lName, listUser: lUser, listBirthdate: lBday },
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.status == 'error') {
					showFlashMessage('error', returnedData.message);
				} else {
					// All OK
					$('#add_list_name').val('');
					$('#add_list_bday').val('');
					$('#add_list_user').val('');
					showFlashMessage('info', returnedData.message);
				}
			}
		});
	}
}

function updateRight(userId, listId, rightElement, rightType) {
	currentUserId = parseInt(userId);
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
		url: bwAdminURL + '/a_adm_lists_mgmt.php?listId=' + currentListId + '&userId=' + currentUserId + '&action=editrights',
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

function addUser() {
	var uUsername = $('#username_add').val();
	var uName = $('#name_add').val();
	var uPwd = $('#pwd_add').val();
	var uPwdRepeat = $('#pwd_repeat_add').val();
	var uEmail = $('#email_add').val();
	if(uPwd.length < 2 || uPwd != uPwdRepeat || uUsername.length < 1) {
		showFlashMessage('error', bwLng.errorFormFields);
	} else {
		$.ajax({
			url: 'a_adm_users_mgmt.php?action=add',
			type: 'POST',
			dataType: 'json',
			data: { username: uUsername, name: uName, pwd: uPwd, email: uEmail },
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.status == 'error') {
					showFlashMessage('error', returnedData.message);
				} else {
					// All OK
					$('#username_add').val('');
					$('#name_add').val('');
					$('#pwd_add').val('');
					$('#pwd_repeat_add').val('');
					$('#email_add').val('');
					showFlashMessage('info', returnedData.message);
				}
			}
		});
	}
}

function deleteUser(id) {
	$.ajax({
		url: 'a_adm_users_mgmt.php?action=del',
		type: 'POST',
		dataType: 'json',
		data: { userId: id },
		error: function(jqXHR, textStatus, errorThrown) {
			showFlashMessage('error', 'An error occured: ' + errorThrown);
		},
		success: function(returnedData, textStatus, jqXHR) {
			if(returnedData.status == 'error') {
				showFlashMessage('error', returnedData.message);
			} else {
				// All OK
				$('#user_' + currentUserId).remove();
				showFlashMessage('info', returnedData.message);
			}
		}
	});
}

function addEvent() {
	var eName = $('#add_event_name').val();
	var ePerm = $('#add_event_perm').val();
	var eDay = $('#add_event_day').val();
	var ePMonth = $('#add_event_month').val();
	var eYear = $('#add_event_year').val();
	if(eName.length < 2) {
		showFlashMessage('error', bwLng.errorFormFields);
	} else {
		$.ajax({
			url: 'a_adm_events_mgmt.php?action=add',
			type: 'POST',
			dataType: 'json',
			data: { eventName: eName, eventPerm: ePerm, eventDay: eDay, eventMonth: ePMonth, eventYear: eYear },
			error: function(jqXHR, textStatus, errorThrown) {
				showFlashMessage('error', 'An error occured: ' + errorThrown);
			},
			success: function(returnedData, textStatus, jqXHR) {
				if(returnedData.status == 'error') {
					showFlashMessage('error', returnedData.message);
				} else {
					// All OK
					$('#add_event_name').val('');
					$('#add_event_day').val('');
					$('#add_event_month').val('');
					$('#add_event_year').val('');
					showFlashMessage('info', returnedData.message);
				}
			}
		});
	}
}

function editEventName(id)
{
	if($('#edit_event_' + id).length > 0) {
		var newEventName = $('#edit_event_' + id).val();
		if(newEventName.length > 4) {
			if(newEventName == currentEventName) {
				// No need to edit anything
				$('#name_event_' + id).show();
				$('#edit_event_' + id).remove();
			} else {
				$.ajax({
					url: 'a_adm_events_mgmt.php?action=edit',
					type: 'POST',
					dataType: 'json',
					data: { eventId: id, newName: newEventName },
					error: function(jqXHR, textStatus, errorThrown) {
						showFlashMessage('error', 'An error occured: ' + errorThrown);
					},
					success: function(returnedData, textStatus, jqXHR) {
						if(returnedData.status == 'error') {
							showFlashMessage('error', returnedData.message);
						} else {
							// All OK
							$('#name_event_' + id).text(newEventName);
							$('#orig_name_event_' + id).val(newEventName);
							$('#name_event_' + id).show();
							$('#edit_event_' + id).remove();
							showFlashMessage('info', returnedData.message);
						}
					}
				});
			}
		}
	}
}