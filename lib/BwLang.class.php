<?php
class BwLang
{
	private $currentLanguage = '';
	private $systemLanguage  = '';

	public function __construct($lang = '')
	{
		// Import params from config file
		global $bwLang, $bwVendorDir;

		if(!empty($lang))
			$this->currentLanguage = $lang;
		else
			$this->currentLanguage = BwConfig::get('default_langage', 'en');
		$this->systemLanguage = $this->getSystemLocale($this->currentLanguage);

		require_once($bwVendorDir . DS . 'php-gettext' . DS . 'gettext.inc');
	}
	
	public static function load()
	{
		$lang = new self();
		$lang->setupLocale();
	}

	/**
	 * Returns the system locale format
	 */
	private function getSystemLocale($simpleLang = 'en')
	{
		switch ($simpleLang) {
			case 'en':
				return 'en_US';
			case 'fr':
				return 'fr_FR';
		}
	}

	/**
	 * Borrowed to phpMyAdmin ;)
	 */
	public function getLangList()
	{
		global $bwLocaleDir;
		/* We can always speak English */
		$result = array('en' => $this->getLangDetails('en'));

		/* Check for existing directory */
		if (!is_dir($bwLocaleDir)) {
			return $result;
		}

		/* Open the directory */
		$handle = @opendir($bwLocaleDir);
		if ($handle === FALSE) {
			return $result;
		}

		/* Process all files */
		while (FALSE !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && file_exists($bwLocaleDir . DS . $file . DS . 'LC_MESSAGES' . DS . 'bestwishes.mo')) {
				$result[$file] = $this->getLangDetails($file);
			}
		}
		/* Close the handle */
		closedir($handle);

		return $result;
	}

	/**
	 * Borrowed to phpMyAdmin ;)
	 */
	public function getLangDetails($lang) {
		switch ($lang) {
			case 'en':
				return array('en|english', 'en', 'English');
			case 'fr':
				return array('fr|french', 'fr', 'Fran&ccedil;ais');
		}
		return array("$lang|$lang", $lang, $lang);
	}

	public function setupLocale()
	{
		global $bwLocaleDir;

		// Set locale
		T_bind_textdomain_codeset('bestwishes', 'UTF-8');
		T_bindtextdomain('bestwishes', $bwLocaleDir);
		T_textdomain('bestwishes');
		T_setlocale(LC_MESSAGES, $this->systemLanguage);

		return true;
	}
}