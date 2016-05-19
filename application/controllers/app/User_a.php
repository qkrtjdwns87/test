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
 * User_a
 * 
 *
 * @author : Administrator
 * @date    : 2016. 2
 * @version:
 */
class User_a extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_searchKey = '';
	
	protected $_searchWord = '';
	
	protected $_uriMethod = '';
	
	/**
	 * @var 회원 고유번호
	 */
	protected $_uNum = 0;
		
	/**
	 * @var 처리후 되돌아갈 url
	 */
	protected  $_returnUrl = '';
	
	/**
	 * @var array	class간(주로 view) 넘겨주는 data set
	 */
	protected $_sendData = array();
	
	/**
	 * @var array	data set
	 */
	protected $_data = array();
	
	protected $_tbl = 'BOARD';
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = TRUE;
	
	/**
	 * @var CodeIngniter pagination 사용여부
	 */
	protected $_isCIpagingUse = FALSE;
	
	/**
	 * @var 파일첨부갯수
	 */
	protected $_fileCnt = 2;	
	
	protected $_fb;
	
	protected $_tw;
	
	protected $_nv;	
	
	protected $_ka;	
	
	protected $_gg;	
	
	protected $_encKey = '';
	
	/**
	 * @var integer USER LEVEL
	 */
	protected $_uLevelType = 0;	
	
	/**
	 * @var string 앱으로 부터 전달받는 고유 deviceid
	 */
	protected $_deviceId = '';	
	
	/**
	 * @var string 앱으로 부터 전달받는 pushid
	 */
	protected $_pushId = '';
	

	protected $_authkey = '';
	
	public function __construct() 
	{
		parent::__construct ();
		
		/*
		 if ($this->input->post('remember_me'))
		 {
		 // set sess_expire_on_close to 0 or FALSE when remember me is checked.
		 $this->config->set_item('sess_expire_on_close', '0'); // do change session config
		 }
		 $this->session->sess_expire_on_close = TRUE; 이런식으로 설정해주면
		세션마다 따로 적용됌		 
		 */		
	
		$this->load->library(array('session'));
		$this->load->helper(array('url', 'cookie'));
		$this->load->model(array('user_model', 'main_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();	

		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'login': //로그인 폼
				$this->login();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/user/login', $data);				
				break;
			case 'shoplogin': //로그인 폼
				$this->shoplogin();
				//$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/user/shoplogin', $data);				
				break;
			case 'writeform': //회원가입
				$this->getBeforeRegistUserInfo();
				$this->load->view('app/user/user_write', $this->_sendData);
				break;
			case 'write':
				$this->setUserDataInsert();
				break;	
			case 'snsemailregform':
				$this->load->view('app/user/sns_email_regist', $this->_sendData);
				break;
			case 'snsemailregist':
				$this->setSnsUserEmailUpdate();
				break;
			case 'join': //회원가입선택 폼
				$this->login();
				$this->load->view('app/user/user_join', array_merge($this->_data, $this->_sendData));
				break;	
			case 'joinend': //완료 페이지
				$this->load->view('app/user/user_join_end', $this->_sendData);
				break;				
			case 'joinchildend': //14세미만 완료 페이지
				$this->load->view('app/user/user_joinchild_end', $this->_sendData);
				break;
			case 'dormantclearform'; //휴면계정 form
				$this->load->view('app/user/user_dormant_clear', $this->_sendData);
				break;		
			case 'pwreissueform': //비밀번호 재발급 form
				$this->load->view('app/user/user_passwd_reissue', $this->_sendData);
				break;				
			case 'pwreissue': //비밀번호 재발급
				$this->setUserPasswordReissue();
				break;				
			default:
				$this->{"{$this->_uriMethod}"}();
				break;				
		}		
	}
	
	/**
	 * @method name : setPrecedeValues
	 * uri 처리관련
	 * post, get 내용 처리 (선행처리가 필요한 것만 - 그외의 것은 메소드 안에서 처리)
	 *
	 */
	private function setPrecedeValues()
	{
		$this->_mode = $this->input->post_get('mode', FALSE);
		
		//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
		$this->_uri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
		$this->_arrUri = $this->common->segmentExplode($this->_uri);
		$this->_uriMethod = (!empty($this->uri->segment(3))) ? $this->uri->segment(3) : $this->_uriMethod;
		
		if (in_array('uno', $this->_arrUri))
		{
			$this->_uNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'uno')));
		}
		$this->_uNum = $this->common->nullCheck($this->_uNum, 'int', 0);
		
		if (in_array('return_url', $this->_arrUri))
		{
			$this->_returnUrl = $this->common->urlExplode($this->_arrUri, 'return_url');
		}
		$this->_returnUrl = $this->common->nullCheck($this->_returnUrl, 'str', '');		
		
		if (empty($this->_returnUrl))
		{
			$this->_returnUrl = $this->input->post_get('return_url', FALSE);
		}
		
		$joinType = $this->input->post_get('jointype', TRUE);
		$this->_deviceId = $this->input->post_get('deviceid', TRUE);
		$this->_pushId = $this->input->post_get('pushid', TRUE);
		
		$this->_uLevelType = $this->common->getSessionUserLevelCodeId();		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=user'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_uNum > 0) ? '/uno/'.$this->_uNum : '';		
		
		/*
		 * 파라메터 처럼 array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentParam' => $this->_currentParam,				
			'returnUrl' => $this->_returnUrl,				
			'searchKey' => $this->_searchKey,
			'searchWord' => $this->_searchWord,
			'pageMethod' => $this->_uriMethod,
			'uNum' => $this->_uNum,
			'userEmail' => '',
			'userName' => '',
			'userBirth' => '',
			'userGender' =>	'',
			'userMobile' =>	'',
			'joinType' => $joinType,				
			'tbl' => $this->_tbl,
			'fileCnt' => $this->_fileCnt,
			'isCIpagingUse' => $this->_isCIpagingUse,
			'isLogin' => ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0) ? FALSE : TRUE, //$this->common->getIsLogin(),
			'sessionData' => $this->common->getSessionAll(),
			'deviceId' => $this->_deviceId,
			'pushId' => $this->_pushId
		);	

			

			//print_r($this->_sendData);
			//log_message('debug', '_sendData : ' .print_r($this->_sendData));
	}
	
	private function loginCheck($url = '/app/user_a/login')
	{
		//if (!$this->common->getIsLogin())
		if ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0)
		{
			//$this->common->message('로그인후 이용하실 수 있습니다.', '/app/user_a/login', 'top');
			$this->common->message('로그인후 이용하실 수 있습니다.', "app_showMenuWindow('로그인', '".$url."');", 'js');
		}
	}

	/**
	 * @method name : login
	 * 로그인 페이지로 이동 (view load) 
	 * SNS로그인에 필요한 내용들 모두 view 페이지로
	 * 
	 */
	public function login()
	{
		/*
			sns Login을 위한 세션 저장 
			*/
		 

		$this->common->setSession('sns_method',   $this->_uriMethod);
		$this->common->setSession('sns_deviceId', $this->_deviceId);
		$this->common->setSession('sns_pushId',   $this->_pushId);	
		
		log_message('debug', 'HTTP_USER_AGENT : ' .$_SERVER["HTTP_USER_AGENT"]); 
		 
		//Facebook
		$this->_fb = new Facebook\Facebook([
			'app_id' => $this->config->item('facebook_appid'),
			'app_secret' => $this->config->item('facebook_secret_code'),
			'default_graph_version' => 'v2.2',
		]);
		
		$helper = $this->_fb->getRedirectLoginHelper();
		//$permissions = ['email', 'user_birthday']; // optional ['email', 'publish_stream']
		$permissions = ['email']; // optional ['email', 'publish_stream']
		// $fbLoginUrl = $helper->getLoginUrl(
		// 	$this->common->getDomain().'/extApi/facebook_callback?return_url='.$this->_returnUrl, 
		// 	$permissions
		// );

		$fbLoginUrl = $helper->getLoginUrl(
			$this->common->getDomain().'/extApi/facebook_callback', 
			$permissions
		);

		//$fbLogoutUrl = $helper->getLogoutUrl(
		//		$this->common->getSession('facebook_access_token'),
		//		$this->common->getDomain().'/'	//next
		//);
		$fbLogoutUrl = $this->common->getDomain().'/extApi/facebook_logout';
		$fbIsLogin = ($this->common->nullCheck($this->common->getSession('facebook_access_token'), 'str', '') == '') ? FALSE : TRUE;		

		//twitter
		//App setting에서 oauth_callback 의
		//Enable Callback Locking 체크를 하면 안된다
		//App setting의 callback url을 쓰려면
		//array('oauth_callback' => OAUTH_CALLBACK)
		$this->_tw = new TwitterOAuth(
			$this->config->item('twitter_consumer_key'),
			$this->config->item('twitter_secret')
		);
		
		$request_token  = $this->_tw->oauth(
			'oauth/request_token', 
			array('oauth_callback' => $this->common->getDomain().$this->config->item('twitter_oauth_callback').'?return_url='.$this->_returnUrl, )
		);
		
		$this->common->setSession('twitter_oauth_token', $request_token['oauth_token']);
		$this->common->setSession('twitter_oauth_token_secret', $request_token['oauth_token_secret']);
		
		$twLoginUrl = $this->_tw->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		$twLogoutUrl = $this->common->getDomain().'/extApi/twitter_logout';	//twitter는 logout api가 없음
		$twIsLogin = ($this->common->nullCheck($this->common->getSession('twitter_access_token'), 'str', '') == '') ? FALSE : TRUE; 
		
		//naver
		$this->_nv = new Naver(
			array(
				"CLIENT_ID" => $this->config->item('naver_client_id'),
				"CLIENT_SECRET" => $this->config->item('naver_client_secret'),
				"RETURN_URL" => $this->common->getDomain().$this->config->item('naver_callback')	// (*필수)콜백 URL
			)
		);
		
		$nvLoginUrl = $this->_nv->loginString($this->_returnUrl);
		$nvLogoutUrl = $this->_nv->logoutString($this->common->getDomain().'/extApi/naver_logout');
		$nvIsLogin = ($this->common->nullCheck($this->common->getSession('naver_access_token'), 'str', '') == '') ? FALSE : TRUE;
		
		//Kakao
		//$this->_ka = new Kakao_REST_API_Helper($this->config->item('kakao_native_app_key'));
		//$this->_ka->set_admin_key($this->config->item('kakao_admin_key'));
		$app_key = $this->config->item('kakao_restapi_key');
		$redirect_uri = $this->common->getDomain().'/extApi/kakao_callback';
		
		//kakao는 return될 파라메터를 붙일수 있는 방법이 없어 보안파라메터인 state에 붙여서 넘겨줌
		$kaLoginUrl = 'https://kauth.kakao.com/oauth/authorize?client_id='.$app_key.'&redirect_uri='.$redirect_uri.'&response_type=code&state='.$this->_returnUrl;///extApi/kakao_login'; //$this->_nv->loginString($this->_returnUrl);
		$kaLogoutUrl = $this->common->getDomain().'/extApi/kakao_logout';
		$kaIsLogin = ($this->common->nullCheck($this->common->getSession('kakao_access_token'), 'str', '') == '') ? FALSE : TRUE;		
		
		//Google
		$this->_gg = new Google_Client();
		$this->_gg->setApplicationName($this->config->item('google_application_name'));
		$this->_gg->setClientId($this->config->item('google_client_id'));
		$this->_gg->setClientSecret($this->config->item('google_client_secret'));
		$this->_gg->setRedirectUri($this->common->getDomain().'/extApi/google_callback');
		//$this->_gg->setDeveloperKey('insert_your_developer_key');
		$oauth2 = new Google_Oauth2Service($this->_gg);
		
		$ggLoginUrl = $this->_gg->createAuthUrl().'&state='.$this->_returnUrl;
		$ggLogoutUrl = $this->common->getDomain().'/extApi/google_logout';
		$ggIsLogin = ($this->common->nullCheck($this->common->getSession('google_access_token'), 'str', '') == '') ? FALSE : TRUE;
		
		//view로 넘겨줄 data
		$this->_data = array(
			'fbLoginUrl' => $fbLoginUrl,
			'fbLogoutUrl' => $fbLogoutUrl,
			'fbIsLogin' => $fbIsLogin,
			'twLoginUrl' => $twLoginUrl,
			'twLogoutUrl' => $twLogoutUrl,
			'twIsLogin' => $twIsLogin,
			'nvLoginUrl' => $nvLoginUrl,
			'nvLogoutUrl' => $nvLogoutUrl,
			'nvIsLogin' => $nvIsLogin,
			'kaLoginUrl' => $kaLoginUrl,
			'kaLogoutUrl' => $kaLogoutUrl,
			'kaIsLogin' => $kaIsLogin,
			'ggLoginUrl' => $ggLoginUrl,				
			'ggLogoutUrl' => $ggLogoutUrl,
			'ggIsLogin' => $ggIsLogin				
		);
	}

	/**
	 * @method name : login
	 * 로그인 페이지로 이동 (view load) 
	 * SNS로그인에 필요한 내용들 모두 view 페이지로
	 * 
	 */
	public function shoplogin()
	{
		/*
			sns Login을 위한 세션 저장 
			*/
		 

		$this->common->setSession('sns_method',   $this->_uriMethod);
		$this->common->setSession('sns_deviceId', $this->_deviceId);
		$this->common->setSession('sns_pushId',   $this->_pushId);	
		
		 
		 
		  
		//view로 넘겨줄 data
		$this->_data = array();
	}
	
	public function logout()
	{
		$this->common->setLogout('/', TRUE);
	}	
	
	/**
	 * @method name : loginConfirm
	 * 로그인 정보 입력후 확인(web only)
	 * 
	 */
	public function loginConfirm()
	{
		$userEmail = $this->input->post_get('useremail', TRUE);
		$userPass = $this->input->post_get('userpw', TRUE);
		$autoLogin = $this->input->post_get('autologin', TRUE); //자동로그인 체크여부
		$autoLogin = ($autoLogin == 'Y') ? 'Y' : 'N';
		
		$this->_returnUrl = (!empty($this->_returnUrl)) ? base64_decode($this->_returnUrl) : '/';
		
		if (!empty($userEmail) && !empty($userPass))
		{
			$userPass = sha1($userPass);
			$userInfo = $this->user_model->getUserRowData(array('USER_EMAIL' => $userEmail), 'email');	//이메일주소로 db에서 정보 가져오기
			
			if (!$userInfo)
			{
				//$this->common->message('회원가입내용을 찾을 수 없습니다.\\n다시 확인해 주시기 바랍니다.', 'parent.loginBtnReset();', 'js');				
				$this->common->message('', "parent.joinNotice('".$this->_returnUrl."');", 'js');
			}
			else
			{
				if ($userInfo['DEL_YN'] == 'Y')
				{
					//$this->common->message('회원정보를 찾을 수 없습니다.\\n다시 확인해 주시기 바랍니다.', 'parent.loginBtnReset();', 'js');
					$msg = "회원정보를 찾을 수 없습니다.<br />다시 확인해 주시기 바랍니다.";
					$this->common->message('', "parent.msgNotice('".$msg."', '".$this->_returnUrl."');", 'js');
				}
				
				if ($userInfo['USER_PASS'] != $userPass)
				{
					//임시비번까지 확인
					if (!$this->user_model->getPasswordReissueNumber($userInfo['NUM'], $userPass))
					{
						//$this->common->message('비밀번호가 일치하지 않습니다.\\n다시 확인해 주시기 바랍니다.', 'parent.loginBtnReset();', 'js');
						$msg = "비밀번호가 일치하지 않습니다.<br />다시 확인해 주시기 바랍니다.";
						$this->common->message('', "parent.msgNotice('".$msg."', '".$this->_returnUrl."');", 'js');
					}
				}
				
				$stdUserCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'SNSUSER'); //유효 회원판별 기준 레벨 코드번호
				$dorUserCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'DORMANT'); //휴면회원 레벨 코드번호

				if ($userInfo['ULEVELCODE_NUM'] > $stdUserCodeNum)
				{
					if ($userInfo['USTATECODE_NUM'] == $dorUserCodeNum) //휴면계정확인
					{
						$this->common->message('', 'parent.dormantNotice();', 'js');
					}
					
					//$this->common->message('회원정보를 찾을 수 없습니다.\\n다시 확인해 주시기 바랍니다.', 'parent.loginBtnReset();', 'js');
					$this->common->message('', "parent.joinNotice('".$this->_returnUrl."');", 'js');
				}
			}
			
			//프로필 이미지
			$defaultImg = '/images/app/main/photo.jpg';
			if (!empty($userInfo['PROFILE_FILE_INFO']))
			{
				$img = '';
				$arrFile = explode('|', $userInfo['PROFILE_FILE_INFO']);
				if (!empty($arrFile[0]))
				{
					if ($arrFile[4] == 'Y')	//썸네일생성 여부
					{
						$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
					}
					else
					{
						$img = $arrFile[2].$arrFile[3];
					}
				}
				$defaultImg = (!empty($img)) ? $img : $defaultImg;
			}
			
			//최근 로그인 일자 update
			$this->user_model->setUserLastLoginUpdate($userInfo['NUM']);			
			//SHOP 생성 작가인 경우 SHOP고유번호 가져오기
			$shopInfo = $this->user_model->getShopInfoByUserNum($userInfo['NUM']);
		
			$this->common->setSession('session_date', date('Y-m-d H:i:s'));
			$this->common->setSession('user_num', $userInfo['NUM']);
			$this->common->setSession('user_id', $userInfo['USER_ID']);			
			$this->common->setSession('user_name', $userInfo['USER_NAME']);
			$this->common->setSession('user_nick', $userInfo['USER_NICK']);
			$this->common->setSession('user_email', $userInfo['USER_EMAIL']); //로그인시 이메일은 암호화된 내용으로 세팅			
			$this->common->setSession('user_level', $userInfo['ULEVELCODE_NUM']);
			$this->common->setSession('user_state', $userInfo['USTATECODE_NUM']);
			$this->common->setSession('profileimg', $defaultImg);			
			$this->common->setSession('shop_num', (!$shopInfo) ? 0 : $shopInfo['NUM']);

			if (!empty($this->_deviceId) && !empty($this->_pushId))
			{
				//앱정보 update
				$this->user_model->setAppInfoUpdate($userInfo['NUM'], $this->_deviceId, $this->_pushId);
			}
				
			//비밀번호 변경주기 체크
			if ($this->common->getSession('passChangeAfter') != 'Y') //30일 이후 체크하지 않은 경우
			{
				$passCycle = 0;
				$result = $this->main_model->getPassChangeMainRowData(0);
				if ($result)
				{
					$passCycle = $result['recordSet']['ORDER']; //개월
					if ($passCycle > 0)
					{
						$lastPwDate = $userInfo['LASTPWCHANGE_DATE'];
						$dt = date('Ymd', strtotime($lastPwDate.'+'.$passCycle.' month'));
						if ($dt < date('Ymd'))
						{
							$this->common->message('', "parent.passChangeNotice('.$passCycle.', '".$this->_returnUrl."');", 'js');
						}
					}
				}
			}
			
			//redirect($this->_returnUrl);
			//$this->common->message('', $this->_returnUrl, 'parent');
			$userNumEnc = $this->common->sqlEncrypt($userInfo['NUM'].'_'.$userInfo['ULEVELCODE_NUM'], $this->_encKey);
			//echo '<br/>enc='.$userNumEnc;
			//echo '<br/>deviceid='.$this->_deviceId;
			//echo '<br/>_pushId='.$this->_pushId;
			//exit;
			
			log_message('debug', '[cicus] - loginComfirm :: usernum : ' . $userInfo['NUM']);

			// 자동 로그인이 셋팅 되어 있을 때만 쿠키를 생성한다.
			$cookieExpire = 0;
			if ($autoLogin == 'Y') {
				$cookieExpire = (60*60*24)*30;	//(60*60*24)*30; //30일 유지
			} else {
				$cookieExpire = 0;	//(60*60*24)*30; //30일 유지
			}
			
			//웹뷰 페이지용 쿠키생성
			set_cookie('usernum', $userInfo['NUM'], $cookieExpire);
			set_cookie('profileimg', $defaultImg, $cookieExpire);
			set_cookie('authkey', $userNumEnc, $cookieExpire);
			set_cookie('deviceid', $this->_deviceId, $cookieExpire);
			set_cookie('pushid', $this->_pushId, $cookieExpire);
			
			// 아이폰과의 통신을 위한 코드
			// iframe 에 데이터를 넘긴다.
			echo 
			"<script>
		        var IOSframe = document.createElement('iframe');
		        IOSframe.style.display = 'none';
		        IOSframe.src = 'jscall://loginok/".$userNumEnc."/".$autoLogin."';
		        document.documentElement.appendChild(IOSframe);
			</script>";
			
			$this->common->message('', "parent.app_loginok('".$this->_returnUrl."', '".$userNumEnc."' ,'".$autoLogin."');", 'js');

		}
		else
		{
			$this->common->message('잘못된 접근입니다.', '', 'parent');			
		}
	}
	
	/**
	 * @method name : 신규 회원가입
	 * 
	 * 
	 */
	private function setUserDataInsert()
	{
		$joinType = $this->input->post_get('jointype', TRUE);
		$joinEmail = $this->input->post_get('joinemail', TRUE);
		$userEmail = $this->input->post_get('useremail', TRUE);
		$emailEnc = $this->common->sqlEncrypt($userEmail, $this->_encKey);
		$userPass = sha1($this->input->post_get('passwd1', TRUE));
		$userMobile = $this->input->post_get('usermobile', TRUE);
		
		if($userEmail==""){
		$this->common->app_script("alert('이메일 입력되지 않았습니다.');");
		}
		if ($joinEmail != $userEmail) //가입보류된 이메일주소 비교
		{
			//동일한 이메일 주소여부 확인
			$result = $this->user_model->getUserRowData(array('USER_EMAIL' => $userEmail), 'email');
			if ($result)
			{
				$this->common->message('', 'parent.emailDuplicate();', 'js');
			}
		}
		
		if (strlen($userMobile) == 10)
		{
			$userMobile = substr($userMobile, 0, 3).'-'.substr($userMobile, 3, 3).'-'.substr($userMobile, 6, 4);
		}
		else
		{
			$userMobile = substr($userMobile, 0, 3).'-'.substr($userMobile, 3, 4).'-'.substr($userMobile, 7, 4);			
		}
		$userMobileEnc = $this->common->sqlEncrypt($userMobile, $this->_encKey);
		$userBirth = $this->input->post_get('userbirth', TRUE);
		//만14세 확인
		$birthday1 = date('Ymd', strtotime( $userBirth ));
		$nowday1 =  date('Ymd'); //현재날짜
		$age1      = floor(($nowday1 - $birthday1) / 10000);
		
		//한국나이
		/*
		$birthyear = date('Y', strtotime( $userBirth ));
		$nowyear = date('Y'); //현재년도
		$age2 = $nowyear - $birthyear + 1;
		*/
		$uStateCodeNum = ($age1 < 14) ? $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'HOLD') : $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'OK');
		$userBirth = substr($userBirth, 0, 4).'-'.substr($userBirth, 4, 2).'-'.substr($userBirth, 6, 2);
		$marketYn = $this->input->post_get('privacy3', TRUE);
		$uLevelCodeNum = ($joinType == 'sns') ? $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'SNSUSER') : $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'USER');
		$userName = $this->input->post_get('username', TRUE);
		$insData = array(
			'USER_NAME' => $userName,
			'USER_EMAIL' => $emailEnc,
			'USER_PASS' => $userPass,
			'ULEVELCODE_NUM' => $uLevelCodeNum,
			'USTATECODE_NUM' => $uStateCodeNum,				
			'USER_GENDER' => $this->input->post_get('usergender', TRUE),
			'USER_MOBILE' => $userMobileEnc,
			'USER_BIRTH' => $userBirth,				
			'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'NONE'),
			'INFLOW_ROUTE' => ($this->common->getMobileCheck()) ? 'M' : 'W',
			'LEAVE_RESONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('LEAVE_REASON', 'NONE'),
			'MARKET_YN' => ($marketYn == 'Y') ? 'Y' : 'N',
			'REMOTEIP' => $this->input->ip_address()
		);
		
		if ($joinType == 'sns')
		{
			unset($insData['SNSCODE_NUM']);
			unset($insData['INFLOW_ROUTE']);
			unset($insData['LEAVE_RESONCODE_NUM']);
			unset($insData['REMOTEIP']);
			$result = $this->user_model->setUserDataUpdate($this->_uNum, $insData);
		}
		else 
		{
			$result = $this->user_model->setUserDataInsert($insData);
			$this->_uNum = $result;
		}
		
		//로그인 
		$defaultImg = '/images/app/main/photo.jpg';
		$this->common->setSession('session_date', date('Y-m-d H:i:s'));
		$this->common->setSession('user_num', $this->_uNum);
		//$this->common->setSession('user_id', '');
		$this->common->setSession('user_name', $userName);
		//$this->common->setSession('user_nick', '');
		$this->common->setSession('user_email', $emailEnc); //로그인시 이메일은 암호화된 내용으로 세팅
		$this->common->setSession('user_level', $uLevelCodeNum);
		$this->common->setSession('user_state', $uStateCodeNum);
		$this->common->setSession('profileimg', $defaultImg);
		$this->common->setSession('shop_num', 0);
		
		$redirUrl = ($age1 < 14) ? '/app/user_a/joinchildend' : '/app/user_a/joinend';
		if ($result > 0)
		{
			//회원가입 메일
			$mailfile = './emailform/member_email.html';
			$strHtml = $this->common->htmlDocToString($mailfile);
			
			$strText = "email";
			$strVal = $userEmail;
			$strHtml = $this->common->stringReplaceMatchValue($strHtml, $strText, $strVal);
			
			//이메일 발송
			$mailDt = array(
				'fromEmail' => $this->config->item('email_addr'),
				'fromName' => $this->config->item('email_name'),
				'toEmail' => $userInfo['USER_EMAIL_DEC'],
				'cc' => '',
				'bcc' => '',
				'subject' => '가입축하!',
				'content' => $strHtml
			);
			$this->common->emailSend($mailDt);
			
			
			//$this->common->message('', $redirUrl.'/return_url/'.$this->_returnUrl, 'parent');
			$autoLogin = 'N';
			$userNumEnc = $this->common->sqlEncrypt($this->_uNum, $this->_encKey);
			
			// 아이폰과의 통신을 위한 코드
			// iframe 에 데이터를 넘긴다.
			echo 
			"<script>
		        var IOSframe = document.createElement('iframe');
		        IOSframe.style.display = 'none';
		        IOSframe.src = 'jscall://loginok/".$userNumEnc."/".$autoLogin."';
		        document.documentElement.appendChild(IOSframe);
			</script>";
						
			$this->common->message('', "parent.app_loginok('".$this->_returnUrl."', '".$userNumEnc."' ,'".$autoLogin."');", 'js');
		}		
	}
	
	/**
	 * @method name : setSnsUserEmailUpdate
	 * SNS 로그인후 이메일인증 진행시
	 * 
	 */
	private function setSnsUserEmailUpdate()
	{
		$emailEnc = $this->common->sqlEncrypt($this->input->post_get('useremail', TRUE), $this->_encKey);
		$result = $this->user_model->setSnsUserEmailUpdate($this->_uNum, $emailEnc);
		
		if ($result > 0)
		{
			$userInfo = $this->user_model->getUserRowData(array('NUM' => $this->_uNum));
			//SHOP 생성 작가인 경우 SHOP고유번호 가져오기
			$shopInfo = $this->user_model->getShopInfoByUserNum($this->_uNum);
			
			$this->common->setSession('session_date', date('Y-m-d H:i:s'));
			$this->common->setSession('user_num', $this->_uNum);
			$this->common->setSession('user_id', $userInfo['USER_ID']);
			$this->common->setSession('user_name', $userInfo['USER_NAME']);
			$this->common->setSession('user_nick', $userInfo['USER_NICK']);
			$this->common->setSession('user_email', $userInfo['USER_EMAIL']); //로그인시 이메일은 암호화된 내용으로 세팅
			$this->common->setSession('user_level', $userInfo['ULEVELCODE_NUM']);
			$this->common->setSession('user_state', $userInfo['USTATECODE_NUM']);			
			$this->common->setSession('profileimg', $this->common->getSession('profileimg'));
			$this->common->setSession('shop_num', (!$shopInfo) ? 0 : $shopInfo['NUM']);
			
			//$this->common->message('등록 되었습니다.', '/', 'parent')
			$deviceId =$this->common->getSession('sns_deviceId');
			$pushId =$this->common->getSession('sns_pushId');

	 		log_message('debug', '_deviceId : ' .$deviceId );
			log_message('debug', '_pushId : ' .$pushId );
			if (!empty($deviceId) && !empty($pushId))
			{
				//앱정보 update
				$this->user_model->setAppInfoUpdate($this->_uNum, $deviceId, $pushId);

			} 

			
			$userNumEnc = $this->common->sqlEncrypt($userInfo['NUM'].'_'.$userInfo['ULEVELCODE_NUM'], $this->_encKey);

				log_message('debug', 'email : ' .$this->input->post_get('useremail', TRUE) );	
				log_message('debug', '_uNum : ' .$this->_uNum );	
				log_message('debug', 'NUM : ' .$userInfo['NUM'] );	
				log_message('debug', 'ULEVELCODE_NUM : ' .$userInfo['ULEVELCODE_NUM'] );	
				log_message('debug', 'USER_EMAIL : ' .$userInfo['USER_EMAIL'] );	
				log_message('debug', 'auth key : ' .$userNumEnc );	

			$this->common->app_script("app_loginok('', '".$userNumEnc."', 'N');");
		}else{
			$this->common->app_script("alert('이미 등록된 이메일입니다.');");
		
		}		
	}
	
	/**
	 * @method name : getBeforeRegistUserInfo
	 * 가입보류된 상태 확인
	 * SNS회원가입 진행시 해당
	 * 
	 */
	private function getBeforeRegistUserInfo()
	{
		if ($this->_uNum > 0)
		{
			unset($this->_sendData['userEmail']);
			unset($this->_sendData['userName']);
			unset($this->_sendData['userBirth']);
			unset($this->_sendData['userGender']);
			unset($this->_sendData['userMobile']);
			//unset($this->_sendData['joinType']);			
			//$joinType = '';
			$userInfo = $this->user_model->getUserRowData(array('NUM' => $this->_uNum));
			//if ($userInfo['ULEVELCODE_NUM'] == 830) $joinType = 'sns'; //SNS가입보류	SNS회원가입중
		
			//	log_message('debug', '[circus] - USER_EMAIL : ' . $userInfo['USER_EMAIL']);			
			//	log_message('debug', '[circus] - _encKey : ' . $this->_encKey);			
			//log_message('debug', '[circus] - USER_EMAIL : ' . $this->common->sqlDecrypt($userInfo['USER_EMAIL'], $this->_encKey));	
				
			
			//$emailEnc = $this->common->sqlEncrypt($userInfo['SNSCODE_NUM'].'_'.$userInfo['SNS_ID'].'@circus.flag.co.kr', $this->_encKey);
			//if($emailEnc == $userInfo['USER_EMAIL']){			
			//	$userInfo['USER_EMAIL']="";
			//}


			$userMobile = $this->common->sqlDecrypt($userInfo['USER_MOBILE'], $this->_encKey);
			$userMobile = str_replace('-', '', $userMobile);
			$userBirth = str_replace('-', '', $userInfo['USER_BIRTH']);
			$this->_sendData = $this->_sendData + array(
				'userEmail' => $this->common->sqlDecrypt($userInfo['USER_EMAIL'], $this->_encKey),
				'userName' => $userInfo['USER_NAME'],
				'userBirth' => $userBirth,
				'userGender' =>	$userInfo['USER_GENDER'],
				'userMobile' =>	$userMobile
				//'joinType' => $joinType
			);
		}
	}
	
	/**
	 * @method name : setUserPasswordReissue
	 * 임시 비밀번호 발송 
	 * 
	 */
	private function setUserPasswordReissue()
	{
		$reqType = $this->input->post_get('reqtype', TRUE); //발송시 대상(메일, 휴대폰)
		$email = $this->input->post_get('useremail', TRUE);
		$emailEnc = $this->common->sqlEncrypt($email, $this->_encKey);
		
		$userInfo = $this->user_model->getUserRowData(array('USER_EMAIL' => $email),'email');
		if ($userInfo)
		{
			$result = $this->user_model->setUserPasswordReissue($reqType, $userInfo);  //임시비번받기
			//이메일, 문자로 발송
			if ($reqType == 'email')
			{
				$mailfile = './emailform/pass_email.html';
				$strHtml = $this->common->htmlDocToString($mailfile);
				
				$strText = "passwd|email";
				$strVal = $result.'|'.$userInfo['USER_EMAIL_DEC'];
				$strHtml = $this->common->stringReplaceMatchValue($strHtml, $strText, $strVal);
				
				//이메일 발송
				$mailDt = array(
					'fromEmail' => $this->config->item('email_addr'),
					'fromName' => $this->config->item('email_name'),
					'toEmail' => $userInfo['USER_EMAIL_DEC'],
					'cc' => '',
					'bcc' => '',
					'subject' => '비밀번호 전송!',
					'content' => $strHtml
				);
				$this->common->emailSend($mailDt);				
			}
			else if ($reqType == 'mobile')
			{
				$qData = array(
					'phoneNum' => $userInfo['USER_MOBILE_DEC'],
					'smsContent' => '[Circus]비밀번호는 '.$result. '입니다.',
					'smsSubject' => '', //단문인 경우 필요없음
					'smsType' => 'S'
				);
				$this->common->smsSend($qData);
			}
			
			//$this->common->message('임시 비밀번호를 발송하였습니다.', '/app/user_a/login', 'top');
			$msg = ($reqType == 'email') ? $email : $userInfo['USER_MOBILE_DEC'];
			$msg .= "(으)로 임시 비밀번호가 발송되었습니다.";
			$this->common->message('', "parent.msgNotice('".$msg."', '".$this->_returnUrl."');", 'js');			
		}
		else 
		{
			//$this->common->message('회원정보를 찾을 수 없습니다.\\n다시 확인해 보시기 바랍니다.', '-', '');
			$msg = "회원정보를 찾을 수 없습니다.<br />다시 확인해 보시기 바랍니다.";
			$this->common->message('', "parent.msgNotice('".$msg."', '".$this->_returnUrl."');", 'js');
		}
	}
	
	/**
	 * @method name : setAppInfoUpdate
	 * 사용자 앱고유정보 관리 
	 * 
	 * @param unknown $uNum
	 */
	private function setAppInfoUpdate($uNum)
	{
		$result = $this->user_model->setAppInfoUpdate($uNum, $this->_deviceId, $this->_pushId);
	}
}
?>