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
 * User
 * 
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class User extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_searchKey = '';
	
	protected $_searchWord = '';
	
	protected $_uriMethod = 'login';
	
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
	
	public function __construct() 
	{
		parent::__construct ();
	
		$this->load->helper(array('url'));
		$this->load->model('user_model');
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();	
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'login':
				$this->login();
				$result = array_merge($this->_data, $this->_sendData);
				$this->load->view('user/login_view', array_merge($this->_data, $this->_sendData));				
				break;
			case 'writeform':
				$this->load->view('user/user_write', $this->_sendData);
				break;
			case 'write':
				$this->setUserDataInsert();
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
		$this->_isApi = $this->input->post_get('isApi', FALSE);
		
		//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
		$this->_uri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
		$this->_arrUri = $this->common->segmentExplode($this->_uri);
		$this->_uriMethod = (!empty($this->uri->segment(2))) ? $this->uri->segment(2) : $this->_uriMethod;
		
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
		
		if ($this->_returnUrl == '')
		{
			$this->_returnUrl = $this->input->post_get('return_url', FALSE);
		}
		
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->_uriMethod;		
		
		$this->loginCheck();

		/*
		 * 파라메터 처럼 array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'returnUrl' => $this->_returnUrl,				
			'searchKey' => $this->_searchKey,
			'searchWord' => $this->_searchWord,
			'pageMethod' => $this->_uriMethod,
			'uNum' => $this->_uNum,
			'tbl' => $this->_tbl,
			'fileCnt' => $this->_fileCnt,
			'isCIpagingUse' => $this->_isCIpagingUse,
			'isLogin' => $this->common->getIsLogin(),
			'sessionData' => $this->common->getSessionAll()
		);		
	}
	
	private function loginCheck()
	{
		if (in_array($this->_uriMethod, array('update', 'delete')))
		{
			if (!$this->common->getIsLogin())
			{
				$this->common->message('로그인후 이용하실 수 있습니다.', '', 'top');
			}
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
		//Facebook
		$this->_fb = new Facebook\Facebook([
			'app_id' => $this->config->item('facebook_appid'),
			'app_secret' => $this->config->item('facebook_secret_code'),
			'default_graph_version' => 'v2.2',
		]);
		
		$helper = $this->_fb->getRedirectLoginHelper();
		$permissions = ['email']; // optional ['email', 'publish_stream']
		$fbLoginUrl = $helper->getLoginUrl(
			$this->common->getDomain().'/extApi/facebook_callback?return_url='.$this->_returnUrl, 
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
		
		$this->_returnUrl = base64_decode($this->_returnUrl);
		
		if (!empty($userEmail) && !empty($userPass))
		{
			$userPass = sha1($userPass);
			$this->_data = $this->user_model->getUserRowData(array('USER_EMAIL' => $userEmail), 'email');	//이메일주소로 db에서 정보 가져오기
			
			if (count($this->_data) == 0)
			{
				$this->common->message('회원가입내용을 찾을 수 없습니다.\\n다시 확인해 주시기 바랍니다.', '-', '');				
			}
			else
			{
				if ($this->_data['DEL_YN'] == 'Y')
				{
					$this->common->message('회원정보를 찾을 수 없습니다.\\n다시 확인해 주시기 바랍니다.', '-', '');
				}
				
				if ($this->_data['USER_PASS'] != $userPass)
				{
					$this->common->message('비밀번호가 일치하지 않습니다.\\n다시 확인해 주시기 바랍니다.', '-', '');				
				}
				
				$stdUserCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'USER'); //유효 회원판별 기준 레벨 코드번호
				$dorUserCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'DORMANT'); //휴면회원 레벨 코드번호

				if ($this->_data['ULEVELCODE_NUM'] >= $stdUserCodeNum)
				{
					if ($this->_data['USTATECODE_NUM'] == $dorUserCodeNum)
					{
						$this->common->message('계정이 휴면상태입니다.\\n휴면해지 절차를 진행해 주시기 바랍니다.', '-', '');
					}
					
					$this->common->message('회원정보를 찾을 수 없습니다.\\n다시 확인해 주시기 바랍니다.', '-', '');					
				}
			}
			
			//최근 로그인 일자 update
			$this->user_model->setUserLastLoginUpdate($this->_data['NUM']);			
			//SHOP 생성 작가인 경우 SHOP고유번호 가져오기
			$shopInfo = $this->user_model->getShopInfoByUserNum($this->_data['NUM']);
		
			$this->common->setSession('session_date', date('Y-m-d H:i:s'));
			$this->common->setSession('user_num', $this->_data['NUM']);
			$this->common->setSession('user_id', $this->_data['USER_ID']);			
			$this->common->setSession('user_name', $this->_data['USER_NAME']);
			$this->common->setSession('user_nick', $this->_data['USER_NICK']);
			$this->common->setSession('user_email', $this->_data['USER_EMAIL']); //로그인시 이메일은 암호화된 내용으로 세팅			
			$this->common->setSession('user_level', $this->_data['ULEVELCODE_NUM']);
			$this->common->setSession('shop_num', $this->_data['SHOP_NUM']);			
		
			//redirect($this->_returnUrl);
			$this->common->message('', $this->_returnUrl, 'parent');
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
	public function setUserDataInsert()
	{
		$emailEnc = $this->common->sqlEncrypt($this->input->post_get('uemail', TRUE), $this->_encKey);
		$userPass = sha1($this->input->post_get('upass', TRUE));
		$userMobile = $this->input->post_get('umobile1', TRUE).'-'.$this->input->post_get('umobile2', TRUE).'-'.$this->input->post_get('umobile3', TRUE);
		$userMobileEnc = $this->common->sqlEncrypt($userMobile, $this->_encKey);
		$userBirth = $this->input->post_get('ubirth_year', TRUE)
					.'-'.str_pad($this->input->post_get('ubirth_month', TRUE), 2, '0', STR_PAD_LEFT)
					.'-'.str_pad($this->input->post_get('ubirth_day', TRUE), 2, '0', STR_PAD_LEFT);
		
		$insData = array(
			'USER_NAME' => $this->input->post_get('uname', TRUE),
			'USER_EMAIL' => $emailEnc,
			'USER_PASS' => $userPass,
			'ULEVELCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'USER'),
			'USTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'OK'),				
			'USER_GENDER' => $this->input->post_get('ugender', TRUE),
			'USER_MOBILE' => $userMobileEnc,
			'USER_BIRTH' => $userBirth,				
			'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'NONE'),
			'INFLOW_ROUTE' => ($this->common->getMobileCheck()) ? 'M' : 'W',
			'LEAVE_RESONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('LEAVE_REASON', 'NONE'),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->user_model->setUserDataInsert($insData);
		
		if ($result > 0)
		{
			$this->common->message('가입 되었습니다.', '/', 'parent');
		}		
	}
}
?>