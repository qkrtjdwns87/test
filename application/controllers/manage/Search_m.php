<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Search_m
 * popup 검색
 *
 * @author : Administrator
 * @date    : 2015. 12.
 * @version:
 */
class Search_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = '';
	
	/**
	 * @var string 처리후 되돌아갈 url
	 */
	protected  $_returnUrl = '';	
	
	/**
	 * @var array	class간(주로 view) 넘겨주는 data set
	 */
	protected $_sendData = array();
	
	/**
	 * @var string 고유번호로 search 되야 하는 경우
	 * 고유번호 통합
	 */
	protected $_schNo = '';	
	
	/**
	 * @var array	data set
	 */
	protected $_data = array();

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
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'user':
				$this->load->model('user_model');
				$this->getUserDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/user_search', $data);
				break;			
			case 'manager':
				$this->load->model('user_model');				
				$this->getManagerDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/manager_search', $data);
				break;
			case 'shophistory':
				$this->load->model('shop_model');
				$this->getShopHistoryDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/shop_history', $data);
				break;				
			case 'itemhistory':
				$this->load->model('item_model');
				$this->getItemHistoryDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/item_history', $data);
				break;
			case 'item':
				$this->load->model('item_model');
				$this->getItemDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/item_search', $data);				
				break;
			case 'itemrank':
				$this->load->model('item_model');
				$this->getItemRankStatsDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/item_rank_search', $data);
				break;				
			case 'shop':
				$this->load->model('shop_model');
				$this->getShopDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/shop_search', $data);
				break;				
			case 'story':
				$this->load->model('story_model');
				$this->getStoryDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/story_search', $data);
				break;				
			case 'orderlist':
				$this->load->model('order_model');
				$this->getOrderDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/search/order_search', $data);
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
		
		if (in_array('return_url', $this->_arrUri))
		{
			$this->_returnUrl = $this->common->urlExplode($this->_arrUri, 'return_url');
		}
		$this->_returnUrl = $this->common->nullCheck($this->_returnUrl, 'str', '');
		
		if ($this->_returnUrl == '')
		{
			$this->_returnUrl = $this->input->post_get('return_url', FALSE);
		}
		
		if (in_array('schno', $this->_arrUri))
		{
			$this->_schNo = $this->common->urlExplode($this->_arrUri, 'schno');
		}
		$this->_schNo = $this->common->nullCheck($this->_schNo, 'int', 0);
	
		//검색조건에 해당되는 경우 get이나 post로 받고 parameter 구성
		$searchKey = $this->input->post_get('skey', TRUE);
		$searchWord = $this->input->post_get('sword', TRUE);
		if (!empty($searchKey) && !empty($searchWord)) $this->_currentParam .= '&skey='.$searchKey.'&sword='.$searchWord;
	
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=search'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_schNo > 0) ? '/schno/'.$this->_schNo : '';
		
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
			'pageMethod' => $this->_uriMethod
		);
	}
	
	/**
	 * @method name : getUserDataList
	 * 회원 Search 리스트
	 * 
	 */
	private function getUserDataList()
	{
		$userName = $this->input->post_get('username', FALSE);
		if (!empty($userName)) $this->_currentParam .= '&username='.$userName;
	
		$userEmail = $this->input->post_get('useremail', TRUE);
		if (!empty($userEmail)) $this->_currentParam .= '&useremail='.$userEmail;
	
		$userMobile = $this->input->post_get('usermobile', TRUE);
		if (!empty($userMobile)) $this->_currentParam .= '&usermobile='.$userMobile;
		
		$userState = $this->input->post_get('userstate', TRUE);
		if (!empty($userState)) $this->_currentParam .= '&userstate='.$userState;		
	
		$this->_sendData = $this->_sendData + array(
			'userName' => $userName,
			'userEmail' => $userEmail,
			'userMobile' => $userMobile,
			'userState' => $userState,
			'currentParam' => $this->_currentParam,
		);
	
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
	 * @method name : getManagerDataList
	 * 관리자 search 리스트 
	 * 
	 */
	private function getManagerDataList()
	{
		$userName = $this->input->post_get('username', FALSE);
		if (!empty($userName)) $this->_currentParam .= '&username='.$userName;
		
		$userEmail = $this->input->post_get('useremail', TRUE);
		if (!empty($userEmail)) $this->_currentParam .= '&useremail='.$userEmail;
		
		$userUseYn = $this->input->post_get('useyn', TRUE);
		if (!empty($userUseYn)) $this->_currentParam .= '&useyn='.$userUseYn;
		
		$this->_sendData = $this->_sendData + array(
			'userName' => $userName,
			'userEmail' => $userEmail,
			'userUseYn' => $userUseYn,
			'currentParam' => $this->_currentParam,
		);
		
		$this->_data = $this->user_model->getManagerDataList($this->_sendData, FALSE);
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
	 * @method name : getShopHistoryDataList
	 * Shop 히스토리 data list 
	 * 
	 */
	private function getShopHistoryDataList()
	{
		$shopState = $this->input->post_get('shopstate', TRUE);
		if (!empty($shopState)) $this->_currentParam .= '&shopstate='.$shopState;
		
		$shopStateCodeNum = $this->input->post_get('shopstatenum', TRUE);
		if (!empty($shopStateCodeNum)) $this->_currentParam .= '&shopstatenum='.$shopStateCodeNum;
		
		$this->_sendData = $this->_sendData + array(
			'sNum' => $this->_schNo,
			'shopState' => (!empty($shopState)) ? $shopState : '', //승인(approval)이상 단계에 해당되는것만
			'shopStateCodeNum' => (!empty($shopStateCodeNum)) ? $shopStateCodeNum : 0, //shopstat 를 지정하는 경우
			'currentParam' => $this->_currentParam,
		);		
		
		$this->_data = $this->shop_model->getShopHistoryDataList($this->_sendData, FALSE);
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
	 * @method name : getItemHistoryDataList
	 * Item histor search 
	 * 
	 */
	private function getItemHistoryDataList()
	{
		$itemState = $this->input->post_get('itemstate', TRUE);
		if (!empty($itemState)) $this->_currentParam .= '&itemstate='.$itemState;
	
		$itemStateCodeNum = $this->input->post_get('itemstatenum', TRUE);
		if (!empty($itemStateCodeNum)) $this->_currentParam .= '&shopstatenum='.$shopStateCodeNum;
	
		$this->_sendData = $this->_sendData + array(
			'siNum' => $this->_schNo,
			'itemState' => (!empty($itemState)) ? $itemState : '', //승인(approval)이상 단계에 해당되는것만
			'itemStateCodeNum' => (!empty($itemStateCodeNum)) ? $itemStateCodeNum : 0, //shopstat 를 지정하는 경우
			'currentParam' => $this->_currentParam,
		);
	
		$this->_data = $this->item_model->getItemHistoryDataList($this->_sendData, FALSE);
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
	
	private function getItemDataList()
	{
		$itemCate = $this->input->post_get('itemcate', TRUE);
		if (!empty($itemCate)) $this->_currentParam .= '&itemcate='.$itemCate;
		
		$itemSearchKey = $this->input->post_get('itemsearchkey', TRUE);
		if (!empty($itemSearchKey)) $this->_currentParam .= '&itemsearchkey='.$itemSearchKey;
		
		$itemSearchWord = $this->input->post_get('itemsearchword', TRUE);
		if (!empty($itemSearchWord)) $this->_currentParam .= '&itemsearchword='.$itemSearchWord;
		
		$shopSearchKey = $this->input->post_get('shopsearchkey', TRUE);
		if (!empty($shopSearchKey)) $this->_currentParam .= '&shopsearchkey='.$shopSearchKey;
		
		$shopSearchWord = $this->input->post_get('shopsearchword', TRUE);
		if (!empty($shopSearchWord)) $this->_currentParam .= '&shopsearchword='.$shopSearchWord;
		
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
		
		$itemName = $itemCode = $shopName = $shopCode = '';
		
		if ($itemSearchKey == 'name')
		{
			$itemName = $itemSearchWord;
		}
		else if ($itemSearchKey == 'code')
		{
			$itemCode = $itemSearchWord;
		}
		
		if ($shopSearchKey == 'name')
		{
			$shopName = $shopSearchWord;
		}
		else if ($shopSearchKey == 'code')
		{
			$shopCode = $shopSearchWord;
		}
		
		//카테고리
		$qData = array(
			'searchKey' => 'ALL',
			'isDelView' => FALSE,
			'isUseNoView' => TRUE,
			'shop_num' => $this->common->getSession('shop_num'),
			'item_num' => $this->input->post_get('item_num', TRUE)
		);
		
		$this->_sendData = $this->_sendData + array(
			'itCateSet' => $this->item_model->getItemCommonCateDataList($qData),
			'itemCate' => $itemCate,
			'itemName' => $itemName,
			'itemCode' => $itemCode,
			'shopName' => $shopName,
			'shopCode' => $shopCode,
			'itemSearchKey' => $itemSearchKey,
			'itemSearchWord' => $itemSearchWord,
			'shopSearchKey' => $shopSearchKey,
			'shopSearchWord' => $shopSearchWord,
			'shopUserName' => $shopUserName
		);
		
		if (!$this->_isAdmin)
		{
			//샵관리자로 로그인한 경우
			$this->_sendData['sNum'] = $this->common->getSession('shop_num');
		}
		
		$this->_sendData['isValidData'] = TRUE; //TRUE 승인된 유효아이템만
		$this->_data = $this->item_model->getItemDataList($this->_sendData, FALSE);
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
	 * @method name : getShopDataList
	 * 샵리스트 정보  
	 * 
	 */
	private function getShopDataList()
	{
		$shopSearchKey = $this->input->post_get('shopsearchkey', TRUE);
		if (!empty($shopSearchKey)) $this->_currentParam .= '&shopsearchkey='.$shopSearchKey;
	
		$shopSearchWord = $this->input->post_get('shopsearchword', TRUE);
		if (!empty($shopSearchWord)) $this->_currentParam .= '&shopsearchword='.$shopSearchWord;
	
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
	
		$shopName = $shopCode = '';
		
		if ($shopSearchKey == 'name')
		{
			$shopName = $shopSearchWord;
		}
		else if ($shopSearchKey == 'code')
		{
			$shopCode = $shopSearchWord;
		}
	
		$this->_sendData = $this->_sendData + array(
			'shopName' => $shopName,
			'shopCode' => $shopCode,
			'shopSearchKey' => $shopSearchKey,
			'shopSearchWord' => $shopSearchWord,
			'shopUserName' => $shopUserName
		);
	
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
	
	/**
	 * @method name : getStoryDataList
	 * 등록된 스토리 리스트
	 * 
	 */
	private function getStoryDataList()
	{
		$this->_data = $this->story_model->getStoryDataList($this->_sendData, FALSE);
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
	
	private function getOrderDataList()
	{
		$searchType = $this->input->post_get('searchtype', TRUE);
		if (!empty($searchType)) $this->_currentParam .= '&searchtype='.$searchType;
	}
	
	/**
	 * @method name : getItemRankStatsDataList
	 * 샵아이템 랭킹 통계 데이터
	 * 
	 */
	private function getItemRankStatsDataList()
	{
		$itemCate = $this->input->post_get('itemcate', TRUE);
		if (!empty($itemCate)) $this->_currentParam .= '&itemcate='.$itemCate;
		
		$itemSearchKey = $this->input->post_get('itemsearchkey', TRUE);
		if (!empty($itemSearchKey)) $this->_currentParam .= '&itemsearchkey='.$itemSearchKey;
		
		$itemSearchWord = $this->input->post_get('itemsearchword', TRUE);
		if (!empty($itemSearchWord)) $this->_currentParam .= '&itemsearchword='.$itemSearchWord;
		
		$shopSearchKey = $this->input->post_get('shopsearchkey', TRUE);
		if (!empty($shopSearchKey)) $this->_currentParam .= '&shopsearchkey='.$shopSearchKey;
		
		$shopSearchWord = $this->input->post_get('shopsearchword', TRUE);
		if (!empty($shopSearchWord)) $this->_currentParam .= '&shopsearchword='.$shopSearchWord;
		
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
		
		$orderBy = $this->input->post_get('orderby', TRUE);
		if (!empty($orderBy)) $this->_currentParam .= '&orderby='.$orderBy;		
		
		$itemName = $itemCode = $shopName = $shopCode = '';
		
		if ($itemSearchKey == 'name')
		{
			$itemName = $itemSearchWord;
		}
		else if ($itemSearchKey == 'code')
		{
			$itemCode = $itemSearchWord;
		}
		
		if ($shopSearchKey == 'name')
		{
			$shopName = $shopSearchWord;
		}
		else if ($shopSearchKey == 'code')
		{
			$shopCode = $shopSearchWord;
		}
		
		//카테고리
		$qData = array(
			'searchKey' => 'ALL',
			'isDelView' => FALSE,
			'isUseNoView' => TRUE,
			'shop_num' => $this->common->getSession('shop_num'),
			'item_num' => $this->input->post_get('item_num', TRUE)
		);
		
		$this->_sendData = $this->_sendData + array(
			'itCateSet' => $this->item_model->getItemCommonCateDataList($qData),
			'itemCate' => $itemCate,
			'itemName' => $itemName,
			'itemCode' => $itemCode,
			'shopName' => $shopName,
			'shopCode' => $shopCode,
			'itemSearchKey' => $itemSearchKey,
			'itemSearchWord' => $itemSearchWord,
			'shopSearchKey' => $shopSearchKey,
			'shopSearchWord' => $shopSearchWord,
			'shopUserName' => $shopUserName,
			'orderBy' => $orderBy
		);
		
		if (!$this->_isAdmin)
		{
			$this->_sendData['sNum'] = $this->common->getSession('shop_num');
		}		
		
		$this->_sendData['isValidData'] = TRUE; //TRUE 승인된 유효아이템만
		$this->_data = $this->item_model->getItemRankStatsDataList($this->_sendData);
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
}