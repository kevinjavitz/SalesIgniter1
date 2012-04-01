<?php
	class subAccounts_admin_customFields_manage_default extends Extension_subAccounts {
	public function __construct(){
		global $App;
		parent::__construct();

	}

	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'ProductCustomFieldsAddOptions',
			'CustomFieldsNewOptions',
			'CustomFieldsSaveOptions'
		), null, $this);

	}

	public function CustomFieldsSaveOptions(&$Field){
		$Field->filter_sub_accounts = (isset($_POST['filter_sub_accounts']) ? '1' : '0');
	}

	public function CustomFieldsNewOptions($Field, &$finalTable, $windowAction){
		$filterSubaccountCheckbox = htmlBase::newElement('checkbox')
		->setId('filter_sub_accounts_' . $windowAction)
		->setName('filter_sub_accounts')
		->setLabel('<b>' . sysLanguage::get('ENTRY_FILTER_SUBACCOUNTS') . '</b>')
		->setLabelPosition('after')
		->setValue('1')
		->setChecked(false);

		if (isset($Field) && $Field !== false){
			$filterSubaccountCheckbox->setId('filter_sub_accounts_' . $Field['field_id'] . $windowAction)
				->setChecked(($Field['filter_sub_accounts'] == '1'));
		}
		$finalTable->addBodyRow(array('columns' => array(
			array('addCls' => 'main', 'text' => $filterSubaccountCheckbox)
		)));
	}

	public function ProductCustomFieldsAddOptions(&$htmlfields, $fInfo){
		global $userAccount;

		//$field = '<br />' . sysLanguage::get('TEXT_FILTER_FROM_ACCOUNT') . ($fInfo['filter_sub_accounts'] == '1' ? 'Yes' : 'No');
		//$htmlfields .= $field;

	}
}
?>