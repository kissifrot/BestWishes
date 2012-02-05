<div id="list-tabs">
	<ul>
		<li><a href="a_adm_users_mgmt.php?action=list">All users</a></li>
		<li><a href="#tab-add-user">Add an user</a></li>
		<li><a href="#tab-edit-rights">User rights</a></li>
	</ul>
	<div id="tab-add-user">
		<p>Add a new user</p>
		<form method="POST" onsubmit="addUser(); return false">
			<table>
				<tr>
					<td><label>Username *</label></td>
					<td><input type="text" name="username" id="username_add" /></td>
				<tr>
				<tr>
					<td><label>Name *</label></td>
					<td><input type="text" name="name" id="name_add" /><td>
				<tr>
				<tr>
					<td><label>Password *</label></td>
					<td><input type="password" name="pwd" id="pwd_add" /></td>
				<tr>
				<tr>
					<td><label>Password (repeat) *</label></td>
					<td><input type="password" name="pwd_repeat" id="pwd_repeat_add" /></td>
				<tr>
				<tr>
					<td><label>E-mail address</label></td>
					<td><input type="text" name="email" id="email_add" /></td>
				<tr>
			<table>
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