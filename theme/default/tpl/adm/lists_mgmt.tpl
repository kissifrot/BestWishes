<div id="list-tabs">
	<ul>
		<li><a href="a_adm_lists_mgmt.php?action=list">All lists</a></li>
		<li><a href="#tab-add-list">Add a list</a></li>
		<li><a href="#tab-edit-rights">List rights</a></li>
	</ul>
	<div id="tab-add-list">
		<p>Add a new list</p>
		<form method="POST" onsubmit="addList(); return false">
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
		<p>List rights</p>
	</div>
</div>

<script type="text/javascript">
{include file='list_translation_strings.tpl'}
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