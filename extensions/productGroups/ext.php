<?php
/*
	Related Products Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_productGroups extends ExtensionBase {

	public function __construct(){
		parent::__construct('productGroups');
	}

	public function init(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
		  'CanAddToQueueProduct',
		'GetUserMembershipPlanInfo'
		), null, $this);
	}

	public function GetUserMembershipPlanInfo(&$planInfo, $Qmembership){
		$planInfo['ppr_prod_group'] = $Qmembership[0]['ppr_prod_group'];
	}

	public function CanAddToQueueProduct($pid, $cartProduct, &$canAdd){
		global $ShoppingCart, $messageStack, $userAccount;

		/*//I need to check the group of this product
		//then the queue of the user and get the products_id and check how many are in the group... if the number is bigger then the limit of the group then show error message
		$QProductGroup = Doctrine_Query::create()
		->from('ProductsGroups')
		->where('FIND_IN_SET('.$pid.',products) > 0')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$productArr = explode(',',$QProductGroup[0]['products']);
		$limit = $QProductGroup[0]['product_group_limit'];
		$pName = $QProductGroup[0]['product_group_name'];
		if ($ShoppingCart->countContentsQueue() > 0) {
			foreach($ShoppingCart->getProductsQueue() as $cartProduct) {
				$pID_string = $cartProduct->getIdString();
				$quantity = $cartProduct->getQuantity();
				if(in_array($pID_string, $productArr)){
					$limit = $limit - $quantity;
					if($limit <= 0){
						$messageStack->addSession('pageStack', 'You cannot add this product into queue because is limit by product group: '. $pName);
						$canAdd = false;
						break;
					}
				}
			}
		}  */

		//i have to check what membership the user have and get the product group
		$membership =& $userAccount->plugins['membership'];
		$planInfo = $membership->getAllPlanInfo();

		$pprG = unserialize($planInfo['ppr_prod_group']);
		$QProductGroup = Doctrine_Query::create()
		->from('ProductsGroups')
		->where('FIND_IN_SET('.$pid.',products) > 0')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$productArr = explode(',',$QProductGroup[0]['products']);
		$limit = $pprG[$QProductGroup[0]['product_group_id']];
		$pName = $QProductGroup[0]['product_group_name'];
		if ($ShoppingCart->countContentsQueue() > 0) {
			foreach($ShoppingCart->getProductsQueue() as $cartProduct) {
				$pID_string = $cartProduct->getIdString();
				$quantity = $cartProduct->getQuantity();
				if(in_array($pID_string, $productArr)){
					$limit = $limit - $quantity;
					if($limit <= 0){
						$messageStack->addSession('pageStack', 'You cannot add this product into queue because is limit by product group: '. $pName);
						$canAdd = false;
						break;
					}
				}
			}
		}
		if($limit <= 0){
			$messageStack->addSession('pageStack', 'You cannot add this product into queue because is limit by product group: '. $pName);
			$canAdd = false;
		}
	}

}
?>
