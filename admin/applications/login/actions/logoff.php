<?php
  Session::remove('login_id');
  Session::remove('login_firstname');
  Session::remove('login_groups_id');
  
  EventManager::attachActionResponse(itw_app_link(null, 'login', 'default'), 'redirect');
?>