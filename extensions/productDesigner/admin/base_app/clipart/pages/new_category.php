<?php
/*
	Product Designer Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$Categories = Doctrine_Core::getTable('ProductDesignerClipartCategories');
	if (isset($_GET['cID']) && empty($_POST)){
		$Category = $Categories->findOneByCategoriesId((int)$_GET['cID']);
		$Category->refresh(true);
	}else{
		$Category = $Categories->getRecord();
	}
	
	$languages = tep_get_languages();
	if (isset($_GET['cID'])){
		$cat = (int)$_GET['cID'];
		$Qimages = Doctrine_Query::create()
		->select('cd.*')
		->from('ProductDesignerClipartImages cd')
		->leftJoin('cd.ProductDesignerClipartImagesToCategories c')
		->where('c.categories_id = ?', $cat )
		->execute();

		$allBox = htmlBase::newElement('div')
		->css('text-align', 'center')
		->setId('allBox');

		$theHtml = '';
		if ($Qimages){
			foreach ($Qimages as $image){
				$imageId = $image['images_id'];

				$theBox = htmlBase::newElement('div')
				->addClass('theBox');

				$imgSrc = sysConfig::getDirWsCatalog() . 'extensions/productDesigner/images/clipart/'.$image['image'];
				$thumbSrc = 'imagick_thumb.php?width=100&height=100&imgSrc=' . sysConfig::getDirFsCatalog() . 'extensions/productDesigner/images/clipart/'.$image['image'];
				
				$imgObj = htmlBase::newElement('image')
				->setSource($thumbSrc);
				
				$imgA = htmlBase::newElement('a')
				->addClass('fancyBox')
				->setHref($imgSrc)
				->append($imgObj);

				$deleteIcon = htmlBase::newElement('icon')
				->setHref(itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=deleteImage&iID=' . $imageId))
				->setType('closeThick');
				
				$zoomIcon = htmlBase::newElement('icon')->setHref('#')->setType('zoomIn');
				
				$theBox->html($imgA->draw() . '<br />' . $zoomIcon->draw() . $deleteIcon->draw());
				$theHtml .= $theBox->draw();
			}
		}
		$allBox->html($theHtml);
		
	}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<form name="new_category" action="<?php echo itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action')) . 'action=saveCategory');?>" method="post" enctype="multipart/form-data">
 <div id="tab_container">
  <ul>
   <li class="ui-tabs-nav-item"><a href="#page-1"><span><?php echo sysLanguage::get('TAB_GENERAL');?></span></a></li>
   <?php if (isset($_GET['cID'])){ ?>
   <li class="ui-tabs-nav-item"><a href="#page-2"><span><?php echo sysLanguage::get('TAB_CLIPART_IMAGES');?></span></a></li>
   <?php } ?>
  </ul>
  <div id="page-1">
  <?php
  echo '<ul>';
  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
  	$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
  	$lID = $languages[$i]['id'];
  	echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lID . '"><span>' . $langImage . '&nbsp;' . $languages[$i]['name'] . '</span></a></li>';
  }
  echo '</ul>';
  
  for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
  	$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
  	$lID = $languages[$i]['id'];

  	$name = '';
  	if (isset($_GET['cID'])){
  		$name = $Category->ProductDesignerClipartCategoriesDescription[$lID]->categories_name;
  	}
?>
   <div id="langTab_<?php echo $lID;?>"><table cellpadding="3" cellspacing="0" border="0">
    <tr>
     <td class="main"><?php echo sysLanguage::get('TEXT_CATEGORIES_NAME'); ?></td>
     <td class="main"><?php echo tep_draw_input_field('categories_name[' . $lID . ']', $name); ?></td>
    </tr>
   </table></div>
<?php } ?>
   <table cellpadding="0" cellspacing="0" border="0">
    <tr>
     <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_IMAGE'); ?></td>
     <td class="main"><?php
     	echo tep_draw_file_field('categories_image');
     	if (isset($_GET['cID'])){
     		echo '<br />' . tep_image(DIR_WS_CATALOG_IMAGES . $Category->categories_image, '', 200, 200) .
     		     '<br />' . DIR_WS_CATALOG_IMAGES . 
     		     '<br /><b>' . $Category->categories_image . '</b>';
     	}
     ?></td>
    </tr>
    <tr>
     <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <tr>
     <td class="main"><?php echo sysLanguage::get('TEXT_SORT_ORDER'); ?></td>
     <td class="main"><?php echo tep_draw_input_field('sort_order', (isset($_GET['cID']) ? $Category->sort_order : ''), 'size="2"');?></td>
    </tr>
   </table>
  </div>
<?php if (isset($_GET['cID'])) { ?>
  <div id="page-2">
   <input type="file" name="file_upload" id="file_upload" />
   <div id="uploadQueue"></div>
    <?php echo tep_draw_hidden_field('cid',$_GET['cID'],"id='cid'");?>  
    <br />  
   <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin:.5em;margin-top:2em;">
    <div style="margin:5px;"><?php
    	if (isset($_GET['cID'])){			
    		echo $allBox->draw();
    		echo '<br style="clear:both;" />';
    	}
    ?></div>
   </div>
  </div>
<?php } ?>
 </div>
<textarea id="debuging" cols="50" rows="30" style="display:none;"></textarea>
 <br />
 <div style="text-align:right"><?php
 	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
 	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
 	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'appPage')), null, 'default', 'SSL'));

 	echo $saveButton->draw() . $cancelButton->draw();
 ?></div>
</form>