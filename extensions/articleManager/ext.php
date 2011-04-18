<?php
/*
	Articles Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class Extension_articleManager extends ExtensionBase {

		public function __construct(){
			parent::__construct('articleManager');
		}

		public function init(){
			global $App, $appExtension, $Template, $tPath, $tPath_array, $current_topic_id, $breadcrumb;
			if ($this->enabled === false) return;

			// Article Manager
			if (isset($_GET['tPath'])){
				$tPath = $_GET['tPath'];
			}else{
				$tPath = '';
			}

			if (tep_not_null($tPath)) {
				$tPath_array = tep_parse_topic_path($tPath);
				$tPath = implode('_', $tPath_array);
				$current_topic_id = $tPath_array[(sizeof($tPath_array)-1)];
			}else{
				$current_topic_id = 0;
			}

			if (isset($tPath_array)) {
				for($i=0, $n=sizeof($tPath_array); $i<$n; $i++) {
					$Qtopic = Doctrine_Query::create()
					->select('topics_name')
					->from('TopicsDescription')
					->where('topics_id = ?', (int) $tPath_array[$i])
					->andWhere('language_id = ?', Session::get('languages_id'))
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qtopic){
						$breadcrumb->add($Qtopic[0]['topics_name'], itw_app_link('appExt=articleManager&tPath=' . implode('_', array_slice($tPath_array, 0, ($i+1))), 'show', 'default'));
					}else{
						break;
					}
				}
			}
			if ($App->getAppPage() == 'default' && $App->getAppName() == 'show'){
				EventManager::attachEvents(array(
					'PageLayoutHeaderCustomMeta'
				), null, $this);
			}
		}

		public function PageLayoutHeaderCustomMeta(){
			if (sysConfig::get('EXTENSION_ARTICLE_MANAGER_SHOW_RSS_ON_BAR') == 'True'){
				echo '<link rel="alternate" type="application/rss+xml"
  					  title="'.sysConfig::get('STORE_NAME').' RSS" href="'.itw_app_link(tep_get_all_get_params().'appExt=articleManager','show','rss').'">';
			}
		}

		public function getModule($type){
			$modulePath = sysConfig::getDirFsCatalog() . 'extensions/articleManager/catalog/';
			switch($type){
				case 'upcoming':
					$modulePath .= 'contentModules/' . $type . '.php';
					break;
				case 'listing':
					$modulePath .= 'listingModules/' . strtolower(sysConfig::get('EXTENSION_ARTICLE_MANAGER_ARTICLE_LIST_MODULE')) . '.php';
					break;
			}
			return $modulePath;
		}
	}

/*
 * @TODO Remove these functions with something "prettier"
 */
// Parse and secure the tPath parameter values
  function tep_parse_topic_path($tPath) {
    // make sure the topic IDs are integers
    $tPath_array = array_map('tep_string_to_int', explode('_', $tPath));

// make sure no duplicate topic IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($tPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($tPath_array[$i], $tmp_array)) {
        $tmp_array[] = $tPath_array[$i];
      }
    }

    return $tmp_array;
  }

////
// Generate a path to topics
// TABLES: topics
  function tep_get_topic_path($current_topic_id = '') {
    global $tPath_array;

    if (tep_not_null($current_topic_id)) {
      $cp_size = sizeof($tPath_array);
      if ($cp_size == 0) {
        $tPath_new = $current_topic_id;
      } else {
        $tPath_new = '';
        $last_topic_query = tep_db_query("select parent_id from " . TABLE_TOPICS . " where topics_id = '" . (int)$tPath_array[($cp_size-1)] . "'");
        $last_topic = tep_db_fetch_array($last_topic_query);

        $current_topic_query = tep_db_query("select parent_id from " . TABLE_TOPICS . " where topics_id = '" . (int)$current_topic_id . "'");
        $current_topic = tep_db_fetch_array($current_topic_query);

        if ($last_topic['parent_id'] == $current_topic['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $tPath_new .= '_' . $tPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $tPath_new .= '_' . $tPath_array[$i];
          }
        }
        $tPath_new .= '_' . $current_topic_id;

        if (substr($tPath_new, 0, 1) == '_') {
          $tPath_new = substr($tPath_new, 1);
        }
      }
    } else {
      $tPath_new = implode('_', $tPath_array);
    }

    return 'tPath=' . $tPath_new;
  }

