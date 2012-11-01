<?php
class BwEvent
{
	public $id;
	public $name;
	public $type;
	public $day;
	public $month;
	public $year = null;
	public $isPermanent;
	
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
		if(empty($this->id))
			return false;

		// Try to read from the cache
		$result = BwCache::read('event_' . $this->id);
		if($result === false) {
			// Nothing in the cache
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'event',
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
				BwCache::write('event_' . $this->id, $result);
				return true;
			} else {
				return false;
			}
		} else {
			$this->storeAttributes($result);
			return true;
		}
	}

	/**
	 *
	 */
	public function loadAll($onlyActive = false)
	{
		// Try to read from the cache
		$results = BwCache::read('event_all');
		if($results === false) {
			// Nothing in the cache
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'event',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryCondition' => '',
				'queryValues' => ''
			);
			if($onlyActive) {
				$queryParams['queryValues'] = array(
					array(
						'parameter' => ':id',
						'variable' => $this->id,
						'data_type' => PDO::PARAM_INT
					)
				);
			}
			if($db->prepareQuery($queryParams)) {
				$results = $db->fetchAll();
				$db->closeQuery();
				if($results === false)
					return $results;

				if(empty($results)) {
					return false;
				}

				$allEvents = array();
				foreach($results as $result) {
					$event = new self($result['id']);
					$event->storeAttributes($result);
					$allEvents[] = $event;
				}

				// Store this in the cache
				BwCache::write('event_all', $results);
				return $allEvents;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allEvents = array();
			foreach($results as $result) {
				$event = new self($result['id']);
				$event->storeAttributes($result);
				$allEvents[] = $event;
			}
			return $allEvents;
		}
	}

	public function __toString()
	{
		return $this->name;
	}

	public function getId()
	{
		return $this->id;
	}

	public function save($fields = array())
	{
		$resultValue = 99;
		if(empty($fields))
			return $resultValue;

		$editableFields = array('name', 'event_day', 'event_month', 'event_year');

		// Associate the data given to SQL params
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
				'tableName' => 'event',
				'queryType' => 'UPDATE',
				'queryCondition' => 'id = :id',
				'queryValues' => $dataFields,
			);
			foreach($dataFields as $dataField) {
				$queryParams['queryFields'][substr($dataField['parameter'], 1)] = $dataField['parameter'];
			}

			$db = BwDatabase::getInstance();
			if($db->prepareQuery($queryParams)) {
				if($db->exec()) {
					$resultValue = 0;
					BwCache::delete('event_all');
				} else {
					$resultValue = 1;
				}
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public static function add($name, $type, $isPerm = true, $day = null, $month = null, $year = null) {
		$resultValue = 99;
		if(empty($name))
			return $resultValue;
		
		// Check for correct date
		if(!empty($day)) {
			if($day <= 0 || $day > 31) {
				$resultValue = 1;
				return $resultValue;
			}
		}
		if(!empty($month)) {
			if($month <= 0 || $month > 12) {
				$resultValue = 1;
				return $resultValue;
			}
		}
		if(!empty($year)) {
			if($year < (date('Y') - 10) || $year > (date('Y') + 10)) {
				$resultValue = 1;
				return $resultValue;
			}
		}

		// Check the full event date if set
		if(!empty($day) && !empty($month) && !empty($year)) {
			$timeEvent = strtotime($year . '-' . $month . '-' . $day);
			if($timeEvent === false) {
				$resultValue = 1;
				return $resultValue;
			}
		}

		// Check for already existing event
		if(self::checkAnyExisting('name', $name)) {
			$resultValue = 2;
			return $resultValue;
		}
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'event',
			'queryType' => 'INSERT',
			'queryFields' => array(
				'name' => ':name',
				'e_type' => ':e_type',
				'event_day' => ':event_day',
				'event_month' => ':event_month',
				'event_year' => ':event_year',
				'is_permanent' => ':is_permanent',
			),
			'queryValues' => array(
				array(
					'parameter' => ':name',
					'variable' => $name,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':e_type',
					'variable' => $type,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':event_day',
					'variable' => $day,
					'data_type' => empty($day) ? PDO::PARAM_NULL : PDO::PARAM_STR
				),
				array(
					'parameter' => ':event_month',
					'variable' => $month,
					'data_type' => empty($month) ? PDO::PARAM_NULL : PDO::PARAM_STR
				),
				array(
					'parameter' => ':event_year',
					'variable' => $year,
					'data_type' => empty($year) ? PDO::PARAM_NULL : PDO::PARAM_STR
				),
				array(
					'parameter' => ':is_permanent',
					'variable' => $isPerm,
					'data_type' => PDO::PARAM_INT
				)
			),
			'queryAutoField' => 'id'
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('event_all');
				// All OK
				$resultValue = 0;
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public static function checkAnyExisting($nameField, $nameValue) {
		$queryParams = array(
			'tableName' => 'event',
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

	private function storeAttributes($sqlResult)
	{
		$this->id          = (int)$sqlResult['id'];
		$this->name        = $sqlResult['name'];
		$this->type        = $sqlResult['e_type'];
		$this->day         = empty($sqlResult['event_day'])? null : (int)$sqlResult['event_day'];
		$this->month       = empty($sqlResult['event_month']) ? null : (int)$sqlResult['event_month'];
		$this->year        = empty($sqlResult['event_year']) ? null : (int)$sqlResult['event_year'];
		$this->isPermanent = (bool) $sqlResult['is_permanent'];
	}

	/**
	 *
	 */
	public static function getAllEvents()
	{
		$event = new self();
		return $event->loadAll();
	}

	/**
	 *
	 */
	public static function getAllActiveEvents()
	{
		$event = new self();
		$onlyActive = true;
		return $event->loadAll($onlyActive);
	}
	
	/**
	 * Get the nearest future events available
	 */
	public static function getNearestEvents($additionalDate = '')
	{
		if(empty($additionalDate)) {
			return false;
		}

		$activeEvents = self::getAllActiveEvents();
		$currentTime = mktime(0, 1, 1); // Today at 00:01:01
		// First update the "birthday" event with this list's birthdate
		foreach($activeEvents as $activeEvent)
		{
			if($activeEvent->type == 'birthday') {
				$birthdate = strtotime($additionalDate);
				$activeEvent->day   = date('j', $birthdate);
				$activeEvent->month = date('n', $birthdate);
				break;
			}
		}

		// Next create the dates corresponding to current year's events and next year's ones
		$calculatedEvents = array();
		foreach($activeEvents as $activeEvent)
		{
			$currentYear = $activeEvent->year;
			if(empty($currentYear)) {
				$currentYear = date('Y');
			}
			$currentMonth = $activeEvent->month;
			if(empty($currentMonth)) {
				$currentMonth = date('n');
			}
			$currentYearEvent = mktime(0, 1, 1, $currentMonth, $activeEvent->day, $currentYear);
			if($currentYearEvent >= $currentTime) {
				$calculatedEvents[] = array(
					'name' => $activeEvent->name,
					'time' => $currentYearEvent
				);
			}
			if($activeEvent->isPermanent) {
				$nextYearEvent = mktime(0, 1, 1, $currentMonth, $activeEvent->day, ($currentYear + 1));
				if($nextYearEvent >= $currentTime) {
					$calculatedEvents[] = array(
						'name' => $activeEvent->name,
						'time' => $nextYearEvent
					);
				}
			}
		}

		// And finally sort them
		usort($calculatedEvents, 'datesCompare');

		return $calculatedEvents;
	}
}