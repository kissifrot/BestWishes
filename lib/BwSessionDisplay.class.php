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
		$this->assign('lngPasswordManagement', _('Manage your password'));
		$this->assign('lngCurrentPasswordLabel', _('Current password:'));
		$this->assign('lngNewPasswordLabel', _('New password:'));
		$this->assign('lngNewPasswordRepeatLabel', _('New Password (repeat):'));
		$this->assign('lngUpdateLabel', _('Update'));
		$this->assign('lngListRightsAndAlerts', _('List rights and alerts'));
		$this->assign('lngNoList', _('(no list)'));
		$this->assign('lngListRightsAndAlertsExplanation', _('You can adjust your alerts and see your rights for each list below:'));
		$this->assign('lngListName', _('List name'));
		$this->assign('lngCanView', _('Can view'));
		$this->assign('lngCanEdit', _('Can edit'));
		$this->assign('lngCanMark', _('Can mark'));
		$this->assign('lngAdditionAlert', _('Addition alert'));
		$this->assign('lngPurchaseAlert', _('Purchase alert'));
	}
}