<?php
	class ShoppingCart_productDesigner {
		
		public function __construct(){
		}
		
		public function init(){
			EventManager::attachEvents(array(
				'AddToCartBeforeAction'
			), 'ShoppingCart', $this);
		}
		
		public function AddToCartBeforeAction(&$pID_info, &$pInfo, &$cartProduct){
			if (isset($_POST['predesign_front']) || isset($_POST['predesign_back'])){
				$predesignInfo = array();
			
				if (isset($_POST['product_designer_image_set'])){
					$predesignInfo['images_id'] = $_POST['product_designer_image_set'];
				}
			
				if (isset($_POST['predesign_front'])){
					$predesignInfo['front'] = (int)$_POST['predesign_front'];
				
					$Qcost = Doctrine_Query::create()
					->select('predesign_cost')
					->from('ProductDesignerPredesigns')
					->where('predesign_id = ?', $predesignInfo['front'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qcost){
						$predesignInfo['front_cost'] = $Qcost[0]['predesign_cost'];
					}
				}
			
				if (isset($_POST['predesign_back'])){
					$predesignInfo['back'] = $_POST['predesign_back'];
				
					$Qcost = Doctrine_Query::create()
					->select('predesign_cost')
					->from('ProductDesignerPredesigns')
					->where('predesign_id = ?', $predesignInfo['back'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qcost){
						$predesignInfo['back_cost'] = $Qcost[0]['predesign_cost'];
					}
				}
			
				if (isset($_POST['school_year'])){
					$predesignInfo['school_year'] = $_POST['school_year'];
				}
			
				if (isset($_POST['player_number'])){
					$predesignInfo['player_number'] = $_POST['player_number'];
				}
			
				if (isset($_POST['player_name'])){
					$predesignInfo['player_name'] = $_POST['player_name'];
				}
			
				if (isset($_POST['primary_color'])){
					$predesignInfo['primary_color'] = $_POST['primary_color'];
				}
			
				if (isset($_POST['secondary_color'])){
					$predesignInfo['secondary_color'] = $_POST['secondary_color'];
				}

				if ($cartProduct){
					$cartProduct->updateInfo(array(
						'predesign' => $predesignInfo
					));
				}else{
					$pInfo['predesign'] = $predesignInfo;
				}
			}
		
			if (isset($_POST['item'])){
				$cartProduct->updateInfo(array(
					'custom_design' => $_POST['item']
				));
			}
		}
	}
?>