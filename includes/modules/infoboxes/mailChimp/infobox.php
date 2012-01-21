<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxMailChimp extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('mailChimp');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
		    $htmlDiv = htmlBase::newElement('div')
			->addClass('mailChimp');

			$htmlEmail = htmlBase::newElement('input')
						->setName('email_address')
						->setLabel(sysLanguage::get('INFOBOX_MAILCHIMP_EMAIL_ADDRESS'))
						->setLabelPosition('before')
						->setLabelSeparator('<br/>')
						->setId('emailMailChimp');
		    $htmlButton = htmlBase::newElement('button')
						->setType('submit')
		                ->addClass('mailChimpSignup')
						->setText(sysLanguage::get('INFOBOX_MAILCHIMP_SEND'));
			$htmlDiv->append($htmlEmail)
			->append($htmlButton);

			$this->setBoxContent($htmlDiv->draw());
			return $this->draw();
	}

		public function buildJavascript(){
		$boxWidgetProperties = $this->getWidgetProperties();

		ob_start();
?>
		$('.mailChimpSignup').click(function() {

				$.ajax({
					url: 'includes/modules/infoboxes/mailChimp/storeAddress.php',

					data: 'ajax=true&email=' + $('#emailMailChimp').val()+'&api=<?php echo $boxWidgetProperties->api_key;?>&list=<?php echo $boxWidgetProperties->list_id;?>',
					success: function(msg) {
						alert(msg);
				        $('#emailMailChimp').val('');
					}
				});

				return false;
		});


<?php
		$javascript = '/* MailChimp Menu --BEGIN-- */' . "\n" .
			ob_get_contents();
		'/* MailChimp --END-- */' . "\n";
		ob_end_clean();

		return $javascript;
	}
}
?>