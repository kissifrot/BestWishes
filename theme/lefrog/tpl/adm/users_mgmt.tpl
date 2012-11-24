<div id="list-tabs">
	<ul>
		<li><a href="a_adm_users_mgmt.php?action=list">All users</a></li>
		<li><a href="#tab-add-user">Add an user</a></li>
		<li><a href="#tab-edit-rights">User rights</a></li>
	</ul>
	<div id="tab-add-user">
		<p>Add a new user</p>
		<form method="post" onsubmit="addUser(); return false">
			<table>
				<tr>
					<td><label>Username *</label></td>
					<td><input type="text" name="username" id="username_add" /></td>
				</tr>
				<tr>
					<td><label>Name *</label></td>
					<td><input type="text" name="name" id="name_add" /><td>
				</tr>
				<tr>
					<td><label>Password *</label></td>
					<td><input type="password" name="pwd" id="pwd_add" /></td>
				</tr>
				<tr>
					<td><label>Password (repeat) *</label></td>
					<td><input type="password" name="pwd_repeat" id="pwd_repeat_add" /></td>
				</tr>
				<tr>
					<td><label>E-mail address</label></td>
					<td><input type="text" name="email" id="email_add" /></td>
				</tr>
			</table>
			<input type="submit" value="Add" />
		</form>
	</div>
	<div id="tab-edit-rights">
		<h2>Rights for each user</h2><br />
		{if !empty($users)}
		<form method="post" id="frm_users_rights" action="" onsubmit="return false">
		{foreach $users as $user}
		<b>{$user->name}: </b><br />
		<table class="border-collapsed">
			<tr>
				<th>List name</th>
				<th>Can view</th>
				<th>Can edit</th>
				<th>Can mark</th>
				<th>Addition alert</th>
				<th>Purchase alert</th>
			</tr>
			{foreach $lists as $list}
			<tr>
				<td>{$list->name}</td>
				<td><input type="checkbox" name="list_{$user->getId()}_{$list->getId()}_view" onclick="updateRight({$user->getId()}, {$list->getId()}, this, 'can_view')" id="list_{$user->getId()}_{$list->getId()}_view"{if $user->canViewList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="list_{$user->getId()}_{$list->getId()}_edit" onclick="updateRight({$user->getId()}, {$list->getId()}, this, 'can_edit')" id="list_{$user->getId()}_{$list->getId()}_edit"{if $user->canEditList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="list_{$user->getId()}_{$list->getId()}_mark" onclick="updateRight({$user->getId()}, {$list->getId()}, this, 'can_mark')" id="list_{$user->getId()}_{$list->getId()}_mark"{if $user->canMarkGiftsForList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="list_{$user->getId()}_{$list->getId()}_add" onclick="updateRight({$user->getId()}, {$list->getId()}, this, 'alert_addition')" id="list_{$user->getId()}_{$list->getId()}_add"{if $user->hasAddAlertForList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="list_{$user->getId()}_{$list->getId()}_purchase" onclick="updateRight({$user->getId()}, {$list->getId()}, this, 'alert_purchase')" id="list_{$user->getId()}_{$list->getId()}_purchase"{if $user->hasPurchaseAlertForList($list->getId())} checked="checked"{/if} /></td>
			</tr>
			{/foreach}
		</table>
		<br />
		{/foreach}
		</form>
		{else}
		<i>(no users)</i>
		{/if}
	</div>
</div>

<script type="text/javascript">
bwAdminURL = '{$adminWebDir}';
{include file='user_translation_strings.tpl'}
$(document).ready(function() {
	var tabsList = $( "#list-tabs" ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html( bwLng.couldNotLoadTab );
			}
		}
	});
	$('input[type="submit"]').button();
});
</script>