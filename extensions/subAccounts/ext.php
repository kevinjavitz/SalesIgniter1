<?php


class Extension_subAccounts extends ExtensionBase {

	/**
	 * class constructor
	 * @public
	 * @return void
	 */
	public function __construct() {
		parent::__construct('subAccounts');
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Initialize this class. (loaded by core)
	 *
	 * @public
	 * @return void
	 */
	public function init() {
		global $App, $appExtension, $Template;
		if ($this->enabled === FALSE) return;


		EventManager::attachEvents(array(
			'ProductListingQueryBeforeExecute',
			'RentalQueueProductSent',
			'ProcessLoginBeforeExecute',
			'ProcessLogoutExecute',
			'CheckoutBeforeExecute',
			'AccountDefaultMyAccountBeforeDrawLinks',
			'RentalQueueBeforeInsert',
			'RentalQueueBeforeExecute',
			'ListingRentalQueue',
			'ListingRentalQueueHeader'
		), null, $this);

	}

	public function RentalQueueBeforeInsert(&$Item){
		if(Session::exists('childrenAccount')){
			$Item->children_customers_id = Session::get('childrenAccount');
		}
	}

	public function RentalQueueProductSent(&$RentedProduct, $QproductsQueue){
		$RentedProduct->children_customers_id = $QproductsQueue['children_customers_id'];
	}

	public function ListingRentalQueueHeader(&$info_box_contents){
		global $userAccount;
		if(Session::exists('childrenAccount')){

		}else{
			$QHasChildren = Doctrine_Query::create()
			->from('Customers')
			->where('parent=?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if($QHasChildren){
				$info_box_contents[] = array(
					'align' => 'center',
					'params' => 'class="ui-widget-header"',
					'text' => sysLanguage::get('TABLE_HEADING_SUBACCOUNT')
				);
			}
		}
	}

	public function RentalQueueBeforeExecute(&$Qrental){
		if(Session::exists('childrenAccount')){
			$Qrental->andWhere('children_customers_id=?', Session::get('childrenAccount'));
		}
	}

	public function ListingRentalQueue(&$info_box_contents, $rInfo){
		global $userAccount;
		if(Session::exists('childrenAccount')){

		}else{

			$QHasChildren = Doctrine_Query::create()
			->from('Customers')
			->where('customers_id=?', $rInfo['children_customers_id'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if($QHasChildren){
				$info_box_contents[] = array(
					'align' => 'center',
					'params' => 'class="productListing-data" valign="top"',
					'text' => $QHasChildren[0]['customers_firstname'].' '.$QHasChildren[0]['customers_lastname']
				);
			}

		}

	}



	public function AccountDefaultMyAccountBeforeDrawLinks(&$links, &$rentalLinksList){
		if(Session::exists('childrenAccount')){
			$links = htmlBase::newElement('a')->html(sysLanguage::get('TEXT_CHANGE_PASSWORD'))
			->setHref(itw_app_link('appExt=subAccounts', 'manage', 'changePassword'))->draw();

			$rentalLinksList = null;

		}
	}

	public function CheckoutBeforeExecute(){
		if(Session::exists('childrenAccount')){
			tep_redirect(itw_app_link(null, 'account', 'default'));
		}
	}

	public function ProcessLogoutExecute(){
		if(Session::exists('childrenAccount')){
			Session::remove('childrenAccount');
		}
	}

	public function ProcessLoginBeforeExecute(&$noValidate, $password, &$Qcustomer){
		global $userAccount;
		if($userAccount->validatePassword($password, $Qcustomer[0]->customers_password)){
			if($Qcustomer[0]->parent != 0){
				Session::set('childrenAccount', $Qcustomer[0]->customers_id);
				$Qcustomer[0]->customers_id = $Qcustomer[0]->parent;

			}
			$noValidate = true;
		}
	}


}



?>
