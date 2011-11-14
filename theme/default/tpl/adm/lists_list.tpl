{foreach from=$lists item=list}
	<span id="name_list_{$list->getId()}">{$list->name}</span>
	<input type="hidden" name="orig_name_list_{$list->getId()}" id="orig_name_list_{$list->getId()}" value="{$list->name}" />
	&nbsp;{$list->birthdate}
	&nbsp;<img alt="Rename" id="edit_list_btn_{$list->getId()}" title="Rename" src="{$themeWebDir}/img/edit.png" class="editList clickable" />
	&nbsp;<img alt="Delete" title="Delete" src="{$themeWebDir}/img/delete.png" class="clickable" />
	<br />
{foreachelse}
<i>There are no lists yet</i><a href="#" id="add-list-link">Add one</a>
<script type="text/javascript">
$(document).ready(function() {
	$('#add-list-link').click(function() {
		tabsList.tabs('select', 2);
		return false;
	});
});
</script>
{/foreach}
<script type="text/javascript">
$(document).ready(function() {
	$('.editList').click(function() {
		var listId = this.id.substr(14, this.id.length);
		currentListName = $('#orig_name_list_' + listId).val();
		if($('#edit_list_' + listId).length > 0) {
			editListName(listId);
		} else {
			$('#name_list_' + listId).hide();
			$('#name_list_' + listId).after('<input type="text" class="edit_list_name" id="edit_list_' + listId + '"></input>');
			$('#edit_list_' + listId).val(currentListName);
			$('#edit_list_' + listId).keyup(function(event) {
				if(event.which == 13) {
					// Enter key
					var listId = this.id.substr(10, this.id.length);
					listId = parseInt(listId);
					editListName(listId);
				}
			});
		}
	});
});
</script>