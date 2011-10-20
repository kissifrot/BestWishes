{foreach from=$list->getCategories() item=category}
	{if $category->giftsCount > 0}
		<b>{$category->name|ucfirst} :</b><br />
		{foreach from=$category->getGifts() item=gift}
			- <span id="gif_name_{$gift->getId()}" onclick="showGiftDetailsWindow({$gift|json_encode|escape})">{$gift->name|ucfirst}</span><br />
		{/foreach}
	{/if}
{foreachelse}
	<i>{$lngInfoEmptyList}</i>
{/foreach}