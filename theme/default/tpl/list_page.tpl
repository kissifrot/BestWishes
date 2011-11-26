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
{if $sessionOk}
	{if $user->canDoActionForList($list->getId(), 'edit')}
		-&nbsp;<a href="/" onclick="$('#frm_add_cat').toggle(); return false"><img alt="{$lngAdd}" src="{$themeWebDir}/img/add.png" />&nbsp;Add a category to the list</a><br />
		<div id="frm_add_cat" style="visibility: visible; display: none;">
			<form id="frm_add_cat" name="frm_add_cat" method="POST" onsubmit="ajouterCategorie({$list->getId()}); return false">
			<table border="0" width="550" cellpadding="5">
				<tr>
					<td align="left" colspan="2"><em><span class="copyright">Pour ajouter une catégorie :<br />-&nbsp;Remplissez son nom<br />-&nbsp;Cliquez sur le bouton &#8220;Ajouter la catégorie&#8221; situé juste en-dessous</span></em>
					</td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;Category name&nbsp;:&nbsp;</td><td>
					<input type="text" id="nom_cat" name="nom_cat" size="25" maxlength="70" />
					</td>
				</tr>
			</table>
			<input type="submit" name="submit" value="Add the category" />
			</form>
			<div id="info_cat">
			</div>
		</div>
		
		-&nbsp;<a href="/" onclick="$('#form_cadeau').toggle(); return false"><img alt="{$lngAdd}" src="{$themeWebDir}/img/add.png" />&nbsp;Ajouter un cadeau (dans une catégorie préexistante) à la liste</a>
		<div id="form_cadeau" style="visibility: visible; display: none;">
			<form id="frm_ajout_cadeau" name="frm_ajout_cadeau" method="POST" onsubmit="ajouterCadeau({$list->getId()}); return false">
				<table border="0" width="550" cellpadding="5">
					<tr>
						<td align="left" colspan="2"><em><span class="copyright">Pour ajouter un cadeau :<br />-&nbsp;Remplissez son nom<br />-&nbsp;Choisissez sa catégorie<br />-&nbsp;Si elle n'existe pas, créez-la en utilisant &#8220;Ajouter une catégorie à la liste&#8221; ci-dessus<br />-&nbsp;Cliquez sur le bouton &#8220;Ajouter le cadeau&#8221; situé juste en-dessous</span></em></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;Nom du cadeau&nbsp;:&nbsp;</td><td>
						<input type="text" id="gift_name" name="gift_name" size="60" maxlength="150" />
						</td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;Catégorie du cadeau&nbsp;:&nbsp;</td><td>
						<div id="div_select_cat">
							<select id="cat_cadeau" name="cat_cadeau">
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
			<div id="info_cadeau">
			</div>
		</div>
		
		{if $list->categoriesCount > 0}
			<br />-&nbsp;<a href="/" onclick="$('#form_modif_cadeau').toggle(); return false"><img alt="{$lngEdit}" src="{$themeWebDir}/img/edit_big.png" />&nbsp;Modifier le nom d'un cadeau</a>
			<div id="form_modif_cadeau" style="visibility: visible; display: none;">
				<table border="0" width="550" cellpadding="5">
					<tr>
						<td><em><span class="copyright">Pour modifier le nom d'un cadeau, cliquez sur l'icône <img src="{$themeWebDir}/img/edit.png" /> à droite de celui-ci.<br />
						Une fois que vous aurez fini la modification, cliquez à nouveau sur l'icône <img src="{$themeWebDir}/img/edit.png" /> ou appuyez sur Entrée pour enregistrer les modifications.
						<br /><img src="{$themeWebDir}/img/exclamation.png" /> Attention, les modifications autorisées sont minimales.</span></em></td>
					</tr>
				</table>
			</div>
			
			<br />-&nbsp;<a href="/" onclick="$('#form_suppr_cadeau').toggle(); return false"><img alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" />&nbsp;Supprimer un cadeau</a>
			<div id="form_suppr_cadeau" style="visibility: visible; display: none;">
				<table border="0" width="550" cellpadding="5">
					<tr>
						<td><em><span class="copyright">Pour supprimer un cadeau, cliquez sur l'icône <img src="{$themeWebDir}/img/delete.png" /> à droite de celui-ci.</span></em></td>
					</tr>
				</table>
			</div>
			
			<br />-&nbsp;<a href="/" onclick="$('#form_suppr_cat').toggle(); return false"><img alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" />&nbsp;Supprimer une catégorie</a>
			<div id="form_suppr_cat" style="visibility: visible; display: none;">
				<table border="0" width="550" cellpadding="5">
					<tr>
						<td><em><span class="copyright">Pour supprimer une catégorie, cliquez sur l'icône <img src="{$themeWebDir}/img/delete.png" /> à gauche de celle-ci.</span></em></td>
					</tr>
				</table>
			</div>
		{/if}
	{else}
		-&nbsp;<a href="/" onclick="$('#form_cadeau').toggle(); return false"><img alt="{$lngAdd}" src="{$themeWebDir}/img/add.png" />&nbsp;Ajouter un cadeau surprise (dans une catégorie préexistante) à la liste</a>
		<div id="form_cadeau" style="visibility: visible; display: none;">
			<form id="frm_ajout_cadeau_surprise" name="frm_ajout_cadeau_surprise" method="POST" onsubmit="ajouterCadeauSurprise({$liste->idListe}); return false">
				<table border="0" width="550" cellpadding="5">
					<tr>
						<td align="left" colspan="2"><em><span class="copyright">Pour ajouter un cadeau :<br />-&nbsp;Remplissez son nom<br />-&nbsp;Choisissez sa catégorie<br />-&nbsp;Cliquez sur le bouton &#8220;Ajouter le cadeau&#8221; situé juste en-dessous</span></em></td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;Nom du cadeau&nbsp;:&nbsp;</td><td>
						<input type="text" id="surprise_gift_name" name="surprise_gift_name" size="60" maxlength="150" />
						</td>
					</tr>
					<tr>
						<td>&nbsp;&nbsp;Catégorie du cadeau&nbsp;:&nbsp;</td><td>
						<div id="div_select_surpise_cat">
							<select id="surprise_gift_cat" name="surprise_gift_cat">
							{foreach from=$list->getCategories() item=category}
								<option value="{$category->getId()}">{$category->name|ucfirst}</option>
							{/foreach}
							</select>
						</div>
						</td>
					</tr>
				</table>
				<input type="submit" name="submit" value="Ajouter le cadeau" />
			</form>
			<div id="info_cadeau_surprise">
			</div>
		</div>
	{/if}
{/if}
<script type="text/javascript">

$(document).ready(function(){ldelim}
	lngDateFormat = '{$lngDateFormat}';
	$('input[type="submit"]').button();
	giftDetailsDialog = $('#gift_details_dialog').dialog({ldelim}autoOpen: false, title: '{$lngDetails}'{rdelim})
{rdelim});
</script>