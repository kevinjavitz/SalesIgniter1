<?php
$original = $_GET['string'];
$translated = sysLanguage::translateText($original, $_GET['to'], $_GET['from']);

EventManager::attachActionResponse($translated[0], 'html');
