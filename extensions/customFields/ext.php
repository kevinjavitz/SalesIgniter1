<?php
/*
	Products Custom Fields Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_customFields extends ExtensionBase {

	public function __construct(){
		parent::__construct('customFields');
	}
	
	public function init(){
		global $App, $appExtension, $Template;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'ProductQueryBeforeExecute',
			'ProductInfoBeforeDescription',
			'CustomProductInfoGetFields',
			'ProductSearchValidateKey',
			'ProductSearchAddValidKeys',
			'ProductSearchQueryBeforeExecute',
			'AdvancedSearchAddSearchFields',
			'SearchBoxAddGuidedOptions',
		), null, $this);

		if ($appExtension->isAdmin()){
			EventManager::attachEvent('BoxCatalogAddLink', null, $this);
		}
		if ($appExtension->isCatalog()){
			EventManager::attachEvent('ProductListingQueryBeforeExecute', null, $this);
		}
	}
	
	public function SearchBoxAddGuidedOptions(&$boxContent, $fieldId){
		$searchItemDisplay = 4;
		$Qfields = Doctrine_Query::create()
		->select('f.search_key, f.field_id, fd.field_name, f2p.value')
		->from('ProductsCustomFields f')
		->leftJoin('f.ProductsCustomFieldsDescription fd')
		->leftJoin('f.ProductsCustomFieldsToProducts f2p')
		->where('fd.language_id = ?', Session::get('languages_id'))
		//->andWhere('f.input_type = ?', 'search')
		->andWhere('f.field_id = ?', $fieldId)
		->orderBy('fd.field_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qfields){
			$fieldHtml = '';
			foreach($Qfields as $fInfo){
				if ($fInfo['search_key'] == '') continue;

				$added = array();
				$dropArray = array();
				if (!empty($fInfo['ProductsCustomFieldsToProducts'])){
					foreach($fInfo['ProductsCustomFieldsToProducts'] as $vInfo){
						$values = explode(';', $vInfo['value']);
						if (sizeof($values) > 0){
							foreach($values as $val){
								$trimmed = trim($val);
								if (!in_array($trimmed, $added)){
										$dropArray[] = array(
										'id'   => addslashes($trimmed),
										'text' => $trimmed
									);
									$added[] = $trimmed;
								}
							}
						}
					}
					
					$count = 0;
					foreach($dropArray as $fieldInfo){
						$QproductCount = Doctrine_Query::create()
						->select('count(*) as total')
						->from('ProductsCustomFieldsToProducts')
						->where('value LIKE ?', '%' . $fieldInfo['id'] . '%')
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						$searchVal = trim($fieldInfo['id']);
						$searchText = trim($fieldInfo['text']);
						$searchKey = $fInfo['search_key'];
						$getIdx = $count;
						$checked = false;
						if (isset($_GET[$searchKey]) && in_array($searchVal, $_GET[$searchKey])){
							$arrayKeys = array_keys($_GET[$searchKey], $searchVal);
							$getIdx = $arrayKeys[0];
							$checked = true;
						}
						
						$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;background:none;"></span>';
						$link = itw_app_link(tep_get_all_get_params(array($searchKey . '[' . $getIdx . ']')) . $searchKey . '[' . $count . ']=' . $searchVal, 'products', 'search_result');
						if ($checked === true){
							$checkIcon = '<span class="ui-icon ui-icon-check" style="display:inline-block;height:14px;"></span>';
							$link = itw_app_link(tep_get_all_get_params(array($searchKey . '[' . $getIdx . ']')), 'products', 'search_result');
						}
						$icon = '<span class="ui-widget ui-widget-content ui-corner-all">' . 
							$checkIcon . 
						'</span>';

						$boxContent .= '<li style="padding-bottom:.3em;' . ($count > $searchItemDisplay ? 'display:none;' : '') . '">' . 
							$icon . 
							' <a href="' . $link . '" data-url_param="' . $searchKey . '[' . $count . ']=' . $searchVal . '">' . 
								$searchText . 
							'</a> (' . $QproductCount[0]['total'] . ')' . 
						'</li>';
						
						$count++;
					}
					if ($count > $searchItemDisplay){
						$boxContent .= '<li class="searchShowMoreLink"><a href="#"><b>More</b></a></li>';
					}
				}
			}
		}
	}
	
	public function ProductSearchAddValidKeys(&$validSearchKeys){
		$Qkeys = Doctrine_Query::create()
		->select('search_key')
		->from('ProductsCustomFields')
		//->where('show_on_site = ?', '1')
				->where('search_key != ?', '')
				->andWhere('search_key is not NULL')
				->execute();
		if ($Qkeys){
			foreach($Qkeys->toArray() as $key){
				if (!in_array($key, $validSearchKeys)){
					$validSearchKeys[] = $key['search_key'];
				}
			}
		}
		
		$validSearchKeys[] = 'field_val';
		$validSearchKeys[] = 'field_id';
	}
	
	public function ProductSearchValidateKey(&$key, &$totalErrors){
		if (($key == 'field_val' || $key == 'field_id') && isset($_GET[$key]) && !empty($_GET[$key])){
			$this->validSearchKeys[$key] = $_GET[$key];
		}else{
			$Qcheck = Doctrine_Query::create()
					->select('field_id')
					->from('ProductsCustomFields')
					->where('search_key != ?', '')
					->andWhere('search_key is not NULL')
					->fetchOne();
			if ($Qcheck !== false){
				if (isset($key) && empty($_GET[$key])){
					$totalErrors++;
				}elseif (isset($key) && !empty($_GET[$key])){
					$this->validSearchKeys[$key] = $_GET[$key];
				}
			}
		}
	}
	
	/* 
	 * Searches on each field/value pair by itself
	 */
	/*
	public function ProductSearchQueryBeforeExecute(&$Qproducts){
		if (isset($this->validSearchKeys) && sizeof($this->validSearchKeys) > 0){
			$fieldsSearch = array();
			foreach($this->validSearchKeys as $k => $v){
				if ($k == 'field_val'){
					$fieldsSearch[] = 'f.field_id = "' . $this->validSearchKeys['field_id'] . '" and f2p.value LIKE "%' . str_replace('.', '%', $v) . '%"';
				}elseif ($k == 'field_id'){
					continue;
				}elseif (is_array($v)){
					$addSearch = array();
					foreach($v as $count => $val){
						$addSearch[] = 'f2p.value like "%' . str_replace('.', '%', $val) . '%"'; 
					}
					$fieldsSearch[] = 'f.search_key = "' . $k . '" and (' . implode(' or ', $addSearch) . ')';
				}else{
					$fieldsSearch[] = 'f.search_key = "' . $k . '" and f2p.value like "%' . str_replace('.', '%', $v) . '%"';
				}
			}
			$Qproducts->leftJoin('p.ProductsCustomFieldsToProducts f2p')
			->leftJoin('f2p.ProductsCustomFields f')
			->andWhere('((' . implode(') or (', $fieldsSearch) . '))');
		}
	}
	*/
	
	/* 
	 * Searches on all field/value pairs
	 */
	public function ProductSearchQueryBeforeExecute(&$Qproducts){
		//echo '<pre>';print_r($this->validSearchKeys);
		if (isset($this->validSearchKeys) && sizeof($this->validSearchKeys) > 0){
			$fieldsSearch = array();
			$i = 1;
			$j = 1;
			foreach($this->validSearchKeys as $k => $v){
				if ($k == 'field_val'){
					$fieldsSearch[] = '(SELECT count(*) FROM ProductsCustomFieldsToProducts f2p' . $i . ' LEFT JOIN f2p' . $i . '.ProductsCustomFields f' . $j . ' WHERE f' . $j . '.field_id = "' . $this->validSearchKeys['field_id'] . '" AND f2p' . $i . '.value LIKE "%' . str_replace('.', '%', $v) . '%") > 0';
					$i++;$j++;
				}elseif ($k == 'field_id'){
					continue;
				}elseif (is_array($v)){
					$addSearch = array();
					foreach($v as $count => $val){
						$addSearch[] = 'f2p' . $i . '.value like "%' . str_replace('.', '%', $val) . '%"'; 
					}
					$fieldsSearch[] = '(SELECT count(*) FROM ProductsCustomFieldsToProducts f2p' . $i . ' LEFT JOIN f2p' . $i . '.ProductsCustomFields f' . $j . ' WHERE f2p' . $i . '.product_id = p.products_id AND f' . $j . '.search_key = "' . $k . '" AND (' . implode(' OR ', $addSearch) . ')) > 0';
					$i++;$j++;
				}else{
					$fieldsSearch[] = '(SELECT count(*) FROM ProductsCustomFieldsToProducts f2p' . $i . ' LEFT JOIN f2p' . $i . '.ProductsCustomFields f' . $j . ' WHERE f2p' . $i . '.product_id = p.products_id AND f' . $j . '.search_key = "' . $k . '" AND f2p' . $i . '.value LIKE "%' . str_replace('.', '%', $v) . '%") > 0';
					$i++;$j++;
				}
			}
			$Qproducts->leftJoin('p.ProductsCustomFieldsToProducts f2p')
			->leftJoin('f2p.ProductsCustomFields f')
			->andWhere('((' . implode(') AND (', $fieldsSearch) . '))');
		}
	}

	public function BoxCatalogAddLink(&$contents){
		$contents['children'][] = array(
			'link'       => itw_app_link('appExt=customFields','manage','default','SSL'),
			'text'       => 'Custom Fields'
		);
	}
	
	public function ProductQueryBeforeExecute(&$productQuery){
		$productQuery->addSelect('f2p.field_id')
		->leftJoin('p.ProductsCustomFieldsToProducts f2p');
	}

	public function ProductListingQueryBeforeExecute(&$Qproducts){
		$Qproducts->leftJoin('p.ProductsCustomFieldsToProducts f2p');
	}

	public function getFields($pId = null, $languageId = null, $shownOnProductInfo = false, $shownOnLabels = false, $shownOnListing = false){
		$Query = Doctrine_Query::create()
		->select('g.group_name, f.*, fd.field_name, f2p.value, f2g.sort_order')
		->from('ProductsCustomFieldsGroups g')
		->leftJoin('g.ProductsCustomFieldsGroupsToProducts g2p')
		->leftJoin('g.ProductsCustomFieldsToGroups f2g')
		->leftJoin('f2g.ProductsCustomFields f')
		->leftJoin('f.ProductsCustomFieldsDescription fd')
		->leftJoin('f.ProductsCustomFieldsToProducts f2p')
		->where('fd.field_name is not null')
		->orderBy('f2g.sort_order');
		
		if (is_null($pId) === false){
			$Query->andWhere('f2p.product_id = ?', (int)$pId)
			->andWhere('g2p.product_id = ?', (int)$pId);
		}
		
		if ($shownOnProductInfo === true){
			$Query->andWhere('f.show_on_site = ?', '1');
		}
		
		if ($shownOnLabels === true){
			$Query->andWhere('f.show_on_labels = ?', '1');
		}
		
		if ($shownOnListing === true){
			$Query->andWhere('f.show_on_listing = ?', '1');
		}
		
		if (is_null($languageId) === false){
			$Query->andWhere('fd.language_id = ?', (int)$languageId);
		}
		
		$Result = $Query->execute()->toArray(true);
		return $Result;
	}
	
	public function ProductInfoBeforeDescription(&$product){
		$productInfo = $product->productInfo;

		if (!isset($productInfo['ProductsCustomFieldsToProducts']) || empty($productInfo['ProductsCustomFieldsToProducts']) || sizeof($productInfo['ProductsCustomFieldsToProducts']) <= 0) return;
			
		$table = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0);

		$groups = $this->getFields($productInfo['products_id'], Session::get('languages_id'), true, false, false);
		foreach($groups as $groupInfo){
			$fieldsToGroups = $groupInfo['ProductsCustomFieldsToGroups'];
			foreach($fieldsToGroups as $fieldToGroup){

				if ($fieldToGroup['ProductsCustomFields']['show_name_on_listing'] == '1'){
					$name = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
				}else{
					$name = '';
				}
				$value = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsToProducts'][0]['value'];
				$inputType = $fieldToGroup['ProductsCustomFields']['input_type'];
				$searchKey = $fieldToGroup['ProductsCustomFields']['search_key'];
			
				$fieldValue = $value;
				if ($inputType == 'upload'){
					$fieldValue = htmlBase::newElement('a')
					->attr('href', itw_app_link('appExt=customFields&filename=' . $value, 'simpleDownload', 'default'))
					->attr('target', '_blank')
					->text('<u>' . $value . '</u>')
					->draw();
				}elseif ($inputType == 'search'){
					$values = explode(';', $value);
					if (sizeof($values) > 0){
						$fieldValues = array();
						foreach($values as $val){
							$searchLink = htmlBase::newElement('a')
							->attr('href', itw_app_link($searchKey . '[]=' . trim($val), 'products', 'search_result'))
							->text('<u>' . $val . '</u>');
						
							$fieldValues[] = $searchLink->draw();
						}
						$fieldValue = implode(', ', $fieldValues);
					}
				}
			
				$table->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => '<b>' . $name . ':</b>'),
						array('addCls' => 'main', 'text' => $fieldValue)
					)
				));
			}
		}
		return $table->draw();
	}
	
	public function CustomProductInfoGetFields(&$product){
		$productInfo = $product->productInfo;

		if ($productInfo['ProductsCustomFieldsToProducts'][0]['customFieldTotal'] <= 0) return;
			
		$output = '';
		$output .= htmlBase::newElement('button')
		->addClass('customFieldsButton ui-state-selected ui-corner-all-big')
		->attr('img_name', DIR_WS_IMAGES . $productInfo['products_image'])
		->setText('Front View')
		->draw() . '<br />';
		
		$groups = $this->getFields($productInfo['products_id'], Session::get('languages_id'));
		foreach($groups as $groupInfo){
			$fieldsToGroups = $groupInfo['ProductsCustomFieldsToGroups'];
			foreach($fieldsToGroups as $fieldToGroup){
				$name = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
				$value = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsToProducts'][0]['value'];
				$inputType = $fieldToGroup['ProductsCustomFields']['input_type'];
				$searchKey = $fieldToGroup['ProductsCustomFields']['search_key'];
			
				$fieldValue = $value;
				if ($inputType == 'upload'){
					$output .= htmlBase::newElement('button')
					->addClass('customFieldsButton ui-corner-all-big')
					->attr('img_name', DIR_WS_IMAGES . $value)
					->setText($name)
					->draw() . '<br />';
				}
			}
		}
		return $output;
	}
	
	public function AdvancedSearchAddSearchFields(&$AdvancedTable){
		$Qfields = Doctrine_Query::create()
		->select('f.search_key, f.field_id, fd.field_name, f2p.value')
		->from('ProductsCustomFields f')
		->leftJoin('f.ProductsCustomFieldsDescription fd')
		->leftJoin('f.ProductsCustomFieldsToProducts f2p')
		->where('fd.language_id = ?', Session::get('languages_id'))
		->andWhere('f.input_type = ?', 'search')
		->orderBy('fd.field_name')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qfields){
			foreach($Qfields as $fInfo){
				if ($fInfo['search_key'] == '') continue ;
			
				$added = array();
				if (!empty($fInfo['ProductsCustomFieldsToProducts'])){
					foreach($fInfo['ProductsCustomFieldsToProducts'] as $vInfo){
						$values = explode(';', $vInfo['value']);
						if (sizeof($values) > 0){
							foreach($values as $val){
								if (!in_array($val, $added) && !empty($val)){
									$added[] = $val;
								}
							}
						}
					}

					natsort($added);

					$dropArray = array(array('id' => '', 'text' => sysLanguage::get('TEXT_PLEASE_SELECT')));
					foreach($added as $value){
						$dropArray[] = array(
										'id'   => addslashes($value),
										'text' => $value
						);
					}
					$AdvancedTable->addBodyRow(array(
							'columns' => array(
								array('addCls' => 'fieldKey', 'text' => $fInfo['ProductsCustomFieldsDescription'][0]['field_name']),
								array('addCls' => 'fieldValue', 'text' => tep_draw_pull_down_menu($fInfo['search_key'], $dropArray))
							)
						));
					unset($dropArray);
				}
			}
		}
	}
}
?>