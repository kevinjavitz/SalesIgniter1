<?php
$languages = sysLanguage::getLanguages();

$toLangDrop = htmlBase::newElement('selectbox')->setName('toLanguage');
foreach(sysLanguage::getGoogleLanguages() as $code => $lang){
	$toLangDrop->addOption($code, $lang);
}
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script>
google.load("language", "1");
</script>
<div class="pageHeading"><?php
echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="relativeParent" style="position:relative;">
	<div class="ui-widget ui-widget-content" style="height:600px;width:275px;overflow:auto;position:absolute;left:0em;top:0em;"><?php
	echo '<div class="ui-widget-content ui-corner-all ui-state-default editLink ui-state-active" style="padding:.5em;margin:.3em;" template_id="new">New Email Template</div>';
	$Qtemplates = Doctrine_Query::create()
			->from('EmailTemplates')
			->orderBy('email_templates_name')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qtemplates as $tInfo){
		echo '<div class="ui-widget-content ui-corner-all ui-state-default editLink" style="padding:.5em;margin:.3em;" template_id="' . $tInfo['email_templates_id'] . '">' . $tInfo['email_templates_name'] . '</div>';
	}
	?></div>
	<form name="emailTemplate" action="<?php echo itw_app_link('action=saveEmailTemplate'); ?>" method="post" enctype="multipart/form-data">
		<div class="ui-widget ui-widget-content ui-corner-all" style="position:relative;margin-left:285px;margin-bottom:1em;text-align:right;"><?php
		echo '<span style="float:left;margin-left:.5em;line-height:3em;">' . htmlBase::newElement('icon')->setType('circleTriangleWest')->draw() . '<b><u>Click template to the left to edit</u></b></span>';
		echo htmlBase::newElement('button')
			->usePreset('save')
			->setType('submit')
			->addClass('saveButton')
			->css('margin', '.3em')
			->draw();
		?></div>
		<div id="templateConfigure" class="ui-widget ui-widget-content ui-corner-all" style="position:relative;margin-left:285px;margin-top:.5em;">
			<div style="margin:.5em;">
				<table cellpadding="3" cellspacing="0" border="0" width="100%">
					<tr>
						<td class="main" width="150"><b>Template Name:</b></td>
						<td class="main"><input type="text" id="emailTemplate" name="email_template" style="width:80%"></td>
					</tr>
					<tr>
						<td class="main"><b>Event Name:</b></td>
						<td class="main"><input type="text" name="email_event" id="emailEvent" style="width:80%"></td>
					</tr>
					<tr>
						<td class="main"><b>Attached File:</b></td>
						<td class="main"><input type="text" id="emailAtt" class="emailAtt" name="email_att" style="width:80%"><br><input type="file" id="emailFile" class="emailFile" name="email_file" value="" style="width:80%"></td>
					</tr>
				</table>
			</div>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" id="varsTable">
				<tr>
					<td valign="top" width="33%"><?php
				echo '<div class="ui-widget-header" style="padding:.3em;">' . sysLanguage::get('HEADING_GLOBAL_VARS') . '</div>' .
				'<div class="main globalVars" style="margin:.5em;">$store_name<br>$store_owner<br>$store_owner_email<br>$today_short<br>$today_long<br>$store_url<br></div>';
				?></td>
					<td valign="top" width="33%" style="padding: 0em 1em;"><?php
						echo '<div class="ui-widget-header" style="padding:.3em;"><span class="ui-icon ui-icon-plusthick addStandardVar" style="float:right;"></span>' . sysLanguage::get('HEADING_AVAIL_VARS') . '</div>' .
						'<div class="main standardVars" style="margin:.5em;"><span class="noVars">No Variables Available.</span></div>';
				?></td>
					<td valign="top" width="33%"><?php
						echo '<div class="ui-widget-header" style="padding:.3em;"><span class="ui-icon ui-icon-plusthick addConditionVar" style="float:right;"></span>' . sysLanguage::get('HEADING_COND_VARS') . '</div>' .
						'<div class="main conditionVars" style="margin:.5em;"><span class="noVars">No Variables Available.</span></div>';
				?></td>
				</tr>
			</table>
			<!--<div class="main" style="text-align:right;"><?php
				echo 'From: English&nbsp;&nbsp;&nbsp;' .
				'To: ' . $toLangDrop->draw() . '&nbsp;&nbsp;&nbsp;' .
				htmlBase::newElement('button')->setId('googleTranslate')->setText('Translate Using Google')->draw() . '<br>' .
				'<div id="googleBrand"></div>';
			?></div>-->
			<div class="ui-tabs-container" style="margin:.5em;">
				<ul>
					<?php foreach($languages as $lInfo){ ?>
					<li><a href="#tab_<?php echo $lInfo['id']; ?>"><span><?php echo $lInfo['showName'](); ?></span></a></li>
					<?php } ?>
				</ul>
				<?php foreach($languages as $lInfo){ ?>
				<div id="tab_<?php echo $lInfo['id']; ?>" lang_name="<?php echo $lInfo['name']; ?>">
					<b>Subject:</b> <input type="text" class="emailSubject" name="email_subject[<?php echo $lInfo['id']; ?>]" value="" style="width:80%"><br /><br />
					<textarea rows="20" cols="100" style="width:100%" name="email_text[<?php echo $lInfo['id']; ?>]" class="makeFCK"></textarea><br /><br />
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="ui-widget ui-widget-content ui-corner-all" style="margin-left:285px;margin-top:1em;text-align:right;"><?php
			echo htmlBase::newElement('button')
				->usePreset('save')
				->setType('submit')
				->addClass('saveButton')
				->css('margin', '.3em')
				->draw();
		?></div>
	</form>
</div>