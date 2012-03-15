<?php
	if (isset($_POST['discs'])){
		$products_id = (int)$_POST['products_id'];
		$discs = (int)$_POST['discs'];

		 $QproductToBox = Doctrine_Query::create()
		->from('ProductsToBox')
		->where('box_id = ?', $products_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		 //print_r($QproductToBox);
		 //itwExit();

		 foreach($QproductToBox as $qptb){

			/*Doctrine_Query::create()
			->delete('ProductsDescription')
			->where('products_id = ?', $qptb['products_id'])
			->execute();
			Doctrine_Query::create()
			->delete('ProductsToCategories')
			->where('products_id = ?', $qptb['products_id'])
			->execute();

			Doctrine_Query::create()
			->delete('ProductsToBox')
			->where('products_id = ?', $qptb['products_id'])
			->execute();


			Doctrine_Query::create()
			->delete('ProductsCustomFieldsGroupsToProducts')
			->where('product_id = ?', $qptb['products_id'])
			->execute();
			Doctrine_Query::create()
			->delete('ProductsCustomFieldsToProducts')
			->where('product_id = ?', $qptb['products_id'])
			->execute();*/
			 $ProductDelete = Doctrine_Core::getTable('Products')->findOneByProductsId($qptb['products_id']);
			 $ProductDelete->delete();

		 }

		$Product = Doctrine_Core::getTable('Products')->findOneByProductsId($products_id);
		$ProductDescription = $Product->ProductsDescription;
		$ProductToCategories = $Product->ProductsToCategories;

		/*Move into extension*/
		$GroupsToProducts = $Product->ProductsCustomFieldsGroupsToProducts;
		$FieldsToProducts = $Product->ProductsCustomFieldsToProducts;
		/*foreach($FieldsToProducts as $ftp){
			echo 'dd'. $ftp->field_id.'---';
			echo 'dd'. $ftp->field_type.'---';
			echo 'dd'. $ftp->value.'---';
		}*/

		//itwExit();
		$Qbox = Doctrine_Query::create()
		->select('max(disc)+1 as max')
		->from('ProductsToBox')
		->where('box_id = ?', $products_id)
		->execute();
		$discNumber = 0;
		if($Qbox){
			$discNumber = $Qbox[0]['max'];
		}
		if ($discNumber <= 0){
			$discNumber = 1;
		}

		for ($i=0; $i<$discs; $i++){

			$newProduct = new Products();
			//$newProduct->products_quantity = $Product->products_quantity;
			//$newProduct->products_on_order = $Product->products_on_order;
			//$newProduct->products_date_ordered = $Product->products_date_ordered;
			$newProduct->products_model = $Product->products_model . '_' . $discNumber;
			$newProduct->products_image = $Product->products_image;
			//$newProduct->products_price = $Product->products_price;
			$newProduct->products_date_available = (empty($Product->products_date_available) ? 'null' : $Product->products_date_available);
			//$newProduct->products_weight = $Product->products_weight;
			$newProduct->products_status = 1;
			//$newProduct->products_featured = $Product->products_featured;
			$newProduct->products_tax_class_id = $Product->products_tax_class_id;
			$newProduct->products_type = $Product->products_type;
			$newProduct->products_in_box = 1;

			foreach($ProductDescription as $description){
				$lID = $description['language_id'];
				$newProduct->ProductsDescription[$lID]->language_id = $lID;
				$newProduct->ProductsDescription[$lID]->products_name = $description['products_name'] . ' (' . $discNumber . ' of ' . $discs . ')';
				//$newProduct->ProductsDescription[$lID]->products_seo_url = $description['products_seo_url'];
				$newProduct->ProductsDescription[$lID]->products_description = $description['products_description'];
				$newProduct->ProductsDescription[$lID]->products_head_title_tag = $description['products_head_title_tag'];
				$newProduct->ProductsDescription[$lID]->products_head_desc_tag = $description['products_head_desc_tag'];
				$newProduct->ProductsDescription[$lID]->products_head_keywords_tag = $description['products_head_keywords_tag'];
				$newProduct->ProductsDescription[$lID]->products_url = $description['products_url'];
				$newProduct->ProductsDescription[$lID]->products_viewed = '0';
			}

			foreach($ProductToCategories as $ptcInfo){
				$newProduct->ProductsToCategories[]->categories_id = $ptcInfo['categories_id'];
			}

			$newProduct->ProductsToBox[$i]->box_id = $products_id;
			$newProduct->ProductsToBox[$i]->disc = $discNumber;

			/*Move into extension*/
			foreach($GroupsToProducts as $grp){
				$newProduct->ProductsCustomFieldsGroupsToProducts[]->group_id = $grp->group_id;
			}

			foreach($FieldsToProducts as $ftp){

				$newProduct->ProductsCustomFieldsToProducts[$ftp->field_id]->field_id = $ftp->field_id;
				$newProduct->ProductsCustomFieldsToProducts[$ftp->field_id]->field_type = $ftp->field_type;
				$newProduct->ProductsCustomFieldsToProducts[$ftp->field_id]->value = $ftp->value;
			}
			/*end move*/
			$newProduct->save();
			$discNumber++;
		}
		$messageStack->addSession('pageStack', $discNumber . ' discs have been created', 'success');
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>