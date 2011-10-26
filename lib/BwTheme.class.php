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
			
			$this->storeAttributes($result);
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

			$this->storeAttributes($result);
			return true;
		}
	}

	/**
	 *
	 */
	public function loadAll()
	{
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'theme',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => '',
			'queryValues' => ''
		);
		if($db->prepareQuery($queryParams)) {
			$results = $db->fetchAll();
			$db->closeQuery();
			if($results === false)
				return $results;

			if(empty($results)) {
				return false;
			}

			$allThemes = array();
			foreach($results as $result) {
				$theme = new self($result['id']);
				$theme->storeAttributes($sqlResult)
				$allThemes[] = $theme;
			}

			return $allThemes;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->id          = $sqlResult['id'];
		$this->name        = $sqlResult['name'];
		$this->shortName   = $sqlResult['short_name'];
		$this->description = $sqlResult['description'];
		$this->isDefault   = (bool)$sqlResult['is_default'];
	}

	/**
	 *
	 */
	public static function getAll()
	{
		$theme = new self();
		return $theme->loadAll();
	}
}