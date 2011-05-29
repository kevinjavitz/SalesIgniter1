<?php
if(isset($_POST['paid_content_provider_id']) && is_array($_POST['paid_content_provider_id'])){
	foreach($_POST['paid_content_provider_id'] as $content_provider_id){
		if((int)$_POST['owed'][$content_provider_id] > 0){
			$RoyaltiesSystemRoyaltiesPaid = new RoyaltiesSystemRoyaltiesPaid();
			$RoyaltiesSystemRoyaltiesPaid->content_provider_id = $content_provider_id;
			$RoyaltiesSystemRoyaltiesPaid->royalty_amount_paid = $_POST['owed'][$content_provider_id];
			$RoyaltiesSystemRoyaltiesPaid->royalty_payment_date = date('Y-m-d H:i:s');
			$RoyaltiesSystemRoyaltiesPaid->save();
		}
	}
}

tep_redirect(itw_app_link('appExt=royaltiesSystem', 'show_reports', 'totals'));
?>