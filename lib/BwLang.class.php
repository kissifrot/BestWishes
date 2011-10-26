<?php
class BwLang
{
	private $currentLanguage = '';
	public function __construct($lang = '')
	{
		// Import params from config file
		global $bwLang, $bwVendorDir;

		if(!empty($lang))
			$this->currentLanguage = $lang;
		else
			$this->currentLanguage = BwConfig::get('default_langage', 'en');

		require_once($bwVendorDir . DS . 'php-gettext' . DS . 'gettext.inc');
	}
	
	public static function load()
	{
		$lang = new self();
		$lang->setupLocale();
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
		_setlocale(LC_MESSAGES, $this->currentLanguage);
		_bindtextdomain('bestwishes', $bwLocaleDir);
		_bind_textdomain_codeset('bestwishes', 'UTF-8');
		_textdomain('bestwishes');

		return true;
	}
}