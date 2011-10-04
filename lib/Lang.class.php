<?php
class Lang
{
	private $currentLanguage = '';
	public function __construct($lang = '')
	{
		// Import params from config file
		global $bwDS, $bwLang, $bwVendorDir;

		if(!empty($lang))
			$this->currentLanguage = $lang;
		else
			$this->currentLanguage = $bwLang;

		require_once($bwVendorDir . $bwDS . 'php-gettext' . $bwDS . 'gettext.inc');
	}

	/**
	 * Borrowed to phpMyAdmin ;)
	 */
	public function getLangList()
	{
		global $bwDS, $bwLocaleDir;
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
			if ($file != "." && $file != ".." && file_exists($bwLocaleDir . $bwDS . $file . $bwDS . 'LC_MESSAGES' . $bwDS . 'bestwishes.mo')) {
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
}