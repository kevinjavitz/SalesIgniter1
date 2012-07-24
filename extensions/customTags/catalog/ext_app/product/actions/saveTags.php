<?php
$pID = $_GET['products_id'];
if($userAccount->isLoggedIn()){
		$cID = $userAccount->getCustomerId();
		if(isset($_POST['tags_names']) && !empty($_POST['tags_names'])){
			$tagArr = ParseTagString($_POST['tags_names']);
			foreach($tagArr as $iTag){
				$iTag = trim($iTag);
				$CustomTag = Doctrine_Core::getTable('CustomTags')->findOneByTagName($iTag);
				if(!$CustomTag){
					$CustomTag = new CustomTags();
					$CustomTag->tag_name = $iTag;
					$CustomTag->tag_status = 0;
					$CustomTag->save();
				}
				$tagId = $CustomTag->tag_id;
				$ProductTags = Doctrine_Core::getTable('TagsToProducts')->findOneByTagIdAndProductsIdAndCustomersId($tagId, $pID, $cID);
	            if(!$ProductTags){
		            $ProductTags = new TagsToProducts();
		            $ProductTags->tag_id = $tagId;
		            $ProductTags->products_id = $pID;
		            $ProductTags->customers_id = $cID;
		            $ProductTags->save();
	            }

			}
			$messageStack->addSession('pageStack','Product tags added for review');
		}
	}
	EventManager::attachActionResponse(itw_app_link('products_id='.$pID,'product', 'info'), 'redirect');
?>