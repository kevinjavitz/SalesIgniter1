<?php
class upload {
	var $file, $filename, $destination, $permissions, $extensions, $tmp_filename, $message_location;

	function upload($file = '', $destination = '', $permissions = '777', $extensions = '') {
		$this->set_file($file);
		$this->set_destination($destination);
		$this->set_permissions($permissions);
		$this->set_extensions($extensions);

		$this->set_output_messages('session');

		if (tep_not_null($this->file) && tep_not_null($this->destination)) {
			$this->set_output_messages('session');

			if ( ($this->parse() == true) && ($this->save() == true) ) {
				return true;
			} else {
				return false;
			}
		}
	}

	function parse() {
		global $messageStack;

		$file = array();

		if (is_array($this->file) && isset($this->file['name'])){
			$file = $this->file;
		}elseif (isset($_FILES[$this->file])) {
			$file = array(
				'name'     => $_FILES[$this->file]['name'],
				'type'     => $_FILES[$this->file]['type'],
				'size'     => $_FILES[$this->file]['size'],
				'tmp_name' => $_FILES[$this->file]['tmp_name']
			);
		}

		if ( tep_not_null($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']) ) {
			if (sizeof($this->extensions) > 0) {
				if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions)) {
					if ($this->message_location == 'direct') {
						$messageStack->add(sysLanguage::get('ERROR_FILETYPE_NOT_ALLOWED'), 'error');
					} else {
						$messageStack->addSession('footerStack', sysLanguage::get('ERROR_FILETYPE_NOT_ALLOWED'), 'error');
					}

					return false;
				}
			}

			$this->set_file($file);
			$this->set_filename($file['name']);
			$this->set_tmp_filename($file['tmp_name']);
			$this->set_file_size($file['size']);

			return $this->check_destination();
		} else {
			if ($this->message_location == 'direct') {
				//$messageStack->add(sysLanguage::get('WARNING_NO_FILE_UPLOADED'), 'warning');
			} else {
				$errorMsg = '<table cellpadding="3" cellspacing="0" border="0">
        	 <tr>
        	  <td class="main"><b><u>Server Message</u></b></td>
        	  <td class="main"><b><u>File Info</u></b></td>
        	 </tr>
        	 <tr>
        	  <td class="main">' . sysLanguage::get('WARNING_NO_FILE_UPLOADED') . '</td>
        	  <td class="main"><b>File Field Name:</b> ' . $this->file . '</td>
        	 </tr>
        	 <tr>
        	  <td><table cellpadding="3" cellspacing="0" border="0">
        	   <tr>
        	    <td class="main"><b>tmp_name is empty:</b></td>
        	    <td class="main">' . (empty($file['tmp_name']) ? 'True' : 'False') . '</td>
        	   </tr>
        	   <tr>
        	    <td class="main"><b>tmp_name == "none":</b></td>
        	    <td class="main">' . ($file['tmp_name'] == 'none' ? 'True' : 'False') . '</td>
        	   </tr>
        	   <tr>
        	    <td class="main"><b>tmp_name is uploaded:</b></td>
        	    <td class="main">' . (is_uploaded_file($file['tmp_name']) ? 'True' : 'False') . '</td>
        	   </tr>
        	  </table></td>
        	  <td><table cellpadding="3" cellspacing="0" border="0">
        	   <tr>
        	    <td class="main"><b>Name:</b></td>
        	    <td class="main">' . $file['name'] . '</td>
        	   </tr>
        	   <tr>
        	    <td class="main"><b>Type:</b></td>
        	    <td class="main">' . $file['type'] . '</td>
        	   </tr>
        	   <tr>
        	    <td class="main"><b>Size:</b></td>
        	    <td class="main">' . $file['size'] . '</td>
        	   </tr>
        	  </table></td>
        	 </tr>
        	</table>';
				$messageStack->addSession('footerStack', $errorMsg, 'warning');
			}

			return false;
		}
	}

	function save() {
		global $messageStack;

		if (substr($this->destination, -1) != '/') $this->destination .= '/';

		if (move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename)) {
			chmod($this->destination . $this->filename, $this->permissions);

			if ($this->message_location == 'direct') {
				$messageStack->add(sysLanguage::get('SUCCESS_FILE_SAVED_SUCCESSFULLY'), 'success');
			} else {
				$messageStack->addSession('footerStack', sysLanguage::get('SUCCESS_FILE_SAVED_SUCCESSFULLY'), 'success');
			}

			return true;
		} else {
			if ($this->message_location == 'direct') {
				//$messageStack->add(sysLanguage::get('ERROR_FILE_NOT_SAVED'), 'error');
			} else {
				$messageStack->addSession('footerStack', sysLanguage::get('ERROR_FILE_NOT_SAVED'), 'error');
			}

			return false;
		}
	}

	function set_file($file) {
		$this->file = $file;
	}

	function set_destination($destination) {
		$this->destination = $destination;
	}

	function set_permissions($permissions) {
		$this->permissions = octdec($permissions);
	}

	function set_filename($filename) {
		$this->filename = $filename;
	}

	function set_file_size($val) {
		$this->file_size = $val;
	}

	function set_tmp_filename($filename) {
		$this->tmp_filename = $filename;
	}

	function set_extensions($extensions) {
		if (tep_not_null($extensions)) {
			if (is_array($extensions)) {
				$this->extensions = $extensions;
			} else {
				$this->extensions = array($extensions);
			}
		} else {
			$this->extensions = array();
		}
	}

	function check_destination() {
		global $messageStack;
		if (!is_writeable($this->destination)) {
			if (is_dir($this->destination)) {
				if ($this->message_location == 'direct') {
					$messageStack->add(sprintf(sysLanguage::get('ERROR_DESTINATION_NOT_WRITEABLE'), $this->destination), 'error');
				} else {
					$messageStack->addSession('footerStack', sprintf(sysLanguage::get('ERROR_DESTINATION_NOT_WRITEABLE'), $this->destination), 'error');
				}
			} else {
				if ($this->message_location == 'direct') {
					$messageStack->add(sprintf(sysLanguage::get('ERROR_DESTINATION_DOES_NOT_EXIST'), $this->destination), 'error');
				} else {
					$messageStack->addSession('footerStack', sprintf(sysLanguage::get('ERROR_DESTINATION_DOES_NOT_EXIST'), $this->destination), 'error');
				}
			}

			return false;
		} else {
			return true;
		}
	}

	function set_output_messages($location) {
		switch ($location) {
			case 'session':
				$this->message_location = 'session';
				break;
			case 'direct':
			default:
				$this->message_location = 'direct';
				break;
		}
	}
}
?>