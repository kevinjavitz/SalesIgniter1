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
					$this->styles[] = $Style->outputInline();
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
				$this->styles[] = $Style->outputInline();
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
	public function disable($val) {
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
			$this->definitions[] = $ruleName . ':' . $ruleVal;
		}
	}

	public function parseJsonRule($key, $val){
		switch($key){
			case 'font':
				if (isset($val->family)) {
					$this->definitions[] = 'font-family: ' . $val->family;
				}

				if (isset($val->style)) {
					$this->definitions[] = 'font-style: ' . $val->style;
				}

				if (isset($val->variant)) {
					$this->definitions[] = 'font-variant: ' . $val->variant;
				}

				if (isset($val->weight)) {
					$this->definitions[] = 'font-weight: ' . $val->weight;
				}

				if (isset($val->size)){
					if ($val->size_unit == 'inherit'){
						$this->definitions[] = 'font-size: ' . $val->size_unit;
					}
					else {
						$this->definitions[] = 'font-size: ' . $val->size . $val->size_unit;
					}
				}
				break;
			case 'text':
				$this->definitions[] = 'color: ' . $val->color;
				$this->definitions[] = 'text-align: ' . $val->align;
				$this->definitions[] = 'text-decoration: ' . $val->decoration;
				$this->definitions[] = 'text-transform: ' . $val->transform;
				$this->definitions[] = 'vertical-align: ' . $val->vertical_align;
				$this->definitions[] = 'white-space: ' . $val->white_space;

				if ($val->indent_unit == 'inherit'){
					$this->definitions[] = 'text-indent: ' . $val->indent_unit;
				}
				else {
					$this->definitions[] = 'text-indent: ' . $val->indent . $val->indent_unit;
				}

				if ($val->line_height_unit == 'inherit'){
					$this->definitions[] = 'line-height: ' . $val->line_height_unit;
				}
				else {
					$this->definitions[] = 'line-height: ' . $val->line_height . $val->line_height_unit;
				}

				if ($val->letter_spacing_unit == 'normal' || $val->letter_spacing_unit == 'inherit'){
					$this->definitions[] = 'letter-spacing: ' . $val->letter_spacing_unit;
				}
				else {
					$this->definitions[] = 'letter-spacing: ' . $val->letter_spacing . $val->letter_spacing_unit;
				}

				if ($val->word_spacing_unit == 'normal' || $val->word_spacing_unit == 'inherit'){
					$this->definitions[] = 'word-spacing: ' . $val->word_spacing_unit;
				}
				else {
					$this->definitions[] = 'word-spacing: ' . $val->word_spacing . $val->word_spacing_unit;
				}
				break;
			case 'border':
				$keys = array('top', 'right', 'bottom', 'left');
				foreach($keys as $k){
					$this->definitions[] = 'border-' . $k . '-width: ' . $val->$k->width . $val->$k->width_unit;
					$this->definitions[] = 'border-' . $k . '-color: ' . $val->$k->color;
					$this->definitions[] = 'border-' . $k . '-style: ' . $val->$k->style;
				}
				break;
			case 'padding':
				$keys = array('top', 'right', 'bottom', 'left');
				foreach($keys as $k){
					if ($val->{$k . '_unit'} == 'auto'){
						$this->definitions[] = 'padding-' . $k . ': ' . $val->{$k . '_unit'};
					}
					else {
						$this->definitions[] = 'padding-' . $k . ': ' . $val->$k . $val->{$k . '_unit'};
					}
				}
				break;
			case 'margin':
				$keys = array('top', 'right', 'bottom', 'left');
				foreach($keys as $k){
					if ($val->{$k . '_unit'} == 'auto'){
						$this->definitions[] = 'margin-' . $k . ': ' . $val->{$k . '_unit'};
					}
					else {
						$this->definitions[] = 'margin-' . $k . ': ' . $val->$k . $val->{$k . '_unit'};
					}
				}
				break;
			case 'background_solid':
				$css = buildBackgroundAlpha(
					$val->background_r,
					$val->background_g,
					$val->background_b,
					$val->background_a
				);
				$this->definitions[] = $css;
				break;
			case 'border_radius':
				$css = buildBorderRadius(
					$val->border_top_left_radius . $val->border_top_left_radius_unit,
					$val->border_top_right_radius . $val->border_top_right_radius_unit,
					$val->border_bottom_right_radius . $val->border_bottom_right_radius_unit,
					$val->border_bottom_left_radius . $val->border_bottom_left_radius_unit
				);
				$this->definitions[] = $css;
				break;
			case 'background_complex_gradient':
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

				$css = buildComplexGradient(
					$val->type,
					$val->h_pos_start . $val->h_pos_start_unit,
					$val->v_pos_start . $val->v_pos_start_unit,
					$val->h_pos_end . $val->h_pos_end_unit,
					$val->v_pos_end . $val->v_pos_end_unit,
					$colorStops,
					$images
				);
				$this->definitions[] = $css;
				break;
			case 'box_shadow':
				$css = '';

				$allShadows = array();
				foreach($val as $sInfo){
					$allShadows[] = (isset($sInfo->shadow_inset) && $sInfo->shadow_inset === true ? 'inset ' : '') .
						$sInfo->shadow_offset_x . ' ' .
						$sInfo->shadow_offset_y . ' ' .
						$sInfo->shadow_blur . ' ' .
						$sInfo->shadow_spread . ' ' .
						$sInfo->shadow_color;
				}

				if ($this->isIE() === true){
					$css .= 'box-shadow: ' . implode(', ', $allShadows) . ';' .
						'behavior: url(' . sysConfig::getDirWsCatalog() . 'ext/ie_behave/PIE.htc)';
				}elseif ($this->isMoz() === true){
					$css .= '-moz-box-shadow:' . implode(', ', $allShadows);
				}elseif ($this->isWebkit()){
					$css .= '-webkit-box-shadow:' . implode(', ', $allShadows);
				}
				$this->definitions[] = $css;
				break;
		}
	}

	public function outputInline(){
		$output = '';
		foreach($this->definitions as $d){
			$output .= $d . ';';
		}
		$output .= '';
		return $output;
	}

	public function outputCss(){
		$output = $this->selector . ' { ';
		foreach($this->definitions as $d){
			$output .= $d . ';';
		}
		$output .= ' }' . "\n";
		return $output;
	}
}
