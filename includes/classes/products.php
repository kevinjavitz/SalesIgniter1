<?php
class storeProducts {
	public function __construct(){
	}
	
	public function getNew($categoryId = null, $limit, $width, $height){
		$Qproducts = Doctrine_Query::create()
		->select('p.products_id')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('p.products_status = ?', '1')
		->orderBy('p.products_date_added desc, pd.products_name asc')
		->limit($limit);
		
		if (is_null($categoryId) === false){
			$Qproducts->leftJoin('p.ProductsToCategories p2c')
			->leftJoin('p2c.Categories c')
			->andWhere('c.parent_id = ?', $categoryId);
		}
		
		EventManager::notify('NewProductsQueryBeforeExecute', &$Qproducts);

		$Result = $Qproducts->execute();
		$Qproducts->free();
		unset($Qproducts);
		$return = false;
		if ($Result){
			$return = array();
			foreach($Result->toArray() as $pInfo){
				$product = new product($pInfo['products_id']);
				if ($product->isValid()){
					$image = $product->getImage();

					EventManager::notify('ProductListingProductsImageShow', &$image, &$product);

					$imageHtml = htmlBase::newElement('image')
					->setSource($image)
					->thumbnailImage(true);
					if($width >0 && $height == 0){
						$imageHtml->setWidth($width)
						->bestFit(false);
					}elseif($width == 0 && $height >0){
						$imageHtml->setHeight($height)
						->bestFit(false);
					}else{
						$imageHtml->setHeight($height)
						->setWidth($width)
						->bestFit(true);
					}

					if (sizeof($product->productInfo['typeArr']) == 1 && in_array('reservation', $product->productInfo['typeArr'])){
						$price = false;
					}else{
						$purchaseTypeCls = $product->getPurchaseType('new');
						$price = $purchaseTypeCls->getPrice();
						if ($price <= 0){
							$price = false;
						}
					}
					$return[] = array(
						'id' => $product->getID(),
						'image' => $imageHtml->draw(),
						'name'  => $product->getName(),
						'price' => $price,
						'taxRate' => $product->getTaxRate()
					);
				}
			}
		}
		return $return;
	}
	
	public function getFeatured($limit = 0, $width, $height){
		$Qproducts = Doctrine_Query::create()
		->select('p.products_id')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('p.products_featured = ?', '1')
		->andWhere('p.products_status = ?', '1')
		->orderBy('rand()');
		//if ($limit > 0){
		$Qproducts->limit($limit);
		//}else{
		//	$Qproducts->limit(MAX_DISPLAY_NEW_PRODUCTS);
		//}
		
		EventManager::notify('FeaturedQueryBeforeExecute', $Qproducts);
		
		$Result = $Qproducts->execute();
		$Qproducts->free();
		unset($Qproducts);
		$return = false;
		if ($Result){
			$return = array();
			foreach($Result->toArray() as $pInfo){
				$product = new product($pInfo['products_id']);
				if ($product->isValid()){
					$image = $product->getImage();

					EventManager::notify('ProductListingProductsImageShow', &$image, &$product);

					$imageHtml = htmlBase::newElement('image')
					->setSource($image)
					->thumbnailImage(true);
					if($width >0 && $height == 0){
						$imageHtml->setWidth($width)
						->bestFit(false);
					}elseif($width == 0 && $height >0){
						$imageHtml->setHeight($height)
						->bestFit(false);
					}else{
						$imageHtml->setHeight($height)
						->setWidth($width)
						->bestFit(true);
					}

					$purchaseTypeCls = $product->getPurchaseType('new');
					$return[] = array(
						'id' => $product->getID(),
						'image' => $imageHtml->draw(),
						'name'  => $product->getName(),
						'price' => $purchaseTypeCls->displayPrice('new'),
						'taxRate' => $product->getTaxRate()
					);
				}
			}
		}
		return $return;
	}
	
	public function getBestSellers($limit = false, $width, $height){
		$Qproducts = Doctrine_Query::create()
		->select('p.products_id')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('p.products_ordered > ?', '0')
		->andWhere('p.products_status = ?', '1')
		->orderBy('p.products_ordered desc, pd.products_name asc');
		
		if ($limit !== false){
			$Qproducts->limit($limit);
		}
		
		EventManager::notify('BestSellerQueryBeforeExecute', $Qproducts);
		
		$Result = $Qproducts->execute();
		$Qproducts->free();
		unset($Qproducts);
		$return = false;
		if ($Result){
			$return = array();
			foreach($Result->toArray() as $pInfo){
				$product = new product($pInfo['products_id']);
				if ($product->isValid()){
					$image = $product->getImage();

					EventManager::notify('ProductListingProductsImageShow', &$image, &$product);

					$imageHtml = htmlBase::newElement('image')
					->setSource($image)
					->thumbnailImage(true);
					if($width >0 && $height == 0){
						$imageHtml->setWidth($width)
						->bestFit(false);
					}elseif($width == 0 && $height >0){
						$imageHtml->setHeight($height)
						->bestFit(false);
					}else{
						$imageHtml->setHeight($height)
						->setWidth($width)
						->bestFit(true);
					}

					$return[] = array(
						'id' => $product->getID(),
						'image' => $imageHtml->draw(),
						'name'  => $product->getName()
					);
				}
			}
		}
		return $return;
	}
}
?>