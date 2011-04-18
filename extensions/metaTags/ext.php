<?php

/**
 * @brief Handle Meta Tags
 *
 * @details
 * Add Meta tags into html header
 *
 * @author Erick Romero
 * @version 1
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */

class Extension_metaTags extends ExtensionBase {

	private $title			= '';
	private $description	= '';
	private $keywords		= '';

	private static $defaults_fetched;

	/**
	 * class constructor
	 * @public
	 * @return void
	 */
	public function __construct() {
		parent::__construct('metaTags');

		self::$defaults_fetched = 0;
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Initialize this class. (loaded by core)
	 *
	 * @public
	 * @return void
	 */
	public function init() {
		// global $App, $appExtension, $Template;
		if ($this->enabled === FALSE) return;

		EventManager::attachEvents(array(
			'PageLayoutHeaderTitle',
			'PageLayoutHeaderMetaDescription',
			'PageLayoutHeaderMetaKeyword'
		), null, $this);
	}

	// -------------------------------------------------------------------------------------------


	/**
	 * Return an array with all language and metatags for the specied page_type
	 * The array will have the next structure:
	 * array (
	 * 		language_1 = array (
	 * 			t = title
	 * 			d = description
	 * 			k = keyword
	 * 		),
	 *		language_2 = array (
	 * 			..
	 * 		),
	 * 		..
	 * )
	 *
	 * @public
	 * @param 	type_page		(char)	the type of page to fetch
	 * @param 	$lang_id		(int)	the language to fetch. -1 fetch all
	 * @see 	for values expected on "type_page" param, see MetaTags model, type_page column definition
	 *
	 * @return array
	 */
	public function fetchPageMetaTags($type_page, $lang_id = -1) {

		if ($lang_id == -1) {
			$Metatags = Doctrine_Query::create()
			->from('MetaTags m')
			->where('m.type_page = ?', $type_page)
			->execute(null, Doctrine_Core::HYDRATE_ARRAY);
		}
		else {
			$Metatags = Doctrine_Query::create()
			->from('MetaTags m')
			->where('m.type_page = ?', $type_page)
			->andWhere('m.language_id 	 = ?', $lang_id)
			->execute(null, Doctrine_Core::HYDRATE_ARRAY);
		}

		$metas_array = array();
		if ($Metatags) {
			foreach($Metatags as $row) {
				$metas_array[$row['language_id']] = array(
					't' => $row['title'],
					'd' => $row['description'],
					'k' => $row['keywords'],
					'i' => $row['metatags_id'],
				);
			}
		}

		return $metas_array;

	}

	// -------------------------------------------------------------------------------------------

	/**
	 * fetch the default values from the DB based in the language
	 *
	 * @private
	 * @return void
	 */
	private function fetchDefaults() {

		if (self::$defaults_fetched != 0) {
			//already fetched
			return;
		}

		$lID = intval(Session::get('languages_id'));
		if ( ! $lID ) $lID = 1; //global english

		$Metatags = Doctrine_Query::create()
		->from('MetaTags m')
		->where('m.language_id = ?', $lID)
		->andWhere('m.type_page = ?', 'D')
		->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

		if ($Metatags) {
			$this->title		= $Metatags['title'];
			$this->description	= $Metatags['description'];
			$this->keywords		= $Metatags['keywords'];
		}

		self::$defaults_fetched = 1;
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Listener for PageLayoutHeaderTitle event from Page_Layout template
	 *
	 * @param	param (string)		The meta tag
	 * @public
	 * @return void
	 */
	public function PageLayoutHeaderTitle(&$param) {
		$tmp = $this->processHeaderTag(1);
		if ($tmp != '') {
			$param = $tmp;
		}
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Listener for PageLayoutHeaderMetaDescription event from Page_Layout template
	 *
	 * @param	param (string)		The meta tag
	 * @public
	 * @return void
	 */
	public function PageLayoutHeaderMetaDescription(&$param) {
		$tmp = $this->processHeaderTag(2);
		if ($tmp != '') {
			//$param = substr($tmp, 0, 500);
			$param = $tmp;
		}
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Listener for PageLayoutHeaderMetaKeyword event from Page_Layout template
	 *
	 * @param	param (string)		The meta tag
	 * @public
	 * @return void
	 */
	public function PageLayoutHeaderMetaKeyword(&$param) {
		$tmp = $this->processHeaderTag(3);
		if ($tmp != '') {
			$param = $tmp;
		}
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Looks for the meta tag and returns it
	 *
	 * @private
	 * @param which	(int)		1=title | 2=description | 3=keyword
	 * @return string
	 */
	private function processHeaderTag($which) {

		if ( ! in_array($which, array(1,2,3))) {
			//invalid param
			return '';
		}

		//fetch the defaults

		$this->fetchDefaults();

		$tmp_meta 		= '';	//overwritten meta
		$glue			= '';	//to glue metatag's values
		$behaviour		= '';	//replace, append, prepend
		$default_meta	= '';	//default meta
		switch ($which) {
			case 1:
				$tmp_meta 		= EventManager::notifyWithReturn('HeaderTagsTitle');
				$glue			= ' - ';
				$behaviour		= sysConfig::get('EXTENSION_METATAGS_TITLE');
				$default_meta	= $this->title;
				break;
			case 2:
				$tmp_meta 		= EventManager::notifyWithReturn('HeaderTagsMetaDescription');
				$glue			= '. ';
				$behaviour		= sysConfig::get('EXTENSION_METATAGS_DESCRIPTION');
				$default_meta	= $this->description;
				break;
			case 3:
				$tmp_meta 		= EventManager::notifyWithReturn('HeaderTagsMetaKeywords');
				$glue			= ',';
				$behaviour		= sysConfig::get('EXTENSION_METATAGS_KEYWORDS');
				$default_meta	= $this->keywords;
				break;
		}

		//transform arrays to string

		if (is_array($tmp_meta)) {
			$tmp_meta = implode($glue, $tmp_meta);
		}

		//handle the behaviour for the metatag

		if (trim($tmp_meta) != '') {
			switch ($behaviour) {
				case 'Append':
					$default_meta = $default_meta . $glue . $tmp_meta;
					break;
				case 'Prepend':
					$default_meta = $tmp_meta . $glue . $default_meta;
					break;
				case 'Replace':
				default:
					$default_meta = $tmp_meta;
					break;
			}
		}

		//some clean up

		$default_meta 		= trim(strip_tags($default_meta));

		//finally, return the metatag's value

		return $default_meta;

	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Return an array with the form's elements to add metatags (title, description and keywords)
	 * The array to return have following structure; just use array[t]->draw() to output
	 *	array(
	 * 		't' => htmlBase::newElement('input')		//meta title
	 * 		'd' => htmlBase::newElement('textarea')		//meta description
	 * 		'k' => htmlBase::newElement('textarea')		//meta keyword
	 *	)
	 *
	 * @public
	 * @param	lang_id 	(int)		The language ID
	 * @param	values 		(array)		Array having the meta values (array('t'=>title,'d'=>description,'k'=>keyword,'i'=>metatag_id))
	 * @param	var_name 	(string)	The variable name to append to each form's element
	 *
	 * @return array
	 */
	public function createFormElements($lang_id = 0, $values = false, $var_name = 'metatags') {

		if (! $values) {
			$values = array('t'=>'','d'=>'','k'=>'', 'i'=>'');
		}

		$t	= htmlBase::newElement('input')
			->css(array('width' => '600px'))
			->setValue($values['t']);

		if ($lang_id == 0){
			$t->setName($var_name . '[t]');
		}else{
			$t->setName($var_name . '[' . $lang_id . '][t]');
		}

		$d	= htmlBase::newElement('textarea')
			->css(array('width' => '600px'))
			->attr('rows', 4)
			->html($values['d']);

		if ($lang_id == 0){
			$d->setName($var_name . '[d]');
		}else{
			$d->setName($var_name . '[' . $lang_id . '][d]');
		}

		$k	= htmlBase::newElement('textarea')
			->css(array('width' => '600px'))
			->attr('rows', 2)
			->html($values['k']);

		if ($lang_id == 0){
			$k->setName($var_name . '[k]');
		}else{
			$k->setName($var_name . '[' . $lang_id . '][k]');
		}

		$elements = array(
			't'	=> $t,
			'd'	=> $d,
			'k'	=> $k,
		);

		if (isset($values['i']) && $values['i'] != '') {
			if($lang_id == 0){
				$elements['i'] = sprintf(
					'<input name="%s[i]" value="%d" type="hidden" >',
					$var_name,
					$values['i']
				);
			}else{
				$elements['i'] = sprintf(
					'<input name="%s[%d][i]" value="%d" type="hidden" >',
					$var_name,
					$lang_id,
					$values['i']
				);
			}
		}
		else {
			$elements['i'] = '';
		}

		return $elements;
	}

}

?>
