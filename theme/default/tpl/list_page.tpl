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

<div id="cat_confirm_delete_dialog" title="Delete this category?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Deleting this category will delete the gifts too. Are you sure?</p>
</div>

<br />
{if $sessionOk && $tipsText}
<h3><img src="{$themeWebDir}/img/information.png" alt="info" class="icon_text"><i>&nbsp;Tips :</i></h3>
<span class="copyright"><i>
	{$tipsText}
</i></span>
{/if}
<br /><br />
<div id="possible_actions">
	<h3><img alt="Informations" src="{$themeWebDir}/img/information.png" class="icon_text" />&nbsp;<i>{$lngPossibleActions}</i></h3>
	{if $sessionOk}
		{if $user->canDoActionForList($list->getId(), 'edit')}
			<div class="action_element">
				-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_add_cat').toggle(); reloadCatsList({$list->getId()}); return false"><img alt="{$lngAdd}" class="icon_text" src="{$themeWebDir}/img/add.png" />&nbsp;Add a category to the list</a><br />
				<div id="section_add_cat" style="visibility: visible; display: none;">
					<form id="frm_add_cat" name="frm_add_cat" method="post" action="" onsubmit="addCat({$list->getId()}); return false">
					<table border="0" width="550" cellpadding="5">
						<tr>
							<td align="left" colspan="2"><em><span class="copyright">To add a category:<br />-&nbsp;Fill its name<br />-&nbsp;Click on the &#8220;Add the category&#8221; button below</span></em>
							</td>
						</tr>
						<tr>
							<td>&nbsp;&nbsp;Category name&nbsp;:&nbsp;</td><td>
							<input type="text" id="cat_name" name="cat_name" size="25" maxlength="70" />
							</td>
						</tr>
					</table>
					<input type="submit" name="submit" value="Add the category" />
					</form>
				</div>
			</div>
			
			<div class="action_element">
				-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_add_gift').toggle(); return false"><img alt="{$lngAdd}" class="icon_text" src="{$themeWebDir}/img/add.png" />&nbsp;Add a gift (in an already existing category) to the list</a>
				<div id="section_add_gift" style="visibility: visible; display: none;">
					<form id="frm_ajout_cadeau" name="frm_ajout_cadeau" method="post" action="" onsubmit="addGift({$list->getId()}, false, false); return false">
						<table border="0" width="550" cellpadding="5">
							<tr>
								<td align="left" colspan="2"><em><span class="copyright">To add a gift:<br />-&nbsp;Fill its name<br />-&nbsp;Choose its category<br />-&nbsp;If it does not exist, create it using &#8220;Add a category to the list&#8221; above<br />-&nbsp;Click on the &#8220;Add the gift&#8221; just below</span></em></td>
							</tr>
							<tr>
								<td>&nbsp;&nbsp;Gift name&nbsp;:&nbsp;</td><td>
								<input type="text" id="gift_name" name="gift_name" size="60" maxlength="150" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;&nbsp;Gift category&nbsp;:&nbsp;</td><td>
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
					-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_edit_gift').toggle(); return false"><img class="icon_text" alt="{$lngEdit}" src="{$themeWebDir}/img/edit_big.png" />&nbsp;Edit a gift's name</a>
					<div id="section_edit_gift" style="visibility: visible; display: none;">
						<table border="0" width="550" cellpadding="5">
							<tr>
								<td><em><span class="copyright">Pour modifier le nom d'un cadeau, cliquez sur l'icône <img  class="icon_text"src="{$themeWebDir}/img/edit.png" alt="Edit" /> à droite de celui-ci.<br />
								Une fois que vous aurez fini la modification, cliquez à nouveau sur l'icône <img class="icon_text" src="{$themeWebDir}/img/edit.png" alt="Edit" /> ou appuyez sur Entrée pour enregistrer les modifications.
								<br /><img class="icon_text" src="{$themeWebDir}/img/exclamation.png" alt="!" /> Attention, les modifications autorisées sont minimales.</span></em></td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="action_element">
					-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_delete_gift').toggle(); return false"><img class="icon_text" alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" />&nbsp;Delete a gift</a>
					<div id="section_delete_gift" style="visibility: visible; display: none;">
						<table border="0" width="550" cellpadding="5">
							<tr>
								<td><em><span class="copyright">To delete a gift, click on the <img class="icon_text" src="{$themeWebDir}/img/delete.png" alt="Del" /> icon next to it.</span></em></td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="action_element">
					-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_delete_cat').toggle(); return false"><img class="icon_text" alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" />&nbsp;Delete a category</a>
					<div id="section_delete_cat" style="visibility: visible; display: none;">
						<table border="0" width="550" cellpadding="5">
							<tr>
								<td><em><span class="copyright">To delete a category, click on the <img class="icon_text" src="{$themeWebDir}/img/delete.png" alt="Del" /> icon next to it.</span></em></td>
							</tr>
						</table>
					</div>
				</div>
			{/if}
		{else}
			<div class="action_element">-&nbsp;<a href="/" class="list_action_link" onclick="$('#section_add_surprise_gift').toggle(); return false"><img alt="{$lngAdd}" class="icon_text" src="{$themeWebDir}/img/add.png" />&nbsp;Add a surprise gift (in an already existing category) to the list</a>
				<div id="section_add_surprise_gift" style="visibility: visible; display: none;">
					<form id="frm_ajout_cadeau_surprise" name="frm_ajout_cadeau_surprise" method="post" action="" onsubmit="ajouterCadeauSurprise({$list->getId()}); return false">
						<table border="0" width="550" cellpadding="5">
							<tr>
								<td align="left" colspan="2"><em><span class="copyright">To add a surprise gift:<br />-&nbsp;Fill its name<br />-&nbsp;Choose its category<br />-&nbsp;Click on the &#8220;Add the gift&#8221; just below</span></em></td>
							</tr>
							<tr>
								<td>&nbsp;&nbsp;Nom du cadeau&nbsp;:&nbsp;</td><td>
								<input type="text" id="surprise_gift_name" name="surprise_gift_name" size="60" maxlength="150" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;&nbsp;Catégorie du cadeau&nbsp;:&nbsp;</td><td>
								<div id="div_select_surpise_cat">
									<select class="gift_cats_list" id="surprise_gift_cat" name="surprise_gift_cat">
									{foreach from=$list->getCategories() item=category}
										<option value="{$category->getId()}">{$category->name|ucfirst}</option>
									{/foreach}
									</select>
								</div>
								</td>
							</tr>
						</table>
						<input type="submit" name="submit" value="Add the gift" />
					</form>
				</div>
			</div>
		{/if}
	{/if}
	<div class="action_element">
		-&nbsp;<a href="{$webDir}/list/pdf/list_{$list->slug}" class="list_action_link"><img alt="PDF" class="icon_text" src="{$themeWebDir}/img/pdf.png" />&nbsp;Display the list as PDF</a>
	</div>
</div>
<script type="text/javascript">
{include file='list_translation_strings.tpl'}
bwURL = '{$webDir}';
{literal}
$(document).ready(function(){
	$( "#cat_confirm_delete_dialog" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true
	});
	$('input[type="submit"]').button();
	giftDetailsDialog = $('#gift_details_dialog').dialog({autoOpen: false, title: bwLng.details});
});
{/literal}
</script>