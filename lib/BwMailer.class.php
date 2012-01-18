<?php
/**
 * Mails management class
 */
class BwMailer
{
	private $transport;
	public $mailFrom;
	
	private $templateData;

	public function __construct()
	{
		// Load Swifft Mailer autoloader
		require_once $GLOBALS['bwVendorDir'] . '/swift/swift_required.php';
		// Configure the transport class
		$transportType = BwConfig::get('mail_transport_type', 'mail');
		switch($transportType) {
			case 'smtp':
				// Send mails using a SMTP server
			break;
			case 'sendmail':
				// Send mails using a locally installed MTA
				$this->transport = Swift_SendmailTransport::newInstance();
			break;
			case 'mail':
				// Use PHP mail()'s function
				$this->transport = Swift_MailTransport::newInstance();
			break;
			default:
				throw new Exception(_('No mailer or incompatiuble one'));
			break;
		}
		$this->mailFrom = bwConfig::get('mail_from', false);
	}

	/**
	 *
	 */
	public function setTemplate($templateFilename = '', $theme = 'default') {
		global $bwThemeDir;

		if(empty($templateFilename) || empty($theme)) {
			return false;
		}

		// Add the template file extension if not specified
		if(stristr($templateFilename, '.tpl') === false) {
			$templateFilename .= '.tpl';
		}

		$templatePath = $bwThemeDir . DS . $theme . DS . 'tpl' . DS . 'mails' . DS . $templateFilename;
		if(!is_file($templatePath)) {
			return false;
		}
		$this->templateData = @file_get_contents($templatePath);
		return true;
	}

	/**
	 *
	 */
	public function populateTemplate($variables) {
		if(empty($this->templateData)) {
			return false;
		}

		$replacedData = $this->templateData;
		foreach($variables as $toReplace => $replace) {
			$replacedData = mb_ereg_replace($toReplace , $replace, $replacedData);
		}

		return $replacedData;
	}

	/**
	 *
	 */
	public function sendMessages($messagesToSend = array()) {
		if(empty($messagesToSend)) {
			return false;
		}

		$messagesSent = 0;
		foreach($messagesToSend as $messageToSend) {
			try {
				$messagesSent += $mailer->send($messageToSend);
			} catch(Exception $e) {
				// TODO : log the error
			}
		}
		
		return $messagesSent;
	}

	/**
	 *
	 */
	public static function sendSurpriseAddAlert($addingUser, $addingList, $categoryName, $giftName)
	{
		global $bwLang, $bwURL;

		try {
			$bwMailer = new self();
			if(empty($bwMailer->mailFrom)) {
				return false;
			}
		} catch(Exception $e) {
			// TODO: log this
			return false;
		}

		if(!$bwMailer->setTemplate('alert_sadd_' . $bwLang)) {
			return false;
		}

		// Now cycle through all users to send them an alert
		$allUsers = BwUser::getAll();
		$messagesToSend = array();

		$variables = array(
			'__CATEGORY_NAME__' => $categoryName,
			'__LIST_NAME__' => $addingList->name,
			'__BW_URL__' => $bwURL,
			'__BW_LIST_URL__' => $bwURL . '/list/' . $addingList->slug,
			'__GIFT_NAME__' => $giftName,
			'__ADDING_USER_NAME__' => $addingUser->name
		);
		foreach($allUsers as $anUser) {
			if(!$anUser->isListOwner($addingList) && $anUser->hasAddAlertForList($addingList->getId())) {
				$variables['__USER_NAME__'] = $anUser->name;

				// Prepare the mail data
				$mailSubject = sprintf(_('Addition of a surprise gift to the list %s'), $addingList->name)
				$mailHtmlContent = $bwMailer->populateTemplate($variables);
				$mailTextContent = strip_tags($mailHtmlContent);

				$messagesToSend[] = Swift_Message::newInstance()
				->setSubject($mailSubject)
				->setFrom($bwMailer->mailFrom)
				->setTo($anUser->email)
				->setBody($mailHtmlContent, 'text/html');
				->addPart($mailTextContent, 'text/plain');
			}
		}
		
		$nbMessages = $mailer->send($message);
		
		return ($nbMessages == count($messagesToSend));
	}

