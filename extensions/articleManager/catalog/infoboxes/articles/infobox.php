<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxArticles extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('articles', 'articleManager');

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_ARTICLES'));
		if ($App->getEnv() == 'catalog'){
			$this->setBoxHeadingLink(itw_app_link('appExt=articleManager'));
		}
	}

	public function show(){
		global $App, $tPath, $tPath_array;
		
		$this->topics_string = '';
		$first_topic_element = 0;
		$this->tree = array();

		$topics_query = tep_db_query("select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '0' and t.topics_id = td.topics_id and td.language_id = '" . (int)Session::get('languages_id') . "' order by sort_order, td.topics_name");
		while ($topics = tep_db_fetch_array($topics_query))  {
			$this->tree[$topics['topics_id']] = array('name' => $topics['topics_name'],
			'parent' => $topics['parent_id'],
			'level' => 0,
			'path' => $topics['topics_id'],
			'next_id' => false);

			if (isset($parent_id)) {
				$this->tree[$parent_id]['next_id'] = $topics['topics_id'];
			}

			$parent_id = $topics['topics_id'];

			if ($first_topic_element == 0) {
				$first_topic_element = $topics['topics_id'];
			}
		}

		//------------------------
		if (isset($tPath) && tep_not_null($tPath)) {
			$new_path = '';
			reset($tPath_array);
			while (list($key, $value) = each($tPath_array)) {
				unset($parent_id);
				unset($first_id);
				$topics_query = tep_db_query("select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '" . (int)$value . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)Session::get('languages_id') . "' order by sort_order, td.topics_name");
				if (tep_db_num_rows($topics_query)) {
					$new_path .= $value;
					while ($row = tep_db_fetch_array($topics_query)) {
						$this->tree[$row['topics_id']] = array('name' => $row['topics_name'],
						'parent' => $row['parent_id'],
						'level' => $key+1,
						'path' => $new_path . '_' . $row['topics_id'],
						'next_id' => false);

						if (isset($parent_id)) {
							$this->tree[$parent_id]['next_id'] = $row['topics_id'];
						}

						$parent_id = $row['topics_id'];

						if (!isset($first_id)) {
							$first_id = $row['topics_id'];
						}

						$last_id = $row['topics_id'];
					}
					$this->tree[$last_id]['next_id'] = $this->tree[$value]['next_id'];
					$this->tree[$value]['next_id'] = $first_id;
					$new_path .= '_';
				} else {
					break;
				}
			}
		}
		$this->show_topic($first_topic_element);

		$new_articles_string = '';
		$all_articles_string = '';

		if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_NEW_ARTICLES') == 'True') {
			if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_SHOW_ARTICLE_COUNTS') == 'True') {
				$articles_new_query = tep_db_query("SELECT a.articles_id from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t, " . TABLE_TOPICS_DESCRIPTION . " td, " . TABLE_ARTICLES_DESCRIPTION . " ad where a2t.topics_id = td.topics_id and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a.articles_status = '1' and a.articles_id = ad.articles_id and ad.language_id = '" . (int)Session::get('languages_id') . "' and td.language_id = '" . (int)Session::get('languages_id') . "' and a.articles_date_added > SUBDATE(now( ), INTERVAL '" . sysConfig::get('EXTENSION_ARTICLE_MANAGER_NEW_ARTICLES_DAYS_DISPLAY') . "' DAY)");
				$articles_new_count = '';
			}

			if ($App->getPageName() == 'new') {
				$new_articles_string = '<b>';
			}

			$new_articles_string .= '<a href="' . itw_app_link('appExt=articleManager', 'show', 'new') . '">' . sysLanguage::get('INFOBOX_ARTICLES_NEW_ARTICLES') . '</a>';

			if ($App->getPageName() == 'new') {
				$new_articles_string .= '</b>';
			}

			$new_articles_string .= $articles_new_count . '<br />';

		}

		if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_DISPLAY_ALL_ARTICLES') == 'True') {
			if (isset($topic_depth) && $topic_depth == 'top') {
				$all_articles_string = '<b>';
			}

			$all_articles_string .= '<a href="' . itw_app_link('appExt=articleManager', 'show', 'default') . '">' . sysLanguage::get('INFOBOX_ARTICLES_ALL_ARTICLES') . '</a>';

			if (isset($topic_depth) && $topic_depth == 'top') {
				$all_articles_string .= '</b>';
			}

			//$all_articles_string .= $articles_all_count . '<br />';
			$all_articles_string .= '<br />';
		}

		$this->setBoxContent($new_articles_string . $all_articles_string . $this->topics_string);

		return $this->draw();
	}

	public function show_topic($counter){
		global $tPath_array;

		for ($i=0; $i<$this->tree[$counter]['level']; $i++) {
			$this->topics_string .= "&nbsp;&nbsp;";
		}

		$this->topics_string .= '<a href="';

		if ($this->tree[$counter]['parent'] == 0) {
			$tPath_new = 'tPath=' . $counter;
		} else {
			$tPath_new = 'tPath=' . $this->tree[$counter]['path'];
		}

		$this->topics_string .= itw_app_link('appExt=articleManager&' . $tPath_new, 'show', 'default') . '">';

		if (isset($tPath_array) && in_array($counter, $tPath_array)) {
			$this->topics_string .= '<b>';
		}

		// display topic name
		$this->topics_string .= $this->tree[$counter]['name'];

		if (isset($tPath_array) && in_array($counter, $tPath_array)) {
			$this->topics_string .= '</b>';
		}

		if (tep_has_topic_subtopics($counter)) {
			$this->topics_string .= ' -&gt;';
		}


		if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_SHOW_ARTICLE_COUNTS') == 'True') {
			$articles_in_topic = tep_count_articles_in_topic($counter);
			if ($articles_in_topic > 0) {
				$this->topics_string .= '&nbsp;(' . $articles_in_topic . ')';
			}
		}

		$this->topics_string .= '</a>';

		$this->topics_string .= '<br>';

		if ($this->tree[$counter]['next_id'] != false) {
			$this->show_topic($this->tree[$counter]['next_id']);
		}
	}
}
?>