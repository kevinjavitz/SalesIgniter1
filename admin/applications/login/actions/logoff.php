<?php
  Session::remove('login_id');
  Session::remove('login_master');
  Session::remove('login_firstname');
  Session::remove('login_groups_id');
  Session::remove('admin_showing_stores');//to be moved into extension
  Session::remove('admin_allowed_stores');//to be moved into extension

  EventManager::attachActionResponse(itw_app_link(null, 'login', 'default'), 'redirect');
?>