				<div class="category_list_element_inner" data-catid="{$category->getId()}" data-empty="false" data-canedit="{if $sessionOk && $user->canEditList($list->getId())}true{else}false{/if}"><span id="category_name_{$category->getId()}" class="category_name">{$category->name|ucfirst}</span> :</div>
				{foreach from=$category->getGifts() item=gift}
					<div id="gift_list_elem_{$gift->getId()}" class="gift_list_element" data-giftid="{$gift->getId()}"
					data-giftname="{$gift->name|escape}"
					data-canmarkbought="{if $sessionOk && $user->canMarkGiftsForList($list->getId()) && !$gift->isBought}true{else}false{/if}"
					data-canmarkreceived="{if $sessionOk && $user->isListOwner($list) && !$gift->isReceived}true{else}false{/if}"
					data-canedit="{if $sessionOk && $user->canEditList($list->getId())}true{else}false{/if}"
					>
						{if $sessionOk}
							{if $userLastLoginTime < strtotime($gift->addedDate)}
								<img alt="{$lngNewGift}" title="{$lngNewGift}" class="icon_text" src="{$themeWebDir}/img/new.png" />
							{/if}
							<span id="gif_name_{$gift->getId()}" class="gift_name{if $gift->isBought && !$user->isListOwner($list)} bought_gift{/if}">{$gift->name|ucfirst}</span>
							{if $gift->isBought && !$user->isListOwner($list)}
								{if !empty($gift->purchaseComment)}
								<img class="icon_text gift_status" alt="comment" title="{$lngHasComment}" src="{$themeWebDir}/img/comment.png" />
								{/if}
								<img class="icon_text gift_status" alt="bought" title="{$lngIsBought}" src="{$themeWebDir}/img/gift_bought.png" />
							{/if}
							{if $gift->isSurprise}
								<img class="icon_text gift_status" alt="surprise" title="{$lngIsSurprise}" src="{$themeWebDir}/img/surprise.png" />
							{/if}
						{else}
							<span id="gif_name_{$gift->getId()}" class="gift_name">{$gift->name|ucfirst}</span>
						{/if}
					</div>
				{/foreach}