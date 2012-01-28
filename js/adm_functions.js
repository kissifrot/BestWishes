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