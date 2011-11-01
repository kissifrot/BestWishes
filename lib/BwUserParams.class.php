<?php
class BwUserParams
{
	public $listId;
	public $userId;
	public $canView;
	public $canEdit;
	public $canMark;
	public $alertAddition;
	public $alertBuy;

	public function __construct()
	{
		if(!empty($id)) {
			$this->id = (int)$id;
			$this->load($this->id);
		}
	}

	public function loadByUserIdListId($userId = null, $listId = null)
	{
		if(empty($userId) || empty($listId))
			return false;
		
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'list_user_params',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => array(
				'gift_list_user_id = :gift_list_user_id',
				'gift_list_id = :gift_list_id'
			),
			'queryValues' => array(
				array(
					'parameter' => ':gift_list_user_id',
					'variable' => $userId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':gift_list_id',
					'variable' => $listId,
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

	public function loadByUserId($userId = null)
	{
		if(empty($userId))
			return false;
		
		// Try to read from the cache
		$results = BwCache::read('user_param_' . $userId);
		if($results === false) {
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'list_user_params',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryCondition' => 'gift_list_user_id = :gift_list_user_id',
				'queryValues' => array(
					array(
						'parameter' => ':gift_list_user_id',
						'variable' => $userId,
						'data_type' => PDO::PARAM_INT
					)
				)
			);
			if($db->prepareQuery($queryParams)) {
				$results = $db->fetchAll();
				$db->closeQuery();
				if($results === false)
					return $results;
				
				if(empty($results)) {
					return false;
				}

				$userParams = array();
				foreach($results as $result) {
					$userParam = new self();
					$userParam->storeAttributes($result);
					$userParams[intval($result['gift_list_id'])] = $userParam;
				}

				// Store this in the cache
				BwCache::write('user_param_' . $userId, $results);
				return $userParams;
			} else {
				return false;
			}
		} else {
			// Use cache data
			foreach($results as $result) {
				$userParam = new self();
				$userParam->storeAttributes($result);
				$userParams[intval($result['gift_list_id'])] = $userParam;
			}
			return $userParams;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->listId        = (int)$sqlResult['gift_list_id'];
		$this->userId        = (int)$sqlResult['gift_list_user_id'];
		$this->canView       = (bool)$sqlResult['can_view'];
		$this->canEdit       = (bool)$sqlResult['can_edit'];
		$this->canMark       = (bool)$sqlResult['can_mark'];
		$this->alertAddition = (bool)$sqlResult['alert_addition'];
		$this->alertBuy      = (bool)$sqlResult['alert_buy'];
	}

	/**
	 *
	 */
	public static function getAllByUserId($userId = null)
	{
		$param = new self();
		return $param->loadByUserId($userId);
	}
}