<div style="display:none;" class="editWindow variableClipartEditor" title="You Are Editing:"><table cellpadding="3" cellspacing="0" border="0" width="100%">
 <tr>
  <td>Key: <?php 
  $selectBox = htmlBase::newElement('selectbox')->setName('clipartVariable');
  $Qkeys = Doctrine_Query::create()
  ->select('key_text')
  ->from('ProductDesignerPredesignKeys')
  ->where('key_type = ?', 'clipart')
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
  <td class="main" style="padding-top:1em;"><input type="checkbox" id="centerVariableClipartHorizontal" name="centerHorizontal" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="centerVariableClipartHorizontal">Center Horizontally</label></td>
 </tr>
 <tr>
  <td class="main" style="padding-bottom:1em;"><input type="checkbox" id="centerVariableClipartVertical" name="centerVertical" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="centerVariableClipartVertical">Center Vertically</label></td>
 </tr>
 <tr>
  <td class="main" style="color:#606060;"><input type="checkbox" name="use_color_replace" checked="checked" /><b>Use Color Replacement</b></td>
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
</table></div>