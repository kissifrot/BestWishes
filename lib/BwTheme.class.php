<?php
class BwTheme
{
	private $id;

	public $name;
	public $shortName;
	public $isDefault;
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = $id;
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = $id;
		if(empty($this->id))
			return false;
		
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'theme',
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

	public function loadDefault()
	{
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'theme',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => 'is_default = :is_default',
			'queryValues' => array(
				array(
					'parameter' => ':is_default',
					'variable' => 1,
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

	private function storeAttributes($elem, $sqlResult)
	{
		$elem->id        = $sqlResult['id'];
		$elem->name      = $sqlResult['name'];
		$elem->shortName = $sqlResult['short_name'];
		$elem->isDefault = (bool)$sqlResult['is_default'];
	}
}