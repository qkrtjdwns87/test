<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Item_a
 * 
 *
 * @author : Administrator
 * @date    : 2016. 03.
 * @version:
 */
class Item_a extends CI_Controller {

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
	 * @var string	리스트 정렬 타입
	 */
	protected $_orderType = '';	
	
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
		$this->load->model(array('item_model', 'shop_model', 'review_model', 'comment_model', 'user_model'));

		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			//Item 관련
			case 'list':
				$this->getItemDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/item/item_list', $data);
				break;
			case 'view':
				$this->getShopBaseRowData();
				$this->getShopBestItemDataList();
				$this->getRecommendItemDataList();
				$this->getShopPolicyGroupRowData();
				$this->getItemCommonCateDataList();
				$this->getItemGroupRowData();
				$this->getGroupCodeDataList();
				$this->getReviewDataList();
				$this->getCommentDataList();
				$this->setItemReadCountUpdate();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/item/item_view', $data);
				break;
			case 'viewshare': // 공유화면
				$this->getShopBaseRowData();
				$this->getShopBestItemDataList();
				$this->getRecommendItemDataList();
				$this->getShopPolicyGroupRowData();
				$this->getItemCommonCateDataList();
				$this->getItemGroupRowData();
				$this->getGroupCodeDataList();
				$this->getReviewDataList();
				$this->getCommentDataList();
				$this->setItemReadCountUpdate();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/item/item_view_share', $data);
				break;
			//Special(Event)기획전, Gift 관련
			case 'enlist':
				$this->getEventDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/event/event_list', $data);
				break;
			case 'enview':
				$this->getEventRowData();
				$this->getEventItemDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/event/event_view', $data);
				break;
			//구매후기
			case 'reviewlist':
				$this->getReviewDataList();
				$data = array_merge($this->_data, $this->_sendData);
				($this->_outformat == 'json') ? $this->getReviewDataListToJson() : $this->load->view('app/item/review_list', $data);
				break;	
			case 'reviewwrite':
				$this->setReviewDataInsert();
				break;
			case 'reviewupdate':
				$this->setReviewDataUpdate();
				break;
			case 'reviewdelete':
				$this->setReviewDataDelete();
				break;	
			//한줄 흔적 남기기
			case 'commentlist':
				$this->getCommentDataList();
				$data = array_merge($this->_data, $this->_sendData);
				($this->_outformat == 'json') ? $this->getCommentDataListToJson() : $this->load->view('app/item/comment_list', $data);				
				break;
			case 'commentwrite':
				($this->_outformat == 'json') ? $this->setCommentDataInsertToJson() : $this->setCommentDataInsert();
				break;	 
			case 'commentdelete':
				($this->_outformat == 'json') ? $this->setCommentDataDeleteToJson() : $this->setCommentDataDelete();
				break;
			//플래그 
			case 'flag':
				$this->setFlag();
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
		
		if (in_array('ordtype', $this->_arrUri))
		{
			$this->_orderType = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ordtype')));
		}
		$this->_orderType = $this->common->nullCheck($this->_orderType, 'string', '');		
		
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

		$this->_deviceId = $this->input->post_get('deviceid', TRUE);
		$this->_pushId = $this->input->post_get('pushid', TRUE);
		
