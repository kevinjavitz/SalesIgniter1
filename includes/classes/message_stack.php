<?php
/*
$Id: message_stack.php,v 1.1 2003/05/19 19:45:42 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 osCommerce

Released under the GNU General Public License

Example usage:

$messageStack = new messageStack();
$messageStack->add('general', 'Error: Error 1', 'error');
$messageStack->add('general', 'Error: Error 2', 'warning');
if ($messageStack->size('general') > 0) echo $messageStack->output('general');
*/

class messageStack implements SplObserver {
	private $messages;
	public $msgTemplate;

	public function __construct() {
		$this->messages = array();
		$this->msgTemplate = '<div class="messageStack_%s ui-widget">' .
			'<div style="padding: 0.7em;" class="ui-state-%s ui-corner-all">' .
				'<span style="float: left; margin-right: 0.3em;" class="ui-icon %s"></span>' .
				'%s' .
			'</div>' .
		'</div>';
		
		$this->phpExceptionTemplate = '<table cellpadding="2" cellspacing="0" border="0">' .
			'<tr>' .
				'<td class="main"><b>PHP Error number:</b></td>' .
				'<td class="main">%s</td>' .
			'</tr>' .
			'<tr>' .
				'<td class="main"><b>Server Message:</b></td>' .
				'<td class="main">%s</td>' .
			'</tr>' .
			'<tr>' .
				'<td class="main"><b>File Involved:</b></td>' .
				'<td class="main">%s</td>' .
			'</tr>' .
			'<tr>' .
				'<td class="main"><b>Time Reported:</b></td>' .
				'<td class="main">%s</td>' .
			'</tr>' .
			'<tr>' .
				'<td class="main"><b>On Line:</b></td>' .
				'<td class="main">%s</td>' .
			'</tr>' .
			'<tr>' .
				'<td class="main"><b>PHP Trace:</b></td>' .
				'<td class="main">%s</td>' .
			'</tr>' .
		'</table>';

		$this->typeIcons = array(
			'success' => 'ui-icon-circle-check',
			'error'   => 'ui-icon-circle-close',
			'warning' => 'ui-icon-alert'
		);
	}
	
