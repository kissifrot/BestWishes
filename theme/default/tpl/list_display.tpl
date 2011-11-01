{foreach from=$list->getCategories() item=category}
	{if $category->giftsCount > 0}
			<div id="cat_{$category->getId()}">
			{if $sessionOk && $user->getParamsByListId($list->getId())->canEdit}
			<a href="/" onclick="supprimerCat({$category->giftsCount}, {$category->getId()}, {$list->getid()}); return false" title="Supprimer"><img alt="Supprimer" src="{$themeWebDir}/img/delete.png" /></a> 
			{/if}
			{$category->name|ucfirst} :
			</div>
		{foreach from=$category->getGifts() item=gift}
			<div id="gift_list_elem_{$gift->getId()}">
				{if $sessionOk}
				<span id="gif_name_{$gift->getId()}" class="gift_name" onclick="showGiftDetailsWindow({$gift|json_encode|escape})">{$gift->name|ucfirst}</span>
				{else}
				<span id="gif_name_{$gift->getId()}" class="gift_name" onclick="showGiftDetailsWindow({$gift|json_encode|escape})">{$gift->name|ucfirst}</span>
				{/if}
			</div>
		{/foreach}
	{else}
		{if $sessionOk && $user->getParamsByListId($list->getId())->canEdit}
			<a href="/" onclick="supprimerCat({$category->giftsCount}, {$category->getId()}, {$list->getid()}); return false" title="Supprimer"><img alt="Supprimer" src="{$themeWebDir}/img/delete.png" /></a> 
			{$category->name|ucfirst} :
		{/if}
	{/if}
{foreachelse}
	<i>{$lngInfoEmptyList}</i>
{/foreach}
{if $sessionOk}
{/if}