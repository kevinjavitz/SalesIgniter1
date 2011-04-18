<?php
class Extension_orderCreator extends ExtensionBase {

	public function __construct(){
		parent::__construct('orderCreator');
	}

	public function preSessionInit(){
		$this->removeSession = true;
		if (isset($_GET['appExt']) && $_GET['appExt'] == 'orderCreator'){
			if (!isset($_GET['action']) && !isset($_POST['action']) && !isset($_GET['error'])){
				$this->removeSession = true;
			}else{
				$this->removeSession = false;
			}
			
			/* 
			 * Require any core classes
			 */
			require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
			
			/*
			 * Require any extension specific classes
			 */
			require(dirname(__FILE__) . '/admin/classes/Order/Base.php');
		}
	}
	
	public function postSessionInit(){
		if (Session::exists('OrderCreator')){
			if (isset($this->removeSession) && $this->removeSession === true){
				Session::remove('OrderCreator');
			}
		}
	}
	
	public function init(){
		EventManager::attachEvents(array(
			'OrdersGridButtonsBeforeAdd'
		), null, $this);
	}
	
	public function OrdersGridButtonsBeforeAdd(&$gridButtons){
		$gridButtons[] = htmlBase::newElement('button')
		->setText('New Order')
		->addClass('createButton')
		->setHref(itw_app_link('appExt=orderCreator', 'default', 'new'));
		
		$gridButtons[] = htmlBase::newElement('button')
		->setText('Edit Order')
		->addClass('editButton')
		->disable();
	}
}
?>