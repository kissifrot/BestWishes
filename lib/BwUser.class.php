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

		if(empty(trim($username)) || empty($password)) {
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
}