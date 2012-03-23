<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

abstract class InfoBoxCustomScrollerAbstract
{

	abstract function show();

	public function buildPrevButton($image) {
		if(sysConfig::getDirWsCatalog() == '/' || (strpos($image, sysConfig::getDirWsCatalog()) === 0)){
			$imgPath = $image;
		}else{
			$imgPath = sysConfig::getDirWsCatalog() .$image;
		}
		$imgPath = str_replace('//','/', $imgPath);
		$imgInfo = getimagesize(sysConfig::getDirFsCatalog() . $image);
		return htmlBase::newElement('image')
			->addClass('scrollerPrevImage')
			->attr('data-width', $imgInfo[0])
			->attr('data-height', $imgInfo[1])
			->setSource($imgPath);
	}

	public function buildNextButton($image) {
		if(sysConfig::getDirWsCatalog() == '/' || (strpos($image, sysConfig::getDirWsCatalog()) === 0)){
			$imgPath = $image;
		}else{
			$imgPath = sysConfig::getDirWsCatalog() .$image;
		}
		$imgInfo = getimagesize(sysConfig::getDirFsCatalog() . $image);
		return htmlBase::newElement('image')
			->addClass('scrollerNextImage')
			->attr('data-width', $imgInfo[0])
			->attr('data-height', $imgInfo[1])
			->setSource($imgPath);
	}

	public function buildList($products, $cInfo) {
		$numOfLists = isset($cInfo->rows)?$cInfo->rows:1;

		$Lists = array();
		for($i=0; $i<$numOfLists; $i++){
			$Lists[] = htmlBase::newElement('ul')->addClass('scrollerList');
		}

		$i = 0;
		foreach($products as $pInfo){
			$ListItemImage = htmlBase::newElement('image')
				->setSource('/' . sysConfig::get('DIR_WS_IMAGES') . $pInfo['products_image'])
				->setWidth($cInfo->block_width)
				->setHeight($cInfo->block_height)
				->thumbnailImage(true);

			$productLink = itw_app_link('products_id=' . $pInfo['products_id'], 'product', 'info');

			if ($cInfo->reflect_blocks === true){
				$ListItemImage->addClass('scrollerReflectImage');
			}

			$ScrollBlock = htmlBase::newElement('div')
				->addClass('scrollBlock')
				->css('width', $cInfo->block_width . 'px');

			$ListItemImageLink = htmlBase::newElement('a')
				->setHref($productLink)
				->append($ListItemImage);

			$ImageBlock = htmlBase::newElement('div')
				->addClass('scrollBlockImage')
				->append($ListItemImageLink);

			$ScrollBlock->append($ImageBlock);

			if ($cInfo->show_product_name === true && $cInfo->reflect_blocks === false){
				$ListItemNameLink = htmlBase::newElement('a')
					->setHref($productLink)
					->html($pInfo['ProductsDescription'][0]['products_name']);

				$NameBlock = htmlBase::newElement('div')
					->addClass('scrollBlockName')
					->append($ListItemNameLink);

				$ScrollBlock->append($NameBlock);
			}

			$ListItem = htmlBase::newElement('li')
				->append($ScrollBlock);

			$Lists[$i]->append($ListItem);
			$i++;
			if ($i == $numOfLists){
				$i = 0;
			}
		}

		$ListContainer = htmlBase::newElement('div')
			->addClass('scrollerListContainer')
			->attr('data-block_height', $cInfo->block_height * $numOfLists);
		foreach($Lists as $List){
			$ListContainer->append($List);
		}
		return $ListContainer;
	}

    public function getAllSubCategories($categoriesId, &$categoriesArray){
        $Categories = Doctrine_Query::create()
            ->select('categories_id')
            ->from('Categories')
            ->where('parent_id = ?', $categoriesId)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        if (!empty($Categories)) {
            foreach($Categories as $catInfo){
                $categoriesArray[] = $catInfo['categories_id'];
                $this->getAllSubCategories($catInfo['categories_id'],$categoriesArray);
            }
        }
    }

