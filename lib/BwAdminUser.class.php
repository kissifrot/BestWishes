<?php
class BwAdminUser
{
	private $username;

	public function login($username = '', $password = '')
	{
		$usernameTrim = trim($username);
		$passwordTrim = trim($password);

		if(empty($usernameTrim) || empty($passwordTrim)) {
			return false;
		}

		if($this->checkByUsernamePassword($usernameTrim, $passwordTrim)) {
			// We can mark the user as connected
			$this->setupSession();
			return true;
		}

		return false;
	}

	private function checkByUsernamePassword($username = '', $password = '')
	{
		if(empty($username) || empty($password)) {
			return false;
		}
		
		$configuredUsername = BwConfig::get('admin_username', '');
		$configuredPassword = BwConfig::get('admin_password', '');
		$configuredEmail    = BwConfig::get('admin_email', '');
		$configuredSalt     = BwConfig::get('admin_pwd_salt', '');
		if(empty($configuredUsername) || empty($configuredPassword) || empty($configuredSalt) || empty($configuredEmail)) {
			// There's a problem in the configuration
			echo 'config problem';
			return false;
		}
		
		$hashedGivenPwd = sha1($password . '/' . $configuredSalt  . '-' . $configuredSalt);
		if($hashedGivenPwd === $configuredPassword && $username === $configuredUsername) {
			$this->username = $username;
			$this->setupSession();
			return true;
		}
	}

	private function setupSession()
	{
		$_SESSION['admin_user']         = $this->username;
		$_SESSION['admin_identif']      = sha1($this->username . '|' . $_SERVER['HTTP_USER_AGENT']);
		$_SESSION['admin_identif_serv'] = sha1($_SERVER['SERVER_NAME']);
	}

	/**
	 * Checks if an user is logged in
	 */
	public static function checkSession()
	{
		if(!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user']) || !isset($_SESSION['admin_identif']) || empty($_SESSION['admin_identif'])) {
			return false;
		} else {
			$idSessionUser = $_SESSION['admin_user'];
			$sessionIdendif = $_SESSION['admin_identif'];
			if(sha1($idSessionUser . '|' . $_SERVER['HTTP_USER_AGENT']) === $sessionIdendif)
			{
				// Check the server we're on
				if($_SERVER['HTTP_HOST'] != 'localhost')
				{
					if($_SESSION['admin_identif_serv'] === sha1($_SERVER['SERVER_NAME']))
						return true;
					else
						return false;
				}
				else
					return true;
			}
			else
				return false;
		}
	}
}