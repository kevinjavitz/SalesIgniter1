<?php


switch($_POST['type']){
	case 'close':
		$RentIssue = Doctrine_Core::getTable('RentIssues')->find($_POST['issue_id']);
		$RentIssue->status = 'C';
		$RentIssue->save();
		break;
	case 'new':
		$problemText = $_POST['feedback'];

		$productID = $_POST['products_id'];

		$QData = Doctrine_Query::create()
		->from('RentedProducts')
		->where('products_id = ?', $productID)
		->andWhere('customers_id = ?', $userAccount->getCustomerId())
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QProductName = Doctrine_Query::create()
		->from('ProductsDescription')
		->where('products_id = ?', $productID)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$customerId = $userAccount->getCustomerId();
		$customerName = $userAccount->getFirstName . ' '.$userAccount->getLastName();


		$RentIssue = new RentIssues();
		$RentIssue->parent_id = 0;
		$RentIssue->products_id = $productID;
		$RentIssue->products_name = $QProductName[0]['products_name'];
		$RentIssue->reported_date = date('Y-m-d H:i:s');
		$RentIssue->status = 'O';
		$RentIssue->customers_id = $customerId;
		$RentIssue->feedback = $problemText;
		$RentIssue->save();

		$emailText = issues_getEmailText('newIssue', array(
				sysConfig::get('STORE_OWNER'),
				$customerName,
				$QProductName[0]['products_name'],
				$QData[0]['shipment_date'],
				$problemText,
				$RentIssue->issue_id
		));
		tep_mail(sysConfig::get('STORE_OWNER'),sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'), sysLanguage::get('EMAIL_SUBJECT_REPORT'), $emailText, sysConfig::get('STORE_OWNER'), sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'));

		break;
	case 'reply':
		$problemText = $_POST['feedback'];

		$productID = $_POST['products_id'];

		$QData = Doctrine_Query::create()
		->from('RentedProducts')
		->where('products_id = ?', $productID)
		->andWhere('customers_id = ?', $userAccount->getCustomerId())
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$QProductName = Doctrine_Query::create()
		->from('ProductsDescription')
		->where('products_id = ?', $productID)
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$customerId = $userAccount->getCustomerId();
		$customerName = $userAccount->getFirstName . ' '.$userAccount->getLastName();

		$RentIssueB = Doctrine_Core::getTable('RentIssues')->find($_POST['issue_id']);
		$RentIssueB->status = 'O';
		$RentIssueB->save();

		$RentIssue = new RentIssues();
		$RentIssue->parent_id = $_POST['issue_id'];
		$RentIssue->products_id = $productID;
		$RentIssue->products_name = $QProductName[0]['products_name'];
		$RentIssue->reported_date = date('Y-m-d H:i:s');
		$RentIssue->status = 'O';
		$RentIssue->customers_id = $customerId;
		$RentIssue->feedback = $problemText;
		$RentIssue->save();

		$emailText = issues_getEmailText('replyIssue', array(
			sysConfig::get('STORE_OWNER'),
			$customerName,
			$QProductName[0]['products_name'],
			$QData[0]['shipment_date'],
			$problemText,
			$_POST['issue_id']
		));
		tep_mail(sysConfig::get('STORE_OWNER'),sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'), sysLanguage::get('EMAIL_SUBJECT_REPORT'), $emailText, sysConfig::get('STORE_OWNER'), sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'));
		break;
}
	tep_redirect(itw_app_link(null,'account','rental_issues'));

?>