	public function getQueryResults($queryType, $queryLimit, $selectedCategory = 0) {
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
            case 'category_featured':
                $catsArray = array($selectedCategory);
                $this->getAllSubCategories($selectedCategory, $catsArray);

                $Query->andWhere('p.products_featured = ?', '1')
                        ->leftJoin('p.ProductsToCategories p2c')
                        //->leftJoin('p2c.Categories c')
                        ->andWhere('p2c.categories_id in (' . implode(',',$catsArray) . ')');//, '"' . array(implode(',',$catsArray) . '"'));


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
				global $current_product_id;				
				EventManager::notify('ScrollerRelatedQueryBeforeExecute', &$Query);
				break;
			case 'category':
				global $current_category_id;
				
				if ($current_category_id>0){
					$Query->leftJoin('p.ProductsToCategories p2c')
						->leftJoin('p2c.Categories c')
						->andWhere('c.parent_id = ? OR p2c.categories_id= ?', array($current_category_id, $current_category_id));
				}

				EventManager::notify('ScrollerCategoryQueryBeforeExecute', &$Query);
				break;
		}
		return $Query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
}

class InfoBoxCustomScrollerStack extends InfoBoxCustomScrollerAbstract
{

	public function __construct($scrollers) {
		$this->Scrollers = $scrollers;
		$this->ScrollersConfig = $scrollers->configs;
	}

	public function show() {
		$ScrollInterface = htmlBase::newElement('div')
			->addClass('scrollBoxesStackedContainer');

		foreach($this->ScrollersConfig as $i => $cInfo){
			$PrevButton = $this->buildPrevButton($cInfo->prev_image);
			$NextButton = $this->buildNextButton($cInfo->next_image);
			$Results = $this->getQueryResults($cInfo->query, $cInfo->query_limit, (isset($cInfo->selected_category)?$cInfo->selected_category:''));
			
			if (!empty($Results)) {
				$ProductsList = $this->buildList($Results, $cInfo);

				$Scoller = htmlBase::newElement('div')
					->addClass('scrollBoxStacked')
					->append($PrevButton)
					->append($ProductsList)
					->append($NextButton);

				$ScrollInterface->append($Scoller);
			}
		}

		return $ScrollInterface->draw();
	}
}

class InfoBoxCustomScrollerTabs extends InfoBoxCustomScrollerAbstract
{

	public function __construct($scrollers) {
		$this->Scrollers = $scrollers;
		$this->ScrollersConfig = $scrollers->configs;
	}

	public function show() {
		$ScrollInterface = htmlBase::newElement('div')
			->addClass('scrollBoxesTabsContainer');

		$Tabs = htmlBase::newElement('tabs');
		foreach($this->ScrollersConfig as $i => $cInfo){
			$PrevButton = $this->buildPrevButton($cInfo->prev_image);
			$NextButton = $this->buildNextButton($cInfo->next_image);
			$Results = $this->getQueryResults($cInfo->query, $cInfo->query_limit, (isset($cInfo->selected_category)?$cInfo->selected_category:''));
			$ProductsList = $this->buildList($Results, $cInfo);

			$Scoller = htmlBase::newElement('div')
				->addClass('scrollBoxTabContent')
				->append($PrevButton)
				->append($ProductsList)
				->append($NextButton);

			$Tabs->addTabHeader('page_' . $i, array('text' => $cInfo->headings->{Session::get('languages_id')}));
			$Tabs->addTabPage('page_' . $i, array('text' => $Scoller));
		}

		$ScrollInterface->append($Tabs);

		return $ScrollInterface->draw();
	}
}

class InfoBoxCustomScrollerButtons extends InfoBoxCustomScrollerAbstract
{

	public function __construct($scrollers) {
		$this->Scrollers = $scrollers;
		$this->ScrollersConfig = $scrollers->configs;
	}

	public function show() {
		$ScrollInterface = htmlBase::newElement('div')
			->addClass('scrollBoxesButtonsContainer');

		$Pages = htmlBase::newElement('div')->addClass('scrollBoxButtonsPages');
		$Buttons = htmlBase::newElement('div')->addClass('scrollBoxButtonsButtons');
		foreach($this->ScrollersConfig as $i => $cInfo){
			$PrevButton = $this->buildPrevButton($cInfo->prev_image);
			$NextButton = $this->buildNextButton($cInfo->next_image);
			$Results = $this->getQueryResults($cInfo->query, $cInfo->query_limit, isset($cInfo->selected_category)?$cInfo->selected_category:'');
			$ProductsList = $this->buildList($Results, $cInfo);

			$uniquePageNum = floor(time() / ($i + 1));
			$Scroller = htmlBase::newElement('div')
				->addClass('scrollBoxButtonsContent')
				->addClass('scrollerBoxButtonsPage-' . $uniquePageNum)
				->append($PrevButton)
				->append($ProductsList)
				->append($NextButton);

			$Pages->append($Scroller);
			$Buttons->append(htmlBase::newElement('button')->addClass('ui-state-default')
				->attr('data-page', $uniquePageNum)->setText($cInfo->headings->{Session::get('languages_id')}));
		}

		$ScrollInterface->append($Pages);
		$ScrollInterface->append($Buttons);

		return $ScrollInterface->draw();
	}

