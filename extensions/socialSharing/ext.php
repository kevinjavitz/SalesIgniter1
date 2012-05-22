<?php

class Extension_socialSharing extends ExtensionBase {

	public function __construct(){
		parent::__construct('socialSharing');
	}
	
	public function init(){
		//this is the main extension
		if ($this->isEnabled() === false) return;
		EventManager::attachEvents(array(
			'ProductInfoAfterShowImages',
			'PageLayoutHeaderCustomMeta'
		), null, $this);
		
		//this is for facebook connect
		if(sysConfig::get('EXTENSION_FACEBOOK_CONNECT_ENABLED') == 'True'){
			EventManager::attachEvents(array(
				'LoginBeforeTabs',
				'ProcessLoginBeforeExecute',
				'ProcessLogoutExecute'
			), null, $this);
		}
	}

	public function PageLayoutHeaderCustomMeta(){
		if(isset($_GET['app']) && $_GET['app'] == 'product' && isset($_GET['appPage']) && $_GET['appPage'] == 'info'){
				return '<meta property="og:title" content="'.Session::get('url_title').'"/>
						<meta property="og:type" content="article"/>
						<meta property="og:url" content="'.Session::get('url').'"/>';
		}else{
			return '';
		}
	}
	
	public function ProcessLogoutExecute() {
		if(!class_exists('Facebook'))
			require 'fb-sdk/facebook.php';
		
			$facebook = new Facebook(array(
			'appId'  => sysConfig::get('EXTENSION_FACEBOOK_CONNECT_APPID'),
			'secret' => sysConfig::get('EXTENSION_FACEBOOK_CONNECT_SECRET'),
		));
		
		$user = $facebook->getUser();
		if( $user ) {
			tep_redirect( $facebook->getLogoutUrl() );
		}
	}
	
	public function ProcessLoginBeforeExecute(&$noValidate, $password, &$Qcustomer) {
		if(!class_exists('Facebook'))
			require 'fb-sdk/facebook.php';
		
		$facebook = new Facebook(array(
			'appId'  => sysConfig::get('EXTENSION_FACEBOOK_CONNECT_APPID'),
			'secret' => sysConfig::get('EXTENSION_FACEBOOK_CONNECT_SECRET'),
		));
		
		$user = $facebook->getUser();
		if( $user ) {
			$profile = $facebook->api('/me');
			if( $password == sha1($profile['id']) )
				$noValidate = true;
		}
	}

