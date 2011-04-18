<?php
	$Qnewsletter = Doctrine_Query::create()
	->select('content')
	->from('Newsletters')
	->where('newsletters_id = ?', (int) $_GET['nID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
?>
<div style="text-align:right"><?php
	echo htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $_GET['nID'], 'newsletters', 'default'))
	->draw();
?></div>
<div><tt><?php
	echo nl2br($Qnewsletter[0]['content']);
?></tt></div>
<div style="text-align:right"><?php
	echo htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'nID=' . $_GET['nID'], 'newsletters', 'default'))
	->draw();
?></div>