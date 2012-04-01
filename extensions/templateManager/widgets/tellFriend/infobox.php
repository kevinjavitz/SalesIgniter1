<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxTellFriend extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('tellFriend');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_TELLFRIEND'));
	}

	public function show(){
		if (isset($_GET['products_id'])){
			$boxContent = tep_draw_form('tell_a_friend', itw_app_link(null, 'tell_a_friend', 'default', 'NONSSL', false), 'get') .
			tep_draw_input_field('to_email_address', '', 'size="10"') .
			'&nbsp;' .
			htmlBase::newElement('button')->setType('submit')->setText('Send')->draw() . 
			tep_draw_hidden_field('products_id', $_GET['products_id']) .
			tep_hide_session_id() .
			'<br>' .
			sysLanguage::get('INFOBOX_TELLFRIEND_TEXT') .
			'</form>';

			$this->setBoxContent($boxContent);

			return $this->draw();
		}
		return false;
	}
}
?>