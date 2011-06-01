<div class="ui-widget">
	<?php
	if (isset($beforePageForm)){
		echo $beforePageForm;
	}
	if (isset($pageForm)){
		echo '<form name="' . $pageForm['name'] . '" action="' . $pageForm['action'] . '" method="' . $pageForm['method'] . '">' . "\n";
	}
	if (isset($beforePageContainer)){
		echo $beforePageContainer;
	}
	?>
	<div class="ui-widget-content ui-corner-all pageContainer">
		<?php
		if (isset($beforePageTitle)){
			echo $beforePageTitle;
		}
		if (isset($pageTitle) && !empty($pageTitle)){
		?>
		<div class="ui-widget-header ui-corner-all pageHeaderContainer">
			<span class="ui-icon ui-icon-circle-triangle-e"></span>
			<span class="ui-widget-header-text pageHeaderText"><?php echo $pageTitle;?></span>
		</div>
		<?php
		}
		if (isset($afterPageTitle)){
			echo $afterPageTitle;
		}
		if (isset($beforePageContent)){
			echo $beforePageContent;
		}
		?>
		<div class="ui-widget-text pageContent">
			<?php
			echo $pageContent;
			?>
			<div class="ui-helper-clearfix"></div>
		</div>
		<?php
		if (isset($afterPageContent)){
			echo $afterPageContent;
		}
		?>
	</div>
	<?php
	if (isset($afterPageContainer)){
		echo $afterPageContainer;
	}
	if (isset($beforePageButtons)){
		echo $beforePageButtons;
	}
	if (isset($pageButtons) && !empty($pageButtons)){
	?>
	<div class="ui-widget-content ui-widget-footer-box ui-corner-all"><?php
		echo $pageButtons;
	?></div>
	<?php
	}
	if (isset($afterPageButtons)){
		echo $afterPageButtons;
	}
	
	if (isset($pageForm)){
		echo '</form>' . "\n";
	}
	if (isset($afterPageForm)){
		echo $afterPageForm;
	}
	?>
</div>
