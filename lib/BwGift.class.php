<?php
/**
 * Gifts management class
 */
class BwGift
{
	private $id;
	private $categoryId;
	public $name;
	public $addedDate;
	public $editsCount;
	public $isBought;
	public $isReceived;
	public $boughtDate;
	public $boughtBy;
	public $boughtByName;
	public $isSurprise;
	public $purchaseComment;
	public $imageFilename;
	public $url;
	
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
		if(empty($this->id))
			return false;

		// Try to read from the cache
		$result = BwCache::read('gift_' . $this->id);
		if($result === false) {
			// Nothing in the cache
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'gift',
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
				BwCache::write('gift_' . $this->id, $result);
				return true;
			}
		} else {
			// Use cache data
			$this->storeAttributes($result);
			return true;
		}
	}

	/**
	 *
	 */
	private function loadAllByCategoryId($categoryId = null, $includeReceived = false)
	{
		if(empty($categoryId))
			return false;

		// Try to read from the cache
		$results = BwCache::read('gift_all_cat_' . $categoryId);
		if($results === false) {
			// Nothing in the cache
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'gift',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryValues' => array(
					array(
						'parameter' => ':category_id',
						'variable' => $categoryId,
						'data_type' => PDO::PARAM_INT
					)
				)
			);
			// Filter the received gifts
			if($includeReceived) {
				$queryParams['queryCondition'] = 'category_id = :category_id';
			} else {
				$queryParams['queryCondition'] = array(
					'category_id = :category_id',
					'is_received != 1'
				);
			}
			
			if($db->prepareQuery($queryParams)) {
				$results = $db->fetchAll();
				$db->closeQuery();
				if($results === false)
					return $results;

				if(empty($results)) {
					// Store this in the cache even if empty
					BwCache::write('gift_all_cat_' . $categoryId, $results);
					return false;
				}
				
				$allGifts = array();
				foreach($results as $result) {
					$gift = new self((int)$result['id']);
					$gift->storeAttributes($result);
					$allGifts[] = $gift;
				}

				// Store this in the cache
				BwCache::write('gift_all_cat_' . $categoryId, $results);
				return $allGifts;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allGifts = array();
			foreach($results as $result) {
				$gift = new self((int)$result['id']);
				$gift->storeAttributes($result);
				$allGifts[] = $gift;
			}
			return $allGifts;
		}
	}

	/**
	 *
	 */
	private function loadAllByListId($listId = null)
	{
		if(empty($listId))
			return false;

		// Try to read from the cache
		$results = BwCache::read('gift_all_list_' . $listId);
		if($results === false) {
			// Nothing in the cache
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'gift',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryCondition' => 'gift_list_id = :gift_list_id',
				'queryValues' => array(
					array(
						'parameter' => ':gift_list_id',
						'variable' => $listId,
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
				
				$allGifts = array();
				foreach($results as $result) {
					$gift = new self((int)$result['id']);
					$gift->storeAttributes($result);
					$allGifts[] = $gift;
				}

				// Store this in the cache
				BwCache::write('gift_all_list_' . $listId, $results);
				return $allGifts;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allGifts = array();
			foreach($results as $result) {
				$gift = new self((int)$result['id']);
				$gift->storeAttributes($result);
				$allGifts[] = $gift;
			}
			return $allGifts;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->id              = (int)$sqlResult['id'];
		$this->categoryId      = (int)$sqlResult['category_id'];
		$this->name            = $sqlResult['name'];
		$this->addedDate       = $sqlResult['added_date'];
		$this->editsCount      = (int)$sqlResult['edits_count'];
		$this->isBought        = (bool)$sqlResult['is_bought'];
		$this->isReceived      = (bool)$sqlResult['is_received'];
		$this->boughtDate      = $sqlResult['bought_date'];
		$this->boughtBy        = null;
		$this->boughtByName    = null;
		$this->purchaseComment = null;
		if($this->isBought) {
			$buyingUser = new BwUser((int)$sqlResult['bought_by']);
			if($buyingUser->load()) {
				$this->boughtBy        = (int)$sqlResult['bought_by'];
				$this->boughtByName    = $buyingUser->username;
				$this->purchaseComment = $sqlResult['purchase_comment'];
			}
		}
		$this->isSurprise    = (bool)$sqlResult['is_surprise'];
		$this->url           = $sqlResult['url'];
		$this->imageFilename = $sqlResult['image_filename'];
	}

	/*
	 *
	 */
	public function filterContent()
	{
		$this->isBought        = false;
		$this->boughtDate      = null;
		$this->boughtBy        = null;
		$this->boughtByName    = null;
		$this->purchaseComment = null;
	}

	/**
	 *
	 */
	public static function add($listId = null, $catId = null, $name = '', $forceAdd = false) {

		$resultValue = 99;
		if(empty($listId) || empty($catId) || empty($name)) {
			return $resultValue;
		}

		if(!$forceAdd) {
			if(self::checkExisting($listId, $name)) {
				$resultValue = 1;
				return $resultValue;
			}
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'INSERT',
			'queryFields' => array(
				'gift_list_id' => ':gift_list_id',
				'category_id' => ':category_id',
				'name' => ':name',
				'added_date' => ':added_date',
			),
			'queryValues' => array(
				array(
					'parameter' => ':gift_list_id',
					'variable' => $listId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':category_id',
					'variable' => $catId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':name',
					'variable' => $name,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':added_date',
					'variable' => date('Y-m-d H:i:s'),
					'data_type' => PDO::PARAM_STR
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('category_all_list_' . $listId);
				BwCache::delete('gift_all_cat_' . $catId);
				// All OK
				$resultValue = 0;
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public static function addSurprise($listId = null, $catId = null, $name = '', $forceAdd = false) {

		$resultValue = 99;
		if(empty($listId) || empty($catId) || empty($name)) {
			return $resultValue;
		}

		if(!$forceAdd) {
			if(self::checkExisting($listId, $name)) {
				$resultValue = 1;
				return $resultValue;
			}
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'INSERT',
			'queryFields' => array(
				'gift_list_id' => ':gift_list_id',
				'category_id' => ':category_id',
				'name' => ':name',
				'added_date' => ':added_date',
				'is_surprise' => ':is_surprise',
			),
			'queryValues' => array(
				array(
					'parameter' => ':gift_list_id',
					'variable' => $listId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':category_id',
					'variable' => $catId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':name',
					'variable' => $name,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':added_date',
					'variable' => date('Y-m-d H:i:s'),
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':is_surprise',
					'variable' => 1,
					'data_type' => PDO::PARAM_INT
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('category_all_list_' . $listId);
				BwCache::delete('category_' . $catId);
				BwCache::delete('gift_all_list_' . $listId);
				BwCache::delete('gift_all_cat_' . $catId);
				BwCache::delete('category_all_list_' . $listId);
				BwCache::delete('gift_all_cat_' . $catId);
				// All OK
				$resultValue = 0;
			}
		}
		return $resultValue;
	}

	public function delete($listId = null) {
		$resultValue = 99;
		if(empty($listId)) {
			return $resultValue;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'DELETE',
			'queryFields' => '',
			'queryCondition' => array(
				'gift_list_id = :gift_list_id',
				'id = :id'
			),
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':gift_list_id',
					'variable' => $listId,
					'data_type' => PDO::PARAM_INT
				)
			),
			
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('category_all_list_' . $listId);
				BwCache::delete('category_' . $this->categoryId);
				BwCache::delete('gift_all_list_' . $listId);
				BwCache::delete('gift_all_cat_' . $this->categoryId);
				$resultValue = 0;
			} else {
				$resultValue = 1;
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public function markAsBought($listId = null, $userId = null, $purchaseComment = '') {
		$resultValue = 99;
		if(empty($listId) || empty($userId)) {
			return $resultValue;
		}
		
		if($this->isBought) {
			// Already bought, stop
			$resultValue = 2;
			return $resultValue;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'UPDATE',
			'queryFields' => array(
				'is_bought' => ':is_bought',
				'bought_date' => ':bought_date',
				'bought_by' => ':bought_by',
				'purchase_comment' => ':purchase_comment'
			),
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':is_bought',
					'variable' => 1,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':bought_date',
					'variable' => date('Y-m-d'),
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':bought_by',
					'variable' => $userId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':purchase_comment',
					'variable' => $purchaseComment,
					'data_type' => PDO::PARAM_STR
				)
			),
			
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('category_all_list_' . $listId);
				BwCache::delete('category_' . $this->categoryId);
				BwCache::delete('gift_all_list_' . $listId);
				BwCache::delete('gift_all_cat_' . $this->categoryId);
				$resultValue = 0;
			} else {
				$resultValue = 1;
			}
		}
		return $resultValue;
	}


	/**
	 *
	 */
	public function markAsReceived($listId = null) {
		$resultValue = 99;
		if(empty($listId)) {
			return $resultValue;
		}
		
		if($this->isReceived) {
			// Already received, stop
			$resultValue = 2;
			return $resultValue;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'UPDATE',
			'queryFields' => array(
				'is_received' => ':is_received'
			),
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':is_received',
					'variable' => 1,
					'data_type' => PDO::PARAM_INT
				)
			),
			
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('category_all_list_' . $listId);
				BwCache::delete('category_' . $this->categoryId);
				BwCache::delete('gift_all_list_' . $listId);
				BwCache::delete('gift_all_cat_' . $this->categoryId);
				$resultValue = 0;
			} else {
				$resultValue = 1;
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public static function checkExisting($listId = null, $name = '') {

		if(empty($listId) ||  empty($name)) {
			return false;
		}

		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'SELECT',
			'queryFields' => 'COUNT(*) as count_existing',
			'queryCondition' => array(
				'gift_list_id = :gift_list_id',
				'name = :name',
				'is_received = :is_received'
			),
			'queryValues' => array(
				array(
					'parameter' => ':gift_list_id',
					'variable' => $listId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':name',
					'variable' => $name,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':is_received',
					'variable' => 1,
					'data_type' => PDO::PARAM_INT
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
	public static function getAllByCategoryId($categoryId = null, $includeReceived = false)
	{
		if( empty($categoryId))
			return false;

		$gift = new self();
		return $gift->loadAllByCategoryId((int)$categoryId, $includeReceived);
	}

	/**
	 *
	 */
	public static function getAllByListId($listId = null)
	{
		if(empty($listId))
			return false;

		$gift = new self();
		return $gift->loadAllByListId((int)$listId);
	}
}