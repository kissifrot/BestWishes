{if !empty($message)}
	{include file='error_message.tpl'}
{/if}
<br /><strong>{$lngToLogin}</strong>
<form name="login" method="post" action="login.php">
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
<br /><a href="/" onclick="showPwdResetWindow(); return false">{$lngPasswordForgot}</a>
<script type="text/javascript">
$(document).ready(function() {
	$('input[type="submit"]').button();
});
bwURL = '{$webDir}';
bwLng = {
	cancel            : '{$lngCancel|escape:javascript}',
	confirmation      : '{$lngConfirmation|escape:javascript}',
	confirm           : '{$lngConfirm|escape:javascript}',
	usernameIncorrect : '{$lngUsernameIncorrect|escape:javascript}',
	pleaseWait        : '{$lngPleaseWait|escape:javascript}',
};
</script>
{include file='form_reset_pwd.tpl'}