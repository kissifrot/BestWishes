<div id="options-tabs">
	<ul>
		<li><a href="#tab-options-pwd">Password</a></li>
		<li><a href="#tab-options-list">Lists rights/alerts</a></li>
	</ul>
	<div id="tab-options-pwd">
		<p>Manage your password</p>
		<form method="POST" action="" onsubmit="updatePwd(); return false">
			<label>Current password:</label>
			<input type="password" name="pass" id="pass" /><br />
			<label>New password:</label>
			<input type="password" name="new_pwd" id="new_pwd" /><br />
			<label>New Password (repeat):</label>
			<input type="password" name="new_pwd_repeat" id="new_pwd_repeat" /><br />
			<input type="submit" value="Update" />
		</form>
	</div>
	<div id="tab-options-list">
		<p>List rights and alerts</p>
		You can adjust your alerts and see your rights for each list below:<br /><br />
		{if !empty($lists)}
		<form method="POST" id="frm_list_rights" action="" onsubmit="return false">
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
			{if !$user->isListOwner($list)}
			<tr>
				<td>{$list->name}</td>
				<td><input type="checkbox" disabled="disabled" name="list_{$list->getId()}_view" id="list_{$list->getId()}_view"{if $user->canViewList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" disabled="disabled" name="list_{$list->getId()}_edit" id="list_{$list->getId()}_edit"{if $user->canEditList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" disabled="disabled" name="list_{$list->getId()}_mark" id="list_{$list->getId()}_mark"{if $user->canMarkGiftsForList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="list_{$list->getId()}_add" onclick="updateRight({$list->getId()}, this, 'alert_addition')" id="list_{$list->getId()}_add"{if $user->hasAddAlertForList($list->getId())} checked="checked"{/if} /></td>
				<td><input type="checkbox" name="list_{$list->getId()}_purchase" onclick="updateRight({$list->getId()}, this, 'alert_purchase')" id="list_{$list->getId()}_purchase"{if $user->hasPurchaseAlertForList($list->getId())} checked="checked"{/if} /></td>
			</tr>
			{/if}
			{/foreach}
		</table>
		</form>
		{else}
		<i>(no list)</i>
		{/if}
		
	</div>
</div>

<script type="text/javascript">
{include file='options_translation_strings.tpl'}
bwURL = '{$webDir}';
{literal}
$(document).ready(function() {
	var tabsList = $( '#options-tabs' ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html( bwLng.couldNotLoadTab );
			}
		}
	});
	$('input[type="submit"]').button();
});
{/literal}
</script>