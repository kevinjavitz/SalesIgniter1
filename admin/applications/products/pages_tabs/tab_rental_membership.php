<?php
	$Qmembership = Doctrine_Query::create()
	->from('Membership m')
	->leftJoin('m.MembershipPlanDescription md')
	->where('md.language_id = ?', Session::get('languages_id'))
	->orderBy('sort_order')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$tableGrid = htmlBase::newElement('table')
 		->setCellPadding(2)
 		->setCellSpacing(0);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_MEMBERSHIP')),
			array('text' => 'Not Enabled For Product')
		)
	));

   $enabledMemberships = explode(';',isset($Product['membership_enabled'])?$Product['membership_enabled']:'');


		foreach($Qmembership as $mInfo){
			$planId = $mInfo['plan_id'];
			$planName = $mInfo['MembershipPlanDescription'][0]['name'];
			$checked = false;

			foreach($enabledMemberships as $checkedMembership){
				if($planId == $checkedMembership){
					$checked = true;
					break;
				}
			}

			$htmlCheckbox = htmlBase::newElement('checkbox')
			->setName('rental_membership_enabled[]')
			->setChecked($checked)
			->setValue($planId);

			$tableGrid->addBodyRow(array(
				'columns' => array(
					array('text' => $planName),
					array('text' => $htmlCheckbox->draw(), 'align' => 'center')
				)
			));
		}
echo $tableGrid->draw();

?>
