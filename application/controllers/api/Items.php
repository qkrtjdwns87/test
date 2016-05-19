<?
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Items extends REST_Controller {

	protected $_method = '';
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	/**
	 * @var integer SHOP 고유번호(샵관리자 로그인시 session으로 _sNum 고정)
	 */
	protected $_sNum = 0;
	
	/**
	 * @var integer SHOPITEM 고유번호
	 */
	protected $_siNum = 0;
	
	/**
	 * @var integer item Category 고유번호
	 */
	protected $_itcNum = 0;	
	
	/**
	 * @var integer REVIEW 고유번호
	 */
	protected $_rvNum = 0;	
	
	/**
	 * @var integer EVENT(GIFT) 고유번호
	 */
	protected $_enNum = 0;	
	
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
        //log_message('debug', '[circus] - ' . '###########here');

        // Construct the parent class
        parent::__construct();

        $this->load->helper(array('url'));
        $this->load->model(array('item_model', 'review_model', 'comment_model', 'shop_model'));

        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['item_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['item_post']['limit'] = 1000; // 100 requests per hour per user/key
        $this->methods['item_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function item_get() {exit('No access allowed');}
    public function item_put() {exit('No access allowed');}
    public function item_delete() {exit('No access allowed');}    
    //POST방식만 사용    
    public function item_post()
    {
    	
        //log_message('debug', '[circus] - ' . '###########here');

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
    	$this->_sNum = $this->input->post('sno', TRUE);
    	$this->_siNum = $this->input->post('sino', TRUE);
    	$this->_rvNum = $this->input->post('rvno', TRUE);
    	$this->_itcNum = $this->common->nullCheck($this->input->post('itcno', TRUE), 'int', 0);
    	$this->_enNum = $this->input->post('enno', TRUE);

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

    public function test_post()
    {
        $result = $this->shop_model->getShopMasterInfoBySiNo_sp(48);
        $this->response($result, REST_Controller::HTTP_OK);
    }
    
    /**
     * @method name : remap
     * method별 분기 처리
     *
     */
    public function apiRemap()
    {
        //log_message('debug', '[circus] - ' . '###########here'); 
    	switch($this->_method)
    	{
    		case 'itemlist':
    			$this->_data = $this->getItemDataList();
    			break;
    		case 'itemview':
    			$this->_data = $this->getItemViewGroupData();
    			break;    			
    		case 'optionlist':
    			$this->_data = $this->getItemOptionListData();
    			break;
    		case 'reviewlist':
    			$this->_data = $this->getReviewDataList();
    			break;    			
    		case 'review':
    			$this->_data = $this->getReviewRowData();
    			break;
    		case 'commentlist':
    			$this->_data = $this->getCommentDataList();
    			break;
    		case 'cateitemlist':
    			$this->_data = $this->getCateItemDataList();
    			break;   
    		case 'eventlist':    			
    		case 'speclist':
    		case 'giftlist':
    			$this->_data = $this->getEventDataList();
    			break;
    		case 'specitemlist':
    		case 'giftitemlist':
    			$this->_data = $this->getEventItemDataList();
    			break;
    		case 'itemflag':
    			$this->_data = $this->setItemFlag();
    			break;
    	}
    } 
    
    /**
     * @method name : getItemDataList
     * 아이템 목록 
     * 
     * @return unknown
     */
    private function getItemDataList()
    {
    	$qData = array(
    		'userNum' => $this->common->nullCheck($this->_authkey, 'int', 0),    			
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage,
    		'isValidData' => TRUE
    	);
    	
    	return $this->item_model->getItemDataList($qData);
    }
    
    /**
     * @method name : getItemOptionListData
     * 아이템 고유번호에 대한 옵션 리스트 
     * 
     * @return unknown
     */
    private function getItemOptionListData()
    {
    	if (empty($this->_siNum) || $this->_siNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sino not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code    		
    	}
    	$result = $this->item_model->getItemOptionRowDataList($this->_siNum);
    	return $result;
    }
    
    /**
     * @method name : getItemViewGroupData
     * 아이템 한건의 상세내용 모두 
     * 
     * @return unknown
     */
    private function getItemViewGroupData()
    {
    	if (empty($this->_siNum) || $this->_siNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sino not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	

        //log_message('debug', 'sino : '. $this->_siNum);

        // [yong imp] 한번에 가져 오는 쿼리로 수정하기 

        $result['ceoinfo'] = $this->shop_model->getShopMasterInfoBySiNo_sp($this->_siNum);
    	$result['baseSet'] = $this->item_model->getItemBaseRowData($this->_siNum, $this->_authkey);
    	$result['fileSet'] = $this->item_model->getItemFileDataList($this->_siNum);
    	$result['cateSet'] = $this->item_model->getItemCateDataList($this->_siNum);
    	$result['optSet'] = $this->item_model->getItemOptionRowDataList($this->_siNum);
        
    	return $result;
    }
    
    /**
     * @method name : getReviewDataList
     * 아이템 고유번호에 대한 구매후기 리스트 
     * 
     * @return unknown
     */
    private function getReviewDataList()
    {
    	if (empty($this->_siNum) || $this->_siNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sino not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	$qData = array(
    		'siNum' => $this->_siNum,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage
    	);    	
    	$result = $this->review_model->getReviewDataList($qData);
    	return $result;
    }
    
    /**
     * @method name : getReviewRowData
     * 구매후기 한건에 대한 보기 
     * 
     * @return unknown
     */
    private function getReviewRowData()
    {
    	if (empty($this->_rvNum) || $this->_rvNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'rvno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	$result = $this->review_model->getReviewRowData($this->_rvNum);
    	return $result;
    }
    
    /**
     * @method name : getCommentDataList
     * 아이템에 속한 한줄 후기 
     * 
     * @return unknown
     */
    private function getCommentDataList()
    {
    	if (empty($this->_siNum) || $this->_siNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sino not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	$qData = array(
    		'tblInfo' => 'SHOPITEM_COMMENT',
    		'tNum' => $this->_siNum,
    		'currentPage' => $this->_currentPage,
    		'listCount' => $this->_listCount
    	);
    	$data = $this->comment_model->getCommentDataList($qData, FALSE);
    	$result['commentRsSet'] = $data['recordSet'];
    	$result['commentRsTotCnt'] = $data['rsTotalCount'];    	
    	return $result;
    }
    
    /**
     * @method name : getCateItemDataList
     * 아이템 카테고리와 카테고리별 아이템 리스트
     * 
     * @return unknown
     */
    private function getCateItemDataList()
    {
    	$result['cateSet'] = $this->item_model->getItemCommonCateDataList(
    		array(
    			'searchKey' => 'MALL',
    			'isDelView' => FALSE,
    			'isUseNoView' => TRUE
    		)
    	); 

    	$qData = array(
    		'userNum' => $this->common->nullCheck($this->_authkey, 'int', 0),
    		'itemCate' => ($this->_itcNum > 0) ? $this->_itcNum : 1,
    		'currentPage' => $this->_currentPage,
    		'listCount' => $this->_listCount,
    		'isValidData' => TRUE
    	);   	
    	$result['itemSet'] = $this->item_model->getItemDataList($qData);
    	
    	return $result;
    }
    
    /**
     * @method name : getEventDataList
     * event(Gift, special) 진행 리스트 
     * 
     * @return unknown
     */
    private function getEventDataList()
    {
    	if ($this->_method == 'speclist')
    	{
    		$eventType = 'S';
    	}
    	else if ($this->_method == 'giftlist')
    	{
    		$eventType = 'G';
    	}
    	else
    	{
    		$eventType = 'E';
    	}

    	$qData = array(
    		'eventType' => $eventType,
    		'alwaysYn' => TRUE, //상시진행 포함여부
    		'eventState' => 'ing', //현재 진행중인것만
    		'currentPage' => $this->_currentPage,
    		'listCount' => $this->_listCount
    	);    	
    	$result = $this->item_model->getEventDataList($qData, FALSE);
    	
    	return $result;
    }
    
    /**
     * @method name : getEventItemDataList
     * event(Gift, special)에 등록된 아이템 리스트 
     * 페이징은 하지 않음
     * 
     * 
     * @return unknown
     */
    private function getEventItemDataList()
    {
    	if (empty($this->_enNum) || $this->_enNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'enno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	$resultTitle = ($this->_method == 'giftitemlist') ? 'giftItemSet' : 'specItemSet';
    	$result[$resultTitle] = $this->item_model->getEventItemDataList($this->_enNum, $this->_authkey);
    	 
    	return $result;
    }    
    
    /**
     * @method name : setItemFlag
     * 플래그 처리(아이템) 
     * 
     * @return unknown[]
     */
    private function setItemFlag()
    {
    	if (!$this->_isAppAuth)
    	{
    		$this->response([
    				'status' => FALSE,
    				'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    	
    	if (empty($this->_siNum) || $this->_siNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sino not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	$result = $this->common->setFlag('itemapp', $this->_siNum, $this->_authkey);
    	$dt = array('result' => $result);
    	
    	return $dt; 
    }
}
