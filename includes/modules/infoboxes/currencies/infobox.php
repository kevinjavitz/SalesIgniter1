<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCurrencies extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('currencies');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_CURRENCIES'));
	}

	public function show(){
		global $currencies, $request_type;
		if (isset($currencies) && is_object($currencies)) {
			reset($currencies->currencies);
			$currencies_array = array();
			while (list($key, $value) = each($currencies->currencies)) {
				$currencies_array[] = array('id' => $key, 'text' => $value['title']);
			}

			$hidden_get_variables = '';
			reset($_GET);
			while (list($key, $value) = each($_GET)) {
				if (is_array($value)){
					foreach($value as $k => $v){
						$hidden_get_variables .= tep_draw_hidden_field($key . '[]', $v);
					}
				}else{
					if ( ($key != 'currency') && ($key != Session::getSessionName()) && ($key != 'x') && ($key != 'y') ) {
						$hidden_get_variables .= tep_draw_hidden_field($key, $value);
					}
				}
			}

			$boxContent = tep_draw_form('currencies', itw_app_link(tep_get_all_get_params(array('action'))), 'get') .
			tep_draw_pull_down_menu('currency', $currencies_array, Session::get('currency'), 'onChange="this.form.submit();"') .
			$hidden_get_variables .
			tep_hide_session_id() .
			'</form>';
			
			$this->setBoxContent($boxContent);
			return $this->draw();
		}
		return false;
	}
}
?>