	public static function buildJavascript() {
		$javascript = '' . "\n" .
			'	$(\'.scrollBoxButtonsContent\').each(function (){' . "\n" .
			'		$(this).bind(\'scrollerReady\', function (){' . "\n" .
			'		});' . "\n" .
			'	});' . "\n" .
			'' . "\n" .
			'	$(\'.scrollBoxesButtonsContainer\').each(function (){' . "\n" .
			'		$(this).bind(\'containerReady\', function (){' . "\n" .
			'			var $container = $(this);' . "\n" .
			'			$(this).find(\'button\').each(function (){' . "\n" .
			'				$(this).click(function (e){' . "\n" .
			'					e.stopImmediatePropagation();' . "\n" .
			'					if (!$(this).hasClass(\'ui-state-selected\')){' . "\n" .
			'						$(\'.scrollBoxButtonsContent\').hide();' . "\n" .
			'						$(\'.scrollerBoxButtonsPage-\' + $(this).data(\'page\')).show();' . "\n" .
			'						$(this).parent().find(\'.ui-state-selected\').removeClass(\'ui-state-selected\');' . "\n" .
			'						$(this).addClass(\'ui-state-selected\');' . "\n" .
			'						$container.parent().parent().find(\'.ui-infobox-header-text\').html($(this).find(\'.ui-button-text\').html());' . "\n" .
			'					}' . "\n" .
			'				}).mouseover(function (e){' . "\n" .
			'					e.stopImmediatePropagation();' . "\n" .
			'					if (!$(this).hasClass(\'ui-state-selected\')){' . "\n" .
			'						$(this).addClass(\'ui-state-hover\');' . "\n" .
			'					}' . "\n" .
			'				}).mouseout(function (e){' . "\n" .
			'					e.stopImmediatePropagation();' . "\n" .
			'					if (!$(this).hasClass(\'ui-state-selected\')){' . "\n" .
			'						$(this).removeClass(\'ui-state-hover\');' . "\n" .
			'					}' . "\n" .
			'				});' . "\n" .
			'			});' . "\n" .
			'			$(this).find(\'button\').first().click();' . "\n" .
			'' . "\n" .
			'			$(this).find(\'.scrollBoxButtonsContent\').each(function (i, el){' . "\n" .
			'				if (i > 0) $(this).hide();' . "\n" .
			'			});' . "\n" .
			'		})' . "\n" .
			'	})' . "\n" .
			'' . "\n";

		return $javascript;
	}
}

class InfoBoxCustomScroller extends InfoBoxAbstract
{

	public function __construct() {
		global $App;
		$this->init('customScroller');
		$this->firstAdded = false;
		$this->buildStylesheetMultiple = false;
		$this->buildJavascriptMultiple = false;
	}

	public function show() {
		$boxWidgetProperties = $this->getWidgetProperties();

		$className = 'InfoBoxCustomScroller' . ucfirst($boxWidgetProperties->scrollers->type);
		$this->Scroller = new $className($boxWidgetProperties->scrollers);
		$this->setBoxContent($this->Scroller->show());
		return $this->draw();
	}

