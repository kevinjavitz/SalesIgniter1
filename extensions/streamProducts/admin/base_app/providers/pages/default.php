<?php
	$Qproviders = Doctrine_Query::create()
	->from('ProductsStreamProviders');
	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qproviders);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PROVIDER_NAME')),
			array('text' => sysLanguage::get('TABLE_HEADING_PROVIDER_MODULE'))/*,
			array('text' => 'Sort Order'),
			array('text' => 'info')*/
		)
	));
	
	$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Install Modules')->setHref(itw_app_link('appExt=streamProducts', 'providers', 'modules')),
		htmlBase::newElement('button')->setText('New')->addClass('newButton'),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable(),
		htmlBase::newElement('button')->setText('Delete')->addClass('deleteButton')->disable()
	));

   	$Result = &$tableGrid->getResults();
	if ($Result){
		foreach($Result as $pInfo){
			$providerId = $pInfo['provider_id'];
			$providerName = $pInfo['provider_name'];
			$providerModule = $pInfo['provider_module'];
			
			//$arrowIcon = htmlBase::newElement('icon')->setType('info');

			$tableGrid->addBodyRow(array(
				'rowAttr' => array(
					'data-provider_id' => $providerId
				),
				'columns' => array(
					array('text' => $providerName),
					array('text' => $providerModule)/*,
					array('text' => $arrowIcon->draw(), 'align' => 'right')*/
				)
			));
		}
	}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_PROVIDERS');
?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>