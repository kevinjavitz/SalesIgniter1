<?php
putenv('SHELL=/bin/bash');
putenv('TERM=vt100');
set_time_limit(0);

require('includes/application_top.php');

define('CRON_BILL_METHOD', 'original'); /* Options: original, current */

include(sysConfig::getDirFsCatalog() . 'includes/classes/order.php');

include(sysConfig::getDirFsCatalog() . 'includes/classes/membership_update.php');
$membershipUpdate = new membershipUpdate_cron();

if (sysConfig::get('RENTAL_UPGRADE_CYCLE') == 'true'){
	$Qupdates = Doctrine_Query::create()
		->select('upgrade_date, customers_id, plan_id')
		->from('MembershipUpdate')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qupdates as $uInfo){
		if ($membershipUpdate->timeToBill($uInfo['upgrade_date']) === true){
			$userAccount = new rentalStoreUser($uInfo['customers_id']);
			$userAccount->loadPlugins();
			$membershipUpdate->setCurrentCustomer($userAccount);
			$membershipUpdate->setPlan($uInfo['plan_id']);
		}
	}
}

  if (sysConfig::get('RENTAL_UPGRADE_BILL_DATE') == 'true'){
  	$Qcustomer = Doctrine_Query::create()
  	->select('c.customers_id, cm.next_bill_date as billDate, cm.free_trial_flag as isTrial, cm.free_trial_ends as trialEnds')
  	->from('Customers c')
  	->leftJoin('c.CustomersMembership cm')
  	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


      foreach($Qcustomer as $cInfo){
          if ($cInfo['isTrial'] == 'Y'){
              if ($membershipUpdate->timeToBill($cInfo['trialEnds']) === false){
                  continue;
              }else{
                  $membershipUpdate->setAction('trial');
              }
          }elseif ($membershipUpdate->timeToBill($cInfo['billDate']) === true){
              $membershipUpdate->setAction('initial');
          }elseif ($membershipUpdate->needsRetry($cInfo['customers_id']) === true){
              $membershipUpdate->setAction('retry');
          }else{
              continue;
          }
          
          $userAccount = new rentalStoreUser($cInfo['customers_id']);
          $userAccount->loadPlugins();
          $membershipUpdate->setCurrentCustomer($userAccount);
          if ($membershipUpdate->isCanceled() === true){
              $membershipUpdate->cancelMembership();
          }elseif($membershipUpdate->isMember() && ($membershipUpdate->isActivated() || $membershipUpdate->isRetry())){

              $paymentMethod = $membershipUpdate->paymentMethod();
              $Module = OrderPaymentModules::getModule($paymentMethod);
              $membershipUpdate->setPaymentObj($Module);

              $orderId = $membershipUpdate->insertOrder();
              if (is_numeric($orderId) === true){
                  if ($membershipUpdate->processPayment($orderId) === true){
                      $membershipUpdate->updateCustomersNextBillDate();
                      $membershipUpdate->updateStreamingAccess();
                      if ($membershipUpdate->isFromTrial() === true){
                          $membershipUpdate->concludeFreeTrial();
                      }
                  }
              }
          }
      }
      $membershipUpdate->sendAdminEmail();
  }
  
  if ($messageStack->size('footerStack') > 0){
      echo $messageStack->output('footerStack');
  }

require('includes/application_bottom.php');
?>