<?php
class BwGift
{
	private $id;
	public $name;
	public $addedDate;
	public $isBought;
	public $isReceived;
	public $boughtDate;
	public $boughtBy;
	public $isSurprise;
	public $boughtComment;
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
		$this->id            = (int)$sqlResult['id'];
		$this->name          = $sqlResult['name'];
		$this->addedDate     = $sqlResult['added_date'];
		$this->isBought      = (bool)$sqlResult['is_bought'];
		$this->isReceived    = (bool)$sqlResult['is_received'];
		$this->boughtDate    = $sqlResult['bought_date'];
		$this->boughtBy      = null;
		if($this->isBought) {
			$buyingUser = new BwUser((int)$sqlResult['bought_by']);
			if($buyingUser->load()) {
				$this->boughtBy = $buyingUser->username;
			}
		}
		$this->boughtComment = $sqlResult['bought_comment'];
		$this->isSurprise    = (bool)$sqlResult['is_surprise'];
		$this->url           = $sqlResult['url'];
		$this->imageFilename = $sqlResult['image_filename'];
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