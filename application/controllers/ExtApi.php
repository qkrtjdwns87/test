<?
defined('BASEPATH') or exit ('No direct script access allowed');

require_once $_SERVER["DOCUMENT_ROOT"].'/sdk/facebook-php-sdk-v4-5.0.0/autoload.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/sdk/twitteroauth-0.6.2/autoload.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/sdk/naver-0.0.6/Naver.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/sdk/kakao-2015.12/Kakao.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/sdk/google-api-0.6.0/Google_Client.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/sdk/google-api-0.6.0/contrib/Google_Oauth2Service.php';

use Abraham\TwitterOAuth\TwitterOAuth;
/**
 * ExtApi
 * external API
 * sns api or etc...
 *
 * @author : Administrator
 * @date    : 2015. 12.
 * @version:
 */
class ExtApi extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	/**
	 * @var 처리후 되돌아갈 url
	 */
	protected  $_returnUrl = '';
		
	protected $_uriMethod = '';
	
	/**
	 * @var facebook class object
	 */
	protected $_fb;
	
	protected $_tw;
	
	protected $_nv;
	
	protected $_ka;
	
	protected $_gg;
	
	protected $_encKey = '';
		
	public function __construct()
	{
		parent::__construct ();

		$this->load->helper(array('url', 'cookie'));
		$this->load->model(array('board_model', 'user_model'));
		
		$this->_encKey = $this->config->item('encryption_key');		
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();
		
		if (strpos($this->_uriMethod, 'facebook') !== FALSE)
		{
			$this->_fb = new Facebook\Facebook([
				'app_id' => $this->config->item('facebook_appid'),
				'app_secret' => $this->config->item('facebook_secret_code'),
				'default_graph_version' => 'v2.2',
			]);
		}
		else if (strpos($this->_uriMethod, 'twitter') !== FALSE)
		{
			$this->_tw = new TwitterOAuth(
				$this->config->item('twitter_consumer_key'),
				$this->config->item('twitter_secret')
			);
		}
		else if (strpos($this->_uriMethod, 'naver') !== FALSE)
		{
			$this->_nv = new Naver(
				array(
					"CLIENT_ID" => $this->config->item('naver_client_id'),
					"CLIENT_SECRET" => $this->config->item('naver_client_secret'),
					"RETURN_URL" => $this->common->getDomain().$this->config->item('naver_callback')	// (*필수)콜백 URL
				)
			);
		}
		else if (strpos($this->_uriMethod, 'kakao') !== FALSE)
		{
			$this->_ka = new Kakao_REST_API_Helper($this->config->item('kakao_native_app_key'));
			$this->_ka->set_admin_key($this->config->item('kakao_admin_key'));
		}
		else if (strpos($this->_uriMethod, 'google') !== FALSE)
		{
			$this->_gg = new Google_Client();
			$this->_gg->setApplicationName($this->config->item('google_application_name'));
			$this->_gg->setClientId($this->config->item('google_client_id'));
			$this->_gg->setClientSecret($this->config->item('google_client_secret'));
			$this->_gg->setRedirectUri($this->common->getDomain().'/extApi/google_callback');
			//$this->_gg->setDeveloperKey('insert_your_developer_key');
		}

		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'facebook_login':
				$this->facebookLogin();
				break;
			case 'facebook_callback':
				$this->facebookCallBack();
				break;				
			case 'facebook_callback_contents':
				$this->facebookCallBackContents();
				break;
			case 'facebook_logout':
				$this->facebookLogout();
				break;				
			case 'twitter_login':
				$this->twitterLogin();
				break;				
			case 'twitter_callback':
				$this->twitterCallBack();
				break;				
			case 'twitter_logout':
				$this->twitterLogout();
				break;
			case 'naver_login':
				$this->naverLogin();
				break;				
			case 'naver_callback':
				$this->naverCallBack();
				break;
			case 'naver_logout':
				$this->naverLogout();
				break;				
			case 'kakao_login':
				$this->kakaoLogin();
				break;
			case 'kakao_callback':
				$this->kakaoCallBack();
				break;
			case 'kakao_logout':
				$this->kakaoLogout();
				break;				
			case 'google_login':
				$this->googleLogin();
				break;
			case 'google_callback':
				$this->googleCallBack();
				break;
			case 'google_logout':
				$this->googleLogout();
				break;				
		}
	}
	
	private function setPrecedeValues()
	{
		//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
		$this->_uri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
		$this->_arrUri = $this->common->segmentExplode($this->_uri);
		$this->_uriMethod = (!empty($this->uri->segment(2))) ? $this->uri->segment(2) : $this->_uriMethod;
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->_uriMethod;
		
		if (in_array('return_url', $this->_arrUri))
		{
			$this->_returnUrl = $this->common->urlExplode($this->_arrUri, 'return_url');
			$this->_returnUrl = $this->common->nullCheck($this->_returnUrl, 'str', '');			
		}
		
		if ($this->_returnUrl == '')
		{
			$this->_returnUrl = $this->input->post_get('return_url', FALSE);			
		}
	
		if (in_array('response', $this->_arrUri))
		{
			$this->_currentPage = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'response')));
		}
		
		/*
		 * 파라메터 처럼 array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'pageMethod' => $this->_uriMethod,
			'isLogin' => $this->common->getIsLogin(),
			'sessionData' => $this->common->getSessionAll()
		);
	}
	 
	private function facebookLogin()
	{


		$this->	sns_session_clear();


		$helper = $this->_fb->getRedirectLoginHelper();
		$permissions = ['email']; // optional ['email', 'publish_stream']
		// $fbLoginUrl = $helper->getLoginUrl(
		// 	$this->common->getDomain().'/extApi/facebook_callback?return_url='.$this->_returnUrl, 
		// 	$permissions
		// );

		$fbLoginUrl = $helper->getLoginUrl(
			$this->common->getDomain().'/extApi/facebook_callback'. 
			$permissions
		);
		
		//$fbLogoutUrl = $this->_fb->getLogoutUrl(
		//		array('next' => $this->common->getDomain().'/')
		//);
		
		$fbLogoutUrl = $helper->getLogoutUrl(
			$this->common->getSession('facebook_access_token'),
			$this->common->getDomain().'/'	//next
		);		
	}
	
	/**
	 * @method name : facebookCallBack
	 * facebook 인증후 callback 처리 (유입:로그인 페이지)
	 * 
	 */
	private function facebookCallBack()
	{
		$isSuccess = TRUE;
		 
		$accessToken = '';
		$failMsg = '';
		$helper = $this->_fb->getRedirectLoginHelper();
		 
			
		try {
			$accessToken = $helper->getAccessToken();
			//print('AccessToken : ' . $accessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			//echo 'Graph returned an error: ' . $e->getMessage();
			//exit;
			$isSuccess = FALSE;
			$failMsg = $e->getMessage();
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			//echo 'Facebook SDK returned an error: ' . $e->getMessage();
			//exit;
			$isSuccess = FALSE;
			$failMsg = $e->getMessage();			
		}
		
		print($failMsg);
		 

		if (!isset($accessToken)) {
			if ($helper->getError()) {
				//header('HTTP/1.0 401 Unauthorized');
				//echo "Error: " . $helper->getError() . "\n";
				//echo "Error Code: " . $helper->getErrorCode() . "\n";
				//echo "Error Reason: " . $helper->getErrorReason() . "\n";
				//echo "Error Description: " . $helper->getErrorDescription() . "\n";
				$isSuccess = FALSE;
				$failMsg = 'HTTP/1.0 401 Unauthorized';				
			} else {
				//header('HTTP/1.0 400 Bad Request');
				//echo 'Bad request';
				$isSuccess = FALSE;
				$failMsg = 'HTTP/1.0 400 Bad Request';				
			}
		}		

	 
		// Logged in
		//echo '<h3>Access Token</h3>';
		//var_dump($accessToken->getValue());
		
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $this->_fb->getOAuth2Client();
		
		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		//echo '<h3>Metadata</h3>';
		//var_dump($tokenMetadata);
		
		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($this->config->item('facebook_appid'));
		// If you know the user ID this access token belongs to, you can validate it here
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();
		
		if (! $accessToken->isLongLived()) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				//echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
				//exit;
				$isSuccess = FALSE;
				$failMsg = 'Error getting long-lived access token';				
			}
		
			//echo '<h3>Long-lived</h3>';
			//var_dump($accessToken->getValue());
		}
		
		$accessToken = (string) $accessToken;
		
		//개인정보
		try {
			$response = $this->_fb->get('/me', $accessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			//echo 'Graph returned an error: ' . $e->getMessage();
			//exit;
			$isSuccess = FALSE;
			$failMsg = $e->getMessage();			
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			//echo 'Facebook SDK returned an error: ' . $e->getMessage();
			//exit;
			$isSuccess = FALSE;
			$failMsg = $e->getMessage();			
		}		
		
		$userinfo = $response->getGraphUser();
		 
		if ($isSuccess)
		{
			$this->common->setSession('facebook_access_token', $accessToken);
			//$_SESSION['fb_access_token'] = (string) $accessToken;
			
			if ($userinfo['gender'] == 'male')
			{
				$gender = 'M';
			}
			else if ($userinfo['gender'] == 'female')
			{
				$gender = 'F';
			}
			else 
			{
				$gender = 'N';
			}

			if(!$userinfo['birthday']){
				$userinfo['birthday']='';
			}
			
			$snsInfo = array(
				'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'FACEBOOK'),
				'SNS_ID' => $userinfo['id'],
				'SNS_NAME' => $userinfo['name'],
				'SNS_NICK' => $userinfo['name'],
				'SNS_EMAIL' => NULL,
				'USER_GENDER' => (!empty($gender)) ? $gender : 'N',
				'USER_BIRTH' => (!empty($userinfo['birthday'])) ? date_format($userinfo['birthday'], 'Ymd') : NULL,
				'SNSPROFILE_IMG' => 'https://graph.facebook.com/'.$userinfo['id'].'/picture'
			);
			
			$this->snsUserLoginConfirm($snsInfo);			
		}
		else 
		{
			$this->common->message('로그인이 되지 않았습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');
		}
	}
	
	/**
	 * @method name : facebookCallBackContents
	 * facebook 인증후 callback 처리 (유입 : 컨텐츠페이지)  
	 * 이전에 SNS회원으로 로그인했다면 여기로 와서는 안됨
	 * 
	 */
	private function facebookCallBackContents()
	{
		$helper = $this->_fb->getRedirectLoginHelper();
			
		try 
		{
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		
		$this->common->setSession('facebook_access_token', $accessToken);
		redirect($this->_returnUrl);
	}
	
	private function facebookLogout()
	{
		$helper = $this->_fb->getRedirectLoginHelper();
		$fbLogoutUrl = $helper->getLogoutUrl(
			$this->common->getSession('facebook_access_token'),
			$this->common->getDomain().'/'	//next
		);
		$this->common->setLogout($fbLogoutUrl, TRUE);		
	}
	
	private function twitterLogin()
	{
		//$this->	sns_session_clear();
		//App setting에서 oauth_callback 의
		//Enable Callback Locking 체크를 하면 안된다
		//App setting의 callback url을 쓰려면
		//array('oauth_callback' => OAUTH_CALLBACK)
		$request_token  = $this->_tw->oauth(
			'oauth/request_token',
			array('oauth_callback' => $this->common->getDomain().$this->config->item('twitter_oauth_callback').'?return_url='.$this->_returnUrl, )
		);
		
		$this->common->setSession('twitter_oauth_token', $request_token['oauth_token']);
		$this->common->setSession('twitter_oauth_token_secret', $request_token['oauth_token_secret']);
		
		$twLoginUrl = $this->_tw->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		$twLogoutUrl = '/';	//twitter는 logout api가 없음
	}
	
	private function twitterCallBack()
	{
		$isSuccess = TRUE;
		$failMsg = '';
		
		if ($this->common->nullCheck($this->input->post_get('denied', FALSE), 'str', '') != '')
		{
			//twitter app 동의화면에서 취소시 		
			//redirect('/');
			$this->common->message('로그인이 되지 않았습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');			
		}

		$request_token = [];
		$request_token['oauth_token'] = $this->common->getSession('twitter_oauth_token');
		$request_token['oauth_token_secret'] = $this->common->getSession('twitter_oauth_token_secret');
		
		if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
			// Abort! Something is wrong.
			$isSuccess = FALSE;
			$failMsg = 'oauth token error';			
		}

		$this->_tw = new TwitterOAuth(
			$this->config->item('twitter_consumer_key'),
			$this->config->item('twitter_secret'),
			$request_token['oauth_token'], 
			$request_token['oauth_token_secret']
		);
		
		$access_token = $this->_tw->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
		
		$this->_tw = new TwitterOAuth(
			$this->config->item('twitter_consumer_key'),
			$this->config->item('twitter_secret'),
			$access_token['oauth_token'],
			$access_token['oauth_token_secret']
		);
		
		//$obj = $this->_tw->get("account/verify_credentials");
		//$userinfo = $this->common->objectToArray($obj);
		$userinfo = $this->_tw->get("account/verify_credentials");
		
		//print_r($userinfo);
		//exit;
		//user정보안에 email 항목이 없음
		
		if ($isSuccess && $userinfo)
		{
			$this->common->setSession('twitter_access_token', $access_token);
				
			$snsInfo = array(
				'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'TWITTER'),
				'SNS_ID' => $userinfo->id,
				'SNS_NAME' => $userinfo->name,
				'SNS_NICK' => $userinfo->screen_name,
				'SNS_EMAIL' => NULL, //'circus_'.$userinfo->id.'@twitter.circus.co.kr',	//임시 이메일주소 부여
				'USER_GENDER' => 'N', //모름
				'USER_BIRTH' => NULL,				
				'SNSPROFILE_IMG' => $userinfo->profile_image_url
			);
				
			$this->snsUserLoginConfirm($snsInfo);
		}
		else
		{
			$this->common->message('로그인이 되지 않았습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');			
		}		
	}
	
	private function twitterLogout()
	{
		//api에서 logout을 지원하지 않음
		$this->common->setLogout('/', TRUE);		
	}
	
	private function naverLogin()
	{
		//$this->	sns_session_clear();
	}
	
	private function naverCallBack()
	{
		$return_code = $this->input->post_get('code', FALSE);
		$return_state = $this->input->post_get('state', FALSE);
		
		$result = $this->_nv->getUserProfile($return_code, $return_state);
		
		if ($result['result']['resultcode'] == '00')
		{
			//success
			$this->common->setSession('naver_access_token', $result['access_token']);
			$this->common->setSession('naver_access_token_type', $result['access_token_type']);
				
			$userinfo = $result['response'];
			//print_r($userinfo);
			//exit;
			
			$snsInfo = array(
				'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'NAVER'),
				'SNS_ID' => $userinfo['id'],
				'SNS_NAME' => $userinfo['nickname'],		
				'SNS_NICK' => $userinfo['nickname'],					
				'SNS_EMAIL' => NULL,
				'USER_GENDER' => (!empty($userinfo['gender'])) ? $userinfo['gender'] : 'N',
				'USER_BIRTH' => (!empty($userinfo['birthday'])) ? str_replace('-', '', $userinfo['birthday']) : NULL, //년도는 없음					
				'SNSPROFILE_IMG' => $userinfo['profile_image']
			);
			
			$this->snsUserLoginConfirm($snsInfo);			
		}
		else 
		{
			$this->common->message('로그인이 되지 않았습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');
		}
	}
	
	private function naverLogout()
	{
		$this->_nv->logout();
		$this->common->setLogout('/', TRUE);		
	}
	
	private function kakaoLogin()
	{
		//$this->	sns_session_clear();
	}
	
	private function kakaoCallBack()
	{
		$return_code = $this->input->post_get('code', FALSE);
		$return_state = $this->input->post_get('state', FALSE);
		//kakao는 return될 파라메터를 붙일수 있는 방법이 없어 보안파라메터인 state에 붙여서 넘겨줌
		$this->_returnUrl = $this->input->post_get('state', FALSE);
		
		if ($this->common->nullCheck($return_code, 'str', '') == '')
		{
			$this->common->message('로그인이 되지 않았습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');			
		}
		
		// authorization code로 access token 얻기
		$params = array();
		$params['grant_type']    = 'authorization_code';
		$params['client_id']     = $this->config->item('kakao_restapi_key');
		$params['redirect_uri']  = $this->common->getDomain().'/extApi/kakao_callback';
		$params['code']          = $return_code;
		
		$result = json_decode($this->_ka->create_or_refresh_access_token($params), TRUE);
		
		if (isset($result['error']))
		{
			/* refresh token으로 access token 얻기
			 $params = array();
			 $params['grant_type']    = 'refresh_token';
			 $params['client_id']     = $this->config->item('kakao_restapi_key');
			 $params['refresh_token'] = $result['refresh_token'];
			 $result = $this->_ka->create_or_refresh_access_token($params);
			 */
					
			$this->common->message('로그인이 되지 않았습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');
		}
		
		$this->_ka->set_access_token($result['access_token']);
		$this->_ka->set_admin_key($this->config->item('kakao_admin_key'));
		$this->common->setSession('kakao_access_token', $result['access_token']);
		
		$userinfo = json_decode($this->_ka->me(), TRUE);
		//print_r($userinfo);
		//exit;
		
		$snsInfo = array(
			'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'KAKAO'),
			'SNS_ID' => $userinfo['id'],
			'SNS_NAME' => $userinfo['properties']['nickname'],
			'SNS_NICK' => $userinfo['properties']['nickname'],
			'SNS_EMAIL' => NULL, //'circus_'.$userinfo['id'].'@kakao.circus.co.kr',	//임시 이메일주소 부여
			'USER_GENDER' => 'N', //모름
			'USER_BIRTH' => NULL,			
			'SNSPROFILE_IMG' => $userinfo['properties']['profile_image']
		);

		$this->snsUserLoginConfirm($snsInfo);		
	}
	
	private function kakaoLogout()
	{
		$this->_ka->logout();
		$this->common->setLogout('/', TRUE);		
	}
	
	private function googleLogin()
	{
	//$this->	sns_session_clear();
	}
	
	private function googleCallBack()
	{
		$return_code = $this->input->post_get('code', FALSE);
		$return_state = $this->input->post_get('state', FALSE);
		//google은 return될 파라메터를 붙일수 있는 방법이 없어 보안파라메터인 state에 붙여서 넘겨줌
		$this->_returnUrl = $this->input->post_get('state', FALSE);
		
		$oauth2 = new Google_Oauth2Service($this->_gg);
		$this->_gg->authenticate($return_code);
		$accessToken = $this->_gg->getAccessToken();
		$this->common->setSession('google_access_token', $accessToken);
		
		$this->_gg->setAccessToken($accessToken);
		$userinfo = $oauth2->userinfo->get();
		//print_r($userinfo);
		//exit;
		
		if ($userinfo['gender'] == 'male')
		{
			$gender = 'M';
		}
		else if ($userinfo['gender'] == 'female')
		{
			$gender = 'F';
		}
		else 
		{
			$gender = 'N';
		}
		
		$snsInfo = array(
			'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'GOOGLE'),
			'SNS_ID' => $userinfo['id'],
			'SNS_NAME' => $userinfo['name'],
			'SNS_NICK' => $userinfo['name'],
			'SNS_EMAIL' => NULL,
			'USER_GENDER' => $gender,
			'USER_BIRTH' => NULL,				
			'SNSPROFILE_IMG' => $userinfo['picture']
		);
		
		$this->snsUserLoginConfirm($snsInfo);		
	}
	
	private function googleLogout()
	{
		$accessToken = $this->common->getSession('google_access_token');
		$acc = json_decode($accessToken, TRUE);
		$this->_gg->revokeToken($acc['access_token']);
		$this->common->setLogout('/', TRUE);		
	}
	
	/**
	 * @method name : snsUserLoginConfirm
	 * SNS로그인은 하나만 유지
	 * 
	 * @param array $snsInfo
	 */
	private function snsUserLoginConfirm($snsInfo)
	{
		$userLevelCodeNum = 0;
		$this->_returnUrl = $this->common->nullCheck(base64_decode($this->_returnUrl), 'str', '/');

		$sns_method =$this->common->getSession('sns_method');
		
		//if (strpos($this->_returnUrl, 'jointype=sns') !== FALSE)
		if ($sns_method== "join")
		{
			//SNS 회원가입 처리
			$result = $this->user_model->getUserRowData($snsInfo, 'snsid');
			if ($result)
			{
				if ($result['ULEVELCODE_NUM'] == 790)
				{
					//이미 SNS회원으로 가입된 상태인 경우
					//$this->common->message('이미 회원으로 가입되어 있는 상태입니다.', '/', 'self');
					$this->common->app_script("alert('이미 회원으로 가입되어 있는 상태입니다.');app_closeWindow();");
					//$this->common->message("이미 회원으로 가입되어 있는 상태입니다.", "_android.loginok('".$this->_authkey."','N');", "js");
				}
				else 
				{
					//가입중 취소된 경우로 보고
					//$this->common->message('', '/app/user_a/writeform/uno/'.$result['NUM'].'?jointype=sns', 'self');
					$this->common->app_script("location.href='/app/user_a/writeform/uno/".$result['NUM']."?jointype=sns';");

				}
			}
			else 
			{
				$result = $this->setSNSJoinUserDataInsert($snsInfo);
				//$this->common->message('', '/app/user_a/writeform/uno/'.$result.'?jointype=sns', 'self');
				$this->common->app_script("location.href='/app/user_a/writeform/uno/".$result."?jointype=sns';");

			}
		}else{
			 

				if (empty($snsInfo['SNS_EMAIL']))
				{
					//이메일 주소가 없는 경우
					//id로 조회하여 가입보류상태여부 확인후 이메일등록 페이지로
					//가입이력이 없다면 SNS가입보류 상태로 가입한후 이메일등록 페이지로
					$result = $this->user_model->getUserRowData($snsInfo, 'snsid');
				 
					if ($result)
					{
						$userLevelCodeNum = $result['ULEVELCODE_NUM'];
				 		if ($userLevelCodeNum == 820) //SNS로그인 보류 상태로 있는경우
						{
							//$this->common->message('', '/app/user_a/snsemailregform/uno/'.$result['NUM'], 'self');
							$this->common->app_script("location.href='/app/user_a/snsemailregform/uno/".$result['NUM']."';");
						}
					} 
				}else {
					//$this->user_model->isSNSUserRegistered($userinfo)		//SNS로 회원가입된 이력확인
					$result = $this->user_model->getUserRowData($snsInfo, 'snsemail');
			 
				}
				
			 
				//SNS 회원가입 이력이 없다면
				if (!$result)
				{
					//USER 에 신규등록 insert
					//kakao, twitter의 경우 email정보를 받을수 없으므로 임시 이메일 부여

				 
					if (empty($snsInfo['SNS_EMAIL']))
					{
						//임시 이메일 부여
						$emailEnc = $this->common->sqlEncrypt($snsInfo['SNSCODE_NUM'].'_'.$snsInfo['SNS_ID'].'@circus.flag.co.kr', $this->_encKey);
						 
					}
					else 
					{
						$emailEnc = $this->common->sqlEncrypt($snsInfo['SNS_EMAIL'], $this->_encKey);	
						
						 
					}
					unset($snsInfo['SNS_EMAIL']);
					
					$uLevelCodeNum = ($userLevelCodeNum == 820 || $userLevelCodeNum == 0) ? $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'SNSHOLD') : $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'SNS');
					$snsInfo = $snsInfo + array(
						'USER_NAME' => $snsInfo['SNS_NAME'],
						'USER_NICK' => $snsInfo['SNS_NICK'],					
						'USER_EMAIL' => $emailEnc,
						'USER_PASS' => sha1($snsInfo['SNS_ID']),					
						'ULEVELCODE_NUM' => $uLevelCodeNum,
						'USTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'OK'),
						'SNS_EMAIL' => $emailEnc,
						'INFLOW_ROUTE' => ($this->common->getMobileCheck()) ? 'M' : 'W',
						'LEAVE_RESONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('LEAVE_REASON', 'NONE'),
						'REMOTEIP' => $this->input->ip_address()					
					);			
				 

					if (($userLevelCodeNum == 820 || $userLevelCodeNum == 0) && empty($snsInfo['SNS_EMAIL'])) //SNS로그인 보류 상태로 있는경우
					{
						//이메일등록페이지로
						$this->common->app_script("location.href='/app/user_a/snsemailregform/uno/".$insResultNum."';");
					}else{

						$insResultNum = $this->user_model->setSnsUserDataInsert($snsInfo);
						if ($insResultNum == 0) //신규 SNS 회원생성 오류
						{
							//$this->common->message('로그인에 실패했습니다.\\다시 시도해 보시기 바랍니다.', 'close', '');
							$this->common->app_script("alert('로그인에 실패했습니다.\\다시 시도해 보시기 바랍니다.');");
						}
						else
						{
							$qdata=array( "NUM"=>$insResultNum);
							$result = $this->user_model->getUserRowData($qdata);
							$this->common->app_script("location.href='/app/user_a/snsemailregform/uno/".$insResultNum."';");

							//if($result['NUM']>0){
								//$deviceId =$this->common->getSession('sns_deviceId');
								//$pushId =$this->common->getSession('sns_pushId');
								//$this->user_model->setAppInfoUpdate($result['NUM'], $deviceId, $pushId);
							//}
						}
					}
				}
				

					 
				
			
				if($result['NUM']>0){
					$deviceId =$this->common->getSession('sns_deviceId');
					$pushId =$this->common->getSession('sns_pushId');
					if (!empty($deviceId) && !empty($pushId)){
						$this->user_model->setAppInfoUpdate($result['NUM'], $deviceId, $pushId);
					} 
					 
					$userNumEnc = $this->common->sqlEncrypt($result['NUM'].'_'.$result['ULEVELCODE_NUM'], $this->_encKey);
				
				
					
					$this->user_model->setUserLastLoginUpdate($result['NUM']);
					//SHOP 생성 작가인 경우 SHOP고유번호 가져오기
					$shopInfo = $this->user_model->getShopInfoByUserNum($result['NUM']);		

					//세션데이타 생성		
					$this->common->setSession('session_date', date('Y-m-d H:i:s'));
					$this->common->setSession('user_num', $result['NUM']);
					$this->common->setSession('user_id', $result['USER_ID']);
					$this->common->setSession('user_name', $result['USER_NAME']);
					$this->common->setSession('user_nick', $result['USER_NICK']);
					$this->common->setSession('user_email', $result['USER_EMAIL']);
					$this->common->setSession('sns_type', $result['SNSCODE_NUM']);
					$this->common->setSession('sns_id', $result['SNS_ID']);
					$this->common->setSession('sns_name', $result['SNS_NAME']);
					$this->common->setSession('profileimg', $result['SNSPROFILE_IMG']);
					$this->common->setSession('user_level', $result['ULEVELCODE_NUM']);
					$this->common->setSession('shop_num', (!$shopInfo) ? 0 : $shopInfo['NUM']);

						
						
					$cookieExpire = 0;	//(60*60*24)*30; //30일 유지
					set_cookie('usernum', $result['NUM'], $cookieExpire);
					set_cookie('profileimg', $result['SNSPROFILE_IMG'], $cookieExpire);
					set_cookie('authkey', $userNumEnc, $cookieExpire);
					set_cookie('deviceid', $deviceId, $cookieExpire);
					set_cookie('pushid', $pushId, $cookieExpire);
				
						
					$this->common->app_script("app_loginok('', '".$userNumEnc."', 'N');");
				}else{
					$this->common->app_script("location.href='/app/user_a/login';");

				}
		}
	}
	
	/**
	 * @method name : setSNSJoinUserDataInsert
	 * SNS회원가입 진행중 SNS정보를 회원정보에 insert
	 * (ULEVELCODE_NUM = 830, SNSUSERHOLD 로 저장)
	 * 
	 * @param unknown $snsInfo
	 * @return Ambiguous
	 */
	private function setSNSJoinUserDataInsert($snsInfo)
	{
		$emailEnc = (!empty($snsInfo['SNS_EMAIL'])) ? $this->common->sqlEncrypt($snsInfo['SNS_EMAIL'], $this->_encKey) : '';
		$snsInfo = $snsInfo + array(
			'USER_NAME' => $snsInfo['SNS_NAME'],
			'USER_NICK' => $snsInfo['SNS_NICK'],
			'USER_EMAIL' => $emailEnc,
			'USER_PASS' => sha1($snsInfo['SNS_ID']),
			'ULEVELCODE_NUM' => 830,
			'USTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'OK'),
			'SNS_EMAIL' => $emailEnc,
			'INFLOW_ROUTE' => ($this->common->getMobileCheck()) ? 'M' : 'W',
			'LEAVE_RESONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('LEAVE_REASON', 'NONE'),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$insResultNum = $this->user_model->setSnsUserDataInsert($snsInfo);
		
		return $insResultNum;
	}
}