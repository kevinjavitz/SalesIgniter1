<?php
/*
	Photo Gallery Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class Extension_photoGallery extends ExtensionBase {

		public function __construct() {
			global $App;
			parent::__construct('photoGallery');

			if($App->getEnv() == 'catalog' && isset($_GET['appExt']) && $_GET['appExt'] == 'photoGallery' && is_numeric($_GET['appPage'])){
				$_GET['catId'] = $_GET['appPage'];
				$App->setAppPage('show_category');
			}

		}

		public function init() {
			global $App, $appExtension, $Template, $blog_cPath, $blog_cPath_array, $current_blog_category_id;
			if ($this->enabled === false) return;

			EventManager::attachEvents(array(

			), null, $this);
		}

	}
?>