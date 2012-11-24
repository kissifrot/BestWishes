<div id="options-tabs">
	<ul>
		<li><a href="#tab-options-pwd">{$lngPassword}</a></li>
		<li><a href="#tab-options-themes">{$lngThemes}</a></li>
		<li><a href="#tab-options-list">{$lngListRightsAndAlerts}</a></li>
	</ul>
	<div id="tab-options-pwd">
		<p>{$lngPasswordManagement}</p>
		<form method="post" action="" onsubmit="updatePwd(); return false">
		<table class="no-border" border="0">
			<tr>
				<td><label>{$lngCurrentPasswordLabel}</label></td>
				<td><input type="password" name="pass" id="pass" /></td>
			</tr>
			<tr>
				<td><label>{$lngNewPasswordLabel}</label></td>
				<td><input type="password" name="new_pwd" id="new_pwd" /></td>
			</tr>
			<tr>
				<td><label>{$lngNewPasswordRepeatLabel}</label></td>
				<td><input type="password" name="new_pwd_repeat" id="new_pwd_repeat" /></td>
			</tr>
		</table>
			<input type="submit" value="{$lngUpdateLabel}" />
		</form>
	</div>
	<div id="tab-options-themes">
		<p>{$lngThemes}</p>
		{$lngThemesExplanation}<br /><br />
		{if !empty($themes)}
		<form method="post" id="frm_themes" action="" onsubmit="updateTheme(); return false">
		<select id="theme_id">
		{foreach $themes as $theme}
			<option value="{$theme->id}"{if $theme->id == $user->theme->id} selected="selected"{/if}>{$theme->name}{if $theme->isDefault} {$lngDefault}{/if}</option>
		{/foreach}
		</select><br /><br />
		<input type="hidden" id="curr_theme_id" value="{$user->theme->id}" />
		<input type="submit" value="{$lngUpdateLabel}" />
		</form>
		{else}
		<i>{$lngNoTheme}</i>
		{/if}
	</div>
	<div id="tab-options-list">
		<p>{$lngListRightsAndAlerts}</p>
		{$lngListRightsAndAlertsExplanation}<br /><br />
		{if !empty($lists)}
		<form method="post" id="frm_list_rights" action="" onsubmit="return false">
		<table class="border-collapsed">
			<tr>
				<th>{$lngListName}</th>
				<th>{$lngCanView}</th>
				<th>{$lngCanEdit}</th>
				<th>{$lngCanMark}</th>
				<th>{$lngAdditionAlert}</th>
				<th>{$lngPurchaseAlert}</th>
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
		<i>{$lngNoList}</i>
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