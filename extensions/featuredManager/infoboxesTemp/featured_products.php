<?php
/*
$Id: featured_products.php,v 1.34 2003/06/09 22:49:58 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/
?>
<!-- featured_products //-->
<?php
	$featuredProducts = $storeProducts->getFeatured();
	if ($featuredProducts){
		$mainDiv = htmlBase::newElement('div')->addClass('ui-widget ui-featured-products-container');
		
		$info_box_contents = array();
		foreach($featuredProducts as $pInfo){
			$link = itw_app_link('products_id=' . $pInfo['id'], 'product', 'info');
			
			$tableBlock = htmlBase::newElement('table')->setCellPadding(0)->setCellSpacing(0)
			->addClass('ui-widget-content ui-corner-all ui-featured-product-container');
			
			$tableBlock->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'ui-featured-product-image',
						'text'   => '<a href="' . $link . '">' . $pInfo['image'] . '</a>'
					)
				)
			));
			
			$tableBlock->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'ui-featured-product-name',
						'text'   => '<a href="' . $link . '">' . $pInfo['name'] . '</a>'
					)
				)
			));
			
			/*$tableBlock->addBodyRow(array(
				'columns' => array(
					array(
						'addCls' => 'ui-featured-product-price',
						'text'   => '<span style="font-size:1.3em;font-weight:bold;">' . $pInfo['price'] . '</span>'
					)
				)
			));*/
			
			$mainDiv->append($tableBlock);
		}
		
		$boxTemplate = new Template('featured_products.tpl', 'modules');
		
		$boxTemplate->setVars(array(
			'boxHeading' => sysLanguage::get('CONTENTBOX_HEADING_FEATURED') . '<span style="color:#01833b;font-size:.8em;margin-left:3px;">  ' . TEXT_CLICK_IMAGE . '</span>',
			'boxContent' => $mainDiv->draw()
		));

		echo $boxTemplate->parse();
	}
?>
<!-- featured_products_eof //-->