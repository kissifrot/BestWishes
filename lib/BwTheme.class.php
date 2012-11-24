<?php
class BwTheme
{
	public $id;

	public $name;
	public $shortName;
	public $isDefault;

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
		$result = BwCache::read('theme_' . $this->id);
		if($result === false) {
			// Nothing in the cache
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
				// Store this in the cache
				BwCache::write('theme_' . $this->id, $result);
				return true;
			} else {
				return false;
			}
		} else {
			$this->storeAttributes($result);
			return true;
		}
	}

	public function getId() {
		return $this->id;
	}

	public function loadDefault()
	{
		// Try to read from the cache
		$result = BwCache::read('theme_default');
		if($result === false) {
			// Nothing in the cache
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

				// Store this in the cache
				BwCache::write('theme_default', $result);
				$this->storeAttributes($result);
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
	public function loadAll()
	{
		// Try to read from the cache
		$results = BwCache::read('theme_all');
		if($results === false) {
			// Nothing in the cache
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
					$theme->storeAttributes($result);
					$allThemes[] = $theme;
				}

				return $allThemes;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allThemes = array();
			foreach($results as $result) {
				$theme = new self($result['id']);
				$theme->storeAttributes($result);
				$allThemes[] = $theme;
			}
			return $allEvents;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->id          = (int)$sqlResult['id'];
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