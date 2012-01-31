<div id="list-tabs">
	<ul>
		<li><a href="a_adm_lists_mgmt.php?action=list">All users</a></li>
		<li><a href="#tab-add-user">Add an user</a></li>
		<li><a href="#tab-edit-rights">User rights</a></li>
	</ul>
	<div id="tab-add-user">
		<p>Add a new user</p>
		<form method="POST" onsubmit="addUser(); return false">
			<label>Username *</label>
			<input type="text" name="username" id="username_add" /><br />
			<label>Name *</label>
			<input type="text" name="name" id="name_add" /><br />
			<label>Password *</label>
			<input type="password" name="pwd" id="pwd_add" /><br />
			<label>Password (repeat) *</label>
			<input type="password" name="pwd_repeat" id="pwd_repeat_add" /><br />
			<label>E-mail</label>
			<input type="text" name="email" id="email_add" /><br />
			<input type="submit" value="Add" />
		</form>
	</div>
	<div id="tab-edit-rights">
		<p>User rights</p>
	</div>
</div>

<script type="text/javascript">
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