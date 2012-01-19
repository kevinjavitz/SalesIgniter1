<?php
class Installable extends MI_Importable
{

	public $installed = false;

	public function setInstalled($val){
		$this->installed = $val;
	}

	public function isInstalled(){
		return $this->installed;
	}

	public function onInstall(){

	}

	public function onUninstall(){

	}
}

