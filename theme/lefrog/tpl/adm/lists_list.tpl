{if !empty($lists)}
{foreach from=$lists item=list}
	<div id="list_{$list->getId()}">
		<span id="name_list_{$list->getId()}">{$list->name}</span>
		<input type="hidden" name="orig_name_list_{$list->getId()}" id="orig_name_list_{$list->getId()}" value="{$list->name}" />
		&nbsp;{$list->birthdate}
		&nbsp;<img alt="Rename" id="edit_list_btn_{$list->getId()}" title="Rename" src="{$themeWebDir}/img/edit.png" class="editList clickable" />
		&nbsp;<img alt="Delete" id="delete_list_btn_{$list->getId()}" title="Delete" src="{$themeWebDir}/img/delete.png" class="deleteList clickable" />
	</div>
{/foreach}
{else}
<i>There are no lists yet</i> <a href="#" id="add-list-link">Add one</a>
<script type="text/javascript">
$(document).ready(function() {
	$('#add-list-link').button().click(function() {
		tabsList.tabs( "option", "active", 1 );
		return false;
	});
});
</script>
{/if}
<script type="text/javascript">
$(document).ready(function() {
	$('.editList').click(function() {
		var listId = this.id.substr(14, this.id.length);
		currentListId = parseInt(listId);
		currentListName = $('#orig_name_list_' + currentListId).val();
		if($('#edit_list_' + currentListId).length > 0) {
			editListName(currentListId);
		} else {
			$('#name_list_' + currentListId).hide();
			$('#name_list_' + currentListId).after('<input type="text" class="edit_list_name" id="edit_list_' + currentListId + '"></input>');
			$('#edit_list_' + currentListId).val(currentListName);
			$('#edit_list_' + currentListId).keyup(function(event) {
				if(event.which == 13) {
					// Enter key
					var listId = this.id.substr(10, this.id.length);
					currentListId = parseInt(listId);
					editListName(currentListId);
				}
			});
		}
	});
	$('.deleteList').click(function() {
		var listId = this.id.substr(16, this.id.length);
		currentListId = parseInt(listId);
		// Show a confirmation dialog
		$('<div></div>')
		.html(bwLng.confirmListDeletion)
		.dialog({
			title: bwLng.confirmation,
			buttons: [
				{
					text: bwLng.deleteIt,
					click: function() { 
						deleteList(currentListId);
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
	});
});
</script>