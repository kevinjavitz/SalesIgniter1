<?php
 /*Hidden dates*/
		$GalleryTable = Doctrine_Core::getTable('ProductGallery');
	    Doctrine_Query::create()
		->delete('ProductGallery')
			//->whereNotIn('price_per_rental_per_products_id', $saveArray)
		->andWhere('products_id =?', $Product->products_id)
		->execute();

		if(isset($_POST['gallery'])){
			foreach($_POST['gallery'] as $galleryId => $iGallery){
				$Gallery = $GalleryTable->create();
				$Gallery->file_name = $iGallery['image'];
				$Gallery->comments = $iGallery['comments'];
				$Gallery->products_id = $Product->products_id;
				$Gallery->save();
			}
		}
		/*End Hidden Dates*/
?>