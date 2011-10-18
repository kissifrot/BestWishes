{foreach from=$list->getCategories() item=category}
	{if $category->giftsCount > 0}
		<strong>{$category->name|ucfirst} :</strong><br />
		{foreach from=$category->getGifts() item=gift}
			- {$gift->name|ucfirst}<br />
		{/foreach}
	{/if}
{foreachelse}
	<em>(This list is still empty)</em>
{/foreach}