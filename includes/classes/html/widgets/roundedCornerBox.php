<?php
/**
 * Rounded Corner Box Widget Class
 * @package Html
 */
function getBrowserInfo() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']); 

        // Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari. 
        if (preg_match('/opera/', $userAgent)) { 
            $name = 'opera'; 
        } 
        elseif (preg_match('/webkit/', $userAgent)) { 
            $name = 'safari'; 
        } 
        elseif (preg_match('/msie/', $userAgent)) { 
            $name = 'msie'; 
        } 
        elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) { 
            $name = 'mozilla'; 
        } 
        else { 
            $name = 'unrecognized'; 
        } 

        // What version? 
        if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) { 
            $version = $matches[1]; 
        } 
        else { 
            $version = 'unknown'; 
        } 

        // Running on what platform? 
        if (preg_match('/linux/', $userAgent)) { 
            $platform = 'linux'; 
        } 
        elseif (preg_match('/macintosh|mac os x/', $userAgent)) { 
            $platform = 'mac'; 
        } 
        elseif (preg_match('/windows|win32/', $userAgent)) { 
            $platform = 'windows'; 
        } 
        else { 
            $platform = 'unrecognized'; 
        } 

        return array( 
            'name'      => $name, 
            'version'   => $version, 
            'platform'  => $platform, 
            'userAgent' => $userAgent 
        ); 
}

class htmlWidget_roundedCornerBox implements htmlWidgetPlugin {
	protected $element, $settings;
	
	public function __construct(){
		$browser = getBrowserInfo();
		//if ($browser['name'] == 'msie'){
		//	$this->element = htmlBase::newElement('table')->setCellPadding(0)->setCellSpacing(0)->css('width', '100%');
		//}else{
			$this->element = htmlBase::newElement('div');
		//}
		$this->toPrepend = array();
		$this->toAppend = array();
		$this->settings = array(
			'html'    => '',
			'rounded' => null,
			'size'    => null
		);
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->element, $function), $args);
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
		$this->element->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->element->attr('name', $val);
		return $this;
	}
	
	public function setValue($val){
		$this->settings['value'] = $val;
		return $this;
	}
	
	public function draw(){
			$this->element->addClass('ui-widget ui-widget-content');
		if ($this->element->isType('table')){
			$trOne = new htmlElement('tr');
			$trTwo = new htmlElement('tr');
			$trThree = new htmlElement('tr');
			
			$tdOne = new htmlElement('td');
			$tdTwo = new htmlElement('td');
			$tdThree = new htmlElement('td');
			$tdFour = new htmlElement('td');
			$tdFive = new htmlElement('td');
			$tdSix = new htmlElement('td');
			$tdSeven = new htmlElement('td');
			$tdEight = new htmlElement('td');
			$tdNine = new htmlElement('td');
			
			$r = $this->settings['rounded'];
			if ($r == 'all' || $r == 'top' || $r == 'tl'){
				$className = 'ui-table-corners-tl';
				if ($this->settings['size'] == 'big'){
					$className .= '-big';
				}
				$tdOne->addClass($className);
			}
			
			if ($r == 'all' || $r == 'top' || $r == 'tr'){
				$className = 'ui-table-corners-tr';
				if ($this->settings['size'] == 'big'){
					$className .= '-big';
				}
				$tdThree->addClass($className);
			}
			
			if ($r == 'all' || $r == 'bottom' || $r == 'bl'){
				$className = 'ui-table-corners-bl';
				if ($this->settings['size'] == 'big'){
					$className .= '-big';
				}
				$tdSeven->addClass($className);
			}
			
			if ($r == 'all' || $r == 'bottom' || $r == 'br'){
				$className = 'ui-table-corners-br';
				if ($this->settings['size'] == 'big'){
					$className .= '-big';
				}
				$tdNine->addClass($className);
			}
			
			if (sizeof($this->toPrepend) > 0){
				foreach($this->toPrepend as $elObj){
					$tdFive->prepend($elObj);
				}
			}
			
			if ($this->settings['html'] != ''){
				$tdFive->html($this->settings['html']);
			}
			
			if (sizeof($this->toAppend) > 0){
				foreach($this->toAppend as $elObj){
					$tdFive->append($elObj);
				}
			}
			
			$trOne->append($tdOne)->append($tdTwo)->append($tdThree);
			$trTwo->append($tdFour)->append($tdFive)->append($tdSix);
			$trThree->append($tdSeven)->append($tdEight)->append($tdNine);
			
			$this->element->append($trOne)->append($trTwo)->append($trThree);
		}else{
			
			$className = 'ui-corner-' . $this->settings['rounded'];
			if ($this->settings['size'] == 'big'){
				$className .= '-big';
			}
			$this->element->addClass($className);
			
			if (sizeof($this->toPrepend) > 0){
				foreach($this->toPrepend as $elObj){
					$this->element->prepend($elObj);
				}
			}
			
			if ($this->settings['html'] != ''){
				$this->element->html($this->settings['html']);
			}
			
			if (sizeof($this->toAppend) > 0){
				foreach($this->toAppend as $elObj){
					$this->element->append($elObj);
				}
			}
		}
		return $this->element->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function prepend($el){
		$this->toPrepend[] = $el;
		return $this;
	}
	
	public function append($el){
		$this->toAppend[] = $el;
		return $this;
	}
	
	public function html($val){
		$this->settings['html'] = $val;
		return $this;
	}
	
	public function setRounded($val){
		$this->settings['rounded'] = $val;
		return $this;
	}
	
	public function setSize($val){
		$this->settings['size'] = $val;
		return $this;
	}
}
?>