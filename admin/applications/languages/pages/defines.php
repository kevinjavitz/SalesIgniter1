<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

function iterateLanguageArray($array) {
	$return = '';
	foreach($array as $key => $file){
		if (is_array($file)){
			ksort($file);
			/*
							 * Directory
							 */
			$return .= '<li>' .
				'<span class="ui-icon ui-icon-plusthick" style="vertical-align:middle;"></span>' .
				'<span style="vertical-align:middle;">' .
				$key .
				'</span>' .
				'<ul style="list-style:none;margin:0;padding:0;margin-left:1em;display:none;">' .
				iterateLanguageArray($file) .
				'</ul>' .
				'</li>';
		}
		elseif ($key == 'language_defines' || $key == 'global') {
			/*
							 * Core definition
							 */
			$return .= '<li>' .
				'<span class="ui-icon ui-icon-pencil" style="vertical-align:middle;" data-file_path="' . $file . '"></span>' .
				'<span style="vertical-align:middle;">' .
				'Global Defines' .
				'</span>' .
				'</li>';
		}
		else {
			/*
							 * User definition
							 */
			$return .= '<li>' .
				'<span class="ui-icon ui-icon-plusthick" style="vertical-align:middle;"></span>' .
				'<span style="vertical-align:middle;">' .
				$key .
				'</span>' .
				'<ul style="list-style:none;margin:0;padding:0;margin-left:2em;display:none;">' .
				'<li>' .
				'<span class="ui-icon ui-icon-pencil" style="vertical-align:middle;" data-file_path="' . $file . '"></span>' .
				'<span style="vertical-align:middle;">' .
				basename($file) .
				'</span>' .
				'</li>' .
				'</ul>' .
				'</li>';
		}
	}
	return $return;
}

function iterateLanguageDirectory($dir, $exclude = null) {
	$Directory = new RecursiveDirectoryIterator($dir);
	$Iterator = new RecursiveIteratorIterator($Directory);
	$Regex = new RegexIterator($Iterator, '/^.+global\.xml$/i', RegexIterator::GET_MATCH);

	$extPaths = array();
	foreach($Regex as $arr){
		$skipFile = false;
		if (is_null($exclude) === false){
			foreach($exclude as $excludeDir){
				if (stristr($arr[0], $excludeDir)){
					$skipFile = true;
					break;
				}
			}
		}

		if ($skipFile === false){
			$path = explode('/', str_replace($dir, '', $arr[0]));
			$evalString = '';
			for($i = 0, $n = sizeof($path); $i < $n; $i++){
				if (empty($path[$i])) {
					continue;
				}
				if ($path[$i] == 'language_defines') {
					continue;
				}

				if (stristr($path[$i], '.xml')){
					$evalString .= '[\'' . substr($path[$i], 0, -4) . '\']';
				}
				else {
					$evalString .= '[\'' . $path[$i] . '\']';
				}
			}
			eval('$extPaths' . $evalString . ' = \'' . str_replace(sysConfig::getDirFsCatalog(), '', $arr[0]) . '\';');
		}
	}
	ksort($extPaths);

	$return = '';
	foreach($extPaths as $extName => $pInfo){
		ksort($pInfo);
		$return .= '<li>' .
			'<span class="ui-icon ui-icon-plusthick" style="vertical-align:middle;"></span>' .
			'<span style="vertical-align:middle;">' .
			$extName .
			'</span>' .
			'<ul style="list-style:none;margin:0;padding:0;margin-left:1em;display:none;">' .
			iterateLanguageArray($pInfo) .
			'</ul>' .
			'</li>';
	}

	return $return;
}

?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script>
	google.load("language", "1");
</script>
<?php

$fromLangDrop = htmlBase::newElement('selectbox')->setName('fromLanguage');
$fromLangDrop->addOption('auto', 'Detect language');
$fromLangDrop->selectOptionByValue((isset($_GET['langCode']) ? $_GET['langCode'] : sysLanguage::getCode()));
$toLangDrop = htmlBase::newElement('selectbox')->setName('toLanguage');
foreach(sysLanguage::getGoogleLanguages() as $code => $lang){
	$fromLangDrop->addOption($code, $lang);
	$toLangDrop->addOption($code, $lang);
}
?>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td colspan="2">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td>
						<table cellpadding="3" cellspacing="0" border="0" width="100%">
							<tr>
								<td class="main" width="100">Search:</td>
								<td><input type="text" style="width:100%" id="searchBox"></td>
								<td width="125"><?php echo htmlBase::newElement('button')->setId('searchButton')
									->setText('Search Files')->draw();?></td>
							</tr>
							<tr>
								<td class="main">Search In:</td>
								<td class="main" colspan="2"><?php
									echo '<input type="radio" name="filter_files" value="all" checked="checked">All Files ';
									echo '<input type="radio" name="filter_files" value="core">Core Files ';
									echo '<input type="radio" name="filter_files" value="user">User Files ';
									?></td>
							</tr>
							<tr>
								<td class="main">Search Language:</td>
								<td class="main" colspan="2"><?php
									foreach(sysLanguage::getLanguages() as $lInfo){
										echo '<input type="checkbox" name="filter_lang[]" value="' . $lInfo['directory'] . '"' . ($lInfo['id'] == Session::get('languages_id') ? ' checked="checked"' : '') . '>' . $lInfo['name'] . ' ';
									}
									?></td>
							</tr>
						</table>
					</td>
					<td class="main" width="250" align="right"><?php
						echo 'From: ' . $fromLangDrop->draw() . '<br>' .
							'To: ' . $toLangDrop->draw() . '<br>' .
							htmlBase::newElement('button')->setId('googleTranslate')->setText('Translate Using Google')
								->draw() . '<br>' .
							'<div id="googleBrand"></div>';
						?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width="20%" valign="top">
			<h3><u>User Definitions</u></h3>

			<ul style="list-style:none;margin:0;padding:0;"><?php
				echo iterateLanguageDirectory(sysConfig::getDirFsCatalog() . 'includes/languages/');
				?></ul>
			<h3><u>Core Definitions</u></h3>
			<h4>Admin</h4>

			<ul style="list-style:none;margin:0;padding:0;"><?php
				echo iterateLanguageDirectory(sysConfig::getDirFsAdmin());
				?></ul>
			<h4>Catalog</h4>

			<ul style="list-style:none;margin:0;padding:0;"><?php
				echo iterateLanguageDirectory(sysConfig::getDirFsCatalog(), array(
					sysConfig::getDirFsAdmin(),
					sysConfig::getDirFsCatalog() . 'includes/languages/',
					sysConfig::getDirFsCatalog() . 'includes/languages_phar/'
				));
				?></ul>
		</td>
		<td width="80%" valign="top" class="editWindow"></td>
	</tr>
</table>