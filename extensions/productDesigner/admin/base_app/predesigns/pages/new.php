<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$pixelsPerInch = 72;
	if (isset($_GET['dID'])){
		$Qdesign = Doctrine_Query::create()
		->from('ProductDesignerPredesigns')
		->where('predesign_id = ?', $_GET['dID'])
		->execute();
		if ($Qdesign->count() > 0){
			$design = $Qdesign->toArray();
		}
	}
?>
<style>
#productImageHolder { border:1px solid black;margin-left:auto;margin-right:auto;position:relative;z-index:2; }
#customizeArea { border:1px dotted black;position:absolute;overflow:hidden;top:0px;left:0px;z-index:50; }

.textEntry, .variableTextEntry, .clipartEntry, .variableClipartEntry { position:absolute;top:0;left:0;white-space:nowrap;border: 1px dotted transparent; }
.activeItem { border: 1px dotted red; }
/* Move (Text/Image) Arrows */
.productDesignerArrow.ui-state-default, .productDesignerArrow.ui-state-hover { background: transparent url(../extensions/productDesigner/catalog/base_app/design/stylesheets/images/arrows.png) no-repeat;border:none; }
.productDesignerArrow.ui-state-hover { cursor:pointer; }

.placementArrowNorth, .placementArrowSouth, .zIndexArrowNorth, .zIndexArrowSouth { width:20px;height:24px; }
.placementArrowWest, .placementArrowEast { width:24px;height:20px; }

.placementArrowNorth.ui-state-default, .zIndexArrowNorth.ui-state-default { background-position: 0px 0px; }
.placementArrowNorth.ui-state-hover, .zIndexArrowNorth.ui-state-default { background-position: 0px -26px; }

.placementArrowSouth.ui-state-default, .zIndexArrowSouth.ui-state-default { background-position: -22px 0px; }
.placementArrowSouth.ui-state-hover, .zIndexArrowSouth.ui-state-default { background-position: -22px -26px; }

.placementArrowWest.ui-state-default { background-position: -43px 0px; }
.placementArrowWest.ui-state-hover { background-position: -43px -26px; }

.placementArrowEast.ui-state-default { background-position: -70px 0px; }
.placementArrowEast.ui-state-hover { background-position: -70px -26px; }
#clipartDialogBox{
	display: none;
}
#clipartDialogLeft{
	overflow: auto;
	float:left;
	border-right:1px solid #000000;
}

#clipartDialogRight{

width:680px;
height:400px;
overflow: auto;
}
#clipartImages{
	overflow: auto;
	
}
.cimages{
	list-style-type: none;
	overflow:auto;
}
.cimages li a {
	float:left;
	border:1px solid #ffffff;
}

