<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class Extension_blog extends ExtensionBase {

		public function __construct() {
			parent::__construct('blog');
		}

		public function init() {
			global $App;
			if ($this->isEnabled() === false) return;

			//load extenders
			$this->extenders = array();
			$dirIterate = new DirectoryIterator(sysConfig::getDirFsCatalog()  . 'extensions/blog/data/extender/');
			$directories = array();
			foreach($dirIterate as $fileObj){
				if ($fileObj->isDot() || $fileObj->isFile()){
					continue;
				}

				$directories[] = array(
					'path' => $fileObj->getPath(),
					'basename' => $fileObj->getBasename()
				);
			}

			foreach($directories as $dirInfo){

				if(file_exists(sysConfig::getDirFsCatalog()  . 'extensions/blog/data/extender/'.$dirInfo['basename'].'/fields.xml')){
					$extenderXml = simplexml_load_file(sysConfig::getDirFsCatalog()  .'extensions/blog/data/extender/'.$dirInfo['basename'].'/fields.xml');

					$this->extenders[(string)$extenderXml->info->key]['title'] = (string)$extenderXml->info->title;
					$fields = array();
					foreach($extenderXml->fields->field as $field){
						$fields[(string)$field->name]['type'] = (string)$field->type;
						$fields[(string)$field->name]['table'] = (string)$field->table;
						$fields[(string)$field->name]['realtype'] = (string)$field->realtype;
						$fields[(string)$field->name]['showName'] = (string)$field->showName;
						$fields[(string)$field->name]['default'] = (string)$field->default;
						$fields[(string)$field->name]['showFunction'] = (string)$field->showFunction;
						$fields[(string)$field->name]['searchFunction'] = (string)$field->searchFunction;
					}
					$this->extenders[(string)$extenderXml->info->key]['fields'] = $fields;

				}

			}

			//in the menu add for every key a menu for categories and posts
			//filter by key the posts
			//in the new category show new fields for fields in the key array and get the default data from the extra_field
			//the same in new posts

			if (isset($_GET['appExt']) && $_GET['appExt'] == 'blog'){
				if ($App->getAppName() == 'show_category'){
					$eventFunction = 'addCategoryHeaderTags';
				}elseif ($App->getAppName() == 'show_archive'){
					$eventFunction = 'addArchiveHeaderTags';
				}elseif ($App->getAppName() == 'show_post'){
					$eventFunction = 'addPostHeaderTags';
				}
				EventManager::attachEvent(array(
					'name'     => 'HeaderTagsBeforeOutput',
					'function' => $eventFunction
				), null, $this);
				if ($App->getEnv() == 'catalog' && isset($_GET['appExt']) && $_GET['appExt'] == 'blog' && $_GET['app'] == 'show_category' ){
					$App->setAppPage('default');
					$_GET['actualPage'] = $_GET['appPage'];
				}
			}

			EventManager::attachEvents(array(
				'PageLeftColumnBeforeInfobox',
				'PageRightColumnBeforeInfobox',
				'BoxModulesAddLink',
				'PageLayoutHeaderCustomMeta'
			), null, $this);
		}

		public function getFilesUploadPath($fileType='image', $type='abs'){
			global $fileTypeUploadDirs;
			if($type == 'abs'){
				return sysConfig::getDirFsCatalog() . 'extensions/blog/' . $fileTypeUploadDirs[$fileType]['rel'];
			} else {
				return sysConfig::getDirWsCatalog() . 'extensions/blog/' . $fileTypeUploadDirs[$fileType]['rel'];
			}
		}

		public function BoxModulesAddLink(&$contents){
			$subChildren = array();

			foreach($this->extenders as $key => $fields){
				$subChildren[] = array('link' => false,
									'text' => $fields['title'],
									'children' => array(array('link' => itw_app_link('appExt=blog&key='.$key,'blog_categories','default','SSL'), 'text' => 'Categories'),
														array('link' => itw_app_link('appExt=blog&key='.$key,'blog_posts','default','SSL'), 'text' => 'Posts'))
				);

			}
			if(count($subChildren) > 0){
				$contents['children'][] = array(
					'link' => false,
					'text' => 'Blog Extenders',
					'children' => $subChildren
				);
			}
		}

		public function getCategoryHeaderTitle($seo_title){

			$Query = Doctrine_Query::create()
			->select('c.blog_categories_id, cd.*')
			->from('BlogCategories c')
			->leftJoin('c.BlogCategoriesDescription cd')
			->where('cd.blog_categories_seo_url = ?', $seo_title)
			->andWhere('cd.language_id = ?', (int)Session::get('languages_id'));

			$cInfo = $Query->fetchOne();
			if ($cInfo){
				return  $cInfo['BlogCategoriesDescription'][(int)Session::get('languages_id')]['blog_categories_title'];
			}
			return false;
		}

		public function getPostCategories($blog_post_id){
			global $appExtension;
			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				} else{
					$this->checkMultiStore = false;
				}
			}

			$Query = Doctrine_Query::create()
				->select('cd.*, cc.*, c.*')
				->from('BlogPostToCategories c')
				->leftJoin('c.BlogCategories cc')
				->leftJoin('cc.BlogCategoriesDescription cd')
				->where('cd.language_id = ?', (int) Session::get('languages_id'))
				->andWhere('c.blog_post_id = ?', (int) $blog_post_id);

			if ($this->checkMultiStore === true){
				$Query->addSelect('ps.*')
					->leftJoin('cc.BlogCategoriesToStores ps')
					->andWhere('ps.stores_id = ?', (int) Session::get('current_store_id'));
			}
			return $Query->execute();
		}

		public function getCategoryHeaderDescription($seo_title){

			$Query = Doctrine_Query::create()
			->select('c.blog_categories_id, cd.*')
			->from('BlogCategories c')
			->leftJoin('c.BlogCategoriesDescription cd')
			->where('cd.blog_categories_seo_url = ?', $seo_title)
			->andWhere('cd.language_id = ?', (int)Session::get('languages_id'));

			$cInfo = $Query->fetchOne();
			if ($cInfo){
				return  $cInfo['BlogCategoriesDescription'][(int)Session::get('languages_id')]['blog_categories_description_text'];
			}
			return false;
		}

		public function getArchiveHeaderTitle($seo_title){

			if($seo_title){
				$arch = explode('-', $seo_title);
				return $arch[0] . ' ' . $arch[1];
			}
			return false;
		}

		public function getPostHeaderTitle($seo_title){

			$Query = Doctrine_Query::create()
			->select('p.*, pd.*, pc.*, pcc.*')
			->from('BlogPosts p')
			->leftJoin('p.BlogPostsDescription pd');
			$Query->where('pd.language_id = ?', (int)Session::get('languages_id'));

			$Query->andWhere('pd.blog_post_seo_url = ?', $_GET['appPage']);

			$cInfo = $Query->fetchOne();

			if ($cInfo){
				return   $cInfo['BlogPostsDescription'][(int)Session::get('languages_id')]['blog_post_title'];
			}
			return false;
		}

		public function PageLayoutHeaderCustomMeta(){
			if (sysConfig::get('EXTENSION_BLOG_SHOW_RSS_ON_BAR') == 'True'){
				echo '<link rel="alternate" type="application/rss+xml"
  					  title="'.sysConfig::get('STORE_NAME').' RSS" href="'.itw_app_link('appExt=blog','show_category','rss').'">';
			}
		}

		public function addCategoryHeaderTags($eventName, &$tags_array){

			$Query = Doctrine_Query::create()
			->select('c.blog_categories_id, cd.*')
			->from('BlogCategories c')
			->leftJoin('c.BlogCategoriesDescription cd')
			->where('cd.blog_categories_seo_url = ?', $_GET['appPage'])
			->andWhere('cd.language_id = ?', (int)Session::get('languages_id'));

			$cInfo = $Query->fetchOne();
			if ($cInfo){
				$tags_array['title'] =  $cInfo['BlogCategoriesDescription'][(int)Session::get('languages_id')]['blog_categories_htc_title'];
				$tags_array['desc'] =  $cInfo['BlogCategoriesDescription'][(int)Session::get('languages_id')]['blog_categories_htc_desc'];
				$tags_array['keywords'] =  $cInfo['BlogCategoriesDescription'][(int)Session::get('languages_id')]['blog_categories_htc_keywords'];
			}
		}

		public function addArchiveHeaderTags($eventName, &$tags_array){

			$arch = explode('-', $_GET['appPage']);

			$tags_array['title'] = $arch[0] . ' ' . $arch[1];
			$tags_array['desc'] = $arch[0] . ' ' . $arch[1];
			$tags_array['keywords'] = $arch[0] . ' ' . $arch[1];

		}

		public function addPostHeaderTags($eventName, &$tags_array){

			$Query = Doctrine_Query::create()
			->select('p.*, pd.*, pc.*, pcc.*')
			->from('BlogPosts p')
			->leftJoin('p.BlogPostsDescription pd');
			$Query->where('pd.language_id = ?', (int)Session::get('languages_id'));

			$Query->andWhere('pd.blog_post_seo_url = ?', $_GET['appPage']);

			$cInfo = $Query->fetchOne();

			if ($cInfo){
				$tags_array['title'] =  $cInfo['BlogPostsDescription'][(int)Session::get('languages_id')]['blog_post_head_title'];
				$tags_array['desc'] =  $cInfo['BlogPostsDescription'][(int)Session::get('languages_id')]['blog_post_head_desc'];
				$tags_array['keywords'] =  $cInfo['BlogPostsDescription'][(int)Session::get('languages_id')]['blog_post_head_keywords'];

			}
		}

		public function getCategories($languageId = null, $parent = -1) {
			global $appExtension;

			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				} else{
					$this->checkMultiStore = false;
				}
			}

			$Query = Doctrine_Query::create()
			->select('c.*, cd.*')
			->from('BlogCategories c')
			->leftJoin('c.BlogCategoriesDescription cd')
			->orderBy('c.sort_order, cd.blog_categories_title');

			if (is_null($languageId) === false){
				$Query->where('cd.language_id = ?', (int) $languageId);
			} else{
				$Query->where('cd.language_id = ?', (int) Session::get('languages_id'));
			}

			if ($this->checkMultiStore === true){
				$Query->addSelect('pc.*')
				->leftJoin('c.BlogCategoriesToStores pc')
				->andWhere('pc.stores_id = ?', (int) Session::get('current_store_id'));
			}

			if($parent > -1){
				$Query->where('c.parent_id = ?', (int) $parent);
			}

			$Result = $Query->execute();

			return $Result;
		}

		public function getCategoriesPosts($languageId = null, $seo_cat = null, $pg_limit, $pg, &$pagerBar, $categ = -1) {
			global $appExtension;

			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				} else{
					$this->checkMultiStore = false;
				}
			}

			$Query = Doctrine_Query::create()
			->select('p.*, pd.*, cd.*, cc.*, c.*')
			->from('BlogPosts p')
			->leftJoin('p.BlogPostsDescription pd')
			->leftJoin('p.BlogPostToCategories c')
			->leftJoin('c.BlogCategories cc')
			->leftJoin('cc.BlogCategoriesDescription cd')
			->where('p.post_status = 1')
			->orderBy('p.post_date desc');

			if (is_null($seo_cat) === false){
				$Query->andWhere('cd.blog_categories_seo_url = ?', $seo_cat);
			}else{

			}

			if (is_null($languageId) === false){
				$Query->andWhere('pd.language_id = ?', (int) $languageId);
				$Query->andWhere('cd.language_id = ?', (int) $languageId);
			} else{
				$Query->andWhere('pd.language_id = ?', (int) Session::get('languages_id'));
				$Query->andWhere('cd.language_id = ?', (int) Session::get('languages_id'));
			}

			if($categ > -1){
				$Query->andWhere('cc.blog_categories_id = ?', $categ);
			}

			if ($this->checkMultiStore === true){
				$Query->addSelect('ps.*')
				->leftJoin('cc.BlogCategoriesToStores ps')
				->andWhere('ps.stores_id = ?', (int) Session::get('current_store_id'));
			}

			$listingPager = new Doctrine_Pager($Query, $pg, $pg_limit);
			$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

			$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 5
			));

			$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
			$pagerLayout->setMyType('posts');
			$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;background-color:#ffffff;padding:3px;">{%page}</a>');
			$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

			$pager = $pagerLayout->getPager();

			$Result = $pager->execute()->toArray(true);

			$pagerBar = $pagerLayout->display(array(), true);

			return $Result;
		}

		public function getPostsWithPaging($languageId = null, $seo_cat = null, $pg_limit, $pg, &$pagerBar) {
			global $appExtension;

			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				} else{
					$this->checkMultiStore = false;
				}
			}

			$Query = Doctrine_Query::create()
				->from('BlogPosts p')
				->leftJoin('p.BlogPostsDescription pd')
				->leftJoin('p.BlogPostToCategories c')
				->leftJoin('c.BlogCategories cc')
				->leftJoin('cc.BlogCategoriesDescription cd')
				->where('p.post_status = 1')
				->orderBy('p.post_date desc');

			if (is_null($languageId) === false){
				$Query->andWhere('pd.language_id = ?', (int) $languageId);
				//$Query->andWhere('cd.language_id = ?', (int) $languageId);
			} else{
				$Query->andWhere('pd.language_id = ?', (int) Session::get('languages_id'));
				//$Query->andWhere('cd.language_id = ?', (int) Session::get('languages_id'));
			}
			if (is_null($seo_cat) === false){
				$Query->andWhere('cd.blog_categories_seo_url = ?', $seo_cat);
			}

			if ($this->checkMultiStore === true){
				$Query->leftJoin('cc.BlogCategoriesToStores ps')
					->andWhere('ps.stores_id = ?', (int) Session::get('current_store_id'));
			}

			$listingPager = new Doctrine_Pager($Query, $pg, $pg_limit);
			$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

			$pagerRange = new Doctrine_Pager_Range_Sliding(array(
				'chunk' => 5
			));

			$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
			$pagerLayout->setMyType('posts');
			$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;background-color:#ffffff;padding:3px;">{%page}</a>');
			$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

			$pager = $pagerLayout->getPager();

			$Result = $pager->execute()->toArray(true);

			$pagerBar = $pagerLayout->display(array(), true);

			return $Result;
		}


		public function getPosts($languageId = null, $seo_title = null){
			global $appExtension;

			/*if (!isset($this->checkMultiStore)){
			$multiStore = $appExtension->getExtension('multiStore');
			if ($multiStore !== false && $multiStore->isEnabled() === true){
			$this->checkMultiStore = true;
			}else{
			$this->checkMultiStore = false;
			}
			}*/

			$Query = Doctrine_Query::create()
			->select('p.*, pd.*, pc.*, pcc.*')
			->from('BlogPosts p')
			->leftJoin('p.BlogPostsDescription pd')
			->leftJoin('p.BlogCommentToPost pc')
			->leftJoin('pc.BlogComments pcc');

			if (is_null($languageId) === false){
				$Query->where('pd.language_id = ?', (int)$languageId);
			}else{
				$Query->where('pd.language_id = ?', (int)Session::get('languages_id'));
			}

			$Query->andWhere('pd.blog_post_seo_url = ?', $seo_title);
			$Result = $Query->fetchOne();

			return $Result;
		}

		public function getArchives($languageId = null) {
			global $appExtension;

			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				} else{
					$this->checkMultiStore = false;
				}
			}

			/*
			modify to get month and year of posts or show a list of month-year from the first blogpost to the last blogpost. When a single post exist in the month-year pair the datetitle is shown.
			*/
			$Query = Doctrine_Query::create()
			->select('MONTH(p.post_date) as month, YEAR(p.post_date) as year')
			->from('BlogPosts p')
			->leftJoin('p.BlogPostToCategories c')
			->leftJoin('c.BlogCategories cc')
			->where('p.post_status = 1');

			if ($this->checkMultiStore === true){
				$Query->leftJoin('cc.BlogCategoriesToStores ps')
				->andWhere('ps.stores_id = ?', (int) Session::get('current_store_id'));
			}

			$Query->distinct(true);

			$Result = $Query->execute();

			return $Result;
		}

		public function getArchivesPosts($languageId = null, $archiveMonth = null, $archiveYear = null, $pg_limit, $pg, &$pagerBar) {
			global $appExtension;

			if (!isset($this->checkMultiStore)){
				$multiStore = $appExtension->getExtension('multiStore');
				if ($multiStore !== false && $multiStore->isEnabled() === true){
					$this->checkMultiStore = true;
				} else{
					$this->checkMultiStore = false;
				}
			}

			$Query = Doctrine_Query::create()
			->select('p.*, pd.*, cd.*, cc.*, c.*')
			->from('BlogPosts p')
			->leftJoin('p.BlogPostsDescription pd')
			->leftJoin('p.BlogPostToCategories c')
			->leftJoin('c.BlogCategories cc')
			->leftJoin('cc.BlogCategoriesDescription cd')
			->where('p.post_status = 1')
			->orderBy('p.post_date desc');

			if (is_null($archiveMonth) === false){
				$Query->andWhere('MONTH(p.post_date) = ?', $archiveMonth);
			}

			if (is_null($archiveYear) === false){
				$Query->andWhere('YEAR(p.post_date) = ?', $archiveYear);
			}

			if (is_null($languageId) === false){
				$Query->andWhere('pd.language_id = ?', (int) $languageId);
			} else{
				$Query->andWhere('pd.language_id = ?', (int) Session::get('languages_id'));
			}

			if (is_null($languageId) === false){
				$Query->andWhere('cd.language_id = ?', (int) $languageId);
			} else{
				$Query->andWhere('cd.language_id = ?', (int) Session::get('languages_id'));
			}

			if ($this->checkMultiStore === true){
				$Query->addSelect('ps.*')
				->leftJoin('cc.BlogCategoriesToStores ps')
				->andWhere('ps.stores_id = ?', (int) Session::get('current_store_id'));
			}

			//$Result = $Query->execute();
			$listingPager = new Doctrine_Pager($Query, $pg, $pg_limit);
			$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

			$pagerRange = new Doctrine_Pager_Range_Sliding(array(
			'chunk' => 5
			));

			$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
			$pagerLayout->setMyType('posts');
			$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;background-color:#ffffff;padding:3px;">{%page}</a>');
			$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

			$pager = $pagerLayout->getPager();

			$Result = $pager->execute()->toArray(true);

			$pagerBar = $pagerLayout->display(array(), true);

			return $Result;
		}

		public function PageLeftColumnBeforeInfobox(&$infoboxes, &$isshow){

			if(isset($_GET['appExt']) && strpos($_GET['appExt'],'blog') !== false ){
				if(sysConfig::get('EXTENSION_BLOG_SHOW_ONLY_INFOBOXES') == 'True'){

					if((strpos($infoboxes->getBoxCode(),'blogCategories') !== false || strpos($infoboxes->getBoxCode(),'blogArchives') !== false )){
						$isshow = true;
					}else{
						$isshow = false;
					}
				}else{
					$isshow = true;
				}
			}else{

				if(sysConfig::get('EXTENSION_BLOG_SHOW_INFOBOXES_ON_REST_SITE') == 'True'){
					$isshow = true;
				}else{
					if((strpos($infoboxes->getBoxCode(),'blogCategories') !== false || strpos($infoboxes->getBoxCode(),'blogArchives') !== false )){
						$isshow = false;
					}else{
						$isshow = true;
					}
				}
			}
		}

		public function PageRightColumnBeforeInfobox(&$infoboxes, &$isshow){

			if(isset($_GET['appExt']) && strpos($_GET['appExt'],'blog') !== false ){
				if(sysConfig::get('EXTENSION_BLOG_SHOW_ONLY_INFOBOXES') == 'True'){

					if((strpos($infoboxes->getBoxCode(),'blogCategories') !== false || strpos($infoboxes->getBoxCode(),'blogArchives') !== false )){
						$isshow = true;
					}else{
						$isshow = false;
					}
				}else{
					$isshow = true;
				}
			}else{

				if(sysConfig::get('EXTENSION_BLOG_SHOW_INFOBOXES_ON_REST_SITE') == 'True'){
					$isshow = true;
				}else{
					if((strpos($infoboxes->getBoxCode(),'blogCategories') !== false || strpos($infoboxes->getBoxCode(),'blogArchives') !== false )){
						$isshow = false;
					}else{
						$isshow = true;
					}
				}
			}
		}

	}


	/*blog frontend function*/
	function addBlogChildren($child, $currentPath, &$ulElement) {
		global $current_blog_category_id;
		$currentPath .= '_' . $child['blog_categories_id'];
		$currentSEO = $child['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_seo_url'];

		$childLinkEl = htmlBase::newElement('a')
		->addClass('ui-widget ui-widget-content ui-corner-all blogInfoboxLink')
		->html('<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span><span style="display:inline-block;vertical-align:middle;">' . $child['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'] . '</span>')
		->setHref(itw_app_link('appExt=blog', 'show_category' , $currentSEO ));

		if ($child['blog_categories_id'] == $current_blog_category_id){
			$childLinkEl->addClass('selected');
		}

		$Qchildren = Doctrine_Query::create()
		->select('c.blog_categories_id, cd.blog_categories_title, c.parent_id')
		->from('BlogCategories c')
		->leftJoin('c.BlogCategoriesDescription cd')
		->where('c.parent_id = ?', $child['blog_categories_id'])
		->andWhere('cd.language_id = ?', (int)Session::get('languages_id'))
		->orderBy('c.sort_order, cd.blog_categories_title');

		$currentParentChildren = $Qchildren->execute()->toArray(true);

		$children = false;
		if ($currentParentChildren){
			$childLinkEl
			->html(
			'<span style="float:right;" class="ui-icon ui-icon-triangle-1-e"></span>' .
			'<span style="line-height:1.5em;">' .
			'<span class="ui-icon ui-icon-triangle-1-e ui-icon-categories-bullet" style="vertical-align:middle;"></span>' .
			'<span style="vertical-align:middle;">' .
			$child['BlogCategoriesDescription'][Session::get('languages_id')]['blog_categories_title'] .
			'</span>' .
			'</span>');

			$children = htmlBase::newElement('list')
			->addClass('ui-widget ui-widget-content ui-corner-all ui-menu-flyout')
			->css('display', 'none');
			foreach($currentParentChildren as $childInfo){
				addBlogChildren($childInfo, $currentPath, &$children);
			}
		}

		$liElement = htmlBase::newElement('li')
		->append($childLinkEl);
		if ($children){
			$liElement->append($children);
		}
		if ($ulElement->hasListItems()){
			/*$liElement->css(array(
			'border-top' => '1px solid #313332'
			));*/
		}
		$ulElement->addItemObj($liElement);
	}
	/*end blog*/

	/*Blog functions*/
    if(!function_exists('tep_friendly_seo_url')){
		function tep_friendly_seo_url($string){
			$string = preg_replace("`\[.*\]`U","",$string);
			$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
			$string = htmlentities($string, ENT_COMPAT, 'utf-8');
			$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
			$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
			return strtolower(trim($string, '-'));
		}
    }

	function tep_set_post_status($post_id, $status) {
		if ($status == '1') {
			return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update blog_posts set post_status = '1' where post_id = '" . (int)$post_id . "'");
		} elseif ($status == '0') {
			return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update blog_posts set post_status = '0' where post_id = '" . (int)$post_id . "'");
		} else {
			return -1;
		}
	}

	function tep_set_comment_status($comment_id, $status) {
		if ($status == '1') {
			return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update blog_comments set comment_status = '1' where comment_id = '" . (int)$comment_id . "'");
		} elseif ($status == '0') {
			return Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->exec("update blog_comments set comment_status = '0' where comment_id = '" . (int)$comment_id . "'");
		} else {
			return -1;
		}
	}

	function tep_get_blog_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
		if (!is_array($category_tree_array)) $category_tree_array = array();
		if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));

		if ($include_itself) {
			$category_query = tep_db_query("select cd.blog_categories_title from blog_categories_description cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.blog_categories_id = '" . (int)$parent_id . "'");
			$category = tep_db_fetch_array($category_query);
			$category_tree_array[] = array('id' => $parent_id, 'text' => $category['blog_categories_title']);
		}

		$categories_query = tep_db_query("select c.blog_categories_id, cd.blog_categories_title, c.parent_id from blog_categories c, blog_categories_description cd where c.blog_categories_id = cd.blog_categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.blog_categories_title");
		while ($categories = tep_db_fetch_array($categories_query)) {
			if ($exclude != $categories['blog_categories_id']) $category_tree_array[] = array('id' => $categories['blog_categories_id'], 'text' => $spacing . $categories['blog_categories_title']);
			$category_tree_array = tep_get_blog_category_tree($categories['blog_categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
		}

		return $category_tree_array;
	}


	function tep_articles_in_blog_category_count($categories_id, $include_deactivated = false) {
		$products_count = 0;

		if ($include_deactivated) {
			$products_query = tep_db_query("select count(*) as total from blog_posts p, blog_post_to_categories p2c where p.post_id = p2c.blog_post_id and p2c.blog_categories_id = '" . (int)$categories_id . "'");
		} else {
			$products_query = tep_db_query("select count(*) as total from blog_posts p, blog_post_to_categories p2c where p.post_id = p2c.blog_post_id and p.post_status = '1' and p2c.blog_categories_id = '" . (int)$categories_id . "'");
		}

		$products = tep_db_fetch_array($products_query);

		$products_count += $products['total'];

		$childs_query = tep_db_query("select blog_categories_id from blog_categories where parent_id = '" . (int)$categories_id . "'");
		if (tep_db_num_rows($childs_query)) {
			while ($childs = tep_db_fetch_array($childs_query)) {
				$products_count += tep_articles_in_blog_category_count($childs['blog_categories_id'], $include_deactivated);
			}
		}

		return $products_count;
	}

	////
	// Count how many subcategories exist in a category
	// TABLES: categories
	function tep_childs_in_blog_category_count($categories_id) {
		$categories_count = 0;

		$categories_query = tep_db_query("select blog_categories_id from blog_categories where parent_id = '" . (int)$categories_id . "'");
		while ($categories = tep_db_fetch_array($categories_query)) {
			$categories_count++;
			$categories_count += tep_childs_in_blog_category_count($categories['blog_categories_id']);
		}

		return $categories_count;
	}

	/*end blog functions*/

	/*blog html_ouput*/
	function tep_get_blog_category_tree_list($parent_id = '0', $checked = false, $include_itself = true) {
		if (tep_childs_in_blog_category_count($parent_id) > 0){
			if (!is_array($checked)){
				$checked = array();
			}
			$catList = '<ul class="catListingUL">';

			if ($parent_id == '0'){
				$category_query = tep_db_query("select cd.blog_categories_title from blog_categories_description cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.blog_categories_id = '" . (int)$parent_id . "'");
				if (tep_db_num_rows($category_query)){
					$category = tep_db_fetch_array($category_query);

					$catList .= '<li>' . tep_draw_checkbox_field('blog_categories[]', $parent_id, (in_array($parent_id, $checked)), 'id="catCheckbox_' . $parent_id . '"') . '<label for="catCheckbox_' . $parent_id . '">' . $category['blog_categories_title'] . '</label></li>';
				}
			}

			$categories_query = tep_db_query("select c.blog_categories_id, cd.blog_categories_title, c.parent_id from blog_categories c, blog_categories_description cd where c.blog_categories_id = cd.blog_categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.blog_categories_title");
			while ($categories = tep_db_fetch_array($categories_query)) {
				$catList .= '<li>' . tep_draw_checkbox_field('blog_categories[]', $categories['blog_categories_id'], (in_array($categories['blog_categories_id'], $checked)), 'id="catCheckbox_' . $categories['blog_categories_id'] . '"') . '<label for="catCheckbox_' . $categories['blog_categories_id'] . '">' . $categories['blog_categories_title'] . '</label></li>';
				if (tep_childs_in_blog_category_count($categories['blog_categories_id']) > 0){
					$catList .= '<li class="subCatContainer">' . tep_get_blog_category_tree_list($categories['blog_categories_id'], $checked, false) . '</li>';
				}
			}
			$catList .= '</ul>';
		}

		return $catList;
	}
	/*end blog*/

?>