<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Qpages = Doctrine_Query::create()
	->select('p.*, pd.pages_title')
	->from('Pages p')
	->leftJoin('p.PagesDescription pd')
	->where('pd.language_id = ?', Session::get('languages_id'))
	->orderBy('p.sort_order, pd.pages_title');
	
	$tableGrid2 = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0)
	->css(array(
		'width' => '100%'
	));

	$tableGrid2->addHeaderRow(array(
		'columns' => array(
			array(
				'addCls' => 'ui-widget-header',
				'text' => 'Content Type',
				'css' => array(
					'border-right' => 'none'
				)
			),
			array(
				'addCls' => 'ui-widget-header',
				'text' => 'Action'
			)
		)
	));

	$css = array(
		'font-size' => '.7em'
	);
	
	$tableGrid2->addBodyRow(array(
		'addCls' => 'ui-state-default',
		'columns' => array(
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 0px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'text' => 'Custom Fields Page'
			),
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 1px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'align' => 'right',
				'text' => htmlBase::newElement('button')->css($css)->setText('New')->setHref(itw_app_link('appExt=infoPages', 'manage', 'newFieldPage'))->draw() .
				          htmlBase::newElement('button')->addClass('actionButton')->css($css)->setText('Open')->attr('action', 'getFieldsListing')->draw()
			)
		)
	));

	$tableGrid2->addBodyRow(array(
		'addCls' => 'ui-state-default',
		'columns' => array(
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 0px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'text' => 'Content Blocks'
			),
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 1px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'align' => 'right',
				'text' => htmlBase::newElement('button')->css($css)->setText('New')->setHref(itw_app_link('appExt=infoPages', 'manage', 'newContentBlock'))->draw() .
				          htmlBase::newElement('button')->addClass('actionButton')->css($css)->setText('Open')->attr('action', 'getBlocksListing')->draw()
			)
		)
	));
	
	$tableGrid2->addBodyRow(array(
		'addCls' => 'ui-state-default',
		'columns' => array(
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 0px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'text' => 'Pages'
			),
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 1px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'align' => 'right',
				'text' => htmlBase::newElement('button')->css($css)->setText('New')->setHref(itw_app_link('appExt=infoPages', 'manage', 'newPage'))->draw() .
				          htmlBase::newElement('button')->addClass('actionButton')->css($css)->setText('Open')->attr('action', 'getPagesListing')->draw()
			)
		)
	));

	$tableGrid2->addBodyRow(array(
		'addCls' => 'ui-state-default',
		'columns' => array(
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 0px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'text' => 'Popups'
			),
			array(
				'addCls' => 'main',
				'css' => array(
					'border-width' => '0px 1px 1px 1px',
					'border-style' => 'solid',
					'border-color' => '#AAAAAA'
				),
				'align' => 'right',
				'text' => htmlBase::newElement('button')->css($css)->setText('New')->setHref(itw_app_link('appExt=infoPages', 'manage', 'newPopup'))->draw() .
				          htmlBase::newElement('button')->addClass('actionButton')->css($css)->setText('Open')->attr('action', 'getPopupsListing')->draw()
			)
		)
	));
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:98%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;"><?php echo $tableGrid2->draw();?></div>
   <br />
   <div style="width:99%;margin:5px;" class="gridHolder"></div>
  </div>
 </div>
