<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<form name="return_reservations" action="<?php echo itw_app_link('appExt=payPerRentals&action=return');?>" method="post">
<?php
	$table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->attr('align', 'center')
	->css(array(
		'width' => '50%'
	))
	->addClass('ui-widget');
	
	$table->addHeaderRow(array(
		'columns' => array(
			array('addCls' => 'ui-widget-header', 'css' => array('border-right' => 'none'), 'text' => sysLanguage::get('TABLE_HEADING_BARCODE')),
			array('addCls' => 'ui-widget-header', 'css' => array('border-right' => 'none'), 'text' => sysLanguage::get('TABLE_HEADING_COMMENTS')),
			array('addCls' => 'ui-widget-header', 'text' => sysLanguage::get('TABLE_HEADING_BROKEN'))
		)
	));
	
	for($i=0;$i<10;$i++){
		$table->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none', 'border-right' => 'none'), 'align' => 'center', 'text' => tep_draw_input_field('barcode[]')),
				array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none', 'border-right' => 'none'), 'align' => 'center', 'text' => tep_draw_textarea_field('comment[]',true,30,2)),
				array('addCls' => 'ui-widget-content', 'css' => array('border-top' => 'none'), 'align' => 'center', 'text' => tep_draw_checkbox_field('broken[]','1'))
			)
		));
	}
	
	$returnButton = htmlBase::newElement('button')
	->setType('submit')
	->usePreset('save')
	->setText(sysLanguage::get('TEXT_BUTTON_RETURN'))
	->draw();
	
	$table->addBodyRow(array(
		'columns' => array(
			array('colspan' => 3, 'align' => 'center', 'text' => $returnButton)
		)
	));
	
	echo $table->draw();
?>
</form>