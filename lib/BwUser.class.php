<?php
class BwUser
{
	private $id;

	public $username;
	public $name;
	public $email;
	public $lastLogin;
	public $theme;

	protected static $instance;

	public function __construct($id = null)
	{
		if(!empty($id)) {
			$this->id = (int)$id;
			$this->load($this->id);
		}
	}

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self($_SESSION['user_id']);
		}
		return self::$instance;
	}

	public function login($username = '', $password = '')
	{
		$usernameTrim = trim($username);
		$passwordTrim = trim($password);

		if(empty($usernameTrim) || empty($passwordTrim)) {
			return false;
		}

		if($this->loadByUsernamePassword($usernameTrim, $passwordTrim)) {
			// We can mark the user as connected
			$this->setupSession();
			return true;
		}

		return false;
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = $id;
		if(empty($this->id))
			return false;
		
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list_user',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$result = $db->fetch();
			$db->closeQuery();
			if($result === false)
				return $result;
			
			if(empty($result)) {
				return false;
			}

			$this->storeAttributes($this, $result);
			return true;
		}
	}

	private function loadByUsernamePassword($username = '', $password = '')
	{
		if(empty($username) || empty($password)) {
			return false;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list_user',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => 'username = :username',
			'queryValues' => array(
				array(
					'parameter' => ':username',
					'variable' => $username,
					'data_type' => PDO::PARAM_INT
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$result = $db->fetch();
			$db->closeQuery();
			if($result === false)
				return $result;
			
			if(empty($result)) {
				return false;
			}

			// Now compare the password
			$hashedGivenPwd = sha1($result['salt'] . $password . '/' . $result['salt']);
			if($hashedGivenPwd === $result['password']) {
				$this->storeAttributes($this, $result);
				return true;
			}
			return false;
		}
	}

	private function storeAttributes($elem, $sqlResult)
	{
		$elem->id        = $sqlResult['id'];
		$elem->name      = $sqlResult['name'];
		$elem->username  = $sqlResult['username'];
		$elem->email     = $sqlResult['email'];
		$elem->lastLogin = $sqlResult['last_login'];
		// Load theme info
		$theme = new BwTheme();
		if($theme->load($sqlResult['theme_id'])) {
			$elem->theme = $theme;
		} else {
			if($theme->loadDefault()) {
				$elem->theme = $theme;
			} else {
				$elem->theme = '';
				// TODO: Theme error
			}
		}
	}

	private function setupSession()
	{
		$_SESSION['user_id']      = $this->id;
		$_SESSION['identif']      = sha1($this->id . '|' . $_SERVER['HTTP_USER_AGENT']);
		$_SESSION['identif_serv'] = sha1($_SERVER['SERVER_NAME']);
	}

	/**
	 * Checks if an user is logged in
	 */
	public static function checkSession()
	{
		if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || !isset($_SESSION['identif']) || empty($_SESSION['identif']))
		{
			return false;
		}
		else
		{
			$idSessionUser = $_SESSION['user_id'];
			$sessionIdendif = $_SESSION['identif'];
			if(sha1($idSessionUser . '|' . $_SERVER['HTTP_USER_AGENT']) === $sessionIdendif)
			{
				// Check the server we're on
				if($_SERVER['HTTP_HOST'] != 'localhost')
				{
					if($_SESSION['identif_serv'] === sha1($_SERVER['SERVER_NAME']))
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

	public function getTheme()
	{
		return $this->theme;
	}
}