<?php
class BwGift
{
	private $id;
	public $name;
	
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = $id;
	}

	public function getGifts()
	{
		return $this->gifts;
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

			$this->id        = $result['id'];
			$this->name      = $result['name'];
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
				$gist = new self($result['id']);
				$gist->id        = $result['id'];
				$gist->name      = $result['name'];
				$allGifts[] = $gist;
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
				$gist = new self($result['id']);
				$gist->id        = $result['id'];
				$gist->name      = $result['name'];
				$allGifts[] = $gist;
			}

			return $allGifts;
		}
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