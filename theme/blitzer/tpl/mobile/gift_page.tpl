<h2>Gift details</h2>
{$gift->name|ucfirst}
<p>{$lngAddedOnP} {$gift->addedDate|date_format:$lngDateFormat}</p>
{if $gift->isBought}
	<div id="gift_details_buy">
		<h4><b>{$lngPurchaseInfo}</b></h4>
		<p>{$lngBoughtByP} {$gift->boughtByName}</p>
		<p>{$lngPurchaseDateP} {$gift->purchaseDate|date_format:$lngDateFormat}</p>
		{if !empty($gift->purchaseComment)}
		<div id="gift_details_buy_comment">
			<p>{$lngCommentP} {$gift->purchaseComment}</p>
		</div>
		{/if}
	</div>
{/if}
<span id="gift_details_url"></span>
<div id="gift_details_image"></div>

{if $sessionOk}
	{if $user->canEditList($list->getId())}
		<a href="/" onclick="confirmDeleteGift({$gift->getId()}, {$list->getId()}); return false" title="{$lngDelete}" data-rel="dialog" data-role="button">{$lngDelete}</a>
	{/if}
	{if $user->canMarkGiftsForList($list->getId()) && !$gift->isBought}
		<a href="/" onclick="showBuyWindow('{$gift->name|escape:'javascript'|escape}', {$gift->getId()}, {$list->getId()}); return false" title="{$lngMarkAsBought}" data-rel="dialog" data-role="button">$lngMarkAsBought}</a> 
	{/if}
	{if $user->isListOwner($list) && !$gift->isReceived}
		<a href="/" onclick="markGiftAsReceived(); return false" title="{$lngMarkAsReceived}" data-rel="dialog" data-role="button">{$lngMarkAsReceived}</a> 
	{/if}
{/if}

<a href="{$webDir}/list/{$list->slug}" data-role="button" data-icon="arrow-l">{$lngBackToList}</a> 

<script type="text/javascript">
{include file='list_translation_strings.tpl'}
bwURL = '{$webDir}';
var pageListId = {$list->getId()};
</script>