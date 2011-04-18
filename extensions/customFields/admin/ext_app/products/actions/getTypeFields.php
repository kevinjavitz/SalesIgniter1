<?php
	$Qfields = Doctrine_Query::create()
	->from('ProductsCustomFields f')
	->leftJoin('f.ProductsCustomFieldsDescription fd')
	->leftJoin('f.ProductsCustomFieldsToGroups f2g')
	->where('f2g.group_id = ?', (int)$_GET['gID'])
	->execute();
	$rows = array();
	if ($Qfields->count() > 0){
		foreach($Qfields->toArray() as $field){
			$fieldId = $field['field_id'];
			$fieldType = $field['input_type'];
			$fieldName = $field['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
			
			$value = '';
			if (isset($_GET['pID']) && !empty($_GET['pID'])){
				$Qvalue = Doctrine_Query::create()
				->select('value')
				->from('ProductsCustomFieldsToProducts')
				->where('product_id = ?', (int)$_GET['pID'])
				->andWhere('field_id = ?', $fieldId)
				->fetchOne();
				$value = stripslashes($Qvalue['value']);
			}
			
			$input = '';
			switch($fieldType){
				case 'select':
					$oArr = array();
					
					$Qoptions = Doctrine_Query::create()
					->select('o.option_id, od.option_name')
					->from('ProductsCustomFieldsOptions o')
					->leftJoin('o.ProductsCustomFieldsOptionsDescription od')
					->leftJoin('o.ProductsCustomFieldsOptionsToFields o2f')
					->where('o2f.field_id = ?', $fieldId)
					->orderBy('sort_order')
					->execute();
					if ($Qoptions->count()){
						foreach($Qoptions->toArray(true) as $option){
							$oArr[] = array(
								'id'   => $option['ProductsCustomFieldsOptionsDescription'][Session::get('languages_id')]['option_name'],
								'text' => $option['ProductsCustomFieldsOptionsDescription'][Session::get('languages_id')]['option_name']
							);
						}
					}
					$input = tep_draw_pull_down_menu('fields[' . $fieldId . ']', $oArr, $value);
					break;
				case 'text':
					$input = tep_draw_input_field('fields[' . $fieldId . ']', $value);
					break;
				case 'search':
				case 'textarea':
					$input = tep_draw_textarea_field('fields[' . $fieldId . ']', 'soft', '30', '3', $value) . ($fieldType == 'search' ? '<br><small>*Separate values using ;</small>' : '');
					break;
				case 'upload':
					$input = tep_draw_file_field('fields_' . $fieldId) . '<br>Local File: ' . tep_draw_input_field('fields[' . $fieldId . ']', $value);
					break;
			}
			$rows[] = '<tr>
						 <td class="main" valign="top">' . $fieldName . ':</td>
						 <td class="main">' . $input . '</td>
						</tr>';
		}
	}
	
	$html = '<table cellpadding="3" cellspacing="0">' . 
		'<tr>' . 
			'<td><table cellpadding="3" cellspacing="0">' . 
				implode('', $rows) . 
			'</table></td>' . 
		'</tr>' . 
	'</table>';
	
	EventManager::attachActionResponse($html, 'html');
?>