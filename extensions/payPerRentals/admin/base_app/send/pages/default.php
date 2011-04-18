  <table border="0" width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
     <tr>
      <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></td>
      <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
     </tr>
    </table></td>
   </tr>
   <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
     <tr>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_FROM_DATE');?></legend>
       <div type="text" id="DP_startDate"></div><br>
       <input type="text" name="start_date" id="start_date" value="<?php echo date('Y-m-d');?>">
      </fieldset></td>
      <td width="50%" class="main" valign="top"><fieldset>
       <legend><?php echo sysLanguage::get('LEGEND_TO_DATE');?></legend>
       <div type="text" id="DP_endDate"></div><br>
       <input type="text" name="end_date" id="end_date" value="<?php echo date('Y-m-d');?>">
       </fieldset></td>
     </tr>
    </table></td>
   </tr>
   <tr>
    <td align="right"><input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_GET_RES');?>" name="get_res" id="get_res"></td>
   </tr>
   <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '10');?></td>
   </tr>
   <tr>
    <td><table cellpadding="2" cellspacing="0" border="0" width="100%" id="reservationsTable">
     <thead>
      <tr class="dataTableHeadingRow">
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_SEND');?></td>
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMERS_NAME');?></td>
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS_NAME');?></td>
       <td class="dataTableHeadingContent" style="text-align:left;"><?php echo sysLanguage::get('TABLE_HEADING_BARCODE');?></td>
	   <td class="dataTableHeadingContent" style="text-align:left;"><?php echo "Location";?></td>
       <td class="dataTableHeadingContent"><?php echo 'Dates';?></td>
		<td class="dataTableHeadingContent"><?php echo 'Tracking Number';?></td>
       <td class="dataTableHeadingContent">View Order</td>
      </tr>
     </thead>
     <tfoot>
      <tr>
       <td colspan="6" align="right"><input type="button" value="<?php echo sysLanguage::get('TEXT_BUTTON_SEND');?>" name="send" id="send"></td>
      </tr>
     </tfoot>
     <tbody>
     </tbody>
    </table></td>
   </tr>
  </table>
<div id="ajaxLoader" title="Ajax Operation">Performing An Ajax Operation<br>Please Wait....</div>