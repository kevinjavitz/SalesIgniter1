<?php
/* One Page Checkout - BEGIN */
  if (Session::exists('account_action') === false || Session::get('account_action') != 'upgrade'){
      Session::set('newRentAccount', true);
      Session::set('shipping', false);
      Session::set('payment', false);
      if ($userAccount->isLoggedIn() === true){
          Session::set('billto', Session::get('customer_default_address_id'));
          Session::set('sendto', Session::get('customer_default_address_id'));
      }else{
          Session::set('billto', false);
          Session::set('sendto', false);
      }
      tep_redirect(itw_app_link(tep_get_all_get_params(), 'checkout', 'default'));
  }
/* One Page Checkout - END */
  if (isset($_POST['continue'])){
      $membership =& $userAccount->plugins['membership'];
      $planInfo = $membership->getPlanInfo((int)$_POST['plan_id']);

      if (RENTAL_UPGRADE_CYCLE == 'true'){
          if (Session::get('account_action') == 'upgrade'){ /* Yes it can only be this, but i don't wanna take it out */
	          Doctrine_Query::create()
		          ->delete('MembershipUpdate')
		          ->where('customers_id = ?', $userAccount->getCustomerId())
		          ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

              $Qinsert = new MembershipUpdate();
              $Qinsert->customers_id = $userAccount->getCustomerId();
              $Qinsert->plan_id = (int)$_POST['plan_id'];
              $Qinsert->upgrade_date = date('Y-m-d', $membership->membershipInfo['next_bill_date']);
              $Qinsert->save();

              $messageStack->addSession('pageStack', 'Your new membership will take effect on "' . tep_date_short(date('Y-m-d', $membership->membershipInfo['next_bill_date'])) . '"', 'success');
              tep_redirect(itw_app_link(null, 'account', 'default', 'SSL'));
          }
      }

      $row_cust = '';
      if ($membership->planInfo['membership_months'] > 0){
          $row_cust .= $membership->planInfo['membership_months'];
      }
      if ($membership->planInfo['membership_days'] > 0){
          $row_cust .= $membership->planInfo['membership_days'];
      }

      $row_day='';
      if ($planInfo['membership_months'] > 0){
          $row_day .= $planInfo['membership_months'];
      }
      if ($planInfo['membership_days'] > 0){
          $row_day .= $planInfo['membership_days'];
      }

      if (Session::get('account_action') == 'upgrade'){
          if ($membership->membershipInfo['payment_method'] == 'usaepay'){
              $paymentMethod = 'USAePay';
          }elseif ($membership->membershipInfo['payment_method'] == 'authorizenet'){
              $paymentMethod = 'Authorize.Net';
          }elseif($membership->membershipInfo['payment_method'] == 'cc'){
              $paymentMethod = 'Credit Card';
          }

          // send a mail request to admin
          $subject="New request for subscription upgradation";
          $body = "Hello Admin,\nThe following customer has submited the request for upgrading his/her subscription.\nCustomer ID: ".$userAccount->getCustomerId().
                  "\nCustomer Name: ".$userAccount->getFullName().
                  "\nCustomer Email: ".$userAccount->getEmailAddress().
                  "\n-------------------------------------------------".
                  "\n\nCurrent Plan".
                  "\n\nPlan ID: ".$membership->planInfo['plan_id'].
                  "\nPackage Name: ".$membership->planInfo['package_name'].
                  $row_cust.
                  "\nNumber of Titles That Can Be Issued: ".$membership->planInfo['no_of_titles'].
                  "\nFree Trial Period: ".$membership->planInfo['free_trial'].
                  "\nPrice: ".$membership->planInfo['price'].
                  "\nPayment Method: " . $paymentMethod .
                  "\n-------------------------------------------------".
                  "\n\nNew Plan Requested".
                  "\n\nPlan ID: ".$_POST['plan_id'].
                  "\nPackage Name: ".$planInfo['package_name'].
                  $row_day.
                  "\nNumber of Titles That Can Be Issued: ".$planInfo['no_of_titles'].
                  "\nFree Trial Period: ".$planInfo['free_trial'].
                  "\nPrice: ".$planInfo['price'].
                  "\nPayment Method: " . $paymentMethod;

          //STORE_OWNER_EMAIL_ADDRESS;
          mail(STORE_OWNER_EMAIL_ADDRESS,$subject,$body,"From:".EMAIL_FROM);
          $messageStack->addSession('pageStack', sysLanguage::get('UPGRADE_EMAIL_SENT'), 'success');

          tep_redirect(itw_app_link(null, 'account', 'default', 'SSL'));
      }
  }
	ob_start();
