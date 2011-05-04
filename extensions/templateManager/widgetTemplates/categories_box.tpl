<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-widget ui-widget-content ui-infobox ui-corner-all-medium">
 <div class="ui-widget-header">
  <div class="ui-infobox-header-text"><?php echo $boxHeading;?></div>
 </div>
 <div class="ui-infobox-content"><?php echo $boxContent;?></div>
</div>