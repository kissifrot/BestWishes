<div id="list-tabs">
	<ul>
		<li><a href="a_adm_lists_mgmt.php?action=list">All lists</a></li>
		<li><a href="#tab-add-list">Add a list</a></li>
		<li><a href="#tab-edit-rights">List rights</a></li>
	</ul>
	<div id="tab-add-list">
		<p>Add a new list</p>
		<form method="post" onsubmit="addList(); return false">
			<table>
				<tr>
					<td><label>List name:</label></td>
					<td><input type="text" name="list_name" id="add_list_name" /></td>
				</tr>
				<tr>
					<td><label>Related user:</label></td>
					<td>{html_options name=list_user options=$users id=add_list_user}</td>
				</tr>
				<tr>
					<td><label>Birthdate:</label></td>
					<td><input id="add_list_bday" type="text" name="birthdate" /></td>
				</tr>
			</table>
			<input type="submit" value="Add" />
		</form>
	</div>
	<div id="tab-edit-rights">
		<h2>Rights for each list</h2><br />
		{if !empty($lists)}
		<form method="post" id="frm_list_rights" action="" onsubmit="return false">
		{foreach $lists as $list}
		<b>{$list->name}: </b><br />
		<table class="border-collapsed">
			<tr>
				<th>Username</th>
				<th>Can view</th>
				<th>Can edit</th>
				<th>Can mark</th>
				<th>Addition alert</th>
				<th>Purchase alert</th>
			</tr>
			{foreach $allUsers as $user}
			<tr>
				<td>{$user->username}</td>
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
		<i>(no list)</i>
		{/if}
	</div>
</div>

<script type="text/javascript">
{include file='list_translation_strings.tpl'}
bwAdminURL = '{$adminWebDir}';
$(document).ready(function() {
	var tabsList = $( '#list-tabs' ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html( bwLng.couldNotLoadTab );
			}
		}
	});
	$('input[type="submit"]').button();
	$('#add_list_bday').datepicker({ changeYear: true, yearRange: '-120:+0', dateFormat: 'yy-mm-dd', minDate: '-120y', maxDate: '-1m'});
});
</script>