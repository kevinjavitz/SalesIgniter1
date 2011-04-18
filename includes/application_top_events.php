<?php
// Email Template Manager Start
define('ORDER_UPDATE_EMAIL', '1');
define('ORDER_PROCESS_EMAIL', '2');
define('RECOVER_CART_SALES_EMAIL', '3');
define('RENTAL_SENT_EMAIL', '4');
define('RENTAL_RETURNED_EMAIL', '5');
define('RENTAL_ISSUES_EMAIL', '6');
define('NEW_RENTAL_CUSTOMER_EMAIL', '7');
define('CREATE_ACCOUNT_EMAIL', '8');
define('ONETIME_RENTAL_SENT_EMAIL', '9');
define('ONETIME_RENTAL_RETURNED_EMAIL', '10');
define('ORDER_SUCCESS_EMAIL', '11');
define('RENTAL_ORDER_SUCCESS_EMAIL', '12');
define('MEMBERSHIP_CANCEL_REQUEST_EMAIL', '13');
define('AFFILIATE_PASSWORD_FORGOTTEN_EMAIL', '14');
define('AFFILIATE_CREATE_ACCOUNT_EMAIL', '15');
define('GIFT_VOUCHER_SEND_EMAIL', '16');
define('PASSWORD_FORGOTTEN_EMAIL', '17');
define('TELL_A_FRIEND_EMAIL', '18');
define('ADMIN_MEMBERSHIP_UPGRADED_EMAIL', '19');
define('ADMIN_MEMBERSHIP_ACTIVATED_EMAIL', '20');
define('ADMIN_MEMBERSHIP_CANCELED_EMAIL', '21');
define('MEMBERSHIP_EXPIRED_EMAIL', '22');
define('MEMBERSHIP_RENEWAL_FAIL', '23');
define('RENTAL_QUEUE_EMPTY_EMAIL', '24');
define('RENTAL_ORDER_SUCCESS_ADMIN_EMAIL', '25');
define('ORDER_INVENTORY_SUCCESS_EMAIL', '26');
define('ORDER_UPDATE_EMAIL_INVENTORY', '27');
define('TALK_TO_US_EMAIL', '28');

$events_array = array(
	array('id' => ADMIN_MEMBERSHIP_ACTIVATED_EMAIL,   'type' => 'admin', 'text' => '(Admin) Membership activated email'),
	array('id' => ADMIN_MEMBERSHIP_CANCELED_EMAIL,    'type' => 'admin', 'text' => '(Admin) Membership cancelled email'),
	array('id' => ADMIN_MEMBERSHIP_UPGRADED_EMAIL,    'type' => 'admin', 'text' => '(Admin) Membership upgraded email'),
	array('id' => ONETIME_RENTAL_SENT_EMAIL,          'type' => 'admin', 'text' => '(Admin) Onetime rental sent email'),
	array('id' => ONETIME_RENTAL_RETURNED_EMAIL,      'type' => 'admin', 'text' => '(Admin) Onetime rental returned email'),
    array('id' => ORDER_UPDATE_EMAIL,                 'type' => 'admin', 'text' => '(Admin) Order update email'),
	array('id' => ORDER_PROCESS_EMAIL,                'type' => 'admin', 'text' => '(Admin) Order process email'),
	array('id' => RECOVER_CART_SALES_EMAIL,           'type' => 'admin', 'text' => '(Admin) Recover cart sales email'),
	array('id' => RENTAL_SENT_EMAIL,                  'type' => 'admin', 'text' => '(Admin) Rental sent email'),
	array('id' => RENTAL_RETURNED_EMAIL,              'type' => 'admin', 'text' => '(Admin) Rental returned email'),
	array('id' => RENTAL_ISSUES_EMAIL,                'type' => 'admin', 'text' => '(Admin) Rental issues email'),
	array('id' => RENTAL_QUEUE_EMPTY_EMAIL,           'type' => 'admin', 'text' => '(Admin) Rental queue empty email'),
	array('id' => CREATE_ACCOUNT_EMAIL,               'type' => 'site',  'text' => '(Site) Account creation email'),
	array('id' => AFFILIATE_CREATE_ACCOUNT_EMAIL,     'type' => 'site',  'text' => '(Site) Affiliate create account email'),
	array('id' => AFFILIATE_PASSWORD_FORGOTTEN_EMAIL, 'type' => 'site',  'text' => '(Site) Affiliate password forgotten email'),
	array('id' => GIFT_VOUCHER_SEND_EMAIL,            'type' => 'site',  'text' => '(Site) Gift voucher send email'),
	array('id' => MEMBERSHIP_CANCEL_REQUEST_EMAIL,    'type' => 'site',  'text' => '(Site) Membership cancel request email'),
	array('id' => NEW_RENTAL_CUSTOMER_EMAIL,          'type' => 'site',  'text' => '(Site) New rental customer email'),
	array('id' => ORDER_SUCCESS_EMAIL,                'type' => 'site',  'text' => '(Site) Order success email'),
	array('id' => PASSWORD_FORGOTTEN_EMAIL,           'type' => 'site',  'text' => '(Site) Password forgotten email'),
	array('id' => RENTAL_ORDER_SUCCESS_EMAIL,         'type' => 'site',  'text' => '(Site) Rental order success email'),
	array('id' => TELL_A_FRIEND_EMAIL,                'type' => 'site',  'text' => '(Site) Tell a friend email'),
	array('id' => TALK_TO_US_EMAIL,                   'type' => 'site',  'text' => '(Site) Talk to us email'),
	array('id' => MEMBERSHIP_EXPIRED_EMAIL,           'type' => 'site',  'text' => '(Site) Membership expired email'),
	array('id' => MEMBERSHIP_RENEWAL_FAIL,            'type' => 'site',  'text' => '(Site) Membership payment failure email'),
	array('id' => RENTAL_ORDER_SUCCESS_ADMIN_EMAIL,   'type' => 'site',  'text' => '(Site) Admin email for membership'),
	array('id' => ORDER_INVENTORY_SUCCESS_EMAIL,       'type' => 'site',  'text' => '(Site) Inventory Owner Email when a order is placed'),
	array('id' => ORDER_UPDATE_EMAIL_INVENTORY,       'type' => 'site',  'text' => '(Site) Email from Inventory Owner when order is approved or cancelled')
);

