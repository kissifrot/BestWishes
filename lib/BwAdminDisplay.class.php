<?php
/**
 * Display management class, admin version
 */
class BwAdminDisplay extends BwDisplay
{
	public function __construct($theme = null)
	{
		global $bwThemeDir;

		parent::__construct($theme);
		$this->setTemplateDir(array(
			$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS . 'adm' . DS,
			$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS . 'common' . DS
		));
		$this->siteName .= ' Admin';
		$this->assign('sessionOk', true);
		$this->assign('users', BwUser::getAll());
	}

	/**
	 * List translated strings
	 */
	public function assignListStrings()
	{
		$this->assign('lngConfirmListDeletion', _('Are you sure you want to delete this list?'));
		$this->assign('lngDeleteIt', _('Delete it'));
		$this->assign('lngCancel', _('Cancel'));
		$this->assign('lngConfirmation', _('Confirmation'));
		$this->assign('lngErrorFormFields', _('You must correctly fill all the form fields'));
	}

	/**
	 * User translated strings
	 */
	public function assignUserStrings()
	{
		$this->assign('lngConfirmUserDeletion', _('Are you sure you want to delete this user? His/her belonging list(s) will be deleted too'));
		$this->assign('lngDeleteIt', _('Delete it'));
		$this->assign('lngCancel', _('Cancel'));
		$this->assign('lngConfirmation', _('Confirmation'));
		$this->assign('lngErrorFormFields', _('You must correctly fill all the form fields'));
	}
}