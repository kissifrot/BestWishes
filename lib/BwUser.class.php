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
	
	public function __toString()
	{
		return $this->name;
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

	public function getTheme()
	{
		return $this->theme;
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

	public function getParamsByListId($listId = null)
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