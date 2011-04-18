<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-widget ui-widget-content ui-infobox ui-corner-all-medium">
	<div class="ui-widget-header ui-infobox-header ui-corner-all">
		<span class="ui-infobox-header-text"><?php echo $boxHeading;?></span>
		<?php if (isset($boxLink)){ ?>
		<span class="ui-infobox-header-link"><?php echo $boxLink;?></span>
		<?php } ?>
	</div>
	<div class="ui-infobox-content ui-corner-bottom-medium"><?php echo $boxContent;?></div>
</div>