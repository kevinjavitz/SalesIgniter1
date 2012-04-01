<?php
/**
 * Icon Widget Class
 * @package Html
 */
class htmlWidget_icon implements htmlWidgetPlugin
{

	protected $iconElement;

	public function __construct() {
		$this->iconElement = new htmlElement('a');
		$this->iconElement->addClass('ui-icon');
	}

	public function __call($function, $args) {
		$return = call_user_func_array(array($this->iconElement, $function), $args);
		if (!is_object($return)){
			return $return;
		}
		return $this;
	}

	/* Required Functions From Interface: htmlElementPlugin --BEGIN-- */
	public function startChain() {
		return $this;
	}

	public function setId($val) {
		$this->iconElement->attr('id', $val);
		return $this;
	}

	public function setName($val) {
		$this->iconElement->attr('name', $val);
		return $this;
	}

	public function addClass($val) {
		$this->iconElement->addClass($val);
		return $this;
	}

	public function draw() {
		return $this->iconElement->draw();
	}

	/* Required Functions From Interface: htmlElementPlugin --END-- */

	public function changeElement($elType) {
		$this->iconElement->changeElement($elType);
		return $this;
	}

	public function getIconClassFromType($type) {
		$icon = 'ui-icon-';
		switch($type){
			case 'folderClosed':
				$icon .= 'folder-collapsed';
				break;
			case 'folderOpen':
				$icon .= 'folder-open';
				break;
			case 'info':
				$icon .= 'info';
				break;
			case 'alert':
				$icon .= 'alert';
				break;
			case 'radioOff':
				$icon .= 'radio-off';
				break;
			case 'radioOn':
				$icon .= 'radio-on';
				break;
			case 'bullet':
				$icon .= 'bullet';
				break;
			case 'circleTriangleNorth':
				$icon .= 'circle-triangle-n';
				break;
			case 'circleTriangleEast':
				$icon .= 'circle-triangle-e';
				break;
			case 'circleTriangleSouth':
				$icon .= 'circle-triangle-s';
				break;
			case 'circleTriangleWest':
				$icon .= 'circle-triangle-w';
				break;
			case 'triangleNorth':
				$icon .= 'triangle-1-n';
				break;
			case 'triangleEast':
				$icon .= 'triangle-1-e';
				break;
			case 'triangleSouth':
				$icon .= 'triangle-1-s';
				break;
			case 'triangleWest':
				$icon .= 'triangle-1-w';
				break;
			case 'thickArrowNorth':
				$icon .= 'arrowthick-1-n';
				break;
			case 'thickArrowEast':
				$icon .= 'arrowthick-1-e';
				break;
			case 'thickArrowSouth':
				$icon .= 'arrowthick-1-s';
				break;
			case 'thickArrowWest':
				$icon .= 'arrowthick-1-w';
				break;
			case 'circleCheck':
				$icon .= 'circle-check';
				break;
			case 'circleClose':
				$icon .= 'circle-close';
				break;
			case 'circlePlus':
				$icon .= 'circle-plus';
				break;
			case 'next':
				$icon .= 'seek-next';
				break;
			case 'lockClosed':
			case 'login':
				$icon .= 'locked';
				break;
			case 'lockOpen':
				$icon .= 'unlocked';
				break;
			case 'print':
				$icon .= 'print';
				break;
			case 'help':
				$icon .= 'help';
				break;
			case 'pencil':
				$icon .= 'pencil';
				break;
			case 'newwin':
				$icon .= 'newwin';
				break;

			case 'star':
				$icon .= 'star';
				break;
			case 'refresh':
				$icon .= 'refresh';
				break;
			case 'cancel':
				$icon .= 'cancel';
				break;
			case 'process':
			case 'required':
				$icon .= 'gear';
				break;
			case 'disc':
				$icon .= 'disk';
				break;
			case 'move':
				$icon .= 'arrow-4';
				break;
			case 'stop':
				$icon .= 'stop';
				break;
			case 'zoomIn':
				$icon .= 'zoomin';
				break;
			case 'zoomOut':
				$icon .= 'zoomout';
				break;
			case 'trash':
				$icon .= 'trash';
				break;
			case 'save':
				$icon .= 'check';
				break;
			case 'wrench':
			case 'edit':
				$icon .= 'wrench';
				break;
			case 'plusThick':
			case 'insert':
			case 'add':
			case 'install':
				$icon .= 'plusthick';
				break;
			case 'minusThick':
			case 'uninstall':
				$icon .= 'minusthick';
				break;
			case 'closeThick':
			case 'delete':
			case 'remove':
				$icon .= 'closethick';
				break;
			case 'search':
				$icon .= 'search';
				break;
			case 'email':
				$icon .= 'mail-closed';
				break;
			case 'orders':
			case 'invoice':
				$icon .= 'document';
				break;
			case 'copy':
				$icon .= 'copy';
				break;
			case 'details':
				$icon .= 'document-b';
				break;
			case 'comment':
				$icon .= 'comment';
				break;
			default:
				$icon .= $type;
		}
		return $icon;
	}

	public function setType($type) {
		$this->iconElement->addClass($this->getIconClassFromType($type));
		return $this;
	}

	public function setTooltip($val) {
		$this->iconElement->attr('tooltip', $val);
		return $this;
	}

	public function click($val) {
		$this->iconElement->click($val);
		return $this;
	}

	public function setHref($val) {
		$this->iconElement->attr('href', $val);
		return $this;
	}

	public function css($key, $val = '') {
		$this->iconElement->css($key);
		return $this;
	}

	public function hide() {
		$this->iconElement->css('display', 'none');
		return $this;
	}
}

?>