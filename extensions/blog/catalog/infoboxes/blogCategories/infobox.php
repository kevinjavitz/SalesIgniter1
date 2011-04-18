<?php
class InfoBoxBlogCategories extends InfoBoxAbstract {
	
	public function __construct(){
		$this->init('blogCategories', 'blog');
		$this->enabled = true;
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_BLOG_CATEGORIES'));

	}

	public function getCats(){
	global $cPath_array;
		$Qcategories = Doctrine_Query::create()
		->select('c.blog_categories_id, cd.blog_categories_title, cd.blog_categories_seo_url, c.parent_id')
		->from('BlogCategories c')
		->leftJoin('c.BlogCategoriesDescription cd')
		->where('c.parent_id = ?', '0')
		->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('c.sort_order, cd.blog_categories_title');


		$Result = $Qcategories->execute(array(), Doctrine::HYDRATE_ARRAY);

		$menuContainer = htmlBase::newElement('div')
		->attr('id', 'blogcategoriesModuleMenu');

		if ($Result){
		    foreach($Result as $idx => $cInfo){
		        $categoryId = $cInfo['blog_categories_id'];
		        $parentId = $cInfo['parent_id'];
		        $categoryName = $cInfo['BlogCategoriesDescription'][0]['blog_categories_title'];
		        $categorySEO = $cInfo['BlogCategoriesDescription'][0]['blog_categories_seo_url'];

		        $headerEl = htmlBase::newElement('a');
		        if (isset($cPath_array) && $cPath_array[0] == $categoryId){
		            $headerEl->addClass('currentCategory');
		        }
		        $headerEl->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">'.$categoryName)
						 ->addClass('blogInfoboxLink')
						 ->setHref(itw_app_link('appExt=blog', 'show_category' , $categorySEO));

		        $Qchildren = Doctrine_Query::create()
		        ->select('c.blog_categories_id, cd.blog_categories_title, cd.blog_categories_seo_url, c.parent_id')
		        ->from('BlogCategories c')
		        ->leftJoin('c.BlogCategoriesDescription cd')
		        ->where('c.parent_id = ?', $categoryId)
		        ->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		        ->orderBy('c.sort_order, cd.blog_categories_title');

		        $currentChildren = $Qchildren->execute();

		        $flyoutContainer = htmlBase::newElement('div');
		        $ulElement = htmlBase::newElement('list');
		        if ($currentChildren->count() > 0){
		            foreach($currentChildren->toArray() as $child){
		                addBlogChildren($child, $categoryId, &$ulElement);
		            }
		            /* $childLinkEl = htmlBase::newElement('a')
		            ->addClass('')
		            ->css('border-color', 'transparent')
		            ->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">View Posts</span>')
		            ->setHref(itw_app_link('appExt=blog', 'show_category', $categorySEO ));

		            $liElement = htmlBase::newElement('li')
		            ->append($childLinkEl);
		            $ulElement->addItemObj($liElement);*/
		        }else{
		           /* $childLinkEl = htmlBase::newElement('a')
		            ->addClass('')
		            ->css('border-color', 'transparent')
		            ->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span class="ui-categories-text" style="vertical-align:middle;">View Posts</span>')
		            ->setHref(itw_app_link('appExt=blog', 'show_category' , $categorySEO));

		            $liElement = htmlBase::newElement('li')
		            ->append($childLinkEl);
		            $ulElement->addItemObj($liElement);*/
		        }
		        $flyoutContainer->append($ulElement);

		        $menuContainer->append($headerEl)->append($flyoutContainer);
		    }
		}
		return $menuContainer->draw();
	}

	
	public function show(){
		
		if ($this->enabled === false) return;
		
		$this->setBoxContent($this->getCats());
		
		return $this->draw();
	}
}
?>