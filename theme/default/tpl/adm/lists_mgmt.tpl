<div id="list-tabs">
	<ul>
		<li><a href="a_adm_lists_mgmt.php?action=list">All lists</a></li>
		<li><a href="#tab-add-list">Add a list</a></li>
		<li><a href="#tab-edit-rights">List rights</a></li>
	</ul>
	<div id="tab-add-list">
		<p>Add a new list</p>
		<form method="POST">
			<label>List name:</label>
			<input type="text" name="list_name" /><br />
			<label>Related user:</label>
			{html_options name=list_user options=$users}
			<!--<select name="list_user"></select>--><br />
			<label>Birthdate:</label>
			<input id="birthdate_picker" type="text" name="birthdate"><br />
			<input type="submit" value="Add" />
		</form>
	</div>
	<div id="tab-edit-rights">
		<p>List rights</p>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	var tabsList = $( '#list-tabs' ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html(
					'Could not load this tab' );
			}
		}
	});
	$('input[type="submit"]').button();
	$('#birthdate_picker').datepicker({ changeYear: true, yearRange: '-120:+0', dateFormat: 'yy-mm-dd', minDate: '-120y', maxDate: '-1m'});
});
</script>