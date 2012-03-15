<?php
/**
 * Html Tabs Widget Class
 * @package Html
 */
class htmlWidget_tabs implements htmlWidgetPlugin {
	protected $tabElement, $tabPages, $tabHeaders, $selectedTab;
	
	public function __construct(){
		$this->tabHeaderElement = htmlBase::newElement('list');
		$this->tabElement = new htmlElement('div');
		$this->tabPages = array();
		$this->tabHeaders = array();
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->tabElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Functions From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain(){
		return $this;
	}
	
	public function setId($val){
		$this->tabElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->tabElement->attr('name', $val);
		return $this;
	}
	
	public function hide(){
		$this->tabElement->css('display', 'none');
		return $this;
	}
	
	public function addClass($className){
		$this->tabElement->addClass($className);
		return $this;
	}
	
	public function draw(){
		foreach($this->tabHeaders as $id => $obj){
			if (isset($this->tabPages[$id])){
				if (!empty($this->selectedTab) && $this->selectedTab == $id){
					$obj->addClass('ui-tabs-selected');
				}
				$this->tabHeaderElement->addItemObj($obj);
			}
		}
		
		$this->tabElement->append($this->tabHeaderElement);
		
		foreach($this->tabPages as $id => $obj){
			if (isset($this->tabHeaders[$id])){
				$this->tabElement->append($obj);
			}
		}
		return $this->tabElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function addTabHeader($id, $settings){
		$aObj = htmlBase::newElement('a')
		->setHref('#' . $id)
		->html($settings['text']);
		
		$liObj = new htmlElement('li');
		$liObj->append($aObj);
		if(isset($settings['addCls'])){
			$liObj->addClass($settings['addCls']);
		}
		$this->tabHeaders[$id] = $liObj;
		return $this;
	}
	
	public function addTabPage($id, $settings, $after = null){
		$this->tabPages[$id] = new htmlElement('div');
		$this->tabPages[$id]->attr('id', $id);
		
		if (is_object($settings['text'])){
			$this->tabPages[$id]->append($settings['text']);
		}else{
			$this->tabPages[$id]->html($settings['text']);
		}
		return $this;
	}
	
	public function &getTabPages($id = false){
		if ($id === false){
			return $this->tabPages;
		}else{
			return $this->tabPages[$id];
		}
		return false;
	}
	
	public function setSelected($id){
		$this->selectedTab = $id;
		return $this;
	}
}
?>