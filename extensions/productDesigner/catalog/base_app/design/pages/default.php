<?php
	echo tep_draw_form('cart_quantity', itw_app_link(tep_get_all_get_params(array('action')), 'shoppingCart', 'default'));
	echo tep_draw_hidden_field('products_id', $_GET['products_id']);
?>
<div id="productDesigner" style="position:relative;"><table cellpadding="0" cellspacing="0" border="0" width="100%">
 <tr>
  <td width="240" valign="top"><div class="ui-widget ui-widget-content ui-corner-all productDesignerInfoBox">
   <div class="ui-widget-header productDesignerInfoBoxHeader"><span>Select Item To Edit</span></div>
   <div>
    <div class="productDesignerInfoBoxContent">
<div style="display:none;" class="editWindow textEditor" title="You Are Editing:"><table cellpadding="3" cellspacing="0" border="0" width="100%">
 <tr>
  <td><?php echo '<input type="text" name="edit_image_text" style="width:98%;">';?></td>
 </tr>
 <tr>
  <td><?php echo htmlBase::newElement('button')->addClass('removeButton')->setText('REMOVE')->draw();?></td>
 </tr>
 <tr>
  <td class="main" style="padding-top:1em;"><input type="checkbox" id="textCenterHorizontal" name="centerHorizontal" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="textCenterHorizontal">Center Horizontally</label></td>
 </tr>
 <tr>
  <td class="main" style="padding-bottom:1em;"><input type="checkbox" id="textCenterVertical" name="centerVertical" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="textCenterVertical">Center Vertically</label></td>
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
  <td class="main" style="color:#606060;"><b>Font Color:</b></td>
 </tr>
 <tr>
  <td id="colorBlocks"></td>
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
  <td class="main" style="color:#606060;"><b>Font Stroke Color:</b></td>
 </tr>
 <tr>
  <td id="fontStrokeColorBlocks"></td>
 </tr>
</table></div>

<div style="display:none;" class="editWindow clipartEditor" title="You Are Editing:"><table cellpadding="3" cellspacing="0" border="0" width="100%">
 <tr>
  <td><?php echo htmlBase::newElement('button')->addClass('removeButton')->setText('REMOVE')->draw();?></td>
 </tr>
 <tr>
  <td class="main" style="padding-top:1em;"><input type="checkbox" id="clipartCenterHorizontal" name="centerHorizontal" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="clipartCenterHorizontal">Center Horizontally</label></td>
 </tr>
 <tr>
  <td class="main" style="padding-bottom:1em;"><input type="checkbox" id="clipartCenterVertical" name="centerVertical" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="clipartCenterVertical">Center Vertically</label></td>
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

