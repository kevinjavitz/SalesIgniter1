<?php
/**
 * Html Element Base Class
 * @package Html
 */
class htmlElement
{

	/**
	 * @var array Html Elements appended to the current element
	 */
	private $appendElements = array();

	/**
	 * @var array Html Elements prepended to the currenc element
	 */
	private $prependElements = array();

	/**
	 * @var string Element Type
	 */
	private $element = '';

	/**
	 * @var string Html content to show in the element
	 */
	private $htmlContent = null;

	/**
	 * @var string Text content to show in the element
	 */
	private $textContent = null;

	/**
	 * @var array Html attributes for the element
	 */
	private $attributes = array();

	/**
	 * @var array Css classes for the element
	 */
	private $classes = array();

	/**
	 * @var int Random number used for element id/name attribute
	 */
	public static $randomNumber = 0;

	/**
	 * Create the element
	 * @param string $elementType
	 */
	public function __construct($elementType) {
		$allowedElements = array('form', 'button', 'input', 'select', 'option', 'textarea', 'a', 'b', 'br', 'hr', 'div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'ul', 'li', 'img', 'label', 'table', 'tr', 'th', 'td', 'thead', 'tbody', 'tfoot', 'fieldset', 'legend');
		if (!in_array($elementType, $allowedElements)){
			die('Element Not Supported: ' . $elementType);
		}
		$this->appendElements = array();
		$this->prependElements = array();
		$this->element = $elementType;
		$this->htmlContent = '';
		$this->textContent = '';
		$this->attributes = '';
		$this->classes = array();
	}

	/**
	 * Begin the chain to allow method chaining
	 * @return htmlElement
	 */
	public function startChain() {
		return $this;
	}

	/**
	 * Check if the element is a specified type
	 * @param string $check
	 * @return bool
	 */
	public function isType($check) {
		return ($this->element == $check);
	}

	/**
	 * Create a random number for nameing and id assignment
	 * @return int
	 */
	private function generateRandomNumber() {
		/*
		$randomNumber = round(rand(rand(1, 500), rand(505, 9000)) * rand(1, 100)/rand(1, 15), 0);
		while(in_array($randomNumber, self::$randomNumbers) === true){
			$randomNumber = round(rand(rand(1, 500), rand(505, 9000)) * rand(1, 100)/rand(1, 15), 0);
		}
		self::$randomNumbers[] = $randomNumber;
		return $randomNumber;
		*/
		return self::$randomNumber++;
	}

	/**
	 * Set the id attribute for the element to the random number
	 * @return htmlElement
	 */
	public function setIdRandom() {
		$this->attr('id', 'htmlElement_randomId_' . $this->generateRandomNumber());
		return $this;
	}

	/**
	 * Set the name attribute for the element to the random number
	 * @return htmlElement
	 */
	public function setNameRandom() {
		$this->attr('name', 'htmlElement_randomName_' . $this->generateRandomNumber());
		return $this;
	}

	/**
	 * Add a css class to the element, can be multiple classes in one string
	 * @param string $cls
	 * @return htmlElement
	 */
	public function addClass($cls) {
		$this->classes[$cls] = $cls;
		return $this;
	}

	/**
	 * Remove a css class from the element, does not support multiple classes in one string
	 * @param string $cls
	 * @return htmlElement
	 */
	public function removeClass($cls) {
		if ($this->hasClass($cls)){
			unset($this->classes[$cls]);
		}
		return $this;
	}

	/**
	 * Check if the element has a specific class
	 * @param string $cls
	 * @return bool
	 */
	public function hasClass($cls) {
		return array_key_exists($cls, $this->classes);
	}

	/**
	 * Add a css style to the element, can be an array of style => value
	 * Also can retrieve a css style setting by not including a value
	 * @param string|array $key
	 * @param null|string $val
	 * @return htmlElement|string
	 */
	public function css($key, $val = false) {
		if (is_array($key)){
			foreach($key as $k => $v){
				if (is_object($v) || substr($v, 0, 1) == '{' || substr($v, 0, 1) == '[') {
					$Style = new StyleBuilder();
					$Style->addRule($k, $v);
					foreach($Style->getArray() as $k => $v){
						$this->styles[$k] = $v;
					}
				}
				else {
					$this->styles[$k] = $v;
				}
			}
		}
		elseif ($val !== false) {
			if (is_object($val) || substr($val, 0, 1) == '{' || substr($val, 0, 1) == '[') {
				$Style = new StyleBuilder();
				$Style->addRule($key, $val);
				foreach($Style->getArray() as $k => $v){
					$this->styles[$k] = $v;
				}
			}
			else {
				$this->styles[$key] = $val;
			}
		}
		else {
			if (isset($this->styles[$key])){
				return $this->styles[$key];
			}
			else {
				return '';
			}
		}
		return $this;
	}

	/**
	 * Check if the element has the specified css style
	 * @param string $name
	 * @return bool
	 */
	public function hasCss($name) {
		return (isset($this->styles[$name]));
	}

	/**
	 * Remove a css attribute from the element
	 * @param string $name
	 * @return htmlElement
	 */
	public function removeCss($name) {
		if ($this->hasCss($name)){
			unset($this->styles[$name]);
		}
		return $this;
	}

	/**
	 * Format the id attribute into a compliant string
	 * @param string $val
	 * @return mixed
	 */
	private function formatIdAttr($val) {
		return str_replace(array('[', ']'), '_', str_replace('][', '_', $val));
	}

	/**
	 * Add an html attribute to the element
	 * Also can get the value of an attribute if val is null
	 * @param string|array $name
	 * @param null|string $val
	 * @return htmlElement|string
	 */
	public function attr($name, $val = false) {
		if (is_array($name)){
			foreach($name as $k => $v){
				if ($k == 'id'){
					$v = $this->formatIdAttr($v);
				}
				$this->attributes[$k] = $v;
			}
		}
		elseif ($val !== false) {
			if ($name == 'id'){
				$val = $this->formatIdAttr($val);
			}
			$this->attributes[$name] = $val;
		}
		else {
			if (isset($this->attributes[$name])){
				return $this->attributes[$name];
			}
			else {
				return '';
			}
		}
		return $this;
	}

	/**
	 * Check if the element has a specific html attribute
	 * @param string $name
	 * @return bool
	 */
	public function hasAttr($name) {
		return (isset($this->attributes[$name]));
	}

	/**
	 * Remove an html attribute from the element
	 * @param string $name
	 * @return htmlElement
	 */
	public function removeAttr($name) {
		if ($this->hasAttr($name)){
			unset($this->attributes[$name]);
		}
		return $this;
	}

	/**
	 * Append an htmlElement to the element
	 * @param htmlElement $element
	 * @return htmlElement
	 */
	public function append($element) {
		if (!is_object($element)){
			trigger_error('Appended element must be an object.', E_USER_ERROR);
		}
		$this->appendElements[] = $element;
		return $this;
	}

	/**
	 * Get all appended elements
	 * @return array
	 */
	public function &getAppendedElements() {
		return $this->appendElements;
	}

	/**
	 * Prepend an htmlElement to the element
	 * @param htmlElement $element
	 * @return htmlElement
	 */
	public function prepend($element) {
		if (!is_object($element)){
			trigger_error('Appended element must be an object.', E_USER_ERROR);
		}
		$this->prependElements[] = $element;
		return $this;
	}

	/**
	 * Add html content to the element
	 * @param string|null $html
	 * @return htmlElement|string
	 */
	public function html($html = false) {
		if ($html === false){
			return $this->htmlContent;
		}
		else {
			$this->htmlContent = $html;
		}
		return $this;
	}

	/**
	 * Add text content to the element
	 * @param string|null $html
	 * @return htmlElement|string
	 */
	public function text($text = false) {
		if ($text === false){
			return $this->textContent;
		}
		else {
			$this->textContent = strip_tags($text);
		}
		return $this;
	}

	/**
	 * Set the value for the element, mainly for input/textarea/button
	 * @param string|null $val
	 * @return htmlElement|string
	 */
	public function val($val = false) {
		if ($val !== false){
			if ($this->element == 'textarea'){
				$this->html($val);
			}
			elseif ($this->element == 'select') {
				//$this->selectOptionByValue($val);
			}
			else {
				$this->attr('value', $val);
			}
		}
		else {
			if ($this->element == 'textarea'){
				return $this->html();
			}
			elseif ($this->element == 'select') {
				//return $this->selectedOption();
			}
			else {
				return $this->attr('value');
			}
		}
		return $this;
	}

	/**
	 * Change the element type, do not use this unless you know what you're doing
	 * @param string $to
	 * @return htmlElement
	 */
	public function changeElement($to) {
		$this->element = $to;
		return $this;
	}

	/**
	 * Output the element and all its appended/prepended elements
	 * @return string
	 */
	public function draw() {
		$html = '<' . $this->element;
		if (!empty($this->classes)){
			$html .= ' class="' . implode(' ', $this->classes) . '"';
		}
		if (!empty($this->attributes)){
			$arr = array();
			foreach($this->attributes as $name => $val){
				if ($name == 'onclick' || $name == 'onchange'){
					$arr[] = $name . '="' . $val . '"';
				}
				else {
					$arr[] = $name . '="' . str_replace('"', '\"', stripslashes($val)) . '"';
				}
			}
			$html .= ' ' . implode(' ', $arr);
		}
		if (!empty($this->styles)){
			$arr = array();
			foreach($this->styles as $name => $val){
				if (is_numeric($name)){
					$arr[] = $val;
				}
				else {
					$arr[] = $name . ':' . $val;
				}
			}
			$html .= ' style="' . implode(';', $arr) . '"';
		}
		if ($this->element == 'input' || $this->element == 'br'){
			$html .= ' />';
		}
		else {
			$html .= '>';
			if (sizeof($this->prependElements) > 0){
				foreach($this->prependElements as $elObj){
					if (is_object($elObj)){
						$html .= $elObj->draw();
					}
				}
			}
			if (is_object($this->htmlContent)){
				$html .= stripslashes($this->htmlContent->draw());
			}
			elseif (strlen($this->htmlContent) > 0) {
				$html .= stripslashes($this->htmlContent);
			}
			if (strlen($this->textContent) > 0){
				$html .= stripslashes($this->textContent);
			}
			if (sizeof($this->appendElements) > 0){
				foreach($this->appendElements as $elObj){
					if (is_object($elObj)){
						$html .= $elObj->draw();
					}
				}
			}
			$html .= '</' . $this->element . '>';
		}
		if ($this->hasClass('required')){
			$requiredIcon = htmlBase::newElement('icon')
				->setType('required')
				->css(array('display' => 'inline-block'))
				->setTooltip('Input Required')
				->addClass('ui-icon-required');
			$html .= $requiredIcon->draw();
		}
		return $html;
	}

	/**
	 * Set an onclick action for the element
	 * @param string $val
	 * @return void
	 */
	public function click($val) {
		$this->attr('onclick', $val);
	}

	/**
	 * Hide the element
	 * @return htmlElement
	 */
	public function hide() {
		$this->css('display', 'none');
		return $this;
	}

	/**
	 * Disable or Enable the element
	 * @param bool $val
	 * @return htmlElement
	 */
	public function disable($val = true) {
		if ($val === true){
			$this->attr('disabled', 'disabled')->addClass('ui-state-disabled');
		}
		else {
			$this->removeAttr('disabled')->removeClass('ui-state-disabled');
		}
		return $this;
	}

	/**
	 * Set the element to required or not
	 * @param bool $val
	 * @return htmlElement
	 */
	public function setRequired($val) {
		if ($val === true){
			$this->addClass('required');
		}
		else {
			$this->removeClass('required');
		}
		return $this;
	}
}

/**
 * Interface used for htmlElement plugins
 */
interface htmlElementPlugin
{

