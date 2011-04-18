<?php
	$editTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0);
	
	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<b>Scroller Properties</b>')
		)
	));

 	$selectedText = isset($Infobox['widget_properties']['nr_imag'])?$Infobox['widget_properties']['nr_imag']:'';
	$nr_space = isset($Infobox['widget_properties']['nr_space'])?$Infobox['widget_properties']['nr_space']:'';

	$newNr = isset($Infobox['widget_properties']['nr_new'])?$Infobox['widget_properties']['nr_new']:'';
	$bestNr = isset($Infobox['widget_properties']['nr_best'])?$Infobox['widget_properties']['nr_best']:'';
	$featNr = isset($Infobox['widget_properties']['nr_feat'])?$Infobox['widget_properties']['nr_feat']:'';

	$newText = isset($Infobox['widget_properties']['new_text'])?$Infobox['widget_properties']['new_text']:'';
	$bestText = isset($Infobox['widget_properties']['best_text'])?$Infobox['widget_properties']['best_text']:'';
	$featText = isset($Infobox['widget_properties']['feat_text'])?$Infobox['widget_properties']['feat_text']:'';

	$widthImage = isset($Infobox['widget_properties']['width_imag'])?$Infobox['widget_properties']['width_imag']:'';
	$heightImage = isset($Infobox['widget_properties']['height_imag'])?$Infobox['widget_properties']['height_imag']:'';


	$nrImag = htmlBase::newElement('input')
	->setName('nr_imag')
	->setLabel('Number of products in scroller:')
	->setValue($selectedText)
	->setLabelPosition('before');

	$nrSpace = htmlBase::newElement('input')
	->setName('nr_space')
	->setLabel('Space between products:')
	->setValue($nr_space)
	->setLabelPosition('before');
	$widthImag = htmlBase::newElement('input')
	->setName('width_imag')
	->setLabel('Width of image:')
	->setValue($widthImage)
	->setLabelPosition('before');

	$heightImag = htmlBase::newElement('input')
	->setName('height_imag')
	->setLabel('Height of image:')
	->setValue($heightImage)
	->setLabelPosition('before');

	$nrNew = htmlBase::newElement('input')
	->setName('nr_new')
	->setLabel('Number of new Products:')
	->setValue($newNr)
	->setLabelPosition('before');

	$nrBest = htmlBase::newElement('input')
	->setName('nr_best')
	->setLabel('Number of Best Seller Products:')
	->setValue($bestNr)
	->setLabelPosition('before');

	$nrFeat = htmlBase::newElement('input')
	->setName('nr_feat')
	->setLabel('Number of Featured Products:')
	->setValue($featNr)
	->setLabelPosition('before');

	$featText = htmlBase::newElement('input')
	->setName('feat_text')
	->setLabel('Featured Products Text:')
	->setValue($featText)
	->setLabelPosition('before');

	$bestText = htmlBase::newElement('input')
	->setName('best_text')
	->setLabel('BestSeller Products Text:')
	->setValue($bestText)
	->setLabelPosition('before');

	$newText = htmlBase::newElement('input')
	->setName('new_text')
	->setLabel('New Products Text:')
	->setValue($newText)
	->setLabelPosition('before');


	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $nrImag->draw())
		)
	));
	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $nrSpace->draw())
		)
	));
	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $widthImag->draw())
		)
	));
	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $heightImag->draw())
		)
	));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $nrNew->draw())
		)
	));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $nrBest->draw())
		)
	));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $nrFeat->draw())
		)
	));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $featText->draw())
		)
	));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $bestText->draw())
		)
	));

	$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => $newText->draw())
		)
	));


	
	echo $editTable->draw();
?>