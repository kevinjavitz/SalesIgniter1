<?php
  $order_by = '';
  $sort_by = '';
  if (isset($_GET['sort_by'])){
      $sort_by = $_GET['sort_by'];
      if ($sort_by == 'customer'){
          $order_by = ' order by c.customers_firstname, c.customers_lastname';
      }elseif ($sort_by == 'barcode'){
          $order_by = ' order by products_barcode';
      }
  }
?>  
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
       <tr>
        <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_RETURN'); ?></td>
        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
       </tr>
       <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
         <tr>
          <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
           <tr class="dataTableHeadingRow">
            <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_TITLE'); ?></td>
            <td valign="top" class="dataTableHeadingContent"><table cellspacing='0' cellpadding='0'>
             <tr>
              <td valign="top"><a href="<?php echo itw_app_link((isset($_GET['cID']) ? 'sort_by=customer&cID=' . $_GET['cID'] : 'sort_by=customer'), 'rental_queue', 'return')?>" class="headerLink"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMER');?></a></td>
              <?php if ($sort_by == 'customer'){ ?>
              <td valign="top"><?php echo tep_image('images/down.gif');?></td>
              <?php } ?>
             </tr>
            </table></td>
            <td valign="top" class="dataTableHeadingContent"><table cellspacing="0" cellpadding="0">
             <tr>
              <td valign="top"><a href="<?php echo itw_app_link((isset($_GET['cID']) ? 'sort_by=barcode&cID=' . $_GET['cID'] : 'sort_by=barcode'), 'rental_queue', 'return')?>" class="headerLink"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE');?></a></td>
              <?php if ($sort_by == 'barcode'){ ?>
              <td valign="top"><?php echo tep_image('images/down.gif');?></td>
              <?php } ?>
             </tr>
	        </table></td>
	        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE_IMG'); ?></td>
	        <?php if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && EXTENSION_INVENTORY_CENTERS_ENABLED == 'True'){ ?>
	        <td valign="top" class="dataTableHeadingContent"><?php echo 'Inventory Center'; ?></td>
	        <?php } ?>
	        <td valign="top" class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_COMMENTS'); ?></td>
	        <td valign="top" class="dataTableHeadingContent" align="center"><?php echo 'Action&nbsp;'; ?></td>
	       </tr>
<?php
  if (isset($_GET['cID'])) $order_by = ' AND r.customers_id = "' . $_GET['cID'] . '"' . $order_by;

  if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && EXTENSION_INVENTORY_CENTERS_ENABLED == 'True'){
      $invCenterArray = array();
      $QinvCenters = tep_db_query('select inventory_center_id, inventory_center_name from ' . TABLE_PRODUCTS_INVENTORY_CENTERS . ' order by inventory_center_name');
      while($invCenters = tep_db_fetch_array($QinvCenters)){
          $invCenterArray[] = array(
              'id'   => $invCenters['inventory_center_id'],
              'text' => $invCenters['inventory_center_name']
          );
      }
  }
  
  $Qrented = dataAccess::setQuery('select r.customers_queue_id, r.customers_id, r.products_id, p.products_name, r.date_added, r.products_barcode, concat(c.customers_firstname, " ", c.customers_lastname) as full_name from {rented} r inner join {product_description} p on p.products_id = r.products_id inner join {customers} c on r.customers_id = c.customers_id where p.language_id = {language_id} ' . $order_by);
  $Qrented->setTable('{rented}', TABLE_RENTED_QUEUE);
  $Qrented->setTable('{product_description}', TABLE_PRODUCTS_DESCRIPTION);
  $Qrented->setTable('{customers}', TABLE_CUSTOMERS);
  $Qrented->setValue('{language_id}', Session::get('languages_id'));
  $Qrented->runQuery();
  if ($Qrented->numberOfRows() < 1){
      echo 	'<tr>
       <td colspan="7" class="messageStackError">' . sysLanguage::get('TEXT_RENTED_QUEUE_EMPTY') . '</td>
      </tr>';
  } else {
      while($Qrented->next() !== false){
      	if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && EXTENSION_INVENTORY_CENTERS_ENABLED == 'True'){
          $Qbarcode = dataAccess::setQuery('select i.use_center, ib.barcode from {inventory} i, {barcodes} ib where ib.inventory_id = i.inventory_id and ib.barcode_id = {barcode_id}');
      	}else{
          $Qbarcode = dataAccess::setQuery('select ib.barcode from {inventory} i, {barcodes} ib where ib.inventory_id = i.inventory_id and ib.barcode_id = {barcode_id}');
      	}
          $Qbarcode->setTable('{inventory}', TABLE_PRODUCTS_INVENTORY);
          $Qbarcode->setTable('{barcodes}', TABLE_PRODUCTS_INVENTORY_BARCODES);
          $Qbarcode->setValue('{barcode_id}', $Qrented->getVal('products_barcode'));
          $Qbarcode->runQuery();
?>
	       <tr class="dataTableRow">
	        <td class="main"><?php echo $Qrented->getVal('products_name'); ?></td>
	        <td class="main"><?php echo $Qrented->getVal('full_name'); ?></td>
	        <td class="main"><?php echo $Qbarcode->getVal('barcode'); ?></td>
	        <td class="main"><img src="showBarcode.php?code=<?php echo $Qrented->getVal('products_barcode');?>"></td>
	        <?php if (defined('EXTENSION_INVENTORY_CENTERS_ENABLED') && EXTENSION_INVENTORY_CENTERS_ENABLED == 'True'){ ?>
	        <td class="main"><?php
	         if ($Qbarcode->getVal('use_center') == '1'){
	             $QinvCenter = dataAccess::setQuery('select i.inventory_center_id from {centers} i, {barcode_center} b2c where b2c.inventory_center_id = i.inventory_center_id and b2c.barcode_id = {barcode_id}');
	             $QinvCenter->setTable('{centers}', TABLE_PRODUCTS_INVENTORY_CENTERS);
	             $QinvCenter->setTable('{barcode_center}', TABLE_PRODUCTS_INVENTORY_BARCODES_TO_INVENTORY_CENTERS);
	             $QinvCenter->setValue('{barcode_id}', $Qrented->getVal('products_barcode'));
	             $QinvCenter->runQuery();
	             
	             echo tep_draw_pull_down_menu('inventory_center', $invCenterArray, $QinvCenter->getVal('inventory_center_id'), 'defaultValue="' . $QinvCenter->getVal('inventory_center_id') . '" id="inventory_center"');
	         }
	        ?></td>
	        <?php } ?>
	        <td class="main"><?php echo  tep_draw_textarea_field('comments','soft',35,5,'', 'id="comments"'); ?></td>
	        <td class="main" align="center"><?php
	         echo htmlBase::newElement('button')->addClass('returnOk')->setText('Return OK')->draw() . '<br>' . 
	         htmlBase::newElement('button')->addClass('returnBroken')->setText('Return Broken')->draw() . '<br>' . 
	         htmlBase::newElement('button')->addClass('appendComments')->setText('Just Comments')->draw() . '<br>' . 
	         '<input type="hidden" name="queue_id" id="queue_id" value="' . $Qrented->getVal('customers_queue_id') . '">';
	        ?></td>
	       </tr>
<?php
      }
  }
?>
          </table></td>
         </tr>
        </table></td>
       </tr>
      </table></td>
     </tr>
    </table>