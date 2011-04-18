<?php
	//$Category->related_products = '';
	if (isset($_GET['cID']) && isset($_POST['categories'])){
		$Qrelation = Doctrine_Query::create()
					->from('ForcedSetCategories')
					->where('forced_set_category_one_id = ?', $_GET['cID'])
					->orWhere('forced_set_category_two_id = ?', $_GET['cID'])
					->execute();

		if ($Qrelation){
			if ($Qrelation[0]->forced_set_category_one_id == $_GET['cID']){
				if(isset($_POST['categories']) && !empty($_POST['categories'])){
					$Qrelation[0]->forced_set_category_two_id = $_POST['categories'][0];
					$Qrelation[0]->forced_set_category_one_id = $_GET['cID'];
				}
			}else{
				if(isset($_POST['categories']) && !empty($_POST['categories'])){
					$Qrelation[0]->forced_set_category_one_id = $_POST['categories'][0];
					$Qrelation[0]->forced_set_category_two_id = $_GET['cID'];
				}
			}
			$Qrelation[0]->save();
		}else{
			$Qrelation = new ForcedSetCategories();
			if(isset($_POST['categories']) && !empty($_POST['categories'])){
					$Qrelation->forced_set_category_one_id = $_POST['categories'][0];
					$Qrelation->forced_set_category_two_id = $_GET['cID'];
			}
			$Qrelation->save();
		}		
	}else{
		if(isset($_POST['categories']) && !empty($_POST['categories'])){
			$messageStack->addSession('pageStack', 'Relation can be added only on edit', 'error');
		}
	}

?>