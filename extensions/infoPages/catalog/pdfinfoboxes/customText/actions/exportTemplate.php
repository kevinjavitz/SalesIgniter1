<?php

$Pages = Doctrine_Core::getTable('Pages');

$PagesDescription = Doctrine_Core::getTable('PagesDescription');



$Settings = json_decode($Widget->Configuration['widget_settings']->configuration_value);



$InfoPage = $Pages->find($Settings->selected_page);



$PageType = $InfoPage->page_type;

$PageStatus = $InfoPage->status;

$PageKey = $InfoPage->page_key;

?>

$Pages = Doctrine_Core::getTable('Pages');

$PagesDescription = Doctrine_Core::getTable('PagesDescription');



$Page = $Pages->findOneByPageKey('<?php echo $InfoPage->page_key;?>');

if (!$Page){

$Page = $Pages->create();

<?php

foreach($Pages->getColumns() as $colName => $cInfo){

	if (

		$colName == 'pages_id'

	) continue;

	?>

$Page-><?php echo $colName;?> = '<?php echo $InfoPage->$colName;?>';

<?php

		}

?>



$PageDescription = $PagesDescription->create();

<?php

$Descriptions = $InfoPage->PagesDescription;

foreach($Descriptions as $InfoPageDescription){

	if ($InfoPageDescription->language_id != 1) continue;



	foreach($PagesDescription->getColumns() as $colName => $cInfo){

		if (

			$colName == 'pages_id' ||

			$colName == 'id'

		) continue;

		?>

	$PageDescription-><?php echo $colName;?> = '<?php echo cleanString($InfoPageDescription->$colName);?>';

	<?php

 			}

}

?>



$Page->PagesDescription->add($PageDescription);

$Page->save();

}

$WidgetProperties->selected_page = $Page->pages_id;

