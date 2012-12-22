{foreach $list->getCategories() as $category}
	{if $category->giftsCount > 0}
			<div id="cat_{$category->getId()}" class="category_list_element">
				{include file='cat_display.tpl'}
			</div>
	{else}
		{if $sessionOk && $user->canEditList($list->getId())}
			<div id="cat_{$category->getId()}" class="category_list_element">
				<div class="category_list_element_inner" data-catid="{$category->getId()}" data-empty="true" data-canedit="true"><span id="category_name_{$category->getId()}" class="category_name">{$category->name|ucfirst}</span> :</div>
			</div>
		{/if}
	{/if}
{foreachelse}
	<i>{$lngInfoEmptyList}</i>
{/foreach}