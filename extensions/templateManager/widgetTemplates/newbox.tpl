<div id="newsm">
<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="">
<?php if (!empty($boxHeading)){ ?>
 <div class="ui-widget-header ui-corner-top">
  <div class="ui-widget-header-text"><?php echo $boxHeading;?></div>
 </div>
<?php } ?>
 <div class="ui-widget-content"><?php echo $boxContent;?></div>
</div>
</div>