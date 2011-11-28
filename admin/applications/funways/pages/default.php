
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td align="right"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('search', itw_app_link(null,'funways','default'), 'get');
    echo sysLanguage::get('HEADING_TITLE_SEARCH') . ' ' . tep_draw_input_field('search');
    echo '</form>';
?>
                </td>
              </tr>
              <tr>
                <td class="smallText" align="right">
<?php
    echo tep_draw_form('goto', itw_app_link(null,'funways','default'), 'get');
    echo sysLanguage::get('HEADING_TITLE_GOTO') . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
    echo '</form>';
?>
                </td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
<?php
    echo tep_draw_form('select', itw_app_link(null,'funways','default'), 'get');
?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <!--<td class="dataTableHeadingContent"><?php //echo sysLanguage::get('TABLE_HEADING_SORT_ORDER'); ?></td>-->
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_SORT_ORDER'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_CATEGORIES_PRODUCTS'); ?></td>
				<td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_SUB'); ?></td>
				<td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_URL'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('TABLE_HEADING_PRODS_COUNT'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?>&nbsp;</td>
              </tr>
<?php
    $categories_count = 0;
    $rows = 0;
    if (isset($_GET['search'])) {
      $search = tep_db_prepare_input($_GET['search']);

      $categories_query = tep_db_query("select c.categories_id, c.categories_name, c.link_to, c.categories_description, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_FUNWAYS_CATEGORIES . " c where c.categories_name like '%" . tep_db_input($search) . "%' order by c.sort_order, c.categories_name");
    } else {
      $categories_query = tep_db_query("select c.categories_id, c.categories_name, c.link_to, c.categories_description, c.categories_image, c.parent_id, c.sort_order, c.date_added, c.last_modified from " . TABLE_FUNWAYS_CATEGORIES . " c where c.parent_id = '" . (int)$current_category_id . "' order by c.sort_order, c.categories_name");
    }
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $rows++;

// Get parent_id for subcategories if search
      if (isset($_GET['search'])) $cPath= $categories['parent_id'];

       $category_childs = array('childs_count' => tep_funways_childs_in_category_count($categories['categories_id']));
       $category_products = array('products_count' => tep_count_funways_products_in_category($categories['categories_id']));

      if ((!isset($_GET['cID']) && !isset($_GET['pID']) || (isset($_GET['cID']) && ($_GET['cID'] == $categories['categories_id']))) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {

        $cInfo_array = array_merge($categories, $category_childs, $category_products);
        $cInfo = new objectInfo($cInfo_array);
      }
    //  $prods_count = tep_count_funways_products_in_category($categories['categories_id']);
      if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $click = itw_app_link(tep_get_path($categories['categories_id']),'funways','default');
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $click = itw_app_link('cPath=' . $cPath . '&cID=' . $categories['categories_id'],'funways','default');
      }
?>
                <td class="dataTableContent" ><?php echo tep_draw_input_field('sort_order_cat[' . $categories['categories_id'] . ']',$categories['sort_order'],' size=7'); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<? echo $click;?>'"><?php echo '<a href="' . itw_app_link(tep_get_path($categories['categories_id'],'funways','default')) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] . '</b>'; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<? echo $click;?>'" align="center"><? echo $category_childs['childs_count']; ?> </td>
                <td class="dataTableContent" onclick="document.location.href='<? echo $click;?>'" align="center"><? echo $categories['link_to'];?></td>
                <td class="dataTableContent" onclick="document.location.href='<? echo $click;?>'" align="center"><?php echo $category_products['products_count']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<? echo $click;?>'" align="right"><?php if (isset($cInfo) && is_object($cInfo) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . itw_app_link('cPath=' . $cPath . '&cID=' . $categories['categories_id'],'funways','default') . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }

    $products_count = 0;
    if (isset($_GET['search'])) {
      $products_query = tep_db_query("select p.products_id, p.products_name, p.products_description,  p.products_date_added, p.products_last_modified, p.sort_order, p.link_to, p2c.categories_id from " . TABLE_FUNWAYS_PRODUCTS . " p, " . TABLE_FUNWAYS_PTC . " p2c where p.products_id = p2c.products_id and pd.products_name like '%" . tep_db_input($search) . "%' order by p.products_name");
    } else {
      $products_query = tep_db_query("select p.products_id, p.products_name, p.products_description, p.products_date_added, p.products_last_modified, p.sort_order, p.link_to from " . TABLE_FUNWAYS_PRODUCTS . " p, " . TABLE_FUNWAYS_PTC . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$current_category_id . "' order by p.products_name");
    }
    while ($products = tep_db_fetch_array($products_query)) {
      $products_count++;
      $rows++;

// Get categories_id for product if search
      if (isset($_GET['search'])) $cPath = $products['categories_id'];

      if ( (!isset($_GET['pID']) && !isset($_GET['cID']) || (isset($_GET['pID']) && ($_GET['pID'] == $products['products_id']))) && !isset($pInfo) && !isset($cInfo) && (substr($action, 0, 3) != 'new')) {
// find out the rating average from customer reviews
        $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . (int)$products['products_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);
        $pInfo_array = array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }

      if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id) ) {
        echo '              <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $click = itw_app_link('cPath=' . $cPath . '&pID=' . $products['products_id'],'funways','default');
      } else {
        echo '              <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
        $click = itw_app_link('cPath=' . $cPath . '&pID=' . $products['products_id'],'funways','default');
      }

      $funways_product = tep_db_fetch_array(tep_db_query("select pd.products_id, pd.products_name from " . TABLE_PRODUCTS_DESCRIPTION . " pd where language_id=1 and pd.products_id = ".$products['link_to']));

?>
                <td class="dataTableContent"><?php echo tep_draw_input_field('sort_order_prod[' . $products['products_id'] . ']',$products['sort_order'],' size=7'); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?echo $click; ?>'"><?php echo '<a href="' . itw_app_link('cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only','funways','default') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $funways_product['products_name'];?></td>
                <td class="dataTableContent" align="center" onclick="document.location.href='<?echo $click; ?>'" align="center">Product</td>
                <td class="dataTableContent" align="center" onclick="document.location.href='<?echo $click; ?>'"><a href="<? echo itw_app_link('pID=' . $funways_product['products_id'],'funways','default').'&action=new_product'?>">[Edit]</a></td>
                <td class="dataTableContent" onclick="document.location.href='<?echo $click; ?>'" align="center">Product</td>
                <td class="dataTableContent" align="right"><?php if (isset($pInfo) && is_object($pInfo) && ($products['products_id'] == $pInfo->products_id)) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . itw_app_link('cPath=' . $cPath . '&pID=' . $products['products_id'],'funways','default') . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              	</tr>
<?php
    }

    $cPath_back = '';
    if (sizeof($cPath_array) > 0) {
      for ($i=0, $n=sizeof($cPath_array)-1; $i<$n; $i++) {
        if (empty($cPath_back)) {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = (tep_not_null($cPath_back)) ? 'cPath=' . $cPath_back . '&' : '';
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo '<input type="submit" name="save_order" value="Save"><br>'.sysLanguage::get('TEXT_CATEGORIES') . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_count; ?></td>
                    <td align="right" class="smallText"><?php
                     if (sizeof($cPath_array) > 0) echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link( $cPath_back . 'cID=' . $current_category_id,'funways','default'))->draw() . '&nbsp;'; if (!isset($_GET['search'])) echo htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_NEW_CATEGORY'))->setHref(itw_app_link('cPath=' . $cPath . '&action=new_category','funways','default'))->draw() . '&nbsp;' . htmlBase::newElement('button')->setText(IMAGE_NEW_PRODUCT)->setHref(itw_app_link('cPath=' . $cPath . '&action=new_product','funways','default'))->draw(); ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
            </form>
          </td>
<?php
    $heading = array();
    $contents = array();
    switch ($action) {
      case 'new_category':
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW_CATEGORY') . '</b>');

        $contents = array('form' => tep_draw_form('newcategory', itw_app_link('action=insert_category&cPath=' . $cPath,'funways','default'), 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => sysLanguage::get('TEXT_NEW_CATEGORY_INTRO'));

        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_CATEGORIES_NAME') . tep_draw_input_field('categories_name'));
        $contents[] = array('text' => '<br>' . TEXT_URL_TEXT .'<br>' . sysLanguage::get('TEXT_LINK_TO') . tep_draw_input_field('categories_link_to'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_CATEGORIES_DESCRIPTION') . tep_draw_textarea_field('categories_description','soft',30,5));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_CATEGORIES_IMAGE') . '<br>' . tep_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_SORT_ORDER') . '<br>' . tep_draw_input_field('sort_order', '', 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath,'funways','default'))->draw());
        break;
      case 'edit_category':
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_CATEGORY') . '</b>');

        $contents = array('form' => tep_draw_form('categories', itw_app_link('action=update_category&cPath=' . $cPath,'funways','default'), 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => sysLanguage::get('TEXT_EDIT_INTRO'));

        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_CATEGORIES_NAME') . tep_draw_input_field('categories_name', $cInfo->categories_name));
        $contents[] = array('text' => '<br>' . TEXT_URL_TEXT .'<br>' . sysLanguage::get('TEXT_LINK_TO') . tep_draw_input_field('categories_link_to', $cInfo->link_to));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_CATEGORIES_DESCRIPTION') . tep_draw_textarea_field('categories_description','soft',30,5,$cInfo->categories_description));
        $contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->categories_image, $cInfo->categories_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $cInfo->categories_image . '</b>');
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_EDIT_CATEGORIES_IMAGE') . '<br>' . tep_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_EDIT_SORT_ORDER') . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));

        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id,'funways','default'))->draw());
        break;
      case 'delete_category':
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_CATEGORY') . '</b>');

        $contents = array('form' => tep_draw_form('categories', itw_app_link('action=delete_category_confirm&cPath=' . $cPath,'funways','default'),'get') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => sysLanguage::get('TEXT_DELETE_CATEGORY_INTRO'));
        $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
        if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(sysLanguage::get('TEXT_DELETE_WARNING_CHILDS'), $cInfo->childs_count));
        if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(sysLanguage::get('TEXT_DELETE_WARNING_PRODUCTS'), $cInfo->products_count));
        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('delete')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id,'funways','default'))->draw());
        break;
      case 'move_category':
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_MOVE_CATEGORY') . '</b>');

        $contents = array('form' => tep_draw_form('categories', itw_app_link('action=move_category_confirm&cPath=' . $cPath,'funways','default'),'get') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => sprintf(sysLanguage::get('TEXT_MOVE_CATEGORIES_INTRO'), $cInfo->categories_name));
        $contents[] = array('text' => '<br>' . sprintf(sysLanguage::get('TEXT_MOVE'), $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_funways_category_tree(), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('move')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id,'funways','default'))->draw());
        break;
      case 'copy_to':
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_COPY_TO') . '</b>');

        $contents = array('form' => tep_draw_form('copy_to', itw_app_link('action=copy_to_confirm&cPath=' . $cPath,'funways','default'),'get') . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => sysLanguage::get('TEXT_INFO_COPY_TO_INTRO'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_INFO_CURRENT_CATEGORIES') . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_CATEGORIES') . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id));
        $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . sysLanguage::get('TEXT_COPY_AS_DUPLICATE'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('copy')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath . '&pID=' . $pInfo->products_id,'funways','default'))->draw());
        break;

      case 'new_product':
      $funways_array = array();
      $funways_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_FUNWAYS_PRODUCTS . " f where f.link_to = p.products_id");
      while ($funways = tep_db_fetch_array($funways_query)) {
        $funways_array[] = $funways['products_id'];
      }
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_NEW_PRODUCT') . '</b>');

        $contents = array('form' => tep_draw_form('newproduct', itw_app_link('action=insert_product&cPath=' . $cPath,'funways','default'), 'post', 'enctype="multipart/form-data"').tep_draw_hidden_field('categories_id', $cPath_array[sizeof($cPath_array)-1]));
        $contents[] = array('text' => TEXT_NEW_PRODUCT_INTRO);
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_LINK_TO_PRODUCT') . tep_draw_products_down('products_link_to', 'style="width:300px"', /*$funways_array*/array()));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_PRODUCTS_DESCRIPTION') . tep_draw_textarea_field('products_description','soft',30,5));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_PRODUCTS_IMAGE') . '<br>' . tep_draw_file_field('products_image'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_SORT_ORDER') . '<br>' . tep_draw_input_field('products_sort_order', '', 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath,'funways','default'))->draw());
        break;
      case 'edit_product':
      $funways_array = array();
      $funways_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p, " . TABLE_FUNWAYS_PRODUCTS . " f where f.link_to = p.products_id and p.products_id <> ".$pInfo->link_to);
      while ($funways = tep_db_fetch_array($funways_query)) {
        $funways_array[] = $funways['products_id'];
      }

        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT_PRODUCT') . '</b>');

        $contents = array('form' => tep_draw_form('products', itw_app_link('action=update_product&cPath=' . $cPath,'funways','default'), 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('products_id', $pInfo->products_id).tep_draw_hidden_field('categories_id', $cPath));
        $contents[] = array('text' => sysLanguage::get('TEXT_EDIT_INTRO'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_LINK_TO_PRODUCT') . tep_draw_products_down('products_link_to', 'style="width:150px"', $funways_array, $pInfo->link_to));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_PRODUCTS_DESCRIPTION') . tep_draw_textarea_field('products_description','soft',30,5,$pInfo->products_description));
        $contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $pInfo->products_image, $pInfo->products_name) . '<br>' . DIR_WS_CATALOG_IMAGES . '<br><b>' . $pInfo->products_image . '</b>');
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_PRODUCTS_IMAGE') . '<br>' . tep_draw_file_field('products_image'));
        $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_EDIT_SORT_ORDER') . '<br>' . tep_draw_input_field('products_sort_order', $pInfo->sort_order, 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id,'funways','default'))->draw());
        break;
      case 'delete_product':
        $heading[] = array('text' => '<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_PRODUCT') . '</b>');

			$contents = array('form' => tep_draw_form('products', itw_app_link('action=delete_product_confirm&cPath=' . $cPath,'funways','default'),'get') . tep_draw_hidden_field('products_id', $pInfo->products_id));
			$contents[] = array('text' => sysLanguage::get('TEXT_DELETE_PRODUCT_INTRO'));
			$contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');

			$product_categories_string = '';
			$product_categories = tep_generate_funways_category_path($pInfo->products_id, 'product');
			for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
			  $category_path = '';
			  for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
				$category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
			  }
			  $category_path = substr($category_path, 0, -16);
			  $product_categories_string .= tep_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br>';
			}
			$product_categories_string = substr($product_categories_string, 0, -4);

			$contents[] = array('text' => '<br>' . $product_categories_string);
			$contents[] = array('align' => 'center', 'text' => '<br>' . htmlBase::newElement('button')->usePreset('delete')->setType('submit')->draw() . ' ' . htmlBase::newElement('button')->usePreset('cancel')->setHref(itw_app_link('cPath=' . $cPath . '&pID=' . $pInfo->products_id,'funways','default'))->draw());
        break;

      default:
        if ($rows > 0) {
          if (isset($cInfo) && is_object($cInfo)) { // category info box contents
            $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');

            $contents[] = array('align' => 'center', 'text' => htmlBase::newElement('button')->usePreset('edit')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category','funways','default'))->draw() . ' ' . htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category','funways','default'))->draw() . ' ' . htmlBase::newElement('button')->usePreset('move')->setText('Move')->setHref(itw_app_link('cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=move_category','funways','default'))->draw());
            $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_DATE_ADDED') . ' ' . tep_date_short($cInfo->date_added));
            if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => sysLanguage::get('TEXT_LAST_MODIFIED') . ' ' . tep_date_short($cInfo->last_modified));
            $contents[] = array('text' => '<br>' . tep_info_image($cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT) . '<br>' . $cInfo->categories_image);
            $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . sysLanguage::get('TEXT_PRODUCTS') . ' ' . $cInfo->products_count);
          } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents
            $heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->link_to, Session::get('languages_id')) . '</b>');

            $contents[] = array('align' => 'center', 'text' => htmlBase::newElement('button')->usePreset('edit')->setHref(itw_app_link('cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=edit_product','funways','default'))->draw() . ' ' . htmlBase::newElement('button')->usePreset('delete')->setHref(itw_app_link('cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product','funways','default'))->draw());

            $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_DATE_ADDED') . ' ' . tep_date_short($pInfo->products_date_added));
            if (tep_not_null($pInfo->products_last_modified)) $contents[] = array('text' => sysLanguage::get('TEXT_LAST_MODIFIED') . ' ' . tep_date_short($pInfo->products_last_modified));
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => sysLanguage::get('TEXT_DATE_AVAILABLE') . ' ' . tep_date_short($pInfo->products_date_available));
            $contents[] = array('text' => '<br>' . tep_info_image($pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image);
            $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_PRODUCTS_PRICE_INFO') . ' ' . $currencies->format($pInfo->products_price) . '<br>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity);
            $contents[] = array('text' => '<br>' . sysLanguage::get('TEXT_PRODUCTS_AVERAGE_RATING') . ' ' . number_format($pInfo->average_rating, 2) . '%');
          }
        } else { // create category/product info
          $heading[] = array('text' => '<b>' . sysLanguage::get('EMPTY_CATEGORY') . '</b>');

          $contents[] = array('text' => sysLanguage::get('TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS'));
        }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
    </table>
