<?php
	$multiStore = $appExtension->getExtension('multiStore');
	Doctrine_Query::create()
	->delete('StoresExtraFees')
	->where('timefees_id = ?', $timefees->timefees_id)
	->execute();
	$stores1 = $multiStore->getStoresArray();
	foreach($stores1->toArray(true) as $sInfo){
		$sID = $sInfo['stores_id'];
		$StoresExtraFees = new StoresExtraFees();
		$StoresExtraFees->stores_id = $sID;
		$StoresExtraFees->timefees_id = $timefees->timefees_id;
		$StoresExtraFees->show_method = $_POST['store_show_method'][$sID];

		if ($_POST['store_show_method'][$sID] == 'use_custom'){
			$StoresExtraFees->timefees_name = $_POST['timefees_name_'.$sID];
			$StoresExtraFees->timefees_description = $_POST['timefees_description_'.$sID];
			$StoresExtraFees->timefees_fee = $_POST['timefees_fee_'.$sID];
			$StoresExtraFees->timefees_hours = $_POST['timefees_hours_'.$sID];
			$tfMandatory = 0;
			if(isset($_POST['timefees_mandatory_'.$sID])){
				$tfMandatory = 1;
			}
			$StoresExtraFees->timefees_mandatory = $tfMandatory;
		}else{
			$StoresExtraFees->timefees_name = $_POST['timefees_name'];
			$StoresExtraFees->timefees_description = $_POST['timefees_description'];
			$StoresExtraFees->timefees_fee = $_POST['timefees_fee'];
			$StoresExtraFees->timefees_hours = $_POST['timefees_hours'];
			$tfMandatory = 0;
			if(isset($_POST['timefees_mandatory'])){
				$tfMandatory = 1;
			}
			$StoresExtraFees->timefees_mandatory = $tfMandatory;

		}
		$StoresExtraFees->save();
	}

?>