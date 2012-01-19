<?php
class MultiStore_admin_admin_members_default extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}

	public function load(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
				'AdminMembersNewEditWindowBeforeDraw'
			), null, $this);
	}

	public function AdminMembersNewEditWindowBeforeDraw(htmlWidget_infobox &$infoBox, Admin $Admin){
		$checkboxes = array();
		foreach($this->getStoresArray(false, true) as $sInfo){
			$checkboxes[] = array(
				'value' => $sInfo['stores_id'],
				'label' => $sInfo['stores_name'],
				'labelPosition' => 'after'
			);
		}

		$storesGroup = htmlBase::newElement('checkbox')
			->addGroup(array(
				'name' => 'admins_stores[]',
				'data' => $checkboxes,
				'checked' => explode(',', $Admin->admins_stores)
			));

		$infoBox->addContentRow(sysLanguage::get('ENTRY_ADMINS_STORES') . '<br>' . $storesGroup->draw());
	}
}