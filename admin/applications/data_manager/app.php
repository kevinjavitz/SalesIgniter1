<?php
	$appContent = $App->getAppContentFile();


	
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');

	$separator = "\t";
	$default_image_manufacturer = '';
	$default_image_product = '';
	$default_image_category = '';
	$active = 'Active';
	$inactive = 'Inactive';
	$deleteStatus = 'Delete';
	$zero_qty_inactive = false;
	$replace_quotes = false;
	
	$showLogInfo = false;

	
	function tep_get_tax_class_rate($tax_class_id) {
		$tax_multiplier = 0;
		$tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " WHERE  tax_class_id = '" . $tax_class_id . "' GROUP BY tax_priority");
		if (tep_db_num_rows($tax_query)) {
			while ($tax = tep_db_fetch_array($tax_query)) {
				$tax_multiplier += $tax['tax_rate'];
			}
		}
		return $tax_multiplier;
	}

	function tep_get_tax_title_class_id($tax_class_title) {
		$classes_query = tep_db_query("select tax_class_id from " . TABLE_TAX_CLASS . " WHERE tax_class_title = '" . $tax_class_title . "'" );
		$tax_class_array = tep_db_fetch_array($classes_query);
		$tax_class_id = $tax_class_array['tax_class_id'];
		return $tax_class_id ;
	}

//if (isset($_POST['buttoninsert'])) $action = 'importProducts';
//if (isset($_POST['buttonsplit'])) $action = 'splitFile';
//if (isset($_POST['buttoninserttemp'])) $action = 'importProducts';
?>