	public function LoginBeforeTabs() {
		if(!class_exists('Facebook'))
			require 'fb-sdk/facebook.php';
		
		$facebook = new Facebook(array(
			'appId'  => sysConfig::get('EXTENSION_FACEBOOK_CONNECT_APPID'),
			'secret' => sysConfig::get('EXTENSION_FACEBOOK_CONNECT_SECRET'),
		));
		
		// Get User ID
		$redirectUrl = false;
		$user = $facebook->getUser();
		if( $user ) {
			try {
				global $userAccount, $emailEvent;
				$profile = $facebook->api('/me');
				$dbUser = Doctrine_Query::create()->from('Customers')->where("customers_email_address='".$profile['email']."'")->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				
				if( $dbUser ) {
					$pL = $userAccount->processLogIn($profile['email'], sha1($profile['id']));
					if($pL === true) {
						$redirectUrl = itw_app_link(null, 'account', 'default', 'SSL');
					}
				} else {
					$process = true;
					$hasError = false;
					$userAccount = new rentalStoreUser();
					$userAccount->loadPlugins();
					$addressBook =& $userAccount->plugins['addressBook'];
					
					$newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ1234567890'), 0, 8);
					$name = explode(' ', $profile['name']);
					$accountValidation = array(
						'entry_firstname'      => $name[0],
						'entry_lastname'       => $name[count($name)-1],
						'email_address'        => $profile['email'],
						'password'             => $newPassword,
						'confirmation'         => $newPassword,
						'terms'                => 1
					);
					
					$hasError = $userAccount->validate($accountValidation);
					if ($hasError === false){
						$userAccount->setFirstName($accountValidation['entry_firstname']);
						$userAccount->setLastName($accountValidation['entry_lastname']);
						$userAccount->setEmailAddress($accountValidation['email_address']);
						$userAccount->setPassword($accountValidation['password']);
						$userAccount->setNewsLetter($accountValidation['newsletter']);
						$userAccount->setLanguageId(Session::get('languages_id'));
					
						$customerId = $userAccount->createNewAccount();
						$addressBook->insertAddress($accountValidation, true, true);
					
						$userAccount->processLogIn(
							$accountValidation['email_address'],
							$accountValidation['password']
						);
					
						$emailEvent = new emailEvent(null, $userAccount->getLanguageId());
						$emailEvent->setEvent('password_forgotten');
						
						$emailEvent->setVars(array(
							'newPassword' => $newPassword,
						));
						
						$emailEvent->sendEmail(array(
							'email' => $profile['email'],
							'name' => $profile['name']
						));
						
						$redirectUrl = itw_app_link(null, 'account', 'default', 'SSL');
					}
				}
			} catch (FacebookApiException $e) {
				
			}
		}
		if( $redirectUrl )
			tep_redirect( $redirectUrl );
		
		return '<fb:login-button scope="'.sysConfig::get('EXTENSION_FACEBOOK_CONNECT_PERMISSIONS').'"></fb:login-button>
				<div id="fb-login"></div>
				<script type="text/javascript">
				   window.fbAsyncInit = function() {
			        FB.init({
			          appId: \''.$facebook->getAppID().'\',
			          cookie: true,
			          xfbml: true,
			          oauth: true
			        });
			        FB.Event.subscribe(\'auth.login\', function(response) {
			          window.location.reload();
			        });
			        FB.Event.subscribe(\'auth.logout\', function(response) {
			          window.location.reload();
			        });
			      };
			      (function() {
			        var e = document.createElement(\'script\'); e.async = true;
			        e.src = document.location.protocol +
			          \'//connect.facebook.net/en_US/all.js\';
			        document.getElementById(\'fb-login\').appendChild(e);
			      }());
			    </script>';
		
		/*
		 * This is for showing a Facebook registration form that is very similar to our system's form
		 * and is handled by createFBAccount action
		 * 
		$data_fields = "[{'name':'name'},{'name':'email'},";
		$data_fields .= "{'name':'street_address', 'description':'Street Address', 'type':'text'},";
		$data_fields .= "{'name':'city', 'description':'City', 'type':'text'},";
		$data_fields .= "{'name':'postal_code', 'description':'Postal Code', 'type':'text'},";
		
		//countries
		$countryJson = '';
		$countriesArray = Doctrine_Query::create()->from('Countries')->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach( $countriesArray as $country )
			$countryJson .= "'{$country['countries_id']}':'".str_replace("'", "", htmlentities($country['countries_name']))."',";
		$countryJson = substr( $countryJson, 0, -1 );
			
		$data_fields .= "{'name':'country', 'description':'Country', 'type':'select', 'options':{{$countryJson}}},";
		$data_fields .= "{'name':'password'}]";
		return '<script type="text/javascript">
					window.fbAsyncInit = function() {
			          FB.init({
			            appId      : \''.sysConfig::get('EXTENSION_FACEBOOK_CONNECT_APPID').'\',
			            status     : true, 
			            cookie     : true,
			            xfbml      : true,
			            oauth      : true,
			          });
			        };
			        (function(d){
			           var js, id = \'facebook-jssdk\'; if (d.getElementById(id)) {return;}
			           js = d.createElement(\'script\'); js.id = id; js.async = true;
			           js.src = "//connect.facebook.net/en_US/all.js";
			           d.getElementsByTagName(\'head\')[0].appendChild(js);
			         }(document));
				</script>
				<div class="fb-registration" data-fields="'.$data_fields.'" data-redirect-uri="'.itw_app_link('action=createFBAccount', 'account', 'create', 'SSL').'">Sign in with Facebook</div>';*/
	}

	public function ProductInfoAfterShowImages($product, &$productsImage){
		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_TWITTER') == 'True'){
			$dataUrl = '';
			$dataCount = '';
			Session::set('url',itw_app_link('products_id='.$product->getID(),'product','info'));
			Session::set('url_title', $product->getName());
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHORT_URL') == 'True'){
				$url = 'data-url="'.$this->make_bitly_url(itw_app_link('products_id='.$product->getID(),'product','info')).'" ';
				$dataUrl = 'data-counturl="'.itw_app_link('products_id='.$product->getID(),'product','info').'" ';
			}else{
				$url = 'data-url="'.itw_app_link('products_id='.$product->getID(),'product','info').'" ';
			}
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
				if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
					$dataCount  = 'data-count="horizontal"';
				}else{
					$dataCount = 'data-count="vertical"';
				}
			}else{
				$dataCount  = 'data-count="none"';
			}
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SIZE') == 'Medium'){
				$dataSize = 'data-size="medium"';
			}else{
				$dataSize = 'data-size="large"';
			}

			$productsImage .='<div style="display:inline-block;width:90px;"><a href="https://twitter.com/share" '.$dataUrl.' '.$url.' class="twitter-share-button" data-lang="en" '.$dataCount.' '.$dataSize. '>Tweet</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></div>';

		}

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_FACEBOOK') == 'True'){
			$dataCount = '';
			$url = 'data-href="'.itw_app_link('products_id='.$product->getID(),'product','info').'" ';
			$productsImage .='<div style="display:inline-block;" id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, \'script\', \'facebook-jssdk\'));</script>';

			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
				if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
					$dataCount  = 'data-layout="button_count"';
				}else{
					$dataCount = 'data-layout="box_count"';
				}
			}else{
				$dataCount  = '';
			}
			/*if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SIZE') == 'Medium'){
				$dataSize = 'data-size="medium"';
			}else{
				$dataSize = 'data-size="large"';
			} */
			$productsImage .='<div style="display:inline-block;width:90px" class="fb-like" '.$url.' data-send="false" '.$dataCount.' data-width="450" data-show-faces="false"></div>';
		}

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_GPLUS') == 'True'){
			$dataCount = '';
			$url = 'data-href="'.itw_app_link('products_id='.$product->getID(),'product','info').'" ';

			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
				if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
					$dataCount  = '<g:plusone></g:plusone>';
				}else{
					$dataCount = '<g:plusone size="tall"></g:plusone>';
				}
			}else{
				$dataCount  = '<g:plusone annotation="inline"></g:plusone>';
			}

			$productsImage .= '<div style="display:inline-block;width:90px;position:relative;z-index:100;">'.$dataCount.'</div>';
			$productsImage .='<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
		</script>';

		}

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_DIGG') == 'True'){
			$productsImage .= '<script type="text/javascript">
