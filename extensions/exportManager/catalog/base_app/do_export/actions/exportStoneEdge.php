<?php
//here I make the username and password check
//and include the needed action based on setfunction.?

function StoneEdgeCount($table,$whereCond = '')
{
	if($table == 'products') {
		$count = StoneEdgeProductQueryCount();

	} else {
		$query = Doctrine_Query::create()
			->from($table);

		if(tep_not_null($whereCond))
			$query->where($whereCond);

		$query->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
		$count = $query->count();
	}
	//it's ok to return zero.
	return $count;
}

/**
 * Return a valid SQL query
 *
 * Method to create a common SQL query for the products
 *
 * @access public
 * @return string the valid SQL query.
 */
function StoneEdgeProductQuery($end='')
{

	$Qproducts = Doctrine_Query::create()
			->select('piq.*,pi.*,p.*,pd.*')
			->from('Products p')
			->leftJoin('p.ProductsInventory pi')
			->leftJoin('pi.ProductsInventoryQuantity piq')
			->leftJoin('p.ProductsDescription pd')
			->where('pd.language_id = ?', Session::get('languages_id'))
			->andWhere('pi.type = "new" OR pi.type = "used"')
			->andWhere('piq.available > 0')
			->andWhere('p.products_status = 1')
			->orderBy('p.products_id')
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

	return $Qproducts;	
}

/**
 * Return a valid SQL query
 *
 * Method to create a common SQL query for the product row count
 *
 * @access public
 * @return string the valid SQL query.
 */

function StoneEdgeProductQueryCount($end='')
{
	$Qprod = StoneEdgeProductQuery();
	return $Qprod->count();
}

/**
 * Return a valid SQL query
 *
 * Method to create a common SQL query for the orders
 *
 * @access public
 * @return string the valid SQL query.
 */

function StoneEdgeOrderQuery($whereCond="o.orders_status > 0")
{
	$Qorders = Doctrine_Query::create()
	->select('o.orders_id, a.entry_name, o.date_purchased, o.customers_id, o.last_modified, o.currency, o.currency_value, s.orders_status_name, ot.text as order_total, o.payment_module')
	->from('Orders o')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersAddresses a')
	->leftJoin('o.OrdersStatus s')
	->where('s.language_id = ?', Session::get('languages_id'))
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhere('a.address_type = ?', 'customer')
	->orderBy('o.date_purchased desc');
}

/**
 * Return a valid SQL query
 *
 * Method to create a common SQL query for the product row count
 *
 * @access public
 * @return string the valid SQL query.
 */

function StoneEdgeOrderQueryCount()
{
	$Qord = StoneEdgeOrderQuery();
	return $Qord->count(); 
}

require_once(dirname(__FILE__) . "/exportCustomersStoneEdge.php");
require_once(dirname(__FILE__) . "/exportOrdersStoneEdge.php");
require_once(dirname(__FILE__) . "/exportProductsStoneEdge.php");

$GLOBALS['XMLReturn'] = '';
error_reporting(0);