	public function buildStylesheet() {
		$boxWidgetProperties = $this->getWidgetProperties();

		$css = '/* Custom Scroller --BEGIN-- */' . "\n" .
			'.scrollBlock { ' .
			'text-align:center;' .
			' }' . "\n" .
			'.scrollBlockImage { ' .
			'margin-left: auto;' .
			'margin-right: auto;' .
			' }' . "\n" .
			'.scrollBlockName { ' .
			'text-align:center;' .
			' }' . "\n" .
			'.scrollerPrevImage {' .
			' position:relative;' .
			'display:inline-block;' .
			'vertical-align:middle;' .
			' }' . "\n" .
			'.scrollerNextImage { ' .
			'position:relative;' .
			'display:inline-block;' .
			'vertical-align:middle;' .
			' }' . "\n" .
			'.scrollerListContainer { ' .
			'position:relative;' .
			'display:inline-block;' .
			'vertical-align:middle;' .
			'overflow:hidden;' .
			'background:transparent;' .
			' }' . "\n" .
			'.scrollerList { ' .
			'position:relative;' .
			'list-style:none;' .
			'display:block;' .
			'vertical-align:middle;' .
			'width:9999px;' .
			'padding:0;' .
			'margin:0;' .
			'background:transparent;' .
			' }' . "\n" .
			'.scrollerList li { ' .
			'position:relative;' .
			'display:inline-block;' .
			'vertical-align:middle;' .
			'background:transparent;' .
			' }' . "\n" .
			'.scrollBoxesStackedContainer { ' .
			'margin:.5em;' .
			'background:transparent;' .
			' }' . "\n" .
			'.scrollBoxStacked { ' .
			'margin:1em;' .
			'background:transparent;' .
			' }' . "\n" .
			'.scrollBoxesTabsContainer { ' .
			' }' . "\n" .
			'.scrollBoxTabContent { ' .
			' }' . "\n" .
			'.scrollBoxesButtonsContainer { ' .
			' }' . "\n" .
			'.scrollBoxButtonsContent { ' .
			' }' . "\n" .
			'.scrollBoxButtonsButtons {' .
			'text-align: center;' .
			'margin-top: 1em;' .
			' }' . "\n" .
			'.scrollerReflectImage img, .scrollerReflectImage canvas { display:block;margin-right:auto;margin-left:auto; }' . 
			'/* Custom Scroller --END-- */' . "\n";

		$className = 'InfoBoxCustomScroller' . ucfirst($boxWidgetProperties->scrollers->type);
		if (method_exists($className, 'buildStylesheet')){
			$css .= $className::buildStylesheet();
		}

		return $css;
	}

	public function buildJavascript() {
		$boxWidgetProperties = $this->getWidgetProperties();

		$javascript = '';
		$className = 'InfoBoxCustomScroller' . ucfirst($boxWidgetProperties->scrollers->type);
		if (method_exists($className, 'buildJavascript')){
			$javascript .= $className::buildJavascript();
		}
		ob_start();
?>

			$('.scrollBoxesStackedContainer, .scrollBoxesTabsContainer, .scrollBoxesButtonsContainer').each(function (){
				var $Container = $(this);

				$Container.find('.scrollBoxStacked, .scrollBoxTabContent, .scrollBoxButtonsContent').each(function (){
					var $Scroller = $(this);
					var $ListContainer = $Scroller.find('.scrollerListContainer');
					var $PrevButton = $Scroller.find('.scrollerPrevImage');
					var $NextButton = $Scroller.find('.scrollerNextImage');
					var PrevLoaded = false;
					var NextLoaded = false;

					var ContainerWidth = parseInt($Scroller.innerWidth());
					var PrevButtonWidth = parseInt($PrevButton.data('width'));
					var NextButtonWidth = parseInt($NextButton.data('width'));

					var ListWidth = ContainerWidth - PrevButtonWidth - NextButtonWidth - 10;
					$ListContainer.width(ListWidth + 'px');

					var maxOffset;
					
					//Calculate actual width of scroll elements
					var blocksWidth = 0;
					$ListContainer.find('.scrollerList li').each(function(){
						blocksWidth += $(this).outerWidth(true);
					});
					$Scroller.data('maxOffset', -blocksWidth);

					$ListContainer.find('.scrollerReflectImage').reflect();

					$Scroller.trigger('scrollerReady');


					var listOffset = 0;

					
					function scrollPrev(){
						if (listOffset == 0) return false;

							listOffset += $ListContainer.outerWidth();
							$ListContainer.find('ul').animate({
								left: listOffset
							});
							
						return true;
					}
					
					function scrollNext(){					
						if ((listOffset - $ListContainer.outerWidth()) < $Scroller.data('maxOffset')) return false;

						listOffset -= $ListContainer.outerWidth();

						$ListContainer.find('ul').animate({
							left: listOffset
						});
						
						return true;
					}
					

					$PrevButton.click(function (){
						scrollPrev();
					});
					
					$NextButton.click(function (){
						scrollNext();
					});					
				});
				$Container.trigger('containerReady');
			});

<?php
		$javascriptSource = ob_get_contents();
		ob_end_clean();

		$javascript .= '/* Custom Scroller --BEGIN-- */' . "\n" .
			$javascriptSource .
			'/* Custom Scroller --END-- */' . "\n";

		return $javascript;
	}
}

?>