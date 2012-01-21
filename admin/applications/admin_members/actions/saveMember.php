<?php
	$Qcheck = Doctrine_Query::create()
	->select('admin_email_address')
	->from('Admin')
	->where('admin_email_address = ?', $_POST['admin_email_address']);
	if (isset($_GET['mID'])){
		$Qcheck->andWhere('admin_id != ?', (int)$_GET['mID']);
	}
	$Result = $Qcheck->execute();
	if ($Result === false){
		$messageStack->addSession('pageStack', sysLanguage::get('TEXT_INFO_ERROR'), 'error');
		$link = itw_app_link(tep_get_all_get_params(array('action')) . 'action=new_member');
	}else{
		if (!isset($_GET['mID'])){
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
		
		$Admin = Doctrine_Core::getTable('Admin');
		if (isset($_GET['mID'])){
			$adminAccount = $Admin->findOneByAdminId((int)$_GET['mID']);
		}else{
			$adminAccount = $Admin->create();
			$adminAccount->admin_password = tep_encrypt_password($makePassword);
		}
		
		$adminAccount->admin_groups_id = $_POST['admin_groups_id'];
		$adminAccount->admin_firstname = $_POST['admin_firstname'];
		$adminAccount->admin_lastname = $_POST['admin_lastname'];
		$adminAccount->admin_email_address = $_POST['admin_email_address'];
		$adminAccount->favorites_links = str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link(null,'categories','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link(null,'products','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link('appExt=infoPages','manage','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link('appExt=payPerRentals','reservations_reports','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link('appExt=payPerRentals','return','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link('appExt=payPerRentals','send','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link('appExt=blog','blog_posts','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link(null,'label_maker','default')) . ';'.
										 str_replace( sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_ADMIN'),'',itw_app_link(null,'rental_queue','default'));

		$adminAccount->favorites_names = 'Categories;'.
										 'Products;'.
										 'Manage Pages;'.
										 'Reservation Reports;'.
										 'Return Reservation Rentals;'.
										 'Send Reservation Rentals;'.
										 'Manage Blog Posts;'.
										 'Label Maker;'.
										 'Rental Queue;';
								
		$adminAccount->save();

		if (isset($_GET['mID'])){
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
				HTTP_SERVER . DIR_WS_ADMIN,
				$adminAccount->admin_email_address,
				$passText,
				STORE_OWNER
			),
			STORE_OWNER,
			STORE_OWNER_EMAIL_ADDRESS
		);

		$link = itw_app_link(tep_get_all_get_params(array('mID', 'action')) . 'mID=' . $adminAccount->admin_id);
	}
	EventManager::attachActionResponse($link, 'redirect');
?>