		$this->_uLevelType = $this->common->getSessionUserLevelCodeId();
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
			'orderType' => $this->_orderType,
			'pageMethod' => $this->_uriMethod,
			'sNum' => $this->_sNum,				
			'siNum' => $this->_siNum,
			'enNum' => $this->_enNum,				
			'ctNum' => $this->_ctNum,				
			'tbl' => $this->_tbl,
			'fileCnt' => $this->_fileCnt,
			'isLogin' => ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0) ? FALSE : TRUE, //$this->common->getIsLogin(),
			'isAdmin' => $this->_isAdmin,				
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
			//$this->common->message('로그인후 이용하실 수 있습니다.', '/app/user_a/login', 'top');
			$this->common->message('로그인후 이용하실 수 있습니다.', "app_showMenuWindow('로그인', '".$url."');", 'js');
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
	
	private function getItemDataList()
	{
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
	 * @method name : getItemBaseRowData
	 * 아이템 기본정보 data 
	 * 
	 */
	private function getItemBaseRowData()
	{
		$uNum = get_cookie('usernum'); //$this->common->getSession('user_num');
		$this->_data['baseSet'] = $this->item_model->getItemBaseRowData($this->_siNum, $uNum, FALSE);	
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
	 * @method name : getShopBestItemDataList
	 * 샵 대표 Item 내용
	 *
	 */
	private function getShopBestItemDataList()
	{
		 $result = $this->shop_model->getShopBestItemDataList($this->_sNum, FALSE);
		 $this->_data['shopBestItemSet'] = (count($result) > 0) ? $result['recordSet'] : array();
	}	
	
	/**
	 * @method name : getRecommendItemDataList
	 * 추천 아이템(동일 카테고리)
	 * 
	 */
	private function getRecommendItemDataList()
	{
		$qData = array(
			//'sNum' => $this->_sNum,
			'siNum' => $this->_siNum,
			'userNum' => get_cookie('usernum'), //$this->common->getSession('user_num'), //로그인한 회원 플래그 확인
			'listCount' => 12,
			'searchKey' => 'category',
			'isValidData' => TRUE,
		);
		
		if (!isset($this->_data['cateSet'])) $this->getItemCateDataList();
		$cateNum = '';
		foreach ($this->_data['cateSet'] as $rs):
			$cateNum = $rs['CATE_NUM'].',';
		endforeach;
		$cateNum = (strlen($cateNum) > 0) ? substr($cateNum, 0, -1) : '';
		$qData['cateNum'] = $cateNum;
		$result = $this->item_model->getRecommendItemDataList($qData);
		$this->_data['recommItemTotCnt'] = $result['rsTotalCount'];

		// 20150511 yong mod - 추천 아이템 우선 제거함
		// $this->_data['recommItemSet'] = array_chunk($result['recordSet'], 4); //4개씩 분할
		$this->_data['recommItemSet'] = null;
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
		//$this->getShopStatsRowData();
		//$this->getItemStatsRowData();
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
	 * @method name : setItemReadCountUpdate
	 * 아이템 조회수 증가 
	 * 
	 */
	private function setItemReadCountUpdate()
	{
		$this->item_model->setItemReadCountUpdate($this->_siNum);
	}

	/**
	 * @method name : getEventDataList
	 * 기획전 리스트 
	 * 
	 */
	private function getEventDataList()
	{
		unset($this->_sendData['listCount']);
		$this->_sendData['listCount'] = 20; //리스트수 재정의
		$this->_sendData['viewYn'] = 'Y'; //게시중인것만
		$this->_sendData['alwaysYn'] = 'Y'; //상시진행포함
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
	 * @method name : getReviewDataList
	 * 구매후기 리스트 
	 * 
	 */
	private function getReviewDataList()
	{
		unset($this->_sendData['listCount']);
		unset($this->_sendData['currentPage']);
		$this->_sendData['listCount'] = ($this->_uriMethod == 'reviewlist') ? 10 : 5; //리스트수 재정의
		$this->_sendData['currentPage'] = ($this->_uriMethod == 'reviewlist') ? $this->_currentPage : 1; //리스트수 재정의		
		
		$maxNum = $this->input->post_get('maxNo', FALSE);
		$this->_sendData['maxNum'] = $this->common->nullCheck($maxNum, 'int', 0);
		
		$result = $this->review_model->getReviewDataList($this->_sendData, FALSE);
		$this->_data['reviewRsSet'] = $result['recordSet'];
		$this->_data['reviewRsTotCnt'] = $result['rsTotalCount'];
		//페이징으로 보낼 데이터
		/*
		$pgData = array(
			'rsTotalCount' => $this->_data['reviewRsTotCnt'],
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentUrl' => $this->_currentUrl,
			'currentParam' => $this->_currentParam
		);
	
		$this->_data['pagination'] = $this->common->listAdminPagingUrl($pgData);
		*/
	}
	
	private function getReviewDataListToJson()
	{
		exit($this->common->arrayToJson($this->_data));
	}	
	
	/**
	 * @method name : setReviewDataInsert
	 * 구매 후기 작성
	 *
	 */
	private function setReviewDataInsert()
	{
		$this->loginCheck();
		
		$userInfo = $this->common->getUserInfo('num', get_cookie('usernum'));
		
		if ($userInfo['USTATECODE_NUM'] == 950)	//($this->common->getSession('user_state') == 950)
		{
			$this->common->message('패널티가 부과된 사용자입니다.\\n글을 작성하실 수 없습니다.', '-', '');
		}
		
		if ($userInfo['USTATECODE_NUM'] == 940)	//($this->common->getSession('user_state') == 940)
		{
			$this->common->message('14세미만 승인 절차 진행중인 사용자입니다.\\n글을 작성하실 수 없습니다.', '-', '');
		}		
		
		if ($this->common->getIsBlackUserIP(get_cookie('usernum'), $this->input->ip_address()))
		{
			$this->common->message('IP :'.$this->input->ip_address().'는 블랙리스트 ip입니다.\\n글을 작성하실 수 없습니다.', '-', '');
		}
		
		$content = $this->input->post_get('review_content', TRUE);
		$ordItemNum = $this->common->nullCheck($this->input->post_get('orditemno', FALSE), 'int', 0);
		$insData = array(
			'USER_NUM' => get_cookie('usernum'),
			'USER_ID' => $userInfo['USER_ID'],
			'USER_NAME' => $userInfo['USER_NAME'],
			'USER_EMAIL' => $userInfo['USER_EMAIL'],
			'CONTENT' => $content,
			'ORDERITEM_NUM' => ($ordItemNum > 0) ? $ordItemNum : NULL, //주문아이템 고유번호
			'SHOPITEM_NUM' => $this->input->post_get('itemno', FALSE), //아이템 고유번호
			'SCORE' => $this->input->post_get('score', FALSE), //별점 (최대5점)
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$result = $this->review_model->setReviewDataInsert(
			$insData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		$listUrl = '/manage/review_m/list';
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
	
		if ($result > 0)
		{
			$this->common->message('등록 되었습니다.', $listUrl, 'parent');
		}
	}
	
	/**
	 * @method name : setReviewDataUpdate
	 * 구매후기 수정
	 *
	 */
	private function setReviewDataUpdate()
	{
		$this->loginCheck();
		$upData = array(
			'CONTENT' => $this->input->post_get('review_content', FALSE),
			'SHOPITEM_NUM' => $this->input->post_get('itemno', FALSE), //아이템 고유번호
			'SCORE' => $this->input->post_get('score', FALSE), //별점 (최대5점)
		);
	
		$result = $this->review_model->setReviewDataUpdate(
			$this->_rvNum,
			$upData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		if ($result > 0)
		{
			$listUrl = '/manage/review_m/list';
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
				
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}
	}
	
	/**
	 * @method name : setReviewDataDelete
	 * 구매후기 삭제
	 *
	 */
	private function setReviewDataDelete()
	{
		$this->loginCheck();
		$result = $this->review_model->setReviewDataDelete($this->_rvNum);
	
		$listUrl = '/manage/review_m/list';
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
	
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');
	}	
	
	/**
	 * @method name : getCommentDataList
	 * 댓글(흔적남기기) 리스트
	 *
	 */	
	private function getCommentDataList()
	{
		unset($this->_sendData['listCount']);
		unset($this->_sendData['currentPage']);
		$this->_sendData['listCount'] = ($this->_uriMethod == 'commentlist') ? 10 : 5; //리스트수 재정의
		$this->_sendData['currentPage'] = ($this->_uriMethod == 'commentlist') ? $this->_currentPage : 1; //리스트수 재정의
		$maxNum = $this->input->post_get('maxNo', FALSE);
		$qData = array(
			'tblInfo' => 'SHOPITEM_COMMENT',
			'tNum' => $this->_siNum,
			'currentPage' => $this->_sendData['currentPage'],
			'listCount' => $this->_sendData['listCount'],
			'maxNum' => $this->common->nullCheck($maxNum, 'int', 0)
		);
		$result = $this->comment_model->getCommentDataList($qData, FALSE);
		$this->_data['commentRsSet'] = $result['recordSet'];
		$this->_data['commentRsTotCnt'] = $result['rsTotalCount'];
		
		//페이징으로 보낼 데이터
		/*
		$pgData = array(
				'rsTotalCount' => $this->_data['rsTotalCount'],
				'listCount' => $this->_listCount,
				'currentPage' => $this->_currentPage,
				'currentUrl' => $this->_currentUrl,
				'currentParam' => $this->_currentParam
		);
	
		$this->_data['pagination'] = $this->common->listAdminPagingUrl($pgData);
		*/
	}	
	
	private function getCommentDataListToJson()
	{
		exit($this->common->arrayToJson($this->_data));
	}	
	
	/**
	 * @method name : setCommentDataInsert
	 * 댓글(흔적남기기) 게시글 작성
	 *
	 */
	private function setCommentDataInsert()
	{
		$this->loginCheck();
		$itemNum = $this->common->nullCheck($this->input->post_get('itemno', TRUE), 'int', 0);
		$userInfo = $this->common->getUserInfo('num', get_cookie('usernum'));
		$brdContent = $this->input->post_get('brd_content', TRUE);
		
		//글쓰기 제약 사항
		$userInfo = $this->common->getUserInfo('num', get_cookie('usernum'));
		if ($userInfo['USTATECODE_NUM'] == 950)
		{
			$this->common->message('패널티부과 대상자입니다. 글을 작성하실수 없습니다.', '-', '');
		}
		
		if ($userInfo['USTATECODE_NUM'] == 940)
		{
			$this->common->message('14세미만 대상자입니다. 글을 작성하실수 없습니다.', '-', '');			
		}
		
		if ($this->common->getIsBlackUserIP(get_cookie('usernum'), $this->input->ip_address()))
		{
			$this->common->message('블랙리스트 ip입니다. 글을 작성하실수 없습니다.', '-', '');			
		}
		
		$arrCheck = $this->common->abuseWordCheck($brdContent);
		if ($arrCheck['isChecked'])
		{
			$word = mb_substr($arrCheck['checkedWord'], 0, 1, 'UTF-8').'**';
			$this->common->message('금지어가 있습니다('.$word.'). 글을 작성하실수 없습니다.', '-', '');
		}		
		
		$insData = array(
			'TBLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM_COMMENT'),
			'TBL_NUM' => ($itemNum > 0) ? $itemNum : $this->_siNum,
			'USER_NUM' => get_cookie('usernum'),
			'USER_ID' => $userInfo['USER_ID'],
			'USER_NAME' => $userInfo['USER_NAME'],
			'USER_EMAIL' => $userInfo['USER_EMAIL'],
			'CONTENT' => $brdContent,
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$result = $this->comment_model->setCommentDataInsert(
			$insData,
			$this->config->item('board_thread_interval'),
			FALSE	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		$listUrl = '/app/item_a/commentwrite/sno/'.$sNum.'/sino/'.$siNum;
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
	
		if ($result > 0)
		{
			$this->common->message('등록 되었습니다.', 'parent.location.reload();', 'js');
		}
	}
	
	private function setCommentDataInsertToJson()
	{
		if ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0) exit($this->common->arrayToJson(array('result' => -1)));
		$itemNum = $this->common->nullCheck($this->input->post_get('itemno', TRUE), 'int', 0);
		$userInfo = $this->common->getUserInfo('num', get_cookie('usernum'));
		$brdContent = $this->input->post_get('brd_content', TRUE);
		
		//글쓰기 제약 사항
		$userInfo = $this->common->getUserInfo('num', get_cookie('usernum'));
		if ($userInfo['USTATECODE_NUM'] == 950)
		{
			exit($this->common->arrayToJson(array('result' => 0, 'message' => '패널티부과 대상자입니다. 글을 작성하실수 없습니다.')));
		}
		
		if ($userInfo['USTATECODE_NUM'] == 940)
		{
			exit($this->common->arrayToJson(array('result' => 0, 'message' => '14세미만 대상자입니다. 글을 작성하실수 없습니다.')));
		}
		
		if ($this->common->getIsBlackUserIP(get_cookie('usernum'), $this->input->ip_address()))
		{
			exit($this->common->arrayToJson(array('result' => 0, 'message' => '블랙리스트 ip입니다. 글을 작성하실수 없습니다.')));
		}
		
		$arrCheck = $this->common->abuseWordCheck($brdContent);
		if ($arrCheck['isChecked'])
		{
			$word = mb_substr($arrCheck['checkedWord'], 0, 1, 'UTF-8').'**';
			exit($this->common->arrayToJson(array('result' => 0, 'message' => '금지어가 있습니다('.$word.'). 글을 작성하실수 없습니다.')));			
		}
		
		$insData = array(
			'TBLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM_COMMENT'),
			'TBL_NUM' => ($itemNum > 0) ? $itemNum : $this->_siNum,
			'USER_NUM' => get_cookie('usernum'),
			'USER_ID' => $userInfo['USER_ID'],
			'USER_NAME' => $userInfo['USER_NAME'],
			'USER_EMAIL' => $userInfo['USER_EMAIL'],
			'CONTENT' => $brdContent,
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$result = $this->comment_model->setCommentDataInsert(
			$insData,
			$this->config->item('board_thread_interval'),
			FALSE	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		$result = array(
			'result' => $result,
			'commentNo' => $result,
			'userEmail' => $this->common->sqlDecrypt($userInfo['USER_EMAIL'], $this->_encKey),	
			'createDate' => date('Y-m-d H:i:s'),
			'content' => $brdContent,
			'profileImg' => get_cookie('profileimg') //$this->common->getSession('profileimg')
		);
		
		exit($this->common->arrayToJson($result));
	}	
	
	/**
	 * @method name : setCommentDataDelete
	 * 댓글(흔적남기기) 리스트 1건 삭제
	 *
	 */
	private function setCommentDataDelete()
	{
		$this->loginCheck();
		$comtNum = $this->common->nullCheck($this->input->post_get('comtno', TRUE), 'int', 0);
		$result = $this->comment_model->setCommentDataDelete($comtNum, TRUE);
	
		$listUrl = '/app/item_a/commentwrite/sno/'.$sNum.'/sino/'.$siNum;
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
	
		$this->common->message('삭제 되었습니다.', 'parent.location.reload();', 'js');
	}	
	
	private function setCommentDataDeleteToJson()
	{
		if (!$this->common->getIsLogin()) exit($this->common->arrayToJson(array('result' => -1)));
		
		$comtNum = $this->common->nullCheck($this->input->post_get('comtno', TRUE), 'int', 0);
		$result = $this->comment_model->setCommentDataDelete($comtNum, TRUE, $this->_outformat);
		
		exit($this->common->arrayToJson(array('result' => $result)));
	}	
	
	/**
	 * @method name : setFlag
	 * 플래그 처리(item 플래그 처리) 
	 * ajax 처리시 최종 결과 echo로 처리 (datatype=json)
	 * 
	 */
	private function setFlag()
	{
		$this->loginCheck();
		$fromType = $this->common->nullCheck($this->input->post_get('fromtype', TRUE), 'str', 'item');
		$uniqNum = $this->input->post_get('uniqno', FALSE);			
		$highNum = $this->input->post_get('highno', TRUE);
		
		$result = $this->common->setFlag($fromType, $uniqNum, $highNum);
		//$this->common->message('', "parent.flagingEnd('".$fromType."', ".$result.", '".$uniqNum."', '".$highNum."');", 'js');
		exit($this->common->arrayToJson(array('result' => $result)));
	}
}