////
// Return the number of articles in a topic
// TABLES: articles, articles_to_topics, topics
  function tep_count_articles_in_topic($topic_id, $include_inactive = false) {
    $articles_count = 0;
    if ($include_inactive == true) {
      $articles_query = tep_db_query("SELECT COUNT(*) as total from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a2t.topics_id = '" . (int)$topic_id . "'");
    } else {
      $articles_query = tep_db_query("SELECT COUNT(*) as total from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t where (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now())) and a.articles_id = a2t.articles_id and a.articles_status = '1' and a2t.topics_id = '" . (int)$topic_id . "'");
    }
    $articles = tep_db_fetch_array($articles_query);
    $articles_count += $articles['total'];

    $child_topics_query = tep_db_query("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$topic_id . "'");
    if (tep_db_num_rows($child_topics_query)) {
      while ($child_topics = tep_db_fetch_array($child_topics_query)) {
        $articles_count += tep_count_articles_in_topic($child_topics['topics_id'], $include_inactive);
      }
    }

    return $articles_count;
  }

////
// Return true if the topic has subtopics
// TABLES: topics
  function tep_has_topic_subtopics($topic_id) {
    $child_topic_query = tep_db_query("SELECT COUNT(*) as count from " . TABLE_TOPICS . " where parent_id = '" . (int)$topic_id . "'");
    $child_topic = tep_db_fetch_array($child_topic_query);

    if ($child_topic['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

////
// Return all topics
// TABLES: topics, topic_descriptions
  function tep_get_topics($topics_array = '', $parent_id = '0', $indent = '') {
    if (!is_array($topics_array)) $topics_array = array();

    $topics_query = tep_db_query("select t.topics_id, td.topics_name from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where parent_id = '" . (int)$parent_id . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)Session::get('languages_id') . "' order by sort_order, td.topics_name");
    while ($topics = tep_db_fetch_array($topics_query)) {
      $topics_array[] = array('id' => $topics['topics_id'],
                                  'text' => $indent . $topics['topics_name']);

      if ($topics['topics_id'] != $parent_id) {
        $topics_array = tep_get_topics($topics_array, $topics['topics_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $topics_array;
  }

////
// Return all subtopic IDs
// TABLES: topics
  function tep_get_subtopics(&$subtopics_array, $parent_id = 0) {
    $subtopics_query = tep_db_query("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$parent_id . "'");
    while ($subtopics = tep_db_fetch_array($subtopics_query)) {
      $subtopics_array[sizeof($subtopics_array)] = $subtopics['topics_id'];
      if ($subtopics['topics_id'] != $parent_id) {
        tep_get_subtopics($subtopics_array, $subtopics['topics_id']);
      }
    }
  }

////
// Recursively go through the topics and retreive all parent topic IDs
// TABLES: topics
  function tep_get_parent_topics(&$topics, $topics_id) {
    $parent_topics_query = tep_db_query("select parent_id from " . TABLE_TOPICS . " where topics_id = '" . (int)$topics_id . "'");
    while ($parent_topics = tep_db_fetch_array($parent_topics_query)) {
      if ($parent_topics['parent_id'] == 0) return true;
      $topics[sizeof($topics)] = $parent_topics['parent_id'];
      if ($parent_topics['parent_id'] != $topics_id) {
        tep_get_parent_topics($topics, $parent_topics['parent_id']);
      }
    }
  }

////
// Construct a topic path to the article
// TABLES: articles_to_topics
  function tep_get_article_path($articles_id) {
    $tPath = '';

    $topic_query = tep_db_query("select a2t.topics_id from " . TABLE_ARTICLES . " a, " . TABLE_ARTICLES_TO_TOPICS . " a2t where a.articles_id = '" . (int)$articles_id . "' and a.articles_status = '1' and a.articles_id = a2t.articles_id limit 1");
    if (tep_db_num_rows($topic_query)) {
      $topic = tep_db_fetch_array($topic_query);

      $topics = array();
      tep_get_parent_topics($topics, $topic['topics_id']);

      $topics = array_reverse($topics);

      $tPath = implode('_', $topics);

      if (tep_not_null($tPath)) $tPath .= '_';
      $tPath .= $topic['topics_id'];
    }

    return $tPath;
  }

////
// Return an article's name
// TABLES: articles
  function tep_get_articles_name($article_id, $language = '') {
    if (empty($language)) $language = Session::get('languages_id');

    $article_query = tep_db_query("select articles_name from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language . "'");
    $article = tep_db_fetch_array($article_query);

    return $article['articles_name'];
  }

  function tep_get_topic_name($topic_id, $language_id) {
    $topic_query = tep_db_query("select topics_name from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . (int)$topic_id . "' and language_id = '" . (int)$language_id . "'");
    $topic = tep_db_fetch_array($topic_query);

    return $topic['topics_name'];
  }

  function tep_get_topic_tree($parent_id = '0', $spacing = '', $exclude = '', $topic_tree_array = '', $include_itself = false) {

    if (!is_array($topic_tree_array)) $topic_tree_array = array();
    if ( (sizeof($topic_tree_array) < 1) && ($exclude != '0') ) $topic_tree_array[] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));

    if ($include_itself) {
      $topic_query = tep_db_query("select cd.topics_name from " . TABLE_TOPICS_DESCRIPTION . " cd where cd.language_id = '" . (int)Session::get('languages_id') . "' and cd.topics_id = '" . (int)$parent_id . "'");
      $topic = tep_db_fetch_array($topic_query);
      $topic_tree_array[] = array('id' => $parent_id, 'text' => $topic['topics_name']);
    }

    $topics_query = tep_db_query("select c.topics_id, cd.topics_name, c.parent_id from " . TABLE_TOPICS . " c, " . TABLE_TOPICS_DESCRIPTION . " cd where c.topics_id = cd.topics_id and cd.language_id = '" . (int)Session::get('languages_id') . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.topics_name");
    while ($topics = tep_db_fetch_array($topics_query)) {
      if ($exclude != $topics['topics_id']) $topic_tree_array[] = array('id' => $topics['topics_id'], 'text' => $spacing . $topics['topics_name']);
      $topic_tree_array = tep_get_topic_tree($topics['topics_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $topic_tree_array);
    }

    return $topic_tree_array;
  }

  function tep_generate_topic_path($id, $from = 'topic', $topics_array = '', $index = 0) {
    if (!is_array($topics_array)) $topics_array = array();

    if ($from == 'article') {
      $topics_query = tep_db_query("select topics_id from " . TABLE_ARTICLES_TO_TOPICS . " where articles_id = '" . (int)$id . "'");
      while ($topics = tep_db_fetch_array($topics_query)) {
        if ($topics['topics_id'] == '0') {
          $topics_array[$index][] = array('id' => '0', 'text' => sysLanguage::get('TEXT_TOP'));
        } else {
          $topic_query = tep_db_query("select cd.topics_name, c.parent_id from " . TABLE_TOPICS . " c, " . TABLE_TOPICS_DESCRIPTION . " cd where c.topics_id = '" . (int)$topics['topics_id'] . "' and c.topics_id = cd.topics_id and cd.language_id = '" . (int)Session::get('languages_id') . "'");
          $topic = tep_db_fetch_array($topic_query);
          $topics_array[$index][] = array('id' => $topics['topics_id'], 'text' => $topic['topics_name']);
          if ( (tep_not_null($topic['parent_id'])) && ($topic['parent_id'] != '0') ) $topics_array = tep_generate_topic_path($topic['parent_id'], 'topic', $topics_array, $index);
          $topics_array[$index] = array_reverse($topics_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'topic') {
      $topic_query = tep_db_query("select cd.topics_name, c.parent_id from " . TABLE_TOPICS . " c, " . TABLE_TOPICS_DESCRIPTION . " cd where c.topics_id = '" . (int)$id . "' and c.topics_id = cd.topics_id and cd.language_id = '" . (int)Session::get('languages_id') . "'");
      $topic = tep_db_fetch_array($topic_query);
      $topics_array[$index][] = array('id' => $id, 'text' => $topic['topics_name']);
      if ( (tep_not_null($topic['parent_id'])) && ($topic['parent_id'] != '0') ) $topics_array = tep_generate_topic_path($topic['parent_id'], 'topic', $topics_array, $index);
    }

    return $topics_array;
  }

  function tep_output_generated_topic_path($id, $from = 'topic') {
    $calculated_topic_path_string = '';
    $calculated_topic_path = tep_generate_topic_path($id, $from);
    for ($i=0, $n=sizeof($calculated_topic_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_topic_path[$i]); $j<$k; $j++) {
        $calculated_topic_path_string .= $calculated_topic_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -16) . '<br>';
    }
    $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -4);

    if (strlen($calculated_topic_path_string) < 1) $calculated_topic_path_string = sysLanguage::get('TEXT_TOP');

    return $calculated_topic_path_string;
  }

  function tep_get_generated_topic_path_ids($id, $from = 'topic') {
    $calculated_topic_path_string = '';
    $calculated_topic_path = tep_generate_topic_path($id, $from);
    for ($i=0, $n=sizeof($calculated_topic_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_topic_path[$i]); $j<$k; $j++) {
        $calculated_topic_path_string .= $calculated_topic_path[$i][$j]['id'] . '_';
      }
      $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -1) . '<br>';
    }
    $calculated_topic_path_string = substr($calculated_topic_path_string, 0, -4);

    if (strlen($calculated_topic_path_string) < 1) $calculated_topic_path_string = sysLanguage::get('TEXT_TOP');

    return $calculated_topic_path_string;
  }

////
// Sets the status of an article
  function tep_set_article_status($articles_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_ARTICLES . " set articles_status = '1', articles_last_modified = now() where articles_id = '" . (int)$articles_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_ARTICLES . " set articles_status = '0', articles_last_modified = now() where articles_id = '" . (int)$articles_id . "'");
    } else {
      return -1;
    }
  }

  function tep_get_articles_head_title_tag($article_id, $language_id = 0) {
    if ($language_id == 0) $language_id = Session::get('languages_id');
    $article_query = tep_db_query("select articles_head_title_tag from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = tep_db_fetch_array($article_query);

    return $article['articles_head_title_tag'];
  }

  function tep_get_articles_description($article_id, $language_id) {
    $article_query = tep_db_query("select articles_description from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = tep_db_fetch_array($article_query);

    return $article['articles_description'];
  }

  function tep_get_articles_head_desc_tag($article_id, $language_id) {
    $article_query = tep_db_query("select articles_head_desc_tag from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = tep_db_fetch_array($article_query);

    return $article['articles_head_desc_tag'];
  }

  function tep_get_articles_head_keywords_tag($article_id, $language_id) {
    $article_query = tep_db_query("select articles_head_keywords_tag from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = tep_db_fetch_array($article_query);

    return $article['articles_head_keywords_tag'];
  }

  function tep_get_articles_url($article_id, $language_id) {
    $article_query = tep_db_query("select articles_url from " . TABLE_ARTICLES_DESCRIPTION . " where articles_id = '" . (int)$article_id . "' and language_id = '" . (int)$language_id . "'");
    $article = tep_db_fetch_array($article_query);

    return $article['articles_url'];
  }


////
// Count how many articles exist in a topic
// TABLES: articles, articles_to_topics, topics
  function tep_articles_in_topic_count($topics_id, $include_deactivated = false) {
    $articles_count = 0;

    if ($include_deactivated) {
      $articles_query = tep_db_query("select count(*) as total from " . TABLE_ARTICLES . " p, " . TABLE_ARTICLES_TO_TOPICS . " p2c where p.articles_id = p2c.articles_id and p2c.topics_id = '" . (int)$topics_id . "'");
    } else {
      $articles_query = tep_db_query("select count(*) as total from " . TABLE_ARTICLES . " p, " . TABLE_ARTICLES_TO_TOPICS . " p2c where p.articles_id = p2c.articles_id and p.articles_status = '1' and p2c.topics_id = '" . (int)$topics_id . "'");
    }

    $articles = tep_db_fetch_array($articles_query);

    $articles_count += $articles['total'];

    $childs_query = tep_db_query("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$topics_id . "'");
    if (tep_db_num_rows($childs_query)) {
      while ($childs = tep_db_fetch_array($childs_query)) {
        $articles_count += tep_articles_in_topic_count($childs['topics_id'], $include_deactivated);
      }
    }

    return $articles_count;
  }

////
// Count how many subtopics exist in a topic
// TABLES: topics
  function tep_childs_in_topic_count($topics_id) {
    $topics_count = 0;

    $topics_query = tep_db_query("select topics_id from " . TABLE_TOPICS . " where parent_id = '" . (int)$topics_id . "'");
    while ($topics = tep_db_fetch_array($topics_query)) {
      $topics_count++;
      $topics_count += tep_childs_in_topic_count($topics['topics_id']);
    }

    return $topics_count;
  }

// Topics Description contribution
  function tep_get_topic_heading_title($topic_id, $language_id) {
    $topic_query = tep_db_query("select topics_heading_title from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . $topic_id . "' and language_id = '" . $language_id . "'");
    $topic = tep_db_fetch_array($topic_query);
    return $topic['topics_heading_title'];
  }

  function tep_get_topic_description($topic_id, $language_id) {
    $topic_query = tep_db_query("select topics_description from " . TABLE_TOPICS_DESCRIPTION . " where topics_id = '" . $topic_id . "' and language_id = '" . $language_id . "'");
    $topic = tep_db_fetch_array($topic_query);
    return $topic['topics_description'];
  }

	function tep_get_topic_tree_list($parent_id = '0', $checked = false, $include_itself = true) {
		$catList = '';
		if (tep_childs_in_topic_count($parent_id) > 0){
			if (!is_array($checked)){
				$checked = array();
			}
			$catList = '<ul class="topicListingUL">';

			if ($parent_id == '0'){
				$Qtopic = Doctrine_Query::create()
				->select('topics_name')
				->from('TopicsDescription')
				->where('language_id = ?', (int)Session::get('languages_id'))
				->andWhere('topics_id = ?', (int)$parent_id)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qtopic){
					$catList .= '<li>' . tep_draw_checkbox_field('topics[]', $parent_id, (in_array($parent_id, $checked)), 'id="topicCheckbox_' . $parent_id . '"') . '<label for="topicCheckbox_' . $parent_id . '">' . $Qtopic[0]['topics_name'] . '</label></li>';
				}
			}

			$Qtopics = Doctrine_Query::create()
			->select('t.topics_id, td.topics_name, t.parent_id')
			->from('Topics t')
			->leftJoin('t.TopicsDescription td')
			->where('td.language_id = ?', (int)Session::get('languages_id'))
			->andWhere('t.parent_id = ?', (int)$parent_id)
			->orderBy('t.sort_order, td.topics_name')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qtopics){
				foreach($Qtopics as $topic) {
					$catList .= '<li>' . tep_draw_checkbox_field('topics[]', $topic['topics_id'], (in_array($topic['topics_id'], $checked)), 'id="topicCheckbox_' . $topic['topics_id'] . '"') . '<label for="topicCheckbox_' . $topic['topics_id'] . '">' . $topic['TopicsDescription'][0]['topics_name'] . '</label></li>';
					if (tep_childs_in_topic_count($topic['topics_id']) > 0){
						$catList .= '<li class="subTopicContainer">' . tep_get_topic_tree_list($topic['topics_id'], $checked, false) . '</li>';
					}
				}
			}
			$catList .= '</ul>';
		}

		return $catList;
	}
?>