<div data-role="popup" id="popupPurchaseGift" data-dismissible="false">	<div data-role="header">		<h1>{$lngPurchaseConfirmation}</h1>	</div>	<div data-role="content">		<form id="frm_buy_gift" name="frm_buy_gift" method="post" onsubmit="return false">			<table border="0" width="470" cellpadding="5" class="formTable">				<tr><td align="center" colspan="2">&nbsp;&nbsp;<strong>{$lngPurchaseInformation}</strong>&nbsp;&nbsp;</td></tr>				<tr><td>&nbsp;&nbsp;{$lngGiftP}&nbsp;</td><td>{$gift->name}</td></tr>				<tr><td>&nbsp;&nbsp;{$lngPurchaseDateP}&nbsp;</td><td>{$smarty.now|date_format:"%d/%m/%y"}</td></tr>				<tr><td>&nbsp;&nbsp;{$lngCommentOptionalP}&nbsp;</td><td>				<textarea id="purchase_comment" name="purchase_comment" cols="25" rows="5"></textarea>				</td></tr>			</table>			<a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="b">{$lngCancel}</a>			<a href="{$webDir}/gift/show/{$list->slug}/{$gift->getId()}" onclick="mobileMarkGiftAsBought();" data-role="button" data-inline="true" data-transition="flow" data-theme="b">{$lngConfirmPurchase}</a>		</form>	</div></div>