<?php
class BwList
{
	private $id;
	public $slug;
	public $name;
	public $ownerId;
	public $birthdate;
	public $lastUpdate;
	public $categoriesCount = 0;
	
	private $categories;
	
	public function __construct($slug = null)
	{
		if(!empty($slug))
			$this->slug = $slug;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getCategories()
	{
		return $this->categories;
	}

	public function lightLoad($slug = null)
	{
		return $this->load($slug, true);
	}

	public function load($slug = null, $lightLoad = false)
	{
		if(!empty($slug))
			$this->slug = $slug;
		if(empty($this->slug))
			return false;

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'SELECT',
			'queryFields' => '*',
			'queryCondition' => 'slug = :slug',
			'queryValues' => array(
				array(
					'parameter' => ':slug',
					'variable' => $this->slug,
					'data_type' => PDO::PARAM_STR
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

			$this->storeAttributes($result, $lightLoad);
			return true;
		} else {
			return false;
		}
	}

	public function loadById($id = null)
	{
		if(!empty($id))
			$this->id = (int)$id;
		if(empty($this->id))
			return false;

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
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
		} else {
			return false;
		}
	}

	/**
	 *
	 */
	private function loadAll()
	{
		// Try to read from the cache
		$results = BwCache::read('list_all');
		if($results === false) {
			$db = BwDatabase::getInstance();
			$queryParams = array(
				'tableName' => 'gift_list',
				'queryType' => 'SELECT',
				'queryFields' => '*',
				'queryCondition' => '',
				'queryValues' => '',
				'queryOrderBy' => 'name ASC',
			);
			if($db->prepareQuery($queryParams)) {
				$results = $db->fetchAll();
				$db->closeQuery();
				if($results === false)
					return $results;

				if(empty($results)) {
					return false;
				}

				$allLists = array();
				foreach($results as $result) {
					$list = new self($result['slug']);
					$list->storeAttributes($result);
					$allLists[] = $list;
				}

				// Store this in the cache
				BwCache::write('list_all', $results);
				return $allLists;
			} else {
				return false;
			}
		} else {
			// Use cache data
			$allLists = array();
			foreach($results as $result) {
				$list = new self($result['slug']);
				$list->storeAttributes($result);
				$allLists[] = $list;
			}
			return $allLists;
		}
	}

	public function save($fields = array())
	{
		$resultValue = 99;
		if(empty($fields))
			return $resultValue;

		$editableFields = array('name', 'slug', 'birthdate');

		// Special case for the name
		if(isset($fields['name']) && !empty($fields['name'])) {
			// Check for already existing list
			if($this->checkExisting('name', $fields['name'])) {
				$resultValue = 1;
				return $resultValue;
			}
			$fields['slug'] = BwInflector::slug($fields['name']);
		}

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
				'tableName' => 'gift_list',
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
					$this->deleteFromCache();
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
	public static function add($name, $ownerId, $birthdate) {
		$resultValue = 99;
		if(empty($name) || empty($ownerId) || empty($birthdate))
			return $resultValue;
		
		// Check for correct birthdate
		$timeBirthdate = strtotime($birthdate);
		if($timeBirthdate === false || $timeBirthdate > strtotime('-1 month', strtotime(date('Y-m-d')))) {
			$resultValue = 1;
			return $resultValue;
		}

		// Check for already existing list
		if(self::checkAnyExisting('name', $name)) {
			$resultValue = 2;
			return $resultValue;
		}
		// Now generate the slug
		$slug = BwInflector::slug($name);
		$countStart = 1;
		if(self::checkAnyExisting('slug', $slug)) {
			// That slug already exists, we'll try another
			while(self::checkAnyExisting('slug', $slug . $countStart)) {
				$countStart++;
			}
			$slug = $slug . $countStart;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'INSERT',
			'queryFields' => array(
				'user_id' => ':user_id',
				'name' => ':name',
				'slug' => ':slug',
				'birthdate' => ':birthdate',
			),
			'queryValues' => array(
				array(
					'parameter' => ':user_id',
					'variable' => $ownerId,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':name',
					'variable' => $name,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':slug',
					'variable' => $slug,
					'data_type' => PDO::PARAM_STR
				),
				array(
					'parameter' => ':birthdate',
					'variable' => $birthdate,
					'data_type' => PDO::PARAM_STR
				)
			),
			'queryAutoField' => 'id'
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// Empty cache
				BwCache::delete('list_all');
				// All OK
				$newListId = intval($db->lastInsertId());
				//  Add the necessary rights to all users
				$resultValue = BwUserParams::addByListId($newListId, $ownerId);
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	public function checkExisting($nameField, $nameValue) {
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'SELECT',
			'queryFields' => 'COUNT(id) as count_existing',
			'queryCondition' => array(
				'id != :id',
				$nameField . ' = :' . $nameField
			),
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
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

	/**
	 *
	 */
	public function updateLastUpdate()
	{
		$lastUpdate = date('Y-m-d H:i:s');
		$this->lastUpdate = $lastUpdate;
		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'UPDATE',
			'queryFields' => array(
				'last_update' => ':last_update',
			),
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':last_update',
					'variable' => $lastUpdate,
					'data_type' => PDO::PARAM_STR
				)
			),
			'queryCondition' => 'id = :id'
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// TODO: Empty cache or not?
				return true;
			}
		}
		return false;
	}

	/**
	 *
	 */
	public static function checkAnyExisting($nameField, $nameValue) {
		$queryParams = array(
			'tableName' => 'gift_list',
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

	/**
	 *
	 */
	public function deleteFromCache()
	{
		return (BwCache::delete('list_all') && BwCache::delete('list_' . $this->id));
	}

	/**
	 *
	 */
	private function storeAttributes($sqlResult, $lightStore = false)
	{
		$this->id         = (int)$sqlResult['id'];
		$this->name       = $sqlResult['name'];
		if(empty($this->slug)) {
			$this->slug   = $sqlResult['slug'];
		}
		$this->ownerId    = (int)$sqlResult['user_id'];
		$this->birthdate  = $sqlResult['birthdate'];
		$this->lastUpdate = $sqlResult['last_update'];

		if(!$lightStore) {
			$this->categoriesCount = 0;
			$this->categories = BwCategory::getAllByListId($this->id);
			if(!empty($this->categories)) {
				$this->categoriesCount = count($this->categories);
			}
		}
	}

	/**
	 *
	 */
	public static function getAll()
	{
		$list = new self();
		return $list->loadAll();
	}

	/**
	 *
	 */
	public function delete() {
		$resultValue = 99;
		
		// First delete the categories (and their gifts)
		$resultValue = $this->deleteAllCategories();
		if($resultValue != 0) {
			return $resultValue;
		}

		// Then delete the user params
		$resultValue = $this->deleteAllParams();
		if($resultValue != 0) {
			return $resultValue;
		}

		$db = BwDatabase::getInstance();
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'DELETE',
			'queryFields' => '',
			'queryCondition' => 'id = :id',
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				)
			),
			
		);
		if($db->prepareQuery($queryParams)) {
			$result =  $db->exec();
			if($result) {
				// TODO: Correctly empty cache
				BwCache::delete('list_all');
				BwCache::delete('category_all_list_' . $this->id);
				BwCache::delete('gift_all_list_' . $this->id);
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
	private function deleteAllCategories() {
		$resultValue = 99;

		if($this->categoriesCount == 0) {
			$resultValue = 0;
			return $resultValue;
		}

		// Delete each of the categories
		foreach($this->categories as $aCategory) {
			$resultValue = $aCategory->delete($this->id);
			if($resultValue != 0) {
				return $resultValue;
			}
		}
		return $resultValue;
	}

	/**
	 *
	 */
	private function deleteAllParams() {
		$resultValue = 99;

		// Delete all the corresponding parameters
		$resultValue = BwUserParams::deleteByListId($this->id, $this->ownerId);

		return $resultValue;
	}

	/**
	 *
	 */
	public function PdfOutput($title = '', $subTitle = '', $sessionOK = false, $user = null) {
		// Initialize the PDF class
		$pdf = new BwPdf();
		
		$pdf->SetFont('DejaVu', 'B', 16);
		$pdf->Cell(0, 10, $title, 0, 1, 'C');
		$pdf->SetFont('DejaVu', 'I', 12);
		$pdf->Cell(0, 10, $subTitle, 0, 1, 'C');

		if($this->categoriesCount > 0) {
			foreach ($this->categories as $category) {
				if ($category->giftsCount > 0) {
					$pdf->SetFont('DejaVu', 'B', 12);
					$pdf->Cell(0, 12, ucfirst($category->name).' :', 0, 1);
					foreach ($category->getGifts() as $gift) {
						if($sessionOK) {
							if($user->isListOwner($this)) {
								$pdf->SetFont('DejaVu','',12);
								$pdf->Cell($pdf->GetStringWidth($gift->name), 6, ucfirst($gift->name), 0, 1);
							} else {
								if($gift->isBought) {
									if($gift->boughtBy === $user->getId()) {
										$pdf->SetFont('DejaVu', 'BI', 12);
										$pdf->SetTextColor(255, 0, 0);
										$pdf->Cell($pdf->GetStringWidth($gift->name), 6, ucfirst($gift->name), 0, 0);
										$pdf->SetTextColor(0, 0, 0);
										$pdf->SetFont('DejaVu', 'I', 12);
										$pdf->Cell(0, 6, sprintf(_('(bought by yourself on %s)'), date(_('m/d/y'), strtotime($gift->purchaseDate))), 0, 1);
									} else {
										$pdf->SetFont('DejaVu', 'BI', 12);
										$pdf->SetTextColor(255, 0, 0);
										if($pdf->GetStringWidth($gift->name) > 200)
											$pdf->MultiCell(0, 6, ucfirst($gift->name), 0, 'L');
										else
											$pdf->Cell($pdf->GetStringWidth($gift->name), 6, ucfirst($gift->name), 0, 0);
										$pdf->SetTextColor(0, 0, 0);
										$pdf->SetFont('DejaVu', 'I', 12);
										$pdf->Cell(0, 6, sprintf(_('(bought by %s on %s)'), $gift->boughtByName, date(_('m/d/y'), strtotime($gift->purchaseDate))), 0, 1);
									}
								} else {
									$pdf->SetFont('DejaVu','',12);
									$pdf->Cell($pdf->GetStringWidth($gift->name), 6, ucfirst($gift->name), 0, 1);
								}
							}
						} else {
							$pdf->SetFont('DejaVu','',12);
							$pdf->Cell($pdf->GetStringWidth($gift->name), 6, ucfirst($gift->name), 0, 1);
						}
					}
				}
			}
		} else {
			$pdf->SetFont('DejaVu','I',12);
			$pdf->Cell(0,12,_('(This list is still empty)'), 0, 1, 'C');
		}
		$pdf->Output(_('list_') . $this->slug . '.pdf', 'I');
	}

	/**
	 * This function will flter the categories and gifts which will then be displayed
	 */
	public function filterContent($isConnected = false, $connectedUser = null)
	{
		if(!$isConnected) {
			if(empty($this->categories))
				return true;
			// Standard view, delete the surprise gifts, received gifts and the empty categories
			foreach($this->categories as $category) {
				if($category->giftsCount > 0) {
					foreach($category->gifts as $index => $gift) {
						if($gift->isSurprise) {
							unset($category->gifts[$index]);
						}
					}
				}
			}
			return true;
		}

		// Connected view
		if(!empty($connectedUser)) {
			if($connectedUser->getId() == $this->ownerId) {
				if(empty($this->categories))
					return true;
				// The user is viewing his/her list, delete the surprise and received gifts
				foreach($this->categories as $category) {
					if($category->giftsCount > 0) {
						foreach($category->gifts as $index => $gift) {
							if($gift->isBought) {
								$category->gifts[$index]->filterContent();
							}
							if($gift->isSurprise) {
								unset($category->gifts[$index]);
							}
						}
					}
				}
			}

		}
		return true;
	}

	/**
	 *
	 */
	public function getNearestEventData()
	{
		$calculatedEvents = BwEvent::getNearestEvents($this->birthdate);
		if(empty($calculatedEvents))
			return false;

		$nextEventData = end($calculatedEvents);
		$daysLeft = 0;
		$currentTime = mktime(0, 1, 1); // Today at 00:01:01
		$timeLeft = $nextEventData['time'] - $currentTime;
		$daysLeft = round($timeLeft/3600/24);
		$nextEventData['daysLeft'] = $daysLeft;

		return $nextEventData;
	}
}