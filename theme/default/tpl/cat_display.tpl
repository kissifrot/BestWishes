				{if $sessionOk && $user->canEditList($list->getId())}
					<a href="/" onclick="confirmDeleteCat({$list->getId()}, {$category->getId()}); return false" title="{$lngDelete}"><img alt="{$lngDelete}" class="icon_text" src="{$themeWebDir}/img/delete.png" /></a> 
				{/if}
				<span id="category_name_{$category->getId()}" class="category_name">{$category->name|ucfirst}</span> :
				{foreach from=$category->getGifts() item=gift}
					<div id="gift_list_elem_{$gift->getId()}" class="gift_list_element">
						{if $sessionOk}
							{if $user->canEditList($list->getId())}
							<a href="/" onclick="confirmDeleteGift({$gift->getId()}, {$list->getId()}); return false" title="{$lngDelete}"><img alt="{$lngDelete}" class="icon_text" src="{$themeWebDir}/img/delete.png" /></a>&nbsp;
								{if $cfgMaxEdits && $gift->editsCount >= $cfgMaxEdits}
									<a href="/" onclick="startEditGift(false); return false" title="{$lngCannotEditGift}"><img alt="{$lngEdit}" class="icon_text" src="{$themeWebDir}/img/edit_not.png" /></a>&nbsp;
								{else}
									<a id="actn_edit_gift_{$gift->getId()}" href="/" onclick="startEditGift(true, '{$gift->name|escape:'javascript'|escape}', {$gift->getId()}, {$list->getId()}); return false" title="{$lngEditGift}"><img alt="{$lngEdit}" class="icon_text" src="{$themeWebDir}/img/edit.png" /></a>&nbsp;
								{/if}
							{else}
								{if $userLastLoginTime < strtotime($gift->addedDate)}
									<img alt="{$lngNewGift}" title="{$lngNewGift}" class="icon_text" src="{$themeWebDir}/img/new.png" />
								{/if}
							{/if}
							<span id="gif_name_{$gift->getId()}" class="gift_name{if $gift->isBought && !$user->isListOwner($list)} bought_gift{/if}" ondblclick="showGiftDetailsWindow({$gift->getId()}, {$list->getId()})">{$gift->name|ucfirst}</span>
							{if $user->canMarkGiftsForList($list->getId()) && !$gift->isBought}
							&nbsp;<a href="/" onclick="showBuyWindow('{$gift->name|escape:'javascript'|escape}', {$gift->getId()}, {$list->getId()}); return false" title="{$lngMarkAsBought}"><img alt="{$lngMarkAsBought}" class="icon_text" src="{$themeWebDir}/img/gift_buy.png" /></a> 
							{/if}
							{if $user->isListOwner($list) && !$gift->isReceived}
							&nbsp;<a href="/" onclick="markGiftAsReceived(); return false" title="{$lngMarkAsReceived}"><img alt="{$lngMarkAsReceived}" class="icon_text" src="{$themeWebDir}/img/gift_received.png" /></a> 
							{/if}
							{if $gift->isBought && !$user->isListOwner($list)}
								{if !empty($gift->boughtComment)}
								<img class="icon_text gift_status" alt="comment" title="has comment" src="{$themeWebDir}/img/comment.png" />
								{/if}
								<img class="icon_text gift_status" alt="bought" title="is bought" src="{$themeWebDir}/img/gift_bought.png" />
							{/if}
							{if $gift->isSurprise}
								<img class="icon_text gift_status" alt="surprise" title="is surprise" src="{$themeWebDir}/img/surprise.png" />
							{/if}
						{else}
							<span id="gif_name_{$gift->getId()}" class="gift_name" ondblclick="showGiftDetailsWindow({$gift->getId()}, {$list->getId()})">{$gift->name|ucfirst}</span>
						{/if}
					</div>
				{/foreach}