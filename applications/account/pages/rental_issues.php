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
} else {
	?>
	<tr>
		<td>
			<?php
   		    //$sql_customers_issues= "SELECT r.issue_id,r.customers_id,r.products_id, p.products_name, date_format(r.reported_date,'%m/%d/%Y') as formatted_date ,r.status FROM ".TABLE_RENTAL_ISSUES .' r, '. TABLE_PRODUCTS_DESCRIPTION.' p '. " where p.products_id = r.products_id and p.language_id='".Session::get('languages_id')."' and customers_id =".$userAccount->getCustomerId();	//No chain
			//$rs_customers_issues = tep_db_query($sql_customers_issues);
			$QIssues = Doctrine_Query::create()
			->from('RentIssues')
			->where('parent_id = ?', '0')
			->andWhere('customers_id = ?', $userAccount->getCustomerId())
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if($QIssues){
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
							<?foreach($QIssues as $iIssue){?>
							<tr>
								<td class="main"><?php echo $iIssue['issue_id']; ?></td>
								<td class="main"><?php echo $iIssue['products_name'] ?></td>
								<td class="main"><?php echo $iIssue['reported_date']; ?></td>
								<td class="main"><?php
                	if($iIssue['status']=='P'){
									echo 'Pending';
								}
									if($iIssue['status']=='O'){
										echo 'Open';
									}
									if($iIssue['status']=='C'){
										echo 'Closed';
									}
									?></td>
								<td class="main"><a href="<?php echo itw_app_link('action=details&issueID='.$iIssue['issue_id'], 'account', 'rental_issues') ?>"><?=sysLanguage::get('TEXT_DETAILS')?></td>
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
         	if($_GET['action'] == 'details' && isset($_GET['issueID'])){
				$QdetailIssue = Doctrine_Query::create()
				->from('RentIssues')
				->where('issue_id = ?', $_GET['issueID'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				$pageContent->set('pageForm', array(
					'name' => 'rental_issues_details',
					'action' => itw_app_link('action=resolve_ticket', 'account', 'rental_issues'),
					'method' => 'post'
				));
		?>

		<tr>
			<td>
				<?php

				echo tep_draw_hidden_field('issue_id', $_GET['issueID']);
				echo tep_draw_hidden_field('products_id', $QdetailIssue[0]['products_id']);
				?>
				<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
					<tr class="infoBoxContents">
						<td><table border="0" width="100%" cellspacing="0" cellpadding="2">
							<tr>
								<td width="10">&nbsp;</td>
								<td align="right" class="main" width=200><?php echo sysLanguage::get('TABLE_HEADING_ISSUE_ID'); ?>:</td>
								<td class="main"><?php echo $QdetailIssue[0]['issue_id']; ?></td>
								<td width="10">&nbsp;</td>
							</tr>
							<tr>
								<td width="10">&nbsp;</td>
								<td align="right" class="main"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCT_A'); ?>:</td>
								<td class="main"><?php echo $QdetailIssue[0]['products_name']; ?></td>
								<td width="10">&nbsp;</td>
							</tr>
							<tr>
								<td width="10">&nbsp;</td>
								<td align="right" class="main"><?php echo sysLanguage::get('TEXT_CONVERSATION'); ?>:</td>


								<td class="main" valign=top>
									<?php


									$QIssues = Doctrine_Query::create()
									->from('RentIssues')
									->where('parent_id = ?', $_GET['issueID'])
									->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
									echo 'Me: '.$QdetailIssue[0]['feedback'].'<br/>';
								    foreach($QIssues as $dissue){
									    if($dissue['customers_id'] > 0){
		 				  		            echo 'Me: '.$dissue['feedback']."<br>";
									    }else{
										    echo '&nbsp;&nbsp;&nbsp;Admin: '.$dissue['feedback']."<br>";
									    }
								    }
									?></td>
								<td width="10">&nbsp;</td>
							</tr>
							<tr>
								<td width="10">&nbsp;</td>
								<td align="right" class="main"><?php echo sysLanguage::get('TEXT_REPLY'); ?>:</td>
								<td>
									<?php
									    echo tep_draw_textarea_field('feedback', 'soft', '40', '5','');
									?>
								</td>
								<td width="10">&nbsp;</td>
							</tr>


						</table>
						</td>
					</tr>
				</table></td>
		</tr>
				<?php
			$pageButtons =

			htmlBase::newElement('button')
			->usePreset('continue')
			->setText(sysLanguage::get('TEXT_REPLY_TICKET'))
			->attr('name', 'type')
			->attr('value', 'reply')
			->setType('submit')
			->draw();

			if($QdetailIssue[0]['status'] == 'O'){
				$pageButtons .= htmlBase::newElement('button')
				->usePreset('cancel')
				->setText(sysLanguage::get('TEXT_CLOSE_TICKET'))
				->attr('name', 'type')
				->attr('value', 'close')
				->setType('submit')
				->draw();
			}else{
				$pageButtons .= htmlBase::newElement('button')
				->usePreset('cancel')
				->setText(sysLanguage::get('TEXT_REOPEN_TICKET'))
				->attr('name', 'type')
				->attr('value', 'reply')
				->setType('submit')
				->draw();
			}

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

$pageButtons .= htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();


$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