$events_vars['global'] = array(
    'store_name',
    'store_owner',
    'store_owner_email',
    'today_short',
    'today_long',
    'store_url'
);

$events_vars[ORDER_UPDATE_EMAIL] = array(
    'condition_vars' => array(
        'trackingLinks' => array(
            'trackingLinks'
        ),
        'adminComments' => array(
            'adminComments'
        ),
        'historyLink' => array(
            'historyLink'
        )
    ),
	'full_name',
	'orderID',
	'status',
	'datePurchased',
	'orderedProducts',
	'orderTotals'
);

$events_vars[ORDER_PROCESS_EMAIL] = array(
    'condition_vars' => array(
        'trackingLinks' => array(
            'trackingLinks'
        ),
        'adminComments' => array(
            'adminComments'
        ),
        'historyLink' => array(
            'historyLink'
        )
    ),
	'full_name',
	'orderID',
	'status',
	'datePurchased',
	'orderedProducts',
	'orderTotals'
);

$events_vars[RECOVER_CART_SALES_EMAIL] = array(
    'cartContents',
    'loginLink',
    'firstname',
    'lastname',
    'full_name'
);

$events_vars[RENTAL_SENT_EMAIL] = array(
	'firstname',
	'lastname',
	'full_name',
	'rentedProduct',
	'requestDate',
	'shipmentDate',
	'arrivalDate'
);

$events_vars[RENTAL_RETURNED_EMAIL] = array(
	'firstname',
	'lastname',
	'full_name',
	'rented_product'
);

$events_vars[RENTAL_ISSUES_EMAIL] = array(
	'firstname',
	'lastname',
	'full_name',
	'issueID',
	'issueDetails'
);

$events_vars[CREATE_ACCOUNT_EMAIL] = array(
    'condition_vars' => array(
        'signupVoucher' => array(
            'signupVoucherAmount',
            'signupVoucherCode',
            'signupVoucherLink'
        ),
        'signupCoupon' => array(
            'signupCouponDescription',
            'signupCouponCode'
        ),
        'password' => array(
            'password'
        )
    ),
    'full_name',
    'firstname',
    'lastname',
    'email_address'
);

$events_vars[NEW_RENTAL_CUSTOMER_EMAIL] = array(
    'condition_vars' => array(
        'signupVoucher' => array(
            'signupVoucherAmount',
            'signupVoucherCode',
            'signupVoucherLink'
        ),
        'signupCoupon' => array(
            'signupCouponDescription',
            'signupCouponCode'
        )
    ),
    'full_name',
    'firstname',
    'lastname',
	'packageName',
	'membershipPeriod',
	'numberOfTitles',
	'price',
	'tax'
);
	
$events_vars[ONETIME_RENTAL_RETURNED_EMAIL] = array(
	'full_name',
	'email_address',
	'days_late',
	'rented_product'
);

$events_vars[ONETIME_RENTAL_SENT_EMAIL] = array(
    'full_name',
	'rented_product',
	'due_date'
);

$events_vars[ORDER_SUCCESS_EMAIL] = array(
    'condition_vars' => array(
        'order_comments' => array(
            'order_comments'
        ),
        'shipping_address' => array(
            'shipping_address'
        ),
        'pickup_address' => array(
            'pickup_address'
        ),
        'po_number' => array(
            'po_number'
        ),
        'payment_footer' => array(
            'payment_footer'
        ),
	    'terms' => array(
            'terms'
        ),
	    'event_description' => array(
            'event_description'
        )
    ),
    'order_id',
	'invoice_link',
	'date_ordered',
	'ordered_products',
	'orderTotals',
	'billing_address',
	'paymentTitle'
);

