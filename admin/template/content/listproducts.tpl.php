<table width="550" border="1" cellspacing="1" bordercolor="gray">
<tr>
<td colspan="3">
<h4><?php echo sysLanguage::get('HEADING_TITLE');?></h4>
</td>
</tr>
<?
   $coupon_get=tep_db_query("select restrict_to_products,restrict_to_categories from " . TABLE_COUPONS . "  where coupon_id='".$_GET['cid']."'");
   $get_result=tep_db_fetch_array($coupon_get);

    echo "<tr><th>" . TABLE_HEADING_PRODUCT_ID . "</th><th>" . TABLE_HEADING_PRODUCT_NAME . "</th><th>" . TABLE_HEADING_PRODUCT_SIZE . "</th></tr><tr>";
    $pr_ids = split("[,]", $get_result['restrict_to_products']);
    for ($i = 0; $i < count($pr_ids); $i++) {
      $result = mysql_query("SELECT * FROM products, products_description WHERE products.products_id = products_description.products_id and products_description.language_id = '" . Session::get('languages_id') . "'and products.products_id = '" . $pr_ids[$i] . "'");
      if ($row = mysql_fetch_array($result)) {
            echo "<td>".$row["products_id"]."</td>\n";
            echo "<td>".$row["products_name"]."</td>\n";
            echo "<td>".$row["products_model"]."</td>\n";
            echo "</tr>\n";
      }
    }
      echo "</table>\n";
?>
<br>
<table width="550" border="0" cellspacing="1">
<tr>
<td align=middle><input type="button" value="<?php echo sysLanguage::get('TEXT_CLOSE_WINDOW');?>" onClick="window.close()"></td>
</tr></table>