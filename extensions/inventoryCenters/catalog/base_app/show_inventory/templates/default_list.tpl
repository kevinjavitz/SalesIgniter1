<script type="text/javascript" src="<?php echo 'templates/' . Session::get('tplDir'). '/javascript/';?>index_default.js"></script>
<script type="text/javascript">
$(document).ready(function (){
	initialize();
	i1 = 0;
	var inv = [];
<?php
		$Inventory_centers = Doctrine_Query::create()
		->from('ProductsInventoryCenters')
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if($Inventory_centers){
			$i = 0;
			foreach($Inventory_centers as $inv){
				$invent = stripslashes (htmlspecialchars ($inv['inventory_center_address']));
				$invent = str_replace("\r\n", " ", $invent);
				$pointC = unserialize($inv['inventory_center_address_point']);
                		$inventX =$pointC['lat'];
                		$inventY =$pointC['lng'];
				$invent_det = stripslashes ($inv['inventory_center_details']);
				$invent_det = str_replace("\r\n", " ", $invent_det);
				
				$invent_name = stripslashes ($inv['inventory_center_name']);
				$invent_name = str_replace("\r\n", " ", $invent_name);
				
				echo '	inv.push({' . "\n" . 
					// '		details: \'' . $invent_det . '\',' . "\n" . 
					 '		name: \'' . $invent_name . '<br/><a style="text-decoration:underline;" href="' . itw_app_link('appExt=inventoryCenters&inv_id=' . $inv['inventory_center_id'], 'show_inventory', 'default') . '">More Info</a>\',' . "\n" . 
					 //'		address: \'' . $invent . '\',' . "\n" .
					 '		addressX: \'' . $inventX . '\',' . "\n" .
					 '		addressY: \'' . $inventY . '\',' . "\n" .
					 '		func: function(response){' . "\n" . 
					 '		}' . "\n" . 
					 '	});' . "\n";
				//echo "inv[i] = inv1";
				//echo 'showLocation(inv[' . $i . '].address, inv[' . $i . '].name);' . "\n";
				//echo "i = i + 1;";
				$i++;
			}
		}
?>
	$.each(inv, function (i, el){
		//setTimeout('showLocation(\'' + this.address + '\', \'' + this.name + '\');', 200*i);
		showLocation2(this.addressX, this.addressY, this.name, this.ind);
	});

	$("#message").appendTo(map.getPane(G_MAP_FLOAT_SHADOW_PANE));
	
});
</script>
<div class="ui-widget ui-widget-content ui-corner-all">
 <div class="ui-widget-header  ui-corner-all" style="padding:10px;"><span class="ui-icon ui-icon-circle-triangle-e" style="float:left;"></span><span class="ui-contentbox-header-text"><?php echo $pageHeader;?></span><br style="clear:both;"></div>
 <div class="ui-contentbox-content">
 <div id="gMap"></div>
 <?php echo $pageContent;?>
 <div class="ui-helper-clearfix"></div></div>
</div> 
<div class="ui-widget ui-widget-content ui-corner-all" style="text-align:right;margin-top:.5em;width:100%;"><div style="margin:.3em;"><?php echo $continueButton;?></div></div>