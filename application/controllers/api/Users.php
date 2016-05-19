<?
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

/**
 * package_name
 * 
 *
 * @author : Administrator
 * @date    : 2016. 2
 * @version:
 */
class Users extends REST_Controller {
	
	protected $_method = '';
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;	

	/**
	 * @var integer USER 고유번호
	 */
	protected $_uNum = 0;
	
	/**
	 * @var integer 앱으로 부터 전달받는 authkey (사용자 고유번호 등 pk 고유번호)
	 */
	protected $_authkey = 0;
	
	/**
	 * @var string 앱으로 부터 전달받는 고유 deviceid
	 */
	protected $_deviceId = '';
	
	/**
	 * @var string 앱으로 부터 전달받는 pushid
	 */
	protected $_pushId = '';
	
	/**
	 * @var string 앱사용자 유효성 체크
	 */
	protected  $isAppAuth = FALSE;
	
	/**
	 * @var integer USER LEVEL
	 */
	protected $_uLevelType = 0;	
	
	/**
	 * @var array	data set
	 */
	protected $_data = array();	
	
	protected $_encKey = '';	
	
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();

        $this->load->helper(array('url', 'cookie'));
        $this->load->model(array('user_model', 'item_model', 'story_model'));
        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['user_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function user_get() {exit('No access allowed');}
    public function user_put() {exit('No access allowed');}
    public function user_delete() {exit('No access allowed');}
    //POST방식만 사용
    public function user_post()
    {
    	$this->_method = $this->input->post('method', TRUE);
    	$this->_authkey = $this->common->nullCheck($this->input->post('authkey', TRUE), 'str', '');
    	if (!empty($this->_authkey))
    	{
    		//암호화된 내용이므로 복호화
    		$this->_authkey = $this->common->sqlDecrypt($this->_authkey, $this->_encKey);
    		$arrAuth = explode('_', $this->_authkey);
    		$this->_authkey = $arrAuth[0];
    		$this->_uLevelType = $this->common->getCodeIdByCodeNum($arrAuth[1]);    		
    	}
    	$this->_deviceId = $this->input->post_get('deviceid', TRUE);
    	$this->_pushId = $this->input->post_get('pushid', TRUE);
    	$this->_currentPage = $this->input->post('page', TRUE);
    	$this->_listCount = $this->input->post('listcount', TRUE);

        log_message('debug', '[circus] _authkey : ' . $this->_authkey);
        log_message('debug', '[circus] _deviceId : ' . $this->_deviceId);
        log_message('debug', '[circus] _pushId : ' . $this->_pushId);

    	$this->_isAppAuth = $this->common->getAppAuthCheck($this->_authkey, $this->_deviceId, $this->_pushId); //deviceid, pushid 유효성 검증
    	$this->_uNum = $this->input->post('uno', TRUE);

        log_message('debug', '_isAppAuth : ' . $this->_isAppAuth);
    	
    	$this->apiRemap(); //분기
    	
    	if (!isset($this->_data) || empty($this->_data))
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'data not found'
    		], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code    		
    	}
    	else 
    	{
    		$this->response($this->_data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    	}
    }

    /**
     * @method name : remap
     * method별 분기 처리
     * 
     */
    public function apiRemap()
    {
    	if (!in_array($this->_method, array('version')) && !$this->_isAppAuth)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    	
        log_message('debug', '[circus] - method : ' . $this->_method);

    	switch($this->_method)
    	{
    		case 'version':    			
    			$this->_data = $this->getCurrentAppVersion();
    			break;
    		case 'relogin';
    			$this->_data = $this->setReloginAsSession();
    			break;
    		case 'logout';
    			$this->_data = $this->setLogOut();
    			break;    		
    		case 'main':
    		case 'mainuser': //타인의 정보    			
    			$this->_data = $this->getMypageMainRowData();
    			break;
    		case 'update': //내정보 update
    			$this->_data = $this->setMyInfoUpdate();
    			break;
    		case 'flagopen': //flag공개설정 여부 update
    			$this->_data = $this->setFlagOpenUpdate();
    			break; 
    		case 'flagitem':
    		case 'flagitemuser':
    			$this->_data = $this->getFlagItemDataList();
    			break;    			
   			case 'flagshop':
   			case 'flagshopuser':   				
   				$this->_data = $this->getFlagShopDataList();
   				break; 
   			case 'flagstory':
   			case 'flagstoryuser':
   				$this->_data = $this->getFlagStoryDataList();
   				break;   				
   			case 'follower': //본인을 팔로윙하는 경우
   			case 'following': //본인이 팔로워하는 경우
   				$this->_data = $this->setFollow();
   				break;
   			case 'followerlist':
   			case 'followinglist':
   			case 'followerlistuser':
   			case 'followinglistuser':   				
   				$this->_data = $this->getFollowDataList();
   				break;  
   			case 'pushupdate':
   				$this->_data = $this->setAppPushConfigUpdate();
   				break;
   			case 'pushlist':
   				$this->_data = $this->getAppPushConfigData();
   				break;   				
    	}
    }  
    
