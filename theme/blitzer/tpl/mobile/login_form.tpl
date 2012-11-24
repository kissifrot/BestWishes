{if !empty($message)}
	{include file='error_message.tpl'}
{/if}
<form name="login" method="post" action="login.php">
		&nbsp;&nbsp;{$lngLoginLabel}&nbsp;
		<input type="text" id="username" name="username" size="15" maxlength="15">
		&nbsp;&nbsp;{$lngPasswordLabel}&nbsp;
		<input type="password" name="pass" size="15" maxlength="30" />
	</table>
	<input type="hidden" name="login_frm_submitted" value="1" />
	<input type="submit" name="submit" value="{$lngLoginAction}" />
</form>
<br /><a href="/" onclick="alert('TODO: password reset'); return false">{$lngPasswordForgot}</a>