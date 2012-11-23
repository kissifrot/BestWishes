<?php
/**
 * Display management class
 */
require_once($GLOBALS['bwVendorDir'] . DS . 'smarty' . DS . 'Smarty.class.php');

class BwDisplay extends Smarty
{
	protected $siteName;
	protected $theme = 'default';
	protected $themeWebDir = '';
	
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
		$this->themeWebDir = $bwURL . '/theme/' . $this->theme;

		parent::__construct(); // Call Smarty's constructor
		$this->setTemplateDir(array(
			$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS,
			$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS . 'common' . DS
		));
		// Detect a mobile device
		$this->adaptMobile();

		$this->setCompileDir($bwThemeDir . DS . $this->theme . DS . 'tpl_c' . DS);
		$this->setConfigDir($bwThemeDir . DS . $this->theme . DS . 'configs' . DS);
		$this->setCacheDir($bwThemeDir . DS . $this->theme . DS . 'cache' . DS);
		// $this->setCaching(Smarty::CACHING_LIFETIME_CURRENT);

		$this->assign('theme', $this->theme);
		$this->assign('pageViewed', 'any');
		$this->assign('webDir', $bwURL);
		$this->assign('themeWebDir', $this->themeWebDir);
		$this->assign('sessionOk', false);
		$this->assign('lists', BwList::getAll());

		// Translated strings
		