    /**
     * @method name : getCurrentAppVersion
     * 가장 최근 앱버전 
     * 
     */
    private function getCurrentAppVersion()
    {
    	$ver = $this->config->item('app_version');
    	$result = array(
    		'versionname' => $ver,
    		'versioncode' => (string)intval($ver)
    	);
    	return $result;	
    }   
    
    /**
     * @method name : setReloginAsSession
     * 세션 생성용 api 
     * 
     */
    private function setReloginAsSession()
    {
    	$result = 0;
		$userInfo = $this->common->getUserInfo('num', $this->_authkey);
		
		if ($userInfo)
		{
			$result = 1;
			$shopInfo = $this->user_model->getShopInfoByUserNum($userInfo['NUM']);
			
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
			
			//웹뷰 페이지용 쿠키생성
			$cookieExpire = 0; //(60*60*24)*30; //30일 유지
			set_cookie('usernum', $userInfo['NUM'], $cookieExpire);
			set_cookie('profileimg', $defaultImg, $cookieExpire);			
			set_cookie('authkey', $this->_authkey, $cookieExpire);
			set_cookie('deviceid', $this->_deviceId, $cookieExpire);
			set_cookie('pushid', $this->_pushId, $cookieExpire);			
		}
		
		return array('result' => $result);
    }
    
