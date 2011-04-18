    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php // Are we doing an e-mail to some customers?
if (count($custid) > 0 ) {  ?>
            <tr>
              <td class="pageHeading" align="left" colspan=2 width="50%"><? echo sysLanguage::get('HEADING_TITLE'); ?> </td>
              <td class="pageHeading" align="left" colspan=4 width="50%"><? echo HEADING_EMAIL_SENT; ?> </td>
            </tr>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left" colspan="1" width="15%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMER'); ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="30%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="25%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="10%" nowrap>&nbsp;</td>
            </tr><tr>&nbsp;<br></tr>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left"   colspan="1"  width="15%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_MODEL'); ?></td>
              <td class="dataTableHeadingContent" align="left"   colspan="2"  width="55%" nowrap><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
              <td class="dataTableHeadingContent" align="center" colspan="1"  width="10%" nowrap> <?php echo TABLE_HEADING_QUANTY; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_PRICE'); ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="10%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_TOTAL'); ?></td>
            </tr>
<?php
	foreach ($custid as $cid)
	{
	  $query1 = tep_db_query("select cb.products_id pid,
                                    cb.customers_basket_quantity qty,
                                    cb.customers_basket_date_added bdate,
                                    cus.customers_firstname fname,
                                    cus.customers_lastname lname,
                                    cus.customers_email_address email
                          from      " . TABLE_CUSTOMERS_BASKET . " cb,
                                    " . TABLE_CUSTOMERS . " cus
                          where     cb.customers_id = cus.customers_id  and
                                    cus.customers_id = '".$cid."'
                          order by  cb.customers_basket_date_added desc ");

	  $knt = mysql_num_rows($query1);
	  for ($i = 0; $i < $knt; $i++)
	  {
		 $inrec = tep_db_fetch_array($query1);

		// set new cline and curcus
		 if ($lastcid != $cid) {
			if ($lastcid != "") {
			  $cline .= "
			  <tr>
				 <td class='dataTableContent' align='right' colspan='6' nowrap><b>" . TABLE_CART_TOTAL . "</b>" . $currencies->format($tprice) . "</td>
			  </tr>
			  <tr>
				 <td colspan='6' align='right'>" . htmlBase::newElement('button')->usePreset('delete')->setHref(tep_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=" . $cid . "&tdate=" . $tdate . "&sdate=" . $sdate))->draw() . "</td>
			  </tr>\n";
			 echo $cline;
			}
			$cline = "<tr> <td class='dataTableContent' align='left' colspan='6' nowrap><a href='" . tep_href_link(FILENAME_CUSTOMERS, 'search=' . $inrec['lname'], 'NONSSL') . "'>" . $inrec['fname'] . " " . $inrec['lname'] . "</a>".$customer."</td></tr>";
			$tprice = 0;
		  }
		  $lastcid = $cid;

		// get the shopping cart
		$query2 = tep_db_query("select   p.products_price price,
												p.products_tax_class_id taxclass,
												p.products_model model,
                                    pd.products_name name
                            from    " . TABLE_PRODUCTS . " p,
                                    " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                                    " . TABLE_LANGUAGES . " l
                            where   p.products_id = '" . $inrec['pid'] . "' and
                                    pd.products_id = p.products_id and
                                    pd.language_id = " . (int)Session::get('languages_id') );

		$inrec2 = tep_db_fetch_array($query2);
		$sprice = tep_get_products_special_price( $inrec['pid'] );
		if( $sprice < 1 )
			$sprice = $inrec2['price'];
		// Some users may want to include taxes in the pricing, allow that. NOTE HOWEVER that we don't have a good way to get individual tax rates based on customer location yet!
			if( RCS_INCLUDE_TAX_IN_PRICES  == 'true' )
				$sprice += ($sprice * tep_get_tax_rate( $inrec2['taxclass'] ) / 100);
			else if( RCS_USE_FIXED_TAX_IN_PRICES  == 'true' && RCS_FIXED_TAX_RATE > 0 )
				$sprice += ($sprice * RCS_FIXED_TAX_RATE / 100);

		$tprice = $tprice + ($inrec['qty'] * $sprice);
      $pprice_formated  = $currencies->format($sprice);
      $tpprice_formated = $currencies->format(($inrec['qty'] * $sprice));

      $cline .= "<tr class='dataTableRow'>
                    <td class='dataTableContent' align='left'   width='15%' nowrap>" . $inrec2['model'] . "</td>
                    <td class='dataTableContent' align='left'  colspan='2' width='55%'><a href='" . tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $inrec['pid'] . '&origin=' . FILENAME_RECOVER_CART_SALES . '?page=' . $_GET['page'], 'NONSSL') . "'>" . $inrec2['name'] . "</a></td>
                    <td class='dataTableContent' align='center' width='10%' nowrap>" . $inrec['qty'] . "</td>
                    <td class='dataTableContent' align='right'  width='10%' nowrap>" . $pprice_formated . "</td>
                    <td class='dataTableContent' align='right'  width='10%' nowrap>" . $tpprice_formated . "</td>
                 </tr>";

		$mline .= $inrec['qty'] . ' x ' . $inrec2['name'] . "\n";

		if( EMAIL_USE_HTML == 'true' )
			$mline .= '   <blockquote><a href="' . itw_catalog_app_link('products_id='. $inrec['pid'], 'product', 'info') . '">' . itw_catalog_app_link('products_id='. $inrec['pid'], 'product', 'info') . "</a></blockquote>\n\n";
		else
			$mline .= '   (' . itw_catalog_app_link('products_id='. $inrec['pid'], 'product', 'info').")\n\n";
	  }

	  $cline .= "</td></tr>";


		// E-mail Processing - Requires EMAIL_* defines in the
		// includes/languages/english/recover_cart_sales.php file
		$cquery = tep_db_query("select * from orders where customers_id = '".$cid."'" );
		$email = EMAIL_TEXT_LOGIN;

		if( EMAIL_USE_HTML == 'true' )
			$email .= '  <a HREF="' . tep_catalog_href_link(FILENAME_CATALOG_LOGIN, '', 'SSL') . '">' . tep_catalog_href_link(FILENAME_CATALOG_LOGIN, '', 'SSL')  . '</a>';
		else
			$email .= '  (' . tep_catalog_href_link(FILENAME_CATALOG_LOGIN, '', 'SSL') . ')';

	  $firstname = $inrec['fname'];
	  $lastname = $inrec['lname'];
	  $full_name = $firstname . ' ' . $lastname;
	  $cartContents = $mline;
	  $loginLink = $email;
	  $customers_id = $cid;

	  $email_event = new email_event(RECOVER_CART_SALES_EMAIL);
	  $email_event->sendEmail(array(
	      'email' => $inrec['email'],
	      'name' => $full_name
	  ));

/*
		$email .= "\n" . sysLanguage::get('EMAIL_SEPARATOR') . "\n\n";

	  if (RCS_EMAIL_FRIENDLY == 'true')
		 $email .= EMAIL_TEXT_SALUTATION . $inrec['fname'] . ",";
	  else
		 $email .= STORE_NAME . "\n" . sysLanguage::get('EMAIL_SEPARATOR') . "\n";

	  if (mysql_num_rows($cquery) < 1)
		 $email .= sprintf(EMAIL_TEXT_NEWCUST_INTRO, $mline);
	  else
		 $email .= sprintf(EMAIL_TEXT_CURCUST_INTRO, $mline);

	  $email .= EMAIL_TEXT_BODY_HEADER . $mline . EMAIL_TEXT_BODY_FOOTER;

		if( EMAIL_USE_HTML == 'true' )
			$email .= '<a HREF="' . tep_catalog_href_link('', '') . '">' . STORE_OWNER . "\n" . tep_catalog_href_link('', '')  . '</a>';
		else
			$email .= STORE_OWNER . "\n" . tep_catalog_href_link('', '');

		$email .= "\n\n". $_POST['message'];
		$custname = $inrec['fname']." ".$inrec['lname'];

		$outEmailAddr = '"' . $custname . '" <' . $inrec['email'] . '>';
		if( tep_not_null(RCS_EMAIL_COPIES_TO) )
			$outEmailAddr .= ', ' . RCS_EMAIL_COPIES_TO;

		//tep_mail('', $outEmailAddr, sysLanguage::get('EMAIL_TEXT_SUBJECT'), $email, STORE_OWNER, EMAIL_FROM);

		$mline = "";
*/
		// See if a record for this customer already exists; if not create one and if so update it
		$donequery = tep_db_query("select * from ". TABLE_SCART ." where customers_id = '".$cid."'");
		if (mysql_num_rows($donequery) == 0)
			tep_db_query("insert into " . TABLE_SCART . " (customers_id, dateadded, datemodified ) values ('" . $cid . "', '" . seadate('0') . "', '" . seadate('0') . "')");
		else
			tep_db_query("update " . TABLE_SCART . " set datemodified = '" . seadate('0') . "' where customers_id = " . $cid );

		echo $cline;
		$cline = "";
	}

	echo "<tr><td colspan=8 align='right' class='dataTableContent'><b>" . TABLE_CART_TOTAL . "</b>" . $currencies->format($tprice) . "</td> </tr>";
	echo "<tr><td colspan=6 align='right'>" . htmlBase::newElement('button')->usePreset('delete')->setHref(tep_href_link(FILENAME_RECOVER_CART_SALES, "action=delete&customer_id=" . $cid . "&tdate=" . $tdate . "&sdate=" . $sdate))->draw() . "</td>  </tr>\n";
	echo "<tr><td colspan=6 align=center><a href=".$PHP_SELF.">" . TEXT_RETURN . "</a></td></tr>";
}
else	 //we are NOT doing an e-mail to some customers
{
?>
        <!-- REPORT TABLE BEGIN //-->
            <tr>
              <td class="pageHeading" align="left" width="50%" colspan="4"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
              <td class="pageHeading" align="right" width="50%" colspan="4">
                <form method=post action="<?php echo $PHP_SELF;?>">
                  <table align="right" width="100%">
                    <tr class="dataTableContent" align="right">
                      <td><?php echo DAYS_FIELD_PREFIX; ?><input type=text size=4 width=4 value=<?php echo $sdate; ?> name=sdate> - <input type=text size=4 width=4 value=<?php echo $tdate; ?> name=tdate><?php echo DAYS_FIELD_POSTFIX; ?><input type=submit value="<?php echo DAYS_FIELD_BUTTON; ?>"></td>
                    </tr>
                  </table>
                </form>
              </td>
            </tr>
<form method=post action=<?php echo $PHP_SELF; ?>>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left" colspan="2" width="10%" nowrap><?php echo TABLE_HEADING_CONTACT; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="15%" nowrap><?php echo TABLE_HEADING_DATE; ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="1" width="30%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_CUSTOMER'); ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="30%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_EMAIL'); ?></td>
              <td class="dataTableHeadingContent" align="left" colspan="2" width="15%" nowrap><?php echo TABLE_HEADING_PHONE; ?></td>
            </tr><tr>&nbsp;<br></tr>
            <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" align="left"   colspan="2"  width="10%" nowrap>&nbsp; </td>
              <td class="dataTableHeadingContent" align="left"   colspan="1"  width="15%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_MODEL'); ?></td>
              <td class="dataTableHeadingContent" align="left"   colspan="2" width="55%" nowrap><?php echo TABLE_HEADING_DESCRIPTION; ?></td>
              <td class="dataTableHeadingContent" align="center" colspan="1" width="5%" nowrap> <?php echo TABLE_HEADING_QUANTY; ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1"  width="5%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_PRICE'); ?></td>
              <td class="dataTableHeadingContent" align="right"  colspan="1" width="10%" nowrap><?php echo sysLanguage::get('TABLE_HEADING_TOTAL'); ?></td>
            </tr>
