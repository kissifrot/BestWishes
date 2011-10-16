<?php
class BwUser
{
	private $id;
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = $id;
	}

	public function login($username = '', $password = '')
	{
		$usernameTrim = trim($username);
		$passwordTrim = trim($password);

		if(empty($usernameTrim) || empty($passwordTrim)) {
			return false;
		}
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = $id;
		if(empty($this->id))
			return false;
		
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'users',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				':id' => $this->id
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
			
			return true;
		}
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
}