<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

$rows = 0;


$Query = Doctrine_Query::create()
->select('c.*, pc.*')
->from('BlogCommentToPost c')
->leftJoin('c.BlogComments pc')
->orderBy('pc.comment_date desc');

if (isset($Post)){
	$Query->andWhere('c.blog_post_id = ?', (int) $Post['post_id']);
}

$tableGrid = htmlBase::newElement('newGrid')
->usePagination(true)
->setPageLimit((isset($_GET['limit']) ? (int) $_GET['limit'] : 25))
->setCurrentPage((isset($_GET['page']) ? (int) $_GET['page'] : 0))
->setQuery($Query);

$tableGrid->addHeaderRow(array('columns' => array(array('text' => sysLanguage::get('TABLE_HEADING_COMMENTS')), array('text' => sysLanguage::get('TABLE_HEADING_STATUS')), array('text' => sysLanguage::get('TABLE_HEADING_ACTION')))));

$comments = &$tableGrid->getResults();
if ($comments){
	foreach ($comments as $comment){
		$commentId = $comment['blog_comment_id'];
		$rows++;
		$cInfo = new objectInfo($comment);

		$statusIcon = htmlBase::newElement('icon');
		if ($cInfo->BlogComments['comment_status'] == '1'){
			$statusIcon->setType('circleCheck')->setTooltip('Click to disable')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=setCommentflag&flag=0&cID=' . $commentId));
		} else{
			$statusIcon->setType('circleClose')->setTooltip('Click to enable')
			->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'action=setCommentflag&flag=1&cID=' . $commentId));
		}

		$arrowIcon = htmlBase::newElement('icon')
		->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $commentId, null, null, 'SSL'));

		if (isset($cInfo) && $commentId == $cInfo->blog_comment_id && isset($_GET['cID']) && $_GET['cID'] == $commentId){
			$myInfo = $cInfo;
			$addCls = 'ui-state-default';
			$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'cID')), null, null, 'SSL');
			$arrowIcon->setType('circleTriangleEast');
		} else{
			$addCls = '';
			$onclickLink = itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $commentId . "#page-comments", null, null, 'SSL');
			$arrowIcon->setType('info');
		}

		$tableGrid->addBodyRow(array('addCls' => $addCls, 'click' => 'document.location=\'' . $onclickLink . '\'', 'columns' => array(array('text' => $comment['BlogComments']['comment_text']), array('text' => $statusIcon->draw(), 'align' => 'right'), array('text' => $arrowIcon->draw(), 'align' => 'right'))));
	}
}


$infoBox = htmlBase::newElement('infobox');
$editButton = htmlBase::newElement('button')->usePreset('edit');
$deleteButton = htmlBase::newElement('button')->usePreset('delete');
if(isset($myInfo)){
	$cInfo = $myInfo;
}
if (!empty($action)){
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, null, 'SSL'));
}

switch ($action) {
	case 'delete_comment':
		$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_CATEGORY') . '</b>');
		$infoBox->setForm(array('name' => 'comment', 'action' => itw_app_link(tep_get_all_get_params(array('action')) . 'action=deleteCommentConfirm', null, null, 'SSL')));

		$deleteButton->setType('submit');

		$infoBox->addButton($deleteButton)->addButton($cancelButton);

		$infoBox->addContentRow(sysLanguage::get('TEXT_DELETE_CATEGORY_INTRO') . tep_draw_hidden_field('comment_id', $cInfo->blog_comment_id));
		$infoBox->addContentRow('<b>' . $cInfo->BlogComments['comment_text'] . '</b>');

		break;
	default:
		$infoBox->setButtonBarLocation('top');
		if (isset($cInfo) && is_object($cInfo)){ // category info box contents
			$commentAuthor = $cInfo->BlogComments['comment_author'];
			$commentId = $cInfo->blog_comment_id;

			$infoBox->setHeader('<b>' . $commentAuthor . '</b>');

			$editButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cInfo->blog_comment_id, null, 'new_comment', 'SSL'));
			$deleteButton->setHref(itw_app_link(tep_get_all_get_params(array('action', 'cID')) . 'cID=' . $cInfo->blog_comment_id . '&action=deleteComment', null, null, 'SSL'));

			$infoBox->addButton($editButton)->addButton($deleteButton);
		} else{ // create category/product info
			$infoBox->setHeader('<b> NO COMMENT</b>');
		}
		break;
}
?>

<div class="pageHeading"><?php echo "Post Comments";?></div>
<br/>


<div style="width:75%;float:left;">
<?php echo $tableGrid->draw();?>
</div>
<div style="width:25%;float:left">
<?php echo $infoBox->draw();?>
</div>
<br style="clear:both;"/>