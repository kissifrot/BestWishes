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

	public function load($slug = null)
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

			$this->storeAttributes($result);
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
		if(empty($fields))
			return false;

		$editableFields = array('name', 'slug', 'birthdate');

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
				return $db->exec();
			} else {
				return false;
			}
		}
	}

	public function checkExisting($name, $value) {
		$queryParams = array(
			'tableName' => 'gift_list',
			'queryType' => 'SELECT',
			'queryFields' => 'COUNT(id) as count_existing',
			'queryCondition' => array(
				'id != :id',
				$name . ' = :' . $name
			),
			'queryValues' => array(
				array(
					'parameter' => ':id',
					'variable' => $this->id,
					'data_type' => PDO::PARAM_INT
				),
				array(
					'parameter' => ':' . $name,
					'variable' => $value,
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

	private function storeAttributes($sqlResult)
	{
		$this->id         = (int)$sqlResult['id'];
		$this->name       = $sqlResult['name'];
		$this->ownerId    = (int)$sqlResult['user_id'];
		$this->birthdate  = $sqlResult['birthdate'];
		$this->lastUpdate = $sqlResult['last_update'];

		$this->categoriesCount = 0;
		$this->categories = BwCategory::getAllByListId($this->id);
		if(!empty($this->categories)) {
			$this->categoriesCount = count($this->categories);
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
		} else {
			// Connected view
			if(!empty($connectedUser)) {
				if($connectedUser->getId() == $this->ownerId) {
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
		}
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