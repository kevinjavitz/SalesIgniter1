<?php
//	require('../includes/classes/product.php');
	$fp = fopen(sysConfig::getDirFsCatalog().'feed/feed.xml', 'wb');

	function WriteHeader(){
			 global $fp;
					$exportDate = tep_date_short(date("Y/m/d"));
					$header = '<?xml version="1.0" encoding="' . CHARSET . '"?>
					<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
						<title>' . STORE_NAME  . '</title>

						<updated>'.$exportDate.'</updated>
						<author>
							<name>' . STORE_NAME . '</name>
						</author>
						<id>tag:'.time().'</id>
					';
			fwrite($fp, $header);
	}

	function WriteFooter(){
		global $fp;
		fwrite($fp, '</feed>');
	}

	function GetResult($generateFull = false, $start = 0){
		$Qproducts = Doctrine_Query::create()
			->select('p.*, pd.*, p2c.categories_id, m.*')
			->from('Products p')
			->leftJoin('p.ProductsDescription pd')
			->leftJoin('p.ProductsToCategories p2c')
			->leftJoin('p.Manufacturers m')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->orderBy('p.products_id')
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

		return $Qproducts;
	}


	 function WriteRow($row){
		global $fp;
		$expirationDate = tep_date_short(date("Y/m/d", strtotime('+1 month')));
		$productClass = new product($row['products_id']);

		 if($productClass->isValid()){

			 foreach($productClass->productInfo['typeArr'] as $typeName){
				 
				$purchaseTypes[$typeName] = $productClass->getPurchaseType($typeName);
				if ($purchaseTypes[$typeName]->hasInventory() === true && ($typeName == "new" || $typeName == "used" )){

					$link = htmlspecialchars(itw_app_link('products_id=' . $productClass->getID() , 'product', 'info'));
					$description = strip_tags($row['ProductsDescription'][0]['products_description']);
		            $categories_id = (int)$row['ProductsToCategories'][0]['categories_id'];
					$category = Doctrine_Core::getTable('CategoriesDescription')->findOneByCategoriesId($categories_id);
					$price = $purchaseTypes[$typeName]->getPrice();

					if (strlen($description) > 1000) {
						$description = substr($description, 0, 997) . "...";
					}


					$entry = array(
						'title' =>"![CDATA[" . $row['ProductsDescription'][0]['products_name'] . "]]",
						'link' => $link,
						'description' => "![CDATA[" . $description . "]]",
						'g:expiration_date' => $expirationDate,
						'g:id' => $row['products_id'] . (($typeName == "new") ? '':'u'),
						'g:condition' => $typeName,
						'g:quantity' => $purchaseTypes[$typeName]->getCurrentStock(),
						'g:price' => $price
					);

					if (tep_not_null($category['categories_name'])){
						$entry['g:department'] = "![CDATA[" . $category['categories_name'] . "]]";
					}

					if ($productClass->hasManufacturer()) {
						$entry['g:brand'] = "![CDATA[" . $productClass->getManufacturerName() . "]]";
					}

					if ($productClass->hasImage()){
						$image = $productClass->getImage();
						$entry['g:image_link'] = $image;
					}

					if ($productClass->hasModel()) {
						$entry['g:model_number'] = "![CDATA[" . $productClass->getModel() . "]]";
					}


					$xml = "<entry>\n";
					foreach($entry as $k => $v) {
						$xml .= "\t<".$k."><![CDATA[".$v."]]></".$k.">\n";
					}
					$xml .= "</entry>\n";

					fwrite($fp, $xml);
		        }
			 }
		}
	}

	WriteHeader();

	$products = GetResult();
	foreach($products as $prod){
		WriteRow($prod);
	}

	WriteFooter();
	fclose($fp);

	//$messageStack->addSession('pageStack', 'Export was succesfull', 'success');
	//EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>