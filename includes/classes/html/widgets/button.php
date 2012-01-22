<?php
/**
 * Button Widget Class
 * @package Html
 */
class htmlWidget_button implements htmlWidgetPlugin {
	protected $buttonElement, $textElement, $iconElement;
	
	public function __construct(){
		$this->buttonElement = new htmlElement('button');
		//$this->textElement = new htmlElement('span');

		$this->buttonElement
		//->addClass('ui-button')
		//->addClass('ui-widget')
		//->addClass('ui-state-default')
		//->addClass('ui-corner-all')
		->attr('type', 'button');
		
		$this->settings = array(
		'text'    => 'Submit Query',
		'icon'    => false,
		'href'    => false,
		'tooltip' => false
		);
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->buttonElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}
	
	/* Required Classes From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain(){
		return $this;
	}
	
	public function setId($val){
		$this->buttonElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->buttonElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		$button = $this->settings;
		//$this->textElement->html($button['text']);
		$this->buttonElement->html($button['text']);
		if ($button['icon'] !== false){
			$this->buttonElement->attr('icon', $button['icon']);
		}
		
		//$this->buttonElement->append($this->textElement);
		
		if ($button['tooltip'] !== false){
			$this->buttonElement->attr('tooltip', $button['tooltip']);
		}
		
		if ($button['href'] !== false && $this->buttonElement->hasClass('ui-state-disabled') === false){
			$this->buttonElement
			->attr('href', $button['href'])
			->css('text-decoration', 'none');
		}
		
		return $this->buttonElement->draw();
	}
	/* Required Classes From Interface: htmlElementPlugin --END-- */
	
	public function disable(){
		$this->buttonElement->addClass('ui-state-disabled');
		return $this;
	}
	
	public function setType($val){
		$this->buttonElement->attr('type', $val);
		return $this;
	}
	
	public function setText($val){
		$this->settings['text'] = $val;
		return $this;
	}
	
	public function setIcon($type){
		$iconObj = htmlBase::newElement('icon');

		$this->settings['icon'] = $iconObj->getIconClassFromType($type);
		return $this;
	}
	
	public function setHref($val, $encode = false, $target = null){
		if ($encode === true){
			$this->settings['href'] = htmlspecialchars($val);
		}else{
			$this->settings['href'] = $val;
		}
		$this->buttonElement->changeElement('a');
		if (!is_null($target)){
			$this->buttonElement->attr('target', $target);
		}
		return $this;
	}
	
	public function setTooltip($val){
		$this->settings['tooltip'] = $val;
		return $this;
	}
	
	public function usePreset($preset){
		switch($preset){
			case 'back':
				$this->setIcon('circleTriangleWest')
				->setText(sysLanguage::get('TEXT_BUTTON_BACK'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_BACK'));
				break;
			case 'new':
				$this->setIcon('circlePlus')
				->setText(sysLanguage::get('TEXT_BUTTON_NEW'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_NEW'));
				break;
			case 'edit':
				$this->setIcon('wrench')
				->setText(sysLanguage::get('TEXT_BUTTON_EDIT'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_EDIT'));
				break;
			case 'delete':
				$this->setIcon('closeThick')
				->setText(sysLanguage::get('TEXT_BUTTON_DELETE'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_DELETE'));
				break;
			case 'cancel':
				$this->setIcon('cancel')
				->setText(sysLanguage::get('TEXT_BUTTON_CANCEL'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_CANCEL'));
				break;
			case 'next':
				$this->setIcon('next')
				->setText(sysLanguage::get('TEXT_BUTTON_NEXT'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_NEXT'));
				break;
			case 'save':
				$this->setIcon('save')
				->setText(sysLanguage::get('TEXT_BUTTON_SAVE'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_SAVE'));
				break;
			case 'install':
				$this->setIcon('plusThick')
				->setText(sysLanguage::get('TEXT_BUTTON_INSTALL'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_INSTALL'));
				break;
			case 'uninstall':
				$this->setIcon('closeThick')
				->setText(sysLanguage::get('TEXT_BUTTON_UNINSTALL'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_UNINSTALL'));
				break;
			case 'continue':
				$this->setIcon('circleTriangleEast')
				->setText(sysLanguage::get('TEXT_BUTTON_CONTINUE'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_CONTINUE'));
				break;
			case 'load':
				$this->setIcon('disc')
				->setText(sysLanguage::get('TEXT_BUTTON_LOAD'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_LOAD'));
				break;
			case 'trash':
				$this->setIcon('trash')
				->setText(sysLanguage::get('TEXT_BUTTON_TRASH'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_TRASH'));
				break;
			case 'print':
				$this->setIcon('print')
				->setText(sysLanguage::get('TEXT_BUTTON_PRINT'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_PRINT'));
				break;
			case 'help':
				$this->setIcon('help')
				->setText(sysLanguage::get('TEXT_BUTTON_HELP'))
				->setTooltip(sysLanguage::get('TEXT_BUTTON_HELP'));
				break;
		}
		return $this;
	}
}
?>