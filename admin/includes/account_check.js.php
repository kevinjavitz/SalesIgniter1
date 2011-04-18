<?php
/*
  $Id: account_check.js.php,v 1.8 2003/02/10 22:30:55 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>

<script language="JavaScript" type="text/JavaScript">
<!--
/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

function echeck(str) {

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   //alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   //alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		   // alert("Invalid E-mail ID")
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		   // alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		   // alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    //alert("Invalid E-mail ID")
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    //alert("Invalid E-mail ID")
		    return false
		 }

 		 return true					
	}
	
<?php
if (substr(basename($PHP_SELF), 0, 12) == 'admin_member') {
?>

function validateForm() { 
  var p,z,xEmail,errors='',dbEmail,result=0,i;

  var adminName1 = document.newmember.admin_firstname.value;
  var adminName2 = document.newmember.admin_lastname.value;
  var adminEmail = document.newmember.admin_email_address.value;
  
  if (adminName1 == '') { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_FIRSTNAME'); ?>';
  } else if (adminName1.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) { 
    errors+='- Firstname length must over  <?php echo (ENTRY_FIRST_NAME_MIN_LENGTH); ?>\n';
  }

  if (adminName2 == '') { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_LASTNAME'); ?>';
  } else if (adminName2.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) { 
    errors+='- Lastname length must over  <?php echo (ENTRY_LAST_NAME_MIN_LENGTH);  ?>\n';
  }

  if (adminEmail == '') {
    errors+='<?php echo sysLanguage::get('JS_ALERT_EMAIL'); ?>';
  } else if (echeck(adminEmail) == false) {
    errors+='<?php echo sysLanguage::get('JS_ALERT_EMAIL_FORMAT'); ?>';
  } else if (adminEmail.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    errors+='<?php echo sysLanguage::get('JS_ALERT_EMAIL_FORMAT'); ?>';
  }

  if (errors) alert('The following error(s) occurred:\n'+errors);
  document.returnValue = (errors == '');
}


function checkGroups(obj) {
  var subgroupID,i;
  subgroupID = eval("this.defineForm.subgroups_"+parseFloat((obj.id).substring(7)));
    
  if (subgroupID.length > 0) {
    for (i=0; i<subgroupID.length; i++) {
      if (obj.checked == true) { subgroupID[i].checked = true; }
      else { subgroupID[i].checked = false; }
    }
  } else {
    if (obj.checked == true) { subgroupID.checked = true; }
    else { subgroupID.checked = false; }
  }
}

function checkSub(obj) {
  var groupID,subgroupID,i,num=0;
  groupID = eval("this.defineForm.groups_"+parseFloat((obj.id).substring(10)));
  subgroupID = eval("this.defineForm."+(obj.id));
      
  if (subgroupID.length > 0) {    
    for (i=0; i < subgroupID.length; i++) {
      if (subgroupID[i].checked == true) num++;
    }
  } else {
    if (subgroupID.checked == true) num++;
  }
  if (num>0) { groupID.checked = true; }
  else { groupID.checked = false; }
}

<?php
} else {
?>

function validateForm() { 
  var p,z,xEmail,errors='',dbEmail,result=0,i;

  var adminName1 = document.account.admin_firstname.value;
  var adminName2 = document.account.admin_lastname.value;
  var adminEmail = document.account.admin_email_address.value;
  var adminPass1 = document.account.admin_password.value;
  var adminPass2 = document.account.admin_password_confirm.value;
  
  if (adminName1 == '') { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_FIRSTNAME'); ?>';
  } else if (adminName1.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_FIRSTNAME_LENGTH') . ENTRY_FIRST_NAME_MIN_LENGTH; ?>\n';
  }

  if (adminName2 == '') { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_LASTNAME'); ?>';
  } else if (adminName2.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_LASTNAME_LENGTH') . ENTRY_LAST_NAME_MIN_LENGTH;  ?>\n';
  }

  if (adminEmail == '') {
    errors+='<?php echo sysLanguage::get('JS_ALERT_EMAIL'); ?>';
  } else if (echeck(adminEmail) == false) {
    errors+='<?php echo sysLanguage::get('JS_ALERT_EMAIL_FORMAT'); ?>';
  } else if (adminEmail.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
    errors+='<?php echo sysLanguage::get('JS_ALERT_EMAIL_FORMAT'); ?>';
  }
  
  if (adminPass1 == '') { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_PASSWORD'); ?>';
  } else if (adminPass1.length < <?php echo ENTRY_PASSWORD_MIN_LENGTH; ?>) { 
    errors+='<?php echo sysLanguage::get('JS_ALERT_PASSWORD_LENGTH') . ENTRY_PASSWORD_MIN_LENGTH; ?>\n';
  } else if (adminPass1 != adminPass2) {
    errors+='<?php echo sysLanguage::get('JS_ALERT_PASSWORD_CONFIRM'); ?>';
  }
  
  if (errors) alert('The following error(s) occurred:\n'+errors);
  document.returnValue = (errors == '');
}

<?php
}
?>

//-->
</script>