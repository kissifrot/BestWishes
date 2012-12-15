<?php
/**
 * Categories management class
 */
class BwCategory
{
	public $id;
	public $name;
	public $isVisible;
	public $giftsCount = 0;
	public $giftListId;
	
	public $gifts;
	
	public function __construct($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getGifts()
	{
		return $this->gifts;
	}

	public function load($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
		if(empty($this->id))
			return false;

		// Try to read from the cache
		$result = BwCache::read('category_' . $this->id);
		if($result === false) {
			// Nothing in the cache
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

				$this->storeAttributes($result);
				// Store this in the cache
				BwCache::write('category_' . $this->id, $result);
				return true;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$this->storeAttributes($result);
			return true;
		}
	}

	private function storeAttributes($sqlResult)
	{
		$this->id         = (int)$sqlResult['id'];
		$this->name       = $sqlResult['name'];
		$this->giftListId = (int)$sqlResult['gift_list_id'];
		$this->isVisible  = (bool)$sqlResult['is_visible'];

		$this->giftsCount = 0;
		$this->gifts = BwGift::getAllByCategoryId((int)$sqlResult['id']);
		if(!empty($this->gifts)) {
			$this->giftsCount = count($this->gifts);
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
		$results = BwCache::read('category_all_list_' . $listId);
		if($results === false) {
			// Nothing in the cache
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
				),
				'queryOrderBy' => 'name ASC'
			);
			if($db->prepareQuery($queryParams)) {
				$results = $db->fetchAll();
				$db->closeQuery();
				if($results === false)
					return $results;

				if(empty($results)) {
					return array();
				}
				
				$allCategories = array();
				foreach($results as $result) {
					$category = new self($result['id']);
					$category->storeAttributes($result);
					$allCategories[] = $category;
				}

				// Store this in the cache
				BwCache::write('category_all_list_' . $listId, $results);
				return $allCategories;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allCategories = array();
			foreach($results as $result) {
				$category = new self($result['id']);
				$category->storeAttributes($result);
				$allCategories[] = $category;
			}
			return $allCategories;
		}
	}

	/**
	 *
	*/
	public static function add($listId = null, $name = '') {
		$resultValue = 99;
		if(empty($listId) || empty($name)) {
			return $resultValue;
		}

		if(self::checkExisting($listId, $name)) {
			$resultValue = 2;
			return $resultValue;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'category',
			'queryType' => 'INSERT',
			'queryFields' => array(
				'gift_list_id' => ':gift_list_id',
				'name' => ':name',
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
					'data_type' => PDO::PARAM_INT
				)
			)
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('category_all_list_' . $listId);
				// All OK
				$resultValue = 0;
			} else {
				$resultValue = 1;
			}
		}
		return $resultValue;
	}

	public function delete($listId = null) {
		$resultValue = 99;
		if(empty($listId)) {
			return $resultValue;
		}

		if(!$this->giftListId == $listId) {
			return $resultValue;
		}

		// Delete all the gifts first
		$resultValue = $this->deleteAllGifts($listId);
		if($resultValue != 0) {
			return $resultValue;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'category',
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
				BwCache::delete('category_' . $this->id);
				BwCache::delete('gift_all_list_' . $listId);
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
	private function deleteAllGifts($listId = null) {
		$resultValue = 99;
		if(empty($listId)) {
			return $resultValue;
		}

		if($this->giftsCount == 0) {
			$resultValue = 0;
			return $resultValue;
		}

		$gift = new BwGift();
		// Delete all the gifts for this category
		$resultValue = BwGift::deleteByCategoryId($listId, $this->id);
		return $resultValue;
	}

	/**
	 *
	 */
	public static function checkExisting($listId = null, $name = '') {

		if(empty($listId) || empty($name))
			return false;

		$queryParams = array(
			'tableName' => 'category',
			'queryType' => 'SELECT',
			'queryFields' => 'COUNT(id) as count_existing',
			'queryCondition' => array(
				'gift_list_id = :gift_list_id',
				'name = :name'
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
	public static function getAllByListId($listId = null)
	{
		if(empty($listId))
			return false;

		$category = new self();
		return $category->loadAllByListId((int)$listId);
	}

	/**
	 * This function will flter the gifts which will then be displayed
	 */
	public function filterContent($isConnected = false, $connectedUser = null, $list)
	{
		if(!$isConnected) {
			if(empty($this->gifts))
				return true;
			// Standard view, delete the surprise gifts and received gifts
			if($this->giftsCount > 0) {
				foreach($this->gifts as $index => $gift) {
					if($gift->isSurprise) {
						unset($this->gifts[$index]);
					}
				}
			}
			return true;
		}

		// Connected view
		if(!empty($connectedUser)) {
			if($connectedUser->getId() == $list->ownerId) {
				if(empty($this->gifts))
					return true;
				// The user is viewing his/her list, delete the surprise and received gifts
				if($this->giftsCount > 0) {
					foreach($this->gifts as $index => $gift) {
						if($gift->isBought) {
							$this->gifts[$index]->filterContent();
						}
						if($gift->isSurprise) {
							unset($this->gifts[$index]);
						}
					}
				}
			}
		}
		return true;
	}
}