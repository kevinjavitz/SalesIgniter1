<div class="ui-widget">
	<?php if (isset($pageForm)){ ?>
	<form name="<?php echo $pageForm['name'];?>" action="<?php echo $pageForm['action'];?>" method="<?php echo $pageForm['method'];?>">
	<?php } ?>
	<div class="ui-widget-content ui-corner-all pageContainer">
		<?php if (isset($pageTitle) && !empty($pageTitle)){ ?>
		<div class="ui-widget-header ui-corner-all pageHeaderContainer">
			<span class="ui-icon ui-icon-circle-triangle-e"></span>
			<span class="ui-widget-header-text pageHeaderText"><?php echo $pageTitle;?></span>
		</div>
		<?php } ?>
		<div class="ui-widget-text pageContent">
			<?php echo $pageContent;?>
			<div class="ui-helper-clearfix"></div>
		</div>
	</div>
	<?php if (isset($pageButtons) && !empty($pageButtons)){ ?>
	<div class="ui-widget-content ui-widget-footer-box ui-corner-all"><?php
		echo $pageButtons;
	?></div>
	<?php } ?>
	<?php if (isset($pageForm)){ ?>
	</form>
	<?php } ?>
</div>
