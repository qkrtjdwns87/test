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
class Messages extends REST_Controller {
	
	protected $_method = '';
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;	

	/**
	 * @var integer MESSAGE 고유번호
	 */
	protected $_msgNum = 0;
	
	/**
	 * @var integer 대화리스트 상한선
	 * 페이징 리스트시 신규생성대화와의 중복 방지
	 */
	protected $_maxMsgNum = 0;
	
	/**
	 * @var integer 연결되는 메시지 그룹(원본번호)
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 */
	protected $_msgGrpNum = 0;
	
	/**
	 * @var integer MESSAGE TYPE (1:1, 일반메시지)
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 */
	protected $_msgType = 0;
	
	/**
	 * @var string listview 에서 리스팅 해줄 날짜
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 */
	protected $_msgToDate = '';
	
	/**
	 * @var integer ORDERS 고유번호
	 */
	protected $_ordNum = 0;	
	
	/**
	 * @var integer SHOP 고유번호(샵관리자 로그인시 session으로 _sNum 고정)
	 */
	protected $_sNum = 0;
	
	/**
	 * @var integer SHOPITEM 고유번호
	 */
	protected $_siNum = 0;	
	
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

        $this->load->helper(array('url'));
        $this->load->model(array('message_model', 'item_model', 'order_model'));
        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['message_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['message_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['message_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function message_get() {exit('No access allowed');}
    public function message_put() {exit('No access allowed');}
    public function message_delete() {exit('No access allowed');}
    //POST방식만 사용
    public function message_post()
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
    	$this->_isAppAuth = $this->common->getAppAuthCheck($this->_authkey, $this->_deviceId, $this->_pushId); //deviceid, pushid 유효성 검증
    	$this->_msgNum = $this->input->post('msgno', TRUE);
    	$this->_maxMsgNum = $this->input->post('maxmsgno', TRUE);
    	$this->_msgGrpNum = $this->input->post('msggrpno', TRUE);
    	$this->_msgType = $this->input->post('msgtype', TRUE);
    	$this->_msgToDate = $this->input->post('msgtodate', TRUE);
    	$this->_sNum = $this->input->post('sno', TRUE);
    	$this->_siNum = $this->input->post('sino', TRUE);
    	$this->_ordNum = $this->input->post('ordno', TRUE);
    	
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
    	if (!$this->_isAppAuth)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    	
    	switch($this->_method)
    	{
    		case 'new_user_shop':
    		case 'new_user_shopq':    			
    		case 'new_user_mall':
    		case 'new_user_mallq':
    		case 'new_shop_mall':    			
    		case 'new_shop_mallq':    			
    			$this->_data = $this->setNewMessageInit();
    			break;    			
    		case 'msglist': //대화 리스트
    			$this->_data = $this->getMessageDataList();
    			break;
    		case 'targetlist': //나의 발송대상자 명단
    			$this->_data = $this->getMessageTargetDataList();
    			break;    			
    		case 'write_user_to_shop':
    		case 'write_user_to_mall':    			
   			case 'write_shop_to_user':
   			case 'write_shop_to_mall':
   			case 'write_mall_to_user':   				
   			case 'write_mall_to_shop':
   				$this->_data = $this->getMessageDataInsert();
   				break;  
   			case 'listviewum': //메시지 그룹 리스트(회원과써커스간)
   			case 'listviewus': //메시지 그룹 리스트(회원과샵간)   				
   				$this->_data = $this->getMessageDataViewList();
   				break;
    	}
    }  
    
    /**
     * @method name : setNewMessageInit
     * 초기 메시지창을 띄울때
     * 아이템 상세에서 대화메시지 시작시 아이템정보에 대한 메시지 생성필요
     * 앱으로 부터 꼭 받아야 할 파라메터
     * method
     * ordno (주문관련 대화인 경우)
     * sno (샵 또는 아이템관련인 경우)
     * sino (아이템관련인 경우)
     * 
     * 
     * @return unknown
     */
    private function setNewMessageInit()
    {
     	$this->_currentPage = ($this->common->nullCheck($this->_currentPage, 'int', 0) == 0) ? 1 : $this->_currentPage;
    	if ($this->_method == 'new_user_shop' || $this->_method == 'new_user_shopq')
    	{
    		//유저 샵간의 신규메시지 생성시(제품상세에서 메시지 작성등...)
    		//해당 아이템으로 대화한 이력이 있는지 확인
    		//있는 경우 해당 그룹번호 이어서 작성
    		//없는 경우 아이템 대화 한건 자동 생성
    		$msgType = 17040; //($this->_method == 'new_user_shop') ? 17160 : 17040; //17040 회원,샵간의 문의성 대화
    		$qData = array(
    			'pageMethod' => $this->_method,
    			'uNum' => $this->_authkey,
    			'ordNum' => $this->_ordNum,
    			'sNum' => $this->_sNum,
    			'siNum' => $this->_siNum,
    			'listCount' => $this->_listCount,
    			'currentPage' => $this->_currentPage,    				
   				'ordData' => ($this->_ordNum > 0) ? $this->order_model->getOrderBaseRowData($this->_ordNum) : array(), //item대화생성을 위해    				
    			'itemData' => ($this->_siNum > 0) ? $this->item_model->getItemBaseRowData($this->_siNum, $this->_authkey) : array(), //item대화생성을 위해
    			'msgType' => $msgType,
    			'isApp' => TRUE
    		);
    		
    		$result = $this->message_model->setNewMessageInit($qData);
    	}
    	else if ($this->_method == 'new_user_mall' || $this->_method == 'new_user_mallq')
    	{    
    		$msgType = 17030; //($this->_method == 'new_user_mall') ? 17150 : 17030; //17030 회원,몰간의 문의성 대화
    		$qData = array(
    			'pageMethod' => $this->_method,
    			'uNum' => $this->_authkey,
    			'listCount' => $this->_listCount,
    			'currentPage' => $this->_currentPage,    				
    			'msgType' => $msgType,
    			'isApp' => TRUE
    		);
    		
    		$result = $this->message_model->setNewMessageInit($qData);
    	}
    	else if ($this->_method == 'new_shop_mall' || $this->_method == 'new_shop_mallq')
    	{
    		$msgType = 17020; //($this->_method == 'new_shop_mall') ? 17140 : 17020; //17020 샵,몰간의 문의성 대화
    		$qData = array(
    			'pageMethod' => $this->_method,
    			'uNum' => $this->_authkey,
    			'sNum' => $this->_sNum,
    			'listCount' => $this->_listCount,
    			'currentPage' => $this->_currentPage,    				
    			'msgType' => $msgType,
    			'isApp' => TRUE
    		);
    		
    		$result = $this->message_model->setNewMessageInit($qData);
    	}
    	
    	//데이타 조합
    	//$data['msgDateSet'] = $result['msgDateSet']; //일자정보
    	$data['msglistSet'] =  $result['msgDataSet'];
    	unset($result['msgDateSet']);
    	unset($result['msgDataSet']);
    	//$data['msgSet'] = $result;
    	$data['msgGrpNo'] = $result['msgGrpNum'];
    	$data['maxMsgNo'] = $result['maxMsgNum'];
    	
    	return $data;
    }
    
    /**
     * @method name : getMessageDataList
     * 일자별 리스트 
     * 앱으로 부터 꼭 받아야 할 파라메터
     * msgdate
     * msggrpno
     * 
     * 
     * 
     * 
     * @return unknown
     */
    private function getMessageDataList()
    {
    	$msgToDate = $this->input->post('msgdate', TRUE);
    	
    	/*
    	$msgSet = $this->input->post('msgset', TRUE);
    	$arrMsgSet = json_decode($msgSet, TRUE);
    	$msgGrpNum = ($arrMsgSet) ? $arrMsgSet['msgGrpNum'] : 0;
    	
    	if (empty($msgSet) || !isset($msgSet))
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'msgset not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	} 
    	
    	if (empty($msgToDate) || !isset($msgToDate))
    	{
    		$this->response([
    				'status' => FALSE,
    				'message' => 'msgdate not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	*/
    	
    	if (empty($this->_msgGrpNum) || $this->_msgGrpNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'msgGrpNo not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	if (empty($this->_maxMsgNum) || $this->_maxMsgNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'maxMsgNo not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	
    	/* 일자별 목록 에서 페이징으로 기획이 변경됨
    	$result['msgDateSet'] = $this->message_model->getListViewMessageDate(
   			array(
				'msgToDate' => $msgToDate,
				'msgGrpNum' => $this->_msgGrpNum
   			)    			
    	);
    	*/
    	
    	$qData = array(
   			'maxMsgNum' => $this->_maxMsgNum,
    		'msgGrpNum' => $this->_msgGrpNum,
    		'uNum' => $this->_authkey,
    		'listCount' => $this->_listCount, //1000, //사실상 일자별 무한대 $this->_listCount,
    		'currentPage' => $this->_currentPage
    	);

    	$result['msglistSet'] = $this->message_model->getMessageDataList($qData);

    	//메시지 읽음 처리
    	$msgNum = '';
    	foreach ($result['msglistSet']['recordSet'] as $rs)
    	{
    		$msgNum .= $rs['NUM'].',';
    	}
    	
    	if (!empty($msgNum))
    	{
    		$msgNum = substr($msgNum, 0, -1);
    		$this->message_model->setReadMessage($msgNum, $this->_authkey);
    	}
    	
    	return $result;
    }
    
    /**
     * @method name : getMessageDataInsert
     * 메세지 저장 
     * 앱으로 부터 꼭 받아야 할 파라메터
     * targetno,
     * msg_content
     * method
     * msggrpno
     * 
     * 
     * 
     * @return unknown[]
     */
    private function getMessageDataInsert()
    {
    	$targetNum = $this->input->post_get('targetno', TRUE); //TOUSER_NUM or SHOP_NUM, SHOPITEM_NUM
    	$msgContent = $this->input->post_get('msg_content', FALSE);
    	
    	/*
    	$msgSet = $this->input->post('msgset', TRUE);
    	$arrMsgSet = json_decode($msgSet, TRUE);
    	$msgGrpNum = ($arrMsgSet) ? $arrMsgSet['msgGrpNum'] : 0;
    	$msgType =  ($arrMsgSet) ? $arrMsgSet['msgType'] : $this->common->getCodeNumByCodeGrpNCodeId('MSGTYPE', 'NONE'); //메시지타입 고유번호(누구와 누구간 일반대화,문의성 대화 구분)
    	*/
    	
    	if (empty($this->_msgGrpNum) || $this->_msgGrpNum == 0)
    	{
    		$this->response([
    			'status' => '406',
    			'message' => 'msgGrpNo not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}  
    	
    	//글쓰기 제약 사항
    	$userInfo = $this->common->getUserInfo('num', $this->_authkey);
    	if ($userInfo['USTATECODE_NUM'] == 950)
    	{
    		$this->response([
    			'status' => '406',
    			'message' => '패널티부과 대상자입니다. 글을 작성하실수 없습니다.'
    		], REST_Controller::HTTP_NOT_ACCEPTABLE); //(406)    		
    	}
    	
    	if ($userInfo['USTATECODE_NUM'] == 940)
    	{
    		$this->response([
    			'status' => '406',
    			'message' => '14세미만 대상자입니다. 글을 작성하실수 없습니다.'
    		], REST_Controller::HTTP_NOT_ACCEPTABLE); //(406)
    	}
    	
    	if ($this->common->getIsBlackUserIP($this->_authkey, $this->input->ip_address()))
    	{
    		$this->response([
    			'status' => '406',
    			'message' => '블랙리스트 ip입니다. 글을 작성하실수 없습니다.'
    		], REST_Controller::HTTP_NOT_ACCEPTABLE); //(406)    		
    	}
    	
    	$arrCheck = $this->common->abuseWordCheck($msgContent);
    	if ($arrCheck['isChecked']) 
    	{
    		$word = mb_substr($arrCheck['checkedWord'], 0, 1, 'UTF-8').'**';
    		$this->response([
    			'status' => '406',
    			'message' => '금지어가 있습니다('.$word.'). 글을 작성하실수 없습니다.'
    		], REST_Controller::HTTP_NOT_ACCEPTABLE); //(406)
    	}    	
    	
    	$msgType = $this->common->getCodeNumByCodeGrpNCodeId('MSGTYPE', 'NONE');
    	if ($this->_method == 'write_user_to_shop' || $this->_method == 'write_shop_to_user')
    	{
    		//샵과 유저간의 대화만 targetNum이 필요
    		if (empty($targetNum) || $targetNum == 0)
    		{
    			$this->response([
    				'status' => FALSE,
    				'message' => 'targetno not specification'
    			], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    		}
    		
    		$msgType = 17040; //문의성 대화 	17160; //일반 대화
    	}
    	else if ($this->_method == 'write_user_to_mall' || $this->_method == 'write_mall_to_user')
    	{
    		$msgType = 17030; //문의성 대화 	17150; //일반 대화
    	}
    	else if ($this->_method == 'write_shop_to_mall' || $this->_method == 'write_mall_to_shop')
    	{
    		$msgType = 17020; //문의성 대화 	17140; //일반 대화
    	} 		
    		
    	$arrMethod = explode('_', $this->_method);
		
    	$senderType = 'U'; //발송자가 일반회원으로서 발송
		if ($arrMethod[1] == 'shop')
		{		
			$senderType = 'S'; //발송자가 샵으로서 발송
		}
		else if ($arrMethod[1] == 'mall')
		{
			$senderType = 'M'; //발송자가 MALL(Circus)으로서 발송
		}
		
		$targetType = 'U'; //발송대상자가 회원
		if ($arrMethod[3] == 'shop')
		{
			$targetType = 'S';
			$targetNum = $this->common->getUserNumByShopNum($targetNum); //샵작가의 회원고유번호 조회
		}
		else if ($arrMethod[3] == 'mall')
		{
			$targetType = 'M';
		}
   	
    	$insData = array(
    		'USER_NUM' => $this->_authkey,
    		'CONTENT' => $msgContent,
    		'SENDER_TYPE' => $senderType,
    		'TARGET_TYPE' => $targetType,
    		'MSGTYPECODE_NUM' => $msgType,
    		'ORDERS_NUM' => (!empty($this->_ordNum)) ? $this->_ordNum : NULL, //관련 주문번호가 있는 경우
    		'SHOP_NUM' => (!empty($this->_sNum)) ? $this->_sNum : NULL, //관련 샵번호가 있는 경우
    		'SHOPITEM_NUM' => (!empty($this->_siNum)) ? $this->_siNum : NULL, //관련 아이템번호가 있는 경우
    		'REMOTEIP' => $this->input->ip_address()
    	);
    	
    	$result = $this->message_model->setMessageDataInsert(
    		array(
    			'msgGrpNum' => $this->_msgGrpNum
    		),
    		'N', //전체 발송 여부
    		$targetNum,
    		$insData,
    		TRUE	//파일 업로드 여부 (TRUE, FALSE)
    	);
    	
    	$dt = array('result' => $result);
    	
    	return $dt;
    }
    
    /**
     * @method name : getMessageDataViewList
     * VIEW_MESSAGE 에서 리스트
     * GROUP별 리스트(같은그룹은 한row로 표현)
     * json data 중
     * USER_MSG_COUNT : 보낸 메시지수
     * TOUSER_MSG_COUNT : 받은 메시지수
     * TOUSER_UNREAD_COUNT : 읽지않은 메시지 수
     * 
     * 앱으로 부터 꼭 받아야 할 파라메터
     * 
     */
    private function getMessageDataViewList()
    {
    	if ($this->_method == 'listviewum')
    	{
    		$pageMethod = 'listuser';
    	}
    	else if ($this->_method == 'listviewus') 
    	{
    		$pageMethod = 'listusershop';
    	}
    	
    	$qData = array(
    		'uNum' => $this->_authkey,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage,
    		'pageMethod' => $pageMethod
    	);    	
    	$result['myMsgSet'] = $this->message_model->getMessageDataViewList($qData, FALSE);

    	return $result;
    }     
    
    /**
     * @method name : getMessageTargetDataList
     * 나만의 메시지 대상 리스트 
     * 
     * @return unknown
     */
    private function getMessageTargetDataList()
    {
    	$qData = array(
    		'userNum' => $this->_authkey,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage
    	);
    	$result['myTargetSet'] = $this->message_model->getMessageTargetDataList($qData);
    	
    	return $result;    	
    }
}