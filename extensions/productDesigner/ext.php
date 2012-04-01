<?php
/*
	Product Designer Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_productDesigner extends ExtensionBase {

	public function __construct(){
		parent::__construct('productDesigner');
	}

	public function init(){
		global $App, $appExtension;
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'ApplicationTopActionCheckPost',
			'CheckoutProductAfterProductName',
			'CheckoutProcessInsertOrderedProduct',
			'InsertOrderedProductBeforeSave',
			'OrderClassQueryFillProductArray',
			'OrderProductAfterProductName',
			'ProductListingProductsImageShow',
			'ProductInfoProductsImageShow',
			'PopupProductImageShow',
			'ProductInfoButtonBarAddButton'
		), null, $this);

			require(dirname(__FILE__) . '/classEvents/ShoppingCart.php');
			$eventClass = new ShoppingCart_productDesigner();
			$eventClass->init();

			require(dirname(__FILE__) . '/classEvents/ShoppingCartProduct.php');
			$eventClass = new ShoppingCartProduct_productDesigner();
			$eventClass->init();

			require(dirname(__FILE__) . '/classEvents/ShoppingCartDatabase.php');
			$eventClass = new ShoppingCartDatabaseActions_productDesigner();
			$eventClass->init();

		/*
		 * @TODO: Does this really need to be here?
		 */
		global $predesign_cPath, $current_predesign_category_id, $predesign_cPath_array;
		if (isset($_GET['predesign_cPath'])) {
			$predesign_cPath = $_GET['predesign_cPath'];
		} else {
			$predesign_cPath = '';
		}

		if (tep_not_null($predesign_cPath)) {
			$predesign_cPath_array = tep_parse_predesign_category_path($predesign_cPath);
			$predesign_cPath = implode('_', $predesign_cPath_array);
			$current_predesign_category_id = $predesign_cPath_array[(sizeof($predesign_cPath_array)-1)];
		} else {
			$current_predesign_category_id = 0;
		}

		/*
		 * @TODO: Does this really need to be here?
		 */
		global $clipart_cPath, $current_clipart_category_id, $clipart_cPath_array;
		if (isset($_GET['clipart_cPath'])) {
			$clipart_cPath = $_GET['clipart_cPath'];
		} else {
			$clipart_cPath = '';
		}

		if (tep_not_null($clipart_cPath)) {
			$clipart_cPath_array = tep_parse_clipart_category_path($clipart_cPath);
			$clipart_cPath = implode('_', $clipart_cPath_array);
			$current_clipart_category_id = $clipart_cPath_array[(sizeof($clipart_cPath_array)-1)];
		} else {
			$current_clipart_category_id = 0;
		}
	}

	public function ProductInfoButtonBarAddButton($product){
		$return = '';
		if ($product->productInfo['product_designable'] == '1'){
			$return = htmlBase::newElement('button')
			->css('float', 'right')
			->setText('Customize')
			->setHref(itw_app_link('appExt=productDesigner&' . tep_get_all_get_params(), 'design', 'default', 'NONSSL'))
			->draw();
		}
		return $return;
	}

	public function PopupProductImageShow(&$image, $imgSrc){
		$Qpredesign = Doctrine_Query::create()
		->select('predesign_id')
		->from('Products')
		->where('products_id = ?', $_GET['pID'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qpredesign){
			$imgInfo = getimagesize($imgSrc);
			$image = htmlBase::newElement('img')
			->attr('src', itw_app_link('width=' . $imgInfo[0] . '&height=' . $imgInfo[1] . '&appExt=productDesigner&products_id=' . $_GET['pID'] . '&predesign_id=' . $Qpredesign[0]['predesign_id'], 'predesign_thumb', 'process'))
			->draw();
		}
	}

	public function ProductInfoProductsImageShow(&$image, &$productClass, $width = SMALL_IMAGE_WIDTH, $height = SMALL_IMAGE_HEIGHT){
		$this->ProductListingProductsImageShow(&$image, &$productClass, $width, $height);
	}

	public function ProductListingProductsImageShow(&$image, &$productClass, $width = SMALL_IMAGE_WIDTH, $height = SMALL_IMAGE_HEIGHT){
		global $appExtension, $current_category_id;
		$predesignId = $productClass->productInfo['predesign_id'];

		$Qcategory = Doctrine_Query::create()
		->select('product_designer_correlation_type, product_designer_correlation_id')
		->from('Categories')
		->where('categories_id = ?', $current_category_id)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcategory && $Qcategory[0]['product_designer_correlation_id'] > 0){
			if ($Qcategory[0]['product_designer_correlation_type'] == 'activity'){
				$Qpredesigns = Doctrine_Query::create()
				->select('predesign_id')
				->from('ProductDesignerPredesigns')
				->where('FIND_IN_SET(' . $Qcategory[0]['product_designer_correlation_id'] . ', predesign_activities)')
				->andWhere('predesign_location = ?', 'front')
				->orderBy('RAND()')
				->limit('1')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}else{
				$Qpredesigns = Doctrine_Query::create()
				->select('predesign_id')
				->from('ProductDesignerPredesignsToPredesignCategories')
				->where('categories_id = ?', $Qcategory[0]['product_designer_correlation_id'])
				->orderBy('RAND()')
				->limit('1')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			}

			if ($Qpredesigns){
				Session::set('product_desginer_correlation', array(
					'correlationType' => $Qcategory[0]['product_designer_correlation_type'],
					'correlationId'   => $Qcategory[0]['product_designer_correlation_id'],
					'predesignId'     => $Qpredesigns[0]['predesign_id']
				), $productClass->getID());
				$predesignId = $Qpredesigns[0]['predesign_id'];
			}
		}

		if (!empty($predesignId) && $productClass->productInfo['product_designable'] == '1'){
			$multiStore = $appExtension->getExtension('multiStore');
			if ($multiStore !== false && $multiStore->isEnabled() === true){
				$productDefaults = explode(',', $productClass->productInfo['product_designer_default_set']);
				if (in_array(Session::get('current_store_id'), $productDefaults)){
					$images_id = 'product';
				}else{
					$Qimages = Doctrine_Query::create()
					->select('images_id')
					->from('ProductDesignerProductImages')
					->where('products_id = ?', $productClass->getID())
					->andWhere('FIND_IN_SET(' . Session::get('current_store_id') . ', default_set)')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qimages){
						$imagesId = $Qimages[0]['images_id'];
					}else{
						$imagesId = 'product';
					}
				}
			}elseif ($productClass->productInfo['product_designer_default_set'] == '1'){
				$imagesId = 'product';
			}else{
				$Qimages = Doctrine_Query::create()
				->select('images_id')
				->from('ProductDesignerProductImages')
				->where('products_id = ?', $productClass->getID())
				->andWhere('default_set = ?', '1')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$imagesId = $Qimages[0]['images_id'];
			}

			if (!isset($imagesId) || $imagesId == 0 || $imagesId == ''){
				$imagesId = 'product';
			}

			$image = itw_app_link('width=' . $width . '&height=' . $height . '&appExt=productDesigner&products_id=' . $productClass->getID() . '&predesign_id=' . $predesignId . '&images_id=' . $imagesId, 'predesign_thumb', 'process');
		}
	}

	public function OrderClassQueryFillProductArray(&$pInfo, &$product){
		if (!empty($pInfo['design_info'])){
			$designInfo = unserialize($pInfo['design_info']);
			if (isset($designInfo['front'])){
				$product['predesign'] = $designInfo;
			}else{
				$product['custom_design'] = $designInfo;
			}
		}
	}

	public function buildProductPredesignUrl($location, $designInfo){
		$urlVars = array();
		foreach($designInfo as $key => $val){
			if ($key != 'front' && $key != 'back' && $key != 'front_cost' && $key != 'back_cost'){
				$urlVars[] = $key . '=' . $val;
			}
		}

		if (sizeof($urlVars > 0)){
			$urlVars = '&' . implode('&', $urlVars);
		}else{
			$urlVars = '';
		}

		$link = itw_catalog_app_link('width=' . SMALL_IMAGE_WIDTH . '&height=' . SMALL_IMAGE_HEIGHT . '&appExt=productDesigner&predesign_id=' . $designInfo[$location] . $urlVars, 'predesign_thumb', 'process');
		return $link;
	}

	public function buildProductDesignUrl($productId, $purchaseType, $designInfo, $useImage = '0'){
		global $App;
		if ($App->getEnv() == 'admin'){
			$productIdName = 'orders_products_id';
		}else{
			$productIdName = 'products_id';
		}
		$link = itw_catalog_app_link('dpi=30&appExt=productDesigner&' . $productIdName . '=' . $productId . '&useProductImage=' . $useImage . '&purchaseType=' . $purchaseType, 'custom_thumb', 'process');
		return $link;
	}

	public function getPredesignCostStr($loc, $predesign){
		global $currencies;
		if (isset($predesign[$loc . '_cost']) && $predesign[$loc . '_cost'] > 0){
			return '&nbsp;(+' . $currencies->format($predesign[$loc . '_cost']) . ')';
		}
	}

	public function OrderProductAfterProductName(&$cartProduct){
		return $this->CheckoutProductAfterProductName(&$cartProduct);
	}

	public function CheckoutProductAfterProductName($cartProduct){
		if (is_array($cartProduct)){

			if (isset($cartProduct['predesign'])){
				$predesignInfo = $cartProduct['predesign'];
				$img = '';
				if (isset($predesignInfo['front'])){
					$img .= '<br /><small>-&nbsp;<i>Front Design' . $this->getPredesignCostStr('front', $predesignInfo) . ':</i></small><br /><img src="' . $this->buildProductPredesignUrl('front', $predesignInfo) . '" />';
				}
				if (isset($predesignInfo['back'])){
					$img .= '<br /><small>-&nbsp;<i>Back Design' . $this->getPredesignCostStr('back', $predesignInfo) . ':</i></small><br /><img src="' . $this->buildProductPredesignUrl('back', $predesignInfo) . '" />';
				}
				return $img;
			}

			if (isset($cartProduct['custom_design'])){
				$img = '<br /><small>-&nbsp;<i>Design:</i></small><br /><img src="' . $this->buildProductDesignUrl($cartProduct['opID'], $cartProduct['purchase_type'], $cartProduct['custom_design']) . '" />';
				return $img;
			}

		}else{

			if ($cartProduct->hasInfo('predesign')){
				$predesignInfo = $cartProduct->getInfo('predesign');
				$predesignInfo['products_id'] = (int)$cartProduct->getIdString();
				$img = '';
				if (isset($predesignInfo['front'])){
					$img .= '<br /><small>-&nbsp;<i>Front Design' . $this->getPredesignCostStr('front', $predesignInfo) . ':</i></small><br /><img src="' . $this->buildProductPredesignUrl('front', $predesignInfo) . '" />';
				}
				if (isset($predesignInfo['back'])){
					$img .= '<br /><small>-&nbsp;<i>Back Design' . $this->getPredesignCostStr('back', $predesignInfo) . ':</i></small><br /><img src="' . $this->buildProductPredesignUrl('back', $predesignInfo) . '" />';
				}
				return $img;
			}

			if ($cartProduct->hasInfo('custom_design')){
				$img = '<br /><small>-&nbsp;<i>Design:</i></small><br /><img src="' . $this->buildProductDesignUrl($cartProduct->opID, $cartProduct->getPurchaseType(), $cartProduct->getInfo('custom_design')) . '" />';
				return $img;
			}

		}
	}

	public function InsertOrderedProductBeforeSave(&$newOrdersProduct, &$cartProduct){
		if ($cartProduct->hasInfo('predesign')){
			$newOrdersProduct->design_info = serialize($cartProduct->getInfo('predesign'));
		}

		if ($cartProduct->hasInfo('custom_design')){
			$newOrdersProduct->design_info = serialize($cartProduct->getInfo('custom_design'));
		}
	}

	public function CheckoutProcessInsertOrderedProduct($cartProduct, &$products_ordered){
		if ($cartProduct->hasInfo('predesign')){
			$predesignInfo = $cartProduct->getInfo('predesign');
			$products_ordered .= 'Predesign Id: ' . $info[''];
			if (isset($predesignInfo['front'])){
				$products_ordered .= '- Front Design Id: ' . $predesignInfo['front'];
			}
			if (isset($predesignInfo['back'])){
				$products_ordered .= '- Back Design Id: ' . $predesignInfo['back'];
			}
		}

		if ($cartProduct->hasInfo('custom_design')){
			//$newOrdersProduct->design_info = serialize($cartProduct->getInfo('custom_design'));
		}
	}

	public function ApplicationTopActionCheckPost(&$action){
		if (isset($_POST['productDesignerZoomImage']) || (isset($_GET['action']) && $_GET['action'] == 'productDesignerZoomImage')){
			$action = 'productDesignerZoomImage';
		}
	}

	public function zoomImage($eventName){
	}

	public function getDesignerDimensions($imageInfo){
		global $product;
		$pixelsPerInch = 72;
		$zoom = $imageInfo['zoom'];

		if (is_object($product)){
			$Qarea = Doctrine_Query::create()
			->from('ProductDesignerEditableAreas')
			->where('products_id = ?', $product->getId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qarea){
				$editableWidth = $Qarea[0]['area_width'];
				$editableHeight = $Qarea[0]['area_height'];
				$editableWidthInches = $Qarea[0]['area_width_inches'];
				$editableHeightInches = $Qarea[0]['area_height_inches'];
				$editableX = $Qarea[0]['area_x1'];
				$editableY = $Qarea[0]['area_y1'];
			}
		}

		if (strstr($imageInfo['source'], ',')){
			$image = explode(',', $imageInfo['source']);
		}else{
			$image = getimagesize($_SERVER['DOCUMENT_ROOT'] . $imageInfo['source']);
		}

		$prodWidthInches = $image[0] / $pixelsPerInch;
		$prodHeightInches = $image[1] / $pixelsPerInch;

		$dimensions = array(
			'zoom'  => $imageInfo['zoom'],
			'scale' => (($editableWidthInches * $pixelsPerInch) / $editableWidth)
		);

		$dimensions['image'] = array(
			'width' => array(
				'px' => $image[0],
				'in' => $prodWidthInches
			),
			'height' => array(
				'px' => $image[1],
				'in' => $prodHeightInches
			)
		);

		$dimensions['prod'] = array(
			'width' => array(
				'px' => $image[0],
				'in' => $prodWidthInches
			),
			'height' => array(
				'px' => $image[1],
				'in' => $prodHeightInches
			)
		);

		$dimensions['editable'] = array(
			'width' => array(
				'px' => $editableWidth,
				'in' => $editableWidthInches
			),
			'height' => array(
				'px' => $editableHeight,
				'in' => $editableHeightInches
			),
			'pos' => array(
				'x' => $editableX * $dimensions['zoom'],
				'y' => $editableY * $dimensions['zoom']
			)
		);

		$dimensions['editable']['zoom'] = array(
			'width'  => array(
				'px' => $dimensions['editable']['width']['px'] * $dimensions['zoom'],
				'in' => $dimensions['editable']['width']['in'] * $dimensions['zoom']
			),
			'height' => array(
				'px' => $dimensions['editable']['height']['px'] * $dimensions['zoom'],
				'in' => $dimensions['editable']['height']['in'] * $dimensions['zoom']
			)
		);

		$dimensions['image']['zoom'] = array(
			'width'  => array(
				'px' => $dimensions['image']['width']['px'] * $dimensions['zoom'],
				'in' => $dimensions['image']['width']['in'] * $dimensions['zoom']
			),
			'height' => array(
				'px' => $dimensions['image']['height']['px'] * $dimensions['zoom'],
				'in' => $dimensions['image']['height']['in'] * $dimensions['zoom']
			)
		);

		return $dimensions;
	}

	public function isCustomizable($productInfo){
		return ($productInfo['customizable'] == '1');
	}
}

