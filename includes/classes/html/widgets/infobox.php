<?php
/**
 * Infobox Widget Class
 * @package Html
 */
class htmlWidget_infobox implements htmlWidgetPlugin {
	protected $boxElement, $headerBarElement, $contentElement, $buttonBarElement, $settings;
	
	public function __construct(){
		$this->boxElement = new htmlElement('div');
		$this->headerBarElement = new htmlElement('div');
		$this->contentElement = new htmlElement('div');
		$this->buttonBarElement = new htmlElement('div');
		
		$this->boxElement
		->addClass('ui-dialog')
		->addClass('ui-widget')
		->addClass('ui-widget-content')
		->addClass('ui-corner-all')
		->css(array(
			'position'     => 'relative',
			'width'        => 'auto'
		));
		
		$this->headerBarElement
		->addClass('ui-dialog-titlebar')
		->addClass('ui-widget-header')
		->addClass('ui-corner-all')
		->addClass('ui-helper-clearfix');
		
		$this->contentElement
		->addClass('ui-dialog-content')
		->addClass('ui-widget-content');
		
		$this->buttonBarElement
		->addClass('ui-dialog-buttonpane')
		->addClass('ui-widget-content')
		->addClass('ui-helper-clearfix');
		
		$this->settings = array(
		'form'        => false,
		'headerText'  => '',
		'buttonBar'   => array('location' => 'bottom', 'align' => 'center'),
		'buttons'     => array(),
		'contentRows' => array()
		);
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->boxElement, $function), $args);
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
		$this->boxElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->boxElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		$box = $this->settings;
		$hasButtons = false;
		if (sizeof($box['buttons']) > 0){
			$hasButtons = true;
			foreach($box['buttons'] as $button){
				$this->buttonBarElement->append($button);
			}
			$this->buttonBarElement->css('text-align', $box['buttonBar']['align']);
		}
		
		$spanEl = new htmlElement('span');
		$spanEl->addClass('ui-dialog-title')->html($box['headerText']);
		
		$this->headerBarElement->append($spanEl);
		
		$this->boxElement->append($this->headerBarElement);
		if ($hasButtons === true && $box['buttonBar']['location'] == 'top'){
			$this->buttonBarElement->css('border-width', '0px 0px 1px 0px');
			$this->boxElement->append($this->buttonBarElement);
		}
		
		foreach($box['contentRows'] as $row){
			$contentEl = new htmlElement('div');
			$contentEl->addClass('main')->css('padding', '3px');
			if (is_object($row)){
				$contentEl->append($row);
			}elseif (is_array($row)){
				$contentEl->html($row['text']);
			}else{
				$contentEl->html($row);
			}
			
			$this->contentElement->append($contentEl);
		}
		$this->boxElement->append($this->contentElement);
		
		if ($hasButtons === true && $box['buttonBar']['location'] == 'bottom'){
			$this->boxElement->append($this->buttonBarElement);
		}
		
		if ($box['form'] !== false){
			$finalBox = new htmlElement('form');
			$finalBox
			->attr('name', $box['form']['name'])
			->attr('action', $box['form']['action'])
			->attr('method', (isset($box['form']['method']) ? $box['form']['method'] : 'post'));
			
			if (isset($box['form']['attr'])){
				foreach($box['form']['attr'] as $k => $v){
					$finalBox->attr($k, $v);
				}
			}
			
			$finalBox->append($this->boxElement);
		}else{
			$finalBox = $this->boxElement;
		}
		return $finalBox->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */

	public function setHeader($val){
		$this->settings['headerText'] = $val;
		return $this;
	}
	
	public function addButton($buttonObj, $newLine = false){
		if ($newLine === true){
			$this->settings['buttons'][] = htmlBase::newElement('br');
		}
		$this->settings['buttons'][] = $buttonObj;
		return $this;
	}
	
	public function addContentRow($settings){
		$this->settings['contentRows'][] = $settings;
		return $this;
	}
	
	public function setButtonBarLocation($val){
		$this->settings['buttonBar']['location'] = $val;
		return $this;
	}
	
	public function setForm($settings){
		$this->settings['form'] = $settings;
		return $this;
	}
}
?>