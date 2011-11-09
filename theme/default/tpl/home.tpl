<strong>Management/visualization of wishlists</strong><br />
<p>Choose a list to show in the left menu</p>
<div style="width: 300px; margin-left: auto; margin-right: auto; text-align: center">
	{if !empty($message)}
		{include file='error_message.tpl'}
	{/if}
	<br />To log in:</strong>
	<form name="login" method="POST">
		<table border="0" width="300" cellpadding="5">
			<tr><td align="left">&nbsp;&nbsp;{$lngLoginLabel}&nbsp;</td><td>
			<input type="text" id="username" name="username" size="15" maxlength="15">
			</td></tr>
			<tr><td align="left">&nbsp;&nbsp;{$lngPasswordLabel}&nbsp;</td><td>
			<input type="password" name="pass" size="15" maxlength="30" />
			</td></tr>
		</table>
		<input type="hidden" name="login_frm_submitted" value="1" />
		<input type="submit" name="submit" value="{$lngLoginAction}" />
	</form>
	<br /><a href="/" onclick="alert('TODO: password reset'); return false">{$lngPasswordForgot}</a>
	<script type="text/javascript">
	$(document).ready(function() {
		$('input[type="submit"]').button();
	});
	</script>
	<!-- Images preloading -->
	<img src="{$themeWebDir}/img/add.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/edit.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/edit_big.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/money.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/new.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/options.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/delete.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/logout.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/delete.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/exclamation.png" style="display:none" alt="">
	<img src="{$themeWebDir}/img/print.png" style="display:none" alt="">
</div>