?>
<table border="0" width="100%" cellspacing="1" cellpadding="2">
 <tr>
  <td><?php
   $info_box_contents = array();
   $info_box_contents[0][] = array(
       'align'  => 'center',
       'params' => 'class="productListing-heading"',
       'text'   => '&nbsp;'
   );

   $info_box_contents[0][] = array(
       'align'  => 'left',
       'params' => 'class="productListing-heading"',
       'text'   => sysLanguage::get('TEXT_PACKAGE_NAME')
   );

   $info_box_contents[0][] = array(
       'align'  => 'center',
       'params' => 'class="productListing-heading"',
       'text'   => sysLanguage::get('TEXT_MEMBERSHIP_PERIOD')
   );

   $info_box_contents[0][] = array(
       'align'  => 'center',
       'params' => 'class="productListing-heading"',
       'text'   => sysLanguage::get('TEXT_NO_OF_TITLES')
   );

   $info_box_contents[0][] = array(
       'align'  => 'right',
       'params' => 'class="productListing-heading"',
       'text'   => sysLanguage::get('TEXT_PRICE')
   );

   $currentPlan = $userAccount->plugins['membership']->getPlanId();

   $Qplans = Doctrine_Query::create()
	   ->from('Membership m')
	   ->leftJoin('m.MembershipDescription md')
	   ->where('md.language_id = ?', Session::get('languages_id'))
	   ->orderBy('m.sort_order')
	   ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
   $i = 0;
   foreach($Qplans as $plan){
       if (($i/2) == floor($i/2)) {
           $info_box_contents[] = array('params' => 'class="productListing-even"');
       } else {
           $info_box_contents[] = array('params' => 'class="productListing-odd"');
       }
       $cur_row = sizeof($info_box_contents) - 1;

       $style = '';
       $adnl = '';
       $checked = false;
       if ($plan['plan_id'] == $currentPlan){
           $style = ' style="background:#ffffd7"';
           $adnl = '&nbsp;&nbsp;<span style="color:red">' . sysLanguage::get('TEXT_CURRENT') . '</span>';
           $checked = true;
       }

       $info_box_contents[$cur_row][] = array(
           'params' => 'class="productListing-data"' . $style,
           'text'   => tep_draw_radio_field('plan_id', $plan['plan_id'], $checked)
       );

       $info_box_contents[$cur_row][] = array(
           'params' => 'class="productListing-data"' . $style,
           'text'   => $plan['MembershipDescription'][0]['package_name'] . $adnl
       );

       $info_box_contents[$cur_row][] = array(
           'align'  => 'center',
           'params' => 'class="productListing-data"' . $style,
           'text'   => ($plan['membership_months'] > 0 ? $plan['membership_months'] . ' Month(s)' : '') .
                       ($plan['membership_days'] > 0 ? $plan['membership_days'] . ' Days' : '')
       );

       $info_box_contents[$cur_row][] = array(
           'align'  => 'center',
           'params' => 'class="productListing-data"' . $style,
           'text'   => $plan['no_of_titles']
       );

       $info_box_contents[$cur_row][] = array(
           'align'  => 'right',
           'params' => 'class="productListing-data"' . $style,
           'text'   => $currencies->format($plan['price'])
       );

       $i++;
   }
   new productListingBox($info_box_contents);
  ?></td>
 </tr>
 <tr>
  <td class="infoBoxContents"><?php echo sysLanguage::get('TEXT_INFO_MEMBERSHIP_PACKAGE');?></td>
 </tr>
</table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('MEMBERSHIP_OPTIONS');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'membership',
		'action' => itw_app_link(null,'account', 'membership', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