	/**
	 * Start the chain, required for method chaining
	 * @abstract
	 * @return void
	 */
	public function startChain();

	/**
	 * Set the id for the element
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	public function setId($val);

	/**
	 * Set the name for the element
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	public function setName($val);

	/**
	 * Output the element
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	public function draw();
}

/**
 * Interface used for htmlWidget plugins
 */
interface htmlWidgetPlugin
{

	/**
	 * Start the chain, required for method chaining
	 * @abstract
	 * @return void
	 */
	public function startChain();

	/**
	 * Set the id for the element
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	public function setId($val);

	/**
	 * Set the name for the element
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	public function setName($val);

	/**
	 * Output the element
	 * @abstract
	 * @param string $val
	 * @return void
	 */
	public function draw();
}

class StyleBuilder {
	private $definitions = array();
	private $selector = '';

	public function __construct(){

	}

	public function setSelector($name){
		$this->selector = $name;
	}

	public function isIE(){
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$ub = false;
		if (preg_match('/MSIE/i',$u_agent)){
			$ub = true;
		}
		return $ub;
	}

	public function isMoz(){
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$ub = false;
		if (preg_match('/Mozilla/i',$u_agent) && !preg_match('/AppleWebKit/i',$u_agent)){
			$ub = true;
		}
		return $ub;
	}

