<?php
	ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if (isset($_GET['action']) && ($_GET['action'] == 'success')) {
?>
      <tr>
        <td class="main" align="center"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', sysLanguage::get('HEADING_TITLE'), '0', '0', 'align="left"') . sysLanguage::get('TEXT_SUCCESS'); ?></td>
      </tr>
<?php
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();
  }elseif (isset($_GET['opID'])){
      $Qcheck = tep_db_query('select op.products_name, op.products_id, o.date_purchased from ' . TABLE_ORDERS . ' o left join ' . TABLE_ORDERS_PRODUCTS . ' op using(orders_id) where o.customers_id = "' . $userAccount->getCustomerId() . '" and op.orders_products_id = "' . tep_db_prepare_input($_GET['opID']) . '"');
      if (tep_db_num_rows($Qcheck)){
          $check = tep_db_fetch_array($Qcheck);
?>
      <tr>
       <td><table cellpadding="2" cellspacing="0" border="0" width="100%">
        <tr>
         <td>Product:</td>
         <td><?php echo $check['products_name'];?></td>
        </tr>
        <tr>
         <td>Problem:</td>
         <td><textarea name="problem_desc" rows="7" style="width:50%"></textarea></td>
        </tr>
       </table></td>
      </tr>
<?php
		$pageContent->set('pageForm', array(
			'name' => 'rentalIssues',
			'action' => itw_app_link(null, 'account', 'rental_issues'),
			'method' => 'post'
		));
		$pageButtons = tep_draw_hidden_field('action', 'open_ticket') .
		 tep_draw_hidden_field('onetime', 'true') .
		 tep_draw_hidden_field('rented_products', $check['products_id']) .
		 tep_draw_hidden_field('products_name', $check['products_name']) . 
		 htmlBase::newElement('button')
		->usePreset('continue')
		->setType('submit')
		->draw();
      }
  } else {
?>
      <tr>
        <td>
	<?php
		$sql_customers_issues= "SELECT r.issue_id,r.customers_id,r.products_id, p.products_name, date_format(r.reported_date,'%m/%d/%Y') as formatted_date ,r.status FROM ".TABLE_RENTAL_ISSUES .' r, '. TABLE_PRODUCTS_DESCRIPTION.' p '. " where p.products_id = r.products_id and p.language_id='".Session::get('languages_id')."' and customers_id =".$userAccount->getCustomerId();	//No chain
		$rs_customers_issues = tep_db_query($sql_customers_issues);
		if(tep_db_num_rows($rs_customers_issues)>0){
	?>
        <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><?php echo sysLanguage::get('TABLE_HEADING_ISSUE_ID'); ?></td>
                <td class="main"><?php echo sysLanguage::get('TABLE_HEADING_RENTED_PRODUCT'); ?></td>
                <td class="main"><?php echo sysLanguage::get('TABLE_HEADING_REPORTED_DATE'); ?></td>
                <td class="main"><?php echo sysLanguage::get('TABLE_HEADING_STATUS'); ?></td>
                <td class="main"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?></td>

              </tr>
              <tr><td colspan=5><hr></td></tr>
              <?while($dt_customers_issues = tep_db_fetch_array($rs_customers_issues)){?>
              <tr>
                <td class="main"><?php echo $dt_customers_issues['issue_id']; ?></td>
                <td class="main"><?php echo $dt_customers_issues['products_name'] ?></td>
                <td class="main"><?php echo $dt_customers_issues['formatted_date']; ?></td>
                <td class="main"><?php
                	if($dt_customers_issues['status']=='P'){
                		echo 'Pending';
                	}
                	if($dt_customers_issues['status']=='O'){
                		echo 'Open';
                	}
                	if($dt_customers_issues['status']=='C'){
                		echo 'Closed';
                	}
                	?></td>
                	<td class="main"><a href="<?php echo itw_app_link('action=details&ID='.$dt_customers_issues['issue_id'], 'account', 'rental_issues') ?>"><?=sysLanguage::get('TEXT_DETAILS')?></td>
              </tr>
             <?php } ?>
            </table></td>
          </tr>
        </table>
   <?php } ?>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <?php
      	if($_GET[action]=='details' && $_GET[ID]!=''){
			$rs_issues = tep_db_query("SELECT * FROM " . TABLE_RENTAL_ISSUES . " WHERE issue_id = ".$_GET[ID]);
			$dt_issues = tep_db_fetch_array($rs_issues);
			
 		$pageContent->set('pageForm', array(
			'name' => 'rental_issues_details',
			'action' => itw_app_link(null, 'account', 'rental_issues'),
			'method' => 'post'
		));
     ?>

      <tr>
		  <td>
		  	<?php
		  		echo tep_draw_form('rental_issues_details',FILENAME_RENTAL_ISSUES);
		  		echo tep_draw_hidden_field('action', 'reopen_ticket');
		  		echo tep_draw_hidden_field('issue_id', $_GET['ID']);
		  		echo tep_draw_hidden_field('products_id', $dt_issues[products_id]);
			?>
		    <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
			<tr class="infoBoxContents">
			  <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
				<tr>
				  <td width="10">&nbsp;</td>
				  <td align="right" class="main" width=200><?php echo sysLanguage::get('TABLE_HEADING_ISSUE_ID'); ?>:</td>
				  <td class="main"><?php echo $dt_issues[issue_id]; ?></td>
				  <td width="10">&nbsp;</td>
				</tr>
				<tr>
				  <td width="10">&nbsp;</td>
				  <td align="right" class="main"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCT_A'); ?>:</td>
				  <td class="main"><?php echo $dt_issues[products_name]; ?></td>
				  <td width="10">&nbsp;</td>
				</tr>
				<tr>
				  <td width="10">&nbsp;</td>
				  <td align="right" class="main"><?php echo sysLanguage::get('TEXT_PROBLEM'); ?>:</td>
				  <?if($dt_issues[feedback]!=""){?>
				  <td class="main"><?php echo $dt_issues[problem]; ?></td>
				  <?}else{?>

				  <td class="main" valign=top>
				  <?php
				  		echo $dt_issues[problem]."<br>";
				  		echo tep_draw_hidden_field('old_problem',$dt_issues['problem']);
				  		echo tep_draw_textarea_field('problem_new', 'soft', '40', '5','');?></td>
				  <?}?>
				  <td width="10">&nbsp;</td>
				</tr>
				<?if($dt_issues[feedback]!=""){?>
				<tr>
				  <td width="10">&nbsp;</td>
				  <td align="right" class="main"><?php echo sysLanguage::get('TEXT_FEEDBACK'); ?>:</td>
				  <td class="main"><?php echo $dt_issues[feedback]; ?></td>
				  <td width="10">&nbsp;</td>
				</tr>
				<tr>
				  <td width="10">&nbsp;</td>
				  <td align="right" class="main"><?php echo sysLanguage::get('TEXT_PROBLEM_REPLY'); ?>:</td>
				  <?php
				  	//fetch replied text
				  	$rs_thread = tep_db_query("Select problem from " . TABLE_RENTAL_ISSUES . " WHERE parent_id = ".$_GET[ID]);
				  	$dt_thread = tep_db_fetch_array($rs_thread);
				  ?>
				  <td class="main"><?php echo tep_draw_textarea_field('problem_reply','virtual','3','5',$dt_thread[problem]); ?></td>
				  <td width="10">&nbsp;</td>
				</tr>

				<?}?>
			  </table>
			  </td>
			</tr>
		  </table></td>
      </tr>
      <?php
		$pageButtons = tep_draw_hidden_field('action', 'reopen_ticket') . 
		tep_draw_hidden_field('issue_id', $_GET['ID']) . 
		tep_draw_hidden_field('products_id', $dt_issues[products_id]) . 
		 htmlBase::newElement('button')
		->usePreset('continue')
		->setType('submit')
		->draw();
      	}
      ?>
<?php
  }
?>
    </table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
