function showGiftDetailsWindow(giftDetails)
{
	if(typeof giftDetails !== 'undefined') {
		console.log(giftDetails);
		$('#gift_details_name').text(giftDetails.name);
		if(giftDetails.isBought) {
			$('#gift_details_buy').show();
			$('#gift_details_buy_who').text(giftDetails.boughtBy);
			if(giftDetails.boughtDate != null) {
				$('#gift_details_buy_date').text(date(lngDateFormat, strtotime(giftDetails.boughtDate)));
			}
			if(typeof giftDetails.boughtComment !== 'undefined' && giftDetails.boughtComment != null && giftDetails.boughtComment.length > 0) {
				$('#gift_details_buy_comment_text').text(giftDetails.boughtComment);
			} else {
				$('#gift_details_buy_comment').hide();
			}
		} else {
			$('#gift_details_buy').hide();
		}
		giftDetailsDialog.dialog('open');
	}
}