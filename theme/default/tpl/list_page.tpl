{if $daysLeft < 7}
<div class="ui-widget">
	<div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span> 
		{$timeLeftText}</p>
	</div>
</div>
{else}
<div class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em;"> 
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span> 
		{$timeLeftText}</p>
	</div>
</div>
{/if}
<br /><br />
<div id="div_complete_list">
{include file='list_display.tpl'}
</div>

<script type="text/javascript">
$('input[type="submit"]').button();
</script>