    /**
     * @method name : getMypageMainRowData
     * My Circus Main
     * CART_COUNT :장바구니 아이템 개수
     * FLAGOPEN_YN : Flag 공개여부
     * MSGUNREAD_COUNT : 읽지않은 메시지 개수
     * 프로필 이동시 userInfo 그대로 가지고 넘어갈것
     * 
     * 
     * @return unknown
     */
    private function getMypageMainRowData()
    {
    	$userNum = $this->_authkey;

		// log_message('debug', 'phone _authkey : ' .$this->_authkey );	
    	if ($this->_method == 'mainuser')
    	{
    		$userNum = $this->input->post_get('userno', TRUE);
    		if (empty($userNum))
    		{
    			$this->response([
    				'status' => FALSE,
    				'message' => 'userno not specification'
    			], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    		}
    		$result['isFollow'] = $this->common->getIsFollowUser($userNum, $this->_authkey);    		
    	}

    	$result['userSet'] = $this->common->getUserInfo('num', $userNum);
 		//$result['currentAuthkey'] = get_cookie('authkey');  //본인 여부 판단을 위해서	
 		
    	return $result;
    }
    
    /**
     * @method name : setMyInfoUpdate
     * 프로필정보 업데이트 
     * 
     */
    private function setMyInfoUpdate()
    {
    	$marketYn = $this->common->nullCheck($this->input->post_get('marketyn', TRUE), 'str', 'N');
    	$userPass = $this->input->post_get('pwd', TRUE);
    	$userMobile = $this->input->post_get('usermobile', TRUE);
    	$userBirthday = $this->input->post_get('birthday', TRUE);
    	$userGender = $this->input->post_get('gender', TRUE);
    	
    	$upData = array('MARKET_YN' => strtoupper($marketYn));
    	
    	if (!empty($userPass))
    	{
    		$upData['USER_PASS'] = sha1($userPass);
    	}

		if (!empty($userBirthday)&& strlen($userBirthday) == 8)
    	{
    		$userBirthday = substr($userBirthday, 0, 4).'-'.substr($userBirthday, 4, 2).'-'.substr($userBirthday, 6, 2);
			$upData['USER_BIRTH'] =$userBirthday;
    	}
		if (!empty($userGender))
    	{
    		$upData['USER_GENDER'] = $userGender;
    	}
    	
    	if (!empty($userMobile) && strlen($userMobile) > 9)
    	{
    		if (strlen($userMobile) == 10)
    		{
    			$userMobile = substr($userMobile, 0, 3).'-'.substr($userMobile, 3, 3).'-'.substr($userMobile, 6, 4);
    		}
    		else
    		{
    			$userMobile = substr($userMobile, 0, 3).'-'.substr($userMobile, 3, 4).'-'.substr($userMobile, 7, 4);
    		}
    		$userMobileEnc = $this->common->sqlEncrypt($userMobile, $this->_encKey);
    		
    		$upData['USER_MOBILE'] = $userMobileEnc;
    	}
    	
    	$result = $this->user_model->setUserDataUpdate($this->_authkey, $upData, TRUE);
    	
    	return array('result' => $result);    	
    }
    
    /**
     * @method name : setFlagOpenUpdate
     * 플래그 공개여부 
     * 
     * @return unknown[]
     */
    private function setFlagOpenUpdate()
    {
    	$openYn = $this->input->post_get('openyn', TRUE);
    	if (empty($openYn))
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'openyn not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	$result = $this->user_model->setFlagOpenUpdate($this->_authkey, $openYn);
    	
    	return array('result' => $result);
    }
    
    /**
     * @method name : getFlagItemDataList
     * 플래그한 아이템 리스트
     * 앱으로 부터 꼭 받아야 할 파라메터
     * listcount
     * page
     * 
     * 
     * @return unknown
     */
    private function getFlagItemDataList()
    {
    	$userNum = $this->_authkey;
    	if ($this->_method == 'flagitemuser')
    	{
    		$userNum = $this->input->post_get('userno', TRUE); //타인의 리스트를 보는 경우
    		if (empty($userNum))
    		{
    			$this->response([
    				'status' => FALSE,
    				'message' => 'userno not specification'
    			], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    		}
    	}
    	
    	$qData = array(
    		'userNum' => $userNum,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage,
    		'isValidData' => TRUE //유효 데이터	
    	);
    	if ($this->_method == 'flagitemuser') $qData['seeUserNum'] = $this->_authkey; //타인의 리스트나 플래그는 내 기준으로
    	$result['flagItemSet'] = $this->item_model->getFlagItemDataList($qData);
    	
    	return $result;
    }
    
    /**
     * @method name : getFlagShopDataList
     * 플래그한 샵과 샵에 속한 아이템 리스트
     * 앱으로 부터 꼭 받아야 할 파라메터
     * listcount
     * page
     * item_listcount
     * item_page
     * 
     * @return unknown
     */
    private function getFlagShopDataList()
    {
    	$userNum = $this->_authkey;
    	if ($this->_method == 'flagshopuser')
    	{
    		$userNum = $this->input->post_get('userno', TRUE); //타인의 리스트를 보는 경우
    		if (empty($userNum))
    		{
    			$this->response([
    				'status' => FALSE,
    				'message' => 'userno not specification'
    			], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    		}
    	}
    	
    	$itemListCount = $this->input->post_get('item_listcount', TRUE); //삽별 아이템 리스트 나열 갯수
    	$itemCurrentPage = $this->input->post_get('item_page', TRUE); //삽별 아이템 페이지
    	$qData = array(
    		'userNum' => $userNum,
    		'itemListCount' => $itemListCount,
    		'itemCurrentPage' => $itemCurrentPage,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage,
    		'isValidData' => TRUE //유효 데이터
    	);
    	if ($this->_method == 'flagshopuser') $qData['seeUserNum'] = $this->_authkey; //타인의 리스트나 플래그는 내 기준으로    	
    	$result['flagShopSet'] = $this->item_model->getFlagShopDataList($qData); //itemlist가 필요함으로 item_model
    	 
    	return $result;
    }
    
    /**
     * @method name : getFlagStoryDataList
     * 플래그한 스토리
     * 
     * @return unknown
     */
    private function getFlagStoryDataList()
    {
    	$userNum = $this->_authkey;
    	if ($this->_method == 'flagstoryuser')
    	{
    		$userNum = $this->input->post_get('userno', TRUE); //타인의 리스트를 보는 경우
    		if (empty($userNum))
    		{
    			$this->response([
    				'status' => FALSE,
    				'message' => 'userno not specification'
    			], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    		}
    	}
    	 
    	$qData = array(
   			'userNum' => $userNum,
   			'listCount' => $this->_listCount,
   			'currentPage' => $this->_currentPage,
   			'isValidData' => TRUE //유효 데이터
    	);
    	if ($this->_method == 'flagstoryuser') $qData['seeUserNum'] = $this->_authkey; //타인의 리스트나 플래그는 내 기준으로
    	$result['flagStorySet'] = $this->story_model->getFlagStoryDataList($qData);
    	
    	return $result;
    }
    
    /**
     * @method name : setFollow
     * 팔로윙 또는 팔로워 하기 
     * 
     */
    private function setFollow()
    {
    	$toUserNum = $this->input->post_get('touserno', TRUE);
    	if (empty($toUserNum))
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'touserno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	$qData = array(
    		'pageMethod' => $this->_method,
    		'userNum' => $this->_authkey,
    		'toUserNum' => $toUserNum
    	);
    	$result = $this->user_model->setFollow($qData);
    	
    	return array('result' => $result);
    }
    
    /**
     * @method name : getFollowDataList
     * Follower, Following User List 
     * 
     */
    private function getFollowDataList()
    {
    	$userNum = $this->_authkey;
    	if ($this->_method == 'followerlistuser' || $this->_method == 'followinglistuser')
    	{
    		$userNum = $this->input->post_get('userno', TRUE);
    		if (empty($userNum))
    		{
    			$this->response([
    				'status' => FALSE,
    				'message' => 'userno not specification'
    			], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    		}
    	}
    	
    	$qData = array(
    		'pageMethod' => $this->_method,
    		'userNum' => $userNum,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage    			
    	);    	
    	$result['followSet'] = $this->user_model->getFollowUserDataList($qData);
    	
    	return $result;
    }
    
    /**
     * @method name : setLogOut
     * 앱에서 로그아웃
     * 
     * @return string[]
     */
    private function setLogOut()
    {
    	$this->common->setAppLogout();
    	
    	return array('result' => 'ok');
    }
    
    /**
     * @method name : setAppPushConfigUpdate
     * 앱 푸시설정 업데이트 
     * 
     * @return unknown[]
     */
    private function setAppPushConfigUpdate()
    {
    	$upData = array(
   			'MARKET_YN' => strtoupper($this->common->nullCheck($this->input->post_get('marketyn'), 'str', 'N')),
			'DELIVERY_YN' => strtoupper($this->common->nullCheck($this->input->post_get('deliveryyn'), 'str', 'N')),
    		'FLAGSHOP_YN' => strtoupper($this->common->nullCheck($this->input->post_get('flagshopyn'), 'str', 'N')),
			'FLAGITEM_YN' => strtoupper($this->common->nullCheck($this->input->post_get('flagitemyn'), 'str', 'N')),
    		'MSG_YN' => strtoupper($this->common->nullCheck($this->input->post_get('msgyn'), 'str', 'N')),
			'QUEST_YN' => strtoupper($this->common->nullCheck($this->input->post_get('questyn'), 'str', 'N'))
    	);
    	
    	$qData = array(
    		'deviceId' => $this->_deviceId,
    		'pushId' => $this->_pushId
    	);
    	
    	$result = $this->user_model->setAppPushConfigUpdate($qData, $upData);
    	
    	return array('result' => $result);
    }
    
    private function getAppPushConfigData()
    {
    	$qData = array(
    		'deviceId' => $this->_deviceId,
    		'pushId' => $this->_pushId
    	);
    	 
    	$result['pushSet'] = $this->user_model->getAppPushConfigData($qData);
    	
    	return $result;
    }
}