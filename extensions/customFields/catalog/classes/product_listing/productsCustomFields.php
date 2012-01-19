<?php
class productListing_productsCustomFields {

	public function sortColumns(){
		$QcustomFields = Doctrine_Query::create()
						->from('ProductsCustomFields pcf')
						->leftJoin('pcf.ProductsCustomFieldsDescription pcfd')
						->where('language_id = ?', Session::get('languages_id'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
						
		if(count($QcustomFields) > 0){
			foreach($QcustomFields as $cInfo){
				$fieldValue = array(
					'value' => 'f2p.value;f2p.field_id = ' . $cInfo['field_id'] . ' or f2p.field_id is null',
					'name'  => $cInfo['ProductsCustomFieldsDescription'][0]['field_name'],
				);
				$selectSortKeys[] = $fieldValue;
			}
		}

		return $selectSortKeys;
	}

	public function show(&$productClass, &$purchaseTypesCol){
		global $appExtension;
		$productInfo = $productClass->productInfo;

		if (!isset($productInfo['ProductsCustomFieldsToProducts']) || empty($productInfo['ProductsCustomFieldsToProducts']) || sizeof($productInfo['ProductsCustomFieldsToProducts']) <= 0) return;
		
		$CustomFields = $appExtension->getExtension('customFields');
		if ($CustomFields !== false){
			$table = htmlBase::newElement('table')
			->setCellPadding(3)
			->setCellSpacing(0);
			$groups = $CustomFields->getFields($productInfo['products_id'], Session::get('languages_id'), false, false, true);
			foreach($groups as $groupInfo){
				$fieldsToGroups = $groupInfo['ProductsCustomFieldsToGroups'];
				foreach($fieldsToGroups as $fieldToGroup){
					if ($fieldToGroup['ProductsCustomFields']['show_name_on_listing'] == '1'){
						$name = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'] . ': ';
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
								->attr('href', itw_app_link($searchKey . '[]=' . $val, 'products', 'search_result'))
								->text('<u>' . $val . '</u>');

								$fieldValues[] = $searchLink->draw();
							}
							$fieldValue = implode(', ', $fieldValues);
						}
					}

					$table->addBodyRow(array(
						'columns' => array(
							array('addCls' => 'main', 'text' => '<b>' . $name . '</b>'),
							array('addCls' => 'main', 'text' => $fieldValue)
						)
					));
				}
			}
			return $table->draw();
		}
		return '';
	}
}
?>