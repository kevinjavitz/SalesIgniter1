<?php
/*
	Manage QuickBooks Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2012 I.T. Web Experts

	This script and its source is not redistributable
*/

require_once sysConfig::getDirFsCatalog() . 'extensions/manageQuickBooks/QuickBooks.php';

$token = sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_TOKEN');
$oauth_consumer_key= sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_KEY');
$oauth_consumer_secret= sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_SECRET');


$domain = sysConfig::get('HTTP_DOMAIN_NAME');
//change to ssl
$this_url =  'http://' . $domain .  '/admin/manageQuickBooks/Export_To_QuickBooks/oauth.php';
$that_url =  'http://' . $domain .  '/admin/manageQuickBooks/Export_To_QuickBooks/exportToQB.php';

$dsn=$connString;
$encryption_key= sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_ENCRYPT');


$the_username = Session::get('login_firstname') . Session::get('login_id');
$the_tenant = 12345;

if (!QuickBooks_Utilities::initialized($dsn))
{
	// Initialize creates the neccessary database schema for queueing up requests and logging
	QuickBooks_Utilities::initialize($dsn);
}
$IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere($dsn, $encryption_key, $oauth_consumer_key, $oauth_consumer_secret, $this_url, $that_url);


// Set up the IPP instance                                                                                                                                                                                                    
$IPP = new QuickBooks_IPP($dsn);
// Get our OAuth credentials from the database                                                                                                                                                                                
$creds = $IntuitAnywhere->load($the_username, $the_tenant);
// Tell the framework to load some data from the OAuth store                                                                                                                                                                  
$IPP->authMode(
               QuickBooks_IPP::AUTHMODE_OAUTH,
               $the_username,
               $creds);
                                                                                                                                                                                          
