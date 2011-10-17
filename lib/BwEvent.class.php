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
			
			$this->id          = $result['id'];
			$this->name        = $result['name'];
			$this->type        = $result['type'];
			$this->day         = $result['event_day'];
			$this->month       = $result['event_month'];
			$this->year        = $result['event_year'];
			$this->isPermanent = (bool) $result['is_permanent'];
			return true;
		}
	}

	/**
	 *
	 */
	public function loadAll($onlyActive = false)
	{
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
				$event->id          = $result['id'];
				$event->name        = $result['name'];
				$event->type        = $result['type'];
				$event->day         = $result['event_day'];
				$event->month       = $result['event_month'];
				$event->year        = $result['event_year'];
				$event->isPermanent = (bool) $result['is_permanent'];
				$allEvents[] = $event;
			}

			return $allEvents;
		}
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
			$currentYearEvent = mktime(0, 1, 1, $activeEvent->month, $activeEvent->day, $currentYear);
			if($currentYearEvent >= $currentTime) {
				$calculatedEvents[] = array(
					'name' => $activeEvent->name,
					'time' => $currentYearEvent
				);
			}
			if($activeEvent->isPermanent) {
				$nextYearEvent = mktime(0, 1, 1, $activeEvent->month, $activeEvent->day, ($currentYear + 1));
				if($nextYearEvent >= $currentTime) {
					$calculatedEvents[] = array(
						'name' => $activeEvent->name,
						'time' => $nextYearEvent
					);
				}
			}
		}
		
		return $calculatedEvents;
	}
}