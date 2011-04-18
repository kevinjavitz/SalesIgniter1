<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com
	Copyright (c) 2009 I.T. Web Experts
	This script and it's source is not redistributable
*/

class InfoBoxTalkToUs extends InfoBoxAbstract {

	public function __construct(){
		$this->init('talkToUs');
		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_TALKTOUS'));
	}

	public function show(){
			$htmlForm = htmlBase::newElement('form')
						->attr('name','talkToUs')
						->attr('method','post')
						->attr('action', tep_href_link('includes/modules/infoboxes/talkToUs/formResponse.php'));

			$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

			$htmlURL = htmlBase::newElement('input')
						->setType('hidden')
						->setName('url')
						->setValue($url);

			$htmlText = htmlBase::newElement('p')
						->html('We want to hear from you.  Please let us know if you have questions, comments or feedback on any of our products or services.');

			$htmlBr = htmlBase::newElement('br');
			$htmlEmail = htmlBase::newElement('input')
						->setName('email_address')
						->setLabel('Email Address:')
						->setLabelPosition('before')
						->setLabelSeparator('<br/>')
						->setId('emailAddressTalkToUs');

			$htmlPhone = htmlBase::newElement('input')
						->setName('phone')
						->setLabel('Phone Number:')
						->setLabelPosition('before')
						->setLabelSeparator('<br/>')
						->setId('phoneTalkToUs');

			$htmlName = htmlBase::newElement('input')
						->setName('name')
						->setLabel('Name:')
						->setLabelPosition('before')
						->setLabelSeparator('<br/>')
						->setId('nameTalkToUs');

			$htmlTextBeforeMessage = htmlBase::newElement('span')
									->html('Message:');

			$htmlMessage = htmlBase::newElement('textarea')
						->attr('rows', 5)
						->attr('cols', 20)
						->attr('id','messageTalkToUs')
						->setName('message');



			$htmlButton = htmlBase::newElement('button')
						->setType('submit')
						->setText('Send');

			$htmlForm->append($htmlText)
					 ->append($htmlName)
					 ->append($htmlBr)
					 ->append($htmlPhone)
					 ->append($htmlBr)
					 ->append($htmlEmail)
					 ->append($htmlBr)
					 ->append($htmlTextBeforeMessage)
					 ->append($htmlMessage)
					 ->append($htmlURL)
					 ->append($htmlButton);

			$this->setBoxContent($htmlForm->draw());

			return $this->draw();

	}

}

?>