	public function isWebkit(){
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$ub = false;
		if (preg_match('/AppleWebKit/i',$u_agent)){
			$ub = true;
		}
		return $ub;
	}

	public function addRule($ruleName, $ruleVal){
		if (is_array($ruleVal)){
			$ruleVal = json_decode(json_encode($ruleVal));
			$this->parseJsonRule($ruleName, $ruleVal);
		}else
			if (is_object($ruleVal)){
				$this->parseJsonRule($ruleName, $ruleVal);
			}else
				if (substr($ruleVal, 0, 1) == '{' || substr($ruleVal, 0, 1) == '['){
					$ruleVal = json_decode($ruleVal);
					$this->parseJsonRule($ruleName, $ruleVal);
				}else{
					$this->definitions[$ruleName] = $ruleVal;
				}
	}

	public function parseJsonRule($key, $val){
		switch($key){
			case 'font':
				if (isset($val->family)) {
					$this->addRule('font-family', $val->family);
				}

				if (isset($val->style)) {
					$this->addRule('font-style', $val->style);
				}

				if (isset($val->variant)) {
					$this->addRule('font-variant', $val->variant);
				}

				if (isset($val->weight)) {
					$this->addRule('font-weight', $val->weight);
				}

				if (isset($val->size)){
					$fontSize = $val->size . $val->size_unit;
					if ($val->size_unit == 'inherit'){
						$fontSize = $val->size_unit;
					}
					$this->addRule('font-size', $fontSize);
				}
				break;
			case 'text':
				$textIndent = $val->indent . $val->indent_unit;
				if ($val->indent_unit == 'inherit'){
					$textIndent = $val->indent_unit;
				}

				$lineHeight = $val->line_height . $val->line_height_unit;
				if ($val->line_height_unit == 'inherit'){
					$lineHeight = $val->line_height_unit;
				}

				$letterSpacing = $val->letter_spacing . $val->letter_spacing_unit;
				if ($val->letter_spacing_unit == 'normal' || $val->letter_spacing_unit == 'inherit'){
					$letterSpacing = $val->letter_spacing_unit;
				}

				$wordSpacing = $val->word_spacing . $val->word_spacing_unit;
				if ($val->word_spacing_unit == 'normal' || $val->word_spacing_unit == 'inherit'){
					$wordSpacing = $val->word_spacing_unit;
				}

				$this->addRule('color', $val->color);
				$this->addRule('text-align', $val->align);
				$this->addRule('text-decoration', $val->decoration);
				$this->addRule('text-transform', $val->transform);
				$this->addRule('vertical-align', $val->vertical_align);
				$this->addRule('white-space', $val->white_space);
				$this->addRule('text-indent', $textIndent);
				$this->addRule('line-height', $lineHeight);
				$this->addRule('letter-spacing', $letterSpacing);
				$this->addRule('word-spacing', $wordSpacing);
				break;
			case 'border':
				$keys = array('top', 'right', 'bottom', 'left');
				foreach($keys as $k){
					$this->addRule('border-' . $k . '-width', $val->$k->width . $val->$k->width_unit);
					$this->addRule('border-' . $k . '-color', $val->$k->color);
					$this->addRule('border-' . $k . '-style', $val->$k->style);
				}
				break;
			case 'padding':
				$keys = array('top', 'right', 'bottom', 'left');
				foreach($keys as $k){
					$paddingVal = $val->$k . $val->{$k . '_unit'};
					if ($val->{$k . '_unit'} == 'auto'){
						$paddingVal = $val->{$k . '_unit'};
					}
					$this->addRule('padding-' . $k, $paddingVal);
				}
				break;
			case 'margin':
				$keys = array('top', 'right', 'bottom', 'left');
				foreach($keys as $k){
					$marginVal = $val->$k . $val->{$k . '_unit'};
					if ($val->{$k . '_unit'} == 'auto'){
						$marginVal = $val->{$k . '_unit'};
					}
					$this->addRule('margin-' . $k, $marginVal);
				}
				break;
			case 'background_settings':
				switch(true){
					case (isIE7()): $engine = 'trident3'; break;
					case (isIE8()): $engine = 'trident4'; break;
					case (isIE9()): $engine = 'trident5'; break;
					case (isIE10()): $engine = 'trident6'; break;
					case (isWebkit()): $engine = 'webkit'; break;
					case (isPresto()): $engine = 'presto'; break;
					case (isMoz()): $engine = 'gecko'; break;
					default: $engine = 'global'; break;
				}

				$backgroundType = $val->type->$engine;
				if (
					$backgroundType == 'global' ||
					(!isset($val->settings->$engine) || !isset($val->settings->$engine->$backgroundType))
				){
					$engine = 'global';
				}
				$Settings = $val->settings->$engine->$backgroundType;
				$Config = $Settings->config;

				if ($backgroundType == 'solid'){
					buildBackgroundAlpha(
						$Config->background_r,
						$Config->background_g,
						$Config->background_b,
						$Config->background_a,
						$this
					);
				}elseif ($backgroundType == 'image'){
					$this->addRule('background-color', $Config->background_color);
					$this->addRule('background-image', 'url(' . $Config->background_image . ')');
					$this->addRule('background-position', $Config->background_position_x . '% ' . $Config->background_position_y . '%');
					$this->addRule('background-repeat', $Config->background_position_repeat);
				}elseif ($backgroundType == 'gradient'){

					$rgba = 'rgba(%s, %s, %s, %s)';
					$colorStops = array();
					$colorStops[] = array(
						sprintf($rgba,
							$Config->start_color_r,
							$Config->start_color_g,
							$Config->start_color_b,
							($Config->start_color_a / 100)
						),
						'0'
					);
					foreach($Settings->colorStops as $stopInfo){
						$colorStops[] = array(
							sprintf($rgba,
								$stopInfo->color_stop_color_r,
								$stopInfo->color_stop_color_g,
								$stopInfo->color_stop_color_b,
								($stopInfo->color_stop_color_a / 100)
							),
							($stopInfo->color_stop_pos / 100)
						);
					}
					$colorStops[] = array(
						sprintf($rgba,
							$Config->end_color_r,
							$Config->end_color_g,
							$Config->end_color_b,
							($Config->end_color_a / 100)
						),
						'1'
					);

					$images = false;
					if (isset($Settings->imagesBefore)){
						foreach($Settings->imagesBefore as $bimageInfo){
							$images[] = array(
								'css_placement' => 'before',
								'image' => $bimageInfo->image_source,
								'repeat' => $bimageInfo->image_repeat,
								'pos_x' => $bimageInfo->image_pos_x . '%',
								'pos_y' => $bimageInfo->image_pos_y . '%'
							);
						}
					}

					if (isset($Settings->imagesAfter)){
						foreach($Settings->imagesAfter as $aimageInfo){
							$images[] = array(
								'css_placement' => 'after',
								'image' => $aimageInfo->image_source,
								'repeat' => $aimageInfo->image_repeat,
								'pos_x' => $aimageInfo->image_pos_x . '%',
								'pos_y' => $aimageInfo->image_pos_y . '%'
							);
						}
					}

					buildLinearGradient(
						$Config->angle,
						$colorStops,
						$images,
						$this
					);
				}
				break;
			case 'background_solid':
				buildBackgroundAlpha(
					$val->background_r,
					$val->background_g,
					$val->background_b,
					$val->background_a,
					$this
				);
				break;
			case 'background_image':
				$this->addRule('background-color', $val->background_color);
				$this->addRule('background-image', 'url(' . $val->background_image . ')');
				$this->addRule('background-position', $val->background_position_x . '% ' . $val->background_position_y . '%');
				$this->addRule('background-repeat', $val->background_position_repeat);
				break;
			case 'border_radius':
				buildBorderRadius(
					$val->border_top_left_radius . $val->border_top_left_radius_unit,
					$val->border_top_right_radius . $val->border_top_right_radius_unit,
					$val->border_bottom_right_radius . $val->border_bottom_right_radius_unit,
					$val->border_bottom_left_radius . $val->border_bottom_left_radius_unit,
					$this
				);
				break;
			case 'background_linear_gradient':
				$colorStops = array();
				foreach($val->colorStops as $sInfo){
					$colorStops[] = array(
						'rgba(' . $sInfo->color->r . ', ' . $sInfo->color->g . ', ' . $sInfo->color->b . ', ' . $sInfo->color->a . ')',
						$sInfo->position
					);
				}

				$images = false;
				if (isset($val->images)){
					$images = array();
					foreach($val->images as $iInfo){
						$images[] = array(
							'css_placement' => $iInfo->css_placement,
							'image' => $iInfo->image,
							'repeat' => $iInfo->repeat,
							'pos_x' => $iInfo->pos_x . $iInfo->pos_x_unit,
							'pos_y' => $iInfo->pos_y . $iInfo->pos_y_unit
						);
					}
				}

				buildLinearGradient(
					$val->angle,
					$colorStops,
					$images,
					$this
				);
				break;
			case 'box_shadow':
				$allShadows = array();
				foreach($val as $sInfo){
					$allShadows[] = array(
						$sInfo->shadow_offset_x,
						$sInfo->shadow_offset_y,
						$sInfo->shadow_blur,
						$sInfo->shadow_spread,
						$sInfo->shadow_color,
						(isset($sInfo->shadow_inset) ? $sInfo->shadow_inset : false)
					);
				}

				buildBoxShadow($allShadows, $this);
				break;
		}
	}

	public function getArray(){
		return $this->definitions;
	}

	public function outputInline(){
		$output = '';
		foreach($this->definitions as $k => $v){
			$output .= $k . ': ' . $v . ';';
		}
		$output .= '';
		return $output;
	}

	public function outputCss(){
		$output = $this->selector . ' { ';
		foreach($this->definitions as $k => $v){
			$output .= $k . ': ' . $v . ';';
		}
		$output .= ' }' . "\n";
		return $output;
	}
}
