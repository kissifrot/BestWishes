{foreach $list->getCategories() as $category}
	{if $category->giftsCount > 0}
		{include file='cat_display.tpl'}
	{else}
		{if $sessionOk && $user->canEditList($list->getId())}
		<h2>{$category->name|ucfirst}</h2>
		{/if}
	{/if}
{foreachelse}
	<i>{$lngInfoEmptyList}</i>
{/foreach}