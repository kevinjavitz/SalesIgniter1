<?php
	echo '<ul>' . "\n";
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$langImage = tep_image(sysConfig::getDirFsCatalog() . 'languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
		$lID = $languages[$i]['id'];
		echo '	<li class="ui-tabs-nav-item"><a href="#langTab_' . $lID . '"><span>' . $languages[$i]['name'] . '</span></a></li>' . "\n";
	}
	echo '</ul>' . "\n";

	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {

		$langImage = tep_image(sysConfig::getDirFsCatalog() . 'languages/' . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
		$lID = $languages[$i]['id'];
		$name = ''; $description = '';$url = ''; $htc_title = ''; $htc_desc = ''; $htc_keywords = '';
		if (isset($_GET['aID'])){
			$name = $Article->ArticlesDescription[$lID]->articles_name;
			$description = $Article->ArticlesDescription[$lID]->articles_description;
			$url = $Article->ArticlesDescription[$lID]->articles_url;
		}

?>
<div id="langTab_<?php echo $lID;?>">
	<table cellpadding="3" cellspacing="0" border="0">
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_ARTICLES_NAME'); ?></td>
			<td class="main"><?php echo tep_draw_input_field('articles_name[' . $lID . ']', $name, 'size="35"'); ?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo sysLanguage::get('TEXT_ARTICLES_URL') . '<br><small>' . sysLanguage::get('TEXT_ARTICLES_URL_WITHOUT_HTTP') . '</small>'; ?></td>
			<td class="main"><?php echo tep_draw_input_field('articles_url[' . $lID . ']', $url, 'size="35"');?></td>
		</tr>
		<tr>
			<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
		</tr>

<?php

	/**
	 * this event expects an array having two elements: label and content | i.e. (array(label=>'', content=>''))
	 */
	$contents_middle = array();
	EventManager::notify('ArticleManagerFormMiddle', $lID, &$contents_middle);

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

		<tr>
			<td class="main" valign="top"><?php echo sysLanguage::get('TEXT_ARTICLES_DESCRIPTION'); ?></td>
			<td class="main"><?php echo tep_draw_textarea_field('articles_description[' . $lID . ']', 'hard', 30, 5, stripslashes($description), 'class="makeFCK"'); ?></td>
		</tr>
	</table>
</div>
<?php
    }
?>
