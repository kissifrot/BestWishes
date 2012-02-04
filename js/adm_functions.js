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
						showFlashMessage('error', 'An error occured: ' + errorThrown);
					},
					success: function(returnedData, textStatus, jqXHR) {
						if(returnedData.status == 'error') {
							showFlashMessage('error', returnedData.message);
						} else {
							// All OK
							$('#name_list_' + id).text(newListName);
							$('#orig_name_list_' + id).val(newListName);
							$('#name_list_' + id).show();
							$('#edit_list_' + id).remove();
							showFlashMessage('error', returnedData.message);
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
					showFlashMessage('info', returnedData.message);
				}
			}
		});
	}
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