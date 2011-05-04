<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxProductsBox extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('productsBox');
		$this->firstAdded = false;
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}

	public function show(){
		$boxWidgetProperties = $this->getWidgetProperties();

		$this->setBoxId($boxWidgetProperties->id);

		$cInfo = $boxWidgetProperties->config;
		$Results = $this->getQueryResults($cInfo->query, $cInfo->query_limit);
		$ProductsList = $this->buildList($Results, $cInfo);
		
		$this->setBoxContent($ProductsList->draw());
		return $this->draw();
	}
	
	public function buildList($products, $cInfo){
		$List = htmlBase::newElement('ul')->addClass('productsBoxList');
		
		foreach($products as $pInfo){
			$ListItemImage = htmlBase::newElement('image')
			->setSource(sysConfig::getDirWsCatalog().'images/' . $pInfo['products_image'])
			->setWidth($cInfo->block_width)
			->setHeight($cInfo->block_height)
			->thumbnailImage(true);
			
			$productLink = itw_app_link('products_id=' . $pInfo['products_id'], 'product', 'info');
			
			if ($cInfo->reflect_blocks === true){
				$ListItemImage->addClass('productsBoxReflectImage');
			}
			
			$Block = htmlBase::newElement('div')
			->addClass('productsBoxBlock');
			
			$ListItemImageLink = htmlBase::newElement('a')
			->setHref($productLink)
			->append($ListItemImage);
			
			$ImageBlock = htmlBase::newElement('div')
			->addClass('productsBoxBlockImage')
			->append($ListItemImageLink);
			
			$Block->append($ImageBlock);
			
			if ($cInfo->reflect_blocks === false){
				$ListItemNameLink = htmlBase::newElement('a')
				->setHref($productLink)
				->html($pInfo['ProductsDescription'][0]['products_name']);
			
				$NameBlock = htmlBase::newElement('div')
				->addClass('productsBoxBlockName')
				->append($ListItemNameLink);
				
				$Block->append($NameBlock);
			}
			
			$ListItem = htmlBase::newElement('li')
			->css(array(
				'height' => $cInfo->block_height . 'px',
				'width' => $cInfo->block_width . 'px'
			))
			->append($Block);
			
			$List->append($ListItem);
		}
		
		$ListContainer = htmlBase::newElement('div')
		->addClass('productsBoxListContainer')
		->append($List);
		return $ListContainer;
	}
	
	public function getQueryResults($queryType, $queryLimit){
		$Query = Doctrine_Query::create()
		->select('p.products_id, p.products_image, pd.products_name')
		->from('Products p')
		->leftJoin('p.ProductsDescription pd')
		->where('p.products_status = ?', '1')
		->andWhere('pd.language_id = ?', Session::get('languages_id'));
		
		if ($queryLimit > 0){
			$Query->limit($queryLimit);
		}
		
		switch($queryType){
			case 'best_sellers':
				$Query->andWhere('p.products_ordered > ?', '0')
				->orderBy('p.products_ordered desc, pd.products_name asc');
				
				EventManager::notify('ScrollerBestSellersQueryBeforeExecute', &$Query);
				break;
			case 'featured':
				$Query->andWhere('p.products_featured = ?', '1');
				
				EventManager::notify('ScrollerFeaturedQueryBeforeExecute', &$Query);
				break;
			case 'new_products':
				$Query->orderBy('p.products_date_added desc, pd.products_name asc');
				
				EventManager::notify('ScrollerNewProductsQueryBeforeExecute', &$Query);
				break;
			case 'top_rentals':
				EventManager::notify('ScrollerTopRentalsQueryBeforeExecute', &$Query);
				break;
			case 'specials':
				EventManager::notify('ScrollerSpecialsQueryBeforeExecute', &$Query);
				break;
			case 'related':
				EventManager::notify('ScrollerRelatedQueryBeforeExecute', &$Query);
				break;
			case 'category':
				if (Session::exists('current_category_id')){
					$Query->leftJoin('p.ProductsToCategories p2c')
					->leftJoin('p2c.Categories c')
					->andWhere('c.parent_id = ?', Session::get('current_category_id'));
				}
		
				EventManager::notify('ScrollerCategoryQueryBeforeExecute', &$Query);
				break;
		}
		return $Query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}

	public function buildStylesheet(){
		$boxWidgetProperties = $this->getWidgetProperties();
		
		$css = '/* Products Box --BEGIN-- */' . "\n" . 
		'.productsBoxBlock { ' . 
			'text-align:center;' . 
		' }' . "\n" . 
		'.productsBoxBlockImage { ' . 
			'margin-left: auto;' . 
			'margin-right: auto;' . 
		' }' . "\n" . 
		'.productsBoxBlockName { ' . 
			'text-align:center;' . 
		' }' . "\n" . 
		'.productsBoxListContainer { ' . 
			'position:relative;' . 
			'display:inline-block;' . 
			'vertical-align:middle;' . 
			'overflow:hidden;' . 
			'background:transparent;' . 
		' }' . "\n" . 
		'.productsBoxList { ' . 
			'position:relative;' . 
			'list-style:none;' . 
			'display:block;' . 
			'vertical-align:middle;' . 
			'width:9999px;' . 
			'padding:0;' . 
			'margin:0;' . 
			'background:transparent;' . 
		' }' . "\n" . 
		'.productsBoxList li { ' . 
			'position:relative;' . 
			'display:inline-block;' . 
			'vertical-align:middle;' . 
			'background:transparent;' . 
		' }' . "\n" . 
		'/* Products Box --END-- */' . "\n";
		
		return $css;
	}
}
?>
