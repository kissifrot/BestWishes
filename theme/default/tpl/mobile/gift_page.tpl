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

<a href="{$webDir}/list/{$list->slug}" data-role="button" data-icon="arrow-l">{$lngBackToList}</a> 