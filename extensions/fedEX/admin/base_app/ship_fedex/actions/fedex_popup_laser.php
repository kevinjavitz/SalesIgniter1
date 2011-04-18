<?php
	
	$oID = $HTTP_GET_VARS['oID'];
	$active = $HTTP_GET_VARS['active'];
	

	// if there's no other indication, first label should be active label
	if (!$active) {
		$active = 1;
		}
	
	$multiple = array();
	
	$QPackages = Doctrine_Query::create()
                 ->select('multiple, tracking_num')
                 ->from('ShippingManifest')
                 ->where('orders_id = ?', $oID)
                 ->orderBy('multiple desc')
                 ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    $multiple = array();
    if (count($QPackages)){
        foreach($QPackages as $pInfo){
            $multiple[] = $pInfo['multiple'];
        }
    }
	
	// get the highest value from $multiple, it's the last label
	if ($multiple) {
		$last = max($multiple);
		}
	
	// now get the tracking number for the selected (active) label
	if ($multiple) {        
		$tracking_num = $QPackages[$active - 1]['tracking_num'];
		}
	elseif (!$multiple) {
		$tracking_num = $_GET['num'];
		}

?>

    <script language="JavaScript1.1" type="text/javascript">
    var NS4 = (document.layers) ? true : false ;var resolution = 96;if (NS4 && navigator.javaEnabled()){var toolkit = java.awt.Toolkit.getDefaultToolkit();resolution = toolkit.getScreenResolution();}
    </script>
    <script language="JavaScript" type="text/javascript">
    document.write('<img WIDTH=' + (675 * resolution )/100 + '<img HEIGHT=' + (467 * resolution )/100 + ' alt="ASTRA Barcode" src="extensions/fedEX/images/<?php echo $tracking_num; ?>.png">');
    </script>

    <table border="0" width="100%">
      <tr>
        <td colspan="3">
          <img src="images/pixel_trans.gif" border="0" alt=""
          width="1" height="100">
        </td>
      </tr>
      <tr>
        <td align="center">
          <a href="#" onclick=
          "window.print(); return false">Print</a>
        </td>
				<td align="center">
<?php

// links for multiple packages

	if ($multiple) {
		if ($active != 1) {
			echo '<a href="?oID=' . $oID . '&active=' . ($active-1) . '">&lt;-- previous</a> &nbsp; ';
			}
		else {
			echo '&lt;-- previous';
			}
		foreach ($multiple as $package_num) {
			if ($active != $package_num) {
				echo ' &nbsp; <a href="?oID=' . $oID . '&active=' . $package_num . '">' . $package_num . '</a> &nbsp; ';
				}
			else {
				echo ' <b>' . $package_num . '</b> ';
				}
			}
		if ($active != $last) {
			echo ' &nbsp; <a href="?oID=' . $oID . '&active=' . ($active+1) . '">next --&gt;</a>';
			}
		else {
			echo ' &nbsp; next --&gt;';
			}
		}		
?>
        </td>
				<td align="center">
            <?php
                        echo '<a href="'.sysConfig::getDirWsAdmin().'orders/default.php?oID='.$oID.'">Back to order</a>'
            ?>
        </td>
      </tr>
    </table>
