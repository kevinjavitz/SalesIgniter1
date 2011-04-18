<?php
	class newsletter {
		var $show_choose_audience, $title, $content;

		function newsletter($title, $content) {
			$this->show_choose_audience = false;
			$this->title = $title;
			$this->content = $content;
		}

		function choose_audience() {
			return false;
		}

		function confirm() {
			$Qmail = Doctrine_Query::create()
			->select('COUNT(*) AS total')
			->from('Customers')
			->where('customers_newsletter = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$confirm_string = '<table border="0" cellspacing="0" cellpadding="2">' .
				'<tr>' . 
					'<td class="main"><b>' . $this->title . '</b></td>' . 
				'</tr>' . 
				'<tr>' . 
					'<td>' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . 
				'</tr>' . 
				'<tr>' . 
					'<td class="main"><tt>' . nl2br($this->content) . '</tt></td>' . 
				'</tr>' . 
				'<tr>' . 
					'<td>' . tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>' . 
				'</tr>' . 
				'<tr>' . 
					'<td align="right">' . htmlBase::newElement('button')->usePreset('email')->setText(sysLanguage::get('IMAGE_SEND'))->setHref(itw_app_link((isset( $_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $_GET['nID'], 'newsletters', 'confirm_send'))->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link((isset( $_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $_GET['nID'], 'newsletters', 'default'))->draw() . '</td>' . 
				'</tr>' . 
			'</table>';

			return $confirm_string;
		}

		function send($newsletter_id) {
			$Qmail = Doctrine_Query::create()
			->select('customers_firstname, customers_lastname, customers_email_address')
			->from('Customers')
			->where('customers_newsletter = ?', '1')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
			$mimemessage->add_text($this->content);
			$mimemessage->build_message();
			foreach($Qmail as $mInfo){
				$mimemessage->send(
					$mInfo['customers_firstname'] . ' ' . $mInfo['customers_lastname'],
					$mInfo['customers_email_address'],
					'',
					sysConfig::get('EMAIL_FROM'),
					$this->title
				);
			}

			Doctrine_Query::create()
			->update('Newsletters')
			->set('date_sent', 'now()')
			->set('status', '?', '1')
			->where('newsletters_id = ?', (int) $newsletter_id)
			->execute();
		}
	}
?>
