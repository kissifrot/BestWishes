<?php
class BwUser
{
	private $id;

	public $username;
	public $name;
	public $email;
	public $lastLogin;
	public $theme;
	public $listParams;

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

	public function __toString()
	{
		return $this->name;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getTheme()
	{
		return $this->theme;
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

	public function logout()
	{
		$this->deleteSession();
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
		if(empty($this->id))
			return false;

		// Try to read from the cache
		$result = BwCache::read('user_' . $this->id);
		if($result === false) {
			// Nothing in the cache
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

				$this->storeAttributes($result);
				// Store this in the cache
				BwCache::write('user_' . $this->id, $result);
				return true;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$this->storeAttributes($result);
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
				$this->storeAttributes($result);
				return true;
			}
			return false;
		} else {
			return false;
		}
	}

	public function updatePassword($password = '', $newPassword = '')
	{
		$resultCode = 99;
		if(empty($password) || empty($newPassword)) {
			return $resultCode;
		}

		$minPasswordSize = BwConfig::get('min_password_size', 6);
		if(mb_strlen($password) < $minPasswordSize) {
			$resultCode = 1;
			return $resultCode;
		}

		// Ceck if we have the user/password in the db
		if(!$this->loadByUsernamePassword($this->username, $password)) {
			$resultCode = 2;
			return $resultCode;
		}

		// All seems ok, continue
		$newSalt = sha1(uniqid());
		$hashedPwd = sha1($newSalt . $newPassword . '/' . $newSalt);

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list_user',
			'queryType' => 'UPDATE',
			'queryFields' => array(
				'salt' => ':salt',
				'password' => ':password'
			),
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				array(
					'parameter' => ':salt',
					'variable' => $newSalt,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':password',
					'variable' => $hashedPwd,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$resultExec = $db->exec();
			if($resultExec === false)
				return $resultCode;
			
			// All OK
			$resultCode = 0;
		}
		return $resultCode;
	}

	/**
	 *
	 */
	private function loadAll()
	{
		// Try to read from the cache
		$results = BwCache::read('user_all');
		if($results === false) {
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'gift_list_user',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryCondition' => '',
				'queryValues' => '',
				'queryOrderBy' => 'username ASC',
			);
			if($db->prepareQuery($queryParams)) {
				$results = $db->fetchAll();
				$db->closeQuery();
				if($results === false)
					return $results;

				if(empty($results)) {
					return false;
				}

				$allUsers = array();
				foreach($results as $result) {
					$user = new self((int)$result['id']);
					$user->storeAttributes($result);
					$allUsers[] = $user;
				}

				// Store this in the cache
				BwCache::write('user_all', $results);
				return $allUsers;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allUsers = array();
			foreach($results as $result) {
				$user = new self((int)$result['id']);
				$user->storeAttributes($result);
				$allUsers[] = $user;
			}
			return $allUsers;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->id        = (int)$sqlResult['id'];
		$this->name      = $sqlResult['name'];
		$this->username  = $sqlResult['username'];
		$this->email     = $sqlResult['email'];
		$this->lastLogin = $sqlResult['last_login'];
		// Load theme info
		$theme = new BwTheme();
		if($theme->load($sqlResult['theme_id'])) {
			$this->theme = $theme;
		} else {
			if($theme->loadDefault()) {
				$this->theme = $theme;
			} else {
				$this->theme = '';
				// TODO: Theme error
			}
		}
		$this->listParams = array();
	}

	private function setupSession()
	{
		$_SESSION['user_id']      = (int)$this->id;
		$_SESSION['identif']      = sha1((int)$this->id . '|' . $_SERVER['HTTP_USER_AGENT']);
		$_SESSION['identif_serv'] = sha1($_SERVER['SERVER_NAME']);
		$_SESSION['last_login']   = $this->lastLogin;
	}

	public function updateLastLogin()
	{
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list_user',
			'queryType' => 'UPDATE',
			'queryFields' => array(
				'last_login' => ':last_login'
			),
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				array(
					'parameter' => ':last_login',
					'variable' => date('Y-m-d H:i:s'),
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$resultExec = $db->exec();
			if($resultExec === false)
				return $resultExec;
			
			return true;
		}
		return false;
	}

	/**
	 * Checks if an user is logged in
	 */
	public static function checkSession()
	{
		if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || !isset($_SESSION['identif']) || empty($_SESSION['identif'])) {
			return false;
		} else {
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

	/**
	 *
	 */
	public static function add($username = '', $pwd = '', $name = '', $email = '') {
		$resultValue = 99;
		if(empty($username) || empty($pwd))
			return $resultValue;

		// Check for correct email
		if(!empty($email)) {
			if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				$resultValue = 1;
				return $resultValue;
			}
		}

		// Get the default theme
		$allThemes = BwTheme::getAll();
		$themeId = null;
		foreach($allThemes as $aTheme) {
			if($aTheme->isDefault) {
				$themeId = $aTheme->getId();
				break;
			}
		}
		if(empty($themeId)) {
			$themeId = $allThemes[0]->getId();
		}

		// Check for already existing username
		if(self::checkAnyExisting('username', $username)) {
			$resultValue = 2;
			return $resultValue;
		}

		// Now generate the salt and password
		$salt = sha1(uniqid() . mt_rand(0, 50));
		$hashedPwd = sha1($salt . $pwd . '/' . $salt);

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list_user',
			'queryType' => 'INSERT',
			'queryFields' => array(
				'theme_id' => ':theme_id',
				'name' => ':name',
				'username' => ':username',
				'password' => ':password',
				'salt' => ':salt',
				'email' => ':email'
			),
			'queryValues' => array(
				array(
					'parameter' => ':theme_id',
					'variable' => $themeId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':name',
					'variable' => $name,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':username',
					'variable' => $username,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':password',
					'variable' => $hashedPwd,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':salt',
					'variable' => $salt,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':email',
					'variable' => $email,
					'data_type' => PDO::PARAM_STR
				)
			),
			'queryAutoField' => 'id'
		);
		if($db->prepareQuery($queryParams)) {
			$result = $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('user_all');
				// All OK
				$newUserId = intval($db->lastInsertId());
				//  Add the necessary rights to all users
				$resultValue = BwUserParams::addByUserId($newUserId);
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public static function checkAnyExisting($nameField, $nameValue) {
		$queryParams = array(
			'tableName' => 'gift_list_user',
			'queryType' => 'SELECT',
			'queryFields' => 'COUNT(id) as count_existing',
			'queryCondition' => $nameField . ' = :' . $nameField,
			'queryValues' => array(
				array(
					'parameter' => ':' . $nameField,
					'variable' => $nameValue,
					'data_type' => PDO::PARAM_STR
				)
			),
			'queryLimit' => 1
		);
		$db = BwDatabase::getInstance();
		if($db->prepareQuery($queryParams)) {
			$result = $db->fetch();
			$db->closeQuery();
			if($result === false)
				return $result;

			if(empty($result)) {
				return false;
			}
			return (intval($result['count_existing']) != 0);
		} else {
			return false;
		}
	}

	private function deleteSession()
	{
		$_SESSION['user_id']      = null;
		$_SESSION['identif']      = null;
		$_SESSION['identif_serv'] = null;
		$_SESSION['last_login']   = null;
		unset($_SESSION['user_id'], $_SESSION['identif'], $_SESSION['identif_serv'], $_SESSION['last_login']);
	}

	/**
	 *
	 */
	public static function getAll()
	{
		$user = new self();
		return $user->loadAll();
	}

	public function loadParams()
	{
		$this->listParams = BwUserParams::getAllByUserId($this->id);
	}

	public function updateRight($listId =  null, $rightType = '', $enabled = false)
	{
		$resultCode = 99;
		if(empty($listId) || empty($rightType)) {
			return $resultCode;
		}
		return BwUserParams::updateUserRight($this->id, $listId, $rightType, $enabled);
	}

	private function canDoActionForList($listId = null, $action = 'view')
	{
		if(empty($listId))
			return false;

		$listParams = $this->getParamsByListId($listId);
		if(!$listParams) {
			return false;
		}
		
		switch($action) {
			case 'view':
				return $listParams->canView;
			break;
			case 'mark':
				return $listParams->canMark;
			break;
			case 'edit':
				return $listParams->canEdit;
			break;
			case 'a_add':
				return $listParams->alertAddition;
			break;
			case 'a_purchase':
				return $listParams->alertPurchase;
			break;
			default:
				return $listParams->canView;
			break;
		}
	}

	public function canViewList($listId = null)
	{
		if(empty($listId))
			return false;

		return $this->canDoActionForList($listId, 'view');
	}

	public function canEditList($listId = null)
	{
		if(empty($listId))
			return false;

		return $this->canDoActionForList($listId, 'edit');
	}

	public function canMarkGiftsForList($listId = null)
	{
		if(empty($listId))
			return false;

		return $this->canDoActionForList($listId, 'mark');
	}

	public function hasAddAlertForList($listId = null)
	{
		if(empty($listId))
			return false;

		return $this->canDoActionForList($listId, 'a_add');
	}

	public function hasPurchaseAlertForList($listId = null)
	{
		if(empty($listId))
			return false;

		return $this->canDoActionForList($listId, 'a_purchase');
	}

	public function isListOwner($list = null) {
		if(empty($list))
			return false;
		
		return ($list->ownerId === $this->id);
	}

	private function getParamsByListId($listId = null)
	{
		if(empty($listId))
			return false;

		$listId = (int)$listId;

		if(!isset($this->listParams) || empty($this->listParams)) {
			$this->loadParams();
		}

		if(!isset($this->listParams[$listId]) || empty($this->listParams[$listId])) {
			return false;
		}

		return $this->listParams[$listId];
	}
}