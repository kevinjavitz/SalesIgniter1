<?php
/*
	Photo Gallery Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class Extension_eventManager extends ExtensionBase {

		public function __construct() {
			parent::__construct('eventManager');
		}

		public function init() {
			global $App, $appExtension, $Template, $blog_cPath, $blog_cPath_array, $current_blog_category_id;
			if ($this->enabled === false) return;

			EventManager::attachEvents(array(

			), null, $this);
		}

	}
?>