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
	{* Gift details *}
	{include file='gift_details.tpl'}
</div>

{* Gift purchase indication form *}
{include file='form_purchase_gift.tpl'}

<div id="cat_confirm_delete_dialog">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>{$lngConfirmCategoryDeletion}</p>
</div>

<br />
{if $sessionOk && $tipsText}
<h3><img src="{$themeWebDir}/img/information.png" alt="info" class="icon_text"><i>&nbsp;{$lngTipsP}</i></h3>
<span class="smaller"><i>
	{$tipsText|escape}
</i></span>
{/if}
<br /><br />
<div id="possible_actions">
	<h3><img alt="Informations" src="{$themeWebDir}/img/information.png" class="icon_text" />&nbsp;<i>{$lngPossibleActions}</i></h3>
	{if $sessionOk}
		{if $user->canEditList($list->getId())}
			<div class="action_element">
				-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_add_cat').toggle(); reloadCatsList({$list->getId()}); return false"><img alt="{$lngAdd}" class="icon_text" src="{$themeWebDir}/img/add.png" />&nbsp;{$lngActnAddCategory}</a><br />
				<div id="section_add_cat" style="visibility: visible; display: none;">
					<form id="frm_add_cat" name="frm_add_cat" method="post" action="" onsubmit="addCat({$list->getId()}); return false">
					<table border="0" cellpadding="5">
						<tr>
							<td align="left" colspan="2"><em><span class="smaller">{$lngAddCatExplanation}</span></em>
							</td>
						</tr>
						<tr>
							<td>&nbsp;{$lngCategoryNameP}&nbsp;</td><td>
							<input type="text" id="cat_name" name="cat_name" size="30" maxlength="70" />
							</td>
						</tr>
					</table>
					<input type="submit" name="submit" value="Add the category" />
					</form>
				</div>
			</div>
			
			<div class="action_element">
				-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_add_gift').toggle(); return false"><img alt="{$lngAdd}" class="icon_text" src="{$themeWebDir}/img/add.png" />&nbsp;{$lngActnAddGift}</a>
				<div id="section_add_gift" style="visibility: visible; display: none;">
					<form id="frm_ajout_cadeau" name="frm_ajout_cadeau" method="post" action="" onsubmit="addGift({$list->getId()}, false, false, false); return false">
						<table border="0" cellpadding="5">
							<tr>
								<td align="left" colspan="2"><em><span class="smaller">{$lngAddGiftExplanation}</span></em></td>
							</tr>
							<tr>
								<td>&nbsp;{$lngGiftNameP}&nbsp;</td>
								<td>
									<input type="text" id="gift_name" name="gift_name" size="55" maxlength="150" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;{$lngGiftCategoryP}&nbsp;</td>
								<td>
									<select class="gift_cats_list" id="gift_cat" name="gift_cat">
									{foreach from=$list->getCategories() item=category}
										<option value="{$category->getId()}">{$category->name|ucfirst}</option>
									{/foreach}
									</select>
								</td>
							</tr>
						</table>
						<input type="submit" name="submit" value="Add the gift" />
					</form>
				</div>
			</div>
			
			{if $list->categoriesCount > 0}
				<div class="action_element">
					-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_edit_gift').toggle(); return false"><img class="icon_text" alt="{$lngEdit}" src="{$themeWebDir}/img/edit_big.png" />&nbsp;{$lngActnEditGift}</a>
					<div id="section_edit_gift" style="visibility: visible; display: none;">
						<em><span class="smaller">{$lngEditExplanation}</span></em>
					</div>
				</div>
				
				<div class="action_element">
					-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_delete_gift').toggle(); return false"><img class="icon_text" alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" />&nbsp;{$lngActnDelGift}</a>
					<div id="section_delete_gift" style="visibility: visible; display: none;">
						<span class="smaller">{$lngDeleteGiftExplanation}</span></em>
					</div>
				</div>
				
				<div class="action_element">
					-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_delete_cat').toggle(); return false"><img class="icon_text" alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" />&nbsp;{$lngActnDelCategory}</a>
					<div id="section_delete_cat" style="visibility: visible; display: none;">
						<em><span class="smaller">{$lngDeleteCatExplanation}</span></em>
					</div>
				</div>
			{/if}
		{else}
			<div class="action_element">-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_add_surprise_gift').toggle(); return false"><img alt="{$lngAdd}" class="icon_text" src="{$themeWebDir}/img/add.png" />&nbsp;{$lngActnAddSurpriseGift}</a>
				<div id="section_add_surprise_gift" style="visibility: visible; display: none;">
					<form id="frm_add_surprise_gift" name="frm_add_surprise_gift" method="post" action="" onsubmit="addSurpriseGift({$list->getId()}, false); return false">
						<table border="0" cellpadding="5">
							<tr>
								<td align="left" colspan="2"><em><span class="smaller">{$lngAddSurpriseGiftExplanation}</span></em></td>
							</tr>
							<tr>
								<td>&nbsp;{$lngGiftNameP}&nbsp;</td>
								<td>
									<input type="text" id="surprise_gift_name" name="surprise_gift_name" size="55" maxlength="150" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;{$lngGiftCategoryP}&nbsp;</td>
								<td>
									<select class="gift_cats_list" id="surprise_gift_cat" name="surprise_gift_cat">
									{foreach from=$list->getCategories() item=category}
										<option value="{$category->getId()}">{$category->name|ucfirst}</option>
									{/foreach}
									</select>
								</td>
							</tr>
						</table>
						<input type="submit" name="submit" value="Add the gift" />
					</form>
				</div>
			</div>
		{/if}
	{/if}
			<div class="action_element">-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_show_details').toggle(); return false"><img alt="Informations" class="icon_text" src="{$themeWebDir}/img/information.png" />&nbsp;{$lngActnShowDetails}</a>
				<div id="section_show_details" style="visibility: visible; display: none;">
					<em><span class="smaller">{$lngShowDetailsExplanation}</span></em>
				</div>
			</div>
	<div class="action_element">
		-&nbsp;<a href="{$webDir}/list/pdf/list_{$list->slug}" class="list_action_link"><img alt="PDF" class="icon_text" src="{$themeWebDir}/img/pdf.png" />&nbsp;{$lngActnDisplayPDF}</a>
	</div>
</div>
<script type="text/javascript">
{include file='list_translation_strings.tpl'}
bwURL = '{$webDir}';
var pageListId = {$list->getId()};
{literal}
$(document).ready(function(){
	$( '#cat_confirm_delete_dialog' ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true
	});
	$('input[type="submit"]').button();
	giftDetailsDialog = $('#gift_details_dialog').dialog({autoOpen: false, title: bwLng.details});
});
{/literal}
</script>