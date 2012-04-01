<?php
	
	function addGridRow($blogClass, &$tableGrid, &$infoBoxes){
		global $allGetParams, $editButton, $deleteButton;

        $postId = $blogClass['post_id'];

        //get number of comments
		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref(itw_app_link($allGetParams . 'pID=' . $postId));
			
		$statusIcon = htmlBase::newElement('icon');
		if ($blogClass['post_status'] == '1' ){
			$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
			->setHref(itw_app_link($allGetParams . 'action=setflag&flag=0&pID=' . $postId));
		}else{
			$statusIcon->setType('circleClose')->setTooltip('Click to enable')
			->setHref(itw_app_link($allGetParams . 'action=setflag&flag=1&pID=' . $postId));
		}
		
		$rowAttr = array('infobox_id' => $postId);

		$tableGrid->addBodyRow(array(
			'rowAttr' => $rowAttr,
			'columns' => array(
				array('text' => $blogClass['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title']),
				array('text' => '<a href="'.itw_app_link($allGetParams . 'pID='.$postId, null, 'new_post').'">'.'[View/Edit/Add]'.'</a>', 'align' => 'center'),
				array('text' => $statusIcon->draw(), 'align' => 'center')
			)
		));
			
		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setButtonBarLocation('top');

		$infoBox->setHeader('<b>' . $blogClass['BlogPostsDescription'][Session::get('languages_id')]['blog_post_title'] . '</b>');
		$editButton->setHref(itw_app_link(tep_get_all_get_params(array('app', 'appName', 'action','pID')).'pID=' . $postId, null, 'new_post'));
		$deleteButton->attr('post_id', $postId);

		$infoBox->addButton($editButton)->addButton($deleteButton);
		$infoBox->addContentRow(sysLanguage::get('TEXT_DATE_ADDED') . ' ' . tep_date_short($blogClass['post_date']));

		$infoBoxes[$postId] = $infoBox->draw();
		unset($infoBox);

	}
	
	$rows = 0;
	$post_count = 0;
	$lID = (int)Session::get('languages_id');

	$Qposts = Doctrine_Query::create()
	->select('p.*, pd.*, p2c.*')
	->from('BlogPosts p')
	->leftJoin('p.BlogPostsDescription pd')
	->leftJoin('p.BlogPostToCategories p2c')
	->where('pd.language_id = ?', $lID)
	->orderBy('pd.blog_post_title asc, p.post_id desc');

	if (isset($_GET['search'])) {
		$search = $_GET['search'];
		$Qposts->andWhere('pd.blog_post_title LIKE ?', '%' . $search . '%');
	}

	
	$tableGrid = htmlBase::newElement('newGrid')
	->usePagination(true)

	->setQuery($Qposts);

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_POSTS')),
            array('text' => "Action"),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
		)
	));
	
	$posts = &$tableGrid->getResults();
	$infoBoxes = array();
	if ($posts){
		$editButton = htmlBase::newElement('button')->usePreset('edit');
		$deleteButton = htmlBase::newElement('button')->usePreset('delete')->addClass('deletePostButton');
		
		$allGetParams = tep_get_all_get_params(array('action', 'pID', 'flag'));
		foreach($posts as $post){
			$postId = (int)$post['post_id'];
			addGridRow($post, $tableGrid, $infoBoxes);
		}
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <table border="0" width="100%" cellspacing="0" cellpadding="3">
  <tr>
   <td class="smallText" align="right" colspan="2"><?php
   $searchForm = htmlBase::newElement('form')
   ->attr('name', 'search')
   ->attr('action', itw_app_link(null, null, null, 'SSL'))
   ->attr('method', 'get');
   
    $searchField = htmlBase::newElement('input')->setName('search')
    ->setLabel(sysLanguage::get('HEADING_TITLE_SEARCH'))->setLabelPosition('before');
	if (isset($_GET['search'])){
		$searchField->setValue($_GET['search']);
	}
   
   $searchForm->append($searchField);
   echo $searchForm->draw();
   ?></td>
  </tr>
 </table>
 
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   </div>
  </div>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
   <tr>
    <td align="right" class="smallText"><?php
    if (!isset($_GET['search'])){
    	$newProdButton = htmlBase::newElement('button')->usePreset('install')->setText(sysLanguage::get('TEXT_BUTTON_NEW_POST'))
    	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'pID')), null, 'new_post'));

    	echo $newProdButton->draw();
    }
    ?>&nbsp;</td>
   </tr>
  </table>
 </div>
 <div style="width:25%;float:right;"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $pID => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $pID . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>