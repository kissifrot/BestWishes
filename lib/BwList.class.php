<?php
class BwList
{
	private $id;
	public $slug;
	public $name;
	public $birthdate;
	public $lastUpdate;
	
	public function __construct($slug = null)
	{
		if(!empty($slug))
			$this->slug = $slug;
	}

	public function load($slug = null)
	{
		if(!empty($slug))
			$this->slug = $slug;
		if(empty($this->slug))
			return false;
		
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => 'slug = :slug',
			'queryValues' => array(
				array(
					'parameter' => ':slug',
					'variable' => $this->slug,
					'data_type' => PDO::PARAM_STR
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

			$this->id         = $result['id'];
			$this->name       = $result['name'];
			$this->birthdate  = $result['birthdate'];
			$this->lastUpdate = $result['last_update'];
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
			'tableName' => 'gift_list',
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
			
			$allLists = array();
			foreach($results as $result) {
				$list = new BwList($result['slug']);
				$list->id         = $result['id'];
				$list->name       = $result['name'];
				$list->birthdate  = $result['birthdate'];
				$list->lastUpdate = $result['last_update'];
				$allLists[] = $list;
			}

			return $allLists;
		}
	}

	/**
	 *
	 */
	public static function getAllLists()
	{
		$list = new self();
		return $list->loadAll();
	}
}