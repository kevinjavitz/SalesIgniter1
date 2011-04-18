<?php
	echo '<ul>';
	foreach(sysLanguage::getLanguages() as $lInfo){
		echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lInfo['id'] . '"><span>' . '&nbsp;' . $lInfo['showName']() . '</span></a></li>';
	}
	echo '</ul>';

	foreach(sysLanguage::getLanguages() as $lInfo){
		$lID = $lInfo['id'];
		$name = ''; $description = '';$seo_url = ''; $htc_title = ''; $htc_desc = ''; $htc_keywords = ''; $htc_descrip = '';
		if (isset($_GET['cID'])){
			$name = $Category->CategoriesDescription[$lID]->categories_name;
			$description = $Category->CategoriesDescription[$lID]->categories_description;
			$seo_url = $Category->CategoriesDescription[$lID]->categories_seo_url;
		}
?>
<div id="langTab_<?php echo $lID;?>">
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_CATEGORIES_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('categories_name[' . $lID . ']', $name); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_DESCRIPTION'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('categories_description[' . $lID . ']', 'hard', 30, 5, $description, 'class="makeFCK"'); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EDIT_CATEGORIES_SEO_URL'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('categories_seo_url[' . $lID . ']', $seo_url);?></td>
  </tr>


<?php

	/**
	 * this event expects an array having two elements: label and content | i.e. (array(label=>'', content=>''))
	 */
	$contents_middle = array();
	EventManager::notify('CategoriesFormMiddle', $lID, &$contents_middle);

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
