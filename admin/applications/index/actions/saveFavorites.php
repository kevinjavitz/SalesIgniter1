<?php
	$favoritesString = htmlspecialchars_decode($_POST['favs']);

 	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
  	$favoritesl = '';
   	$favoritest = '';
  	if(preg_match_all("/$regexp/siU", $favoritesString, $matches)) {
		$allfavoritesl = $matches[2];
		$allfavoritest = $matches[3];
  	}

	for($i=0;$i<sizeof($allfavoritesl);$i++){
		if (strpos($allfavoritesl[$i], 'removeFromFavorites') == 0){
			$favoritesl .= $allfavoritesl[$i] . ';';
			$favoritest .= $allfavoritest[$i] . ';';
		}
	}

 	$Admin = Doctrine_Core::getTable('Admin')->findOneByAdminId((int)Session::get('login_id'));
	$Admin->favorites_links = $favoritesl;
	$Admin->favorites_names = $favoritest;
	$Admin->save();

    $json = array(
			'success' => true
	);

	EventManager::attachActionResponse($json, 'json');
?>