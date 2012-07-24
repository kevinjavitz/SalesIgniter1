<?php

    $term = $_GET['term'];

    $jsonData = array();

		$QBarcode = Doctrine_Query::create()
		->from('ProductsInventoryBarcodes')
		->where('barcode LIKE ?', '%'.$term.'%')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach($QBarcode as $barcode){
			$jsonData[] = array(
				'value' => $barcode['barcode'],
				'label' => $barcode['barcode']
			);
        }




	EventManager::attachActionResponse($jsonData, 'json');
?>