<?php
 $cust_ses_ids = _GetCustomerSessions();
 $bdate = seadate($sdate);
 $ndate = seadate($tdate);
 $query1 = tep_db_query("select cb.customers_id cid,
                                cb.products_id pid,
                                cb.customers_basket_quantity qty,
                                cb.customers_basket_date_added bdate,
                                cus.customers_firstname fname,
                                cus.customers_lastname lname,
                                cus.customers_telephone phone,
                                cus.customers_email_address email
                         from   " . TABLE_CUSTOMERS_BASKET . " cb,
                                " . TABLE_CUSTOMERS . " cus
                         where  cb.customers_basket_date_added <= CAST('" . $bdate . "' as DATE) and
                         		  cb.customers_basket_date_added > CAST('" . $ndate . "' as DATE) and
                                cus.customers_id not in ('" . implode(", ", $cust_ses_ids) . "') and
                                cb.customers_id = cus.customers_id order by cb.customers_basket_date_added desc,
                                cb.customers_id ");
 $results = 0;
 $curcus = "";
 $tprice = 0;
 $totalAll = 0;
 $first_line = true;
 $skip = false;

 $knt = mysql_num_rows($query1);
 for ($i = 0; $i <= $knt; $i++)
 {
   $inrec = tep_db_fetch_array($query1);

	// If this is a new customer, create the appropriate HTML
    if ($curcus != $inrec['cid'])
    {
      // output line
      $totalAll += $tprice;
      $cline .= "       </td>
                        <tr>
                          <td class='dataTableContent' align='right' colspan='8'><b>" . TABLE_CART_TOTAL . "</b>" . $currencies->format($tprice) . "</td>
                        </tr>
                        <tr>
                          <td colspan='6' align='right'>" . htmlBase::newElement('button')->usePreset('delete')->setHref(tep_href_link(FILENAME_RECOVER_CART_SALES,"action=delete&customer_id=" . $curcus . "&tdate=" . $tdate . "&sdate=" . $sdate))->draw() . "</td>
                        </tr>\n";
      if ($curcus != "" && !$skip)
        echo $cline;

      // set new cline and curcus
      $curcus = $inrec['cid'];

      if ($curcus != "")
		{
			$tprice = 0;

			// change the color on those we have contacted add customer tag to customers
			$fcolor = RCS_UNCONTACTED_COLOR;
			$checked = 1;	// assume we'll send an email
			$new = 1;
			$skip = false;
			$sentdate = "";
			$beforeDate = RCS_CARTS_MATCH_ALL_DATES ? '0' : $inrect['bdate'];
			$customer = $inrec['fname'] . " " . $inrec['lname'];
			$status = "";

			$donequery = tep_db_query("select * from ". TABLE_SCART ." where customers_id = '".$curcus."'");
			$emailttl = seadate(RCS_EMAIL_TTL);

			if (mysql_num_rows($donequery) > 0) {
				$ttl = tep_db_fetch_array($donequery);
				if( $ttl )
				{
					if( tep_not_null($ttl['datemodified']) )	// allow for older scarts that have no datemodified field data
						$ttldate = $ttl['datemodified'];
					else
						$ttldate = $ttl['dateadded'];

					if ($emailttl <= $ttldate) {
						$sentdate = $ttldate;
						$fcolor = RCS_CONTACTED_COLOR;
						$checked = 0;
						$new = 0;
					}
				}
			}

			// See if the customer has purchased from us before
			// Customers are identified by either their customer ID or name or email address
			// If the customer has an order with items that match the current order, assume order completed, bail on this entry!
			$ccquery = tep_db_query('select o.orders_id, o.orders_status from ' . TABLE_ORDERS . ' o left join orders_addresses oa using(orders_id) where oa.address_type = "customer" and (o.customers_id = ' . (int)$curcus . ' OR o.customers_email_address like "' . $inrec['email'] .'" or oa.entry_name like "' . $inrec['fname'] . ' ' . $inrec['lname'] . '") and date_purchased >= "' . $beforeDate . '"' );
			if (mysql_num_rows($ccquery) > 0)
			{
				// We have a matching order; assume current customer but not for this order
				$customer = '<font color=' . RCS_CURCUST_COLOR . '><b>' . $customer . '</b></font>';

				// Now, look to see if one of the orders matches this current order's items
				while( $orec = tep_db_fetch_array( $ccquery ) )
				{
					$ccquery = tep_db_query( 'select products_id from ' . TABLE_ORDERS_PRODUCTS . ' where orders_id = ' . (int)$orec['orders_id'] . ' AND products_id = ' . (int)$inrec['pid'] );
					if( mysql_num_rows( $ccquery ) > 0 )
					{
						if( $orec['orders_status'] > RCS_PENDING_SALE_STATUS )
							$checked = 0;

						// OK, we have a matching order; see if we should just skip this or show the status
						if( RCS_SKIP_MATCHED_CARTS == 'true' && !$checked )
						{
							$skip = true;	// reset flag & break us out of the while loop!
							break;
						}
						else
						{
							// It's rare for the same customer to order the same item twice, so we probably have a matching order, show it
							$fcolor = RCS_MATCHED_ORDER_COLOR;
							$ccquery = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = " . (int)Session::get('languages_id') . " AND orders_status_id = " . (int)$orec['orders_status'] );

							if( $srec = tep_db_fetch_array( $ccquery ) )
								$status = ' [' . $srec['orders_status_name'] . ']';
							else
								$status = ' ['. TEXT_CURRENT_CUSTOMER . ']';
						}
					}
				}
				if( $skip )
					continue;	// got a matched cart, skip to next one
			}
			$sentInfo = sysLanguage::get('TEXT_NOT_CONTACTED');

			if ($sentdate != '')
			$sentInfo = cart_date_short($sentdate);

			$cline = "
				<tr bgcolor=" . $fcolor . ">
				<td class='dataTableContent' align='center' width='1%'>" . tep_draw_checkbox_field('custid[]', $curcus, RCS_AUTO_CHECK == 'true' ? $checked : 0) . "</td>
				<td class='dataTableContent' align='left' width='9%' nowrap><b>" . $sentInfo . "</b></td>
				<td class='dataTableContent' align='left' width='15%' nowrap> " . tep_date_short($inrec['bdate']) . "</td>
				<td class='dataTableContent' align='left' width='30%' nowrap><a href='" . tep_href_link(FILENAME_CUSTOMERS, 'search=' . $inrec['lname'], 'NONSSL') . "'>" . $customer . "</a>".$status."</td>
				<td class='dataTableContent' align='left' colspan='2' width='30%' nowrap><a href='" . tep_href_link('mail.php', 'selected_box=tools&customer=' . $inrec['email']) . "'>" . $inrec['email'] . "</a></td>
				<td class='dataTableContent' align='left' colspan='2' width='15%' nowrap>" . $inrec['phone'] . "</td>
				</tr>";
		}
    }

	// We only have something to do for the product if the quantity selected was not zero!
    if ($inrec['qty'] != 0)
    {
			// Get the product information (name, price, etc)
			$query2 = tep_db_query("select  p.products_price price,
													p.products_tax_class_id taxclass,
													p.products_model model,
													pd.products_name name
										 from    " . TABLE_PRODUCTS . " p,
													" . TABLE_PRODUCTS_DESCRIPTION . " pd,
													" . TABLE_LANGUAGES . " l
										 where   p.products_id = '" . (int)$inrec['pid'] . "' and
													pd.products_id = p.products_id and
													pd.language_id = " . (int)Session::get('languages_id') );
			$inrec2 = tep_db_fetch_array($query2);

			// Check to see if the product is on special, and if so use that pricing
			$sprice = tep_get_products_special_price( $inrec['pid'] );
			if( $sprice < 1 )
				$sprice = $inrec2['price'];
			// Some users may want to include taxes in the pricing, allow that. NOTE HOWEVER that we don't have a good way to get individual tax rates based on customer location yet!
			if( RCS_INCLUDE_TAX_IN_PRICES  == 'true' )
				$sprice += ($sprice * tep_get_tax_rate( $inrec2['taxclass'] ) / 100);
			else if( RCS_USE_FIXED_TAX_IN_PRICES  == 'true' && RCS_FIXED_TAX_RATE > 0 )
				$sprice += ($sprice * RCS_FIXED_TAX_RATE / 100);

			// BEGIN OF ATTRIBUTE DB CODE
			$prodAttribs = ''; // DO NOT DELETE

			if (RCS_SHOW_ATTRIBUTES == 'true')
			{
				$attribquery = tep_db_query("select  cba.products_id pid,
															 po.products_options_name poname,
															 pov.products_options_values_name povname
												  from    " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " cba,
															 " . TABLE_PRODUCTS_OPTIONS . " po,
															 " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov,
															 " . TABLE_LANGUAGES . " l
												  where   cba.products_id ='" . $inrec['pid'] . "' and
			 												 cba.customers_id = " . $curcus . " and
			 												 po.products_options_id = cba.products_options_id and
															 pov.products_options_values_id = cba.products_options_value_id and
															 po.language_id = " . (int)Session::get('languages_id') . " and
															 pov.language_id = " . (int)Session::get('languages_id')
											  );
				$hasAttributes = false;

				if (tep_db_num_rows($attribquery))
				{
				  $hasAttributes = true;
				  $prodAttribs = '<br>';

				  while ($attribrecs = tep_db_fetch_array($attribquery))
					 $prodAttribs .= '<small><i> - ' . $attribrecs['poname'] . ' ' . $attribrecs['povname'] . '</i></small><br>';
				}
			}
			// END OF ATTRIBUTE DB CODE
			$tprice = $tprice + ($inrec['qty'] * $sprice);
			$pprice_formated  = $currencies->format($sprice);
			$tpprice_formated = $currencies->format(($inrec['qty'] * $sprice));

			$cline .= "<tr class='dataTableRow'>
                    <td class='dataTableContent' align='left' vAlign='top' colspan='2' width='12%' nowrap> &nbsp;</td>
                    <td class='dataTableContent' align='left' vAlign='top' width='13%' nowrap>" . $inrec2['model'] . "</td>
                    <td class='dataTableContent' align='left' vAlign='top' colspan='2' width='55%'><a href='" . tep_href_link(FILENAME_CATEGORIES, 'action=new_product_preview&read=only&pID=' . $inrec['pid'] . '&origin=' . FILENAME_RECOVER_CART_SALES . '?page=' . $_GET['page'], 'NONSSL') . "'><b>" . $inrec2['name'] . "</b></a>
                    " . $prodAttribs . "
                    </td>
                    <td class='dataTableContent' align='center' vAlign='top' width='5%' nowrap>" . $inrec['qty'] . "</td>
                    <td class='dataTableContent' align='right'  vAlign='top' width='5%' nowrap>" . $pprice_formated . "</td>
                    <td class='dataTableContent' align='right'  vAlign='top' width='10%' nowrap>" . $tpprice_formated . "</td>
                 </tr>";
	 }
  }
  $totalAll_formated = $currencies->format($totalAll);
  $cline = "<tr></tr><td class='dataTableContent' align='right' colspan='8'><hr align=right width=55><b>" . TABLE_GRAND_TOTAL . "</b>" . $totalAll_formated . "</td>
              </tr>";
  echo $cline;
 echo "<tr><td colspan=8><hr size=1 color=000080><b>". PSMSG ."</b><br>". tep_draw_textarea_field('message', 'soft', '80', '5') ."<br>" . tep_draw_selection_field('submit_button', 'submit', TEXT_SEND_EMAIL) . "</td></tr>";
?>
 </form>
<?php }
//
// end footer of both e-mail and report
//
?>
          <!-- REPORT TABLE END //-->
      </table>