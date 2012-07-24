<script type="text/javascript">
	$(document).ready(function(){
		$('#boxLocation').hide();
		$('.showZip').click(function(){
			$('#boxLocation').dialog({
				title: 'Change Location',
				close: function (e, ui){
					$(this).dialog('destroy');
				}
			});
			return false;
		});
	});
</script>
<?php
	$Qstore = Doctrine_Core::getTable('Stores')->findOneByStoresId((int)Session::get('current_store_id'));
global $currencies;
?>

<div style="color:brown;display:inline-block;width:100%;margin-top:20px;font-weight:bold;">
<table width="100%" cellpadding="2" cellspacing="2">
	<tr>
		<td>
			You are visiting:
		</td>
		<td>
			<?php echo $Qstore['stores_location'];?> (<span class="showZip" style="cursor:pointer;color:black;">Change Location</span>)
		</td>
		<td>
			Delivery Information For Zip Code:
		</td>
		<td>
		   <?php
			if(Session::exists('zipClient'.Session::get('current_store_id'))){
				echo Session::get('zipClient'.Session::get('current_store_id'));
			}else{
				echo '<span class="showZip" style="cursor:pointer;color:black;">Select Zip</span>';
			}


			?>
		</td>
	</tr>
	<tr>
		<td>
			Email:
		</td>
		<td>
			<?php
				echo $Qstore['stores_email'];
			?>
		</td>
		<td>
			Delivery Fee:
		</td>
		<td>
	<?php
	    if(Session::exists('zipClient'.Session::get('current_store_id'))){
		$module = OrderShippingModules::getModule('zonereservation', true);
		$quotes = $module->quote();
		echo $currencies->format($quotes['methods'][0]['cost']);
	}else{
		echo '<span class="showZip" style="cursor:pointer;color:black;">Select Zip</span>';
	}
	?>
		</td>
	</tr>
	<tr>
		<td>
			Phone:
		</td>
		<td>
			<?php
			echo $Qstore['stores_telephone'];
			?>
		</td>
		<td>
			Free Delivery For Orders Over:
		</td>
		<td>
	<?php
	      if(Session::exists('zipClient'.Session::get('current_store_id'))){
			if($quotes['methods'][0]['free_delivery_over'] > -1){
				echo $currencies->format($quotes['methods'][0]['free_delivery_over']);
			}else{
				echo 'No free delivery';
			}
	}else{
		echo '<span class="showZip" style="cursor:pointer;color:black;">Select Zip</span>';
	}
	?>
		</td>
	</tr>
	<tr>
		<td>
			Weather:
		</td>
		<td>
			<?php
			if(Session::exists('zipClient'.Session::get('current_store_id'))){
			   $xml = simplexml_load_file('http://www.google.com/ig/api?weather='.Session::get('zipClient'.Session::get('current_store_id')));
				//$informationCondition = $xml->xpath("/xml_api_reply/weather/current_conditions/condition");
				$informationTemp = $xml->xpath("/xml_api_reply/weather/current_conditions/temp_f");
				$informationIcon = $xml->xpath("/xml_api_reply/weather/current_conditions/icon");
				echo '<img style="display:inline-block;" src="http://www.google.com'.$informationIcon[0]->attributes().'" />'.'<span style="display:inline-block;vertical-align:top;line-height:40px;">(Temperature: '.$informationTemp[0]->attributes().'F)</span>';
			}else{
				echo '<span class="showZip" style="cursor:pointer;color:black;">Select Zip</span>';
			}
			?>
		</td>
		<td>
			Minimum Order Amount:
		</td>
		<td>
	<?php
	      if(Session::exists('zipClient'.Session::get('current_store_id'))){
			$orderTotalMin = OrderTotalModules::getModule('minorder');
			echo $currencies->format($orderTotalMin->orderAmount);
	}else{
		echo '<span class="showZip" style="cursor:pointer;color:black;">Select Zip</span>';
	}
	?>
		</td>
	</tr>

</table>

</div>