<?php
/*
	Products Custom Fields Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_customerCustomFields extends ExtensionBase {

    public function __construct(){
        parent::__construct('customerCustomFields');
    }

    public function init(){
        global $App, $appExtension, $Template;
        if ($this->isEnabled() === false) return;

        EventManager::attachEvents(array(
										'CheckoutSetupPostFields',
										'CheckoutSetupFields',
										'NewCustomerAccountBeforeExecute',
										'UpdateCustomerAccountBeforeExecute'

                                   ), null, $this);

        if ($appExtension->isAdmin()){
            EventManager::attachEvent('BoxCatalogAddLink', null, $this);
        }
    }

    public function BoxCatalogAddLink(&$contents){
        $contents['children'][] = array(
            'link'       => itw_app_link('appExt=customerCustomFields','manage','default','SSL'),
            'text'       => 'Customer Custom Fields'
        );
    }

	public function CheckoutSetupPostFields(){
			$newCustomerData = array();
			if (isset($_POST['customer_custom_field'])){
				$newCustomerData['customer_extra_fields']['customer_custom_field'] = $_POST['customer_custom_field'];
				if (isset($_POST['customer_custom_field_other'])){
					$newCustomerData['customer_extra_fields']['customer_custom_field_other'] = $_POST['customer_custom_field_other'];
				}
			}
		Session::set('extraCustomerData', $newCustomerData);
	}

	public function NewCustomerAccountBeforeExecute(&$newUser){
		if(Session::exists('extraCustomerData')){
			$extraCustomerData = Session::get('extraCustomerData');
		}
		if (isset($extraCustomerData['customer_extra_fields']['customer_custom_field'])){
			foreach($extraCustomerData['customer_extra_fields']['customer_custom_field'] as $fieldId => $val){
				if (is_array($val)) continue;

				$Qfield = Doctrine_Query::create()
						->select('f.field_id, f.input_type, fd.field_name')
						->from('CustomerCustomFields f')
						->leftJoin('f.CustomerCustomFieldsDescription fd')
						->where('f.field_id = ?', $fieldId)
						->andWhere('fd.language_id = ?', Session::get('languages_id'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if ($Qfield[0]['input_type'] == 'selectOther' && $val == 'Other'){
					if (isset($extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId])){
						if (!empty($extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId])){
							$val = $extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId];
						}
					}
				}

				$field = new CustomerCustomFieldsToCustomers();
				$newUser->save();
				$field->value = $val;
				$field->customers_id = $newUser->customers_id;
				$field->field_id = $Qfield[0]['field_id'];
				//$field->field_label = $Qfield[0]['CustomerCustomFieldsDescription'][0]['field_name'];
				$field->field_type = $Qfield[0]['input_type'];
				$field->save();
			}
		}
		Session::remove('extraCustomerData');
	}

	public function UpdateCustomerAccountBeforeExecute(&$newUser){
		global $userAccount;
		if(Session::exists('extraCustomerData')){
			$extraCustomerData = Session::get('extraCustomerData');
		}
		if (isset($extraCustomerData['customer_extra_fields']['customer_custom_field'])){
			foreach($extraCustomerData['customer_extra_fields']['customer_custom_field'] as $fieldId => $val){
				if (is_array($val)) continue;

				$Qfield = Doctrine_Query::create()
						->select('f.field_id, f.input_type, fd.field_name')
						->from('CustomerCustomFields f')
						->leftJoin('f.CustomerCustomFieldsDescription fd')
						->where('f.field_id = ?', $fieldId)
						->andWhere('fd.language_id = ?', Session::get('languages_id'))
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if ($Qfield[0]['input_type'] == 'selectOther' && $val == 'Other'){
					if (isset($extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId])){
						if (!empty($extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId])){
							$val = $extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId];
						}
					}
				}
				$customerId = 0;
				if(isset($newUser->customers_id)){
					$customerId = $newUser->customers_id;
				}elseif($userAccount->isLoggedIn()){
					$customerId = $userAccount->getCustomerId();
				}

				$field = Doctrine_Core::getTable('CustomerCustomFieldsToCustomers')->findOneByFieldIdAndCustomersId($Qfield[0]['field_id'], $customerId);
				if($field){
					$field->value = $val;
					//$field->field_label = $Qfield[0]['CustomerCustomFieldsDescription'][0]['field_name'];
					$field->field_type = $Qfield[0]['input_type'];
					$field->save();
				}
			}
		}
		Session::remove('extraCustomerData');
	}

    public function CheckoutSetupFields(){
		global $userAccount;
		$Query = Doctrine_Query::create()
		->from('CustomerCustomFieldsGroups g')
		->leftJoin('g.CustomerCustomFieldsToGroups f2g')
		->leftJoin('f2g.CustomerCustomFields f')
		->leftJoin('f.CustomerCustomFieldsDescription fd')
		->where('fd.field_name is not null')
		->andWhere('fd.language_id = ?', Session::get('languages_id'))
		->orderBy('f2g.sort_order')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		//print_r($Query);
		//itwExit();
		ob_start();
		foreach($Query as $iGroups){
		?>
	<div class="ui-widget ui-widget-content ui-corner-all">
		<div class="ui-widget-header ui-corner-all" style="padding-left: 5px;margin:0px;"><?php echo $iGroups['group_name'] ?></div>
		<table cellpadding="0" cellspacing="0" border="0">
			<?php
			foreach($iGroups['CustomerCustomFieldsToGroups'] as $iCustomField){
			?>
			<tr>
				<td><?php echo $iCustomField['CustomerCustomFields']['CustomerCustomFieldsDescription'][0]['field_name']; ?></td>
				<td><?php
				$input = '';
				$otherInput = null;
				$fieldId = $iCustomField['CustomerCustomFields']['field_id'];
				$selectedValue = '';
				if(Session::exists('extraCustomerData')){
					$extraCustomerData = Session::get('extraCustomerData');
				}

				if (isset($extraCustomerData['customer_extra_fields']['customer_custom_field'][$iCustomField['CustomerCustomFields']['field_id']])){
					$selectedValue = $extraCustomerData['customer_extra_fields']['customer_custom_field'][$fieldId];
					if (isset($extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId])){
						if (!empty($extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId])){
							$selectedValue = $extraCustomerData['customer_extra_fields']['customer_custom_field_other'][$fieldId];
						}
					}
				}elseif($userAccount->isLoggedIn()){
					$field = Doctrine_Core::getTable('CustomerCustomFieldsToCustomers')->findOneByFieldIdAndCustomersId($fieldId, $userAccount->getCustomerId());
					if($field){
						$selectedValue = $field->value;
					}
				}
				switch($iCustomField['CustomerCustomFields']['input_type']){
					case 'select':
					case 'selectOther':
						$input = htmlBase::newElement('selectbox');

						$Qoptions = Doctrine_Query::create()
								->from('CustomerCustomFieldsOptions o')
								->leftJoin('o.CustomerCustomFieldsOptionsDescription od')
								->leftJoin('o.CustomerCustomFieldsOptionsToFields o2f')
								->where('o2f.field_id = ?', $iCustomField['CustomerCustomFields']['field_id'])
								->andWhere('od.language_id = ?', Session::get('languages_id'))
								->orderBy('sort_order')
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

						//$input->addOption('NoneSelected','Please Select');
						$isOther = true;
						foreach($Qoptions as $option){
								$input->addOption(
									$option['CustomerCustomFieldsOptionsDescription'][0]['option_name'],
									$option['CustomerCustomFieldsOptionsDescription'][0]['option_name']
								);
							if($option['CustomerCustomFieldsOptionsDescription'][0]['option_name'] == $selectedValue){
								$input->selectOptionByValue($selectedValue);
								$isOther = false;
							}
						}
						if($isOther){
							$input->selectOptionByValue('Other');
						}

						if ($iCustomField['CustomerCustomFields']['input_type'] == 'selectOther'){
							$input->addOption('Other', 'Other (Fill in below)');

							$otherInput = '<div class="main" style="clear:both;margin-top:.3em;">Other: ' . tep_draw_input_field('customer_custom_field_other[' . $iCustomField['CustomerCustomFields']['field_id'] . ']',($isOther)?$selectedValue:'') . '</div>';
						}
						break;
					case 'text':
						$input = htmlBase::newElement('input');
						$input->setValue($selectedValue);
						break;
					case 'textarea':
						$input = htmlBase::newElement('textarea')->attr('rows', 3)->attr('cols', 30);
						$input->html($selectedValue);
						break;
					case 'country':
						    if($selectedValue == ''){
								$selectedValue = sysConfig::get('ONEPAGE_DEFAULT_COUNTRY');
							}
							$input = htmlBase::newElement('selectbox')
							->attr('id','country_'.$fieldId)
							->addClass('countryExtraCustomer');

								$Qcountries = Doctrine_Query::create()
								->select('countries_id, countries_name')
								->from('Countries')
								->orderBy('countries_name')
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

								foreach($Qcountries as $cInfo){
									$input->addOption(
										$cInfo['countries_id'],
										$cInfo['countries_name']
									);
								}
								$input->selectOptionByValue($selectedValue);

						break;
					case 'state':
						$input = htmlBase::newElement('input')
						->attr('id','state_'.$fieldId)
						->addClass('stateExtraCustomer');
						$input->setValue($selectedValue);
						break;
				}
					$input->setName('customer_custom_field[' . $iCustomField['CustomerCustomFields']['field_id'] . ']');

					echo $input->draw().(isset($otherInput) ? $otherInput : '');

					?></td>
				<td class="status"></td>
			</tr>
			<?php
			}
			?>
		</table>
	</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$('.countryExtraCustomer').live('change', function (){

					//var fieldIdArr = $(this).attr('id').split('_');
					//alert(fieldIdArr);
					var $stateObj = $(this).parent().parent().parent().find('.stateExtraCustomer');

					var stateId = $stateObj.attr('id');
					var $stateColumn = $('#'+stateId);
					var stateName = $stateColumn.attr('name');
					var stateVal = $stateColumn.val();
					if($stateColumn.size() > 0){
						//showAjaxLoader($stateColumn, 'large');
						var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
						$.ajax({
							url: js_catalog_app_link(linkParams + 'rType=ajax&appExt=customerCustomFields&app=extraFunctions&appPage=default&action=getCountryZones'),
							cache: false,
							dataType: 'html',
							data: 'cID=' + $(this).val()+'&state_id='+stateId+'&state_name='+stateName+'&state_val='+stateVal,
							success: function (data){
								//removeAjaxLoader($stateColumn);
								$('#'+stateId).replaceWith(data);
							}
						});
					}
				});
				$('.countryExtraCustomer').trigger('change');
			});
		</script>
		<?php
		}
		$html = ob_get_contents();
		ob_end_clean();
        return $html;
    }
}
?>