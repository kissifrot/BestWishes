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
		<p>List rights</p>
	</div>
</div>

<script type="text/javascript">
{include file='options_translation_strings.tpl'}
bwURL = '{$webDir}';
{literal}
$(document).ready(function() {
	var tabsList = $( "#options-tabs" ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html(
					"Could not load this tab" );
			}
		}
	});
	$('input[type="submit"]').button();
});
{/literal}
</script>