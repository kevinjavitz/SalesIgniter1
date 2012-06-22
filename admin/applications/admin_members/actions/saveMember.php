<?php
	$Qcheck = Doctrine_Query::create()
	->select('admin_email_address')
	->from('Admin')
	->where('admin_email_address = ?', $_POST['admin_email_address']);
	if (isset($_GET['aID'])){
		$Qcheck->andWhere('admin_id != ?', (int)$_GET['aID']);
	}
	$Result = $Qcheck->execute();

	$errorMsg = '';
	if ($Result === false || !isset($_GET['aID']) && isset($Result[0]) ){
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_ERROR'), 'error');
		$errorMsg = sysLanguage::get('TEXT_INFO_ERROR');
	}else{
		if (!isset($_GET['aID']) && empty($_POST['admin_password'])){
			function randomize() {
				$salt = "abchefghjkmnpqrstuvwxyz0123456789";
				srand((double)microtime()*1000000);
				$i = 0;
				while ($i <= 7) {
					$num = rand() % 33;
					$tmp = substr($salt, $num, 1);
					$pass = $pass . $tmp;
					$i++;
				}
				return $pass;
			}
			$makePassword = randomize();
		}
		if(!empty($_POST['admin_password'])){
			$makePassword = $_POST['admin_password'];
		}
		
		$Admin = Doctrine_Core::getTable('Admin');
		if (isset($_GET['aID'])){
			$adminAccount = $Admin->findOneByAdminId((int)$_GET['aID']);
		}else{
			$adminAccount = $Admin->create();
		}

		$adminAccount->admin_password = tep_encrypt_password($makePassword);
		
		$adminAccount->admin_override_password = (!empty($_POST['admin_override_password']) ? $_POST['admin_override_password'] : '');
		$adminAccount->admin_groups_id = $_POST['admin_groups_id'];
		$adminAccount->admin_firstname = $_POST['admin_firstname'];
		$adminAccount->admin_lastname = $_POST['admin_lastname'];
		$adminAccount->admin_email_address = $_POST['admin_email_address'];
		$adminAccount->admin_simple_admin = isset($_POST['simple_admin'])?1:0;
		if($_POST['admin_favorites_id'] == '0'){
			$adminAccount->favorites_links = str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link(null,'categories','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link(null,'products','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link('appExt=infoPages','manage','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link('appExt=payPerRentals','reservations_reports','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link('appExt=payPerRentals','return','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link('appExt=payPerRentals','send','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link('appExt=blog','blog_posts','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link(null,'label_maker','default')) . ';'.
											 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),'',itw_app_link(null,'rental_queue','default'));

			$adminAccount->favorites_names = 'Categories;'.
											 'Products;'.
											 'Manage Pages;'.
											 'Reservation Reports;'.
											 'Return Reservation Rentals;'.
											 'Send Reservation Rentals;'.
											 'Manage Blog Posts;'.
											 'Label Maker;'.
											 'Rental Queue;';
		}else{
			$AdminFavs = Doctrine_Core::getTable('AdminFavorites')->find($_POST['admin_favorites_id']);
			$adminAccount->favorites_links = $AdminFavs->favorites_links;
			$adminAccount->favorites_names = $AdminFavs->favorites_names;
			$adminAccount->admin_favs_id = $AdminFavs->admin_favs_id;
		}
								
		$adminAccount->save();

		if (isset($_GET['aID'])){
			$subject = sysLanguage::get('ADMIN_EMAIL_EDIT_SUBJECT');
			$string = sysLanguage::get('ADMIN_EMAIL_EDIT_TEXT');
			$passText = '--hidden--';
		}else{
			$subject = sysLanguage::get('ADMIN_EMAIL_SUBJECT');
			$string = sysLanguage::get('ADMIN_EMAIL_TEXT');
			$passText = $makePassword;
		}
		
		tep_mail(
			$adminAccount->admin_firstname . ' ' . $adminAccount->admin_lastname,
			$adminAccount->admin_email_address,
			$subject,
			sprintf(
				str_replace('\n', "\n", $string),
				$adminAccount->admin_firstname,
				sysConfig::get('HTTP_SERVER') . sysConfig::getDirWsAdmin(),
				$adminAccount->admin_email_address,
				$passText,
				sysConfig::get('STORE_OWNER')
			),
			sysConfig::get('STORE_OWNER'),
			sysConfig::get('STORE_OWNER_EMAIL_ADDRESS')
		);

		$link = itw_app_link(tep_get_all_get_params(array('aID', 'action')) . 'aID=' . $adminAccount->admin_id);
	}

	EventManager::attachActionResponse(array(
			'success' => true,
			'errorMsg' => $errorMsg
		), 'json');
?>