.cimages li a:hover {
	float:left;
	border:1px solid red;
}
.folder{
	cursor:pointer;
}
.ui-combobox { position:relative; }
.ui-combobox-selected { display:block;margin:.5em; }
.ui-combobox-drop-icon { position:absolute;top:-1px;right:-1px;height:100%; }
.ui-combobox-drop-icon .ui-icon { margin:.5em; }
.ui-combobox-list-container { background:#ffffff;display:none;position:absolute;top:1.2em;left:-.1em;z-index:999;overflow-y:scroll;overflow-x:none; }
.ui-combobox-list-container ul { list-style: none;margin:0;padding:.2em; }
.ui-combobox-list-option {  }

</style>
<form name="predesign" action="<?php echo itw_app_link(tep_get_all_get_params(array('action')) . 'action=save');?>" method="post">
<fieldset>
 <legend>Predesign Settings</legend>
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main">Cost:</td>
   <td class="main"><input type="text" name="design_cost" value="<?php echo (isset($design) ? $design[0]['predesign_cost'] : '');?>" /></td>
  </tr>
  <tr>
   <td class="main">Design Name:</td>
   <td class="main"><input type="text" name="design_name" value="<?php echo (isset($design) ? $design[0]['predesign_name'] : '');?>" /></td>
  </tr>
  <tr>
   <td class="main">Design For:</td>
   <td class="main"><?php
	$selectBox = htmlBase::newElement('selectbox')
	->setName('design_location')
	->addOption('front', 'Front')
	->addOption('back', 'Back');
	if (isset($design)){
		$selectBox->selectOptionByValue($design[0]['predesign_location']);
	}
	echo $selectBox->draw();
   ?></td>
  </tr>
  <tr>
   <td class="main">Design Classes:</td>
   <td class="main"><?php
	$checkedClasses = array();
	if (isset($design)){
		$checkedClasses = explode(',', $design[0]['predesign_classes']);
	}
	
	$Qclasses = Doctrine_Query::create()
	->from('ProductDesignerPredesignClasses')
	->orderBy('class_name')
	->execute();
	$groupBoxes = array();
	if ($Qclasses->count() > 0){
		foreach($Qclasses->toArray() as $class){
			$groupBoxes[] = array(
				'value' => $class['class_id'],
				'label' => $class['class_name'],
				'labelPosition' => 'after'
			);
		}
		unset($class);
	}
	$Qclasses->free();
	unset($Qclasses);
	
	$classChexboxes = htmlBase::newElement('checkbox')
	->addGroup(array(
		'separator' => '<br />',
		'checked' => $checkedClasses,
		'name' => 'classes[]',
		'data' => $groupBoxes
	));
	echo $classChexboxes->draw();
	unset($checkedClasses);
   ?></td>
  </tr>
  <tr>
   <td class="main">Categories:</td>
   <td class="main"><?php
	$checkedCats = array();
	if (isset($design)){
		$QcurCategories = Doctrine_Query::create()
		->select('categories_id')
		->from('ProductDesignerPredesignsToPredesignCategories')
		->where('predesign_id = ?', $design[0]['predesign_id'])
		->execute();
		if ($QcurCategories->count() > 0){
			foreach($QcurCategories->toArray() as $category){
				$checkedCats[] = $category['categories_id'];
			}
			unset($category);
		}
		$QcurCategories->free();
		unset($QcurCategories);
	}
	echo tep_get_predesign_category_tree_list('0', $checkedCats);
	unset($checkedCats);
   ?></td>
  </tr>
  <tr>
   <td class="main">Activities:</td>
   <td class="main"><?php
	$checkedActivities = array();
	if (isset($design)){
		$checkedActivities = explode(',', $design[0]['predesign_activities']);
	}
	
	$Qactivities = Doctrine_Query::create()
	->from('ProductDesignerPredesignActivities')
	->orderBy('activity_name')
	->execute();
	$groupBoxes = array();
	if ($Qactivities->count() > 0){
		foreach($Qactivities->toArray() as $activity){
			$groupBoxes[] = array(
				'value' => $activity['activity_id'],
				'label' => $activity['activity_name'],
				'labelPosition' => 'after'
			);
		}
		unset($activity);
	}
	$Qactivities->free();
	unset($Qactivities);
	
	$activityChexboxes = htmlBase::newElement('checkbox')
	->addGroup(array(
		'separator' => '<br />',
		'checked' => $checkedActivities,
		'name' => 'activities[]',
		'data' => $groupBoxes
	));
	echo $activityChexboxes->draw();
	unset($checkedActivities);
   ?></td>
  </tr>
 </table>
</fieldset>
<br />
<div id="productDesigner" style="position:relative;"><table cellpadding="0" cellspacing="0" border="0" width="100%">
 <tr>
  <td width="240" valign="top"><div class="ui-widget ui-widget-content ui-corner-all productDesignerInfoBox">
   <div class="ui-widget-header productDesignerInfoBoxHeader" style="padding:.5em;"><span>Select Item To Edit</span></div>
   <div>
    <div class="productDesignerInfoBoxContent"><?php
		require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/admin/base_app/predesigns/edit_windows/clipart.php');
		require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/admin/base_app/predesigns/edit_windows/var_clipart.php');
		require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/admin/base_app/predesigns/edit_windows/text.php');
		require(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/admin/base_app/predesigns/edit_windows/var_text.php');
	?></div>
   </div>
  </div></td>
  <td valign="top" style="padding-left:1em;">
   <div class="ui-widget ui-widget-header ui-corner-all productDesignerTopButtonBar" style="text-align:center;"><?php
   echo htmlBase::newElement('button')->setId('addTextButton')->usePreset('install')->setText('ADD TEXT')->draw();
   echo htmlBase::newElement('button')->setId('addClipartButton')->usePreset('install')->setText('ADD CLIPART')->draw();
   echo htmlBase::newElement('button')->setId('uploadImageButton')->usePreset('install')->setText('UPLOAD IMAGE')->draw();
   echo '<br />';
   echo htmlBase::newElement('button')->setId('addVariableTextButton')->usePreset('install')->setText('ADD VARIABLE TEXT')->draw();
   echo htmlBase::newElement('button')->setId('addVariableClipartButton')->usePreset('install')->setText('ADD VARIABLE CLIPART')->draw();
    ?></div>
   <div style="margin:1em 0em;z-index:1;">
    <div id="productImageHolder" style="width:<?php echo (12*$pixelsPerInch);?>px;height:<?php echo (12*$pixelsPerInch);?>px;">
     <div id="customizeArea" style="width:<?php echo (10*$pixelsPerInch);?>px;height:<?php echo (10*$pixelsPerInch);?>px;"><?php
     if (isset($design) && !empty($design[0]['predesign_settings'])){
     	$items = unserialize($design[0]['predesign_settings']);
     	//print_r($items);
     	foreach($items['text'] as $item){
     		$getVars = array(
     			'img=TEXT',
     			'noCalc=true',
     			'fontSize=' . $item['fontSize'],
    			'fontFamily=' . $item['fontFamily'],
     			'fontColor=' . $item['fontColor'],
      			'fontStroke=' . $item['fontStroke'],
     			'fontStrokeColor=' . $item['fontStrokeColor'],
     			'textTransform=' . $item['textTransform'],
     			'scale=1',
     			'zoom=1',
     			'sid=' . rand(9000000, 9999999)
     		);
     		$dataVars = array(
     			'fontSize:' . $item['fontSize'],
     			'fontFamily:\'' . $item['fontFamily'] . '\'',
     			'fontColor:\'' . $item['fontColor'] . '\'',
      			'fontStroke:' . $item['fontStroke'],
     			'fontStrokeColor:\'' . $item['fontStrokeColor'] . '\'',
    			'textTransform:\'' . $item['textTransform'] . '\'',
     			'centerHorizontal:' . $item['centerHorizontal'],
     			'centerVertical:' . $item['centerVertical'],
     			'useColorReplace:' . (isset($item['useColorReplace']) && $item['useColorReplace'] != '' ? $item['useColorReplace'] : 'false'),
     			'xPos:' . ($item['xPos'] * $pixelsPerInch),
     			'yPos:' . ($item['yPos'] * $pixelsPerInch),
     			'zIndex:' . $item['zIndex']
     		);
     		if (isset($item['textVariable'])){
     			$className = 'variableTextEntry';
      			$getVars[] = 'imageText=' . $item['textVariable'];
     			$dataVars[] = 'imageText:\'' . $item['textVariable'] . '\'';
     			$dataVars[] = 'textVariable:\'' . $item['textVariable'] . '\'';
     		}else{
     			$className = 'textEntry';
     			$getVars[] = 'imageText=' . $item['imageText'];
     			$dataVars[] = 'imageText:\'' . $item['imageText'] . '\'';
     		}
     		echo '<span class="' . $className . '" style="z-index:' . $item['zIndex'] . ';top:' . ($item['yPos'] * $pixelsPerInch) . 'px;left:' . ($item['xPos'] * $pixelsPerInch) . 'px;" data-obj="' . implode(',', $dataVars) . '">';
     		echo '<img src="' . itw_catalog_app_link('appExt=productDesigner&' . implode('&', $getVars), 'thumb_image', 'process') . '" />';
     		echo '</span>';
     	}
     	
     	foreach($items['clipart'] as $item){
     		$getVars = array(
     			'img=CLIPART',
     			'noCalc=true',
     			'scale=1',
     			'zoom=1',
     			'sid=' . rand(9000000, 9999999)
     		);
     		
     		$dataVars = array(
     			'centerHorizontal:' . $item['centerHorizontal'],
     			'centerVertical:' . $item['centerVertical'],
     			'xPos:' . ($item['xPos'] * $pixelsPerInch),
     			'yPos:' . ($item['yPos'] * $pixelsPerInch),
     			'useColorReplace:' . (isset($item['useColorReplace']) && $item['useColorReplace'] != '' ? $item['useColorReplace'] : 'false'),
     			'zIndex:' . $item['zIndex']
     		);
     		
     		if (isset($item['clipartVariable'])){
     			$className = 'variableClipartEntry';
      			$getVars[] = 'clipartVariable=' . $item['clipartVariable'];
     			$dataVars[] = 'clipartVariable:\'' . $item['clipartVariable'] . '\'';
     		}else{
     			$className = 'clipartEntry';
     			$getVars[] = 'file=' . $item['imageSrc'];
     			$dataVars[] = 'imageSrc:\'' . $item['imageSrc'] . '\'';
     		}
     		
     		if (isset($item['imageWidth'])){
     			$getVars[] = 'w=' . $item['imageWidth'];
     			$dataVars[] = 'imageWidth:' . $item['imageWidth'];
     		}
     		
     		if (isset($item['imageHeight'])){
     			$getVars[] = 'h=' . $item['imageHeight'];
     			$dataVars[] = 'imageHeight:' . $item['imageHeight'];
     		}
     		
     		echo '<span class="' . $className . '" style="z-index:' . $item['zIndex'] . ';top:' . ($item['yPos'] * $pixelsPerInch) . 'px;left:' . ($item['xPos'] * $pixelsPerInch) . 'px;" data-obj="' . implode(',', $dataVars) . '">';
     		echo '<img src="' . itw_catalog_app_link('appExt=productDesigner&' . implode('&', $getVars), 'thumb_image', 'process') . '" />';
     		echo '</span>';
     	}
     }
     ?></div>
    </div>
   </div>
  </td>
 </tr>
</table>

<!-- Clipart dialog -->
<div id="clipartDialogBox">
	<div id="clipartDialogLeft">
			<?php echo get_category_tree('0'); ?>
	</div>

	<div id="clipartDialogRight">
		<div id="clipartimages">
	
		</div>
	</div>
</div>

<!--End clipart dialog-->

</div>
<div style="text-align:right;"><?php
	echo htmlBase::newElement('button')->usePreset('save')->setId('saveButton')->setType('submit')->draw() . 
	     htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('appExt=productDesigner', 'predesigns', 'default'))->draw();
?></div>
</form>