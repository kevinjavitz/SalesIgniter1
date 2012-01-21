 <table cellpadding="0" cellspacing="0" border="0">
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_IMAGE'); ?></td>
   <td class="main"><?php
    echo tep_draw_file_field('categories_image');
    if (isset($_GET['cID'])){
    	echo '<br />' . tep_image(DIR_WS_CATALOG_IMAGES . $Category->categories_image, '', 200, 200) . 
    	     '<br />' . DIR_WS_CATALOG_IMAGES . 
    	     '<br /><b>' . $Category->categories_image . '</b>';
    }
   ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <?php if (!isset($cPath_array) || sizeof($cPath_array) <= 0){ ?>
  <tr>
   <td class="main" valign="top"><?php echo 'Show In Menu:'; ?></td>
   <td class="main"><?php
    $menuSet = htmlBase::newElement('radio')
    ->addGroup(array(
		'name'      => 'categories_menu',
		'checked'   => (isset($_GET['cID']) ? $Category->categories_menu : 'both'),
		'data'      => array(
			array('label' => 'Top Menu', 'value' => 'top', 'labelPosition' => 'after'),
			array('label' => 'Infobox Menu', 'value' => 'infobox', 'labelPosition' => 'after'),
			array('label' => 'Both', 'value' => 'both', 'labelPosition' => 'after')
		),
		'separator' => '<br />'
	));
    echo $menuSet->draw();
   ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <?php } ?>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SORT_ORDER'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('sort_order', (isset($_GET['cID']) ? $Category->sort_order : ''), 'size="2"');?></td>
  </tr>
 </table>