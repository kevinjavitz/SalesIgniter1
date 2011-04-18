<div style="display:none;" class="editWindow variableTextEditor" title="You Are Editing:"><table cellpadding="3" cellspacing="0" border="0" width="100%">
 <tr>
  <td>Key: <?php 
  $selectBox = htmlBase::newElement('selectbox')->setName('textVariable');
  $Qkeys = Doctrine_Query::create()
  ->select('key_text')
  ->from('ProductDesignerPredesignKeys')
  ->where('key_type = ?', 'text')
  ->orderBy('key_text')
  ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
  if ($Qkeys){
  	foreach($Qkeys as $key){
  		$selectBox->addOption($key['key_text'], $key['key_text']);
  	}
  }
  echo $selectBox->draw();
  ?></td>
 </tr>
 <tr>
  <td><?php echo htmlBase::newElement('button')->addClass('removeButton')->setText('REMOVE')->draw();?></td>
 </tr>
 <tr>
  <td class="main" style="padding-top:1em;"><input type="checkbox" id="centerVariableHorizontal" name="centerHorizontal" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="centerVariableHorizontal">Center Horizontally</label></td>
 </tr>
 <tr>
  <td class="main" style="padding-bottom:1em;"><input type="checkbox" id="centerVariableVertical" name="centerVertical" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="centerVariableVertical">Center Vertically</label></td>
 </tr>
 <tr>
  <td><div class="ui-widget-header ui-widget ui-widget-content ui-corner-all-big moveItemContainer"><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="50%" style="border-right:1px solid #8f8f8f;color:#606060;"><b>Move Text</b></td>
    <td width="50%" align="center"><table cellpadding="0" cellspacing="0" border="0">
     <tr>
      <td></td>
      <td><div class="productDesignerArrow placementArrowNorth ui-state-default"></div></td>
      <td></td>
     </tr>
     <tr>
      <td><div class="productDesignerArrow placementArrowWest ui-state-default"></div></td>
      <td></td>
      <td><div class="productDesignerArrow placementArrowEast ui-state-default"></div></td>
     </tr>
     <tr>
      <td></td>
      <td><div class="productDesignerArrow placementArrowSouth ui-state-default"></div></td>
      <td></td>
     </tr>
    </table></td>
   </tr>
  </table></div></td>
 </tr>
 <tr>
  <td><div class="ui-widget-header ui-widget ui-widget-content ui-corner-all-big moveItemContainer"><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main" width="50%" style="border-right:1px solid #8f8f8f;color:#606060;"><b>Layer Order</b></td>
    <td width="50%" align="center"><table cellpadding="0" cellspacing="0" border="0">
     <tr>
      <td></td>
      <td><div class="productDesignerArrow zIndexArrowNorth ui-state-default"></div></td>
      <td></td>
     </tr>
     <tr>
      <td></td>
      <td><div class="productDesignerArrow zIndexArrowSouth ui-state-default"></div></td>
      <td></td>
     </tr>
    </table></td>
   </tr>
  </table></div></td>
 </tr>
 <tr>
  <td class="main" style="color:#606060;padding-top:1em;"><b>Text Transform:</b><br /><select name="textTransform">
   <option value="straight">Straight</option>
   <option value="arc_up">Arc Up</option>
   <option value="arc_down">Arc Down</option>
  </select></td>
 </tr>
 <tr>
  <td class="main" style="color:#606060;font-weight:bold;">Font:<br /><select name="fontFamily"><?php
    $fontDir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/productDesigner/fonts');
    foreach($fontDir as $fInfo){
    	if ($fInfo->isDir() || $fInfo->isDot()) continue;
    	
    	$fontName = substr($fInfo->getBasename(), 0, strpos($fInfo->getBasename(), '.'));
    	echo '<option value="' . $fInfo->getBasename() . '">' . ucfirst($fontName) . '</option>';
    }
  ?></select></td>
 </tr>
 <tr>
  <td class="main" style="color:#606060;font-weight:bold;">Size:<br /><select name="fontSize"><?php
     for($i=.25; $i<=6; $i+=.25){
    	echo '<option value="' . $i . '">' . $i . ' Inch(s)</option>';
     }
  ?></select></td>
 </tr>
 <tr>
  <td class="main" style="color:#606060;padding-top:1em;"><b>Font Stroke:</b><br /><select name="fontStroke">
   <option value="0">none</option>
   <option value="1">1</option>
   <option value="2">2</option>
   <option value="3">3</option>
   <option value="4">4</option>
   <option value="5">5</option>
  </select></td>
 </tr>
 <tr>
  <td class="main" style="color:#606060;"><input type="checkbox" name="use_color_replace" checked="checked" /><b>Use Color Replacement</b></td>
 </tr>
 <tr>
  <td><table cellpadding="1" cellspacing="0" border="0">
   <tr>
    <td class="main" style="color:#606060;"><b>Font Color:</b></td>
   </tr>
   <tr>
    <td><table cellpadding="2" cellspacing="0" border="0" class="useReplaceYes">
     <tr>
      <td><input type="radio" name="fontColor" id="primaryColor" value="primary" /><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="primaryColor">Primary Color</label></td>
     </tr>
     <tr>
      <td><input type="radio" name="fontColor" id="secondaryColor" value="secondary" /><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="secondaryColor">Secondary Color</label></td>
     </tr>
    </table>
    <table cellpadding="2" cellspacing="0" border="0" class="useReplaceNo">
     <tr>
      <td id="colorBlocks"></td>
     </tr>
    </table></td>
   </tr>
   <tr>
    <td class="main" style="color:#606060;"><b>Font Stroke Color:</b></td>
   </tr>
   <tr>
    <td><table cellpadding="2" cellspacing="0" border="0" class="useReplaceYes">
     <tr>
      <td><input type="radio" name="fontStrokeColor" id="primaryStrokeColor" value="primary" /><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="primaryStrokeColor">Primary Color</label></td>
     </tr>
     <tr>
      <td><input type="radio" name="fontStrokeColor" id="secondaryStrokeColor" value="secondary" /><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="secondaryColor">Secondary Color</label></td>
     </tr>
    </table>
    <table cellpadding="2" cellspacing="0" border="0" class="useReplaceNo">
     <tr>
      <td id="fontStrokeColorBlocks"></td>
     </tr>
    </table></td>
   </tr>
  </table></td>
 </tr>
</table></div>