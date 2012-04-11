<?php
$xmlRoot = new SimpleXMLElement('<xml/>');

//rss wrapper
$rss = $xmlRoot->addChild('rss');
	$rss->addAttribute('version', '2.0');
	$rss->addAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
	
//channel wrapper
$mainXml = $rss->addChild('channel');
	$mainXml->addChild('title', 'Products');
	$mainXml->addChild('link', 'http://www.installsi2.info/');
	$mainXml->addChild('description', 'My products description.');
	
//get products
$products = Doctrine_Query::create()
	->select(
		'p.products_id, ' . 
		'p.products_model as v_products_model, ' . 
		'p.products_image as v_products_image, ' . 
		'p.products_price as v_products_price, ' . 
		'p.products_price_used as v_products_price_used, ' . 
		'p.products_price_stream as v_products_price_stream, ' . 
		'p.products_price_download as v_products_price_download, ' . 
		'p.products_weight as v_products_weight, ' . 
		'p.products_date_available as v_date_avail, ' . 
		'p.products_tax_class_id as v_tax_class_id, ' .
		'p.products_type as v_products_type, ' . 
		'p.products_in_box as v_products_in_box, ' . 
		'p.products_featured as v_products_featured, ' . 
		'p.products_status as v_status, ' .
		'p.membership_enabled as v_memberships_not_enabled, ' .
		'(SELECT group_concat(p2c.categories_id) FROM ProductsToCategories p2c WHERE p2c.products_id = p.products_id) as v_products_categories'
	)->from('Products p')
	->where('p.products_model is not null')
	->andWhere('p.products_model != ?', '')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

//add products
foreach( $products as $product ) {
	$item = $mainXml->addChild('item');
	
	//set title/description for each language
	foreach(sysLanguage::getLanguages() as $lang){
		$_id = $lang['id'];

		//get info ( other fields not used: products_head_title_tag, products_head_desc_tag, products_head_keywords_tag )
		$_info = Doctrine_Query::create()
			->from('ProductsDescription pd')
			->where('products_id = ?', $product['products_id'])
			->andWhere('language_id = ?', $_id)
			->execute()->toArray();
		
		//set fields
		if(isset($_info[$_id])){
			//title
			$_title = $item->addChild('title', htmlspecialchars( $_info[$_id]['products_name'] ));
				$_title->addAttribute('langID', $_id);
			
			//description
			$_description = $item->addChild('description', htmlspecialchars( $_info[$_id]['products_description'] ));
				$_description->addAttribute('langID', $_id);
				
			//link
			$_link = $item->addChild('link', htmlspecialchars( $_info[$_id]['products_url'] ));
				$_link->addAttribute('langID', $_id);
		}
	}
	
	$params = array(
		'id' => htmlspecialchars( $product['products_id'] ),
		'availability' => ($product['v_status'] == 1 ? 'in stock' : 'out of stock'),
		'price' => number_format( (double)$product['v_products_price'], 2).' USD',
		'condition' => 'new',
		'shipping_weight' => $product['v_products_weight'].' pounds'
	);
	foreach( $params as $key => $value ) {
		$item->addChild("g:{$key}", $value );
	}
}

$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML( $xmlRoot->asXML() );

//force download
header("Content-type: text/xml");
header("Content-disposition: attachment; filename=products.xml");

// Changed if using SSL, helps prevent program delay/timeout (add to backup.php also)
//	header("Pragma: no-cache");
if ($request_type== 'NONSSL'){
	header("Pragma: no-cache");
} else {
	header("Pragma: ");
}
header("Expires: 0");
echo $dom->saveXML();
itwExit();
?>