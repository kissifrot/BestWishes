<?php
class BwGift
{
	private $id;
	public $name;
	public $addedDate;
	public $isBought;
	public $boughtDate;
	public $boughtBy;
	public $boughtComment;
	public $imageFilename;
	public $url;
	public $isSurprise;
	
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = $id;
		if(empty($this->id))
			return false;

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
			return true;
		}
	}

	/**
	 *
	 */
	public function loadAllByListIdCategoryId($listId = null, $categoryId = null)
	{
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => array(
				'gift_list_id = :gift_list_id',
				'category_id = :category_id'
			),
			'queryValues' => array(
				array(
					'parameter' => ':gift_list_id',
					'variable' => $listId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':category_id',
					'variable' => $categoryId,
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
				$gift = new self($result['id']);
				$gift->storeAttributes($result);
				$allGifts[] = $gift;
			}

			return $allGifts;
		}
	}

	/**
	 *
	 */
	public function loadAllByListId($listId = null)
	{
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
				$gift = new self($result['id']);
				$gift->storeAttributes($result);
				$allGifts[] = $gift;
			}

			return $allGifts;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->id            = $sqlResult['id'];
		$this->name          = $sqlResult['name'];
		$this->addedDate     = $sqlResult['added_date'];
		$this->isBought      = (bool)$sqlResult['is_bought'];
		$this->boughtDate    = $sqlResult['bought_date'];
		$this->boughtBy      = null;
		if($this->isBought) {
			$buyingUser = new BwUser((int)$sqlResult['bought_by']);
			if($buyingUser->load()) {
				$this->boughtBy = $buyingUser->username;
			}
		}
		$this->boughtComment = $sqlResult['bought_comment'];
		$this->url           = $sqlResult['url'];
		$this->imageFilename = $sqlResult['image_filename'];
		$this->isSurprise    = (bool)$sqlResult['is_surprise'];
	}

	/**
	 *
	 */
	public static function getAllByListIdCategoryId($listId = null, $categoryId = null)
	{
		if(empty($listId) || empty($categoryId))
			return false;

		$gift = new self();
		return $gift->loadAllByListIdCategoryId($listId, $categoryId);
	}

	/**
	 *
	 */
	public static function getAllByByListId($listId = null)
	{
		if(empty($listId))
			return false;

		$gift = new self();
		return $gift->loadAllByListId($listId);
	}
}