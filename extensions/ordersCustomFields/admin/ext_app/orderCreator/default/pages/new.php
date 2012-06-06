<?php
class ordersCustomFields_admin_orderCreator_default_new extends Extension_ordersCustomFields
{

	public function __construct() {
		parent::__construct('ordersCustomFields');
	}

	public function load() {
		global $appExtension;
		if ($this->isEnabled() === false){
			return;
		}

		EventManager::attachEvents(array(
				'OrderCreatorAddToInfoTableAfter'
			), null, $this);
	}



	public function OrderCreatorAddToInfoTableAfter(&$infoTable, OrderCreator $Editor){
		global $App, $appExtension;

		$Qfields = Doctrine_Query::create()
			->select('f.field_id, f.input_type, f.input_required, fd.field_name')
			->from('OrdersCustomFields f')
			->leftJoin('f.OrdersCustomFieldsDescription fd')
			->where('fd.language_id = ?', Session::get('languages_id'))
			->execute();
		$ocExtraFields = '';
		if ($Qfields->count() > 0){
			foreach($Qfields->toArray(true) as $fInfo){
				$fieldId = $fInfo['field_id'];
				$fieldType = $fInfo['input_type'];
				$fieldName = $fInfo['OrdersCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
				$fieldRequired = ($fInfo['input_required'] == 1);

				$input = '';
				$otherInput = null;
				switch($fieldType){
					case 'select':
					case 'select_other':
						$oArr = array();

						$input = htmlBase::newElement('selectbox');

						$Qoptions = Doctrine_Query::create()
							->select('o.option_id, od.option_name')
							->from('OrdersCustomFieldsOptions o')
							->leftJoin('o.OrdersCustomFieldsOptionsDescription od')
							->leftJoin('o.OrdersCustomFieldsOptionsToFields o2f')
							->where('o2f.field_id = ?', $fieldId)
							->orderBy('sort_order')
							->execute();
						$input->addOption('NoneSelected','Please Select');
						$otherVal = '';
						if(isset($_GET['oID'])){
							$Qfields1 = Doctrine_Query::create()
							->from('OrdersCustomFieldsToOrders')
							->where('orders_id = ?', $_GET['oID'])
							->andWhere('field_id = ?', $fieldId)
							->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if(isset($Qfields1[0])){
								$input->selectOptionByValue('Other');
								$otherVal =  $Qfields1[0]['value'];
							}

						}

						if ($Qoptions->count()){
							foreach($Qoptions->toArray(true) as $option){
								$input->addOption(
									$option['OrdersCustomFieldsOptionsDescription'][Session::get('languages_id')]['option_name'],
									$option['OrdersCustomFieldsOptionsDescription'][Session::get('languages_id')]['option_name']
								);
								if(isset($Qfields1[0]) && $option['OrdersCustomFieldsOptionsDescription'][Session::get('languages_id')]['option_name'] == $otherVal){
									$input->selectOptionByValue($option['OrdersCustomFieldsOptionsDescription'][Session::get('languages_id')]['option_name']);
									$otherVal = '';
								}
							}
						}

						if ($fieldType == 'select_other'){
							$input->addOption('Other', 'Other (Fill in below)');

							$otherInput = '<div class="main" style="clear:both;margin-top:.3em;">Other: ' . tep_draw_input_field('orders_custom_field_other[' . $fieldId . ']',$otherVal) . '</div>';
						}
						break;
					case 'text':
						$input = htmlBase::newElement('input');
						if(isset($_GET['oID'])){
							$Qfields1 = Doctrine_Query::create()
								->from('OrdersCustomFieldsToOrders')
								->where('orders_id = ?', $_GET['oID'])
								->andWhere('field_id = ?', $fieldId)
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if(isset($Qfields1[0])){
								$input->setValue($Qfields1[0]['value']);
							}

						}

						break;
					case 'textarea':
						$input = htmlBase::newElement('textarea')->attr('rows', 3)->attr('cols', 30);
						if(isset($_GET['oID'])){
							$Qfields1 = Doctrine_Query::create()
								->from('OrdersCustomFieldsToOrders')
								->where('orders_id = ?', $_GET['oID'])
								->andWhere('field_id = ?', $fieldId)
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
							if(isset($Qfields1[0])){
								$input->html($Qfields1[0]['value']);
							}

						}
						break;
				}
				$input->setName('orders_custom_field[' . $fieldId . ']');

				if ($fieldRequired === true){
					$input->css('float', 'left');
				}

				$rows[] = '<tr>
							 <td class="main" valign="top">' . $fieldName . ':</td>
							 <td class="main">' . $input->draw() . ($fieldRequired === true ? '<div class="ui-icon ui-icon-gear required_icon" style="margin-left: 3px; margin-top: 1px; float: left;"></div>' : '').(isset($otherInput) ? $otherInput : '')  . '</td>
							</tr>';
			}
			$ocExtraFields = '<div class="main"><b>Extra Info</b></div>' .
				'<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;">' .
				'<table cellpadding="3" cellspacing="0">' .
				'<tr>' .
				'<td><table cellpadding="3" cellspacing="0">' .
				implode('', $rows) .
				'</table></td>' .
				'</tr>' .
				'</table>' .
				'</div>';
		}

		$infoTable->addBodyRow(array(
					'columns' => array(
						array('addCls' => 'main', 'text' => '<b>' . sysLanguage::get('ORDER_CREATOR_EXTRA_FIELDS') . '</b>'),
						array('addCls' => 'main', 'text' => $ocExtraFields)
					)
		));
	}

}