		$this->assign('lngHomeLogin', _('Home/Login'));
		$this->assign('lngHome', _('Home'));
		$this->assign('lngBwDesciption', _('Management/visualization of wishlists'));
		$this->assign('lngLists', _('Lists:'));
		$this->assign('lngOptions', _('Options'));
		$this->assign('lngLogout', _('Logout'));
		$this->assign('lngChangeOptions', _('Change your options'));
		$this->assign('lngDateFormat', _('m/d/Y'));
		$this->assign('lngPleaseWait', _('Please wait...'));
		$this->assign('lngConfirm', _('Confirm'));
		$this->assign('lngCouldNotLoadTab', _('Could not load this tab'));
		$this->assign('lngPassword', _('Password'));
		$this->assign('lngConfirmation', _('Confirmation'));
		$this->assign('lngCancel', _('Cancel'));
	}

	/**
	 * Try to detect a mobile device and adapt the template
	 */
	private function adaptMobile()
	{
		global $bwVendorDir, $bwThemeDir;

		require_once($bwVendorDir . DS . 'Mobile_Detect' . DS . 'Mobile_Detect.php');
		$detect = new Mobile_Detect();
		$isMobile = $detect->isMobile();
		if($isMobile) {
			$this->setTemplateDir(array(
				$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS . 'mobile' . DS,
				$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS,
				$bwThemeDir . DS . $this->theme . DS . 'tpl' . DS . 'common' . DS
			));
		}
	}

	/**
	 * List translated strings
	 */
	public function assignListStrings()
	{
		global $themeWebDir;

		$this->assign('lngPossibleActions', _('Possible actions:'));
		$this->assign('lngInfoEmptyList', _('(This list is still empty)'));
		$this->assign('lngDetails', _('Details'));
		$this->assign('lngDelete', _('Delete'));
		$this->assign('lngAdd', _('Add'));
		$this->assign('lngNewGift', _('New gift'));
		$this->assign('lngEdit', _('Edit'));
		$this->assign('lngDeleteCategory', _('Delete category'));
		$this->assign('lngMarkAsBought', _('Mark as bought'));
		$this->assign('lngMarkAsReceived', _('Mark as received'));
		$this->assign('lngCannotEditGift', _('You cannot edit this gift name'));
		$this->assign('lngEditGift', _('Edit this gift name'));
		$this->assign('lngCannotEdit', _('You cannot edit this gift name'));
		$this->assign('lngMaxEditsReached', _('The max edits count was reached for this gift, you cannot edit it anymore'));
		$this->assign('lngCatNameTooShort', _('The category name is too short'));
		$this->assign('lngGiftNameTooShort', _('The gift name is too short'));
		$this->assign('lngConfirmGiftDeletion', _('Are you sure you want to delete this gift? It cannot be undone'));
		$this->assign('lngConfirmCategoryDeletion', _('Deleting this category will delete all its gifts too. Are you sure?'));
		$this->assign('lngAddAnyway', _('Add anyway'));
		$this->assign('lngDeleteIt', _('Delete it'));
		$this->assign('lngPurchaseConfirmation', _('Purchase confirmation'));
		$this->assign('lngConfirmPurchase', _('Confirm purchase'));
		$this->assign('lngHasComment', _('has comment'));
		$this->assign('lngIsSurprise', _('is surprise'));
		$this->assign('lngIsBought', _('is bought'));
		$this->assign('lngTipsP', _('Tips:'));
		$this->assign('lngCommentOptionalP', _('Comment <em>(optional)</em>:'));
		$this->assign('lngGiftP', _('Gift:'));
		$this->assign('lngPurchaseInformation', _('Purchase information'));
		$this->assign('lngConfirmGiftReceive', _('Are you sure you want to mark this gift as received? The gift won\'t be visible after'));
		$this->assign('lngAddedOnP', _('Added on:'));
		$this->assign('lngPurchaseDateP', _('Purchase date:'));
		$this->assign('lngPurchaseInfo', _('Purchase info'));
		$this->assign('lngBoughtByP', _('Bought by:'));
		$this->assign('lngOnP', _('On:'));
		$this->assign('lngCommentP', _('Comment:'));
		$this->assign('lngGiftNameP', _('Gift name:'));
		$this->assign('lngCategoryNameP', _('Category name:'));
		$this->assign('lngGiftCategoryP', _('Gift category:'));
		$this->assign('lngSameMoveCategory', _('Source and destination categories are the same!'));
		$this->assign('lngBackToList', _('Go back to the list'));
		$this->assign('lngAddGift', _('Add the gift'));
		$this->assign('lngAddCategory', _('Add the category'));

		// Explanation texts
		$this->assign('lngAddCatExplanation', _('To add a category:<br />-&nbsp;Fill its name<br />-&nbsp;Click on the &#8220;Add the category&#8221; button below'));
		$this->assign('lngAddGiftExplanation', _('To add a gift:<br />-&nbsp;Fill its name<br />-&nbsp;Choose its category<br />-&nbsp;If it does not exist, create it using &#8220;Add a category to the list&#8221; above<br />-&nbsp;Click on the &#8220;Add the gift&#8221; just below'));
		$this->assign('lngDeleteGiftExplanation', sprintf(_('To delete a gift, click on the %s icon next to it.'),
			'<img class="icon_text" src="' . $this->themeWebDir . '/img/delete.png" alt="Del" />'));
		$this->assign('lngDeleteCatExplanation', sprintf(_('To delete a category, click on the %s icon next to it.<br />%s Deleting a category will delete all its gifts.'),
			'<img class="icon_text" src="' . $this->themeWebDir . '/img/delete.png" alt="Del" />', '<img class="icon_text" src="' . $this->themeWebDir . '/img/exclamation.png" alt="!" />'));
		$this->assign('lngAddSurpriseGiftExplanation', _('To add a surprise gift:<br />-&nbsp;Fill its name<br />-&nbsp;Choose its category<br />-&nbsp;Click on the &#8220;Add the gift&#8221; just below'));
		$limitedEdition = $this->getTemplateVars('cfgMaxEdits');
		if(!empty($limitedEdition)) {
			$this->assign('lngEditExplanation', sprintf(_('To edit a gift name, click on the %s icon to the left of it.<br />Once you have finished the edition, click anywhere in the page or use Enter key to validate the modification(s).'),
				'<img class="icon_text" src="' . $this->themeWebDir . '/img/edit.png" alt="' . _('Edit') . '" />'));
		} else {
			$this->assign('lngEditExplanation', sprintf(_('To edit a gift name, click on the %s icon to the left of it.<br />Once you have finished the edition, click anywhere in the page or use Enter key to validate the modification(s).<br />%s The modifications allowed are limited.'),
				'<img class="icon_text" src="' . $this->themeWebDir . '/img/edit.png" alt="' . _('Edit') . '" />', '<img class="icon_text" src="' . $this->themeWebDir . '/img/exclamation.png" alt="!" />'));
		}
		$this->assign('lngShowDetailsExplanation', _('To show a gift\'s details, such as the addition date, purchase info (if applicable), ... Just double-click on its name.'));

		// Possible actions
		$this->assign('lngActnAddCategory', _('Add a category to the list'));
		$this->assign('lngActnAddGift', _('Add a gift (in an already existing category) to the list'));
		$this->assign('lngActnEditGift', _('Edit a gift\'s name'));
		$this->assign('lngActnDelGift', _('Delete a gift'));
		$this->assign('lngActnDelCategory', _('Delete a category'));
		$this->assign('lngActnAddSurpriseGift', _('Add a surprise gift (in an already existing category) to the list'));
		$this->assign('lngActnShowDetails', _('Show a gift\'s details'));
		$this->assign('lngActnDisplayPDF', _('Display the list as PDF'));
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