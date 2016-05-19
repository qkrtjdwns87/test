<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Shop
 * 
 *
 * @author : Administrator
 * @date    : 2015. 12.
 * @version:
 */
class Shop_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer SHOP 고유번호
	 */
	protected $_sNum = 0;
	
	/**
	 * @var integer PROFILE_FILE 고유번호
	 */
	protected $_fNum = 0;	
	
	/**
	 * @var integer PROFILE_FILE 인덱스(FILE_ORDER)
	 */
	protected $_fIndex = 0;
	
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
	
	protected $_tbl = 'SHOP';
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = TRUE;
	
	/**
	 * @var integer 파일첨부갯수
	 */
	protected $_fileCnt = 3;
	
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
		$this->load->model(array('shop_model', 'user_model'));
		
		$this->_encKey = $this->config->item('encryption_key');

		
		
		/*
		$mailfile = './application/controllers/mail.html';
		$strHtml = $this->common->htmlDocToString($mailfile);
		
		$strText = "NAME|EMAIL";
		$strVal = "테스트|test@test.com";
		$strHtml = $this->common->stringReplaceMatchValue($strHtml, $strText, $strVal);
		
		$mailDt = array(
			'fromEmail' => 'admin@circusflag.com',
			'fromName' => '써커스',
			'toEmail' => 'churk@pixelize.co.kr',
			'cc' => '',
			'bcc' => '',
			'subject' => '테스트 메일 입니다.',
			'content' => $strHtml
		);
		
		$this->common->emailSend($mailDt);
		*/		
	}
	
	public function _remap()
	{
		$this->loginCheck();
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'list':
				//전체샵 리스트				
				$this->getShopDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_list', $data);
				break;
			case 'apprlist':
				//approval list
				//승인처리단계 리스트
				$this->getShopDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_approval_list', $data);
				break;
			case 'view':
				$this->getStandardShopPolicyRowData();
				$this->getShopGroupRowData();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_view', $data);
				break;
			case 'apprview':
				$this->getStandardShopPolicyRowData();
				$this->getShopGroupRowData();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_approval_view', $data);
				break;
			case 'writeform':
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_write', $data);
				break;
			case 'writeconfirm':
				//신규입력후 확인페이지
				$this->getShopGroupRowData();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_write_confirm', $data);
				break;
			case 'updateform':
				$this->getShopGroupRowData();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_write', $data);
				break;				
			case 'write':
				$this->setShopDataInsert();
				break;
			case 'apprupdate':	//승인처리 update				
			case 'update':
				$this->setShopDataUpdate();
				break;
			case 'reupdate':
				//신규샵 입력후 입력정보 확인단계에서 재수정
				$this->setShopDataReUpdate();
				break;
			case 'requestappr':
				//신규입력후 확인페이지에서 승인요청
				$this->setApprovalRequest();
				break;
			case 'delete':
				$this->setShopDataDelete();
				break;
			case 'profilefiledelete';
				$this->setProfileFileDelete();
				break;	
			//Shop 대표 아이템 등록
			case 'shopbestitemform':
				$this->getShopBestItemDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/shop/shop_main_bestitem', $data);
				break;
			case 'shopbestitemwrite':
				$this->setShopBestItemDataInsert();
				break;
			case 'shopbestitemcontentdelete':
				$this->setShopBestItemContentDelete();
				break;
			case 'pwreissue': //비밀번호 재발급
				$this->setShopPasswordReissue();
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
		$this->_uriMethod = $this->common->nullCheck($this->_uriMethod, 'str', '');
		
		if (in_array('page', $this->_arrUri))
		{
			$this->_currentPage = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'page')));
		}
		$this->_currentPage = $this->common->nullCheck($this->_currentPage, 'int', 1);
		
		if (in_array('sno', $this->_arrUri))
		{
			$this->_sNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'sno')));
		}
		$this->_sNum = $this->common->nullCheck($this->_sNum, 'int', 0);
		
		if ($this->_uLevelType == 'SHOP')
		{
			//샵관리자로 로그인한 경우
			$this->_sNum = $this->common->getSession('shop_num');
		}		
		
		if (in_array('fno', $this->_arrUri))
		{
			$this->_fNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'fno')));
		}
		$this->_fNum = $this->common->nullCheck($this->_fNum, 'int', 0);		
		
		if (in_array('findex', $this->_arrUri))
		{
			$this->_fIndex = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'findex')));
		}
		$this->_fIndex = $this->common->nullCheck($this->_fIndex, 'int', 0);
		
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
		
		$shopState = $this->input->post_get('shopstate', TRUE);
		if (!empty($shopState)) $this->_currentParam .= '&shopstate='.$shopState;
		
		$shopName = $this->input->post_get('shopname', TRUE);
		if (!empty($shopName)) $this->_currentParam .= '&shopname='.$shopName;
		
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
		
		$shopCode = $this->input->post_get('shopcode', TRUE);
		if (!empty($shopCode)) $this->_currentParam .= '&shopcode='.$shopCode;
		
		$userEmail = $this->input->post_get('useremail', TRUE);
		if (!empty($shopEmail)) $this->_currentParam .= '&useremail='.$userEmail;
		
		$shopEmail = $this->input->post_get('shopemail', TRUE);
		if (!empty($shopEmail)) $this->_currentParam .= '&shopemail='.$shopEmail;
		
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
		
		$sDate = $this->input->post_get('sdate', TRUE);
		$eDate = $this->input->post_get('edate', TRUE);		
		if (!empty($sDate) && !empty($eDate)) $this->_currentParam .= '&sdate='.$sDate.'&edate='.$eDate;
		
		$authorType = $this->input->post_get('authortype', TRUE);
		if (!empty($authorType)) $this->_currentParam .= '&authortype='.$authorType;
		
		$sellerType = $this->input->post_get('sellertype', TRUE);
		if (!empty($sellerType)) $this->_currentParam .= '&sellertype='.$sellerType;
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;		
		$this->_currentUrl .= ($this->_sNum > 0) ? '/sno/'.$this->_sNum : '';
				
		/*
		 * array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentParam' => $this->_currentParam,				
			'searchKey' => $searchKey,
			'searchWord' => $searchWord,
			'shopState' => $shopState,
			'shopName' => $shopName,
			'shopUserName' => $shopUserName,
			'shopCode' => $shopCode,
			'userEmail' => $userEmail,				
			'shopEmail' => $shopEmail,
			'sellerType' => $sellerType,
			'sDate' => $sDate,
			'eDate' => $eDate,
			'authortype' => $authorType,
			'sellerType' => $sellerType,
			'pageMethod' => $this->_uriMethod,
			'sNum' => $this->_sNum,
			'tbl' => $this->_tbl,
			'tblEnc' => $this->common->sqlEncrypt($this->_tbl, $this->_encKey),
			'fileCnt' => $this->_fileCnt,
			'isLogin' => $this->common->getIsLogin(),
			'isAdmin' => $this->_isAdmin,
			'userLevelType' => $this->_uLevelType,				
			'sessionData' => $this->common->getSessionAll()
		);
	}
	
	private function loginCheck()
	{
		if (!$this->common->getIsLogin())
		{
			$this->common->message('로그인후 이용하실 수 있습니다.', '/manage/user_m/login', 'top');
		}

		$this->_uLevelType = $this->common->getSessionUserLevelCodeId();
		$this->_isAdmin = in_array($this->_uLevelType, array('SUPERADMIN', 'ADMIN', 'SHOPADMIN')) ? TRUE : FALSE;
		if (!$this->_isAdmin && $this->_uLevelType != 'SHOP') 
		{
			$this->common->message('관리자만 이용하실 수 있습니다.', '/manage/user_m/login', 'top');
		}
	}	
	
	private function getShopDataList()
	{
		$this->_data = $this->shop_model->getShopDataList($this->_sendData, FALSE);
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
	
	private function getShopRowData()
	{
		$this->_data = $this->shop_model->getShopRowData($this->_sNum);
	}
	
	/**
	 * @method name : getShopPolicyGroupRowData
	 * 샵 정책(환불정책 판단을 위해 불러옴)
	 *
	 */
	private function getShopPolicyGroupRowData()
	{
		$this->_sendData['shopPolicySet'] = $this->shop_model->getShopPolicyRowData($this->_sNum);
		//기준샵에서도 정책을 가져온다
		$this->_sendData['stdShopPolSet'] = $this->shop_model->getStandardShopPolicyRowData();
	}	
	
	/**
	 * @method name : getShopBaseRowData
	 * 샵 기본정보 - SHOP
	 * 
	 */
	private function getShopBaseRowData()
	{
		$this->_data['baseSet'] = $this->shop_model->getShopBaseRowData($this->_sNum);
	}
	
	/**
	 * @method name : getShopPolicyRowData
	 * 샵 정책 - 작성자 샵의 정책 
	 *
	 */
	private function getShopPolicyRowData()
	{
		$this->_data['polSet'] = $this->shop_model->getShopPolicyRowData($this->_sNum);
	}
	
	/**
	 * @method name : getShopPolicyAreaDataList
	 * 샵정책과 관련된 지역 정책 
	 * 
	 */
	private function getShopPolicyAreaDataList()
	{
		$this->_data['polAreaSet'] = $this->shop_model->getShopPolicyAreaDataList($this->_sNum);
	}
	
	/**
	 * @method name : getStandardShopPolicyRowData
	 * 샵 정책 - 기준샵(MALL)의 정책
	 * 
	 */
	private function getStandardShopPolicyRowData()
	{
		$this->_data['stdPolSet'] = $this->shop_model->getStandardShopPolicyRowData();
	}
	
	/**
	 * @method name : getShopInformRowData
	 * 샵 - 부가정보 (사업자정보...)
	 * 
	 */
	private function getShopInformRowData()
	{
		$this->_data['infoSet'] = $this->shop_model->getShopInformRowData($this->_sNum);
	}
	
	/**
	 * @method name : getShopfileDataList
	 * 샵 파일 리스트
	 * 
	 */
	private function getShopfileDataList()
	{
		$this->_data['fileSet'] = $this->shop_model->getShopfileDataList($this->_sNum);
	}
	
	/**
	 * @method name : getShopProfileFileDataList
	 * 샵 프로필 파일 리스트 
	 * 
	 */
	private function getShopProfileFileDataList()
	{
		$this->_data['profileFileSet'] = $this->shop_model->getShopProfileFileDataList($this->_sNum);		
	}
	
	/**
	 * @method name : getShopHistoryDataList
	 * 샵 히스토리 data 리스트
	 *
	 */
	private function getShopHistoryDataList()
	{
		$qData = array(
			'sNum' => $this->_sNum,
			'shopStateCodeNum' => 0, //shopstat 를 지정하는 경우
			'listCount' => 10,	//최근 10개만
			'currentPage' => 1
		);
		
		if ($this->_uriMethod == 'apprview')
		{
			//승인(approval)이전 단계에 해당되는것만
			$qData['shopState'] = 'lowerApproval';
		}
	
		$this->_data['hisSet'] = $this->shop_model->getShopHistoryDataList($qData, FALSE);
	}
	
	/**
	 * @method name : getShopStatsRowData
	 * Craft Shop 통계
	 * 
	 */
	private function getShopStatsRowData()
	{
		$this->_data['shopStatsSet'] = $this->shop_model->getShopStatsRowData($this->_sNum);
	}
	
	/**
	 * @method name : getShopGroupRowData
	 * SHOP 과 관련된 data의 group별 data
	 * 
	 */
	private function getShopGroupRowData()
	{
		$this->getShopBaseRowData();
		$this->getShopPolicyRowData();
		$this->getShopPolicyAreaDataList();
		$this->getShopInformRowData();
		$this->getShopfileDataList();
		$this->getShopProfileFileDataList();
		$this->getShopHistoryDataList();
		$this->getShopStatsRowData();
	}
	
	/**
	 * @method name : getGroupCodeDataList
	 * 관계되는 모든 CODE Data List 
	 * 
	 */
	private function getGroupCodeDataList()
	{
		$this->_data['deliCdSet'] = $this->common->getCodeListByGroup('DELIVERY');
		$this->_data['calBankCdSet'] = $this->common->getCodeListByGroup('BANK');
		$this->_data['sellTyCdSet'] = $this->common->getCodeListByGroup('SELLERTYPE');
		$this->_data['spStatCdSet'] = $this->common->getCodeListByGroup('SHOPSTATE');
		$this->_data['itemStCdSet'] = $this->common->getCodeListByGroup('ITEMSTATE');
		$this->_data['deliPlCdSet'] = $this->common->getCodeListByGroup('DELIVERYPOLICY');
		$this->_data['refPlCdSet'] = $this->common->getCodeListByGroup('REFUNDPOLICY');
		$this->_data['calCdSet'] = $this->common->getCodeListByGroup('CALCYCLE');
	}
	
	/**
	 * @method name : setShopDataInsert
	 * 신규 샵 등록
	 * 사업자 정보, 정책관련 내용이 없더라도 insert (update 편의를 위해)
	 * 
	 */
	private function setShopDataInsert()
	{
		$userEmail = $this->input->post_get('user_email', TRUE);
		$userEmailEnc = $this->common->sqlEncrypt($userEmail, $this->_encKey);
		$shopUserName = $this->input->post_get('shopuser_name', TRUE);
		$shopEmailEnc = $this->common->sqlEncrypt($this->input->post_get('shop_email', TRUE), $this->_encKey);		
		$shopMobileEnc = $this->common->sqlEncrypt($this->input->post_get('shop_mobile1', TRUE).'-'.$this->input->post_get('shop_mobile2', TRUE).'-'.$this->input->post_get('shop_mobile3', TRUE), $this->_encKey);
		$shopTel = $this->input->post_get('shop_tel1', TRUE).'-'.$this->input->post_get('shop_tel2', TRUE).'-'.$this->input->post_get('shop_tel3', TRUE);
		$shopTelEnc = $this->common->sqlEncrypt($shopTel, $this->_encKey);		
		$uLevelCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'SHOP');
		$dummyUser = $this->common->getUserInfo('dummy');	//승인관리자 지정전 임시 회원고유번호 
		$userExist = $this->common->getUserInfo('email', $userEmail);
		if ($userExist)
		{
			$this->common->message('동일한 이메일 주소 가입내역이 있습니다.\\n확인후 다시 시도해 주시기 바랍니다.', '-', '');			
		}
		
		//회원가입 정보
		$insUserData = array(
			'USER_NAME' => $shopUserName,
			'USER_EMAIL'=> $userEmailEnc,
			'USER_PASS' => sha1('1111'),	//sha1('0123456789#'),	//임시비밀번호 부여
			'USER_MOBILE' => $shopMobileEnc,
			'ULEVELCODE_NUM' => $uLevelCodeNum,
			'INFLOW_ROUTE' => ($this->common->getMobileCheck()) ? 'M' : 'W',
			'SNSCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SNS', 'NONE'), //USER 히스토리 반영시 필요
			'REMOTEIP' => $this->input->ip_address(),
			'USTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'OK') //USER 히스토리 반영시 필요
		);

		$userResultNum = $this->user_model->setUserDataInsert($insUserData);
		if ($userResultNum == 0)
		{
			$this->common->message('회원생성 오류로 신규샵 생성이 취소 되었습니다.', '-', '');
		}		
		
		//샵기본 정보
		$insData = array(
			'SHOP_NAME' => $this->input->post_get('shop_name', TRUE),				
			'SHOPUSER_NAME' => $shopUserName,
			'SHOP_EMAIL' => $shopEmailEnc,
			'SHOP_MOBILE' => $shopMobileEnc,
			'SHOP_TEL' => $shopTelEnc,				
			'SELLERTYPECODE_NUM' => $this->input->post_get('seller_type', TRUE),				
			'SHOPSTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SHOPSTATE', 'RECEIVE'),
			'APPROVALUSER_NUM' => $dummyUser['NUM'], //임시부여
			'MANAGEUSER_NUM' => $this->input->post_get('manager_change_uno', TRUE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		//사업자 정보
		$coNum = $this->input->post_get('co_num1', TRUE).'-'.$this->input->post_get('co_num2', TRUE).'-'.$this->input->post_get('co_num3', TRUE);
		$coEmailEnc = $this->common->sqlEncrypt($this->input->post_get('co_ceoemail', TRUE), $this->_encKey);
		$coTel = $this->input->post_get('co_tel1', TRUE).'-'.$this->input->post_get('co_tel2', TRUE).'-'.$this->input->post_get('co_tel3', TRUE);
		$coTelEnc = $this->common->sqlEncrypt($coTel, $this->_encKey);
		$zipEnc = $this->common->sqlEncrypt($this->input->post_get('co_zip', TRUE), $this->_encKey);
		$addr1Enc = $this->common->sqlEncrypt($this->input->post_get('co_addr1', TRUE), $this->_encKey);
		$addr2Enc = $this->common->sqlEncrypt($this->input->post_get('co_addr2', TRUE), $this->_encKey);
		$addrJibunEnc = $this->common->sqlEncrypt($this->input->post_get('co_addr_jibun', TRUE), $this->_encKey);
		
		$insInfoData = array(
			'CO_NUM' => $coNum,
			'CO_NAME' => $this->input->post_get('co_name', TRUE),
			'CO_CEONAME' => $this->input->post_get('co_name', TRUE),
			'CO_CEOEMAIL' => $coEmailEnc,
			'CO_TEL' => $coTelEnc,
			'CO_BIZTYPE' => $this->input->post_get('co_biztype', TRUE),
			'CO_BIZEVENT' => $this->input->post_get('co_bizevent', TRUE),
			'CO_ZIP' => $zipEnc,
			'CO_ADDR1' => $addr1Enc,
			'CO_ADDR2' => $addr2Enc,
			'CO_ADDR_JIBUN' => $addrJibunEnc,				
			'CO_MAILORDER_NO' => $this->input->post_get('co_mailorderno', TRUE)
		);
		
		//정책관련 - 빈내용으로 반드시 insert (update편의를 위해)
		$insPlData = array(
			'DELIVERYCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('DELIVERY', 'NONE'),
			'REFDELIVERYCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('DELIVERY', 'NONE'),
			'DELIVPOLICYCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('DELIVERYPOLICY', 'NONE'),
			'REFPOLICYCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('REFUNDPOLICY', 'NONE'),
			'CALCYCLECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('CALCYCLE', 'NONE'),
			'CALBANKCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('BANK', 'NONE')
		);		
		
		$result = $this->shop_model->setShopDataInsert($insData, $insInfoData, $insPlData, $userResultNum, TRUE);
		
		if ($result > 0)
		{
			// yong mod - sendbird 신규 사용자 전송
			// The data to send to the API

			log_message('debug', 'Send To SendBird');

			$postData = array(
			    'auth' => '09382cc3b477fc6e62e1e7ab1fe565232f680054',
			    'id' => $userEmail,
			    'nickname' => $this->input->post_get('shop_name', TRUE),
			    'image_url' => 'http://api.circusflag.com/images/app/main/default_profile.png'
			);

			log_message('debug', '[CircusLog] Shop Email : ' . $userEmail);			
			log_message('debug', '[CircusLog] Shop Name : ' . $this->input->post_get('shop_name', TRUE));			
			// shopUserName
			log_message('debug', '[CircusLog] Shop User Name : ' . $shopUserName);			

			// Setup cURL
			$ch = curl_init('https://api.sendbird.com/user/create');
			curl_setopt_array($ch, array(
			    CURLOPT_POST => TRUE,
			    CURLOPT_RETURNTRANSFER => TRUE,
			    CURLOPT_HTTPHEADER => array(
			        'Content-Type: application/json'
			    ),
			    CURLOPT_POSTFIELDS => json_encode($postData)
			));

			// Send the request
			$response = curl_exec($ch);
			log_message('debug', '[CircusLog] Response From : ' . $response);

			// Check for errors
			if($response === FALSE){
			    die(curl_error($ch));
			}

			// yong mod end

			$this->common->message('신규샵이 생성 되었습니다.', '/manage/shop_m/writeconfirm/sno/'.$result, 'top');
		}
		else 
		{
			$this->common->message('오류로 신규샵 생성이 취소 되었습니다.', '-', '');
		}
	}
	
	/**
	 * @method name : setShopDataUpdate
	 * 샵 정보 update
	 * 
	 */
	private function setShopDataUpdate()
	{
		$emailEnc = $this->common->sqlEncrypt($this->input->post_get('shop_email', TRUE), $this->_encKey);
		$shopMobile = $this->input->post_get('shop_mobile1', TRUE).'-'.$this->input->post_get('shop_mobile2', TRUE).'-'.$this->input->post_get('shop_mobile3', TRUE);
		$shopMobileEnc = $this->common->sqlEncrypt($shopMobile, $this->_encKey);
		$shopTel = $this->input->post_get('shop_tel1', TRUE).'-'.$this->input->post_get('shop_tel2', TRUE).'-'.$this->input->post_get('shop_tel3', TRUE);
		$shopTelEnc = $this->common->sqlEncrypt($shopTel, $this->_encKey);		
		$shopState = $this->input->post_get('shop_state', TRUE);
		$shopStateOrg = $this->input->post_get('shop_state_org', TRUE);
		$managerChangeUnum = $this->input->post_get('manager_change_uno', TRUE);
		$managerChangeYn = $this->input->post_get('manager_change_yn', TRUE);
		$shopStateMemo = $this->input->post_get('shop_state_memo', TRUE);
		$shopStateMemoOrg = $this->input->post_get('shop_state_memo_org', TRUE);
		$managerUserNum = ($managerChangeYn == 'Y') ? $managerChangeUnum : $this->input->post_get('manager_no_org', TRUE);
		$contractSdate = $this->input->post_get('s_date', TRUE);
		$contractEdate = $this->input->post_get('e_date', TRUE);
		$userPass = $this->input->post_get('passwd1', TRUE);
		$userPass = (!empty($userPass)) ? sha1($userPass) : '';
		
		//샵기본 정보
		$upData = array(
			'SHOP_NAME' => $this->input->post_get('shop_name', TRUE),
			'SHOPUSER_NAME' => $this->input->post_get('shopuser_name', TRUE),	
			'SHOP_EMAIL' => $emailEnc,
			'SHOP_MOBILE' => $shopMobileEnc,
			'SHOP_TEL' => $shopTelEnc,				
			'MANAGEUSER_NUM' => $managerUserNum, 
			'SHOPSTATECODE_NUM' => (empty($shopState)) ? $shopStateOrg : $shopState,
			'SHOPSTATE_MEMO' => $shopStateMemo,
			'PROFILE_CONTENT' => $this->input->post_get('profile_content', TRUE),	
			'PROFILE_DATE' => $this->input->post_get('profile_date', TRUE),
			'TODAYAUTHOR_YN' => ($this->input->post_get('author_type', TRUE) == 'today') ? 'Y' : 'N',
			'POPAUTHOR_YN' => ($this->input->post_get('author_type', TRUE) == 'pop') ? 'Y' : 'N',
			'UPDATE_DATE' => date('Y-m-d H:i:s'),
			'USER_PASS' => $userPass
		);
		
		if ($shopStateOrg != $shopState)
		{
			$apprFirstReqDate = $this->input->post_get('appr_firstreq_date', TRUE);
			if ($shopState == 3020)
			{
				$upData['APPROVAL_REQ_DATE'] = date('Y-m-d H:i:s'); //승인요청 일자 update
				
				if (empty($apprFirstReqDate))
				{
					//최초 승인 요청 일자 update
					$upData['APPROVAL_FIRSTREQ_DATE'] = date('Y-m-d H:i:s');					
				}
			}
			
			if ($shopState == 3060)
			{
				$upData = $upData + array(
					'APPROVALUSER_NUM' => $this->common->getSession('user_num'), //승인 처리자
					'APPROVAL_DATE' => date('Y-m-d H:i:s') //최종 승인처리 일자 update
				);
			}

			//상태가 변경되는 경우 변경일자 업데이트
			$upData['SHOPSTATE_UPDATE_DATE'] = date('Y-m-d H:i:s');
		}
		
		if (!empty($contractSdate))
		{
			//계약시작일
			$upData['CONTRACT_START_DATE'] = $contractSdate;			
		}
		
		if (!empty($contractEdate))
		{
			//계약종료일
			$upData['CONTRACT_END_DATE'] = $contractEdate;
		}
		
		if (!empty($contractSdate) && !empty($contractEdate))
		{
			//계약시작일 과 계약종료일이 모두 설정되어있으면 계약된것으로 간주 
			$upData['CONTRACT_YN'] = 'Y';
		}
		else 
		{
			//그외에는 계약해지 또는 무계약 상태
			$upData['CONTRACT_YN'] = 'N';			
		}
		
		//사업자 정보
		$coNum = $this->input->post_get('co_num1', TRUE).'-'.$this->input->post_get('co_num2', TRUE).'-'.$this->input->post_get('co_num3', TRUE);
		$coEmailEnc = $this->common->sqlEncrypt($this->input->post_get('co_ceoemail', TRUE), $this->_encKey);		
		$coTel = $this->input->post_get('co_tel1', TRUE).'-'.$this->input->post_get('co_tel2', TRUE).'-'.$this->input->post_get('co_tel3', TRUE);
		$coTelEnc = $this->common->sqlEncrypt($coTel, $this->_encKey);		
		$zipEnc = $this->common->sqlEncrypt($this->input->post_get('co_zip', TRUE), $this->_encKey);
		$addr1Enc = $this->common->sqlEncrypt($this->input->post_get('co_addr1', TRUE), $this->_encKey);
		$addr2Enc = $this->common->sqlEncrypt($this->input->post_get('co_addr2', TRUE), $this->_encKey);
		$addrJibunEnc = $this->common->sqlEncrypt($this->input->post_get('co_addr_jibun', TRUE), $this->_encKey);
		
		$upInfoData = array(
			'CO_NUM' => $coNum,
			'CO_NAME' => $this->input->post_get('co_name', TRUE),
			'CO_CEONAME' => $this->input->post_get('co_name', TRUE),
			'CO_CEOEMAIL' => $coEmailEnc,
			'CO_TEL' => $coTelEnc,
			'CO_BIZTYPE' => $this->input->post_get('co_biztype', TRUE),
			'CO_BIZEVENT' => $this->input->post_get('co_bizevent', TRUE),
			'CO_ZIP' => $zipEnc,
			'CO_ADDR1' => $addr1Enc,
			'CO_ADDR2' => $addr2Enc,
			'CO_ADDR_JIBUN' => $addrJibunEnc,
			'CO_MAILORDER_NO' => $this->input->post_get('co_mailorderno', TRUE)
		);
		
		if ($this->_uriMethod == 'update')
		{
			//승인쪽에서 update
			//정책관련
			$deliveryCodeNum = $this->input->post_get('delivery_code_num', TRUE);
			$refDeliveryCodeNum = $this->input->post_get('refund_delivery_code_num', TRUE);
			$deliveryPolCodeNum = $this->input->post_get('delivery_policy', TRUE);
			$refTel = $this->input->post_get('refund_tel1', TRUE).'-'.$this->input->post_get('refund_tel2', TRUE).'-'.$this->input->post_get('refund_tel3', TRUE);
			$refTelEnc = $this->common->sqlEncrypt($refTel, $this->_encKey);
			$refZipEnc = $this->common->sqlEncrypt($this->input->post_get('refund_zip', TRUE), $this->_encKey);
			$refAddr1Enc = $this->common->sqlEncrypt($this->input->post_get('refund_addr1', TRUE), $this->_encKey);
			$refAddr2Enc = $this->common->sqlEncrypt($this->input->post_get('refund_addr2', TRUE), $this->_encKey);
			$refAddrJibunEnc = $this->common->sqlEncrypt($this->input->post_get('refund_addr_jibun', TRUE), $this->_encKey);			
			$refundPolicy = $this->input->post_get('refund_policy', TRUE);
			$refundContent = $this->input->post_get('ref_content', TRUE);
			$calBank = $this->input->post_get('cal_bank', TRUE);
			$cal_cycle = $this->input->post_get('cal_cycle', TRUE);
			$chargeType = $this->input->post_get('charge_type', TRUE);
			$fixedCharge = $this->input->post_get('fixed_charge', TRUE);
			
			$upPlData = array(
				'DELIVERYCODE_NUM' => (!empty($deliveryCodeNum)) ? $deliveryCodeNum : $this->common->getCodeNumByCodeGrpNCodeId('DELIVERY', 'NONE'),
				'REFDELIVERYCODE_NUM' => (!empty($refDeliveryCodeNum)) ? $refDeliveryCodeNum : $this->common->getCodeNumByCodeGrpNCodeId('DELIVERY', 'NONE'),
				'DELIVPOLICYCODE_NUM' => (!empty($deliveryPolCodeNum)) ? $deliveryPolCodeNum : $this->common->getCodeNumByCodeGrpNCodeId('DELIVERYPOLICY', 'NONE'),
				'DELIVPOLICY_PRICE' => $this->input->post_get('delivery_policy_price', TRUE),
				'DELIVERY_PRICE' => $this->input->post_get('delivery_price', TRUE),
				'ISLANDDELIVERY_YN' => $this->input->post_get('islanddelivery_yn', TRUE),
				//'ISLANDADD_YN' => $this->input->post_get('islandadd_yn', TRUE),
				//'ISLANDADD_PRICE' => 0, //$this->input->post_get('islandadd_addprice', TRUE),	//항목삭제 2016.01
				//'AREADELIVERY_YN' => 'N', //$this->input->post_get('area_delivery_yn', TRUE),	//항목삭제 2016.01
				'REFUND_TEL' => $refTelEnc,
				'REFUND_ZIP' => $refZipEnc,
				'REFUND_ADDR1' => $refAddr1Enc,
				'REFUND_ADDR2' => $refAddr2Enc,
				'REFUND_ADDR_JIBUN' => $refAddrJibunEnc,					
				'REFPOLICYCODE_NUM' => (!empty($refundPolicy)) ? $refundPolicy : $this->common->getCodeNumByCodeGrpNCodeId('REFUNDPOLICY', 'NONE'),
				'REFPOLICY_CONTENT' => ($refundPolicy == $this->common->getCodeNumByCodeGrpNCodeId('REFUNDPOLICY', 'SHOP')) ? $refundContent : '',	//Shop 자체 정책 사용인 경우에만
				'CALCYCLECODE_NUM' => (!empty($cal_cycle)) ? $cal_cycle : $this->common->getCodeNumByCodeGrpNCodeId('CALCYCLE', 'NONE'),
				'CALBANKCODE_NUM' => (!empty($calBank)) ? $calBank : $this->common->getCodeNumByCodeGrpNCodeId('BANK', 'NONE'),
				'CAL_NAME' => $this->input->post_get('cal_name', TRUE),
				'CAL_ACCOUNT' => $this->input->post_get('cal_account', TRUE)
			);	
			
			if ($chargeType == 'F')
			{
				$upPlData['CHARGE_TYPE'] = $chargeType; //수수료 타입
				$upPlData['FIXED_CHARGE'] = $fixedCharge; //고정입점비
			}
		}
		else if ($this->_uriMethod == 'apprupdate')
		{
			//승인쪽에서 update
			$upPlData = array();
		}
		
		//히스토리
		$insHisData = array(
			'SHOP_NUM' => $this->_sNum,
			'SHOPSTATECODE_NUM' => (empty($shopState)) ? $this->input->post_get('shop_state_org', TRUE) : $shopState,
			'ADMINUSER_NUM' => $this->common->getSession('user_num')
		);
		
		if ($this->_uriMethod == 'update')
		{
			//상태간략 메모(SHOP)내용을 히스토리에 기록(SHOP_HISTORY)
			$insHisData['CONTENT'] = ($shopStateMemo != $shopStateMemoOrg) ? $shopStateMemo : 'SHOP 정보 업데이트';
		}
		else if ($this->_uriMethod == 'apprupdate')
		{
			//히스토리에 기록(SHOP_HISTORY)
			$insHisData['CONTENT'] = $this->input->post_get('shop_history_content', TRUE); //보류/거부 사유
		}
		
		$result = $this->shop_model->setShopDataUpdate($this->_sNum, $upData, $upInfoData, $upPlData, $insHisData, TRUE);
		
		if ($result > 0)
		{
			if ($this->_uriMethod == 'update')
			{
				$listUrl = '/manage/shop_m/list/sno/'.$this->_sNum;
			}
			else if ($this->_uriMethod == 'apprupdate')
			{
				$listUrl = '/manage/shop_m/apprlist/sno/'.$this->_sNum;						
			}
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';			
			
			$viewUrl = '/manage/shop_m/view/sno/'.$this->_sNum;
			$viewUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$viewUrl .= (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';
			
			$this->common->message('수정 되었습니다.', $viewUrl, 'top');
		}
	}
	
	/**
	 * @method name : setShopDataReUpdate
	 * 신규샵 입력후 입력정보 확인단계에서 재수정
	 * 히스토리 기록(X) 
	 * 
	 */
	private function setShopDataReUpdate()
	{
		$shopUserName = $this->input->post_get('shopuser_name', TRUE);
		$shopEmailEnc = $this->common->sqlEncrypt($this->input->post_get('shop_email', TRUE), $this->_encKey);
		$shopMobileEnc = $this->common->sqlEncrypt($this->input->post_get('shop_mobile1', TRUE).'-'.$this->input->post_get('shop_mobile2', TRUE).'-'.$this->input->post_get('shop_mobile3', TRUE), $this->_encKey);

		//샵기본 정보
		$upData = array(
			'SHOP_NAME' => $this->input->post_get('shop_name', TRUE),
			'SHOPUSER_NAME' => $shopUserName,
			'SHOP_EMAIL' => $shopEmailEnc,
			'SHOP_MOBILE' => $shopMobileEnc,
			'SELLERTYPECODE_NUM' => $this->input->post_get('seller_type', TRUE),
			'MANAGEUSER_NUM' => $this->input->post_get('manager_change_uno', TRUE)
		);
		
		//사업자 정보
		$coNum = $this->input->post_get('co_num1', TRUE).'-'.$this->input->post_get('co_num2', TRUE).'-'.$this->input->post_get('co_num3', TRUE);
		$coEmailEnc = $this->common->sqlEncrypt($this->input->post_get('co_ceoemail', TRUE), $this->_encKey);
		$coTel = $this->input->post_get('co_tel1', TRUE).'-'.$this->input->post_get('co_tel2', TRUE).'-'.$this->input->post_get('co_tel3', TRUE);
		$coTelEnc = $this->common->sqlEncrypt($coTel, $this->_encKey);
		$zipEnc = $this->common->sqlEncrypt($this->input->post_get('co_zip', TRUE), $this->_encKey);
		$addr1Enc = $this->common->sqlEncrypt($this->input->post_get('co_addr1', TRUE), $this->_encKey);
		$addr2Enc = $this->common->sqlEncrypt($this->input->post_get('co_addr2', TRUE), $this->_encKey);
		
		$upInfoData = array(
			'CO_NUM' => $coNum,
			'CO_NAME' => $this->input->post_get('co_name', TRUE),
			'CO_CEONAME' => $this->input->post_get('co_name', TRUE),
			'CO_CEOEMAIL' => $coEmailEnc,
			'CO_TEL' => $coTelEnc,
			'CO_BIZTYPE' => $this->input->post_get('co_biztype', TRUE),
			'CO_BIZEVENT' => $this->input->post_get('co_bizevent', TRUE),
			'CO_ZIP' => $zipEnc,
			'CO_ADDR1' => $addr1Enc,
			'CO_ADDR2' => $addr2Enc,
			'CO_MAILORDER_NO' => $this->input->post_get('co_mailorderno', TRUE)
		);
		
		$result = $this->shop_model->setShopDataReUpdate($this->_sNum, $upData, $upInfoData);
		
		$this->common->message('수정 되었습니다.', '/manage/shop_m/writeconfirm/sno/'.$this->_sNum, 'top');
	}
	
	/**
	 * @method name : setApprovalRequest
	 * 신규입력후 확인페이지에서 승인요청 
	 * 
	 */
	private function setApprovalRequest()
	{
		$this->getShopBaseRowData();
		$managerUserNum = $this->_data['baseSet']['MANAGEUSER_NUM'];
		
		$insHisData = array(
			'SHOP_NUM' => $this->_sNum,
			'SHOPSTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('SHOPSTATE', 'APP_REQUEST'),
			'ADMINUSER_NUM' => $this->common->getSession('user_num'),
			'CONTENT' => $this->input->post_get('shop_history_content', TRUE)
		);	

		// yong mod
		// sendbird 로직 넣기

		
		$result = $this->shop_model->setApprovalRequest($insHisData);
		if ($result > 0)
		{
			$listUrl = '/manage/shop_m/apprlist';			
			$this->common->message('승인을 요청 하였습니다.', $listUrl, 'top');
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
		$this->shop_model->setProfileFileDelete($this->_sNum, $this->_fNum, $this->_fIndex);
	
		$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		//$this->common->message('삭제 되었습니다.', 'reload', 'parent');
	}	
	
	/**
	 * @method name : getShopBestItemDataList
	 * 샵 대표 Item 내용
	 *
	 */
	private function getShopBestItemDataList()
	{
		$this->_data = $this->shop_model->getShopBestItemDataList($this->_sNum, FALSE);
	}
	
	/**
	 * @method name : setShopBestItemDataInsert
	 * 샵 대표 Item 구성
	 * 한개의 data만 유지
	 *
	 */
	private function setShopBestItemDataInsert()
	{
		$insData = $this->input->post_get('bestmn', FALSE);
		$result = $this->shop_model->setShopBestItemDataInsert($this->_sNum, $insData);
	
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/shop_m/shopbestitemform/sno/'.$this->_sNum, 'top');
		}
	}
	
	/**
	 * @method name : setShopBestItemContentDelete
	 * 샵 대표 Item 컨텐츠 삭제
	 *
	 */
	private function setShopBestItemContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
		$itemNum = $this->input->post_get('itemno', TRUE);
		$result = $this->shop_model->setShopBestItemContentDelete(
			$this->_sNum,
			$contentNum,
			$contentOrder,
			$itemNum
		);
	
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}	
	
	/**
	 * @method name : setUserPasswordReissue
	 * 임시 비밀번호 발송(샵관리자)
	 *
	 */
	private function setShopPasswordReissue()
	{
		$reqType = $this->input->post_get('reqtype', TRUE); //발송시 대상(메일, 휴대폰)
		$email = $this->input->post_get('reqpw_email', TRUE);
		$emailEnc = $this->common->sqlEncrypt($email, $this->_encKey);
		$mobile = $this->input->post_get('reqpw_mobile', TRUE);
		$mobileEnc = $this->common->sqlEncrypt($mobile, $this->_encKey);		
		
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
					'toEmail' => 'churk@pixelize.co.kr',//$userInfo['USER_EMAIL_DEC'],
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
					'phoneNum' => $mobile,
					'smsContent' => '[Circus]비밀번호는 '.$result. '입니다.',
					'smsSubject' => '', //단문인 경우 필요없음
					'smsType' => 'S'
				);
				$this->common->smsSend($qData);
			}
				
			$msg = ($reqType == 'email') ? $email : $mobile;
			$msg .= "(으)로 임시 비밀번호가 발송되었습니다.";
			$this->common->message($msg, '-', '');
		}
		else
		{
			$msg = "회원정보를 찾을 수 없습니다.\\n다시 확인해 보시기 바랍니다.";
			$this->common->message($msg, '-', '');
		}
	}	
}