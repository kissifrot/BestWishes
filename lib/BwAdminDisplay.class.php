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
		$this->setTemplateDir($bwThemeDir . DS . $this->theme . DS . 'tpl' . DS . 'adm' . DS);
		
		$this->siteName .= ' Admin';
	}
}