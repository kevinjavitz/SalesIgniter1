<?php
	$QsearchWords = Doctrine_Query::create()
	->select('DISTINCT search_text, COUNT(*) as total')
	->from('SearchQueries')
	->groupBy('search_text')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QsearchWords){
		foreach($QsearchWords as $sInfo){
			$Qcheck = Doctrine_Query::create()
			->from('SearchQueriesSorted')
			->where('search_text = ?', $sInfo['search_text'])
			->execute();
			
			if ($Qcheck->count() > 0){
				$Qcheck->search_count += $sInfo['total'];				
				$Qcheck->save();
			}else{
				$newRecord = new SearchQueriesSorted();
				$newRecord->search_count = $sInfo['total'];
				$newRecord->search_text = $sInfo['search_text'];
				$newRecord->save();
			}
		}
		
		Doctrine_Query::create()
		->delete('SearchQueries')
		->execute();
	}
	
	EventManager::attachActionResponse(itw_app_link(null, 'statistics', 'keywords'), 'redirect');
?>