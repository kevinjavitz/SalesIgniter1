<?php
abstract class PurchaseTypeAbstract
{

	public $productInfo;

	public $inventoryCls;

	private $enabled = false;

	private $installed = false;

	private $xmlData = null;

	private $check = null;

	private $configData = array();

	abstract function getPurchaseHtml($key);

	public function init($code, $ProductCls, $forceEnable){
		$this->code = $code;

		$moduleDir = sysConfig::getDirFsCatalog() . 'includes/modules/purchaseTypes/' . $code . '/';
		$this->xmlData = simplexml_load_file(
			$moduleDir . 'data/info.xml',
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		$info = $this->xmlData;

		$Qmodules = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', 'purchase_type')
			->andWhere('m.modules_code = ?', $this->code)
			->orderBy('mc.sort_order')
			->execute();
		if ($Qmodules->count() > 0){
			$this->moduleData = $Qmodules->toArray(true);
			$this->configData = $this->moduleData[0]['ModulesConfiguration'];
			$this->installed = true;

			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/modules/purchaseTypes/' . $code . '/language_defines/global.xml');
			if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/purchaseTypes/' . $code . '/global.xml')){
				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/purchaseTypes/' . $code . '/global.xml');
			}

			$this->title = sysLanguage::get((string) $info->title_key);
			$this->description = sysLanguage::get((string) $info->description_key);
			$this->enabled = (bool) ($this->configData[(string) $info->status_key]['configuration_value'] == 'True' ? true : false);
			$this->sort_order = (int) $this->configData[(string) $info->sort_key]['configuration_value'];

			$this->productInfo = array(
				'id' => $ProductCls->productInfo['products_id'],
				'taxRate' => $ProductCls->productInfo['taxRate']
			);

			$this->inventoryCls = new ProductInventory(
				$ProductCls->productInfo['products_id'],
				$this->code,
				$ProductCls->productInfo['products_inventory_controller']
			);

			if ($forceEnable === true){
				$this->enabled = true;
			}
		}
	}

	public function setProductInfo($key, $val){
		$this->productInfo[$key] = $val;
	}

	public function isEnabled(){
		return $this->enabled;
	}

	public function isInstalled(){
		return ($this->installed === true);
	}

	public function isFromExtension(){
		return false;
	}

	public function getExtensionName(){
		return false;
	}

	public function getConfig(){
		return $this->configData;
	}

	public function getConfigData($key){
		if (array_key_exists($key, $this->configData)){
			return $this->configData[$key]['configuration_value'];
		}
		return null;
	}

	public function getCode(){
		return $this->code;
	}

	public function setTitle($val){
		$this->title = $val;
	}

	public function getTitle(){
		return $this->title;
	}

	public function setDescription($val){
		$this->description = $val;
	}

	public function getDescription(){
		return $this->description;
	}

	public function getStatus(){
		return $this->enabled;
	}

	public function onInstall(&$module, &$moduleConfig){
	}

	public function check(){
		return ($this->isInstalled() === true);
	}

	public function shoppingCartAfterProductName(&$cartProduct) {
		return '';
	}

	public function checkoutAfterProductName(&$cartProduct) {
		return '';
	}

	public function orderAfterEditProductName(&$orderedProduct) {
		return '';
	}

	public function orderAfterProductName(&$orderedProduct) {
		return '';
	}

	public function processAddToOrder(&$pInfo) {
	}

	public function processAddToCart(&$pInfo) {
	}

	public function processUpdateCart(&$pInfo) {
	}

	public function processRemoveFromCart() {
	}

	public function onInsertOrderedProduct($cartProduct, $orderId, &$orderedProduct, &$products_ordered) {
	}

	public function &getInventoryClass() {
		return $this->inventoryCls;
	}

	public function getProductId() {
		return $this->productInfo['id'];
	}

	public function getPrice() {
		if ($this->enabled === false || is_null($this->inventoryCls)) {
			return null;
		}
		if (isset($this->productInfo['special_price'])){
			return $this->productInfo['special_price'];
		}
		return $this->productInfo['price'];
	}

	public function displayPrice() {
		global $currencies, $appExtension;
		if ($this->enabled === false || is_null($this->inventoryCls)) {
			return null;
		}
		if (isset($this->productInfo['special_price'])){
			$extSpecials = $appExtension->getExtension('specials');
			$display = $currencies->display_price($this->productInfo['price'], $this->productInfo['taxRate']);
			$extSpecials->ProductNewPriceBeforeDisplay($this->productInfo['special_price'], $display);
			return $display;
		}
		else {
			return $currencies->display_price($this->productInfo['price'], $this->productInfo['taxRate']);
		}
	}

	public function canUseSpecial() {
		if ($this->enabled === false || is_null($this->inventoryCls)) {
			return false;
		}
		return true;
	}

	public function updateStock($orderId, $orderProductId, &$cartProduct) {
		if ($this->enabled === false || is_null($this->inventoryCls)) {
			return true;
		}
		return $this->inventoryCls->updateStock($orderId, $orderProductId, &$cartProduct);
	}

	public function getTrackMethod() {
		if ($this->enabled === false || is_null($this->inventoryCls)) {
			return null;
		}
		return $this->inventoryCls->getTrackMethod();
	}

	public function getCurrentStock() {
		if ($this->enabled === false || is_null($this->inventoryCls)) {
			return null;
		}
		return $this->inventoryCls->getCurrentStock();
	}

	public function hasInventory() {
		if ($this->enabled === false) {
			return false;
		}
		if (is_null($this->inventoryCls)) {
			return true;
		}
		return ($this->inventoryCls->hasInventory());
	}

	public function getInventoryItems() {
		if ($this->enabled === false) {
			return false;
		}
		if (is_null($this->inventoryCls)) {
			return true;
		}
		return $this->inventoryCls->getInventoryItems();
	}
}

?>