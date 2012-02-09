<?php
	$Products = Doctrine_Core::getTable('Products');
	if (isset($_GET['pID']) && empty($_POST)){
		$Product = $Products->find((int) $_GET['pID']);
	}else{
		$Product = $Products->getRecord();
	}

	$manufacturers_array = array(array('id' => '', 'text' => sysLanguage::get('TEXT_NONE')));
	$Qmanufacturers = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc("select manufacturers_id, manufacturers_name from manufacturers order by manufacturers_name");
	foreach($Qmanufacturers as $manufacturers){
		$manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
		'text' => $manufacturers['manufacturers_name']);
	}

	$tax_class_array = array(array('id' => '0', 'text' => sysLanguage::get('TEXT_NONE')));
	$QtaxClass = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc("select tax_class_id, tax_class_title from tax_class order by tax_class_title");
	foreach($QtaxClass as $tax_class){
		$tax_class_array[] = array('id' => $tax_class['tax_class_id'],
		'text' => $tax_class['tax_class_title']);
	}

	$languages = tep_get_languages();

	if (!isset($Product['products_status'])) $Product['products_status'] = '1';
	switch ($Product['products_status']) {
		case '0': $in_status = false; $out_status = true; break;
		case '1':
		default: $in_status = true; $out_status = false;
	}

	if (!isset($Product['products_featured'])) $Product['products_featured'] = '0';
	switch ($Product['products_featured']) {
		case '1': $non_featured = false; $featured = true; break;
		case '0':
		default: $featured = false; $non_featured = true;
	}


	//------------------------- BOX set begin block -----------------------------//
	$box_id = false;
	$disc_label = 1;
	if ($Product['products_in_box'])
	{
		$box_query = tep_db_query("select box_id, disc from " . TABLE_PRODUCTS_TO_BOX . " where products_id=".$Product['products_id']);
		$box = tep_db_fetch_array($box_query);
		$box_id = $box['box_id'];
		$disc_label = $box['disc'];
	}

	if ($Product['products_id'])
	{
		$boxes_query = tep_db_query("select p.products_id, pd.products_name from " . TABLE_PRODUCTS . " p INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id AND pd.language_id='". (int)Session::get('languages_id') ."' AND p.products_in_box=0 AND p.products_status=1 AND p.products_id<>".$Product['products_id']);
		while ($boxes = tep_db_fetch_array($boxes_query))
		{
			$boxes_array[] = array('id'   => $boxes['products_id'],
			'text' => $boxes['products_name']);
		}
	}

	if (!isset($Product['products_in_box'])) $Product['products_in_box'] = '0';

	$is_box_array = array();
	$is_box_array[] = array('id'   => 0, 'text' => 'No');
	$is_box_array[] = array('id'   => 1, 'text' => 'Yes');
	//------------------------- BOX set end block -----------------------------//

	$ajaxSaveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->addClass('ajaxSave')->setText(sysLanguage::get('TEXT_BUTTON_AJAX_SAVE'));
	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save')->setText(sysLanguage::get('TEXT_BUTTON_SAVE'));
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel');

	if (Session::exists('categories_cancel_link') === true){
		$cancelButton->setHref(Session::get('categories_cancel_link'));
	}else{
		$cancelButton->setHref(itw_app_link((isset($_GET['pID']) ? 'pID=' . $_GET['pID'] : ''), null, 'default'));
	}
?>
<script language="javascript">
var tax_rates = new Array();
<?php
for ($i=0, $n=sizeof($tax_class_array); $i<$n; $i++) {
	if ($tax_class_array[$i]['id'] > 0) {
		echo 'tax_rates["' . $tax_class_array[$i]['id'] . '"] = ' . tep_get_tax_rate_value($tax_class_array[$i]['id']) . ';' . "\n";
	}
}
?>
</script>
 <input type="button" value="Turn On Upload Debugger" id="turnOnDebugger" /><br />
 <form name="new_product" action="<?php echo itw_app_link(tep_get_all_get_params(array('action','pID')) . 'action=saveProduct' . ((int)$Product['products_id'] > 0 ? '&pID=' . $Product['products_id'] : ''));?>" method="post" enctype="multipart/form-data">
 <div style="position:relative;text-align:right;"><?php
 	echo $ajaxSaveButton->draw() . $saveButton->draw() . $cancelButton->draw();
 	echo '<div class="pageHeading" style="position:absolute;left:0;top:.5em;">' . (isset($_GET['pID']) ? 'Edit Product' : 'New Product') . '</div>';
 ?></div>
 <br />
 <?php if (!isset($_GET['pID'])){ ?>
 <div class="ui-widget ui-widget-content ui-corner-all ui-state-warning newProductMessage" style="padding:.3em;font-weight:bold;">You are entering a new product. Some places are disabled, use the "Save Ajax" button to save this product and enable them</div>
 <br />
 <?php } ?>
 <div id="tab_container">
  <ul>
   <li class="ui-tabs-nav-item"><a href="#page-1"><span><?php echo sysLanguage::get('TAB_GENERAL');?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-8"><span><?php echo sysLanguage::get('TAB_IMAGES');?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_DESCRIPTION');?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-3"><span><?php echo sysLanguage::get('TAB_PRICING');?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-4"><span><?php echo sysLanguage::get('TAB_INVENTORY');?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-5"><span><?php echo 'Box Set';?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-r"><span><?php echo sysLanguage::get('TAB_RENTAL_MEMBERSHIP');?></span></a></li>
   <li class="ui-tabs-nav-item"><a href="#page-categories"><span><?php echo 'Categories';?></span></a></li>
<?php
	$contents = EventManager::notifyWithReturn('NewProductTabHeader', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
  </ul>

  <div id="page-1"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_general.php');?></div>
  <div id="page-8"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_images.php');?></div>
  <div id="page-2"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_description.php');?></div>
  <div id="page-3"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_pricing.php');?></div>
  <div id="page-4"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_inventory.php');?></div>
  <div id="page-5"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_box_set.php');?></div>
  <div id="page-r"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_rental_membership.php');?></div>
  <div id="page-categories"><?php include(sysConfig::get('DIR_WS_APP') . 'products/pages_tabs/tab_categories.php');?></div>
<?php
	$contents = EventManager::notifyWithReturn('NewProductTabBody', &$Product);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>
 </div>
 <div style="position:relative;text-align:right;margin-top:.5em;margin-left:250px;"><?php
	if (Session::exists('categories_cancel_link') === true){
		echo tep_draw_hidden_field('categories_save_redirect', Session::get('categories_save_redirect'));
	}
	echo $ajaxSaveButton->draw() . $saveButton->draw() . $cancelButton->draw();
 ?><div class="smallText" style="text-align:left;width:315px;position:absolute;right:.5em;top:3em;">*Image upload fields do not work with ajax save<br>So you'll need to use the normal save button for uploads</div></div>
</form>