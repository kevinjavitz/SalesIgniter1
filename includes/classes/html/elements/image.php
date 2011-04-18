<?php
/**
 * Img Element Class
 * @package Html
 */
class htmlElement_image implements htmlElementPlugin {
	protected $imgElement, $useThumbnail;
	
	public function __construct(){
		$this->imgElement = new htmlElement('img');
		$this->useThumbnail = false;
		$this->useBestFit = true;
	}
	
	public function __call($function, $args){
		$return = call_user_func_array(array($this->imgElement, $function), $args);
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
		$this->imgElement->attr('id', $val);
		return $this;
	}
	
	public function setName($val){
		$this->imgElement->attr('name', $val);
		return $this;
	}
	
	public function draw(){
		$src = $this->imgElement->attr('src');
		if ($this->useThumbnail === true && !stristr($src, 'http')){
			$width = (int)$this->imgElement->css('width');
			$height = (int)$this->imgElement->css('height');
			$this->imgElement->removeCss('width')->removeCss('height');
			
			$this->setSource('imagick_thumb.php?path=rel&width=' . $width . '&height=' . $height . '&imgSrc=' . $src.'&bestFit='. $this->useBestFit);
		}
		return $this->imgElement->draw();
	}
	/* Required Functions From Interface: htmlElementPlugin --END-- */
	
	public function setSource($val){
		$this->imgElement->attr('src', $val);
		return $this;
	}
	
	public function setTitle($val){
		$this->imgElement->attr('title', $val)->attr('alt', $val);
		return $this;
	}
	
	public function setWidth($val){
		$this->imgElement->css('width', $val . 'px');
		return $this;
	}
	
	public function setHeight($val){
		$this->imgElement->css('height', $val . 'px');
		return $this;
	}
	
	public function thumbnailImage($val){
		$this->useThumbnail = $val;
		return $this;
	}

	public function bestFit($val){
		$this->useBestFit = $val;
		return $this;
	}
}
?>