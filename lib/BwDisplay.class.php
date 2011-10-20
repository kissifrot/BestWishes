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
		$this->assign('lngLists', _('Lists:'));
		$this->assign('lngDateFormat', _('m/d/Y'));
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