$realm = $creds['qb_realm'];
$realmID = $realm;
// Load the OAuth information from the database                                                                                                                                                                               
if ($Context = $IPP->context())
  { 

    // Set the DBID                                                                                                                                                                                                           
    $IPP->dbid($Context, 'something');

    // Set the IPP flavor                                                                                                                                                                                                     
    $IPP->flavor($creds['qb_flavor']);

    // Get the base URL if it's QBO                                                                                                                                                                                           
    if ($creds['qb_flavor'] == QuickBooks_IPP_IDS::FLAVOR_ONLINE)
      {
         $IPP->baseURL($IPP->getBaseURL($Context, $realm));
         
      }
     
	$IPP->useIDSParser(false);
	
	// We use our Customer service to operate on customers within IDS/QuickBooks
	$Service = new QuickBooks_IPP_Service_Customer(); 

       $q = Doctrine_Query::create()
      	 ->select('c.*')
         ->from('customers c');
	$results=$q->execute();

	foreach ($results as $customerSI) {
		// Create our customer object
               $addressString = "a.address_book_id = " . $customerSI['customers_default_address_id'];
              
              $q2 = Doctrine_Query::create()
                ->select('a.*')
                ->from('AddressBook a')
                ->where($addressString);
              $address=$q2->execute();

		$Customer = new QuickBooks_IPP_Object_Customer();
	    $Customer->setName($customerSI['customers_firstname'] . " " . $customerSI['customers_lastname']);
		
		$Customer->setGivenName($customerSI['customers_firstname']);
		$Customer->setFamilyName($customerSI['customers_lastname']);
              
              $Phone = new QuickBooks_IPP_Object_Phone();
              $Phone->setDeviceType('LandLine');
              $Phone->setFreeFormNumber($customerSI['customers_telephone']);
              //phone and email do not appear to be supported in QBD - get this error - cannot insert NULL into ("ESB"."EMAIL_API"."LABEL_NAME_MAP")
 		if ($creds['qb_flavor'] == QuickBooks_IPP_IDS::FLAVOR_ONLINE)
	              $Customer->addPhone($Phone);
              $Email = new QuickBooks_IPP_Object_Email();
              $Email->setAddress($customerSI['customers_email_address']);
              if ($creds['qb_flavor'] == QuickBooks_IPP_IDS::FLAVOR_ONLINE)
	              $Customer->addEmail($Email);
	
		// Create the address
		$Address = new QuickBooks_IPP_Object_Address();
		$Address->setLine1($address[0]['entry_street_address']);
		$Address->setCity($address[0]['entry_city']);
		$Address->setCountrySubDivisionCode($address[0]['entry_state']);
		$Address->setPostalCode($address[0]['entry_postcode']);
		$Address->setTag('Billing');
		// Add the address to the customer
     		$Customer->addAddress($Address);
	
	
		// Now, let's add the customer to QuickBooks
		if ($ID = $Service->add($Context, $realmID, $Customer))
		{
			// Yeah, we added it!
                     print(sysLanguage::get('QB_CUSTOMER_ADDED') . $customerSI['customers_firstname'] . " " . $customerSI['customers_lastname'] . "<br>");
                     
		}
	else
		{     
			$errorDetails = $Service->lastResponse();
                     if (!preg_match("/<Message>(.*)<\/Message>/s", $errorDetails, $match))
                         preg_match("/<ErrorDesc>(.*)<\/ErrorDesc>/s", $errorDetails, $match);  
                     echo sysLanguage::get('QB_CUSTOMER_ERROR') . $customerSI['customers_firstname'] . " " . $customerSI['customers_lastname'] . ".  ";
                     echo $match[1] . "<br>";                 
              }
	}


 	     //move on to orders
  	     $o = Doctrine_Query::create()
  	    	 ->select('o.*')
  	       ->from('orders o');
		$results=$o->execute();
             
            foreach ($results as $orders) {
               $prodString = "x.orders_id = " . $orders['orders_id']; 

               $x = Doctrine_Query::create()
  	    	 ->select('x.*')
  	       ->from('OrdersProducts x')
              ->where($prodString);
		$orderproducts=$x->execute();

              foreach ($orderproducts as $product) {
                $Item = new QuickBooks_IPP_Object_Item();
                $Item->setName($product['products_name']);
                $Item->setDesc($product['products_model']);
                $Item->setType('Product');
                $ItemIncomeAccount = new QuickBooks_IPP_Object_Account();
                $ItemIncomeAccount->setAccountName('Sales');
                $Item->addIncomeAccountRef($ItemIncomeAccount);
                $ItemUnitPrice = new QuickBooks_IPP_Object_UnitPrice();
                $ItemUnitPrice->setCurrencyCode('USD');
                $ItemUnitPrice->setAmount($product['products_price']);	
                $Item->addUnitPrice($ItemUnitPrice);
                $ServiceItem = new QuickBooks_IPP_Service_Item();
                $ID = $ServiceItem->add($Context, $realmID, $Item);
                
              }
              $SalesReceipt = new QuickBooks_IPP_Object_SalesReceipt();
              $SalesReceiptHeader = new QuickBooks_IPP_Object_Header();
              $SalesReceiptHeader->setDocNumber("SI-" . $orders['orders_id']);
              $SalesReceiptHeader->setTxnDate($orders['date_purchased']);
                 //get tax
              $totalString = "y.orders_id = " . $orders['orders_id'] .  " AND y.title = 'Sub-Total:'"; 
              $y = Doctrine_Query::create()
  	    	   ->select('y.*')
  	          ->from('OrdersTotal y')
 		   ->where($totalString);
             	$ordertotals=$y->execute();


		$taxString = "tax.orders_id = " . $orders['orders_id'] .  " AND tax.title = 'Tax:'"; 
              $tax = Doctrine_Query::create()
  	    	   ->select('tax.*')
  	          ->from('OrdersTotal tax')
 		   ->where($taxString);
             	$ordertotals_tax=$tax->execute();
             
		  $SalesReceiptHeader->setTaxAmt($ordertotals_tax[0]['value']);
		
              $Service2 = new QuickBooks_IPP_Service_SalesReceipt(); 
              $searchString = "a.customers_id = " . $orders['customers_id']; 

              $a = Doctrine_Query::create()
  	    	  ->select('a.*')
  	         ->from('customers a')
 		  ->where($searchString);
              //customer who order belongs to
		$foundCustomer=$a->execute();
		$searchName=$foundCustomer[0]['customers_firstname'] . " " . $foundCustomer[0]['customers_lastname'];
              $theid=$Service->findAll($Context, $realmID);
              preg_match_all("/<Customer>.*<\/Customer>/sU", $theid, $answer);
              for ($i=0; $i < count($answer[0]); $i++)
              {
                if (preg_match("/$searchName/", $answer[0][$i], $cutAnswer))
                    $pos=$i;
              }
          
            preg_match("/<Id idDomain=\"(.*)\">(.*)<\/Id>/", $answer[0][$pos], $idAndDomain);
            $SalesReceiptHeader->addCustomerId("{". $idAndDomain[1] . "-" . $idAndDomain[2] . "}");
            $SalesReceiptHeader->addCustomerName($searchName);
            $SalesReceipt->addHeader($SalesReceiptHeader);
              
              for($j=0; $j < count($orderproducts); $j++) 
              {
            	   $SalesReceiptLine = new QuickBooks_IPP_Object_Line();
                 $SalesReceiptLine->setItemName($orderproducts[$j]['products_name']);
                

               //this is code to add rental dates
         
                if ((strncmp($orderproducts[0]['purchase_type'], "reservation", 10)) == 0) 
               {    $resString = "r.orders_products_id = " . $orderproducts[$j]['orders_products_id'];
                    $reservation = Doctrine_Query::create()
  	    	        ->select('r.*')
  	               ->from('OrdersProductsReservation r')
 		        ->where($resString);
                    $res=$reservation->execute();
                    $SalesReceiptLine->setDesc($orderproducts[$j]['products_name'] . " -- " . $res[0]['start_date'] . " - " . $res[0]['end_date']);
                } 
                 else  
 	              $SalesReceiptLine->setDesc($orderproducts[$j]['products_name']);
                 $SalesReceiptLine->setQty($orderproducts[$j]['products_quantity']);  
                 $SalesReceiptLine->setAmount($orderproducts[$j]['products_price'] * $orderproducts[$j]['products_quantity']);
                 $SalesReceiptLine->setUnitPrice($orderproducts[$j]['products_price']);
                 $SalesReceipt->addLine($SalesReceiptLine);
                 //$SalesReceiptHeader->setDepositToAccountName("Sales");
		}
           
            
            //check for duplicates

            $ServiceSalesReceipt = new QuickBooks_IPP_Service_SalesReceipt();
            $receipts = $ServiceSalesReceipt->findAll($Context, $realmID, $creds['qb_flavor']);
            $checkString = "/<DocNumber>SI-" . $orders['orders_id'] . "<\/DocNumber>/s";
            if (!(preg_match($checkString, $receipts)))
            {
              if ($ID = $Service2->add($Context, $realmID, $SalesReceipt, $creds['qb_flavor']))
               { preg_match("/<Id.*>(.*)<\/Id>/sU", $ID, $match); echo $searchName . sysLanguage::get('QB_SALE_ADDED') . "SI-" . $orders['orders_id'] . "<br> ";
                //echo "<br>" . print_r($Service2->lastRequest()) . "<br>---" . print_r($Service2->lastResponse());
               } 
              else
		{
		        echo "<br>" . print_r($Service2->lastRequest());
                echo "<br>" . sysLanguage::get('QB_SALE_ERROR') . "<br>"  . $Service2->lastResponse();
		  
		 }
             }
	}
    echo "<br>" . sysLanguage::get('QB_COMPLETE');

}
?>
