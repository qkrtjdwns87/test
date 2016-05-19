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
class Shops extends REST_Controller {
	
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
        $this->load->model(array('item_model', 'order_model', 'shop_model'));
        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['shop_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['shop_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['shop_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function shop_get() {exit('No access allowed');}
    public function shop_put() {exit('No access allowed');}
    public function shop_delete() {exit('No access allowed');}
    //POST방식만 사용
    public function shop_post()
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
    	$this->_sNum = $this->input->post('sno', TRUE);
    	$this->_siNum = $this->input->post('sino', TRUE);
    	$this->_itcNum = $this->common->nullCheck($this->input->post('itcno', TRUE), 'int', 0);
    	
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
    	switch($this->_method)
    	{
    		case 'main':
    			$this->_data = $this->getShopMain();
    			break;    		
    		case 'shopinfo':    			
    			$this->_data = $this->getShopInfoRowData();
    			break;
    		case 'shopcate':
    			$this->_data = $this->getShopCateListData();
    			break;    			
    		case 'shopitem';
    			$this->_data = $this->getShopItemListData();
    			break;    		
    		case 'flagshop':
    			$this->_data = $this->getFlagShopDataList();
    			break;   
    		case 'shopflag':
    			$this->_data = $this->setShopFlag();
    			break;
    	}
    }
    
    /**
     * @method name : getShopMain
     * 샵정보 + 샵생성 카테고리 + 샵 아이템 
     * 
     * @return number
     */
    private function getShopMain()
    {
    	return $this->getShopInfoRowData() + $this->getShopCateListData() + $this->getShopItemListData();
    }
    
    /**
     * @method name : getShopInfoRowData
     * 샵 기본정보 
     * 
     * @return unknown
     */
    private function getShopInfoRowData()
    {
    	if (empty($this->_sNum) || $this->_sNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	//$shopUserNum = $this->common->getUserNumByShopNum($this->_sNum); //샵고유번호로 USER 고유번호 얻기
    	$result['shopInfoSet'] = $this->shop_model->getShopBaseRowData($this->_sNum, $this->_authkey);
    	
    	$profile = $result['shopInfoSet']['PROFILE_CONTENT'];
    	unset($result['shopInfoSet']['PROFILE_CONTENT']);
    	$result['shopInfoSet']['PROFILE_CONTENT'] = $this->common->stripHtmlTags($profile);
    	
    	return $result;
    }
    
    /**
     * @method name : getFlagShopDataList
     * 플래그한 샵과 샵에 속한 아이템 리스트
     * 앱으로 부터 꼭 받아야 할 파라메터
     * sno
     *
     * @return unknown
     */
    private function getFlagShopDataList()
    {
    	if (empty($this->_sNum) || $this->_sNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	$qData = array(
    		'userNum' => $this->_authkey,
    		//'itemListCount' => $itemListCount,
    		//'itemCurrentPage' => $itemCurrentPage,
    		'shopNum' => $this->_sNum,
    		'listCount' => $this->_listCount,
    		'currentPage' => $this->_currentPage,
    		'isValidData' => TRUE //유효 데이터
    	);
    	$result['flagShopSet'] = $this->item_model->getFlagShopDataList($qData, FALSE);
    	
    	return $result;
    }    
    
    /**
     * @method name : setShopFlag
     * 플래그 처리(샵)
     *
     * @return unknown[]
     */
    private function setShopFlag()
    {
    	if (!$this->_isAppAuth)
    	{
    		$this->response([
   				'status' => FALSE,
   				'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    	 
    	if (empty($this->_sNum) || $this->_sNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	 
    	$result = $this->common->setFlag('shopapp', $this->_sNum, $this->_authkey);
    	$dt = array('result' => $result);
    	 
    	return $dt;
    } 
    
    /**
     * @method name : getShopItemListData
     * 샵에 속한 아이템 리스트
     * 검색조건 : 카테고리
     * 
     */
    private function getShopItemListData()
    {
    	if (!$this->_isAppAuth)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    	
    	$qData = array(
    		'userNum' => $this->common->nullCheck($this->_authkey, 'int', 0),
    		'itemCate' => ($this->_itcNum > 0) ? $this->_itcNum : 1,
    		'sNum' => $this->_sNum,
    		'currentPage' => $this->_currentPage,
    		'listCount' => $this->_listCount,
    		'isValidData' => TRUE
    	);
    	$result['itemSet'] = $this->item_model->getItemDataList($qData);
    	
    	return $result;
    }
    
    /**
     * @method name : getCateDataList
     * 샵 카테고리
     * 기획서에는 샵이 생성한 카테고리만 불러오도록 되어 있긴하나...
     * 
     * @return unknown
     */
    private function getShopCateListData()
    {
    	if (empty($this->_sNum) || $this->_sNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'sno not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	
    	$result['cateSet'] = $this->item_model->getItemCommonCateDataList(
    		array(
    			'searchKey' => 'SHOP',
    			'shopNum' => $this->_sNum,
    			'isDelView' => FALSE,
    			'isUseNoView' => TRUE
    		)
    	);
    	
   		return $result;
    }    
}