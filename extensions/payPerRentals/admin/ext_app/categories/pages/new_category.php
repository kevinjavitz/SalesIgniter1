<?php
class payPerRentals_admin_categories_new_category extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'NewCategoryTabHeader',
			'NewCategoryTabBody'
		), null, $this);
	}
	
	public function NewCategoryTabHeader(){
		return '<li class="ui-tabs-nav-item"><a href="#tab_' . $this->getExtensionKey() . '"><span>' . 'Pay Per Rentals' . '</span></a></li>';
	}

	public function NewCategoryTabBody(&$cInfo){
		$PPRshowInMenu = htmlBase::newElement('checkbox')
						->setName('ppr_show_in_menu')
						->setLabelPosition('before')
						->setChecked((($cInfo->ppr_show_in_menu == '1') ? true:false))
						->setLabel('Show In Menu');
		return '<div id="tab_' . $this->getExtensionKey() . '">' . $PPRshowInMenu->draw() . '</div>';
	}
}
?>