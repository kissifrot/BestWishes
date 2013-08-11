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
		<a href="#popupConfirmDelete" data-rel="popup" data-role="button" data-transition="pop" data-icon="delete">{$lngDelete}</a>
	{/if}
	{if $user->canMarkGiftsForList($list->getId()) && !$gift->isBought}
		<a href="#popupPurchaseGift" data-rel="popup" data-role="button" data-transition="pop" >{$lngMarkAsBought}</a> 
	{/if}
	{if $user->isListOwner($list) && !$gift->isReceived}
		<a href="/" onclick="markGiftAsReceived(); return false" title="{$lngMarkAsReceived}" data-rel="dialog" data-role="button">{$lngMarkAsReceived}</a> 
	{/if}
	<div data-role="popup" id="popupConfirmDelete">
		<div data-role="header" data-theme="a" class="ui-corner-top">
			<h1>{$lngConfirmation}</h1>
		</div>
		<div data-role="content" data-theme="d" class="ui-corner-bottom ui-content">
			<h3 class="ui-title">{$lngConfirmGiftDeletion}</h3>
			<a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c">{$lngCancel}</a>
			<a href="{$webDir}/list/{$list->slug}" onclick="deleteGift({$gift->getId()}, {$list->getId()});" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b">{$lngDeleteIt}</a>
		</div>
	</div>
{* Gift purchase indication form *}
{include file='form_purchase_gift.tpl'}

{/if}


<a href="{$webDir}/list/{$list->slug}" data-role="button" data-icon="arrow-l">{$lngBackToList}</a> 

<script type="text/javascript">
{include file='list_translation_strings.tpl'}
bwURL = '{$webDir}';
var pageListId = {$list->getId()};
currentListId = {$list->getId()};
currentGiftId = {$gift->getId()};
</script>