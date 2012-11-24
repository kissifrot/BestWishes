{foreach from=$allEvents item=event}
	<div id="event_{$event->getId()}">
		<span id="name_event_{$event->getId()}">{$event->name}</span>
		<input type="hidden" name="orig_name_event_{$event->getId()}" id="orig_name_event_{$event->getId()}" value="{$event->name}" />
		{$event->day}
		{if $event->month != ''}{$event->month}{/if}
		{if $event->year != ''}{$event->year}{/if}
		<span id="name_event_{$event->getId()}">{if $event->isPermanent}(permanent){/if}</span>
		&nbsp;<img alt="Rename" id="edit_event_btn_{$event->getId()}" title="Rename" src="{$themeWebDir}/img/edit.png" class="editEvent clickable" />
		&nbsp;<img alt="Delete" id="delete_event_btn_{$event->getId()}" title="Delete" src="{$themeWebDir}/img/delete.png" class="deleteEvent clickable" />
	</div>
{foreachelse}
<i>There are no events yet</i><a href="#" id="add-event-link">Add one</a>
<script type="text/javascript">
$(document).ready(function() {
	$('#add-event-link').click(function() {
		tabsList.tabs('select', 2);
		return false;
	});
});
</script>
{/foreach}
<script type="text/javascript">
$(document).ready(function() {
	$('.editEvent').click(function() {
		var eventId = this.id.substr(15, this.id.length);
		currentEventId = parseInt(eventId);
		currentEventName = $('#orig_name_event_' + currentEventId).val();
		if($('#edit_event_' + currentEventId).length > 0) {
			editEventName(currentEventId);
		} else {
			$('#name_event_' + currentEventId).hide();
			$('#name_event_' + currentEventId).after('<input type="text" class="edit_event_name" id="edit_event_' + currentEventId + '"></input>');
			$('#edit_event_' + currentEventId).val(currentEventName);
			$('#edit_event_' + currentEventId).keyup(function(event) {
				if(event.which == 13) {
					// Enter key
					var eventId = this.id.substr(11, this.id.length);
					currentEventId = parseInt(eventId);
					editEventName(currentEventId);
				}
			});
		}
	});
});
</script>