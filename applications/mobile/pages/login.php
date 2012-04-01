<?php
ob_start();
?>
<style>
	.tabs .ui-btn {
		-webkit-border-radius : 10px 10px 0 0;
	}

	.tabPage {
		display    : none;
		background : white;
		padding    : 5px;
	}

	.tabPage p {
		margin : 0px;
	}
</style>
<br>
<div class="tabs" data-role="navbar">
	<ul>
		<li><a href="#" data-href="new">New Customer</a></li>
		<li><a href="#" data-href="return">Returning Customer</a></li>
	</ul>
</div>
<div id="new" class="tabPage">
	<p><?php
		echo sysLanguage::get('TEXT_NEW_CUSTOMER') . '<br><br>' . sprintf(sysLanguage::get('TEXT_NEW_CUSTOMER_INTRODUCTION'), sysConfig::get('STORE_NAME')) . '<br>';
		?></p>

	<div style="text-align:right"><br /><?php
		$newAccountButton = htmlBase::newElement('button')
			->setText(sysLanguage::get('IMAGE_BUTTON_CONTINUE'))
			->setHref(itw_app_link(null, 'account', 'create', 'SSL'))
			->setIcon('circleTriangleEast');

		echo $newAccountButton->draw();
		?></div>
</div>
<div id="return" class="tabPage">
	<div style="margin:.5em;">
		<p><?php echo sysLanguage::get('TEXT_RETURNING_CUSTOMER'); ?></p>
		<br>
		<div><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS'); ?></div>
		<div><?php
			echo htmlBase::newElement('input')
			//->setType('email')
				->setName('email_address')
				->attr('required', '')
				->draw();
			?></div>
		<div><?php echo sysLanguage::get('ENTRY_PASSWORD');?></div>
		<div><?php
			echo htmlBase::newElement('input')
				->setType('password')
				->setName('password')
				->attr('required', '')
				->draw();
			?></div>
		<div style="text-align:center;"><?php
			echo htmlBase::newElement('button')
				->setText(sysLanguage::get('IMAGE_BUTTON_LOGIN'))
				->setType('submit')
				->setIcon('circleTriangleEast')
				->draw();
			?></div>
		<br>
		<div style="text-align:center;">
			<a id="passwordForgotten" href="#"><?php echo sysLanguage::get('TEXT_PASSWORD_FORGOTTEN');?></a>
		</div>
	</div>
</div>

<script>
	function showMessageStack(message){
		if ($('.pageMessageStack').size() > 0){
			$('.pageMessageStack').html('<h3>' +
				message +
				'</h3>');
		}else{
			$('div:jqmData(role=content)').first().prepend('<div class="ui-bar ui-bar-e pageMessageStack"><h3>' +
				message +
				'</h3></div><br>');
		}
	}

	$('#passwordForgottenDialog').live('pageinit', function (){
		var self = this;
		$(this).find('#passwordForgottenDialogSubmit').click(function (){
			$.ajax({
				url: js_app_link('rType=ajax&app=account&appPage=password_forgotten&action=resetPassword'),
				cache: false,
				data: $(self).find('input[name=email_address]').serialize(),
				type: 'post',
				dataType: 'json',
				success: function (data){
					$(self).dialog('close');

					if (data.messageStack.length > 0){
						showMessageStack(data.messageStack);
					}
					//location.reload(true);
				}
			});
		});
	});

	$('#passwordForgotten').click(function (e){
		e.preventDefault();

		$.mobile.changePage(js_app_link('rType=ajax&ui_state=dialog&app=mobile&appPage=password_forgotten'), {
			transition: "pop",
			role: 'dialog',
			reverse: false,
			changeHash: true
		});
		return false;
	});

	$('div').live('pageinit', function (){
		if ($(this).jqmData('role') != 'dialog'){
			return;
		}
		var self = this;
		$(this).find('.ui-icon-delete').parent().parent().click(function (e){
			$(self).dialog('close');
			return false;
		});
	});

	$(document).delegate('.tabs[data-role="navbar"] a', 'click', function () {
		$(this).addClass('ui-btn-active');
		$('.tabPage').hide();
		$('#' + $(this).attr('data-href')).show();
	});
	$('.tabs li a').first().trigger('click');
</script>
<?php
$Contents = ob_get_contents();
ob_end_clean();

$pageContent->set('pageForm', array(
	'name' => 'login',
	'action' => itw_app_link('action=processLogin', 'account', 'login', 'SSL'),
	'method' => 'post'
));

$pageContent->set('pageTitle', 'Login');
$pageContent->set('pageContent', $Contents);

?>