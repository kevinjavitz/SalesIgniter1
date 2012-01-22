<?php
class SortedDisplay extends MI_Importable
{

	public $displayOrder = 0;

	public function setDisplayOrder($val) {
		$this->displayOrder = $val;
	}

	public function getDisplayOrder() {
		return $this->displayOrder;
	}
}

