<?php
class BwCategory
{
	private $id;
	public $slug;
	public $name;
	public $isVisible;
	public $giftsCount = 0;
	private $giftListId;
	
	private $gifts;
	
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
			'tableName' => 'category',
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

			$this->id         = $result['id'];
			$this->name       = $result['name'];
			$this->giftListId = $result['gift_list_id'];
			$this->isVisible  = (bool) $result['is_visible'];

			$this->giftsCount = 0;
			$this->gifts = BwGift::getAllByByListIdCategoryId($result['gift_list_id'], $result['id']);
			if(!empty($this->gifts)) {
				$this->giftsCount = count($this->gifts);
			}
			return true;
		}
	}

	/**
	 *
	 */
	public function loadAllByListId($listId = null)
	{
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'category',
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
			
			$allCategories = array();
			foreach($results as $result) {
				$category = new self($result['id']);
				$category->id        = $result['id'];
				$category->name      = $result['name'];
				$category->isVisible = (bool) $result['is_visible'];

				$category->giftsCount = 0;
				$category->gifts = BwGift::getAllByListIdCategoryId($result['gift_list_id'], $result['id']);
				if(!empty($category->gifts)) {
					$category->giftsCount = count($category->gifts);
				}
				$allCategories[] = $category;
			}

			return $allCategories;
		}
	}

	/**
	 *
	 */
	public static function getAllByListId($listId = null)
	{
		if(empty($listId))
			return false;

		$category = new self();
		return $category->loadAllByListId($listId);
	}
}