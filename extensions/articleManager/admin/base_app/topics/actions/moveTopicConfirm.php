<?php
        if (isset($_POST['topics_id']) && ($_POST['topics_id'] != $_POST['move_to_topic_id'])) {
          $topics_id = tep_db_prepare_input($_POST['topics_id']);
          $new_parent_id = tep_db_prepare_input($_POST['move_to_topic_id']);

          $path = explode('_', tep_get_generated_topic_path_ids($new_parent_id));

          if (in_array($topics_id, $path)) {
            $messageStack->add_session(sysLanguage::get('ERROR_CANNOT_MOVE_TOPIC_TO_PARENT'), 'error');

            tep_redirect(tep_href_link(FILENAME_ARTICLES, 'tPath=' . $tPath . '&tID=' . $topics_id));
          } else {
            tep_db_query("update " . TABLE_TOPICS . " set parent_id = '" . (int)$new_parent_id . "', last_modified = now() where topics_id = '" . (int)$topics_id . "'");

            if (USE_CACHE == 'true') {
              tep_reset_cache_block('topics');
            }

            tep_redirect(tep_href_link(FILENAME_ARTICLES, 'tPath=' . $new_parent_id . '&tID=' . $topics_id));
          }
        }
?>