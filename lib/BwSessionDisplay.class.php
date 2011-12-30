<?php
/**
 * Display management class
 */
class BwSessionDisplay extends BwDisplay
{
	private $user;

	public function __construct($theme = null, $user = null)
	{
		parent::__construct($theme);
		
		$this->assign('sessionOk', true);
		$this->assign('user', $user);
		$this->assign('userLastLogin', $_SESSION['last_login']);
	}

	public function assignOptionsStrings()
	{
		$this->assign('lngCurrentPwdNotSpecified', _('You must specify the current password'));
		$this->assign('lngBothRepeatPwdNotMatch', _('You must repeat the password'));
		$this->assign('lngNothingChange', _('Nothing to change !'));
	}
}