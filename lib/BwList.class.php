<?php
class BwList
{
	private $id;
	public $slug;
	public $name;
	public $birthdate;
	public $lastUpdate;
	public $categoriesCount = 0;
	
	private $categories;
	
	public function __construct($slug = null)
	{
		if(!empty($slug))
			$this->slug = $slug;
	}

	public function getCategories()
	{
		return $this->categories;
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

			$this->id           = $result['id'];
			$this->name         = $result['name'];
			$this->birthdate    = $result['birthdate'];
			$this->lastUpdate   = $result['last_update'];

			$this->categoriesCount = 0;
			$this->categories = BwCategory::getAllByListId($result['id']);
			if(!empty($this->categories)) {
				$this->categoriesCount = count($this->categories);
			}
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
				$list = new self($result['slug']);
				$list->id         = $result['id'];
				$list->name       = $result['name'];
				$list->birthdate  = $result['birthdate'];
				$list->lastUpdate = $result['last_update'];

				$list->categoriesCount = 0;
				$list->categories = BwCategory::getAllByListId($result['id']);
				if(!empty($list->categories)) {
					$list->categoriesCount = count($list->categories);
				}
				$allLists[] = $list;
			}

			return $allLists;
		}
	}

	/**
	 *
	 */
	public static function getAll()
	{
		$list = new self();
		return $list->loadAll();
	}

	/**
	 *
	 */
	public function getNearestEventData()
	{
		$calculatedEvents = BwEvent::getNearestEvents($this->birthdate);
		if(empty($calculatedEvents))
			return false;

		$nextEventData = end($calculatedEvents);
		$daysLeft = 0;
		$currentTime = mktime(0, 1, 1); // Today at 00:01:01
		$timeLeft = $nextEventData['time'] - $currentTime;
		$daysLeft = round($timeLeft/3600/24);
		$nextEventData['daysLeft'] = $daysLeft;

		return $nextEventData;
	}
}