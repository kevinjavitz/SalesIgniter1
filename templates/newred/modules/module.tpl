<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-widget ui-widget-content ui-contentbox ui-corner-top-medium">
<?php if (!empty($boxHeading)){ ?>
 <div class="ui-widget-header ui-contentbox-header ui-corner-all-medium">
  <div class="ui-contentbox-header-text"><?php echo $boxHeading;?></div>
 </div>
<?php } ?>
 <div class="ui-contentbox-content"><?php echo $boxContent;?></div>
</div>