{foreach from=$users item=user}
	<div id="user_{$user->getId()}">
		<span id="username_user_{$user->getId()}">{$user->username}</span>
		<span id="name_user_{$user->getId()}">{$user->name}</span>
		&nbsp;{$user->getTheme()->name}
		&nbsp;<img alt="Rename" id="edit_user_btn_{$user->getId()}" title="Rename" src="{$themeWebDir}/img/edit.png" class="editUser clickable" />
		&nbsp;<img alt="Delete" id="delete_user_btn_{$user->getId()}" title="Delete" src="{$themeWebDir}/img/delete.png" class="deleteUser clickable" />
	</div>
{foreachelse}
<i>There are no users yet</i><a href="#" id="add-user-link">Add one</a>
<script type="text/javascript">
$(document).ready(function() {
	$('#add-user-link').click(function() {
		tabsList.tabs('select', 2);
		return false;
	});
});
</script>
{/foreach}
<script type="text/javascript">
$(document).ready(function() {
	$('.editUser').click(function() {
	});
	$('.deleteUser').click(function() {
		var userId = this.id.substr(16, this.id.length);
		currentUserId = parseInt(userId);
		// Show a confirmation dialog
		$('<div></div>')
		.html(bwLng.confirmUserDeletion)
		.dialog({
			title: bwLng.confirmation,
			buttons: [
				{
					text: bwLng.deleteIt,
					click: function() { 
						deleteUser(currentUserId);
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