<?php
	$multiStore = $appExtension->getExtension('multiStore');
	Doctrine_Query::create()
	->delete('StoresTimeFees')
	->where('timefees_id = ?', $timefees->timefees_id)
	->execute();
	$stores1 = $multiStore->getStoresArray();
	foreach($stores1->toArray(true) as $sInfo){
		$sID = $sInfo['stores_id'];
		$StoresTimeFees = new StoresTimeFees();
		$StoresTimeFees->stores_id = $sID;
		$StoresTimeFees->timefees_id = $timefees->timefees_id;
		$StoresTimeFees->show_method = $_POST['store_show_method'][$sID];

		if ($_POST['store_show_method'][$sID] == 'use_custom'){
			$StoresTimeFees->timefees_name = $_POST['timefees_name_'.$sID];
			$StoresTimeFees->timefees_fee = $_POST['timefees_fee_'.$sID];
			$StoresTimeFees->timefees_start = $_POST['timefees_start_'.$sID];
			$StoresTimeFees->timefees_end = $_POST['timefees_end_'.$sID];
		}else{
			$StoresTimeFees->timefees_name = $_POST['timefees_name'];
			$StoresTimeFees->timefees_fee = $_POST['timefees_fee'];
			$StoresTimeFees->timefees_start = $_POST['timefees_start'];
			$StoresTimeFees->timefees_end = $_POST['timefees_end'];
		}
		$StoresTimeFees->save();
	}

?>