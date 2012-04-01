<div class="ui-widget">
	<?php
	if (isset($boxForm)){
		echo '<form name="' . $boxForm['name'] . '" action="' . $boxForm['action'] . '" method="' . $boxForm['method'] . '">' . "\n";
	}
	?>
	<div class="ui-widget-content ui-corner-all pageContainer">
		<?php
		if (isset($boxHeading) && !empty($boxHeading)){
		?>
		<div class="ui-widget-header ui-corner-all pageHeaderContainer">
			<span class="ui-icon ui-icon-circle-triangle-e"></span>
			<span class="ui-widget-header-text pageHeaderText"><?php echo $boxHeading;?></span>
		</div>
		<?php
		}
		?>
		<div class="ui-widget-text pageContent">
			<?php
				if(isset($boxContent)){
					echo $boxContent;
				}
			?>
			<div class="ui-helper-clearfix"></div>
		</div>
		<?php
		?>
	</div>
	<?php
	if (isset($boxButtons) && !empty($boxButtons)){
	?>
	<div class="ui-widget-content ui-widget-footer-box ui-corner-all"><?php
		echo $boxButtons;
	?></div>
	<?php
	}
	
	if (isset($boxForm)){
		echo '</form>' . "\n";
	}
	?>
</div>
