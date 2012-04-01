<?php
	$emailAddress = (isset($_POST['email_address']) ? $_POST['email_address'] : '');
	$passWord = (isset($_POST['password']) ? $_POST['password'] : '');
	if ($userAccount->processLogIn($emailAddress, $passWord) === true){

		/*if (isset($navigation->snapshot['get']) && sizeof($navigation->snapshot['get']) > 0) {
			if(is_array($navigation->snapshot['get'])){
				$paramsArr = $navigation->snapshot['get'];
				if(isset($navigation->snapshot['get']['app'])){
					$app =$navigation->snapshot['get']['app'];
					unset($navigation->snapshot['get']['app']);
				}else{
					$app = null;
				}

				if(isset($navigation->snapshot['get']['appPage'])){
					$appPage =$navigation->snapshot['get']['appPage'];
					unset($navigation->snapshot['get']['appPage']);
				}else{
					$appPage = null;
				}
				$paramVar = '';
				foreach($navigation->snapshot['get'] as $key => $param){
					if($key != 'rType' && $key != 'action'){
						$paramVar .= $key. '='. $param . '&';
					}
				}

			}else{
				$paramsArr = explode('&',$navigation->snapshot['get']);
				$paramVar = '';
				foreach($paramsArr as $param){
					$varArr = explode('=', $param);
					if($varArr[0] == 'app'){
						$app = $varArr[1];
					}elseif($varArr[0] == 'appPage'){
						$appPage = $varArr[1];
					}else{
						$paramVar .= $param . '&';
					}
				}
			}

			if(!empty($paramVar)){
				$params = substr($paramVar, 0, strlen($paramVar)-1);
			}else{
				$params = null;
			}
			//$origin_href = itw_app_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(Session::getSessionName())), $navigation->snapshot['mode']);
			$origin_href = itw_app_link($params, $app, $appPage, 'SSL');
			//$origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(Session::getSessionName())), $navigation->snapshot['mode']);
			$navigation->clear_snapshot();
			$redirectUrl = $origin_href;
		} else { */
			$redirectUrl = itw_app_link(null, 'account', 'default', 'SSL');
		//}
	}
	
	if (!isset($redirectUrl)){
		$redirectUrl = itw_app_link(null, 'account', 'login', 'SSL');
	}
	EventManager::attachActionResponse($redirectUrl, 'redirect');
?>