<?php
	$BillingPasswordInput = htmlBase::newElement('input')
	->setType('password')
	->attr('maxlength', '40')
	->setName('password')
	->setRequired(true);

	$BillingPasswordConfirmInput = htmlBase::newElement('input')
	->setType('password')
	->attr('maxlength', '40')
	->setName('confirmation')
	->setRequired(true);
	
	$NewsletterInput = htmlBase::newElement('checkbox')
	->setLabel(sysLanguage::get('ENTRY_NEWSLETTER'))
	->setLabelPosition('before')
	->setName('newsletter')
	->setValue('1');

	$FormTable = htmlBase::newElement('formTable');

	$FormTable->addRow(sysLanguage::get('ENTRY_EMAIL_ADDRESS'));
	$FormTable->addRow(tep_draw_input_field('email_address') . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

	$FormTable->addRow(sysLanguage::get('ENTRY_GENDER'));
	$FormTable->addRow(tep_draw_radio_field('gender', 'm') . '&nbsp;&nbsp;' . sysLanguage::get('MALE') . '&nbsp;&nbsp;' . tep_draw_radio_field('gender', 'f') . '&nbsp;&nbsp;' . sysLanguage::get('FEMALE') . '&nbsp;' );


	$FormTable->addRow(sysLanguage::get('ENTRY_DATE_OF_BIRTH'));
	$FormTable->addRow(tep_draw_input_field('dob') . '&nbsp;');

	$FormTable->addRow(sysLanguage::get('ENTRY_FIRST_NAME'), sysLanguage::get('ENTRY_LAST_NAME'));
	$FormTable->addRow(tep_draw_input_field('firstname') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>', tep_draw_input_field('lastname') . '&nbsp;' . '<a style="display: inline-block;" tooltip="Input Required" class="ui-icon ui-icon-gear ui-icon-required"></a>');

//show list of custom fields to check--- move this part into custom fields ext
$Qfields = Doctrine_Query::create()
	->from('ProductsCustomFields f')
	->leftJoin('f.ProductsCustomFieldsDescription fd')
	->where('f.filter_sub_accounts = ?', '1')
	->execute();
$rows = array();
if ($Qfields->count() > 0){
	foreach($Qfields->toArray() as $field){
		$fieldId = $field['field_id'];
		$fieldType = $field['input_type'];
		$fieldName = $field['ProductsCustomFieldsDescription'][Session::get('languages_id')]['field_name'];

		$value = '';


		$input = '';
		switch($fieldType){
			case 'select':
				$oArr = array();

				$Qoptions = Doctrine_Query::create()
					->from('ProductsCustomFieldsOptions o')
					->leftJoin('o.ProductsCustomFieldsOptionsDescription od')
					->leftJoin('o.ProductsCustomFieldsOptionsToFields o2f')
					->where('o2f.field_id = ?', $fieldId)
					->andWhere('od.language_id=?', Session::get('languages_id'))
					->orderBy('sort_order')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$QChecked = Doctrine_Query::create()
					->from('ProductCustomFieldsToCustomers')
					->where('customers_id=?', $cID)
					->andWhere('product_custom_field_id=?', $fieldId)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$checkedOptions = explode(',', $QChecked[0]['options']);
				foreach($Qoptions as $option){
					//get array of checked options
					$name = $option['ProductsCustomFieldsOptionsDescription'][0]['option_name'];
					$checked = '';
					if(in_array($name, $checkedOptions)){
						$checked = 'checked="checked"';
					}
					$input .= '<input '.$checked.' name="fields[' . $fieldId . '][]" type="checkbox" value="'.$name.'">'.$name.'<br/>';
				}

				break;
			/*case 'text':
				$input = tep_draw_input_field('fields[' . $fieldId . ']', $value);
				break;
			case 'search':
			case 'textarea':
				$input = tep_draw_textarea_field('fields[' . $fieldId . ']', 'soft', '30', '3', $value) . ($fieldType == 'search' ? '<br><small>*Separate values using ;</small>' : '');
				break;
			case 'upload':
				$input = tep_draw_file_field('fields_' . $fieldId) . '<br>Local File: ' . tep_draw_input_field('fields[' . $fieldId . ']', $value);
				break;*/
		}
		$rows[] = '<tr>
						 <td class="main" valign="top">' . $fieldName . ':</td>
						 <td class="main">' . $input . '</td>
						</tr>';
	}
}

$html = '<table cellpadding="3" cellspacing="0">' .
	'<tr>' .
	'<td><table cellpadding="3" cellspacing="0">' .
	implode('', $rows) .
	'</table></td>' .
	'</tr>' .
	'</table>';


	ob_start();
?>
	<div class="">
		<div class="" style="margin-top:10px;margin-bottom:10px;line-height:2em;"><?php echo sysLanguage::get('TEXT_ADDRESS'); ?></div>
		<?php
			echo $FormTable->draw();
		?>
	</div>
	<div class="">
		<div class="" style="margin-top:10px;margin-bottom:10px;line-height:2em;"><?php echo sysLanguage::get('TEXT_ACCOUNT_SETTINGS'); ?></div>
		<table class="accountSettings" cellpadding="0" cellspacing="0" border="0" style="margin:.3em;">
			<tr>
				<td><?php echo sysLanguage::get('ENTRY_PASSWORD'); ?></td>
				<td><?php echo $BillingPasswordInput->draw(); ?></td>
				<td><div id="pstrength_password"></div></td>
			</tr>
			<tr>
				<td><?php echo sysLanguage::get('ENTRY_PASSWORD_CONFIRMATION'); ?></td>
				<td colspan="2"><?php echo $BillingPasswordConfirmInput->draw(); ?></td>
			</tr>

		</table>

	</div>
<div class="">
	<div class="" style="margin-top:10px;margin-bottom:10px;line-height:2em;"><?php echo sysLanguage::get('TEXT_FILTER_FIELDS'); ?></div>
	<table class="accountSettings" cellpadding="0" cellspacing="0" border="0" style="margin:.3em;">
		<tr>
			<td><?php
echo $html;
				?></td>
		</tr>

	</table>

</div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_CREATE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setText(sysLanguage::get('TEXT_CREATE_ACCOUNT'))
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'create_account',
		'action' => itw_app_link('action=createAccount&appExt=subAccounts', 'manage', 'create', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>