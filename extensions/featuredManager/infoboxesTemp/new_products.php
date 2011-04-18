<?php
/*
$Id: new_products.php,v 1.34 2003/06/09 22:49:58 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/
?>
<!-- new_products //-->
<?php
$newProducts = $storeProducts->getNew((isset($new_products_category_id) && $new_products_category_id > 0 ? $new_products_category_id : null));
if ($newProducts){
	$boxHeading = sprintf(sysLanguage::get('TABLE_HEADING_NEW_PRODUCTS'), strftime('%B'));
	$corner_left = 'square';
	$corner_right = 'square';
	$box_base_name = 'new_products'; // for easy unique box template setup (added BTSv1.2)
	$box_id = $box_base_name . 'Box';  // for CSS styling paulm (editted BTSv1.2)
	
	$row = 0;
	$col = 0;
	$info_box_contents = array();
	foreach($newProducts as $pInfo){
		$link = itw_app_link('products_id=' . $pInfo['id'], 'product', 'info');

		$mprice = '';
		if($pInfo['price']){
			$mprice = $currencies->display_price( $pInfo['price'],$pInfo['taxRate']);
		}

		$info_box_contents[$row][$col] = array(
			'align' => 'center',
			'params' => 'class="smallText" width="33%" valign="top"',
			'text' => '<a href="' . $link . '">' . $pInfo['image'] . '</a><br><a href="' . $link . '">' . $pInfo['name'] . '</a><br /><span style="font-size:1.3em;font-weight:bold;">' . $mprice . '</span>'
		);
		
		$col ++;
		if ($col > 2){
			$col = 0;
			$row ++;
		}
	}
	
	$boxContent = '<table cellpadding="3" cellspacing="0" border="0" width="100%">';
	for($i=0, $n=sizeof($info_box_contents); $i<$n; $i++){
		$boxContent .= '<tr>';
		for($j=0, $k=sizeof($info_box_contents[$i]); $j<$k; $j++){
			$boxContent .= '<td ' . $info_box_contents[$i][$j]['params'] . ' align="' . $info_box_contents[$i][$j]['align'] . '">' . $info_box_contents[$i][$j]['text'] . '</td>';
		}
		$boxContent .= '</tr>';
	}
	$boxContent .= '</table>';
	
	$boxTemplate = new Template('module.tpl', 'modules');
		
	$boxTemplate->setVars(array(
		'boxHeading' => sprintf(sysLanguage::get('TABLE_HEADING_NEW_PRODUCTS'), strftime('%B')),
		'boxContent' => $boxContent
	));

	echo $boxTemplate->parse();
}
?>
<!-- new_products_eof //-->
