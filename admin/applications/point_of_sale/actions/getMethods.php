<?php
	EventManager::attachActionResponse($pointOfSale->getMethods($_GET['method']), 'html');
?>