$events_vars[RENTAL_ORDER_SUCCESS_EMAIL] = array(
    'condition_vars' => array(
        'newAccount' => array(),
        'renewAccount' => array(),
        'upgradeAccount' => array(),
    ),
	'customerFirstName',
	'customerLastName',
	'currentPlanPackageName',
	'currentPlanMembershipDays',
	'actionText',
	'currentPlanNumberOfTitles',
	'currentPlanFreeTrial',
	'currentPlanPrice'
);
$events_vars[RENTAL_ORDER_SUCCESS_ADMIN_EMAIL] = array_merge($events_vars[RENTAL_ORDER_SUCCESS_EMAIL], array(
	'adminSubject',
	'full_name',
	'emailAddress',
	'customerId'
));

$events_vars[MEMBERSHIP_CANCEL_REQUEST_EMAIL] = array(
    'condition_vars' => array(
        'membershipIsMonths' => array(
            'membershipPeriod'
        ),
        'membershipIsDays' => array(
            'membershipPeriod'
        )
    ),
	'customerID',
	'full_name',
	'emailAddress',
	'paymentMethod',
	'subscriptionDate',
	'planID',
	'packageName',
	'numberOfRentals',
	'freeTrialPeriod',
	'price'
);

$events_vars[AFFILIATE_PASSWORD_FORGOTTEN_EMAIL] = array(
    'firstname',
    'lastname',
    'full_name',
    'newPassword',
    'requestIP'
);

$events_vars[AFFILIATE_CREATE_ACCOUNT_EMAIL] = array(
    'firstname',
    'lastname',
    'full_name',
    'affiliateID',
    'emailAddress',
    'password',
    'affiliatePageLink'
);

$events_vars[GIFT_VOUCHER_SEND_EMAIL] = array(
    'voucherAmount',
    'voucherID',
    'voucherLink',
    'sentFrom',
    'sentTo',
    'message'
);

$events_vars[PASSWORD_FORGOTTEN_EMAIL] = array(
    'firstname',
    'lastname',
    'full_name',
    'newPassword',
    'requestIP'
);

$events_vars[TELL_A_FRIEND_EMAIL] = array(
    'condition_vars' => array(
        'message' => array(
            'message'
        )
    ),
    'fromName',
    'toName',
    'productsName',
    'productsLink',
    'catalogLink'
);

$events_vars[ADMIN_MEMBERSHIP_UPGRADED_EMAIL] = array(
    'condition_vars' => array(
        'previousPlanMembershipDays' => array(
            'previousPlanMembershipDays'
        ),
        'currentPlanMembershipDays' => array(
            'currentPlanMembershipDays'
        )
    ),
    'customerFirstName',
    'customerLastName',
    'previousPlanPackageName',
    'previousPlanNumberOfTitles',
    'previousPlanFreeTrial',
    'previousPlanPrice',
    'currentPlanPackageName',
    'currentPlanNumberOfTitles',
    'currentPlanFreeTrial',
    'currentPlanPrice'
);

$events_vars[ADMIN_MEMBERSHIP_ACTIVATED_EMAIL] = array(
    'condition_vars' => array(
        'currentPlanMembershipDays' => array(
            'currentPlanMembershipDays'
        )
    ),
    'customerFirstName',
    'customerLastName',
    'currentPlanPackageName',
    'currentPlanNumberOfTitles',
    'currentPlanFreeTrial',
    'currentPlanPrice'
);

$events_vars[ADMIN_MEMBERSHIP_CANCELED_EMAIL] = array(
    'condition_vars' => array(
        'currentPlanMembershipDays' => array(
            'currentPlanMembershipDays'
        )
    ),
    'customerFirstName',
    'customerLastName',
    'currentPlanPackageName',
    'currentPlanNumberOfTitles',
    'currentPlanFreeTrial',
    'currentPlanPrice'
);

$events_vars[MEMBERSHIP_EXPIRED_EMAIL] = array(
    'condition_vars' => array(
        'membershipIsMonths' => array(
            'membershipPeriod'
        ),
        'membershipIsDays' => array(
            'membershipPeriod'
        )
    ),
	'customerID',
	'full_name',
	'emailAddress',
	'paymentMethod',
	'subscriptionDate',
	'planID',
	'packageName',
	'numberOfRentals',
	'freeTrialPeriod',
	'price'
);

$events_vars[MEMBERSHIP_RENEWAL_FAIL] = array(
	'customerFullName',
	'declineReason'
);

$events_vars[RENTAL_QUEUE_EMPTY_EMAIL] = array(
	'customer_firstname',
	'customer_lastname',
	'customerFullName'
);

$events_vars[ORDER_INVENTORY_SUCCESS_EMAIL] = array(
    'order_id',
	'inv_address',
	'invoice_link',
	'date_ordered',
	'ordered_products'

);

$events_vars[ORDER_UPDATE_EMAIL_INVENTORY] = array(
	'full_name',
	'orderID',
	'status',
	'datePurchased',
	'orderedProducts',
	'orderTotals',
	'inv_address',
	'deliveryInstructions'
);

$events_vars[TALK_TO_US_EMAIL] = array(
	'condition_vars' => array(
        'message' => array(
            'message'
        )
    )
);

$TEMP_PATH = DIR_FS_CATALOG.'templates/email/';
// Email Template Manager End
?>