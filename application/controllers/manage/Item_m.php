<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Item_m
 * 
 *
 * @author : Administrator
 * @date    : 2016. 01.
 * @version:
 */
class Item_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer SHOP 고유번호(샵관리자 로그인시 session으로 _sNum 고정)
	 */
	protected $_sNum = 0;	
	
	/**
	 * @var integer SHOPITEM 고유번호
	 */
	protected $_siNum = 0;
	
	/**
	 * @var integer SHOPITEM_FILE 고유번호
	 */
	protected $_fNum = 0;	
	
	/**
	 * @var integer SHOPITEM_FILE 인덱스(FILE_ORDER)
	 */
	protected $_fIndex = 0;
	
	/**
	 * @var integer	기획전 고유번호
	 */
	protected $_enNum = 0;
	
	/**
	 * @var integer	카테고리 고유번호
	 */
	protected $_ctNum = 0;	
	
	/**
	 * @var string	이벤트 타입 S:special, G:gift (E:event) 
	 */
	protected $_eventType = '';
	
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
	
	protected $_tbl = 'SHOPITEM';
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = TRUE;
	
	/**
	 * @var integer 파일첨부갯수(여기선 등록된 파일카운트)
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
		$this->load->model(array('item_model', 'shop_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->loginCheck();
		$this->setPrecedeValues();
		
		/* 페이지 정의 */
		switch($this->_uriMethod)
		{
			case 'list':
				$itemListView = 'manage/item/item_list';
				break;
			case 'modilist':
				$itemListView = 'manage/item/item_modi_list';
				break;
			default:
				$itemListView = 'manage/item/item_approval_list';
				break;
		}
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			//Item 관련
			case 'denylist':			
			case 'apprlist':
			case 'modilist'; //수정 요청된 리스트 페이지
			case 'list':
				$this->getItemDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view($itemListView, $data);
				break;
			case 'writeform':
				$this->getShopBaseRowData();
				$this->getShopPolicyGroupRowData();
				$this->getItemCommonCateDataList();				
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/item_write', $data);
				break;
			case 'view':
				break;
			case 'copywriteform': //아이템 복사로 등록하는경우
			case 'copyapprovalwriteform';
			case 'denyupdateform':				
			case 'apprupdateform':
			case 'modiupdateform': //수정 요청된 페이지 보기
			case 'updateform':
				$this->getShopBaseRowData();
				$this->getShopPolicyGroupRowData();
				$this->getItemCommonCateDataList();				
				$this->getItemGroupRowData();
				$this->getGroupCodeDataList();	
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/item_write', $data);
				break;
			case 'copywrite':
			case 'copyapprovalwrite':
			case 'write':
				$this->setItemDataInsert();
				break;
			case 'denyupdate':				
			case 'apprupdate':
			case 'modiupdate':
			case 'update':
				$this->setItemDataUpdate();
				break;
			case 'delete':
				$this->setItemDataDelete();
				break;
			case 'filedelete':
				$this->setItemFileDelete();
				break;		
			case 'modichange': //수정 아이템 상태 변경
				$this->setModiItemDataChange();
				break;				
			case 'change':
				$this->setItemDataChange();
				break;	
			//Special(Event)기획전, Gift 관련
			case 'enlist':
				$this->getEventDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/special_list', $data);
				break;
			case 'enview':
				unset($this->_sendData['fileCnt']);
				$this->_sendData['fileCnt'] = 3;
				$this->getEventRowData();				
				$this->getEventItemDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/special_view', $data);
				break;
			case 'enwriteform':
				unset($this->_sendData['fileCnt']);
				$this->_sendData['fileCnt'] = 3;
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/special_write',  $data);
				break;			
			case 'enupdateform':
				unset($this->_sendData['fileCnt']);
				$this->_sendData['fileCnt'] = 3;
				$this->getEventRowData();				
				$this->getEventItemDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/special_write', $data);
				break;
			case 'enwrite':
				unset($this->_sendData['fileCnt']);
				$this->_sendData['fileCnt'] = 3;
				$this->setEventDataInsert();
				break;
			case 'enupdate':
				unset($this->_sendData['fileCnt']);
				$this->_sendData['fileCnt'] = 3;
				$this->setEventDataUpdate();
				break;	
			case 'enfiledelete':
				$this->setEventFileDelete();
				break;
			case 'endelete':
				$this->setEventDataDelete();
			case 'grpendelete':
				$this->setEventGroupDataDelete();
				break;
			//카테고리 설정
			case 'catelist';
				$this->getItemCategoryDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/cate_list', $data);
				break;
			case 'catewriteform':
				$this->getItemCategoryDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/cate_list', $data);
				break;
			case 'catewrite':
				$this->setItemCategoryDataInsert();				
				break;
			case 'cateupdate':
				$this->setItemCategoryDataUpdate();
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
		
		if (in_array('enno', $this->_arrUri))
		{
			$this->_enNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'enno')));
		}
		$this->_enNum = $this->common->nullCheck($this->_enNum, 'int', 0);
		
		if (in_array('ctno', $this->_arrUri))
		{
			$this->_ctNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ctno')));
		}
		$this->_ctNum = $this->common->nullCheck($this->_ctNum, 'int', 0);		
		
		if (in_array('evtype', $this->_arrUri))
		{
			$this->_eventType = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'evtype')));
		}
		$this->_eventType = $this->common->nullCheck($this->_eventType, 'string', '');		
		
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
		
		$viewYn = $this->input->post_get('viewyn', TRUE);
		if (!empty($viewYn)) $this->_currentParam .= '&viewyn='.$viewYn;
		
		$itemState = $this->input->post_get('itemstate', TRUE);
		if (!empty($itemState)) $this->_currentParam .= '&itemstate='.$itemState;
		
		$itemCate = $this->input->post_get('itemcate', TRUE);
		if (!empty($itemCate)) $this->_currentParam .= '&itemcate='.$itemCate;
		
		$itemName = $this->input->post_get('itemname', TRUE);
		if (!empty($itemName)) $this->_currentParam .= '&itemname='.$itemName;
		
		$itemCode = $this->input->post_get('itemcode', TRUE);
		if (!empty($itemCode)) $this->_currentParam .= '&itemcode='.$itemCode;
		
		$itemTag = $this->input->post_get('itemtag', TRUE);
		if (!empty($itemTag)) $this->_currentParam .= '&itemtag='.$itemTag;
		
		$shopName = $this->input->post_get('shopname', TRUE);
		if (!empty($shopName)) $this->_currentParam .= '&shopname='.$shopName;
		
		$shopUserName = $this->input->post_get('shopusername', TRUE);
		if (!empty($shopUserName)) $this->_currentParam .= '&shopusername='.$shopUserName;
		
		$shopCode = $this->input->post_get('shopcode', TRUE);
		if (!empty($shopCode)) $this->_currentParam .= '&shopcode='.$shopCode;
		
		$eventState = $this->input->post_get('eventstate', TRUE);
		if (!empty($eventState)) $this->_currentParam .= '&eventstate='.$eventState;
		
		$eventTypeTitle = 'Event';
		if ($this->_eventType == 's')
		{
			$eventTypeTitle = '기획전';
		}
		else if ($this->_eventType == 'g')
		{
			$eventTypeTitle = 'Gift';
		}
		
		if ($this->_uLevelType == 'SHOP')
		{
			//샵관리자로 로그인한 경우
			$this->_sNum = $this->common->getSession('shop_num');
		}		
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=item'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= (!empty($this->_eventType)) ? '/evtype/'.$this->_eventType : '';
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
			'sDate' => $sDate,
			'eDate' => $eDate,
			'viewYn' => $viewYn,
			'itemState' => $itemState,
			'itemCate' => $itemCate,
			'itemName' => $itemName,
			'itemCode' => $itemCode,				
			'itemTag' => $itemTag,				
			'shopName' => $shopName,
			'shopUserName' => $shopUserName,
			'shopCode' => $shopCode,
			'eventType' => $this->_eventType,
			'eventTypeTitle' => $eventTypeTitle,
			'eventState' => $eventState,
			'pageMethod' => $this->_uriMethod,
			'sNum' => $this->_sNum,				
			'siNum' => $this->_siNum,
			'enNum' => $this->_enNum,				
			'ctNum' => $this->_ctNum,				
			'tbl' => $this->_tbl,
			'fileCnt' => $this->_fileCnt,
			'isLogin' => $this->common->getIsLogin(),
			'isAdmin' => $this->_isAdmin,				
			'userLevelType' => $this->_uLevelType,				
			'sessionData' => $this->common->getSessionAll(),
			'siteDomain' => $this->common->getDomain()
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
	
	/**
	 * @method name : getItemCateDataList
	 * 아이템등록을 위한 공통 카테고리 
	 * craft shop과 circus 카테고리 모두 포함 (all)
	 * 
	 */
	private function getItemCommonCateDataList()
	{
		$qData = array(
			'searchKey' => 'ALL', 
			'isDelView' => FALSE,
			'isUseNoView' => TRUE, 
			'shopNum' => $this->_sNum,
			'itemNum' => $this->_siNum
		);
		
		$result = $this->item_model->getItemCommonCateDataList($qData);
		$this->_sendData['itCateSet'] = $result;
	}
	
	/**
		@author : asdkjfals
		@crea
		asdlkfjasdlkfjasdklfjasdf
		@para : 
	 */
	private function getItemDataList()
	{	
				
		$this->_data = $this->item_model->getItemDataList($this->_sendData, FALSE);// 조회데이터 _data array에 binding 

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
	 * 아이템 소유 샵정보  
	 * 
	 */
	private function getShopBaseRowData()
	{
		$this->_data['shopBaseSet'] = $this->shop_model->getShopBaseRowData($this->_sNum);
	}
	
	/**
	 * @method name : getItemRowData
	 * 한번에 아이템관련 모든 data 불러오기 
	 * #쓰이지 않음
	 * 
	 */
	private function getItemRowData()
	{
		$this->_data = $this->item_model->getItemRowData($this->_siNum, FALSE);
	}
	
	/**
	 * @method name : getItemBaseRowData
	 * 아이템 기본정보 data 
	 * 
	 */
	private function getItemBaseRowData()
	{
		$uNum = 0; //$this->common->getSession('user_num');
		$this->_data['baseSet'] = $this->item_model->getItemBaseRowData($this->_siNum, $uNum, FALSE);
		
		if ($this->_uriMethod == 'copyapprovalwriteform') //수정 요청 form진입시
		{
			$result = $this->item_model->getPrecedeModiItemRowData($this->_siNum); //선행진행건 확인
			if ($result)
			{
				if (in_array($result['ITEMSTATECODE_NUM'], array(7910, 7920, 7930, 7940)))
				{
					//등록, 요청, 대기, 보류건인 경우
					$this->common->message('동일한 아이템 수정건이 진행중입니다.\\n진행중인 건이 완료되기전 수정을 다시 요청할 수 없습니다.', '', '');
				}
			}
		}		
	}
	
	/**
	 * @method name : getItemFileDataList
	 * 아이템 파일 첨부 data List 
	 * 
	 */
	private function getItemFileDataList()
	{
		$this->_data['fileSet'] = $this->item_model->getItemFileDataList($this->_siNum);
	}
	
	/**
	 * @method name : getItemCateDataList
	 * 아이템에 설정한 카테고리 data List
	 * 
	 */
	private function getItemCateDataList()
	{
		$this->_data['cateSet'] = $this->item_model->getItemCateDataList($this->_siNum);
	}
	
	/**
	 * @method name : getItemTagDataList
	 * 아이템 태그 설정 data list 
	 * 
	 */
	private function getItemTagDataList()
	{
		$this->_data['tagSet'] = $this->item_model->getItemTagDataList($this->_siNum);
	}	
	
	/**
	 * @method name : getItemOptionRowDataList
	 * 아이템 옵션 설정 data List 
	 * 
	 */
	private function getItemOptionRowDataList()
	{
		$this->_data['optSet'] = $this->item_model->getItemOptionRowDataList($this->_siNum);
	}
	
	/**
	 * @method name : getShopStatsRowData
	 * 아이템과 관련된 Craft Shop 통계
	 * 
	 */
	private function getShopStatsRowData()
	{
		$this->_data['shopStatsSet'] = $this->item_model->getShopStatsRowData($this->_sNum);
	}
	
	/**
	 * @method name : getItemStatsRowData
	 * 아이템 통계
	 * 
	 */
	private function getItemStatsRowData()
	{
		$this->_data['itemStatsSet'] = $this->item_model->getItemStatsRowData($this->_siNum);
	}	
	
	/**
	 * @method name : getItemGroupRowData
	 * 아이템 고유번호와 연결된 table 내역 가져오기 
	 * 
	 */
	private function getItemGroupRowData()
	{
		$this->getItemCateDataList();
		$this->getItemBaseRowData();
		$this->getItemFileDataList();
		$this->getItemCateDataList();
		$this->getItemTagDataList();
		$this->getItemOptionRowDataList();
		$this->getShopStatsRowData();
		$this->getItemStatsRowData();
	}
	
	/**
	 * @method name : getValidItemChargeRowData
	 * 적용일자 대비 유효한 수수료 data
	 * CHARGE_TYPE = 'M' 인경우 (MALL)
	 * 주문시 수수료 최종 반영할때 기준샵 조회하여 당시의 수수료로 환산해주어야 하며
	 * ITEM_CHARGE 에도 반영당시 기준샵 수수료율을 update한다
	 * (정확한 수수료율 이력 확인을 위한 절차)
	 * 
	 */
	private function getValidItemChargeRowData()
	{
		$this->_data['chargeSet'] = $this->item_model->getValidItemChargeRowData($this->_siNum);
		$chargeType = $this->_data['chargeSet']['CHARGE_TYPE'];

		if (count($this->_data['chargeSet']) == 0)
		{
			//유효한 수수료 data가 없는 경우 기준샵 수수료를 가져온다			
			$stdShopData = $this->shop_model->getStandardShopPolicyRowData();
			$this->_data['chargeSet'] = array(
				'NUM' => 0,
				'CHARGE_TYPE' => 'M',
				'ITEM_CHARGE' => $stdShopData['ITEM_CHARGE'],
				'PAY_CHARGE' => $stdShopData['PAY_CHARGE'],
				'TAX_CHARGE' => $stdShopData['TAX_CHARGE']
			);			
		}
		else 
		{
			if ($chargeType == 'M')
			{
				$stdShopData = $this->shop_model->getStandardShopPolicyRowData();
				$this->item_model->setValidItemChargeUpdate($this->_data['chargeSet']['NUM'], $stdShopData);
					
				$this->_data['chargeSet'] = array(
					'NUM' => $this->_data['chargeSet']['NUM'],
					'CHARGE_TYPE' => $this->_data['chargeSet']['CHARGE_TYPE'],
					'ITEM_CHARGE' => $stdShopData['ITEM_CHARGE'],
					'PAY_CHARGE' => $stdShopData['PAY_CHARGE'],
					'TAX_CHARGE' => $stdShopData['TAX_CHARGE']
				);
			}			
		}
	}
	
	/**
	 * @method name : getGroupCodeDataList
	 * 관계되는 모든 CODE Data List
	 *
	 */
	private function getGroupCodeDataList()
	{
		$this->_data['refPlCdSet'] = $this->common->getCodeListByGroup('REFUNDPOLICY');		
		$this->_data['itemStCdSet'] = $this->common->getCodeListByGroup('ITEMSTATE');

		//샵아이템정보가 있는 경우 샵아이템에서 설정한 정보 포함하여 카테고리를 가져온다		
		$cateType = ($this->_siNum > 0 || !$this->_isAdmin) ? 'ALL' : 'MALL';	
		$this->_data['mallCateSet'] = $this->item_model->getItemCommonCateDataList(
			array(
				'shopNum' => $this->_sNum,
				'searchKey' => $cateType,
				'isDelView' => FALSE,
				'isUseNoView' => FALSE
			)
		);
		//샵 정책 - 작성자 샵의 정책
		$this->_data['polSet'] = $this->shop_model->getShopPolicyRowData($this->_sNum);
		//기준샵 정책
		$this->_data['stdPolSet'] = $this->shop_model->getStandardShopPolicyRowData();
	}	
	
	/**
	 * @method name : setItemDataInsert
	 * 신규 아이템 등록
	 * 
	 */
	private function setItemDataInsert()
	{
		$itemShopCode = $this->input->post_get('itemshop_code', TRUE);
		//if ($this->item_model->getIsItemShopCodeExist($itemShopCode) && $this->_uriMethod != 'copyapprovalwrite')
		//{
		//	$this->common->message('동일한 샵 아이템코드가 존재합니다.', '-', '');
		//}
		
		if ($this->_sNum == 0)
		{
			$this->common->message('아이템을 등록할 샵정보가 없습니다.\\Craft Shop소유자로 로그인후 다시 이용하세요.', '-', '');
		}
		
		$dummyUser = $this->common->getUserInfo('dummy');	//신규등록시에만 필요		
		$insData = array(
			'pageMethod' => $this->_uriMethod, //아이템 복사로 등록하는경우를 위해
			'copyShopItemNum' => $this->_siNum, //원본 아이템 고유번호(복사, 수정승인시)
			'modiReason' => $this->input->post_get('modi_reason', TRUE),
			'SHOP_NUM' => $this->_sNum,
			'ITEM_NAME' => $this->input->post_get('item_name', TRUE),
			'ITEMSHOP_CODE' => $itemShopCode,
			'ITEMSTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('ITEMSTATE', 'RECEIVE'),
			'OPTION_CONTENT' => $this->input->post_get('opt_content', TRUE),
			'EXPLAIN_CONTENT' => $this->input->post_get('exp_content', TRUE),
			'MAKING_CONTENT' => $this->input->post_get('mak_content', TRUE),
			'REFPOLICY_CONTENT' => ($this->input->post_get('refund_policy', TRUE) =='12020') ? $this->input->post_get('ref_content', TRUE) : '',
			'REFPOLICYCODE_NUM' => $this->input->post_get('refund_policy', TRUE),
			'APPROVALUSER_NUM' => $dummyUser['NUM'],
			'VIEW_YN' => $this->input->post_get('view_yn', TRUE),				
			'PICTURE_YN' => $this->input->post_get('picture_yn', TRUE),
			'SOLESALE_YN' => $this->input->post_get('solesale_yn', TRUE),
			'REPRESENT_YN' => $this->input->post_get('represent_yn', TRUE),				
			'DISCOUNT_YN' => $this->common->nullCheck($this->input->post_get('discount_yn', TRUE), 'str', 'N'),
			'DISCOUNT_PRICE' => $this->common->nullCheck($this->input->post_get('discount_price', TRUE), 'int', 0),
			'OPTION_YN' => $this->common->nullCheck($this->input->post_get('option_yn', TRUE), 'str', 'N'),
			'ITEM_PRICE' => $this->input->post_get('item_price', TRUE),
			'MAXBUY_COUNT' => $this->input->post_get('maxbuy_count', TRUE),
			'STOCKFREE_YN' => $this->input->post_get('stockfree_yn', TRUE),
			'STOCK_COUNT' => $this->input->post_get('stock_count', TRUE),				
			'PAYAFTER_CANCEL_YN' => $this->input->post_get('payafter_cancel_yn', TRUE),
			'PAYAFTER_CANCEL_MEMO' => $this->input->post_get('payafter_cancel_memo', TRUE),
			'MADEAFTER_REFUND_YN' => $this->input->post_get('madeafter_refund_yn', TRUE),
			'MADEAFTER_REFUND_MEMO' => $this->input->post_get('madeafter_refund_memo', TRUE),
			'MADEAFTER_CHANGE_YN' => $this->input->post_get('madeafter_change_yn', TRUE),
			'MADEAFTER_CHANGE_MEMO' => $this->input->post_get('madeafter_change_memo', TRUE),
			'CHARGE_TYPE' => $this->input->post_get('charge_type', TRUE),
			'ITEM_CHARGE' => $this->input->post_get('item_charge', TRUE),
			'PAY_CHARGE' => $this->input->post_get('pay_charge', TRUE),
			'TAX_CHARGE' => $this->input->post_get('tax_charge', TRUE),
			'CHARGETYPE_UPDATE_DATE' => $this->input->post_get('chargetype_update_date', TRUE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$insCate = $this->input->post_get('categrp', FALSE);
		$insTag = $this->input->post_get('tag', TRUE);
		$insOpt = $this->input->post_get('item_opt', FALSE);
		$insCharge = array(
			'CHARGE_TYPE' => $this->input->post_get('charge_type', TRUE),
			'ITEM_CHARGE' => $this->input->post_get('item_charge', TRUE),
			'PAY_CHARGE' => $this->input->post_get('pay_charge', TRUE),
			'TAX_CHARGE' => $this->input->post_get('tax_charge', TRUE),
			'CHARGETYPE_UPDATE_DATE' => $this->input->post_get('chargetype_update_date', TRUE)				
		);
		$result = $this->item_model->setItemDataInsert($insData, $insCate, $insTag, $insOpt, $insCharge, $this->_isUpload);
		
		if ($result > 0)
		{
			if ($this->_uriMethod == 'copyapprovalwrite')
			{
				$this->common->message('수정내용 승인 요청 하였습니다.', '/manage/item_m/modilist', 'top');				
			}
			else 
			{
				$this->common->message('신규 아이템이 생성 되었습니다.', '/manage/item_m/list', 'top');				
			}
		}
		else
		{
			$this->common->message('동일한 아이템 코드가 있습니다.', '-', '');			
		}
	}
	
	private function setItemDataUpdate()
	{
		$itemState = $this->input->post_get('item_state', TRUE);
		$itemStateOrg = $this->input->post_get('item_state_org', TRUE);
		$itemStateMemo = $this->input->post_get('item_state_memo', TRUE);
		$itemStateMemoOrg = $this->input->post_get('item_state_memo_org', TRUE);
		$chargeUpdateDate = $this->input->post_get('chargetype_update_date', TRUE);
		$chargeUpdateDateOrg = $this->input->post_get('chargetype_update_date_org', TRUE);
		$chargeType = $this->input->post_get('charge_type', TRUE);
		$itemCharge = $this->input->post_get('item_charge', TRUE);
		$payCharge  = $this->input->post_get('pay_charge', TRUE);
		$taxCharge = $this->input->post_get('tax_charge', TRUE);
		$chargeTypeOrg = $this->input->post_get('charge_type_org', TRUE);
		$itemChargeOrg = $this->input->post_get('item_charge_org', TRUE);
		$payChargeOrg  = $this->input->post_get('pay_charge_org', TRUE);
		$taxChargeOrg = $this->input->post_get('tax_charge_org', TRUE);	
		$originalItemNum = $this->common->nullCheck($this->input->post_get('org_itemno', TRUE), 'int', 0); //수정요청건인 경우 원본 아이템 고유번호	
		$upData = array(
			'originalItemNum' => $originalItemNum,
			'ITEM_NAME' => $this->input->post_get('item_name', TRUE),
			'ITEMSHOP_CODE' => $this->input->post_get('itemshop_code', TRUE),
			'ITEMSTATECODE_NUM' => $itemState,
			'ITEMSTATE_MEMO' => $itemStateMemo,
			'OPTION_CONTENT' => $this->input->post_get('opt_content', TRUE),
			'EXPLAIN_CONTENT' => $this->input->post_get('exp_content', TRUE),
			'MAKING_CONTENT' => $this->input->post_get('mak_content', TRUE),
			'REFPOLICY_CONTENT' => ($this->input->post_get('refund_policy', TRUE) =='12020') ? $this->input->post_get('ref_content', TRUE) : '',
			'REFPOLICYCODE_NUM' => $this->input->post_get('refund_policy', TRUE),
			'VIEW_YN' => $this->input->post_get('view_yn', TRUE),
			'PICTURE_YN' => $this->input->post_get('picture_yn', TRUE),
			'SOLESALE_YN' => $this->input->post_get('solesale_yn', TRUE),
			'REPRESENT_YN' => $this->input->post_get('represent_yn', TRUE),
			'DISCOUNT_YN' => $this->common->nullCheck($this->input->post_get('discount_yn', TRUE), 'str', 'N'),
			'DISCOUNT_PRICE' => $this->common->nullCheck($this->input->post_get('discount_price', TRUE), 'int', 0),
			'OPTION_YN' => $this->common->nullCheck($this->input->post_get('option_yn', TRUE), 'str', 'N'),
			'ITEM_PRICE' => $this->input->post_get('item_price', TRUE),
			'MAXBUY_COUNT' => $this->input->post_get('maxbuy_count', TRUE),
			'STOCKFREE_YN' => $this->input->post_get('stockfree_yn', TRUE),
			'STOCK_COUNT' => $this->input->post_get('stock_count', TRUE),
			'PAYAFTER_CANCEL_YN' => $this->input->post_get('payafter_cancel_yn', TRUE),
			'PAYAFTER_CANCEL_MEMO' => $this->input->post_get('payafter_cancel_memo', TRUE),
			'MADEAFTER_REFUND_YN' => $this->input->post_get('madeafter_refund_yn', TRUE),
			'MADEAFTER_REFUND_MEMO' => $this->input->post_get('madeafter_refund_memo', TRUE),
			'MADEAFTER_CHANGE_YN' => $this->input->post_get('madeafter_change_yn', TRUE),
			'MADEAFTER_CHANGE_MEMO' => $this->input->post_get('madeafter_change_memo', TRUE),
			'CHARGE_TYPE' => $chargeType,
			'ITEM_CHARGE' => $itemCharge,
			'PAY_CHARGE' => $payCharge,
			'TAX_CHARGE' => $taxCharge,
			'CHARGETYPE_UPDATE_DATE' => $chargeUpdateDate,
			'UPDATE_DATE' => date('Y-m-d H:i:s')
		);
		
		if ($itemStateOrg != $itemState)
		{
			$apprFirstReqDate = $this->input->post_get('appr_firstreq_date', TRUE);
			if ($itemState == 8020)
			{
				$upData['APPROVAL_REQ_DATE'] = date('Y-m-d H:i:s'); //승인요청 일자 update
				if (empty($apprFirstReqDate))
				{
					//최초 승인 요청 일자 update
					$upData['APPROVAL_FIRSTREQ_DATE'] = date('Y-m-d H:i:s');
				}
			}
			
			if ($itemState == 8060)
			{
				if (empty($chargeUpdateDateOrg))
				{
					//최초 아이템 등록시 적용일자 입력을 못하게 함
					//처음 승인이 떨어질때 수수료 적용일자를 오늘날짜로 업데이트함(1번만)
					unset($upData['CHARGETYPE_UPDATE_DATE']);
					$upData = $upData + array(
						'isChargeUpdateDateUpdate' => TRUE,
						'CHARGETYPE_UPDATE_DATE' => date('Y-m-d H:i:s')
					);
				}
				
				$upData = $upData + array(
					'APPROVALUSER_NUM' => $this->common->getSession('user_num'), //승인 처리자
					'APPROVAL_DATE' => date('Y-m-d H:i:s') //승인처리 일자 update
				);
			}
			
			//상태가 변경되는 경우 변경일자 업데이트
			$upData['ITEMSTATE_UPDATE_DATE'] = date('Y-m-d H:i:s');
		}
		
		//히스토리
		$upHisData = array(
			'SHOPITEM_NUM' => $this->_siNum,
			'ITEMSTATECODE_NUM' => (empty($itemState)) ? $this->input->post_get('item_state_org', TRUE) : $itemState,
			'ADMINUSER_NUM' => $this->common->getSession('user_num')
		);
		
		if ($this->_uriMethod == 'update')
		{
			//상태간략 메모내용(메모입력항목이 있을경우)을 히스토리에 기록(SHOPITEM_HISTORY)
			$upHisData['CONTENT'] = ($itemStateMemo != $itemStateMemoOrg) ? $itemStateMemo : 'ITEM 정보 업데이트';
		}
		else if ($this->_uriMethod == 'apprupdate')
		{
			//히스토리에 기록(SHOPITEM_HISTORY)
			$upHisData['CONTENT'] = $this->input->post_get('shop_history_content', TRUE);
		}		

		if (($chargeType != $chargeTypeOrg) || ($itemCharge != $itemChargeOrg) || ($payCharge != $payChargeOrg) || ($taxCharge != $taxChargeOrg))
		{
			//수수료가 변경되는 경우가 있는경우
			$upCharge = array(
				'CHARGE_TYPE' => $this->input->post_get('charge_type', TRUE),
				'ITEM_CHARGE' => $this->input->post_get('item_charge', TRUE),
				'PAY_CHARGE' => $this->input->post_get('pay_charge', TRUE),
				'TAX_CHARGE' => $this->input->post_get('tax_charge', TRUE),
				'CHARGETYPE_UPDATE_DATE' => $this->input->post_get('chargetype_update_date', TRUE)
			);			
		}
		else 
		{
			$upCharge = array();
		}

		$upCate = $this->input->post_get('categrp', FALSE);
		
		$upTag = '';
		if ($this->input->post_get('tag_change_yn', TRUE) == 'Y') 
		{
			$upTag = $this->input->post_get('tag', TRUE);			
		}
		
		$upOpt = $this->input->post_get('item_opt', FALSE);
		
		$result = $this->item_model->setItemDataUpdate(
			$this->_sNum, 
			$this->_siNum, 
			$upData, $upCate, $upTag, $upOpt, $upHisData, $upCharge, $this->_isUpload
		);
		
		if ($result > 0)
		{
			if  ($this->_uriMethod == 'apprupdate')
			{
				$listUrl = '/manage/item_m/apprlist';
			}
			else if  ($this->_uriMethod == 'denyupdate')
			{
				$listUrl = '/manage/item_m/denylist';
			}
			else if  ($this->_uriMethod == 'modiupdate')
			{
				$listUrl = '/manage/item_m/modilist';
			}			
			else 
			{
				$listUrl = '/manage/item_m/list';
			}
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';
			
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setItemChange
	 * 리스트 하단 상태변경 버튼 액션 처리
	 * 
	 */
	private function setItemDataChange()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$method = $this->input->post_get('method', FALSE);
		$selValue = $this->input->post_get('selval', FALSE);
		
		//히스토리
		$insHisData = array(
			'ITEMSTATECODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('ITEMSTATE', 'NONE'),
			'ADMINUSER_NUM' => $this->common->getSession('user_num'),
			'CONTENT' => '관리자에 의해 '.$method.' 변경처리'
		);
		
		$result = $this->item_model->setItemDataChange($method, $selValue, $insHisData);
		
		//if($result > 0)
		//{
			$this->common->message('변경 되었습니다.', $this->_returnUrl, 'top');
		//}
	}
	
	/**
	 * @method name : setModiItemDataChange
	 * 리스트 하단 수정 아이템 상태변경 버튼 액션 처리
	 *
	 */
	private function setModiItemDataChange()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$method = $this->input->post_get('method', FALSE);
		$selValue = $this->input->post_get('selval', FALSE);
	
		//히스토리
		$insHisData = array(
			'ITEMSTATECODE_NUM' => $method,
			'ADMINUSER_NUM' => $this->common->getSession('user_num'),
			'CONTENT' => '수정 승인 요청된 아이템을 '.$this->common->getCodeTitleByCodeNum($method).' 상태로 변경'
		);
		
		$result = $this->item_model->setModiItemDataChange($method, $selValue, $insHisData);
	
		//if($result > 0)
		//{
		$this->common->message('변경 되었습니다.', $this->_returnUrl, 'top');
		//}
	}	
	
	/**
	 * @method name : setItemDataDelete
	 * 아이템 삭제 
	 * 
	 */
	private function setItemDataDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		
		//히스토리
		$insHisData = array(
			'SHOPITEM_NUM' => $this->_siNum,
			'ADMINUSER_NUM' => $this->common->getSession('user_num'),
			'CONTENT' => '아이템 삭제'
		);		
		$result = $this->item_model->setItemDataDelete($this->_siNum, $insHisData);
		
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setItemFileDelete
	 * 아이템 파일첨부 내용 삭제(1건씩) 
	 * 
	 */
	private function setItemFileDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$this->item_model->setItemFileDelete($this->_siNum, $this->_fNum, $this->_fIndex);
		
		$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		//$this->common->message('삭제 되었습니다.', 'reload', 'parent');
	}
	
	/**
	 * @method name : getEventDataList
	 * 기획전 리스트 
	 * 
	 */
	private function getEventDataList()
	{
		$this->_data = $this->item_model->getEventDataList($this->_sendData, FALSE);
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
	 * @method name : getEventItemDataList
	 * 기획전에 등록한 아이템 리스트 
	 * 
	 */
	private function getEventItemDataList()
	{
		$this->_data['enItemSet'] = $this->item_model->getEventItemDataList($this->_enNum);		
	}
	
	/**
	 * @method name : getEventRowData
	 * 기획전(Event) Data 
	 * 
	 */
	private function getEventRowData()
	{
		$this->_data = $this->item_model->getEventRowData($this->_enNum, FALSE);
	}
	
	/**
	 * @method name : setEventDataInsert
	 * 기획전 이벤트 생성
	 * 
	 */
	private function setEventDataInsert()
	{
		//if ($this->_sNum == 0 && strtoupper($this->_eventType) != 'E')
		//{
		//	$this->common->message('아이템을 등록할 샵정보가 없습니다.\\Craft Shop소유자로 로그인후 다시 이용하세요.', '-', '');
		//}
		
		$insData = array(
			'TITLE' => $this->input->post_get('title', TRUE),
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),
			'USER_NAME' => $this->common->getSession('user_name'),
			'USER_NICK' => $this->common->getSession('user_nick'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'START_DATE' => $this->input->post_get('start_date', TRUE),
			'END_DATE' => $this->input->post_get('end_date', TRUE),
			'EVENT_TYPE' => strtoupper($this->_eventType), //S:special, G:gift (E:event) 
			'SHOP_NUM' => (empty($this->_sNum) || $this->_sNum == 0) ? NULL : $this->_sNum,
			'VIEW_YN' => $this->input->post_get('view_yn', TRUE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		if ($this->_eventType == 'e')
		{
			$alwaysYn = $this->input->post_get('always_yn', TRUE);
			$insData['SUMMARY'] = $this->input->post_get('summary', TRUE);
			$insData['W_CONTENT'] = $this->input->post_get('w_content', TRUE);
			$insData['M_CONTENT'] = $this->input->post_get('m_content', TRUE);
			$insData['ALWAYS_YN'] = (!empty($alwaysYn)) ? $alwaysYn : 'N';
		}
	
		$insItem = $this->input->post_get('item', FALSE);
		$result = $this->item_model->setEventDataInsert($insData, $insItem, TRUE);		
	
		$listUrl = '/manage/item_m/enlist/evtype/'.$this->_eventType;
		if ($result > 0)
		{
			$this->common->message('내용이 생성 되었습니다.', $listUrl, 'top');
		}
	}	
	
	/**
	 * @method name : setEventDataUpdate
	 * 기획전 이벤트 update 
	 * 
	 */
	private function setEventDataUpdate()
	{
		$upData = array(
			'TITLE' => $this->input->post_get('title', TRUE),
			'START_DATE' => $this->input->post_get('start_date', TRUE),
			'END_DATE' => $this->input->post_get('end_date', TRUE),
			'VIEW_YN' => $this->input->post_get('view_yn', TRUE),
			'EVENT_TYPE' => strtoupper($this->_eventType), //S:special, G:gift (E:event)
			'orgItemList' => $this->input->post_get('org_item', TRUE) //원본값과 수정값 변화를 판단하기 위한 변수 			
		);
	
		if ($this->_eventType == 'e')
		{
			$alwaysYn = $this->input->post_get('always_yn', TRUE);
			$upData['SUMMARY'] = $this->input->post_get('summary', TRUE);
			$upData['W_CONTENT'] = $this->input->post_get('w_content', TRUE);
			$upData['M_CONTENT'] = $this->input->post_get('m_content', TRUE);
			$upData['ALWAYS_YN'] = (!empty($alwaysYn)) ? $alwaysYn : 'N';
		}
		
		$upItem = $this->input->post_get('item', FALSE);
		$result = $this->item_model->setEventDataUpdate($this->_enNum, $upData, $upItem, TRUE);
	
		$listUrl = '/manage/item_m/enlist/evtype/'.$this->_eventType;		
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? '?cs=shop'.$this->_currentParam : '';
		
		if ($result > 0)
		{
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}
	}

	/**
	 * @method name : setEventDataDelete
	 * 기획전(Event) 개별 삭제 
	 * 
	 */
	private function setEventDataDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$result = $this->item_model->setEventDataDelete($this->_enNum);
		
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setEventGroupDataDelete
	 * 기획전(Event) 다중선택 삭제
	 * 
	 */
	private function setEventGroupDataDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$selValue = $this->input->post_get('selval', FALSE);		
		$result = $this->item_model->setEventGroupDataDelete($selValue);
		
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setEventFileDelete
	 * 기획전(Event) 첨부된 파일 개별삭제 
	 * 
	 */
	private function setEventFileDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$result = $this->item_model->setEventFileDelete($this->_enNum, $this->_fNum, $this->_fIndex);
		
		$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
	}
	
	/**
	 * @method name : getItemCategoryDataList
	 * 카테고리 관리 리스트 
	 * 
	 */
	private function getItemCategoryDataList()
	{
		//$this->_data['shopCateSet'] = $this->item_model->getItemCategoryDataList($this->_ctNum, FALSE);
		$searchType = ($this->_isAdmin) ? 'MALL' : 'SHOP';
		if ($searchType == 'SHOP')
		{
			$this->_sNum = ($this->_sNum > 0) ? $this->_sNum : $this->common->getSession('shop_num');			
		}
		
		$this->_data['shopCateSet'] = $this->item_model->getItemCommonCateDataList(
			array(
				'shopNum' => $this->_sNum,
				'searchKey' => $searchType,
				'isDelView' => FALSE,
				'isUseNoView' => TRUE
			)
		);
		
		if (count($this->_data['shopCateSet']) > 0 && $this->_ctNum == 0)
		{
			//list 초기값
			unset($this->_sendData['ctNum']);
			$this->_sendData['ctNum'] = $this->_data['shopCateSet'][0]['NUM']; 
		}
	}
	
	/**
	 * @method name : setItemCategoryDataInsert
	 * 카테고리 관리 카테고리 생성 
	 * 
	 */
	private function setItemCategoryDataInsert()
	{
		$cateType = ($this->_isAdmin) ? 'M' : 'S';
		$cateListOrder = $this->input->post_get('list_order', FALSE);
		$cateListNum = $this->input->post_get('list_num', FALSE);
		$this->_siNum = ($this->_siNum > 0) ? $this->_siNum : $this->common->getSession('shop_num');
		
		$insData = array(
			'TBL_NUM' => $this->_siNum,
			'CATE_TYPE' => $cateType,
			'CATE_TITLE' => $this->input->post_get('cate_title', FALSE),
			'CATE_MEMO' => $this->input->post_get('cate_memo', FALSE),
			'CATE_ORDER' => $this->input->post_get('cate_order', FALSE),
			'REPRESENT_SHOPITEM_NUM' => $this->input->post_get('rep_shopitem_no', FALSE),
			'USE_YN' => $this->input->post_get('use_yn', FALSE)
		);
		$result = $this->item_model->setItemCategoryDataInsert($insData, $cateListOrder, $cateListNum);
		
		if($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/item_m/catelist', 'top');
		}		
	}
	
	/**
	 * @method name : getItemCategoryDataUpdate
	 * 카테고리 관리 카테고리 update 
	 * 
	 */
	private function setItemCategoryDataUpdate()
	{
		$cateListOrder = $this->input->post_get('list_order', FALSE);
		$cateListNum = $this->input->post_get('list_num', FALSE);
		
		$upData = array(
			'NUM' => $this->input->post_get('cate_no', FALSE),
			'CATE_TITLE' => $this->input->post_get('cate_title', FALSE),
			'CATE_MEMO' => $this->input->post_get('cate_memo', FALSE),
			'CATE_ORDER' => $this->input->post_get('cate_order', FALSE),
			'REPRESENT_SHOPITEM_NUM' => $this->input->post_get('rep_shopitem_no', FALSE),
			'USE_YN' => $this->input->post_get('use_yn', FALSE)
		);
		$result = $this->item_model->setItemCategoryDataUpdate($upData, $cateListOrder, $cateListNum);
		
		//if($result > 0)
		//{
			$this->common->message('처리 되었습니다.', '/manage/item_m/catelist', 'top');
		//}		
	}
}