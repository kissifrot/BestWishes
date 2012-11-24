<div id="event-tabs">
	<ul>
		<li><a href="a_adm_events_mgmt.php?action=list">All events</a></li>
		<li><a href="#tab-add-event">Add an event</a></li>
	</ul>
	<div id="tab-add-event">
		<p>Add a new event</p>
		<form method="post" onsubmit="addEvent(); return false">
			<table>
				<tr>
					<td><label>Name:</label></td>
					<td><input type="text" name="event_name" id="add_event_name" /></td>
				</tr>
				<tr>
					<td><label>Day</label></td>
					<td><input id="add_event_day" type="text" /></td>
				</tr>
				<tr>
					<td><label>Month</label></td>
					<td><input id="add_event_month" type="text" /></td>
				</tr>
				<tr>
					<td><label>Year</label></td>
					<td><input id="add_event_year" type="text" /></td>
				</tr>
				<tr>
					<td><label>Permanent?</label></td>
					<td><input type="checkbox" checked="checked" id="add_event_perm" value="1" /></td>
				</tr>
			</table>
			<input type="submit" value="Add" />
		</form>
	</div>
</div>

<script type="text/javascript">
bwAdminURL = '{$adminWebDir}';
$(document).ready(function() {
	var tabsList = $( '#event-tabs' ).tabs({
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$( anchor.hash ).html( bwLng.couldNotLoadTab );
			}
		}
	});
	$('input[type="submit"]').button();
	$('#add_event_day').datepicker({ dateFormat: 'dd'});
	$('#add_event_month').datepicker({ dateFormat: 'mm'});
	$('#add_event_year').datepicker({ dateFormat: 'yy', minDate: "-1y", maxDate: "+10y"});
});
</script>