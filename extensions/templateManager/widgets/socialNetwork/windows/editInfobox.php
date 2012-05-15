<?php

$multiStore = $appExtension->getExtension('multiStore');
if ($multiStore !== false){
$tabs = '<div id="tab_container"><ul>';
		$stores = $multiStore->getStoresArray();
		foreach($stores as $sInfo){
			$tabs .= '<li class="ui-tabs-nav-item"><a href="#page-'.$sInfo['stores_id'].'"><span>'.$sInfo['stores_name'].'</span></a></li>';
		}
$tabs .= '</ul>';

	foreach($stores as $sInfo){
		$editTable = htmlBase::newElement('table')
			->setCellPadding(2)
			->setCellSpacing(0);
		$fbVar = 'facebook'.$sInfo['stores_id'];
		$gpVar = 'googlePlus'.$sInfo['stores_id'];
		$ttVar = 'twitter'.$sInfo['stores_id'];
		$liVar = 'linked'.$sInfo['stores_id'];
		$btVar = 'beforeText'.$sInfo['stores_id'];
		$emVar = 'email'.$sInfo['stores_id'];

		$selectedFacebook = (isset($WidgetSettings->$fbVar)?$WidgetSettings->$fbVar:(isset($WidgetSettings->facebook)?$WidgetSettings->facebook:''));
		$selectedGooglePlus = (isset($WidgetSettings->$gpVar)?$WidgetSettings->$gpVar:(isset($WidgetSettings->googlePlus)?$WidgetSettings->googlePlus:''));
		$selectedTwitter = (isset($WidgetSettings->$ttVar)?$WidgetSettings->$ttVar:(isset($WidgetSettings->twitter)?$WidgetSettings->twitter:''));
		$selectedLinked = (isset($WidgetSettings->$liVar)?$WidgetSettings->$liVar:(isset($WidgetSettings->linked)?$WidgetSettings->linked:''));
		$selectedBeforeText = (isset($WidgetSettings->$btVar)?$WidgetSettings->$btVar:(isset($WidgetSettings->beforeText)?$WidgetSettings->beforeText:''));
		$selectedEmail = (isset($WidgetSettings->$emVar)?$WidgetSettings->$emVar:(isset($WidgetSettings->email)?$WidgetSettings->email:''));

		$linkFacebook = htmlBase::newElement('input')
			->setName($fbVar)
			->setValue($selectedFacebook);

		$linkGooglePlus = htmlBase::newElement('input')
			->setName($gpVar)
			->setValue($selectedGooglePlus);

		$linkLinked = htmlBase::newElement('input')
			->setName($liVar)
			->setValue($selectedLinked);

		$beforeText = htmlBase::newElement('input')
			->setName($btVar)
			->setValue($selectedBeforeText);

		$linkTwitter = htmlBase::newElement('input')
			->setName($ttVar)
			->setValue($selectedTwitter);

		$linkEmail = htmlBase::newElement('input')
			->setName($emVar)
			->setValue($selectedEmail);

		$editTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Before Text:'),
					array('text' => $beforeText->draw())
				)
			));

		$editTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Twitter Link:'),
					array('text' => $linkTwitter->draw())
				)
			));

		$editTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Facebook Link:'),
					array('text' => $linkFacebook->draw())
				)
			));

		$editTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Google Plus Link:'),
					array('text' => $linkGooglePlus->draw())
				)
			));

		$editTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Linkedin Link:'),
					array('text' => $linkLinked->draw())
				)
			));

		$editTable->addBodyRow(array(
				'columns' => array(
					array('text' => 'Email Link:'),
					array('text' => $linkEmail->draw())
				)
			));
		$tabs .= '<div id="page-'.$sInfo['stores_id'].'">'.$editTable->draw().'</div>';
	}

$tabs .= '</div>';

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('colspan' => 2, 'text' => '<b>Social Networks Properties</b>')
		)
));


	ob_start();
?>
<script src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/ui/jquery.ui.tabs.js"></script>
<script type="text/javascript">

$(document).ready(function () {
	$('#tab_container').tabs();
});
</script>
<?php

	$tabs .= ob_get_contents();
	ob_end_clean();
	$WidgetSettingsTable->addBodyRow(array(
				'columns' => array(
					array('colspan' => 2, 'text' => $tabs)
				)
	));
	}else{
	$editTable = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0);

	$selectedFacebook = (isset($WidgetSettings->facebook)?$WidgetSettings->facebook:'');
	$selectedGooglePlus = (isset($WidgetSettings->googlePlus)?$WidgetSettings->googlePlus:'');
	$selectedTwitter = (isset($WidgetSettings->twitter)?$WidgetSettings->twitter:'');
	$selectedLinked = (isset($WidgetSettings->linked)?$WidgetSettings->linked:'');
	$selectedBeforeText = (isset($WidgetSettings->beforeText)?$WidgetSettings->beforeText:'');
	$selectedEmail = (isset($WidgetSettings->email)?$WidgetSettings->email:'');

	$linkFacebook = htmlBase::newElement('input')
		->setName('facebook')
		->setValue($selectedFacebook);

	$linkGooglePlus = htmlBase::newElement('input')
		->setName('googlePlus')
		->setValue($selectedGooglePlus);

	$linkLinked = htmlBase::newElement('input')
		->setName('linked')
		->setValue($selectedLinked);

	$beforeText = htmlBase::newElement('input')
		->setName('beforeText')
		->setValue($selectedBeforeText);

	$linkTwitter = htmlBase::newElement('input')
		->setName('twitter')
		->setValue($selectedTwitter);

	$linkEmail = htmlBase::newElement('input')
		->setName('email')
		->setValue($selectedEmail);

	$editTable->addBodyRow(array(
			'columns' => array(
				array('text' => 'Before Text:'),
				array('text' => $beforeText->draw())
			)
		));

	$editTable->addBodyRow(array(
			'columns' => array(
				array('text' => 'Twitter Link:'),
				array('text' => $linkTwitter->draw())
			)
		));

	$editTable->addBodyRow(array(
			'columns' => array(
				array('text' => 'Facebook Link:'),
				array('text' => $linkFacebook->draw())
			)
		));

	$editTable->addBodyRow(array(
			'columns' => array(
				array('text' => 'Google Plus Link:'),
				array('text' => $linkGooglePlus->draw())
			)
		));

	$editTable->addBodyRow(array(
			'columns' => array(
				array('text' => 'Linkedin Link:'),
				array('text' => $linkLinked->draw())
			)
		));

	$editTable->addBodyRow(array(
			'columns' => array(
				array('text' => 'Email Link:'),
				array('text' => $linkEmail->draw())
			)
		));
	$WidgetSettingsTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 2, 'text' => '<b>Social Networks Properties</b>')
			)
		));

	$WidgetSettingsTable->addBodyRow(array(
			'columns' => array(
				array('colspan' => 2, 'text' => $editTable->draw())
			)
		));
}
?>