	/**
	 *
	 */
	public static function sendAddAlert($addingUser, $addingList, $categoryName, $giftName)
	{
		global $bwLang, $bwURL;

		try {
			$bwMailer = new self();
			if(empty($bwMailer->mailFrom)) {
				return false;
			}
		} catch(Exception $e) {
			// TODO: log this
			return false;
		}

		if(!$bwMailer->setTemplate('alert_add_' . $bwLang)) {
			return false;
		}

		// Now cycle through all users to send them an alert
		$allUsers = BwUser::getAll();
		$messagesToSend = array();

		$variables = array(
			'__CATEGORY_NAME__' => $categoryName,
			'__BW_URL__' => $bwURL,
			'__BW_LIST_URL__' => $bwURL . '/list/' . $addingList->slug,
			'__GIFT_NAME__' => $giftName,
			'__ADDING_USER_NAME__' => $addingUser->name
		);
		foreach($allUsers as $anUser) {
			if(!$anUser->isListOwner($addingList) && $anUser->hasAddAlertForList($addingList->getId())) {
				$variables['__USER_NAME__'] = $anUser->name;

				// Prepare the mail data
				$mailSubject = sprintf(_('Addition of a gift to the list %s'), $addingList->name)
				$mailHtmlContent = $bwMailer->populateTemplate($variables);
				$mailTextContent = strip_tags($mailHtmlContent);

				$messagesToSend[] = Swift_Message::newInstance()
				->setSubject($mailSubject)
				->setFrom($bwMailer->mailFrom)
				->setTo($anUser->email)
				->setBody($mailHtmlContent, 'text/html');
				->addPart($mailTextContent, 'text/plain');
			}
		}
		
		$nbMessages = $mailer->send($message);
		
		return ($nbMessages == count($messagesToSend));
	}


	/**
	 *
	 */
	public static function sendPurchaseAlert($buyingUser, $buyingList, $giftName, $purchaseComment = '', $isSurprise = false)
	{
		global $bwLang, $bwURL;

		try {
			$bwMailer = new self();
			if(empty($bwMailer->mailFrom)) {
				return false;
			}
		} catch(Exception $e) {
			// TODO: log this
			return false;
		}

		if(!$bwMailer->setTemplate('alert_buy_' . $bwLang)) {
			return false;
		}

		// Now cycle through all users to send them an alert
		$allUsers = BwUser::getAll();
		$messagesToSend = array();
		$purchaseComment = trim($purchaseComment);
		if($isSurprise) {
			$giftName = sprintf(_('surprise gift %s', $giftName));
		} else {
			$giftName = sprintf(_('gift %s', $giftName));
		}
		$variables = array(
			'__BUYER_NAME__' => $buyingUser->name,
			'__LIST_NAME__' => $addingList->name,
			'__BW_URL__' => $bwURL,
			'__BW_LIST_URL__' => $bwURL . '/list/' . $addingList->slug,
			'__GIFT_COMMENT__' => $purchaseComment,
			'__GIFT_FULL_NAME__' => $giftName,
		);
		if(!empty($purchaseComment)) {
			$variables['__GIFT_COMMENT__'] = sprintf(_('He/she left the following comment: %s<br />'), striptags($purchaseComment));
		}

		foreach($allUsers as $anUser) {
			if(!$anUser->isListOwner($addingList) && $anUser->hasAddAlertForList($addingList->getId())) {
				if($anUser->getId() != $buyingUser->getId()) {
					$variables['__USER_NAME__'] = $anUser->name;
					// Prepare the mail data
					$mailSubject = sprintf(_('Purchase of a gift to the list %s'), $addingList->name)
					$mailHtmlContent = $bwMailer->populateTemplate($variables);
					$mailTextContent = strip_tags($mailHtmlContent);

					$messagesToSend[] = Swift_Message::newInstance()
					->setSubject($mailSubject)
					->setFrom($bwMailer->mailFrom)
					->setTo($anUser->email)
					->setBody($mailHtmlContent, 'text/html');
					->addPart($mailTextContent, 'text/plain');
				}
			}
		}
		
		$nbMessages = $mailer->send($message);
		
		return ($nbMessages == count($messagesToSend));
	}
}