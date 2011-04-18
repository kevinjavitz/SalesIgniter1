<?php
	echo '<ul>';
	foreach(sysLanguage::getLanguages() as $lInfo){
		$lID = $lInfo['id'];
		echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lID . '"><span>' . $lInfo['showName']() . '</span></a></li>';
	}
	echo '</ul>';

	foreach(sysLanguage::getLanguages() as $lInfo){
		$lID = $lInfo['id'];

		$ProductsName = htmlBase::newElement('input')
		->setName('products_name[' . $lID . ']');

		$ProductsUrl = htmlBase::newElement('input')
		->setName('products_url[' . $lID . ']');

		$ProductsDescription = htmlBase::newElement('ck_editor')
		->setName('products_description[' . $lID . ']');

		$ProductsSeoUrl = htmlBase::newElement('input')
		->setName('products_seo_url[' . $lID . ']');

		if (isset($Product)){
			$ProductsName->setValue(stripslashes($Product->ProductsDescription[$lID]['products_name']));
			$ProductsUrl->setValue($Product->ProductsDescription[$lID]['products_url']);
			$ProductsDescription->html(stripslashes($Product->ProductsDescription[$lID]['products_description']));
			$ProductsSeoUrl->setValue($Product->ProductsDescription[$lID]['products_seo_url']);
		}
?>
<div id="langTab_<?php echo $lID;?>">
 <table cellpadding="0" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_NAME'); ?></td>
   <td class="main"><?php echo $ProductsName->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_URL') . '<br><small>' . sysLanguage::get('TEXT_PRODUCTS_URL_WITHOUT_HTTP') . '</small>'; ?></td>
   <td class="main"><?php echo $ProductsUrl->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_PRODUCTS_DESCRIPTION'); ?></td>
   <td class="main"><?php echo $ProductsDescription->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td colspan="2" class="main"><hr><?php echo sysLanguage::get('TEXT_PRODUCT_METTA_INFO'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_PRODUCTS_SEO_URL'); ?></td>
   <td class="main"><?php echo $ProductsSeoUrl->draw(); ?></td>
  </tr>


  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>

<?php

	/**
	 * this event expects an array having two elements: label and content | i.e. (array(label=>'', content=>''))
	 */
	$contents_middle = array();
	EventManager::notify('ProductsFormMiddle', $lID, &$contents_middle);

	if (is_array($contents_middle)) {
    	foreach($contents_middle as $element){
			if (is_array($element)) {
				if (!isset($element['label'])) $element['label'] = 'no_defined';
				if (!isset($element['content'])) $element['content'] = 'no_defined';

				echo sprintf(
					'<tr>
						<td class="main">%s</td>
						<td class="main">%s</td>
					</tr>
					',
					$element['label'],
					$element['content']
				);
			}
			else {
				echo sprintf(
					'<tr><td colspan="2" class="main">%s</td></tr>',
					$element
				);
			}
    	}
	}

?>


 </table>
</div>
<?php
    }
?>
