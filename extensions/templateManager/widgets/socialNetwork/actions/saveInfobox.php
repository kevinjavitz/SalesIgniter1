<?php

	$multiStore = $appExtension->getExtension('multiStore');
	if ($multiStore !== false){
		$stores = $multiStore->getStoresArray();
		foreach($stores as $sInfo){
			$fbVar = 'facebook'.$sInfo['stores_id'];
			$gpVar = 'googlePlus'.$sInfo['stores_id'];
			$ttVar = 'twitter'.$sInfo['stores_id'];
			$liVar = 'linked'.$sInfo['stores_id'];
			$btVar = 'beforeText'.$sInfo['stores_id'];
			$emVar = 'email'.$sInfo['stores_id'];
			if(isset($_POST[$fbVar])){
				$WidgetProperties[$fbVar] = $_POST[$fbVar];
			}
			if(isset($_POST[$gpVar])){
				$WidgetProperties[$gpVar] = $_POST[$gpVar];
			}
			if(isset($_POST[$ttVar])){
				$WidgetProperties[$ttVar] = $_POST[$ttVar];
			}
			if(isset($_POST[$liVar])){
				$WidgetProperties[$liVar] = $_POST[$liVar];
			}
			if(isset($_POST[$btVar])){
				$WidgetProperties[$btVar] = $_POST[$btVar];
			}
			if(isset($_POST[$emVar])){
				$WidgetProperties[$emVar] = $_POST[$emVar];
			}
		}
	}else{

		if(isset($_POST['facebook'])){
			$WidgetProperties['facebook'] = $_POST['facebook'];
		}

		if(isset($_POST['googlePlus'])){
			$WidgetProperties['googlePlus'] = $_POST['googlePlus'];
		}
		if(isset($_POST['twitter'])){
			$WidgetProperties['twitter'] = $_POST['twitter'];
		}
		if(isset($_POST['linked'])){
			$WidgetProperties['linked'] = $_POST['linked'];
		}
		if(isset($_POST['beforeText'])){
			$WidgetProperties['beforeText'] = $_POST['beforeText'];
		}
		if(isset($_POST['email'])){
			$WidgetProperties['email'] = $_POST['email'];
		}
	}
?>