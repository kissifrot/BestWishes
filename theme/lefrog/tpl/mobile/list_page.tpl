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
<br />
{* The list itself *}
{include file='list_display.tpl'}

<br />
{if $sessionOk && $tipsText}
<h3><img src="{$themeWebDir}/img/information.png" alt="info" class="icon_text"><i>&nbsp;{$lngTipsP}</i></h3>
<span class="smaller"><i>
	{$tipsText|escape}
</i></span>
{/if}
<br />
<script type="text/javascript">
{include file='list_translation_strings.tpl'}
bwURL = '{$webDir}';
var pageListId = {$list->getId()};
{literal}
$(document).ready(function(){
	$('input[type="submit"]').button();
});
{/literal}
</script>