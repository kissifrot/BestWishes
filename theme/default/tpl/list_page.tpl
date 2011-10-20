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
{* The list itself *}
{include file='list_display.tpl'}
</div>
<div id="gift_details_dialog">
	<span id="gift_details_name"></span>
	<div id="gift_details_buy" style="display: none">
		<h4><b>Buy info</b></h4>
		<p>Bought by: <span id="gift_details_buy_who"></span></p>
		<p>On: <span id="gift_details_buy_date"></span></p>
		<span id="gift_details_buy_comment" style="display: none">
			<p>Comment: <span id="gift_details_buy_comment_text"></span></p>
		</span>
	</div>
	<span id="gift_details_url"></span>
	<div id="gift_details_image"></div>
</div>

<br />
{if $sessionOk}
<i>
<h3><img src="{$themeWebDir}/img/information.png">&nbsp;Informations :</h3>
<span class="copyright">
	-&nbsp;Evitez de mettre "et plus" ou "et plus si paru" dans le nom des cadeaux.<br />
	-&nbsp;Les majuscules ne sont pas interdites ;-)<br />
	-&nbsp;Il n'est pas possible de modifier un nom de catégorie, il faut la supprimer.<br />
	-&nbsp;Il est néanmoins possible de faire de façon limitée des modifications minimales aux noms des cadeaux.<br /><br />
</span>
</i>
{/if}
<br /><br />
<h3><img alt="Informations" src="{$themeWebDir}/img/information.png" />&nbsp;<i>{$lngPossibleActions}</i></h3>

<script type="text/javascript">

$(document).ready(function(){ldelim}
	lngDateFormat = '{$lngDateFormat}';
	$('input[type="submit"]').button();
	giftDetailsDialog = $('#gift_details_dialog').dialog({ldelim}autoOpen: false, title: '{$lngDetails}'{rdelim})
{rdelim});
</script>