<?php
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<div>
	<div style="float: right;">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="smallText"><?php
					echo $icons['activeCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_ACTIVE_CART');
				?></td>
				<td class="smallText"><?php
					echo $icons['inactiveCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_INACTIVE_CART');
				?></td>
			</tr>
			<tr>
				<td class="smallText"><?php
					echo $icons['activeNoCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_ACTIVE_NOCART');
				?></td>
				<td class="smallText"><?php
					echo $icons['inactiveNoCart'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_INACTIVE_NOCART');
				?></td>
			</tr>
			<tr>
				<td class="smallText"><?php
					echo $icons['botActive'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_ACTIVE_BOT');
				?></td>
				<td class="smallText"><?php
					echo $icons['botInactive'] . '&nbsp;' . sysLanguage::get('TEXT_STATUS_INACTIVE_BOT');
				?></td>
			</tr>
		</table>
	</div>
	<br><span class="smallText" style="color:#909090"><?php echo sysLanguage::get('TEXT_SET_REFRESH_RATE');?>:&nbsp;</span>
	<span style="font-size: 10px; color:#0000CC"><?php
		echo '<a class="menuBoxContentLink refreshLink" data-seconds="null" href="javascript:void(0);"><b> ' . sysLanguage::get('TEXT_NONE') . ' </b></a>';
		foreach ($refresh_time as $key => $value) {
			echo ' &#183; <a class="menuBoxContentLink refreshLink" data-seconds="' . $value . '" href="javascript:void(0);"><b>' . $refresh_display[$key] . '</b></a>';
		}
	?></span>
</div>
<div style="margin-left:auto;margin-right:auto;"><font size="2" face="Arial" color="blue"><?php
	echo sysLanguage::get('TEXT_INFO_LAST_REFRESH') . '&nbsp;<span class="refreshTime"></span><br />';
	echo sysLanguage::get('TEXT_INFO_NEXT_REFRESH') . '&nbsp;<span class="nextRefreshTime">N/A</span>';
?></font></div>
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;" class="gridTableHolder"></div>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
<div style="margin:.5em;"><table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td class="smallText" align="left" colspan="2"><?php echo sprintf(sysLanguage::get('TEXT_NUMBER_OF_CUSTOMERS'), '<span class="sessionCount">0</span>');?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="duplicateCount">0</span></td>
		<td class="smallText" align="left"><?php echo sysLanguage::get('TEXT_DUPLICATE_IP'); ?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="botCount">0</span></td>
		<td class="smallText" width="570"><?php echo sysLanguage::get('TEXT_BOTS'); ?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="adminCount">0</span></td>
		<td class="smallText"><?php echo sysLanguage::get('TEXT_ME'); ?></td>
	</tr>
	<tr>
		<td class="smallText" align="right"><span class="customerCount">0</span></td>
		<td class="smallText"><?php echo sysLanguage::get('TEXT_REAL_CUSTOMERS'); ?></td>
	</tr>
</table></div>
<div style="margin:.5em;"><?php
	echo '<b>' . sysLanguage::get('TEXT_MY_IP_ADDRESS') . ':</b>&nbsp;' . tep_get_ip_address() . '<br><small>' . TEXT_NOT_AVAILABLE . '</small>';
?></div>


<?php
/*
  $heading = array();
  $contents = array();
  $heading[] = array('text' => '<b>' . sysLanguage::get('TABLE_HEADING_SHOPPING_CART') . '</b>');
  if (isset($info)) {
    if (STORE_SESSIONS == 'mysql') {
      $session_data = tep_db_query("select value from " . TABLE_SESSIONS . " WHERE sesskey = '" . $info . "'");
      $session_data = tep_db_fetch_array($session_data);
      $session_data = trim($session_data['value']);
    } else {
      if ( (file_exists(tep_session_save_path() . '/sess_' . $info)) && (filesize(tep_session_save_path() . '/sess_' . $info) > 0) ) {
        $session_data = file(tep_session_save_path() . '/sess_' . $info);
        $session_data = trim(implode('', $session_data));
      }
    }
 	$length = strlen($session_data);
    if ($length > 0) {

        $start_id = strpos($session_data, 'customer_id|s');
        $start_cart = strpos($session_data, 'ShoppingCart|O');
        $start_currency = strpos($session_data, 'currency|s');
        $start_country = strpos($session_data, 'customer_country_id|s');
        $start_zone = strpos($session_data, 'customer_zone_id|s');

      for ($i=$start_cart; $i<$length; $i++) {
        if ($session_data[$i] == '{') {
          if (isset($tag)) {
            $tag++;
          } else {
            $tag = 1;
          }
        } elseif ($session_data[$i] == '}') {
          $tag--;
        } elseif ( (isset($tag)) && ($tag < 1) ) {
          break;
        }
      }
  
      $session_data_id = substr($session_data, $start_id, (strpos($session_data, ';', $start_id) - $start_id + 1));
      $session_data_cart = substr($session_data, $start_cart, $i);
      $session_data_currency = substr($session_data, $start_currency, (strpos($session_data, ';', $start_currency) - $start_currency + 1));
      $session_data_country = substr($session_data, $start_country, (strpos($session_data, ';', $start_country) - $start_country + 1));
      $session_data_zone = substr($session_data, $start_zone, (strpos($session_data, ';', $start_zone) - $start_zone + 1));

      session_decode($session_data_id);
      session_decode($session_data_currency);
      session_decode($session_data_country);
      session_decode($session_data_zone);
      session_decode($session_data_cart);

      if (is_object($ShoppingCart)) {
      	$ShoppingCart->initContents();
        foreach($ShoppingCart->getProducts() as $cartProduct) {
          $contents[] = array('text' => $cartProduct->getQuantity() . ' x ' . $cartProduct->getName());
        }

        if (sizeof($ShoppingCart->getProducts()) > 0) {
         $contents[] = array('text' => tep_draw_separator('pixel_black.gif', '100%', '1'));
         $contents[] = array('align' => 'right', 'text'  => sysLanguage::get('TEXT_SHOPPING_CART_SUBTOTAL') . ' ' . $currencies->format($ShoppingCart->showTotal(), true, $currency));
        } else {
         $contents[] = array('text' => sysLanguage::get('TEXT_EMPTY')); // by azer for v2.9
      }
    }
  }
 }
   // Show shopping cart contents for selected entry
   echo '            <td valign="top">' . "\n";

   $box = new box;
   echo $box->infoBox($heading, $contents);

   echo '</td>' . "\n";
*/
?>