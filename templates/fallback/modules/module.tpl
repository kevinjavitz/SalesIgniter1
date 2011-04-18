<div <?php echo (isset($box_id) ? 'id="' . $box_id . '" ' : '');?>class="ui-contentbox ui-corner-bottom">
 <div class="ui-widget ui-widget-header ui-contentbox-header"><span class="ui-icon ui-icon-circle-triangle-e"></span><span class="ui-contentbox-header-text"><?php echo $boxHeading;?></span></div>
 <div class="ui-contentbox-content"><?php echo $boxContent;?><div class="ui-helper-clearfix"></div></div>
</div>