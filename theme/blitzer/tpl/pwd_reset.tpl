<strong>{$lngPasswordReset}</strong><br /><br />
{if $error}
	<div class="ui-widget">
		<div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
			<p><span class="ui-icon ui-icon-alert floatingIcon"></span> 
			{$message}</p>
		</div>
	</div>
{else}
	<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em;"> 
			<p><span class="ui-icon ui-icon-info floatingIcon"></span> 
			{$message}</p>
		</div>
	</div>
{/if}