<div style="display:none;" class="editWindow imageEditor" title="You Are Editing:"><table cellpadding="3" cellspacing="0" border="0" width="100%">
 <tr>
  <td><?php echo htmlBase::newElement('button')->addClass('removeButton')->setText('REMOVE')->draw();?></td>
 </tr>
 <tr>
  <td class="main" style="padding-top:1em;"><input type="checkbox" id="clipartCenterHorizontal" name="centerHorizontal" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="clipartCenterHorizontal">Center Horizontally</label></td>
 </tr>
 <tr>
  <td class="main" style="padding-bottom:1em;"><input type="checkbox" id="clipartCenterVertical" name="centerVertical" style="vertical-align:middle;line-height:1em;"><label style="vertical-align:middle;line-height:1em;font-weight:bold;color:#606060;" for="clipartCenterVertical">Center Vertically</label></td>
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

    </div>
   </div>
  </div></td>
  <td valign="top" style="padding-left:1em;">
   <div class="ui-widget ui-widget-header ui-corner-all productDesignerTopButtonBar"><?php
   echo htmlBase::newElement('button')->setId('addTextButton')->setText('<span class="ui-widget ui-widget-content ui-icon ui-icon-add-text ui-corner-all"></span><span class="buttonText">ADD TEXT</span>')->draw();
   echo htmlBase::newElement('button')->setId('addClipartButton')->setText('<span class="ui-widget ui-widget-content ui-icon ui-icon-add-text ui-corner-all"></span><span class="buttonText">ADD CLIPART</span>')->draw();
   echo htmlBase::newElement('button')->setId('uploadImageButton')->setText('<span class="ui-widget ui-widget-content ui-icon ui-icon-add-text ui-corner-all"></span><span class="buttonText">UPLOAD IMAGE</span>')->draw();
    ?></div>
   <div style="margin:1em 0em;">
    <div id="productImageHolder">
     <img id="designerImage" src="<?php echo $product->getImage();?>" width="300" height="300">
     <div id="customizeArea"><?php
     if (!empty($product->productInfo['predesign_id'])){
		$Qdesign = Doctrine_Query::create()
		->from('ProductDesignerPredesigns')
		->where('predesign_id = ?', $product->productInfo['predesign_id'])
		->execute();
		if ($Qdesign->count() > 0){
			$design = $Qdesign->toArray();
		}
     }
     
     if (isset($design) && !empty($design[0]['predesign_settings'])){
     	$items = unserialize($design[0]['predesign_settings']);
     	
 		$QtextKeys = Doctrine_Query::create()
		->from('ProductDesignerPredesignKeys k')
		->leftJoin('k.ProductDesignerPredesignKeysToStores k2s')
		->where('k.set_from = ?', 'admin')
		//->andWhere('k.key_type = ?', 'text')
		->andWhere('k2s.stores_id = ?', Session::get('current_store_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		
		$textKeys = $QtextKeys;
    	//print_r($items);

    	$multiStore = $appExtension->getExtension('multiStore');
		$storeInfo = $multiStore->storeInfo;
		$colorTone = 'light';
     	foreach($items['text'] as $item){
			$fontColor = $storeInfo['designer_' . $colorTone . '_primary_color'];
			$strokeColor = $storeInfo['designer_' . $colorTone . '_secondary_color'];
			
      		$getVars = array(
     			'img=TEXT',
     			'noCalc=true',
     			'fontSize=' . $item['fontSize'],
    			'fontFamily=' . $item['fontFamily'],
     			'fontColor=' . $fontColor,
      			'fontStroke=' . $item['fontStroke'],
     			'fontStrokeColor=' . $strokeColor,
     			'textTransform=' . $item['textTransform'],
     			'scale=1.92',
     			'zoom=.5',
     			'sid=' . rand(9000000, 9999999)
     		);
     		
    		$dataVars = array(
     			'fontSize:' . $item['fontSize'],
     			'fontFamily:\'' . $item['fontFamily'] . '\'',
     			'fontColor:\'' . $fontColor . '\'',
      			'fontStroke:' . $item['fontStroke'],
     			'fontStrokeColor:\'' . $strokeColor . '\'',
    			'textTransform:\'' . $item['textTransform'] . '\'',
     			'centerHorizontal:' . $item['centerHorizontal'],
     			'centerVertical:' . $item['centerVertical'],
     			'xPos:' . $item['xPos'],
     			'yPos:' . $item['yPos'],
     			'zIndex:' . $item['zIndex']
     		);
     		if (isset($item['textVariable'])){
				$imageText = $item['textVariable'];
				if (is_array($textKeys) && sizeof($textKeys) > 0){
					foreach($textKeys as $keyInfo){
						if ($keyInfo['key_text'] == strtoupper($imageText)){
							$imageText = $keyInfo['ProductDesignerPredesignKeysToStores'][0]['content'];
							break;
						}
					}
				}
				
    			$className = 'textEntry';
      			$getVars[] = 'imageText=' . $imageText;
     			$dataVars[] = 'imageText:\'' . $imageText . '\'';
     		}else{
     			$className = 'textEntry';
     			$getVars[] = 'imageText=' . $item['imageText'];
     			$dataVars[] = 'imageText:\'' . $item['imageText'] . '\'';
     		}
     		echo '<span class="' . $className . '" style="z-index:' . $item['zIndex'] . ';top:' . (($item['yPos'] * .5) / 1.92) . 'px;left:' . (($item['xPos'] * .5) / 1.92) . 'px;" data-obj="' . implode(',', $dataVars) . '">';
     		echo '<img src="' . itw_app_link('appExt=productDesigner&' . implode('&', $getVars), 'thumb_image', 'process', 'NONSSL') . '" />';
     		echo '</span>';
     	}
     	
     	foreach($items['clipart'] as $item){
     		$getVars = array(
     			'img=CLIPART',
     			'noCalc=true',
     			'scale=1.92',
     			'zoom=.5',
     			'sid=' . rand(9000000, 9999999)
     		);
     		
     		$dataVars = array(
     			'centerHorizontal:' . $item['centerHorizontal'],
     			'centerVertical:' . $item['centerVertical'],
     			'xPos:' . $item['xPos'],
     			'yPos:' . $item['yPos'],
     			'zIndex:' . $item['zIndex']
     		);
     		
     		if (isset($item['clipartVariable'])){
 				$imageClipart = $item['clipartVariable'];
				if (is_array($textKeys) && sizeof($textKeys) > 0){
					foreach($textKeys as $keyInfo){
						if ($keyInfo['key_text'] == strtoupper($imageClipart)){
							$imageClipart = $keyInfo['ProductDesignerPredesignKeysToStores'][0]['content'];
							break;
						}
					}
				}
				
     			$className = 'clipartEntry';
     			$getVars[] = 'file=' . $imageClipart;
     			$getVars[] = 'fileDir=images/';
     			$dataVars[] = 'fileDir:\'images/\'';
     			$dataVars[] = 'imageSrc:\'' . $imageClipart . '\'';
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
     		
     		echo '<span class="' . $className . '" style="z-index:' . $item['zIndex'] . ';top:' . (($item['yPos'] * .5) / 1.92) . 'px;left:' . (($item['xPos'] * .5) / 1.92) . 'px;" data-obj="' . implode(',', $dataVars) . '">';
     		echo '<img src="' . itw_app_link('appExt=productDesigner&' . implode('&', $getVars), 'thumb_image', 'process', 'NONSSL') . '" />';
     		echo '</span>';
     	}
     }
     ?></div>
    </div>
    <div style="margin:.5em;text-align:center;">Zoom: <select id="imgZoom"><?php
     for($i=.5;$i<2.1;$i+=.1){
     	echo '<option value="' . $i . '"' . ($i == .5 ? ' selected' : '') . '>' . ($i*100) . '%</option>';
     }
    ?></select></div>
   </div>
   <div class="ui-widget ui-widget-header ui-corner-all quantityBar" style="padding:1em;"><table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
     <td class="main"><?php
	echo htmlBase::newElement('button')->css(array(
		'margin' => '0em',
		'float' => 'right'
	))->setName('buy_new_product')->setText('ADD TO CART')->setType('submit')->draw();
	
	echo $product->displayPrice('new');
	
	//echo 'Quantity: <input type="text" value="1" name="quantity" size="4">';
	
	echo '<div class="productInfoSizeSelector">
	 <b>Size & Qty: <span style="font-size:.8em;text-decoration:underline;"><a href="' . itw_app_link('appExt=infoPages', 'show_page', $sizingInfoPage['page_key']) . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', $sizingInfoPage['page_key'], 'NONSSL') . '\',\'640px\',\'340px\');return false;">Size Chart</a></span></b><br />';
	
	if (($Extension_attributes = $appExtension->getExtension('attributes')) !== false){
		$attributes = $Extension_attributes->drawAttributes(array('return_array' => true));
		if (isset($attributes[3])){
			$sizeAttribs = $attributes[3];
			$headerCols = array();
			$bodyCols = array();
			foreach($sizeAttribs['ProductsOptionsValues'] as $vInfo){
				$headerCols[] = array(
					'addCls' => 'smallText',
					'text' => $vInfo['options_values_name'] . ($vInfo['options_values_price'] > 0 ? '*': '')
				);
				
				$bodyCols[] = array(
					'addCls' => 'smallText',
					'text' => '<input type="text" size="2" name="id[3][' . $vInfo['options_values_id'] . '][]" />'
				);
			}
			
			$attribTable = htmlBase::newElement('table')->setCellPadding(1)->setCellSpacing(0)->attr('width', '100%');
			
			$headerCols[] = array('text' => ' ');
			$bodyCols[] = array('addCls' => 'smallText', 'text' => '<span style="font-size:.9em;padding-left:.3em;line-height:.9em;"><b>* Indicates an additional size charge</b></span>');
			
			$attribTable->addHeaderRow(array(
				'columns' => $headerCols
			))->addBodyRow(array(
				'columns' => $bodyCols
			));
			echo $attribTable->draw();
		}
	}
	
	echo '</div>';
     ?></td>
    </tr>
   </table></div>
   <div class="productDesignerBottomButtonBar"><?php
   echo htmlBase::newElement('button')->disable(true)->setText('<span class="ui-icon ui-icon-mail-closed" style="display:inline-block;vertical-align:middle;"></span><span style="vertical-align:middle;margin-left:.5em;">EMAIL A FRIEND</span>')->draw();
   echo htmlBase::newElement('button')->disable(true)->setText('<span class="ui-icon ui-icon-circle-plus" style="display:inline-block;vertical-align:middle;"></span><span style="vertical-align:middle;margin-left:.5em;">SHARE THIS DESIGN</span>')->draw();
   echo htmlBase::newElement('button')->disable(true)->setText('<span class="ui-icon ui-icon-disk" style="display:inline-block;vertical-align:middle;"></span><span style="vertical-align:middle;margin-left:.5em;">SAVE THIS DESIGN</span>')->draw();
   ?></div>
  </td>
 </tr>
</table></div>
<!-- Clipart dialog -->
<div id="clipartDialogBox">
 <div id="clipartDialogLeft"><?php
 	echo get_category_tree('0');
 ?></div>
 <div id="clipartDialogRight">
  <div id="clipartimages"></div>
 </div>
</div>
<!--End clipart dialog-->
</form>