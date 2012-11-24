{foreach $list->getCategories() as $category}
	{if $category->giftsCount > 0}
			<div id="cat_{$category->getId()}" class="category_list_element">
				{include file='cat_display.tpl'}
			</div>
	{else}
		{if $sessionOk && $user->canEditList($list->getId())}
			<div id="cat_{$category->getId()}" class="category_list_element">
				<a href="/" onclick="deleteCat({$list->getId()}, {$category->getId()}); return false" title="{$lngDelete}"><img alt="{$lngDelete}" class="icon_text" src="{$themeWebDir}/img/delete.png" /></a> 
				<span id="category_name_{$category->getId()}" class="category_name">{$category->name|ucfirst}</span> :
			</div>
		{/if}
	{/if}
<script type="text/javascript">
{literal}
$(document).ready(function(){
	{/literal}
	{if $sessionOk && ($user->isListOwner($list) || $user->canEditList($list->getId()))}
	{literal}
	$('.gift_name').disableSelection();
	$('.gift_name').draggable({
		/*helper: 'clone',*/
		helper: function(event) {
			return $(this).clone().addClass('selectedGift');
		},
		revert: 'invalid'
	});
	$('.category_name').droppable({
		accept: '.gift_name',
		hoverClass: 'selectedCat',
		drop: function(event, ui) {
			var draggableCat = ui.draggable.parent().parent().find('.category_name');
			var draggableCatObjId = draggableCat.get(0).id;
			var targetCatId = this.id.substring(14);
			var draggedGiftId = ui.draggable.get(0).id.substring(9);
			if(draggableCatObjId == this.id) {
				showFlashMessage('error', bwLng.sameMoveCategory);
			} else {
				moveGift(draggedGiftId, pageListId, targetCatId);
			}
		}
	});
	{/literal}
	{/if}
	{literal}
});
{/literal}
</script>
{foreachelse}
	<i>{$lngInfoEmptyList}</i>
{/foreach}