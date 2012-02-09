<?php
/*
	Google Analytics Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_googleAnalytics extends ExtensionBase {

	public function __construct(){
		parent::__construct('googleAnalytics');
	}
	
	public function init(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'PageLayoutHeaderCustomMeta',
			'CheckoutSuccessFinishOutside'

		), null, $this);
	}

	public function PageLayoutHeaderCustomMeta(){
		return '<meta name="google-site-verification" content="'.sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_META_VERIFICATION').'">'. "\n" .
			    sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_TRACKING_GENERAL');
	}
	public function CheckoutSuccessFinishOutside($Order, &$pageContents){

		ob_start();
		if(sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_USE_TRACKING_ANALYTICS') == 'True'){
		?>
	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol ) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		try{
			var pageTracker = _gat._getTracker("<?php echo sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_TRACKING_ID');?>");
			pageTracker._trackPageview();
			pageTracker._addTrans(
				"<?php echo $Order['orders_id'];?>",            // order ID - required
				"<?php echo sysConfig::get('STORE_NAME');?>",  // affiliation or store name
				"<?php echo $Order['OrdersTotal'][0]['value'];?>",           // total - required
				//"1.29",            // tax
				//"15.00",           // shipping
				"<?php echo $Order['OrdersAddresses'][0]['entry_city'];?>",        // city
				"<?php echo $Order['OrdersAddresses'][0]['entry_state'];?>",      // state or province
				"<?php echo $Order['OrdersAddresses'][0]['entry_country'];?>"              // country
			);


			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each
			<?php
			foreach($Order['OrdersProducts'] as $opInfo){
			?>
			pageTracker._addItem(
				"<?php echo $Order['orders_id'];?>",           // order ID - necessary to associate item with transaction
				"<?php echo $opInfo['products_model'];?>",           // SKU/code - required
				"<?php echo $opInfo['products_name'];?>",        // product name
				"<?php echo $opInfo['final_price'];?>",          // unit price - required
				"<?php echo $opInfo['products_quantity'];?>"               // quantity - required
			);
				<?php
			}
			?>
			pageTracker._trackTrans(); //submits transaction to the Analytics servers
		} catch(err) {}
	</script>
		<?php
		}
		if(sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_USE_TRACKING_ADWORDS') == 'True'){
		$totalValue = $Order['OrdersTotal'][0]['value'];
	?>
	<!-- Google Code for Purchase Conversion Page -->
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = <?php echo sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_CONVERSION_ID');?>;
		var google_conversion_language = "en_US";
		var google_conversion_format = "1";
		var google_conversion_color = "666666";
		var google_conversion_label = "Purchase";
		if (<?php echo $totalValue ?>) {
			var google_conversion_value = <?php echo $totalValue ?>
		}
		/* ]]> */
	</script>
	<script type="text/javascript"
		src="http://www.googleadservices.com/pagead/conversion.js">
	</script>
	<noscript>
		<img height=1 width=1 border=0
			src="http://www.googleadservices.com/pagead/
  conversion/<?php echo sysConfig::get('EXTENSION_GOOGLE_ANALYTICS_CONVERSION_ID');?>/?value=
  <?php echo $totalValue ?>&label=Purchase&script=0">
	</noscript>
		<?php
		}

		$html = ob_get_contents();
		ob_end_clean();
		$pageContents = $html;
	}

}
?>