//check for version number request post - this is done by SEOM before each communication to prove that the script exists.
if (isset($_REQUEST['setifunction']) && $_REQUEST['setifunction'] == 'sendversion') {
	if(isset($_REQUEST['omversion'])) {
		$version = $_REQUEST['omversion'];
		echo "SETIResponse: version=$version";
		die();
	} else {//it's not a required field so it may not be there...still need to return something
		echo "SETIResponse: version=5.500";
		die();
	}
}

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'off') {

	//check for admin username and password in post, then validate.
	if (isset($_REQUEST['setiuser']) && $_REQUEST['setiuser'] != '' && isset($_REQUEST['password']) && $_REQUEST['password'] != '') {
		// then they posted their user and password, so check against the db

		if ($_REQUEST['setiuser'] == EXTENSION_EXPORT_MANAGER_USERNAME && $_REQUEST['password'] == EXTENSION_EXPORT_MANAGER_PASSWORD) {// validated

			switch ($_REQUEST['setifunction']) {
				//process order requests - setifunction values: ordercount or downloadorders
				case 'ordercount':
					//$SEOMOrder = new ACCOUNTING_STONEEDGE_ORDERS();
					$GLOBALS['XMLReturn'] .= ProcessOrderCount();
					break;

				case 'downloadorders':
					$GLOBALS['XMLReturn'] .= DownloadOrders();
					//header("Content-type: text/xml");
					break;

				//process customer requests - setifunction values: getcustomerscount or downloadcustomers
				case 'getcustomerscount':

					$GLOBALS['XMLReturn'] .= ProcessCustomerCount();
					break;

				case 'downloadcustomers':

					header("Content-type: text/xml");
					$GLOBALS['XMLReturn'] .= DownloadCustomers();
					break;

				//process product requests - setifunction values: getproductscount or downloadprods
				case 'getproductscount':

					$GLOBALS['XMLReturn'] .= ProcessProductCount();
					break;

				case 'downloadprods':

					header("Content-type: text/xml");
					$GLOBALS['XMLReturn'] .= DownloadProducts();
					break;

				//process inventory requests - setifunction values: downloadqoh, qohreplace, or invupdate
				case 'downloadqoh':

					header("Content-type: text/xml");
					$GLOBALS['XMLReturn'] .= DownloadQuantities();
					break;

				case 'qohreplace':

					$GLOBALS['XMLReturn'] .= ReplaceQuantity();
					break;

				case 'invupdate':

					$GLOBALS['XMLReturn'] .= UpdateInventory();
					break;
				default:
					//this shouldn't ever happen, return an error message
					header("Content-type: text/xml");
					$GLOBALS['XMLReturn'] .= '<?xml version="1.0" encoding="UTF-8" ?>';
					$GLOBALS['XMLReturn'] .= "<SETIError>";
						$GLOBALS['XMLReturn'] .= "<Response>";
							$GLOBALS['XMLReturn'] .= "<ResponseCode>3</ResponseCode>";
							$GLOBALS['XMLReturn'] .= "<ResponseDescription>Error: There was an error in the transmission sent from Stone Edge Order Manager.</ResponseDescription>";
						$GLOBALS['XMLReturn'] .= "</Response>";
					$GLOBALS['XMLReturn'] .= "</SETIError>";
					break;
			}
		} else {
			//then the username or password was invalid so create an error message and kill the page.

			header("Content-type: text/xml");
			$GLOBALS['XMLReturn'] .= '<?xml version="1.0" encoding="UTF-8" ?>';
			$GLOBALS['XMLReturn'] .= "<SETIError>";
				$GLOBALS['XMLReturn'] .= "<Response>";
					$GLOBALS['XMLReturn'] .= "<ResponseCode>3</ResponseCode>";
					$GLOBALS['XMLReturn'] .= "<ResponseDescription>Error: Either the username or password was invalid.</ResponseDescription>";
				$GLOBALS['XMLReturn'] .= "</Response>";
			$GLOBALS['XMLReturn'] .= "</SETIError>";
		}
	} else {
		//then they didn't post a user or password so create an error message and kill the page.
			header("Content-type: text/xml");
			$GLOBALS['XMLReturn'] .= '<?xml version="1.0" encoding="UTF-8" ?>';
			$GLOBALS['XMLReturn'] .= "<SETIError>";
				$GLOBALS['XMLReturn'] .= "<Response>";
					$GLOBALS['XMLReturn'] .= "<ResponseCode>3</ResponseCode>";
					$GLOBALS['XMLReturn'] .= "<ResponseDescription>Error: Either a username or password was not provided. Please check your settings in Stone Edge Order Manager and enter a username and password for this shopping cart.</ResponseDescription>";
				$GLOBALS['XMLReturn'] .= "</Response>";
			$GLOBALS['XMLReturn'] .= "</SETIError>";
	}
} else {
	//return error saying that the URL entered into order manager wasn't https://
	header("Content-type: text/xml");
	$GLOBALS['XMLReturn'] .= '<?xml version="1.0" encoding="UTF-8" ?>';
	$GLOBALS['XMLReturn'] .= "<SETIError>";
		$GLOBALS['XMLReturn'] .= "<Response>";
			$GLOBALS['XMLReturn'] .= "<ResponseCode>3</ResponseCode>";
			$GLOBALS['XMLReturn'] .= "<ResponseDescription>Error: The URL entered into Stone Edge Order Manager is for an insecure connection. Please make sure that the URL begins with 'https://'</ResponseDescription>";
		$GLOBALS['XMLReturn'] .= "</Response>";
	$GLOBALS['XMLReturn'] .= "</SETIError>";
}

echo $GLOBALS['XMLReturn'];
//die($GLOBALS['XMLReturn']);


?>