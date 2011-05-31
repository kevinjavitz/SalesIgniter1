<?php
	class ShoppingCart_forcedSet {
		
		public function __construct(){
		}
		
		public function init(){

			EventManager::attachEvents(array(
				'AddToCartAfterAction',
				'AddToCartBeforeAction'
			), 'ShoppingCart', $this);
		}

		public function AddToCartBeforeAction($pID_info, &$pInfo, &$cartProduct){
			
		}
		                   
		public function AddToCartAfterAction(&$pID_info, &$pInfo, &$cartProduct){
			global $messageStack, $ShoppingCart, $appExtension;

			$pID = $pInfo['id_string'];;
			
			if(sysConfig::get('EXTENSION_FORCED_SET_USE_CATEGORIES') == 'True'){
				$wheelType = '';

				$CustomFields = $appExtension->getExtension('customFields');
				if ($CustomFields !== false && $CustomFields->isEnabled() === true){

					$groups = $CustomFields->getFields($pID, Session::get('languages_id'), false, false, false);

					foreach($groups as $groupInfo){
						$fieldsToGroups = $groupInfo['ProductsCustomFieldsToGroups'];
						foreach($fieldsToGroups as $fieldToGroup){
							$name = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];
							if ($name == 'Wheel Type'){
								$wheelType = $fieldToGroup['ProductsCustomFields']['ProductsCustomFieldsToProducts'][0]['value'];
								break;
							}
						}
						if (!empty($wheelType)){
							break;
						}
					}
				}

			   	if (!empty($wheelType)){
					$QcustomSet = Doctrine_Query::create()
					->from('ForcedSetRelations')
					->where('forced_set_custom_field_one = ?', $wheelType)
					->orWhere('forced_set_custom_field_two = ?', $wheelType)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if (count($QcustomSet)){
						if ($QcustomSet[0]['forced_set_custom_field_one'] == $wheelType){
							$wheelTypeReverse = $QcustomSet[0]['forced_set_custom_field_two'];
						}else{
							$wheelTypeReverse = $QcustomSet[0]['forced_set_custom_field_one'];
						}
					}
				}
				$QcategoryCartProduct = Doctrine_Query::create()
				->from('ProductsToCategories')
				->where('products_id = ?', $pID)
				->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

				$cID = $QcategoryCartProduct[0]['categories_id'];
				
				$Qrelation = Doctrine_Query::create()
				->from('ForcedSetCategories')
				->where('forced_set_category_one_id = ?', $cID)
				->orWhere('forced_set_category_two_id = ?', $cID)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				                      
				if (count($Qrelation) > 0 && ($ShoppingCart->countContents() % 2 == 1)){					
					$messageStack->addSession('pageStack','You have added your ' . $wheelType . ' successfully, now please select a '. $wheelTypeReverse, 'success');
					if ($Qrelation[0]['forced_set_category_one_id'] == $cID){
						tep_redirect(itw_app_link('cPath='. $Qrelation[0]['forced_set_category_two_id'], 'index', 'default'));
						itwExit();
					}else{
						tep_redirect(itw_app_link('cPath='. $Qrelation[0]['forced_set_category_one_id'], 'index', 'default'));
						itwExit();
					}
				}
            }
		}
	

	}
?>