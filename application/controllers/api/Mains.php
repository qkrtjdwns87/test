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
class Mains extends REST_Controller {
	
	protected $_method = '';
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;	

	/**
	 * @var integer Story 고유번호
	 */
	protected $_stoNum = 0;
	
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
        $this->load->model(array('main_model', 'item_model', 'story_model', 'board_model'));
        
        $this->_encKey = $this->config->item('encryption_key');
        
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['main_get']['limit'] = 0; // 500 requests per hour per user/key
        $this->methods['main_post']['limit'] = 500; // 100 requests per hour per user/key
        $this->methods['main_delete']['limit'] = 0; // 50 requests per hour per user/key
    }
    
    public function main_get() {exit('No access allowed');}
    public function main_put() {exit('No access allowed');}
    public function main_delete() {exit('No access allowed');}
    //POST방식만 사용
    public function main_post()
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
    	$this->_stoNum = $this->input->post('stono', TRUE);
    	
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
    	
    	/*
    	$this->response([
    		'status' => FALSE,
    		'message' => 'No method was specification(method=>'.$method.')'
    	], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	
    	$this->set_response([
    		'status' => FALSE,
    		'message' => 'User could not be found'
    	], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
    	
    	$this->set_response([
    		'status' => FALSE,
    		'message' => 'deviceid and pushid unauthorized'
    	], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	*/
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
    		case 'story':
    			$this->_data = $this->getStoryMainData();
    			break;
    		case 'storylist':
	    		$this->_data = $this->getStoryListData();
    			break;
    		case 'bestitem':
    			$this->_data = $this->getBestItemMainData();
    			break;
    		case 'storyflag':
    			$this->_data = $this->setStoryFlag();
    			break;    			
    		case 'appnotice':
    			$this->_data = $this->getNoticeDataList();
    			break;
    		case 'isflag';
	    		$this->_data = $this->getIsFlaged();
	    		break;    		
    		default:
    			$this->_data = $this->getMainData();
    			break;
    	}
    }    
    
    /**
     * @method name : getStoryMainData
     * 관리자 설정 스토리 메인 
     * 
     * @return unknown
     */
    private function getStoryMainData()
    {
    	$result = $this->main_model->getStoryMainRowData(0, $this->_authkey);
    	
    	return $result; 
    }
    
    /**
     * @method name : getStoryListData
     * 스토리 상세 내용 
     * 
     * @return unknown
     */
    private function getStoryListData()
    {
    	$qData = array(
    		'userNum' => $this->_authkey,
   			'listCount' => $this->_listCount,
   			'currentPage' => $this->_currentPage
    	);
    	$result = $this->story_model->getStoryDataList($qData);
    	
    	return $result;
    }
    
    /**
     * @method name : getBestItemMainData
     * 트랜드와 베스트 아이템 
     * 
     * @return unknown
     */
    private function getBestItemMainData()
    {
    	$result['trendSet'] = $this->main_model->getTrendMainRowData(0, $this->_authkey);
    	$qData = array(
    		'uNum' => $this->_authkey,
    		'listCount' => (empty($this->_listCount) || $this->_listCount == 0) ? 10 : $this->_listCount,
    		'currentPage' => (empty($this->_currentPage) || $this->_currentPage == 0) ? 1 : $this->_currentPage,
    		'isValidData' => TRUE
    	);    	
    	$result['bestSet'] = $this->main_model->getBestItemMainRowViewData($qData);
    	//$result['bestSet'] = $this->main_model->getBestItemMainRowData(0); //관리자 지정된 내용만 가져올경우(주의:json구조가 바뀜)
    	
    	return $result;
    }
    
    /**
     * @method name : getMainData
     * 앱메인 내용(HOME) 
     * 
     * @return unknown
     */
    private function getMainData()
    {
    	$qData = array();
    	$result['mainVisualSet'] = $this->main_model->getVisualMainRowData($qData, FALSE);
    	$result['mainTodaySet'] = $this->main_model->getTodayMainRowData($qData, $this->_authkey, FALSE);
    	 
    	//현재 사용중인 기획전 등록번호 조회(1건)
    	$qData = array(
    		'listCount' => 1,
    		'currentPage' => 1,
    		'eventType' => 's',
    		'eventState' => 'ing'
    	);
    	$data = $this->item_model->getEventDataList($qData, FALSE);
    	if ($data['rsTotalCount'] > 0)
    	{
    		$resultNum = $data['recordSet'][0]['NUM'];
    		$result['mainSpecialSet'] = $this->item_model->getEventRowData($resultNum, FALSE);
    		$result['mainSpecialSet']['itemSet'] = $this->item_model->getEventItemDataList($resultNum, 0); //0은 회원고유번호
    	}
    	else
    	{
    		//진행중인 기획전이 없는 경우
    		$result['mainSpecialSet'] = array();
    		$result['mainSpecialSet']['itemSet'] = array();
    	}
    	
    	$qData = array(
    		'uNum' => $this->_authkey,
    		'listCount' => (empty($this->_listCount) || $this->_listCount == 0) ? 10 : $this->_listCount,
    		'currentPage' => (empty($this->_currentPage) || $this->_currentPage == 0) ? 1 : $this->_currentPage,
    		'isValidData' => TRUE
    	);
    	$result['mainNewItemSet'] = $this->main_model->getNewItemMainRowViewData($qData);
    	//$result['mainNewItemSet'] = $this->main_model->getNewItemMainRowData(0, FALSE); //관리자 지정된 내용만 가져올경우(주의:json구조가 바뀜)
    	 
    	$siNum = 0; //$this->common->getSession('shop_num'); 샵카테고리도 같이 필요한 경우 주석 해제
    	$searchType = 'MALL';
    	$result['mainCateSet'] = $this->item_model->getItemCommonCateDataList(
   			array(
				'shopNum' => $siNum,
				'searchKey' => $searchType,
				'isDelView' => FALSE,
				'isUseNoView' => TRUE
  			)
		);
    	
    	return $result;    	
    }
    
    /**
     * @method name : setStoryFlag
     * 플래그 처리(스토리)
     *
     * @return unknown[]
     */
    private function setStoryFlag()
    {
    	if (!$this->_isAppAuth)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    
    	if (empty($this->_stoNum) || $this->_stoNum == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'stono not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    
    	$result = $this->common->setFlag('storyapp', $this->_stoNum, $this->_authkey);
    	$dt = array('result' => $result);
    
    	return $dt;
    }    
    
    private function getNoticeDataList()
    {
    	$urgencyYn = $this->input->post_get('urgencyyn', TRUE); //긴급공지 여부
    	$qData = array(
   			'setNum' => 9150,
   			'urgencyYn' => $urgencyYn,
  			'listCount' => $this->_listCount,
   			'currentPage' => $this->_currentPage
    	);    	
    	$result = $this->board_model->getBoardDataList($qData);
    	
    	return $result;
    }
    
    private function getIsFlaged()
    {
    	if (!$this->_isAppAuth)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'deviceid, pushid or authkey was unauthorized'
    		], REST_Controller::HTTP_UNAUTHORIZED); // UNAUTHORIZED (401) being the HTTP response code
    	}
    	
    	$type = $this->input->post('type', TRUE); //item, shop, story
    	$num = $this->input->post('no', TRUE);
    	
    	if (empty($type))
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'type not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}
    	
    	if (empty($num) || $num == 0)
    	{
    		$this->response([
    			'status' => FALSE,
    			'message' => 'no not specification'
    		], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    	}    	
    	$result = $this->common->getIsFlaged($type, $num, $this->_authkey);
    	
    	return array('result' => $result);
    }
}
