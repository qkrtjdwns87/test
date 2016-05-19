<?
defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * User
 * 
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class User_m extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = '';
	
	/**
	 * @var integer 회원 고유번호
	 */
	protected $_uNum = 0;
	
	/**
	 * @var integer PROFILE_FILE 고유번호
	 */
	protected $_fNum = 0;	
		
	/**
	 * @var string 처리후 되돌아갈 url
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
	 * @var integer 파일첨부갯수
	 */
	protected $_fileCnt = 1;
	
	/**
	 * @var bool 관리자 여부
	 */
	protected $_isAdmin = FALSE;
	
	/**
	 * @var integer USER LEVEL
	 */
	protected $_uLevelType = 0;	
	
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
		$this->loginCheck();		
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'login':
				$this->login();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/login', $data);				
				break;
			case 'list':
				$this->getUserDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/user/user_list', $data);
				break;
			case 'leavelist';
				$this->getUserDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/user/user_leave_list', $data);
				break;
			case 'passwordchange';
				$this->getUserDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/user/user_password_change', $data);
				break;
			case 'passwordchangepop';
				$this->getUserDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/user/user_password_change_pop', $data);
				break;
			case 'writeform':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/user/user_write', $data);
				break;
			case 'updateform':
				$this->getUserGroupRowData();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/user/user_write', $data);
				break;				
			case 'write':
				$this->setUserDataInsert();
				break;				
			case 'update':
				$this->setUserDataUpdate();
				break;
			case 'passwordupdate':
				$this->setUserPassWordUpdate();
				break;	
			case 'change':
				$this->setUserDataChange();
				break;
			case 'profilefiledelete';
				$this->setProfileFileDelete();
				break;						
			default:
				$this->{"{$this->_uriMethod}"}();
				break;				
		}		
	}
	
	/**
	 * @method name : setPrecedeValues
	 * uri 처리관련
	 * 검색은 파라메터로 처리
	 * 그외에는 모두 uri로 처리
	 * page uri는 각 view페이지 마다 틀리기때문에 view페이지에서 추가해 주는 형태로 처리
	 * 
	 * post, get 내용 처리 (선행처리가 필요한 것만 - 그외의 것은 메소드 안에서 처리)
	 */
	private function setPrecedeValues()
	{
		//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
		$this->_uri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
		$this->_arrUri = $this->common->segmentExplode($this->_uri);
		$this->_listCount = $this->config->item('board_list_count');	//페이지당 나열되는 리스트 갯수
		$this->_uriMethod = (!empty($this->uri->segment(3))) ? $this->uri->segment(3) : $this->_uriMethod;
		$this->_uriMethod = $this->common->nullCheck($this->_uriMethod, 'str', 'list');
		
		if (in_array('page', $this->_arrUri))
		{
			$this->_currentPage = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'page')));
		}
		$this->_currentPage = $this->common->nullCheck($this->_currentPage, 'int', 1);
		
		if (in_array('uno', $this->_arrUri))
		{
			$this->_uNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'uno')));
		}
		$this->_uNum = $this->common->nullCheck($this->_uNum, 'int', 0);
		
		if (in_array('fno', $this->_arrUri))
		{
			$this->_fNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'fno')));
		}
		$this->_fNum = $this->common->nullCheck($this->_fNum, 'int', 0);		
		
		if (in_array('return_url', $this->_arrUri))
		{
			$this->_returnUrl = $this->common->urlExplode($this->_arrUri, 'return_url');
		}
		$this->_returnUrl = $this->common->nullCheck($this->_returnUrl, 'str', '');		
		
		if ($this->_returnUrl == '') $this->_returnUrl = $this->input->post_get('return_url', FALSE);
		
		//검색조건에 해당되는 경우 get이나 post로 받고 parameter 구성
		$searchKey = $this->input->post_get('skey', TRUE);
		$searchWord = $this->input->post_get('sword', TRUE);
		
		if (!empty($searchKey) && !empty($searchWord)) $this->_currentParam .= '&skey='.$searchKey.'&sword='.$searchWord;
		
		$sDate = $this->input->post_get('sdate', TRUE);
		$eDate = $this->input->post_get('edate', TRUE);
		if (!empty($sDate) && !empty($eDate)) $this->_currentParam .= '&sdate='.$sDate.'&edate='.$eDate;
		
		$userState = $this->input->post_get('userstate', TRUE);
		if (!empty($userState)) $this->_currentParam .= '&userstate='.$userState;
		
		$userLevel = $this->input->post_get('userlevel', TRUE);
		if (!empty($userLevel)) $this->_currentParam .= '&userlevel='.$userLevel;		
		
		$userEmail = $this->input->post_get('useremail', TRUE);
		if (!empty($userEmail)) $this->_currentParam .= '&useremail='.$userEmail;		
		
		$userMobile = $this->input->post_get('usermobile', TRUE);
		if (!empty($userMobile)) $this->_currentParam .= '&usermobile='.$userMobile;		
		
		$userName = $this->input->post_get('username', TRUE);
		if (!empty($userName)) $this->_currentParam .= '&username='.$userName;		
		
		$userGender = $this->input->post_get('usergender', TRUE);
		if (!empty($userGender)) $this->_currentParam .= '&usergender='.$userGender;		
		
		$emailYn = $this->input->post_get('emailyn', TRUE);
		if (!empty($emailYn)) $this->_currentParam .= '&emailyn='.$emailYn;		
		
		$smsYn = $this->input->post_get('smsyn', TRUE);
		if (!empty($emailYn)) $this->_currentParam .= '&smsyn='.$smsYn;		
		
		$logincheckDay = $this->input->post_get('logincheckday', TRUE);
		if (!empty($logincheckDay)) $this->_currentParam .= '&logincheckday='.$logincheckDay;		
		
		$leaveAdminYn = $this->input->post_get('leaveadminyn', TRUE);
		if (!empty($leaveAdminYn)) $this->_currentParam .= '&leaveAdminYn='.$logincheckDay;		
		
		$leaveReason = $this->input->post_get('leavereason', TRUE);
		if (!empty($leaveReason)) $this->_currentParam .= '&leavereason='.$leaveReason;		

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
			'searchKey' => $searchKey,
			'searchWord' => $searchWord,
			'sDate' => $sDate,
			'eDate' => $eDate,
			'userState' => $userState,
			'userLevel' => $userLevel,
			'userEmail' => $userEmail,
			'userMobile' => $userMobile,
			'userName' => $userName,
			'userGender' => $userGender,
			'emailYn' => $emailYn,
			'smsYn' => $smsYn,
			'logincheckDay' => $logincheckDay,
			'leaveAdminYn' => $leaveAdminYn,
			'leaveReason' => $leaveReason,
			'pageMethod' => $this->_uriMethod,
			'uNum' => $this->_uNum,
			'tbl' => $this->_tbl,
			'tblEnc' => $this->common->sqlEncrypt($this->_tbl, $this->config->item('encryption_key')),				
			'fileCnt' => $this->_fileCnt,
			'isLogin' => $this->common->getIsLogin(),
			'isAdmin' => $this->_isAdmin,				
			'userLevelType' => $this->_uLevelType,				
			'sessionData' => $this->common->getSessionAll()
		);		
	}
	
	private function loginCheck()
	{
		$this->_uLevelType = $this->common->getSessionUserLevelCodeId();
		$this->_isAdmin = in_array($this->_uLevelType, array('SUPERADMIN', 'ADMIN', 'SHOPADMIN')) ? TRUE : FALSE;
		
		if (!in_array($this->_uriMethod, array('', 'login', 'loginconfirm')))
		{
			if (!$this->common->getIsLogin())
			{
				$this->common->message('로그인후 이용하실 수 있습니다.', '/manage/user_m/login', 'top');
			}

			if (!$this->_isAdmin && $this->_uLevelType != 'SHOP')
			{
				$this->common->message('관리자만 이용하실 수 있습니다.', '/manage/user_m/login', 'top');
			}
		}
	}	

	/**
	 * @method name : login
	 * 로그인 페이지로 이동 (view load) 
	 * SNS로그인에 필요한 내용들 모두 view 페이지로
	 * 
	 */
	private function login()
	{

	}
	
	private function logout()
	{
		$this->common->setLogout('/manage/user_m/login', TRUE);
	}	
	
	/**
	 * @method name : loginConfirm
	 * 로그인 정보 입력후 확인(web only)
	 * 
	 */
	private function loginConfirm()
	{
		$userEmail = $this->input->post_get('useremail', TRUE);
		$userPass = $this->input->post_get('userpw', TRUE);
		
		$this->_returnUrl = '/manage/main_m/main';	//base64_decode($this->_returnUrl);
		
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

				$this->_uLevelType = $this->common->getCodeIdByCodeNum($this->_data['ULEVELCODE_NUM']);
				$this->_isAdmin = in_array($this->_uLevelType, array('SUPERADMIN', 'ADMIN', 'SHOPADMIN')) ? TRUE : FALSE;
				if (!$this->_isAdmin && $this->_uLevelType != 'SHOP') 
				{
					$this->common->message('관리자만 이용하실 수 있습니다.', '-', '');
				}
			}
			
			//최근 로그인 일자 update
			$this->user_model->setUserLastLoginUpdate($this->_data['NUM']);			
			//SHOP 생성 작가인 경우 SHOP고유번호 가져오기
			if ($this->_data['ULEVELCODE_NUM'] == 670)
			{
				$shopInfo = $this->user_model->getShopInfoByUserNum($this->_data['NUM']);
				if ($shopInfo['SHOPSTATECODE_NUM'] < 3060 )
				{
					//샵이 승인이전 상태인 경우
					$this->common->message('삽이 아직 승인전 단계입니다.승인후 이용하실 수 있습니다.', '-', '');
				}
				//$this->_returnUrl = '/manage/shop_m/view/sno/'.$this->_data['SHOP_NUM'];
			}
		
			$this->common->setSession('session_date', date('Y-m-d H:i:s'));
			$this->common->setSession('user_num', $this->_data['NUM']);
			$this->common->setSession('user_id', $this->_data['USER_ID']);			
			$this->common->setSession('user_name', $this->_data['USER_NAME']);
			$this->common->setSession('user_nick', $this->_data['USER_NICK']);
			$this->common->setSession('user_email', $this->_data['USER_EMAIL']); //로그인시 이메일은 암호화된 내용으로 세팅			
			$this->common->setSession('user_level', $this->_data['ULEVELCODE_NUM']);
			$this->common->setSession('user_state', $this->_data['USTATECODE_NUM']);
			$this->common->setSession('shop_num', $this->_data['SHOP_NUM']);			
		
			//redirect($this->_returnUrl);
			$this->common->message('', $this->_returnUrl, 'parent');
		}
		else
		{
			$this->common->message('잘못된 접근입니다.', '-', '');			
		}
	}
	
	/**
	 * @method name : getGroupCodeDataList
	 * 관계되는 모든 CODE Data List
	 *
	 */
	private function getGroupCodeDataList()
	{
		$this->_data['uStateCdSet'] = $this->common->getCodeListByGroup('USERSTATE');
		$this->_data['uLevelCdSet'] = $this->common->getCodeListByGroup('USERLEVEL');
		$this->_data['snsCdSet'] = $this->common->getCodeListByGroup('SNS');
		$this->_data['leaveCdSet'] = $this->common->getCodeListByGroup('LEAVE_REASON');
	}	
	
	private function getUserDataList()
	{
		$this->_data = $this->user_model->getUserDataList($this->_sendData, FALSE);
		//페이징으로 보낼 데이터
		$pgData = array(
			'rsTotalCount' => $this->_data['rsTotalCount'],
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentUrl' => $this->_currentUrl,
			'currentParam' => $this->_currentParam
		);
		$this->_data['pagination'] = $this->common->listAdminPagingUrl($pgData);
	}
	
	/**
	 * @method name : getUserGroupRowData
	 * 회원 고유번호와 연결된 table 내역 가져오기 
	 * 
	 */
	private function getUserGroupRowData()
	{
		$this->getUserBaseRowData();
		$this->getUserLatestOrderRowData();
	}
	
	private function getUserBaseRowData()
	{
		$this->_data['baseSet'] = $this->user_model->getUserRowData(array('NUM' => $this->_uNum));		
	}
	
	/**
	 * @method name : getUserOrderRowData
	 * 회원 최근 주문 내역 
	 * 
	 */
	private function getUserLatestOrderRowData()
	{
		
	}
	
	/**
	 * @method name : 신규 회원가입
	 * 
	 * 
	 */
	private function setUserDataInsert()
	{
		
	}

	
	private function setUserDataUpdate()
	{
		$toDate = date('Y-m-d H:i:s');
		$birthDay = $this->input->post_get('birth_year', TRUE).'-'.$this->input->post_get('birth_month', TRUE).'-'.$this->input->post_get('birth_day', TRUE);
		$mobileEnc = $this->common->sqlEncrypt($this->input->post_get('user_mobile1', TRUE).'-'.$this->input->post_get('user_mobile2', TRUE).'-'.$this->input->post_get('user_mobile3', TRUE), $this->_encKey);
		$userState = $this->input->post_get('user_state', TRUE);
		$userStateOrg = $this->input->post_get('user_state_org', TRUE);
		$userStateMemo = $this->input->post_get('ustate_memo', TRUE);
		$emailYn  = $this->input->post_get('email_yn', TRUE);
		$emailYnOrg  = $this->input->post_get('email_yn_org', TRUE);
		$smsYn  = $this->input->post_get('sms_yn', TRUE);
		$smsYnOrg  = $this->input->post_get('email_yn_org', TRUE);
		$marketYn  = $this->input->post_get('market_yn', TRUE);
		$marketYnOrg  = $this->input->post_get('market_yn_org', TRUE);		
		$userGender = $this->input->post_get('user_gender', TRUE);
		$penaltyCount = $this->input->post_get('penalty_count', TRUE);
		//14세미만 승인 대기였으나 정상으로 변경된 경우
		$approvalDate = ($userStateOrg == 940 && $userState == 930) ? $toDate : '';
		
		//상태가 패널티 상태로 변경되는 경우
		$penaltyDate = ($userStateOrg != 950 && $userState == 950) ? $toDate : '';
		//패널티카운트는 어디서 부여?
		//$penaltyCount =($userStateOrg != 950 && $userState == 950) ? ($penaltyCount + 1) : $penaltyCount;
		
		$emailChangeDate = ($emailYn != $emailYnOrg) ? $toDate : ''; 
		$smsChangeDate = ($smsYn != $smsYnOrg) ? $toDate : '';

		$upData = array(
			'USER_MOBILE' => $mobileEnc,
			'USTATECODE_NUM' => (!empty($userState)) ? $userState : $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'OK'),
			'USTATE_MEMO' => $userStateMemo,
			'EMAIL_YN' => $emailYn,
			'SMS_YN' => $smsYn,
			'MARKET_YN' => $marketYn,
			//'PENALTY_COUNT' => $penaltyCount,
			'UPDATE_DATE' => $toDate
		);
		
		if (!empty($userGender)) $upData['USER_GENDER'] = $userGender;
		if (!empty($birthDay) && $birthDay != '--') $upData['USER_BIRTH'] = $birthDay;
		if (!empty($emailChangeDate)) $upData['EMAILYN_CHANGE_DATE'] = $emailChangeDate;
		if (!empty($smsChangeDate)) $upData['SMSYN_CHANGE_DATE'] = $smsChangeDate;
		if (!empty($approvalDate)) $upData['APPROVAL_DATE'] = $approvalDate;		
		if (!empty($penaltyDate)) $upData['PENALTY_DATE'] = $penaltyDate;

		$result = $this->user_model->setUserDataUpdate($this->_uNum, $upData, TRUE);
		
		if ($result > 0)
		{
			$listUrl = '/manage/user_m/list';
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';
				
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}
	}
	
	private function setUserDataChange()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$method = $this->input->post_get('method', FALSE);
		$selValue = $this->input->post_get('selval', FALSE);
		
		$result = $this->user_model->setUserDataChange($method, $selValue);
		
		if($result > 0)
		{
			$this->common->message('변경 되었습니다.', $this->_returnUrl, 'top');
		}
	}
	

	/**
	 * @method name : setProfileFileDelete
	 * 프로필 파일첨부 내용 삭제
	 *
	 */
	private function setProfileFileDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$this->user_model->setProfileFileDelete($this->_fNum);
	
		$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		//$this->common->message('삭제 되었습니다.', 'reload', 'parent');
	}	


	/**
	 * @method name : 비밀번호 변경
	 * 
	 */
	private function setUserPassWordUpdate()
	{

		$updateType = $this->input->post_get('updateType', TRUE); //update Type 구분 
		

		if($updateType == 'login'){

			$uNum = $this->common->getSession('user_num');			   // 로그인 한 사용자 사용자 번호 
			$userPass = sha1($this->input->post_get('passwd1', TRUE)); // 로그인 한 사용자 password

		}else if($updateType =='select'){

			$uNum = $this->input->post_get('selUserNum', TRUE); 		// 선택한 사용자 번호 
			$userPass = sha1($this->input->post_get('passwd1', TRUE));  // 선택한 사용자 password

		}else{
			//EMAIL 이거나 SMS 일떄 
			$sendType  = $this->input->post_get('sendType', TRUE); 	// sendType : SMS or EMAIL
			$userMobile = $this->input->post_get('userMobile', TRUE); // 사용자 전화번호
			$userEmail = $this->input->post_get('userEmail', TRUE); // 사용자 이메일  
			$uNum 	   = $this->input->post_get('selUserNum', TRUE); // 사용자 번호 
			$newPass   = $this->common->generatePassword();       	 // 새로운 비밀번호 생성 
			$userPass  = sha1($newPass);      					 	 // 새로운 비밀번호 암호화 

			//메일 보낼때 필요한 DATA Setting
			$tmpData = array(
				'sendType' => $this->input->post_get('sendType', TRUE),
				'newPass'  => $newPass,
			);

			if(!empty($sendType) && $sendType == 'SMS'){
				$tmpData['userMobile'] = str_replace('-', '', $userMobile);
				//SMS 보내기 ( 아직 기능 구현 X 20160518)

			}else {
				$tmpData['userEmail'] = $userEmail;
				//사용자에게 이메일 전송 
				$this->common->sendEMailTemp($tmpData);
			}

		}

		//사용자 정보 업데이트 
		$result = $this->user_model->setUserPassWordUpdate_sp($uNum, $userPass, TRUE);
		
		if ($result > 0)
		{
			if((!empty($sendType) && $sendType == 'SMS')||(!empty($sendType) && $sendType == 'EMAIL')){
				$returnUrl = '/manage/user_m/list';// 수정완료 후 주석 해제 
				$this->common->message('새로운 비밀 번호를 전송 완료 하였습니다.', $returnUrl, 'top');
			}else{
				$returnUrl = '/manage/user_m/list';// 수정완료 후 주석 해제 
				$this->common->message('수정 되었습니다.', $returnUrl, 'top');
			}
		}
	}

}
?>