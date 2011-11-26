<?php
class BwList
{
	private $id;
	public $slug;
	public $name;
	public $ownerId;
	public $birthdate;
	public $lastUpdate;
	public $categoriesCount = 0;
	
	private $categories;
	
	public function __construct($slug = null)
	{
		if(!empty($slug))
			$this->slug = $slug;
	}

	public function getId()
	{
		return $this->id;
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

			$this->storeAttributes($result);
			return true;
		} else {
			return false;
		}
	}

	public function loadById($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
		if(empty($this->id))
			return false;

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
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
		$results = BwCache::read('list_all');
		if($results === false) {
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'gift_list',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryCondition' => '',
				'queryValues' => '',
				'queryOrderBy' => 'name ASC',
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
					$list->storeAttributes($result);
					$allLists[] = $list;
				}

				// Store this in the cache
				BwCache::write('list_all', $results);
				return $allLists;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allLists = array();
			foreach($results as $result) {
				$list = new self($result['slug']);
				$list->storeAttributes($result);
				$allLists[] = $list;
			}
			return $allLists;
		}
	}

	public function save($fields = array())
	{
		if(empty($fields))
			return false;

		$editableFields = array('name', 'slug', 'birthdate');

		foreach($fields as $fieldName => $fieldValue) {
			if(in_array(strtolower($fieldName), $editableFields)) {
				$dataFields[] = array(
					'parameter' => ':' . strtolower($fieldName),
					'variable' => $fieldValue,
					'data_type' => PDO::PARAM_STR
				);
			}
		}

		if(!empty($dataFields)) {
			$dataFields[] = array(
				'parameter' => ':id',
				'variable' => $this->id,
				'data_type' => PDO::PARAM_INT
			);
			$queryParams = array(
				'tableName' => 'gift_list',
				'queryType' => 'UPDATE',
				'queryCondition' => 'id = :id',
				'queryValues' => $dataFields,
			);
			foreach($dataFields as $dataField) {
				$queryParams['queryFields'][substr($dataField['parameter'], 1)] = $dataField['parameter'];
			}

			$db = BwDatabase::getInstance();
			if($db->prepareQuery($queryParams)) {
				return $db->exec();
			} else {
				return false;
			}
		}
	}

	public function checkExisting($name, $value) {
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'SELECT',
			'queryFields' => 'COUNT(id) as count_existing',
			'queryCondition' => array(
				'id != :id',
				$name . ' = :' . $name
			),
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':' . $name,
					'variable' => $value,
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

	/**
	 *
	 */
	public function deleteFromCache()
	{
		return (BwCache::delete('list_all') && BwCache::delete('list_' . $this->id));
	}

	private function storeAttributes($sqlResult)
	{
		$this->id         = (int)$sqlResult['id'];
		$this->name       = $sqlResult['name'];
		$this->ownerId    = (int)$sqlResult['user_id'];
		$this->birthdate  = $sqlResult['birthdate'];
		$this->lastUpdate = $sqlResult['last_update'];

		$this->categoriesCount = 0;
		$this->categories = BwCategory::getAllByListId($this->id);
		if(!empty($this->categories)) {
			$this->categoriesCount = count($this->categories);
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