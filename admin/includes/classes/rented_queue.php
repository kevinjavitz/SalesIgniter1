<?php
/*
  $Id: order.php,v 1.33 2003/06/09 22:25:35 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class rented_queue {
    var $info, $customer, $delivery;

    function rented_queue($queue_id = '') {
      $this->info = array();
      $this->customer = array();
      $this->delivery = array();

      if (tep_not_null($queue_id)) {
        $this->query($queue_id);
      }
    }

    function query($queue_id) {
      $queue_id = tep_db_prepare_input($queue_id);

      $rented_query = tep_db_query("select customers_id, products_id, products_barcode, date_added from " . TABLE_RENTED_QUEUE . " where customers_queue_id = '" . (int)$queue_id . "'");
      if (!tep_db_num_rows($rented_query) && (isset($_POST['report_filter']) && ($_POST['report_filter'] == 'all' || $_POST['report_filter'] == 'onetime_rental'))){
          $rented_query = tep_db_query("select customers_id, products_id, products_barcode, date_added from rental_bookings where rental_booking_id = '" . (int)$queue_id . "'");
      }
      $rented = tep_db_fetch_array($rented_query);

      $customer_address_query = tep_db_query("select c.customers_firstname, c.customers_lastname, c.customers_telephone, c.customers_email_address, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, co.countries_id, co.countries_name, co.countries_iso_code_2, co.countries_iso_code_3, co.address_format_id, ab.entry_state from " . TABLE_CUSTOMERS . " c, " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " co on (ab.entry_country_id = co.countries_id) where c.customers_id = '" . $rented['customers_id'] . "' and ab.customers_id = '" . $rented['customers_id'] . "' and c.rental_address_id = ab.address_book_id");
      $customer_address = tep_db_fetch_array($customer_address_query);

      $this->info = array('products_id' => $rented['products_id'],
                          'date_added' => $rented['date_added'],
                          'products_barcode' => $rented['products_barcode']);

      $this->customer = array('id' => $customers['customers_id'],
                              'firstname' => $customer_address['customers_firstname'],
                              'lastname' => $customer_address['customers_lastname'],
                              'telephone' => $customer_address['customers_telephone'],
                              'email_address' => $customer_address['customers_email_address']);

     $this->delivery = array('firstname' => $customer_address['entry_firstname'],
                              'lastname' => $customer_address['entry_lastname'],
                              'company' => $customer_address['entry_company'],
                              'street_address' => $customer_address['entry_street_address'],
                              'suburb' => $customer_address['entry_suburb'],
                              'city' => $customer_address['entry_city'],
                              'postcode' => $customer_address['entry_postcode'],
                              'state' => ((tep_not_null($customer_address['entry_state'])) ? $customer_address['entry_state'] : $customer_address['zone_name']),
                              'zone_id' => $customer_address['entry_zone_id'],
                              'country' => $customer_address['countries_name'],
                              'format_id' => $customer_address['address_format_id']);

	}
  }
?>