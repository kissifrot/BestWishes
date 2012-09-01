				<h2>{$category->name|ucfirst}</h2>
				<ul class="ui-listview ui-listview-inset ui-corner-all ui-shadow" data-role="listview" data-inset="true">
				{foreach from=$category->getGifts() item=gift}
					<li data-iconpos="right" data-icon="arrow-r" data-wrapperels="div" data-iconshadow="true" data-shadow="false" data-corners="false"><a href="{$webDir}/gift/{$list->slug}/{$gift->getId()}">{$gift->name|ucfirst}</a></li>
				{/foreach}
				</ul>