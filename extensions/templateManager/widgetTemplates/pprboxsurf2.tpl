<?php
	$inventory_centersExt = $appExtension->getExtension('inventoryCenters');
	$invInfoCenters = $inventory_centersExt->getInventoryCenters((isset($_GET['inv_id']) ? (int)$_GET['inv_id'] : $_GET['inv_name']));
	$invInfoCenters = $invInfoCenters[0];


?>
<script type="text/javascript">
	$(document).ready(function (){
		//$('#<?php echo $box_id;?> .rentbbut').button();
		$('#<?php echo $box_id;?> .rentbbut').html('<span class="ui-button-text">Submit</span>');
        //$('#<?php echo $box_id;?> .rentbbut .ui-button-text').html('Submit');
		$('#<?php echo $box_id;?> .ui-infobox-header-text:first').html('Check Rates &amp; Availability At <?php echo  $invInfoCenters['inventory_center_name']; ?>');
		htmlCont = '<input type="hidden" name="continent" value="<?php echo $invInfoCenters['inventory_center_continent']?>">';
		htmlCountry = '<input type="hidden" name="country" value="<?php echo $invInfoCenters['inventory_center_country']?>">';
		htmlState = '<input type="hidden" name="state" value="<?php echo $invInfoCenters['inventory_center_state']?>">';
		htmlCity = '<input type="hidden" name="city" value="<?php echo $invInfoCenters['inventory_center_city']?>">';
		isContinent = '<input type="hidden" name="isContinent" value="4">';
		rType = '<input type="hidden" name="fromInfobox" value="infobox">';
		$('#<?php echo $box_id;?>').find('.sd').append(htmlCont);
		$('#<?php echo $box_id;?>').find('.sd').append(htmlCountry);
		$('#<?php echo $box_id;?>').find('.sd').append(htmlState);
		$('#<?php echo $box_id;?>').find('.sd').append(htmlCity);
		$('#<?php echo $box_id;?>').find('.sd').append(isContinent);
		$('#<?php echo $box_id;?>').find('.sd').append(rType);

    });
</script>
<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-widget ui-widget-content ui-infobox ui-corner-all-medium">
 <div class="ui-widget-header ui-infobox-header ui-corner-all">
  <div class="ui-infobox-header-text"><?php echo $boxHeading;?></div>
 </div>
 <div class="ui-infobox-content ui-corner-bottom-medium"><?php echo $boxContent;?></div>
</div>