/*
 * Clipart Categories path
 */

	function tep_parse_clipart_category_path($clipart_cPath) {
		// make sure the category IDs are integers
		$clipart_cPath_array = array_map('tep_string_to_int', explode('_', $clipart_cPath));

		// make sure no duplicate category IDs exist which could lock the server in a loop
		$tmp_array = array();
		$n = sizeof($clipart_cPath_array);
		for ($i=0; $i<$n; $i++) {
			if (!in_array($clipart_cPath_array[$i], $tmp_array)) {
				$tmp_array[] = $clipart_cPath_array[$i];
			}
		}

		return $tmp_array;
	}

	function tep_get_clipart_path($current_clipart_category_id = '') {
		global $clipart_cPath_array;

		if ($current_clipart_category_id == '') {
			$clipart_cPath_new = implode('_', $clipart_cPath_array);
		} else {
			if (sizeof($clipart_cPath_array) == 0) {
				$clipart_cPath_new = $current_clipart_category_id;
			} else {
				$clipart_cPath_new = '';
				$last_clipart_category_query = tep_db_query("select parent_id from product_designer_clipart_categories where categories_id = '" . (int)$clipart_cPath_array[(sizeof($clipart_cPath_array)-1)] . "'");
				$last_clipart_category = tep_db_fetch_array($last_clipart_category_query);

				$current_clipart_category_query = tep_db_query("select parent_id from product_designer_clipart_categories where categories_id = '" . (int)$current_clipart_category_id . "'");
				$current_clipart_category = tep_db_fetch_array($current_clipart_category_query);

				if ($last_clipart_category['parent_id'] == $current_clipart_category['parent_id']) {
					for ($i = 0, $n = sizeof($clipart_cPath_array) - 1; $i < $n; $i++) {
						$clipart_cPath_new .= '_' . $clipart_cPath_array[$i];
					}
				} else {
					for ($i = 0, $n = sizeof($clipart_cPath_array); $i < $n; $i++) {
						$clipart_cPath_new .= '_' . $clipart_cPath_array[$i];
					}
				}

				$clipart_cPath_new .= '_' . $current_clipart_category_id;

				if (substr($clipart_cPath_new, 0, 1) == '_') {
					$clipart_cPath_new = substr($clipart_cPath_new, 1);
				}
			}
		}
		return 'clipart_cPath=' . $clipart_cPath_new;
	}


	function tep_childs_in_clipart_category_count($categories_id) {
		$categories_count = 0;

		$categories_query = tep_db_query("select categories_id from product_designer_clipart_categories where parent_id = '" . (int)$categories_id . "'");
		while ($categories = tep_db_fetch_array($categories_query)) {
			$categories_count++;
			$categories_count += tep_childs_in_clipart_category_count($categories['categories_id']);
		}
		return $categories_count;
	}


	function tep_get_admin_clipart_category_tree($parent_id = '0', $spacing = '', $exclude = '', $clipart_category_tree_array = '', $include_itself = false) {
		if (!is_array($clipart_category_tree_array)) $clipart_category_tree_array = array();
		if ( (sizeof($clipart_category_tree_array) < 1) && ($exclude != '0') ) $clipart_category_tree_array[] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));

		if ($include_itself) {
			$clipart_category_query = tep_db_query("select cd.categories_name from product_designer_clipart_categories_description cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.categories_id = '" . (int)$parent_id . "'");
			$clipart_category = tep_db_fetch_array($clipart_category_query);
			$clipart_category_tree_array[] = array('id' => $parent_id, 'text' => $clipart_category['categories_name']);
		}

		$clipart_categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from product_designer_clipart_categories c, product_designer_clipart_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
		while ($clipart_categories = tep_db_fetch_array($clipart_categories_query)) {
			if ($exclude != $clipart_categories['categories_id']) $clipart_category_tree_array[] = array('id' => $clipart_categories['categories_id'], 'text' => $spacing . $clipart_categories['categories_name']);
			$clipart_category_tree_array = tep_get_admin_clipart_category_tree($clipart_categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $clipart_category_tree_array);
		}

		return $clipart_category_tree_array;
	}
	/*Clipart Categories*/
	function childs_category_count($categories_id) {

		$categories_count = 0;

		$Qcategories = Doctrine_Query::create()
			->select('c.categories_id')
			->from('ProductDesignerClipartCategories c')
			->where('c.parent_id = ?',(int)$categories_id)
			->execute();

		if($Qcategories) {
			foreach($Qcategories as $category) {
				$categories_count++;
				$categories_count += childs_category_count($category->categories_id);
			}
		}
		return $categories_count;

	}

	function get_category_tree($parent_id = '0', $i = 0) {
		if (childs_category_count($parent_id) > 0) {
			if ($i == 0){
				$cat_tree = '<ul id="clipartBrowser" class="filetree treeview-famfamfam">'."\n";
			}else{
				$cat_tree = '<ul>'."\n";
			}
			$Qcategories = Doctrine_Query::create()
				->select('c.*, cd.categories_name as cat')
				->from('ProductDesignerClipartCategories c')
				->leftJoin('c.ProductDesignerClipartCategoriesDescription cd')
				->where('cd.language_id = ?', Session::get('languages_id'))
				->orderBy('c.sort_order, cd.categories_name');

			$Qcategories->andWhere('c.parent_id = ?', (int)$parent_id);
			$q = $Qcategories->execute();

			if($q) {
				foreach ($q as $subcategory) {
					$i++;
					$cat_tree .= '<li><span class="folder" onclick="catclick('.$subcategory->categories_id.')">'.$subcategory->ProductDesignerClipartCategoriesDescription[Session::get('languages_id')]['categories_name'].'</span>'."\n";
					if (childs_category_count($subcategory->categories_id) > 0) {
						$cat_tree .= get_category_tree($subcategory->categories_id, $i);
					}
					$cat_tree .= '</li>'."\n";
				}
			}
			$cat_tree .= '</ul>'."\n";
		}
		return $cat_tree;
	}

	/*End Clipart Categories*/
	/*function tep_images_in_category_count($categories_id) {
	$images_count = 0;

	$images_query = tep_db_query("select count(*) as total from " . TABLE_CLIPART_IMAGES . " p, " . TABLE_CLIPART_IMAGES_TO_CATEGORIES . " p2c where p.images_id = p2c.images_id and p2c.categories_id = '" . (int)$categories_id . "'");

	$images = tep_db_fetch_array($images_query);

	$images_count += $products['total'];

	$childs_query = tep_db_query("select categories_id from " . TABLE_CLIPART_CATEGORIES . " where parent_id = '" . (int)$categories_id . "'");
	if (tep_db_num_rows($childs_query)) {
		while ($childs = tep_db_fetch_array($childs_query)) {
			$images_count += tep_images_in_category_count($childs['categories_id']);
		}
	}

	return $images_count;
}
*/

