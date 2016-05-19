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
class Searches extends REST_Controller {
	
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
	 * @var integer	카테고리 고유번호
	 */
	protected $_ctNum = 0;	
	
	protected $_searchKey = '';
	
	protected $_searchWord = '';
	
	protected $_orderBy = '';	
	
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
        $this->load->model(array('item_model', 'shop_model', 'main_model'));
        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['search_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['search_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['search_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function search_get() {exit('No access allowed');}
    public function search_put() {exit('No access allowed');}
    public function search_delete() {exit('No access allowed');}
    //POST방식만 사용
    public function search_post()
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
    	$this->_ctNum = $this->input->post('ctno', TRUE);
    	$this->_searchKey = $this->input->post_get('skey', TRUE);
    	$this->_searchWord = $this->input->post_get('sword', TRUE);
    	$this->_orderBy = $this->input->post_get('sort', TRUE);
    	
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
    			$this->_data = $this->getMain();
    			break;   
    		case 'cate':
    			$this->_data = $this->getCateListData();
    			break;    			
    		case 'recomm':
    			$this->_data = $this->getRecommSearchMainRowData();
    			break;
    		case 'search':
    		case 'searchshop':
    		case 'searchitem':
    			$this->_data = $this->getSearchListData();
    			break;
    	}
    }
    
    /**
     * @method name : getMain
     * 검색 메인 
     * 
     * @return number
     */
    private function getMain()
    {
    	return $this->getCateListData() + $this->getRecommSearchMainRowData(0);
    }
    
    /**
     * @method name : getCateListData
     * 카테고리(써커스 생성 카테고리만) 
     * 
     * @return unknown
     */
    private function getCateListData()
    {
    	$result['cateSet'] = $this->item_model->getItemCommonCateDataList(
   			array(
				'searchKey' => 'MALL',
   				'itemCate' => ($this->_ctNum > 0) ? $this->_ctNum : 1,   				
				'isDelView' => FALSE,
				'isUseNoView' => TRUE
   			)
		);
    	 
    	return $result;
    }  
    
    /**
     * @method name : getRecommSearchMainRowData
     * 추천검색어 목록 
     * 
     * @return unknown
     */
    private function getRecommSearchMainRowData()
    {
    	$result['recommSet'] = $this->main_model->getRecommSearchMainRowData(0);
    	
    	return $result;
    }
    
    private function getSearchListData()
    {
    	$result = '';
    	$userNum = $this->common->nullCheck($this->_authkey, 'int', 0);

    	if ($this->_method == 'searchitem' || $this->_method == 'search')
    	{
 	    	$qData = array(
	    		'itemName' => $this->_searchWord,
	    		'shopName' => $this->_searchWord,
	    		'shopUserName' => $this->_searchWord,
	    		'itemCate' => $this->_ctNum,
	    		'itemTag' => $this->_searchWord,
	    		'userNum' => $userNum,
	    		'listCount' => $this->_listCount,
	    		'currentPage' => $this->_currentPage,
 	    		'orderBy' => $this->_orderBy,
 	    		'isTotalSearch' => TRUE,
	    		'isValidData' => TRUE
	    	);
	    	
	    	$result['itemSet'] = $this->item_model->getItemDataList($qData);
    	}    	
    	
    	if ($this->_method == 'searchshop' || $this->_method == 'search')
    	{
	    	$qData = array(
	    		'shopName' => $this->_searchWord,
	    		'shopUserName' => $this->_searchWord,
	    		'userNum' => $userNum,
	    		'listCount' => $this->_listCount,
	    		'currentPage' => $this->_currentPage,
	    		'isTotalSearch' => TRUE,	    			
	    		'isValidData' => TRUE
	    	);
	    	
	    	$result['shopSet'] = $this->shop_model->getShopDataList($qData);
	    	//샵에 속한 아이템 리스트
	    	if ($result['shopSet']['rsTotalCount'] > 0)
	    	{
    			for($i=0; $i<count($result['shopSet']['recordSet']); $i++)
    			{
    				$qDt = array(
    					'sNum' => $result['shopSet']['recordSet'][$i]['NUM'],
    					'userNum' => $userNum,
    					'listCount' => 3, //고정 //$this->common->nullCheck($this->input->post_get('item_listcount', TRUE), 'int', 3),
    					'currentPage' => 1, //$this->common->nullCheck($this->input->post_get('item_page', TRUE), 'int', 1),
    					'isValidData' => TRUE
    				);
    				
    				$itemDt = $this->item_model->getItemDataList($qDt);
    				$result['shopSet']['recordSet'][$i]['itemSet'] = $itemDt;
    			}
	    	}
    	}
    	
    	return $result;
   }
}