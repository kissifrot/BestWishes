{foreach from=$list->getCategories() item=category}
	{if $category->giftsCount > 0}
			<div id="cat_{$category->getId()}" class="category_list_element">
				{if $sessionOk && $user->canDoActionForList($list->getId(), 'edit')}
					<a href="/" onclick="deleteCat(); return false" title="{$lngDelete}"><img alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" /></a> 
				{/if}
				{$category->name|ucfirst} :
			</div>
		{foreach from=$category->getGifts() item=gift}
			<div id="gift_list_elem_{$gift->getId()}" class="gift_list_element">
				{if $sessionOk}
					{if $user->canDoActionForList($list->getId(), 'edit')}
					<a href="/" onclick="deleteGift(); return false" title="{$lngDelete}"><img alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" /></a>&nbsp;
					{/if}
					<span id="gif_name_{$gift->getId()}" class="gift_name" onclick="showGiftDetailsWindow({$gift|json_encode|escape})">{$gift->name|ucfirst}</span>
					{if $user->canDoActionForList($list->getId(), 'mark') && !$gift->isBought}
					&nbsp;<a href="/" onclick="margGiftAsBought(); return false" title="{$lngMarkAsBought}"><img alt="{$lngMarkAsBought}" src="{$themeWebDir}/img/money.png" /></a> 
					{/if}
					{if $user->isListOwner($list) && !$gift->isReceived}
					&nbsp;<a href="/" onclick="margGiftAsReceived(); return false" title="{$lngMarkAsReceived}"><img alt="{$lngMarkAsReceived}" src="{$themeWebDir}/img/gift.png" /></a> 
					{/if}
				{else}
					<span id="gif_name_{$gift->getId()}" class="gift_name" onclick="showGiftDetailsWindow({$gift|json_encode|escape})">{$gift->name|ucfirst}</span>
				{/if}
			</div>
		{/foreach}
	{else}
		{if $sessionOk && $user->canDoActionForList($list->getId(), 'edit')}
			<div id="cat_{$category->getId()}" class="category_list_element">
				<a href="/" onclick="deleteCat(); return false" title="{$lngDelete}"><img alt="{$lngDelete}" src="{$themeWebDir}/img/delete.png" /></a> 
				{$category->name|ucfirst} :
			</div>
		{/if}
	{/if}
{foreachelse}
	<i>{$lngInfoEmptyList}</i>
{/foreach}
{if $sessionOk}
{/if}