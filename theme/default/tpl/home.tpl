<strong>Management/visualization of wishlists</strong><br />

{if $sessionOk}
	<p>
		{if !empty($userLastLogin) }
		Welcome <b>{$user->name}</b>, your last visit was on {$userLastLogin|date_format:$lngDateFormat}
		{else}
		Welcome <b>{$user->name}</b>
		{/if}
	</p>
	<p>Choose a list to show in the left menu</p>
{else}
<p>Choose a list to show in the left menu</p>
<div style="width: 300px; margin-left: auto; margin-right: auto; text-align: center">
	{include file='login_form.tpl'}
</div>
{/if}
<!-- Images preloading -->
<img src="{$themeWebDir}/img/add.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/edit.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/edit_big.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/money.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/new.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/options.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/delete.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/logout.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/delete.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/exclamation.png" style="display:none" alt="" /> 
<img src="{$themeWebDir}/img/print.png" style="display:none" alt="" /> 
