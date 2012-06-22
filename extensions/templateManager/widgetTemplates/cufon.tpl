<script type="text/javascript" src="<?php echo sysConfig::getDirWsCatalog();?>extensions/templateManager/widgets/cufonFonts/javascript/cufon-yui.js">

</script>
<?php echo $boxContent;?>
<!--[if gte IE 9]>
	<script type="text/javascript">
	$(window).load(function(){
		Cufon.set('engine', 'canvas');
	});
	</script>
<![endif]-->
<script type="text/javascript">
	Cufon.now();
</script>