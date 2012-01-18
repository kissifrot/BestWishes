<?php
/**
 * Display management class
 */
require_once($GLOBALS['bwVendorDir'] . '/smarty/Smarty.class.php');

class BwDisplay extends Smarty
{
	public $siteName;
	public $theme = 'default';
	
	private $pageTitle = '';

	public function __construct($theme = null)
	{
		global $bwThemeDir, $bwURL;

		$this->siteName = BwConfig::get('site_name', 'BestWishes');
		
		// Check for theme
		if(!empty($theme))
			$this->theme = $theme;
		else
			$this->theme = 'default';
		$this->theme = cleanupVariable('theme', $this->theme);
		if(!is_dir($bwThemeDir . DS . $this->theme)) {
			exit('Theme not found');
		}

		parent::__construct(); // Call Smarty's constructor
		$this->setTemplateDir($bwThemeDir . DS . $this->theme . DS . 'tpl' . DS);
		$this->setCompileDir($bwThemeDir . DS . $this->theme . DS . 'tpl_c' . DS);
		$this->setConfigDir($bwThemeDir . DS . $this->theme . DS . 'configs' . DS);
		$this->setCacheDir($bwThemeDir . DS . $this->theme . DS . 'cache' . DS);
		// $this->setCaching(Smarty::CACHING_LIFETIME_CURRENT);

		$this->assign('theme', $this->theme);
		$this->assign('webDir', $bwURL);
		$this->assign('themeWebDir', $bwURL . '/theme/' . $this->theme);
		$this->assign('sessionOk', false);
		$this->assign('lists', BwList::getAll());

		// Translated strings
		$this->assign('lngHomeLogin', _('Home/Login'));
		$this->assign('lngHome', _('Home'));
		$this->assign('lngLists', _('Lists:'));
		$this->assign('lngOptions', _('Options'));
		$this->assign('lngLogout', _('Logout'));
		$this->assign('lngLogout', _('Logout'));
		$this->assign('lngChangeOptions', _('Change your options'));
		$this->assign('lngDateFormat', _('m/d/Y'));
		$this->assign('lngPleaseWait', _('Please wait...'));
		$this->assign('lngConfirm', _('Confirm'));
		$this->assign('lngCouldNotLoadTab', _('Could not load this tab'));
	}

	public function assignListStrings()
	{
		$this->assign('lngPossibleActions', _('Possible actions:'));
		$this->assign('lngInfoEmptyList', _('(This list is still empty)'));
		$this->assign('lngDetails', _('Details'));
		$this->assign('lngDelete', _('Delete'));
		$this->assign('lngAdd', _('Add'));
		$this->assign('lngEdit', _('Edit'));
		$this->assign('lngDeleteCategory', _('Delete category'));
		$this->assign('lngMarkAsBought', _('Mark as bought'));
		$this->assign('lngMarkAsReceived', _('Mark as received'));
		$this->assign('lngCannotEdit', _('You cannot edit this gift name'));
		$this->assign('lngMaxEditsReached', _('The max edits count was reached for this gift, you cannot edit it anymore'));
		$this->assign('lngCatNameTooShort', _('The category name is too short'));
		$this->assign('lngGiftNameTooShort', _('The gift name is too short'));
		$this->assign('lngConfirmGiftDeletion', _('Are you sure you want to delete this gift? It cannot be undone'));
		$this->assign('lngAddAnyway', _('Add anyway'));
		$this->assign('lngDeleteIt', _('Delete it'));
		$this->assign('lngCancel', _('Cancel'));
		$this->assign('lngConfirmation', _('Confirmation'));
		$this->assign('lngPurchaseConfirmation', _('Purchase confirmation'));
		$this->assign('lngConfirmPurchase', _('Confirm purchase'));
		$this->assign('lngGift', _('Gift'));
		$this->assign('lngPurchaseDate', _('Purchase date'));
		$this->assign('lngComment', _('Comment'));
		$this->assign('lngPurchaseInformation', _('<strong>Purchase information</strong>&nbsp;<em>(optional)</em>'));
		$this->assign('lngConfirmGiftReceive', _('Are you sure you want to mark this gift as received? The gift won\'t be visible after'));
	}

	public function showJSONStatus($status = 'success', $message = '')
	{
		$this->assign('status', $status);
		$this->assign('message', $message);
		$this->display('json_message.tpl');
	}

	public function showJSONData($data = null)
	{
		$this->assign('data', $data);
		$this->display('json_data.tpl');
	}

	public function header($title = '', $subTitle = '')
	{
		if(empty($title)) {
			$this->pageTitle = $this->siteName;
		} else {
			$this->pageTitle = $this->siteName . ' - ' . $title;
		}

		$this->assign('siteName', $this->siteName);
		$this->assign('pageTitle', $this->pageTitle);
		$this->assign('subTitle', $subTitle);

		$this->display('header.tpl', cleanupVariable('default', $title));
	}

	public function footer()
	{
		$this->assign('version', BwVersion::VERSION);
		$this->display('footer.tpl');
	}
}