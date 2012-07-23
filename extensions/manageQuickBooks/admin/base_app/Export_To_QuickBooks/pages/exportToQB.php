<?php
/*
	Manage QuickBooks Extension Version 1 
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com
 
	Copyright (c) 2012 I.T. Web Experts  

	This script and its source is not redistributable
*/
require_once sysConfig::getDirFsCatalog() . 'extensions/manageQuickBooks/QuickBooks.php';

error_log("start" . date("H:i:s"). "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
error_log("Beginning export\n", 3, sysConfig::getDirFsCatalog() . "error_log");

require_once sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/zonereservation/Doctrine/ModulesShippingZoneReservationMethods.php';
require_once sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/zonereservation/Doctrine/ModulesShippingZoneReservationMethodsDescription.php';

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

	/* We use our Customer service to operate on customers within IDS/QuickBooks */
	$Service = new QuickBooks_IPP_Service_Customer(); 
	
	
    $q = Doctrine_Query::create()
    ->select('c.*')
    ->from('customers c');
	$results=$q->execute();
	
	
	
//if (false) { //test - skip customers

	foreach ($results as $customerSI) 
	{
	//if ($customerSI['customers_id'] == 30 || $customerSI['customers_id'] == 105) { //testing - run specific customer
		// Create our customer object
        $addressString = "a.address_book_id = " . $customerSI['customers_default_address_id'];
        $q2 = Doctrine_Query::create()
        ->select('a.*')
        ->from('AddressBook a')
        ->where($addressString);
        $address=$q2->execute();

		$Customer = new QuickBooks_IPP_Object_Customer();
		$Customer->setName(trim(trim($customerSI['customers_firstname']) . " " . trim($customerSI['customers_lastname'])));
		$Customer->setGivenName(trim($customerSI['customers_firstname']));
		$Customer->setFamilyName(trim($customerSI['customers_lastname']));
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
		$Address->setCity(trim($address[0]['entry_city']));
		$Address->setCountrySubDivisionCode($address[0]['entry_state']);
		$Address->setPostalCode($address[0]['entry_postcode']);
		$Address->setTag('Billing');
		// Add the address to the customer
     	$Customer->addAddress($Address);
		// Now, let's add the customer to QuickBooks
		if ($ID = $Service->add($Context, $realmID, $Customer))
		{
			error_log("Added Customer" . trim($customerSI['customers_id']) . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
			// Yeah, we added it
            //print(sysLanguage::get('QB_CUSTOMER_ADDED') . trim($customerSI['customers_firstname']) . " " . trim($customerSI['customers_lastname']) . "<br>");
		}
	    else
		{     
			$errorDetails = $Service->lastResponse();
            if (!preg_match("/<Message>(.*)<\/Message>/s", $errorDetails, $match))
                preg_match("/<ErrorDesc>(.*)<\/ErrorDesc>/s", $errorDetails, $match);  
            error_log("Error Customer" . trim($customerSI['customers_id']) . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
             error_log(print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(), 1), 3, sysConfig::getDirFsCatalog() . "error_log");
            //echo sysLanguage::get('QB_CUSTOMER_ERROR') . $customerSI['customers_firstname'] . " " . $customerSI['customers_lastname'] . ".  ";
            //echo $match[1] . "<br>";                 
        }
		time_nanosleep(0,160000000);  //cannot exceed threshold of 500 hits per minute at Intuit
	}
//} //testing - run specific customer
//} //testing - skip customers


			 $ServiceItem = new QuickBooks_IPP_Service_Item();
             //shipping prices
             $x = Doctrine_Query::create()
  	    	 ->select('x.*')
  	         ->from('ModulesShippingZoneReservationMethods x');
             //->where($prodString); 
		     $shipMethods=$x->execute();

              foreach ($shipMethods as $shippingMethod) {
                $Item = new QuickBooks_IPP_Object_Item();
                
                $descString = "y.method_id = " . $shippingMethod['method_id']; 
                $y = Doctrine_Query::create()
 	 	    	 ->select('y.*')
  		         ->from('ModulesShippingZoneReservationMethodsDescription y')
        	     ->where($descString);
		        $desc=$y->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		        $Item->setName("Shipping" . $shippingMethod['method_id']);
		        //$Item->setName(substr($desc[0]['method_text'], 0, 20));;
                $Item->setDesc($desc[0]['method_text']);
                $Item->setType('Service');
                $ItemIncomeAccount = new QuickBooks_IPP_Object_Account();
                $ItemIncomeAccount->setAccountName('Sales');
                $Item->addIncomeAccountRef($ItemIncomeAccount);
                $ItemUnitPrice = new QuickBooks_IPP_Object_UnitPrice();
                $ItemUnitPrice->setCurrencyCode('USD');
                $ItemUnitPrice->setAmount($shippingMethod['method_cost']);	
                $Item->addUnitPrice($ItemUnitPrice);
                $Item->setTaxable('false');
                //$ServiceItem = new QuickBooks_IPP_Service_Item();
                //$ID = $ServiceItem->add($Context, $realmID, $Item);
                if ($ID = $ServiceItem->add($Context, $realmID, $Item))
				{
   
				}
				else
				{	     
				$errorDetails = $ServiceItem->lastResponse();
                if (!preg_match("/<Message>(.*)<\/Message>/s", $errorDetails, $match))
                         preg_match("/<ErrorDesc>(.*)<\/ErrorDesc>/s", $errorDetails, $match);  
                 //  print_r($ServiceItem->lastRequest()); echo "error adding shipping"; print_r($ServiceItem->lastResponse());
                                  
              }
	}
	
                
       		 //add custom fields
             $custString = "c.module_type = 'custom'"; 

               $c = Doctrine_Query::create()
  	    	   ->select('c.*')
  	           ->from('OrdersTotal c')
               ->where($custString);
		       $cust=$c->execute();

              foreach ($cust as $customCharge) {
                $Item = new QuickBooks_IPP_Object_Item();
                $Item->setName('Custom' . $customCharge['orders_id']);
                $Item->setDesc($customCharge['title']);
                $Item->setType('Product');
                $ItemIncomeAccount = new QuickBooks_IPP_Object_Account();
                $ItemIncomeAccount->setAccountName('Sales');
                $Item->addIncomeAccountRef($ItemIncomeAccount);
                $ItemUnitPrice = new QuickBooks_IPP_Object_UnitPrice();
                $ItemUnitPrice->setCurrencyCode('USD');
                $ItemUnitPrice->setAmount($customCharge['value']);	
                $Item->addUnitPrice($ItemUnitPrice);
                $Item->setTaxable('false');
                //$ServiceItem = new QuickBooks_IPP_Service_Item();
                $ID = $ServiceItem->add($Context, $realmID, $Item);
                
              }        
	     	 $discString = "d.module_type = 'pprdiscount'"; 

               $d = Doctrine_Query::create()
  	          ->select('d.*')
  	          ->from('OrdersTotal d')
              ->where($discString);
			  $disc=$d->execute();

              foreach ($disc as $discount) {
                $Item = new QuickBooks_IPP_Object_Item();
                $Item->setName('Discount' . $discount['orders_id']);
                $Item->setDesc($discount['title']);
                $Item->setType('Product');
                $ItemIncomeAccount = new QuickBooks_IPP_Object_Account();
                $ItemIncomeAccount->setAccountName('Sales');
                $Item->addIncomeAccountRef($ItemIncomeAccount);
                $ItemUnitPrice = new QuickBooks_IPP_Object_UnitPrice();
                $ItemUnitPrice->setCurrencyCode('USD');
                $ItemUnitPrice->setAmount($discount['value']);	
                $Item->addUnitPrice($ItemUnitPrice);
                $Item->setTaxable('false');
                //$ServiceItem = new QuickBooks_IPP_Service_Item();
                if ($ID = $ServiceItem->add($Context, $realmID, $Item))
		{
			// Yeah, we added it!
                                  
		}
    	else
		{     
			$errorDetails = $ServiceItem->lastResponse();
            if (!preg_match("/<Message>(.*)<\/Message>/s", $errorDetails, $match))
                     preg_match("/<ErrorDesc>(.*)<\/ErrorDesc>/s", $errorDetails, $match);  
                  // print_r($ServiceItem->lastRequest()); echo "error adding discount"; print_r($ServiceItem->lastResponse());
                                    
              }
                
              }     
 

	 // use Invoices move on to orders
	 

		//Start Invoices
		error_log("Starting Invoices\n", 3, sysConfig::getDirFsCatalog() . "error_log");
  	     $o = Doctrine_Query::create() 
  	       ->select('o.*')
  	       ->from('orders o')
  	       ->orderBy('o.orders_id');
		$results=$o->execute();
	$Service2 = new QuickBooks_IPP_Service_Invoice(); 
          foreach ($results as $orders) {
          	$hasLines=false;
            //if($orders['orders_id'] == 210 ) { //testing 	- run specific invoices
           $prodString = "x.orders_id = " . $orders['orders_id']; 

             $x = Doctrine_Query::create()
  	    	 ->select('x.*')
  	         ->from('OrdersProducts x')
             ->where($prodString);
			$orderproducts=$x->execute();

              foreach ($orderproducts as $product) {
             	
                $Item = new QuickBooks_IPP_Object_Item();
                $model=$product['products_model'];
                $model = str_replace("(", "", $model);  //these characters don't work well with intuit
                $model = str_replace(")", "", $model);
                $model = str_replace("&", "", $model);
                $Item->setName($model);
                $Item->setDesc($product['products_name']);
                $Item->setType('Product');
                $ItemIncomeAccount = new QuickBooks_IPP_Object_Account();
                $ItemIncomeAccount->setAccountName('Sales');
                $Item->addIncomeAccountRef($ItemIncomeAccount);
                $ItemUnitPrice = new QuickBooks_IPP_Object_UnitPrice();
                $ItemUnitPrice->setCurrencyCode('USD');
                $ItemUnitPrice->setAmount($product['products_price']);	
                $Item->addUnitPrice($ItemUnitPrice);
                //$ServiceItem = new QuickBooks_IPP_Service_Item();
                $ID = $ServiceItem->add($Context, $realmID, $Item);
                time_nanosleep(0,160000000);
                
              }
            
               
              
              
              $Invoice = new QuickBooks_IPP_Object_Invoice();
              $InvoiceHeader = new QuickBooks_IPP_Object_Header();
              $InvoiceHeader->setDocNumber("Si-" . $orders['orders_id']);
              $InvoiceHeader->setTxnDate($orders['date_purchased']);
            
              $totalString = "y.orders_id = " . $orders['orders_id'] .  " AND y.module_type = 'total'"; 
              
              $y = Doctrine_Query::create()
  	    	  ->select('y.*')
  	          ->from('OrdersTotal y')
 		      ->where($totalString);
             	$ordertotals=$y->execute();
             		
              
              $searchString = "a.customers_id = " . $orders['customers_id']; 

              $a = Doctrine_Query::create()
  	    	  ->select('a.*')
  	          ->from('customers a')
 		      ->where($searchString);
        	 if ($a->count() <= 0)  
              {
              	error_log("There was no customer for that invoice - " . $orders['orders_id'], 3, sysConfig::getDirFsCatalog() . "error_log");
              	continue;
              }
              //customer who order belongs to
		$foundCustomer=$a->execute();
		$trimmed = trim($foundCustomer[0]['customers_firstname']);
		$trimmed_last = trim($foundCustomer[0]['customers_lastname']);
		if (!(empty($trimmed)))
		{
			if (!(empty($trimmed_last))) {
				$searchName=trim($foundCustomer[0]['customers_firstname']) . " " . trim($foundCustomer[0]['customers_lastname']);
				$searchName2=trim($foundCustomer[0]['customers_lastname']) . ", " . trim($foundCustomer[0]['customers_firstname']);
			}
			else 
			    $searchName=$trimmed;
		}
		else if (!(empty($trimmed_last))){
			$searchName=trim($foundCustomer[0]['customers_lastname']);
		}
		else 
		  continue;
		      
              
             //error_log("searchname " . $searchName . " searchname2 " . $searchName2 .  "\n" . print_r($theid,1), 3, sysConfig::getDirFsCatalog() . "error_log");
             //error_log3("service " . $Service . " --- " . print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             $answer = array(); $records=0;
             for ($paging=1; $paging < 100; $paging++) {
             	//error_log("testing paging" . $paging, 3 , sysConfig::getDirFsCatalog() . "error_log");
             	$theid=$Service->findAll($Context, $realmID, '', $paging);
             	$savedArray = $answer;	
              	 if (preg_match_all("/<Name>.*<\/Name>/sU", $theid, $testanswer)) 
              	 { 
                	  preg_match_all("/<Customer>.*<\/Customer>/sU", $theid, $pageAnswer);
                	  //error_log("pageAnswer" . print_r($pageAnswer[0],1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // error_log("savedArray " . print_r($savedArray, 1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	  //$answer = $savedArray + $pageAnswer[0];
                	 for($z=0; $z<count($pageAnswer[0]); $z++)
                	  {
                	  	$answer[$z+$records]=$pageAnswer[0][$z];
                	  }
                	   $records += count($pageAnswer[0]);
                	  //error_log("records is " . $records, 3, sysConfig::getDirFsCatalog() . "error_log");
                	   
                	  
              	 }
              	 else 
               		  break;
               	 
             } 
             
             //error_log("searchname " . $searchName . " answer" . print_r($answer,1), 3, sysConfig::getDirFsCatalog() . "error_log");
             
              $found=false;
              for ($i=0; $i < count($answer); $i++)
              { 
               // error_log('searching for ' . $searchName . ' in ' . $answer[0][$i], 3, sysConfig::getDirFsCatalog() . "error_log");
               $searchNamecut = str_replace("'", "", $searchName);   //handle apostrophes in name
               $searchNamecut2 = str_replace("'", "", $searchName2);
               $nametocheck = str_replace("&apos;","", $answer[$i]);
               $searchNamecut = str_replace("@", "\@", $searchNamecut); //handle emails as name
                                         
                if (preg_match("/<Name>$searchNamecut<\/Name>/isU", $nametocheck, $cutAnswer)
                		|| preg_match("/<Name>$searchNamecut2<\/Name>/isU", $nametocheck, $cutAnswer) ) 
                {   $found=true;
                    $pos=$i;
                    break;
                     
                }
              }
              //error_log("before", 3, sysConfig::getDirFsCatalog() . "error_log");
         	if ($found) {
         		//error_log(" found customer \n", 3, sysConfig::getDirFsCatalog() . "error_log"); continue;
            	preg_match("/<Id idDomain=\"(.*)\">(.*)<\/Id>/", $answer[$pos], $idAndDomain);
           	    $InvoiceHeader->addCustomerId("{". $idAndDomain[1] . "-" . $idAndDomain[2] . "}");
            	$InvoiceHeader->addCustomerName($searchName);
            	//$InvoiceHeader->setARAccountName("Accounts Receivable");
           		$Invoice->addHeader($InvoiceHeader);
            
            
           
             for($j=0; $j < count($orderproducts); $j++) 
              {
              	$hasLines=true;
            	 $InvoiceLine = new QuickBooks_IPP_Object_Line();
            	 $model=$orderproducts[$j]['products_model'];
                $model = str_replace("(", "", $model);
                $model = str_replace(")", "", $model);
                $model = str_replace("&", "", $model);
                 $InvoiceLine->setItemName($model);
                 //this is code to add rental dates
                if ((strncmp($orderproducts[0]['purchase_type'], "reservation", 10)) == 0) 
                {
                	$resString = "r.orders_products_id = " . $orderproducts[$j]['orders_products_id'];
                    $reservation = Doctrine_Query::create()
  	    	        ->select('r.*')
  	                ->from('OrdersProductsReservation r')
 		            ->where($resString);
                    $res=$reservation->execute();
                    $InvoiceLine->setDesc($orderproducts[$j]['products_name'] . " -- " . $res[0]['start_date'] . " - " . $res[0]['end_date']);
                } 
                else  
 	             	$InvoiceLine->setDesc($orderproducts[$j]['products_name']);
 	             	
                 $InvoiceLine->setQty($orderproducts[$j]['products_quantity']);  
                 $InvoiceLine->setAmount($orderproducts[$j]['products_price'] * $orderproducts[$j]['products_quantity']);
                 $InvoiceLine->setUnitPrice($orderproducts[$j]['products_price']);
                 $Invoice->addLine($InvoiceLine);
                 
                 //$SalesReceiptHeader->setDepositToAccountName("Sales");
		}
			$customString = "custom.orders_id = " . $orders['orders_id'] .  " AND custom.module_type = 'custom'"; 
              $custom = Doctrine_Query::create()
  	    	   ->select('custom.*')
  	          ->from('OrdersTotal custom')
 		      ->where($customString);
              $ordertotals_custom=$custom->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
                      
              for($t=0; $t < count($ordertotals_custom); $t++)
              {
                  $hasLines=true;     
               $InvoiceCustomLine = new QuickBooks_IPP_Object_Line();
               $InvoiceCustomLine->setItemName('Custom' . $ordertotals_custom[$t]['orders_id']);
               $InvoiceCustomLine->setDesc($ordertotals_custom[$t]['title']);
               $InvoiceCustomLine->setAmount($ordertotals_custom[$t]['value']);
               $InvoiceCustomLine->setQty(1);
               $InvoiceCustomLine->setUnitPrice($ordertotals_custom[$t]['value']);
               
               $Invoice->addLine($InvoiceCustomLine);  
              }
            
		
		
		
		     $shipString = "ship.orders_id = " . $orders['orders_id'] .  " AND ship.module_type = 'shipping'";
		     $ship = Doctrine_Query::create()->select('ship.*')->from('OrdersTotal ship')->where($shipString);
		     $ordertotals_ship=$ship->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
              $shipTotal = 0;
            
              for($t=0; $t < count($ordertotals_ship); $t++)
              {
               $hasLines=true;          
              $w = Doctrine_Query::create()
  	    	 ->select('w.*')
  	         ->from('ModulesShippingZoneReservationMethodsDescription w');
  	         $shipMeths=$w->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
  	         //error_log3(var_dump($shipMeths) . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
  	         for($cnt=0; $cnt < count($shipMeths); $cnt++)
  	         {
  	         	preg_match('/\((.*)\)/', $ordertotals_ship[$t]['title'], $shipmatch);
  	         	if (strcmp(trim($shipmatch[1]), trim($shipMeths[$cnt]['method_text'])) == 0)
  	         	{ 
  	         	$itemname = 'Shipping' . $shipMeths[$cnt]['method_id'];
  	         	break;
  	         	}
  	         	 
  	         }
              
  	         
               $InvoiceShipLine = new QuickBooks_IPP_Object_Line();
               $needle = "Ship-" . substr($ordertotals_ship[$t]['method'],6);
               //$found = $ServiceItem->findByName($Context, $realmID, $needle);
               //$found = $ServiceItem->findAll($Context, $realmID);
               //preg_match("/<Id idDomain=\"(.*)\">(.*)<\/Id>/", $found, $id);
               //echo "found is" . print_r($found); echo "id is " . print_r($id);
               //$InvoiceDiscLine->setItemId("{". $id[1] . "-" . $id[2] . "}");
               //$InvoiceDiscLine->setItemId($found->getId());
               //$InvoiceShipLine->setItemName('Shipping' . substr($ordertotals_ship[$t]['method'], 6));
               $InvoiceShipLine->setItemName($itemname);
               //$InvoiceShipLine->setDesc($ordertotals_ship[$t]['title']);
               $InvoiceShipLine->setDesc(trim($shipmatch[1]));
               $InvoiceShipLine->setAmount($ordertotals_ship[$t]['value']);
               $InvoiceShipLine->setQty(1);
               $InvoiceShipLine->setUnitPrice($ordertotals_ship[$t]['value']);
              
               $Invoice->addLine($InvoiceShipLine);  
              }
              
              $discString = "disc.orders_id = " . $orders['orders_id'] .  " AND disc.module_type = 'pprdiscount'"; 
              $disc = Doctrine_Query::create()
  	    	   ->select('disc.*')
  	          ->from('OrdersTotal disc')
 		      ->where($discString);
              $ordertotals_disc=$disc->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
              $discTotal = 0;
            
              for($t=0; $t < count($ordertotals_disc); $t++)
              {$hasLines=true;
               $InvoiceDiscLine = new QuickBooks_IPP_Object_Line();
               $needle = "Ship-" . substr($ordertotals_disc[$t]['method'],6);
               //$found = $ServiceItem->findByName($Context, $realmID, $needle);
               //$found = $ServiceItem->findAll($Context, $realmID);
               //preg_match("/<Id idDomain=\"(.*)\">(.*)<\/Id>/", $found, $id);
               //echo "found is" . print_r($found); echo "id is " . print_r($id);
               //$InvoiceDiscLine->setItemId("{". $id[1] . "-" . $id[2] . "}");
               //$InvoiceDiscLine->setItemId($found->getId());
               $InvoiceDiscLine->setItemName('Discount' . $orders['orders_id']);
               $InvoiceDiscLine->setDesc($ordertotals_disc[$t]['title']);
               $InvoiceDiscLine->setAmount($ordertotals_disc[$t]['value']);
               $InvoiceDiscLine->setQty(1);
               $InvoiceDiscLine->setUnitPrice($ordertotals_disc[$t]['value']);
               
               
               
              $Invoice->addLine($InvoiceDiscLine); 
            
              }
              
              if (!$hasLines && $ordertotals[0]['value'] != 0)
              {$Item = new QuickBooks_IPP_Object_Item();
                $Item->setName('Total' . $orders['orders_id']);
                $Item->setDesc("Custom total for" . $orders['orders_id']);  
                $Item->setType('Product');
                $ItemIncomeAccount = new QuickBooks_IPP_Object_Account();
                $ItemIncomeAccount->setAccountName('Sales');
                $Item->addIncomeAccountRef($ItemIncomeAccount);
                $ItemUnitPrice = new QuickBooks_IPP_Object_UnitPrice();
                $ItemUnitPrice->setCurrencyCode('USD');
                $ItemUnitPrice->setAmount($ordertotals[0]['value']);	
                $Item->addUnitPrice($ItemUnitPrice);
                //$ServiceItem = new QuickBooks_IPP_Service_Item();
                $ID = $ServiceItem->add($Context, $realmID, $Item);
              	
              $InvoiceFixLine = new QuickBooks_IPP_Object_Line();
               $InvoiceFixLine->setItemName('Total' . $orders['orders_id']);
               $InvoiceFixLine->setDesc("Custom total for" . $orders['orders_id']);
               $InvoiceFixLine->setAmount($ordertotals[0]['value']);
               $InvoiceFixLine->setQty(1);
               $InvoiceFixLine->setUnitPrice($ordertotals[0]['value']);
               
               $Invoice->addLine($InvoiceFixLine); 
              }
            //check for duplicates

            //$ServiceInvoice = new QuickBooks_IPP_Service_Invoice();
				$answer = array(); $records=0;
                for ($paging=1; $paging < 100; $paging++) {
             	//error_log("testing paging in invoices(invoice)" . $paging, 3 , sysConfig::getDirFsCatalog() . "error_log");
             	$theid=$Service2->findAll($Context, $realmID, '', $paging);
             	//error_log("service " . $Service . " --- " . print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             	$savedArray = $answer;	
              	 if (preg_match_all("/<DocNumber>.*<\/DocNumber>/isU", $theid, $testanswer)) 
              	 { 
                	  preg_match_all("/<Invoice>.*<\/Invoice>/sU", $theid, $pageAnswer);
                	  //error_log("pageAnswer" . print_r($pageAnswer[0],1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // error_log("savedArray " . print_r($savedArray, 1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // $answer = $savedArray + $pageAnswer[0];
                	   for($z=0; $z<count($pageAnswer[0]); $z++)
                	  {
                	  	$answer[$z+$records]=$pageAnswer[0][$z];
                	  }
                	   $records += count($pageAnswer[0]);
                	   //error_log("records is " . $records, 3, sysConfig::getDirFsCatalog() . "error_log");
              	 }
              	 else 
               		  break;
               	 
             } 
            
            $checkString = "/<DocNumber>Si-" . $orders['orders_id'] . "<\/DocNumber>/s";
            $invFound=false;
            //error_log("search name is " . $checkString . "answer is " . print_r($answer,1), 3, sysConfig::getDirFsCatalog() . "error_log");
            for($i=0; $i<count($answer); $i++)
            { 
            if (preg_match($checkString, $answer[$i]))
               {   $invFound=true;
                    
                    break;
                     //echo "cutanswer: "; print_r($cutAnswer);
                }
            }
            
            if (!$invFound)
            {
                if ($ID = $Service2->add($Context, $realmID, $Invoice))
            
               { preg_match("/<Id.*>(.*)<\/Id>/sU", $ID, $match); //echo $searchName . sysLanguage::get('QB_SALE_ADDED') . "Si-" . $orders['orders_id'] . "<br> ";
               // echo "<br>" . print_r($Service2->lastRequest()) . "<br>---" . print_r($Service2->lastResponse());
                //error_log(print_r($Service2->lastRequest(),1) . "\n" . print_r($Service2->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
                error_log('invoice added ' . $orders['orders_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
            } 
              else
	          {
		      //echo "<br>" . print_r($Service2->lastRequest());
               // echo "<br>" . sysLanguage::get('QB_SALE_ERROR');
                 error_log(print_r($Service2->lastRequest(),1) . "\n" . print_r($Service2->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
                 error_log('invoice error ' . $orders['orders_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
               
		  
		      }
            }
            else 
            { 
             //echo "Invoice for " . $orders['orders_id'] . "already added.";
             error_log('invoice duplicate ' . $orders['orders_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
             //echo "check string is" . $checkString; print_r($invoices); exit;
             
            }
         } else { //echo "Problem adding invoice - customer not found";  error_log('problem invoice - customer not found' . $orders['orders_id'] . " searchname is " . $searchName . "name to check is " . $nametocheck . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
         
         error_log('customer not found - invoice ' . $orders['orders_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
         //error_log("debug" . print_r($Service2->lastRequest(),1) . "\n" . print_r($Service2->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
         
            // time_nanosleep(0,050000000);  //cannot exceed threshold of 500 hits per minute at Intuit
           
          }
              }  
        // }  //testing - run specific invoices
   

           error_log(date("H:i:s") . "starting payments\n", 3, sysConfig::getDirFsCatalog() . "error_log");
           
          $ServicePayment = new QuickBooks_IPP_Service_Payment();
             //process payments (only successful credit card payments and not refund) may need adjusted for future users
            $payString = "`success`=1 AND `payment_module` != 'moneyorder' AND `payment_module` != 'cashondelivery' AND `gateway_message` NOT LIKE '%refund%' AND `payment_amount` != 0";
             
              $pay = Doctrine_Query::create()
  	    	   ->select('pay.*')
  	          ->from('OrdersPaymentsHistory pay')
 		      ->where($payString)
 		             ->orderBy('pay.payment_history_id');
              $payments=$pay->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
              $payTotal = 0;
            
              for($t=0; $t < count($payments); $t++)
              {
  //if ($payments[$t]['orders_id'] == 199) { //testing - run specific payments
    //error_log("working payment " . $payments[$t]['payment_history_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
              $searchString = "b.orders_id = " . $payments[$t]['orders_id'];
              $b = Doctrine_Query::create()
              ->select ('b.*')
              ->from ('orders b') 
              ->where($searchString);
       
              if ($b->count() <= 0)  
              {
              	error_log ("There was no order for that payment " . $payments[$t]['payment_history_id'], 3, sysConfig::getDirFsCatalog() . "error_log");
              	continue;
              }
              $foundOrder=$b->execute();
              
              $searchString = "a.customers_id = " . $foundOrder[0]['customers_id']; 
              $a = Doctrine_Query::create()
  	    	  ->select('a.*')
  	          ->from('customers a')
 		      ->where($searchString);
              if ($a->count() <= 0)  
              {
              	error_log ("There was no customer for that payment " . $payments[$t]['payment_history_id'], 3, sysConfig::getDirFsCatalog() . "error_log");
              	continue;
              }
              //customer who payment belongs to
              $foundCustomer=$a->execute();
              //$Service = new QuickBooks_IPP_Service_Customer();
             // $ServicePayment = new QuickBooks_IPP_Service_Payment();
              $Payment = new QuickBooks_IPP_Object_Payment();
              $PaymentHeader = new QuickBooks_IPP_Object_Header();
              $PaymentHeader->setDocNumber("SiPay-" . $payments[$t]['payment_history_id'] . "-" . $payments[$t]['orders_id']);
              //$PaymentHeader->setDocNumber("SiPay122");  //testing
              $PaymentHeader->setTxnDate($payments[$t]['date_added']);
              //$PaymentHeader->addCustomerId("{". "NG" . "-" . "3700255" . "}"); //testing
              $PaymentHeader->setTotalAmt($payments[$t]['payment_amount']);
              //$PaymentHeader->setTotalAmt(37.42);  //testing
              $PaymentHeader->setCustomerName(trim($foundCustomer[0]['customers_firstname']) . " " . trim($foundCustomer[0]['customers_lastname']));
              //$Service = new QuickBooks_IPP_Service_Customer(); 
              //$searchName=trim($foundCustomer[0]['customers_firstname']) . " " . trim($foundCustomer[0]['customers_lastname']);
		//inserted
		$trimmed = trim($foundCustomer[0]['customers_firstname']);
		$trimmed_last = trim($foundCustomer[0]['customers_lastname']);
		if (!(empty($trimmed)))
		{
			if (!(empty($trimmed_last))) {
				$searchName=trim($foundCustomer[0]['customers_firstname']) . " " . trim($foundCustomer[0]['customers_lastname']);
				$searchName2=trim($foundCustomer[0]['customers_lastname']) . ", " . trim($foundCustomer[0]['customers_firstname']);
			}
			else 
			    $searchName=$trimmed;
		}
		else if (!(empty($trimmed_last))){
			$searchName=trim($foundCustomer[0]['customers_lastname']);
		}
		else 
		  continue;
		      
              
            //error_log("searchname " . $searchName . " searchname2 " . $searchName2 .  "\n" . print_r($theid,1), 3, sysConfig::getDirFsCatalog() . "error_log");
             //error_log3("service " . $Service . " --- " . print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             $answer = array(); $records=0;
             for ($paging=1; $paging < 100; $paging++) {
             	//error_log("testing paging" . $paging, 3 , sysConfig::getDirFsCatalog() . "error_log");
             	$theid=$Service->findAll($Context, $realmID, '', $paging);
             	//error_log("service " . $Service . " --- " . print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             	$savedArray = $answer;	
              	 if (preg_match_all("/<Name>.*<\/Name>/sU", $theid, $testanswer)) 
              	 { 
                	  preg_match_all("/<Customer>.*<\/Customer>/sU", $theid, $pageAnswer);
                	  //error_log("pageAnswer" . print_r($pageAnswer[0],1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // error_log("savedArray " . print_r($savedArray, 1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // $answer = $savedArray + $pageAnswer[0];
                	 for($z=0; $z<count($pageAnswer[0]); $z++)
                	  {
                	  	$answer[$z+$records]=$pageAnswer[0][$z];
                	  }
                	   $records += count($pageAnswer[0]);
                	   //error_log("records is " . $records, 3, sysConfig::getDirFsCatalog() . "error_log");
              	 }
              	 else 
               		  break;
               	 
             } 
		
    			$searchNamecut = str_replace("'", "", $searchName);   //handle apostrophes in name
               $searchNamecut2 = str_replace("'", "", $searchName2);
               
               $searchNamecut = str_replace("@", "\@", $searchNamecut); //handle emails as name
              //inserted
              $found = false;
              for ($i=0; $i < count($answer); $i++)
              { //preg_match("/<Name>.*</\Name>/sU", $answer[0][$i], $testing); print_r($testing); //testing
                //print_r($answer[0][$i]); echo "<br>-------<br>";
                $nametocheck = str_replace("&apos;","", $answer[$i]);
                if (preg_match("/$searchNamecut/isU", $nametocheck, $cutAnswer)
			|| preg_match("/<Name>$searchNamecut2<\/Name>/isU", $nametocheck, $cutAnswer) ) 
                {		$found=true;
                	    $pos=$i;  break;
                }
              }
            //  error_log("searchname is " . $searchNamecut . " searchname2 is " . $searchNamecut2 . " \n answer is " . print_r($answer, 1), 3, sysConfig::getDirFsCatalog() . "error_log");
          if ($found) {
            preg_match("/<Id idDomain=\"(.*)\">(.*)<\/Id>/", $answer[$pos], $idAndDomain);
            $PaymentHeader->addCustomerId("{". $idAndDomain[1] . "-" . $idAndDomain[2] . "}");
              //$PaymentHeader->setARAccountName("Accounts Receivable");
              $Payment->addHeader($PaymentHeader);
              
              //$ServiceInvoice = new QuickBooks_IPP_Service_Invoice();
              //find invoice for payment
				$answer = array(); $records=0;
                for ($paging=1; $paging < 100; $paging++) {
             	//error_log("testing paging in payments(invoice)" . $paging, 3 , sysConfig::getDirFsCatalog() . "error_log");
             	$theid=$Service2->findAll($Context, $realmID, '', $paging);
             	//error_log("service " . $Service . " --- " . print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             	$savedArray = $answer;	
              	 if (preg_match_all("/<DocNumber>.*<\/DocNumber>/isU", $theid, $testanswer)) 
              	 { 
                	  preg_match_all("/<Invoice>.*<\/Invoice>/sU", $theid, $pageAnswer);
                	  //error_log("pageAnswer" . print_r($pageAnswer[0],1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // error_log("savedArray " . print_r($savedArray, 1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 //$answer = $savedArray + $pageAnswer[0];
                	  for($z=0; $z<count($pageAnswer[0]); $z++)
                	  {
                	  	$answer[$z+$records]=$pageAnswer[0][$z];
                	  }
                	   $records += count($pageAnswer[0]);
                	   //error_log("records is " . $records, 3, sysConfig::getDirFsCatalog() . "error_log");
              	 }
              	 else 
               		  break;
               	 
             } 
             
           
              $searchName = "Si-" . $payments[$t]['orders_id'];
              $pos=0; $found=false;
              //error_log("search name is " . $searchName . "answer is " . print_r($answer,1), 3, sysConfig::getDirFsCatalog() . "error_log");
            
              for ($i=0; $i < count($answer); $i++)
              { //preg_match("/<Name>.*</\Name>/sU", $answer[0][$i], $testing); print_r($testing); //testing
                //error_log3(print_r($answer[0][$i],1) .  "<br>-------<br>",3 ,sysConfig::getDirFsCatalog() . "error_log");
                
                
                if (preg_match("/$searchName/is", $answer[$i], $cutAnswer)) 
                {  //error_log3('searchname is ' . $searchName . 'in ' . print_r($answer[0][$i]), 3, sysConfig::getDirFsCatalog() . "error_log");
                	$found=true;
                    $pos=$i;  
                }
              }
              if ($found) {
          //echo "end of answer";
            preg_match("/<Invoice><Id idDomain=\"(.*)\">(.*)<\/Id><Sync/", $answer[0][$pos], $idAndDomain);
            
           
              $PaymentLine = new QuickBooks_IPP_Object_Line();
              $PaymentLine->setTxnId("{" . $idAndDomain[1] . "-" . $idAndDomain[2] . "}");
              //$PaymentLine->setTxnId("{NG-9151614}"); //testing
              //$PaymentLine->setTxnNum("11" . $orders['orders_id']);
              
              $PaymentLine->setAmount($payments[$t]['payment_amount']);
              
              //testing $PaymentLine->setDesc("Payment on order " . $payments[$t]['orders_id']); //desc for online, memo for windows?
      		//testing        $Payment->addLine($PaymentLine);
              //added 
             // $ServicePayment = new QuickBooks_IPP_Service_Payment();
                
			$answer = array(); $records=0;
                for ($paging=1; $paging < 100; $paging++) {
             	//error_log("testing paging in payments(payment)" . $paging, 3 , sysConfig::getDirFsCatalog() . "error_log");
             	  $theid=$ServicePayment->findAll($Context, $realmID, '', $paging);
             	//error_log("service " . $Service . " --- " . print_r($Service->lastRequest(),1) . "\n" . print_r($Service->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             	$savedArray = $answer;	
              	 if (preg_match_all("/<DocNumber>.*<\/DocNumber>/isU", $theid, $testanswer)) 
              	 { 
                	  preg_match_all("/<Payment>.*<\/Payment>/sU", $theid, $pageAnswer);
                	  //error_log("pageAnswer" . print_r($pageAnswer[0],1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	 // error_log("savedArray " . print_r($savedArray, 1), 3, sysConfig::getDirFsCatalog() . "error_log");
                	  //$answer = $savedArray + $pageAnswer[0];
                	   for($z=0; $z<count($pageAnswer[0]); $z++)
                	  {
                	  	$answer[$z+$records]=$pageAnswer[0][$z];
                	  }
                	   $records += count($pageAnswer[0]);
                	   //error_log("records is " . $records, 3, sysConfig::getDirFsCatalog() . "error_log");
              	 }
              	 else 
               		  break;
               	 
             } 
            $checkString = "/<DocNumber>SiPay-" . $payments[$t]['payment_history_id'] . "-" .  $payments[$t]['orders_id'] . "<\/DocNumber>/s";
            //$answer2=implode($answer);
            //error_log("count is " . count($answer) . " checkstring is " . $checkString . " answer is " . print_r($answer,1), 3, sysConfig::getDirFsCatalog() . "error_log");
            $found = false; $pos = 0;
               for ($i=0; $i < count($answer); $i++)
              { //preg_match("/<Name>.*</\Name>/sU", $answer[0][$i], $testing); print_r($testing); //testing
                //error_log3(print_r($answer[0][$i],1) .  "<br>-------<br>",3 ,sysConfig::getDirFsCatalog() . "error_log");
                
                
                if (preg_match($checkString, $answer[$i], $cutAnswer)) 
                {  //error_log3('searchname is ' . $searchName . 'in ' . print_r($answer[0][$i]), 3, sysConfig::getDirFsCatalog() . "error_log");
                	$found=true;
                    $pos=$i;  
                }
              }
            if (!$found)
            { 
              if ($ID = $ServicePayment->add($Context, $realmID, $Payment))
               { 
               //echo "added payment " . $payments[$t]['payment_history_id'];
                  error_log(date("H:i:s") . 'added payment ' . $payments[$t]['payment_history_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
             error_log(print_r($ServicePayment->lastRequest(),1) .  "\n" . print_r($ServicePayment->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             
               // echo "<br>" . print_r($ServicePayment->lastRequest()) . "<br>---" . print_r($ServicePayment->lastResponse());
            
               } 
              else
		{
			  //echo "problem adding payment " . $payments[$t]['payment_history_id'];
			     error_log(date("H:i:s") . 'problem adding payment ' . $payments[$t]['payment_history_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
             error_log(print_r($ServicePayment->lastRequest(),1) .  "\n" . print_r($ServicePayment->lastResponse(),1), 3, sysConfig::getDirFsCatalog() . "error_log");
             
		        //echo "<br>" . print_r($ServicePayment->lastRequest());
             
                //echo "<br>"  . $ServicePayment->lastResponse();
         
               
        
		  
		 }
            }
            else 
              { 
            // echo "Payment SiPay-" . $payments[$t]['payment_history_id'] . "-" . $payments[$t]['orders_id'] . "already added.";
             error_log('payment duplicate SiPay-' . $payments[$t]['payment_history_id'] . "-" . $payments[$t]['orders_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
             //echo "check string is" . $checkString; print_r($invoices); exit;
             
            }
              }
		 else {// echo "There was a problem (no invoice found) for payment " . $payments[$t]['payment_history_id'];
		    error_log(date("H:i:s") . 'problem (no invoice found) adding payment ' . $payments[$t]['payment_history_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
             

		 }
              
             
             // time_nanosleep(0,100000000);  //cannot exceed threshold of 500 hits per minute at Intuit
             // error_log(date("H:i:s") . 'done payment '  . $payments[$t]['payment_history_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
              
              }
    
     else {
       error_log("customer not found payment - " . $payments[$t]['payment_history_id'] . "\n", 3, sysConfig::getDirFsCatalog() . "error_log");
    }
 //} //testing - run specific payments
              
              }
       
            
      error_log("end" . date("H:i:s"), 3, sysConfig::getDirFsCatalog() . "error_log");        
    echo "<br>" . sysLanguage::get('QB_COMPLETE');
    echo mail(sysConfig::get('EXTENSION_MANAGE_QUICKBOOKS_EMAIL'), "QuickBooks Export", "The SalesIgniter QuickBooks Export has completed.");
   

    
    
}

?>