(function() {
var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];
s.type = \'text/javascript\';
s.async = true;
s.src = \'http://widgets.digg.com/buttons.js\';
s1.parentNode.insertBefore(s, s1);
})();
		</script>';
			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
				if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
					$productsImage .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton DiggCompact" href="http://digg.com/submit?url='.urlencode(itw_app_link('products_id='.$product->getID(),'product','info')).'&amp;title='.urlencode($product->getName()).'"></a></div>';
				}else{
					$productsImage .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton DiggMedium" href="http://digg.com/submit?url='.urlencode(itw_app_link('products_id='.$product->getID(),'product','info')).'&amp;title='.urlencode($product->getName()).'"></a></div>';
				}
			}else{
				if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SIZE') == 'Medium'){
					$productsImage .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton" href="http://digg.com/submit?url='.urlencode(itw_app_link('products_id='.$product->getID(),'product','info')).'&amp;title='.urlencode($product->getName()).'"><img src="http://developers.diggstatic.com/sites/all/themes/about/img/follow_buttons/Follow-On-Digg-Mini.png" alt="" title=""  /></a></div>';
				}else{
					$productsImage .= '<div style="display:inline-block;width:90px;"><a class="DiggThisButton" href="http://digg.com/submit?url='.urlencode(itw_app_link('products_id='.$product->getID(),'product','info')).'&amp;title='.urlencode($product->getName()).'"><img src="http://developers.diggstatic.com/sites/all/themes/about/img/follow_buttons/Follow-On-Digg-Small.png" alt="" title=""  /></a></div>';
				}
			}
		}

		if(sysConfig::get('EXTENSION_SOCIAL_SHARING_SHOW_LINKEDIN') == 'True'){

			if(sysConfig::get('EXTENSION_SOCIAL_SHARING_TYPE') == 'Count'){
				if(sysConfig::get('EXTENSION_SOCIAL_SHARING_FORMAT') == 'Horizontal'){
					$productsImage .= '<div style="display:inline-block;width:90px;"><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'.itw_app_link('products_id='.$product->getID(),'product','info').'" data-counter="right"></script></div>';
				}else{
					$productsImage .= '<div style="display:inline-block;width:90px;"><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'.itw_app_link('products_id='.$product->getID(),'product','info').'" data-counter="top"></script></div>';
				}
			}else{
				$productsImage .= '<div style="display:inline-block;width:90px;"><script src="http://platform.linkedin.com/in.js" type="text/javascript"></script><script type="IN/Share" data-url="'.itw_app_link('products_id='.$product->getID(),'product','info').'"></script></div>';
			}
		}


	}

	public function make_bitly_url($url,$format = 'xml',$version = '2.0.1')
	{
		//create the URL
		$bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.sysConfig::get('EXTENSION_SOCIAL_SHARING_BITLY_USERNAME').'&apiKey='.sysConfig::get('EXTENSION_SOCIAL_SHARING_BITLY_API').'&format='.$format;

		//get the url
		//could also use cURL here
		$response = file_get_contents($bitly);

		//parse depending on desired format
		if(strtolower($format) == 'json')
		{
			$json = @json_decode($response,true);
			return $json['results'][$url]['shortUrl'];
		}
		else //xml
		{
			$xml = simplexml_load_string($response);
			return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
		}
	}
}
?>