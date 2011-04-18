<style type="text/css">
<!--
h4 {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: x-small; text-align: center}
p {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
th {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
td {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: xx-small}
-->
</style>
<table width="550" border="1" cellspacing="1" bordercolor="gray">
<tr>
<td colspan="4">
<h4><?php echo sysLanguage::get('HEADING_TITLE');?></h4>
</td>
</tr>
<?
   $coupon_get=tep_db_query("select restrict_to_categories from " . TABLE_COUPONS . " where coupon_id='".$_GET['cid']."'");
   $get_result=tep_db_fetch_array($coupon_get);
   echo "<tr><th>" . TABLE_HEADING_CATEGORY_ID . "</th><th>" . TABLE_HEADING_CATEGORY_NAME . "</th></tr><tr>";
   $cat_ids = split("[,]", $get_result['restrict_to_categories']);
   for ($i = 0; $i < count($cat_ids); $i++) {
     $result = mysql_query("SELECT * FROM categories, categories_description WHERE categories.categories_id = categories_description.categories_id and categories_description.language_id = '" . Session::get('languages_id') . "' and categories.categories_id='" . $cat_ids[$i] . "'");
     if ($row = mysql_fetch_array($result)) {
       echo "<td>".$row["categories_id"]."</td>\n";
       echo "<td>".$row["categories_name"]."</td>\n";
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