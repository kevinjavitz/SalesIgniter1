<?php
/**
 * Content Box Widget Class
 * @package Html
 */
class htmlWidget_contentbox implements htmlWidgetPlugin {
	protected $boxElement, $headerBarElement, $contentElement, $buttonBarElement, $settings;
	
	public function __construct(){
		$this->boxElement = new htmlElement('div');
		$this->headerBarElement = new htmlElement('div');
		$this->contentElement = new htmlElement('div');
		$this->buttonBarElement = new htmlElement('div');
		
		$this->boxElement->addClass('ui-widget ui-contentbox');
		$this->headerBarElement->addClass('ui-widget-header ui-contentbox-header');
		$this->contentElement->addClass('ui-widget-content ui-contentbox-content')->addClass('ui-corner-all');
		$this->buttonBarElement->addClass('ui-contentbox-buttons');
		
		$this->settings = array(
			'form'          => false,
			'headerText'    => '',
			'buttonBar'     => array('location' => 'bottom', 'align' => 'center'),
			'buttons'       => array(),
			'contentBlocks' => array()
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
		
		$this->headerBarElement->html('<span class="ui-contentbox-header-text">' . $box['headerText'] . '</span>');
		
		$this->boxElement->append($this->headerBarElement);
		if ($hasButtons === true && $box['buttonBar']['location'] == 'top'){
			$this->buttonBarElement->css('border-width', '0px 0px 1px 0px');
			$this->boxElement->append($this->buttonBarElement);
		}
		
		if (sizeof($box['contentBlocks']) > 0){
			$html = '';
			foreach($box['contentBlocks'] as $block){
				$html .= $block;
			}
			$this->contentElement->html($html);
		}
		
		if ($hasButtons === true && $box['buttonBar']['location'] == 'bottom'){
			$this->contentElement->append($this->buttonBarElement);
		}
		
		$this->boxElement->append($this->contentElement);
		
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
	
	public function addButton($buttonObj){
		$this->settings['buttons'][] = $buttonObj;
		return $this;
	}
	
	public function addContentBlock($html){
		$this->settings['contentBlocks'][] = $html;
		return $this;
	}
	
	public function setButtonBarLocation($val){
		$this->settings['buttonBar']['location'] = $val;
		return $this;
	}
	
	public function setButtonBarAlign($val){
		$this->settings['buttonBar']['align'] = $val;
		return $this;
	}
	
	public function setForm($settings){
		$this->settings['form'] = $settings;
		return $this;
	}
}
?>