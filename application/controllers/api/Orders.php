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
class Orders extends REST_Controller {
	
	protected $_method = '';
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;	

	/**
	 * @var integer MESSAGE 고유번호
	 */
	protected $_msgNum = 0;
	
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
	 * @var integer ORDERPART 고유번호
	 */
	protected $_ordPtNum = 0;	
	
	/**
	 * @var integer SHOP 고유번호(샵관리자 로그인시 session으로 _sNum 고정)
	 */
	protected $_sNum = 0;
	
	/**
	 * @var integer SHOPITEM 고유번호
	 */
	protected $_siNum = 0;	
	
	/**
	 * @var integer SHOPITEM_OPTION_SUB  고유번호
	 */
	protected $_sioptsNum = 0;	
	
	/**
	 * @var integer 아이템 수량
	 */
	protected $_siQuantity = 0;	
	
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
        $this->load->model(array('item_model', 'order_model', 'review_model'));
        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['order_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['order_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['order_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function order_get() {exit('No access allowed');}
    public function order_put() {exit('No access allowed');}
    public function order_delete() {exit('No access allowed');}
    //POST방식만 사용
    public function order_post()
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
    	$this->_msgGrpNum = $this->input->post('msggrpno', TRUE);
    	$this->_msgType = $this->input->post('msgtype', TRUE);
    	$this->_msgToDate = $this->input->post('msgtodate', TRUE);
    	$this->_sNum = $this->input->post('sno', TRUE);
    	$this->_siNum = $this->input->post('sino', TRUE);
    	$this->_sioptsNum = $this->input->post('sioptsno', TRUE);
    	$this->_siQuantity = $this->input->post('siqty', TRUE);
    	
    	$this->_ordNum = $this->input->post('ordno', TRUE);
    	$this->_ordPtNum = $this->input->post('ordptno', TRUE);
    	
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
    		case 'inputcart':    			
    			$this->_data = $this->setInputCart();
    			break;
    		case 'reviewform': //한줄 후기 입력창
    			$this->_data = $this->getOrderViewDataList();
    			break;
    		case 'review':
    			$this->_data = $this->setReviewDataInsert();
    			break;
    	}
    }  
    
    /**
     * @method name : setInputCart
     * 아이템 카트에 담기 
     * 앱으로 부터 꼭 받아야 할 파라메터
     * sno
     * sino
     * sioptsno (구분자 '|' 로 다수입력 가능)
     * siqty
     * 
     */
    private function setInputCart()
    {
    	if (empty($this->_sNum) || $this->_sNum == 0)
    	{
    		$this->response([
    				'status' => FALSE,
    				'message' => 'sno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	if (empty($this->_siNum) || $this->_siNum == 0)
    	{
    		$this->response([
    				'status' => FALSE,
    				'message' => 'sino not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	if (empty($this->_siQuantity) || $this->_siQuantity == 0)
    	{
    		$this->response([
    				'status' => FALSE,
    				'message' => 'siqty not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	$directYn = $this->input->post('directyn', TRUE); //즉시구매여부

    	$qData = array(
    		'uNum' => $this->_authkey,
    		'sNum' => $this->_sNum,
    		'siNum' => $this->_siNum,
    		'sioptsNum' => $this->_sioptsNum,
    		'siQuantity' => $this->_siQuantity,
    		'directYn' => $directYn
    	);
    	$result = $this->order_model->setInputCart($qData);

    	if ($directYn == 'Y')
    	{
    		$result = $this->order_model->setCartToOrderDirectDataUpdate($this->_authkey, $result);
    	}
    	
    	return array('result' => $result);
    }
    
    /**
     * @method name : getOrderViewDataList
     * 후기 작성시 주문했던 주문아이템 리스트
     * 앱으로 부터 꼭 받아야 할 파라메터 
     * ordno
     * ordptno
     * 
     * 
     */
    private function getOrderViewDataList()
    {
    	if (empty($this->_ordNum) || $this->_ordNum == 0)
    	{
    		$this->response([
   				'status' => FALSE,
   				'message' => 'ordno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	
    	if (empty($this->_ordPtNum) || $this->_ordPtNum == 0)
    	{
    		$this->response([
   				'status' => FALSE,
   				'message' => 'ordptno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	

		$qData = array(
			'ordNum' => $this->_ordNum,
			'ordPartNum' => $this->_ordPtNum,
			'isDelView' => FALSE
		);
		$result['reviewItemSet'] = $this->order_model->getOrderViewDataList($qData);
		
		return $result;
    }   
    
    /**
     * @method name : setReviewDataInsert
     * 구매후기 저장 
     * 앱으로 부터 꼭 받아야 할 파라메터
     * orditemno
     * review_content
     * score
     * 
     */
    private function setReviewDataInsert()
    {
    	$userInfo = $this->common->getUserInfo('num', $this->_authkey);
    	$ordItemNum = $this->input->post_get('orditemno', FALSE); //구성 [ORDERITEM_NUM|SHOPITEM_NUM]
		$reviewContent = $this->input->post_get('review_content', TRUE);
		
    	if (empty($ordItemNum))
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'orditemno not specification'
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
    	 
    	$arrCheck = $this->common->abuseWordCheck($reviewContent);
    	if ($arrCheck['isChecked'])
    	{
    		$word = mb_substr($arrCheck['checkedWord'], 0, 1, 'UTF-8').'**';
    		$this->response([
    			'status' => '406',
    			'message' => '금지어가 있습니다('.$word.'). 글을 작성하실수 없습니다.'
    		], REST_Controller::HTTP_NOT_ACCEPTABLE); //(406)
    	}
    	
    	$arrItNum = explode('|', $ordItemNum);
    	$insData = array(
    		'USER_NUM' => $this->_authkey,
    		'USER_ID' => $userInfo['USER_ID'],
    		'USER_NAME' => $userInfo['USER_NAME'],
    		'USER_EMAIL' => $userInfo['USER_EMAIL'],
    		'CONTENT' => $reviewContent,
    		'ORDERITEM_NUM' => $arrItNum[0], //주문아이템 고유번호
    		'SHOPITEM_NUM' => $arrItNum[1], //아이템 고유번호
    		'SCORE' => $this->input->post_get('score', FALSE), //별점 (최대5점)
    		'REMOTEIP' => $this->input->ip_address()
    	);
    
    	$result = $this->review_model->setReviewDataInsert(
    		$insData,
    		TRUE	//파일 업로드 여부 (TRUE, FALSE)
    	);
    
    	return array('result' => $result);
    }    
}