/*
 * @TODO: Move to the extension class
 */
	function tep_parse_predesign_category_path($predesign_cPath) {
		// make sure the category IDs are integers
		$predesign_cPath_array = array_map('tep_string_to_int', explode('_', $predesign_cPath));

		// make sure no duplicate category IDs exist which could lock the server in a loop
		$tmp_array = array();
		$n = sizeof($predesign_cPath_array);
		for ($i=0; $i<$n; $i++) {
			if (!in_array($predesign_cPath_array[$i], $tmp_array)) {
				$tmp_array[] = $predesign_cPath_array[$i];
			}
		}

		return $tmp_array;
	}

	function tep_get_predesign_category_tree($parent_id = '0', $spacing = '', $exclude = '', $predesign_category_tree_array = '', $include_itself = false) {
		if (!is_array($predesign_category_tree_array)) $predesign_category_tree_array = array();
		if ( (sizeof($predesign_category_tree_array) < 1) && ($exclude != '0') ) $predesign_category_tree_array[] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));

		if ($include_itself) {
			$predesign_category_query = tep_db_query("select cd.categories_name from product_designer_predesign_categories_description cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.categories_id = '" . (int)$parent_id . "'");
			$predesign_category = tep_db_fetch_array($predesign_category_query);
			$predesign_category_tree_array[] = array('id' => $parent_id, 'text' => $predesign_category['categories_name']);
		}

		$predesign_categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from product_designer_predesign_categories c, product_designer_predesign_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
		while ($predesign_categories = tep_db_fetch_array($predesign_categories_query)) {
			if ($exclude != $predesign_categories['categories_id']) $predesign_category_tree_array[] = array('id' => $predesign_categories['categories_id'], 'text' => $spacing . $predesign_categories['categories_name']);
			$predesign_category_tree_array = tep_get_predesign_category_tree($predesign_categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $predesign_category_tree_array);
		}

		return $predesign_category_tree_array;
	}

	function tep_get_predesign_path($current_predesign_category_id = '') {
		global $predesign_cPath_array;

		if ($current_predesign_category_id == '') {
			$predesign_cPath_new = implode('_', $predesign_cPath_array);
		} else {
			if (sizeof($predesign_cPath_array) == 0) {
				$predesign_cPath_new = $current_predesign_category_id;
			} else {
				$predesign_cPath_new = '';
				$last_predesign_category_query = tep_db_query("select parent_id from product_designer_predesign_categories where categories_id = '" . (int)$predesign_cPath_array[(sizeof($predesign_cPath_array)-1)] . "'");
				$last_predesign_category = tep_db_fetch_array($last_predesign_category_query);

				$current_predesign_category_query = tep_db_query("select parent_id from product_designer_predesign_categories where categories_id = '" . (int)$current_predesign_category_id . "'");
				$current_predesign_category = tep_db_fetch_array($current_predesign_category_query);

				if ($last_predesign_category['parent_id'] == $current_predesign_category['parent_id']) {
					for ($i = 0, $n = sizeof($predesign_cPath_array) - 1; $i < $n; $i++) {
						$predesign_cPath_new .= '_' . $predesign_cPath_array[$i];
					}
				} else {
					for ($i = 0, $n = sizeof($predesign_cPath_array); $i < $n; $i++) {
						$predesign_cPath_new .= '_' . $predesign_cPath_array[$i];
					}
				}

				$predesign_cPath_new .= '_' . $current_predesign_category_id;

				if (substr($predesign_cPath_new, 0, 1) == '_') {
					$predesign_cPath_new = substr($predesign_cPath_new, 1);
				}
			}
		}
		return 'predesign_cPath=' . $predesign_cPath_new;
	}

	function tep_childs_in_predesign_category_count($categories_id) {
		$categories_count = 0;

		$categories_query = tep_db_query("select categories_id from product_designer_predesign_categories where parent_id = '" . (int)$categories_id . "'");
		while ($categories = tep_db_fetch_array($categories_query)) {
			$categories_count++;
			$categories_count += tep_childs_in_predesign_category_count($categories['categories_id']);
		}
		return $categories_count;
	}

	function tep_get_predesign_category_tree_list($parent_id = '0', $checked = false, $include_itself = true) {
		if (tep_childs_in_predesign_category_count($parent_id) > 0){
			if (!is_array($checked)){
				$checked = array();
			}
			$catList = '<ul class="catListingUL">';

			if ($parent_id == '0'){
				$category_query = tep_db_query("select cd.categories_name from product_designer_predesign_categories_description cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.categories_id = '" . (int)$parent_id . "'");
				if (tep_db_num_rows($category_query)){
					$category = tep_db_fetch_array($category_query);

					$catList .= '<li>' . tep_draw_checkbox_field('categories[]', $parent_id, (in_array($parent_id, $checked)), 'id="catCheckbox_' . $parent_id . '"') . '<label for="catCheckbox_' . $parent_id . '">' . $category['categories_name'] . '</label></li>';
				}
			}

			$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from product_designer_predesign_categories c, product_designer_predesign_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
			while ($categories = tep_db_fetch_array($categories_query)) {
				$catList .= '<li>' . tep_draw_checkbox_field('categories[]', $categories['categories_id'], (in_array($categories['categories_id'], $checked)), 'id="catCheckbox_' . $categories['categories_id'] . '"') . '<label for="catCheckbox_' . $categories['categories_id'] . '">' . $categories['categories_name'] . '</label></li>';
				if (tep_childs_in_predesign_category_count($categories['categories_id']) > 0){
					$catList .= '<li class="subCatContainer">' . tep_get_predesign_category_tree_list($categories['categories_id'], $checked, false) . '</li>';
				}
			}
			$catList .= '</ul>';
		}
		return $catList;
	}
?>