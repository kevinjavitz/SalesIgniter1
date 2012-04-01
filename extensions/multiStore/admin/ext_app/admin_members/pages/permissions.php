<?php
class MultiStore_admin_admin_members_permissions extends Extension_multiStore {

	public function __construct(){
		parent::__construct('multiStore');
	}

	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
				'AdminExtraPermissions'
			), null, $this);
	}

	public function AdminExtraPermissions(htmlWidget_infobox &$infoBox, $gID){
		$checkboxes = array();
		$AdminGroups = Doctrine_Core::getTable('AdminGroups')->find($gID);
		$varExtra = unserialize($AdminGroups->extra_data);
		$checked = array();
		if($varExtra['buttonsMultistoreEnabled']['hasPayInvoice'] == true){
			$checked[] = 'hasPayInvoice';
		}
		if($varExtra['buttonsMultistoreEnabled']['hasCreateInvoice'] == true){
			$checked[] = 'hasCreateInvoice';
		}
		$checkboxes[] = array(
				'value' => 'hasCreateInvoice',
				'label' => 'Has Create Invoice',
				'labelPosition' => 'after'
		);

		$checkboxes[] = array(
			'value' => 'hasPayInvoice',
			'label' => 'Has Pay Invoice',
			'labelPosition' => 'after'
		);


		$buttonsGroup = htmlBase::newElement('checkbox')
			->addGroup(array(
				'name' => 'buttonsMultistoreEnabled[]',
				'data' => $checkboxes,
				'checked' => $checked
			));

		$infoBox->addContentRow(sysLanguage::get('ENTRY_ADMINS_ENABLED_BUTTONS') . '<br>' . $buttonsGroup->draw());
	}
}