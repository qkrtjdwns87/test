<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Item_a
 * 
 *
 * @author : Administrator
 * @date    : 2016. 04.
 * @version:
 */
class Shop_a extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var string 출력 포맷 (json)
	 */
	protected $_outformat = '';
	
	/**
	 * @var integer SHOP 고유번호
	 */
	protected $_sNum = 0;	
	
	/**
	 * @var integer SHOPITEM 고유번호
	 */
	protected $_siNum = 0;
	
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
	 * @var integer 파일첨부갯수(여기선 등록된 파일카운트)
	 */
	protected $_fileCnt = 1;

	/**
	 * @var integer USER LEVEL
	 */
	protected $_uLevelType = 0;	
	
	protected $_encKey = '';
	
	/**
	 * @var string 앱으로 부터 전달받는 고유 deviceid
	 */
	protected $_deviceId = '';
	
	/**
	 * @var string 앱으로 부터 전달받는 pushid
	 */
	protected $_pushId = '';	
	
	public function __construct()
	{
		parent::__construct ();
		
		$this->load->helper(array('url', 'cookie'));
		$this->load->model(array('item_model', 'shop_model', 'main_model'));

		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'shopview':
				$this->getShopInfoRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/shop/shop_view', $data);
				break;
			case 'itemlist':
			case 'bestlist':
				if ($this->_outformat != 'json') $this->getShopInfoRowData(); //SNS공유시 필요 
				$this->getBestItemListData();
				$data = array_merge($this->_data, $this->_sendData);
				($this->_outformat == 'json') ? $this->getBestItemListDataToJson() : $this->load->view('app/shop/shop_item_list', $data);
				break;	
			case 'itemlistshare': // sns share
				if ($this->_outformat != 'json') $this->getShopInfoRowData(); //SNS공유시 필요 
				$this->getBestItemListData();
				$data = array_merge($this->_data, $this->_sendData);
				($this->_outformat == 'json') ? $this->getBestItemListDataToJson() : $this->load->view('app/shop/shop_item_list_share', $data);
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
		
		if (in_array('sno', $this->_arrUri))
		{
			$this->_sNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'sno')));
		}
		$this->_sNum = $this->common->nullCheck($this->_sNum, 'int', 0);
		
		if (in_array('sino', $this->_arrUri))
		{
			$this->_siNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'sino')));
		}
		$this->_siNum = $this->common->nullCheck($this->_siNum, 'int', 0);
		
		if (in_array('format', $this->_arrUri))
		{
			$this->_outformat = $this->common->urlExplode($this->_arrUri, 'format');
		}
		$this->_outformat = $this->common->nullCheck($this->_outformat, 'str', '');
		
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
		
		$shopName = $this->input->post_get('shopname', TRUE);
		if (!empty($shopName)) $this->_currentParam .= '&shopname='.$shopName;
		
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
		
		$shopCode = $this->input->post_get('shopcode', TRUE);
		if (!empty($shopCode)) $this->_currentParam .= '&shopcode='.$shopCode;
		
		$this->_deviceId = $this->input->post_get('deviceid', TRUE);
		$this->_pushId = $this->input->post_get('pushid', TRUE);
		
		$this->_uLevelType = $this->common->getSessionUserLevelCodeId();
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_sNum > 0) ? '/sno/'.$this->_sNum : '';		
		$this->_currentUrl .= ($this->_siNum > 0) ? '/sino/'.$this->_siNum : '';
		
		/*
		 * 파라메터 처럼 array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentParam' => $this->_currentParam,				
			'searchKey' => $searchKey,
			'searchWord' => $searchWord,
			'shopName' => $shopName,
			'shopUserName' => $shopUserName,
			'shopCode' => $shopCode,
			'pageMethod' => $this->_uriMethod,
			'sNum' => $this->_sNum,				
			'siNum' => $this->_siNum,
			'tbl' => $this->_tbl,
			'fileCnt' => $this->_fileCnt,
			'isLogin' => ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0) ? FALSE : TRUE, //$this->common->getIsLogin(),
			'userLevelType' => $this->_uLevelType,				
			'sessionData' => $this->common->getSessionAll(),
			'siteDomain' => $this->common->getDomain(),
			'deviceId' => $this->_deviceId,
			'pushId' => $this->_pushId				
		);
	}
	
	private function loginCheck($url = '/app/user_a/login')
	{
		//if (!$this->common->getIsLogin(TRUE))
		if ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0)
		{
			//log_message('[circus] Clicked Here');
			//$this->common->message('로그인후 이용하실 수 있습니다.', '/app/user_a/login', 'top');
			$this->common->message('로그인후 이용하실 수 있습니다.', "app_showMenuWindow('로그인', '".$url."');", 'js');
		}
	}	
	
	/**
	 * @method name : getShopInfoRowData
	 * 샵소개 
	 * 
	 */
	private function getShopInfoRowData()
	{
		$this->_data['shopSet'] = $this->shop_model->getShopBaseRowData($this->_sNum, $this->common->nullCheck(get_cookie('usernum'), 'int', 0));
	}	
	
	private function getBestItemListData()
	{
		$qData = array(
			'uNum' => $this->common->nullCheck(get_cookie('usernum'), 'int', 0),
			'sNum' => $this->_sNum,
			'listCount' => (empty($this->_listCount) || $this->_listCount == 0) ? 10 : $this->_listCount,
			'currentPage' => (empty($this->_currentPage) || $this->_currentPage == 0) ? 1 : $this->_currentPage,
			'isValidData' => TRUE
		);
		$result = $this->main_model->getBestItemMainRowViewData($qData);
		$this->_data['bestRsSet'] = $result['recordSet'];
		$this->_data['bestRsTotCnt'] = $result['rsTotalCount'];
	}	
	
	private function getBestItemListDataToJson()
	{
		exit($this->common->arrayToJson($this->_data));
	}	
}