	public function update(SplSubject $obj){
		$Exception = $obj->getException();
		
		$errorDetail = $Exception->getDetailLevel();
		$errorType = $Exception->getErrorType();
		
		$tableHtml = htmlBase::newElement('table')
		->setCellPadding(2)
		->setCellSpacing(0);
		
		if ($errorType !== 'success' || $errorDetail == 'more'){
			$tableHtml->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>PHP Error number:</b>'),
					array('addCls' => 'main', 'text' => $Exception->getCode())
				)
			));
		}
		
		$tableHtml->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>Server Message:</b>'),
				array('addCls' => 'main', 'text' => $Exception->getMessage())
			)
		));
		
		if ($errorDetail == 'more'){
			$tableHtml->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>File Involved:</b>'),
					array('addCls' => 'main', 'text' => $Exception->getFile())
				)
			));
		}
		
		$tableHtml->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => '<b>Time Reported:</b>'),
				array('addCls' => 'main', 'text' => date('F d, Y @ H:i:s a (T)'))
			)
		));
		
		
		if ($errorDetail == 'more'){
			$tableHtml->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>On Line:</b>'),
					array('addCls' => 'main', 'text' => $Exception->getLine())
				)
			));
			
			$tableHtml->addBodyRow(array(
				'columns' => array(
					array('addCls' => 'main', 'text' => '<b>PHP Trace:</b>'),
					array('addCls' => 'main', 'text' => nl2br($Exception->getTraceAsString()))
				)
			));
		}
		
		$this->addSession('footerStack', $tableHtml->draw(), $errorType);
	}

	public function add($group, $message, $type = 'error') {
		$this->messages[] = array(
		'text'  => $message,
		'type'  => $type,
		'group' => $group
		);
	}

	public function add_session($group, $message, $type = 'error'){
		$this->addSession($group, $message, $type);
	}

	public function parseMultipleIntoTable($messages){
		$table = '<table cellpadding="2" cellspacing="0" border="0">';
		foreach($messages as $message){
			$table .= '<tr><td class="main">' . $message . '</td></tr>';
		}
		$table .= '</table>';
		return $table;
	}

	public function parseMultipleIntoOrderedList($messages){
		$list = '<ol>';
		foreach($messages as $message){
			$list .= '<li>' . $message . '</li>';
		}
		$list .= '</ol>';
		return $list;
	}

	public function parseMultipleIntoUnorderedList($messages){
		$list = '<ul>';
		foreach($messages as $message){
			$list .= '<li>' . $message . '</li>';
		}
		$list .= '</ul>';
		return $list;
	}

	public function addMultiple($group, $messages, $type = 'error', $formatter = 'table'){
		switch($formatter){
			case 'ordered_list':
				$element = $this->parseMultipleIntoOrderedList($messages);
				break;
			case 'unordered_list':
				$element = $this->parseMultipleIntoUnorderedList($messages);
				break;
			case 'table':
			default:
				$element = $this->parseMultipleIntoTable($messages);
				break;
		}
		$this->add($group, $element, $type);
	}

	public function addSessionMultiple($group, $messages, $type = 'error', $formatter = 'table'){
		switch($formatter){
			case 'ordered_list':
				$element = $this->parseMultipleIntoOrderedList($messages);
				break;
			case 'unordered_list':
				$element = $this->parseMultipleIntoUnorderedList($messages);
				break;
			case 'table':
			default:
				$element = $this->parseMultipleIntoTable($messages);
				break;
		}
		$this->addSession($group, $element, $type);
	}

	public function addSession($group, $message, $type = 'error') {
		if (Session::exists('messageToStack') === false) {
			Session::set('messageToStack', array());
		}
		$duplicate = false;
		foreach($this->messages as $msg){
			if ($msg['group'] == $group && $msg['text'] == $message && $msg['type'] == $type) {
				$duplicate = true;
			}
		}
		if (!$duplicate){
			Session::append('messageToStack', array(
				'group' => $group,
				'text'  => $message,
				'type'  => $type
			));
		}
	}

	public function reset() {
		$this->messages = array();
	}

	public function output($group, $groupMessages = false) {
		$output = array();
		$urgency = array();
		if (Session::exists('messageToStack') === true){
			$msgArr = &Session::getReference('messageToStack');
			foreach($msgArr as $index => $msg){
				if ($msg['group'] == $group){
					$this->add($msg['group'], $msg['text'], $msg['type']);
					unset($msgArr[$index]);
				}
			}
			if (sizeof($msgArr) <= 0){
				Session::remove('messageToStack');
			}
		}

		foreach($this->messages as $msg){
			if ($msg['group'] == $group) {
				$urgency[$msg['type']][] = $msg;
			}
		}

		if (isset($urgency['error'])){
			if ($groupMessages === true){
				$multiple = array();
				foreach($urgency['error'] as $msg){
					$multiple[] = $msg['text'];
				}
				$output[] = $this->parseTemplate($group, $this->parseMultipleIntoTable($multiple), 'error');
			}else{
				foreach($urgency['error'] as $msg){
					$output[] = $this->parseTemplate($group, $msg['text'], $msg['type']);
				}
			}
		}

		if (isset($urgency['warning'])){
			if ($groupMessages === true){
				$multiple = array();
				foreach($urgency['warning'] as $msg){
					$multiple[] = $msg['text'];
				}
				$output[] = $this->parseTemplate($group, $this->parseMultipleIntoTable($multiple), 'warning');
			}else{
				foreach($urgency['warning'] as $msg){
					$output[] = $this->parseTemplate($group, $msg['text'], $msg['type']);
				}
			}
		}

		if (isset($urgency['success'])){
			if ($groupMessages === true){
				$multiple = array();
				foreach($urgency['success'] as $msg){
					$multiple[] = $msg['text'];
				}
				$output[] = $this->parseTemplate($group, $this->parseMultipleIntoTable($multiple), 'success');
			}else{
				foreach($urgency['success'] as $msg){
					$output[] = $this->parseTemplate($group, $msg['text'], $msg['type']);
				}
			}
		}

		return implode($output, '<br />');
	}

	public function parseTemplate($group, $message, $type = 'error'){
		return sprintf($this->msgTemplate,
			$group,
			$type,
			$this->typeIcons[$type],
			stripslashes($message)
		);
	}

	public function size($group) {
		$count = 0;
		if (Session::exists('messageToStack') === true){
			$msgArr = Session::get('messageToStack');
			foreach($msgArr as $index => $msg){
				if ($msg['group'] == $group){
					$count++;
				}
			}
		}

		for ($i=0, $n=sizeof($this->messages); $i<$n; $i++) {
			if ($this->messages[$i]['group'] == $group) {
				$count++;
			}
		}
		return $count;
	}
}
?>