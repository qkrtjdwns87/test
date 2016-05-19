<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Order_m
 * 
 *
 * @author : Administrator
 * @date    : 2016. 01.
 * @version:
 */
class Order_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer ORDERS 고유번호
	 */
	protected $_ordNum = 0;	
	
	/**
	 * @var integer ORDERPART 고유번호
	 */
	protected $_ordPtNum = 0;	
	
	/**
	 * @var integer ORDERITEM 고유번호
	 */
	protected $_ordiNum = 0;
	
	/**
	 * @var integer ORDERITEM_OPTION 고유번호
	 */
	protected $_ordoptNum = 0;	
	
	/**
	 * @var integer SHOP 고유번호(샵관리자 로그인시 session으로 _sNum 고정)
	 */
	protected $_sNum = 0;	
	
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
	
	protected $_tbl = 'ORDERS';
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = FALSE;
	
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
		$this->load->library('c_pp_cli');
		$this->load->model(array('item_model', 'shop_model', 'order_model', 'job_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->loginCheck();
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		if (in_array($this->_uriMethod, array('list', 'paylist', 'cancellist', 'refundlist', 'exchangelist', 'deliverylist', 'deliveryfinishlist')))
		{
			if ($this->_uriMethod == 'paylist')
			{
				$this->_sendData['ordStateScopeLow'] = 5060;
				$this->_sendData['ordStateScopeHigh'] = 5080;
				$this->_sendData['ordStateScopeExcept'] = '';
			}
			else if ($this->_uriMethod == 'cancellist')
			{
				$this->_sendData['ordStateScopeLow'] = 5110;
				$this->_sendData['ordStateScopeHigh'] = 5120;
				$this->_sendData['ordStateScopeExcept'] = '';				
			}
			else if ($this->_uriMethod == 'refundlist')
			{
				$this->_sendData['ordStateScopeLow'] = 5130;
				$this->_sendData['ordStateScopeHigh'] = 5180;
				$this->_sendData['ordStateScopeExcept'] = '';
			}
			else if ($this->_uriMethod == 'exchangelist')
			{
				$this->_sendData['ordStateScopeLow'] = 5190;
				$this->_sendData['ordStateScopeHigh'] = 5210;
				$this->_sendData['ordStateScopeExcept'] = '';
			}
			else if ($this->_uriMethod == 'deliverylist')
			{
				$this->_sendData['ordStateScopeLow'] = 5100;
				$this->_sendData['ordStateScopeHigh'] = 5250;
				$this->_sendData['ordStateScopeExcept'] = '5110,5115,5120,5130,5135,5140,5150,5160,5165,5170,5180,5190,5195,5200,5210,5230';
			}
			else if ($this->_uriMethod == 'deliveryfinishlist')
			{
				$this->_sendData['ordStateScopeLow'] = 5230;
				$this->_sendData['ordStateScopeHigh'] = 5260;
				$this->_sendData['ordStateScopeExcept'] = '5240,5250';
			}			
				
			$this->getOrderDataList();
			$this->getGroupCodeDataList();
			$this->getOrderStateStatsDataList();			
		}
		
		switch($this->_uriMethod)
		{
			case 'list':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_list', $data);				
				break;
			case 'paylist':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_pay_list', $data);
				break;
			case 'cancellist':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_cancel_list', $data);
				break;
			case 'refundlist':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_refund_list', $data);
				break;	
			case 'exchangelist':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_exchange_list', $data);
				break;
			case 'deliverylist':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_delivery_list', $data);
				break;
			case 'deliveryfinishlist':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_deliveryfinish_list', $data);
				break;					
			case 'writeform':
				break;
			case 'view':
				break;
			case 'updateform':
				break;
			case 'write':
				$this->setItemDataInsert();
				break;
			case 'update':
				$this->setItemDataUpdate();
				break;
			case 'delete':
				$this->setItemDataDelete();
				break;
			case 'filedelete':
				$this->setItemFileDelete();
				break;				
			case 'change':
				$this->setOrderStateDataChange();
				break;
			//관리자 상세팝업
			case 'ordinfo':
				$this->getOrderViewDataList();
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_info_view', $data);
				break;
			case 'orduserinfo':
				$this->getOrderViewDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_userinfo_view', $data);
				break;				
			case 'ordrecinfo':
				$this->getOrderViewDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_recinfo_view', $data);
				break;				
			case 'ordpayinfo':
				$this->getOrderViewDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_payinfo_view', $data);
				break;	
			case 'ordinfomemo':
				$this->getOrderViewDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_memoinfo_view', $data);
				break;	
			case 'ordinfohistory';
				$this->getOrderHistoryDataList();
				$this->getOrderViewDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/order_historyinfo_view', $data);
				break;			
			case 'payinfoupdate':
				$this->setPayinfoUpdate();
				break;
			case 'userinfoupdate';
				$this->setUserInfoUpdate();
				break;
			case 'recinfoupdate':
				$this->setRecInfoUpdate();
				break;
			case 'paymentupdate': //환불, 취소 처리(PG사 연동)
				$this->setChangePaymentUpdate();
				break;
			//popup
			case 'cancelreqform':
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/cancel_request_pop', $data);
				break;			
			case 'cancelreq':
				$this->setCancelReasonInsert();
				break;
			case 'denyreqform':
				$this->getCancelResonRowData(); //사유를 불러와야 하는 경우
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/cancel_deny_pop', $data);
				break;
			case 'denyreq':
				$this->setCancelDenyUpdate();
				break;			
			case 'deliverywriteform': //배송정보 입력
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/order/delivery_write_pop', $data);				
				break;
			case 'deliverywrite':
				$this->setDeliveryInfoInsert();
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
		
		if (in_array('ordno', $this->_arrUri))
		{
			$this->_ordNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ordno')));
		}
		$this->_ordNum = $this->common->nullCheck($this->_ordNum, 'int', 0);
		
		if (in_array('ordptno', $this->_arrUri))
		{
			$this->_ordPtNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ordptno')));
		}
		$this->_ordPtNum = $this->common->nullCheck($this->_ordPtNum, 'int', 0);
		
		if (in_array('ordino', $this->_arrUri))
		{
			$this->_ordiNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ordino')));
		}
		$this->_ordiNum = $this->common->nullCheck($this->_ordiNum, 'int', 0);
		
		if (in_array('ordoptno', $this->_arrUri))
		{
			$this->_ordoptNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ordoptno')));
		}
		$this->_ordoptNum = $this->common->nullCheck($this->_ordoptNum, 'int', 0);		
		
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
		
		$orderState = $this->input->post_get('orderstate', TRUE);
		if (!empty($orderState)) $this->_currentParam .= '&orderstate='.$orderState;		
		
		$grpOrderState = $this->input->post_get('grporderstate', TRUE);
		if (!empty($grpOrderState)) $this->_currentParam .= '&grporderstate='.$grpOrderState;		
		
		$itemName = $this->input->post_get('itemname', TRUE);
		if (!empty($itemName)) $this->_currentParam .= '&itemname='.$itemName;
		
		$itemCode = $this->input->post_get('itemcode', TRUE);
		if (!empty($itemCode)) $this->_currentParam .= '&itemcode='.$itemCode;
		
		$shopName = $this->input->post_get('shopname', TRUE);
		if (!empty($shopName)) $this->_currentParam .= '&shopname='.$shopName;
		
		$shopCode = $this->input->post_get('shopcode', TRUE);
		if (!empty($shopCode)) $this->_currentParam .= '&shopcode='.$shopCode;
		
		$dateSearchKey = $this->input->post_get('datesearchkey', TRUE);
		if (!empty($dateSearchKey)) $this->_currentParam .= '&datesearchkey='.$dateSearchKey;		
		
		$payType = $this->input->post_get('paytype', TRUE);
		if (!empty($payType)) $this->_currentParam .= '&paytype='.$payType;
		
		$grpPayType = $this->input->post_get('grppaytype', TRUE);
		if (!empty($grpPayType)) $this->_currentParam .= '&grppaytype='.$grpPayType;		
		
		$ordSearchKey = $this->input->post_get('ordsearchkey', TRUE);
		if (!empty($ordSearchKey)) $this->_currentParam .= '&ordsearchkey='.$ordSearchKey;		
		
		$ordSearchWord = $this->input->post_get('ordsearchword', TRUE);
		if (!empty($ordSearchWord)) $this->_currentParam .= '&ordsearchword='.$ordSearchWord;
		
		$deliveryType = $this->input->post_get('deliverytype', TRUE);
		if (!empty($deliveryType)) $this->_currentParam .= '&deliverytype='.$deliveryType;
		
		$invoiceYn = $this->input->post_get('invoiceyn', TRUE);
		if (!empty($invoiceYn)) $this->_currentParam .= '&invoiceyn='.$invoiceYn;
		
		if ($this->_uLevelType == 'SHOP')
		{
			//샵관리자로 로그인한 경우
			$this->_sNum = $this->common->getSession('shop_num');
		}		
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=order'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_ordNum > 0) ? '/ordno/'.$this->_ordNum : '';		
		$this->_currentUrl .= ($this->_ordPtNum > 0) ? '/ordptno/'.$this->_ordPtNum : '';		
		$this->_currentUrl .= ($this->_ordiNum > 0) ? '/ordino/'.$this->_ordiNum : '';
		$this->_currentUrl .= ($this->_ordoptNum > 0) ? '/ordoptno/'.$this->_ordoptNum : '';
		
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
			'orderState' => $orderState,
			'grpOrderState' => $grpOrderState,
			'itemName' => $itemName,
			'itemCode' => $itemCode,
			'shopName' => $shopName,
			'shopCode' => $shopCode,
			'dateSearchKey' => $dateSearchKey,
			'payType' => $payType,
			'grpPayType' => $grpPayType,
			'ordSearchKey' => $ordSearchKey,
			'ordSearchWord' => $ordSearchWord,
			'deliveryType' => $deliveryType,
			'invoiceYn' => $invoiceYn,
			'pageMethod' => $this->_uriMethod,
			'ordNum' => $this->_ordNum,
			'ordPtNum' => $this->_ordPtNum,
			'ordiNum' => $this->_ordiNum,
			'ordoptNum' => $this->_ordoptNum,
			'sNum' => $this->_sNum,				
			'tbl' => $this->_tbl,
			'tblEnc' => $this->common->sqlEncrypt($this->_tbl, $this->_encKey),				
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
	 * @method name : getGroupCodeDataList
	 * 관계되는 모든 CODE Data List
	 *
	 */
	private function getGroupCodeDataList()
	{
		$this->_data['ordStCdSet'] = $this->common->getCodeListByGroup('ORDSTATE');
		$this->_data['payTypeCdSet'] = $this->common->getCodeListByGroup('ORDPAY');
		$this->_data['deliCdSet'] = $this->common->getCodeListByGroup('DELIVERY');
		$this->_data['bankCdSet'] = $this->common->getCodeListByGroup('BANK');
		
		if ($this->_uriMethod == 'cancelreqform')
		{
			$this->_data['cancelCdSet'] = $this->common->getCodeListByGroup('RECISION');
		}
	}
	
	private function getOrderGroupRowData()
	{
		$this->getOrderBaseRowData();
	}	
	
	/**
	 * @method name : getOrderStateStatsDataList
	 * 주문현황(주문상태별 각각의 통계 수치)
	 * 
	 */
	private function getOrderStateStatsDataList()
	{
		if (!empty($this->_sendData['sDate']) && !empty($this->_sendData['eDate']))
		{
			$qData['sDate'] = $this->_sendData['sDate'];			
			$qData['eDate'] = $this->_sendData['eDate'];
			$qData['statsSearchKey'] = 'term';
		}
		else 
		{
			$qData['statsSearchKey'] = 'today';
		}
		
		if (!empty($this->_sNum) && $this->_sNum > 0)
		{
			$qData['sNum'] = $this->_sNum;
		}
		$this->_data['ordStatsToDaySet'] = $this->order_model->getOrderStateStatsDataList($qData);
		
		//통합통계
		//모두 동일하나 searchKey만 다름
		//unset($qData['statsSearchKey']);
		//$qData['statsSearchKey'] = '';
		//$this->_data['ordStatsSet'] = $this->order_model->getOrderStateStatsDataList($qData);
	}
	
	private function getOrderDataList()
	{
		$this->_data = $this->order_model->getOrderDataList($this->_sendData, FALSE);
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
	 * @method name : getOrderViewDataList
	 * 주문상세정보 list Data 
	 * 
	 */
	private function getOrderViewDataList()
	{
		$qData = array(
			'sNum' => $this->_sNum,
			'ordNum' => $this->_ordNum,
			'isDelView' => FALSE
		);
		$this->_data['ordSet'] = $this->order_model->getOrderViewDataList($qData);
	}
	
	/**
	 * @method name : getOrderStateRowData
	 * 주문아이템의 상태 조회(1건) 
	 * 
	 */
	private function getOrderStateRowData()
	{
		$result = $this->item_model->getOrderStateRowData($this->_ordiNum);
	}
	
	/**
	 * @method name : setOrderStateDataChange
	 * 주문상태변경 (일괄변경시)  
	 * 
	 */
	private function setOrderStateDataChange()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$target = $this->input->post_get('target', FALSE);		
		$orderState = $this->input->post_get('orderstate', FALSE);
		$orderStateTitle = $this->common->getCodeTitleByCodeNum($orderState);		
		$selValue = $this->input->post_get('selval', FALSE); //$selValue 구성=[ORDERPART고유번호|아이템고유번호]
		$dummyUser = $this->common->getUserInfo('dummy');

		//히스토리
		$insHisData = array(
			'ORDSTATECODE_NUM' => $orderState,
			'REASONUSER_NUM' => $this->common->getSession('user_num'),
			'REASON_CONTENT' => $orderStateTitle.' 상태로 변경',
			'ANSWERUSER_NUM' => $dummyUser['NUM'],
			'RECISIONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('RECISION', 'NONE'),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->order_model->setOrderStateDataChange($orderState, $selValue, $insHisData);
		
		//통계반영(오늘 일자만)
		$toDate = date('Y-m-d');
		$stateData = array(
			'sDate' => $toDate,
			'eDate' =>$toDate
		);
		$this->job_model->setOrderStateDay($stateData);
		$this->job_model->setShopOrderStateDay($stateData);		
		
		//if($result > 0)
		//{
		$this->common->message('변경 되었습니다.', $this->_returnUrl, $target);
		//}
	}
	
	/**
	 * @method name : setPayinfoUpdate
	 * 주문상세 -> 결제정보 update 
	 * 
	 */
	private function setPayInfoUpdate()
	{	
		/* 기능 축소
		$resultBankAcoountName = $this->input->post_get('result_bankacoount_name', TRUE);
		$resultBank = $this->input->post_get('result_bank', TRUE);
		$resultBank = (!empty($resultBank)) ? $resultBank : $this->common->getCodeNumByCodeGrpNCodeId('BANK', 'NONE');
		$resultBankAcoount = $this->input->post_get('result_bankacoount', TRUE);
		$payDate = $this->input->post_get('paydate', TRUE);
		$refundBankAcoountName = $this->input->post_get('refund_bankacoount_name', TRUE);
		$refundBank = $this->input->post_get('refund_bank', TRUE);
		$refundBankacoount = $this->input->post_get('refund_bankacoount', TRUE);
		$refundDate = $this->input->post_get('refund_date', TRUE);
		$refundAmount = $this->input->post_get('refund_amount', TRUE);
		
		$upData['TAXBILL_YN'] = $taxBillYn;
		*/
		$payCode = $this->input->post_get('paycode', TRUE);		
		$cashReceiptYn = $this->input->post_get('cashreceipt_yn', TRUE);
		$taxBillYn = $this->input->post_get('taxbill_yn', TRUE);
		
		if (in_array($payCode, array(5520, 5530))) //무통장, 가상계좌
		{
			/* 기능 축소
			$upData = $upData + array(
				'PAYRESULT_BANKACCOUNT_NAME' => $resultBankAcoountName,
				'PAYRESULT_BANKCODE_NUM' => $resultBank,
				'PAYRESULT_BANKACCOUNT' => $resultBankAcoount,
				'CASHRECEIPT_YN' => $cashReceiptYn,
				'TAXBILL_YN' => $taxBillYn,
				'REFBANKACCOUNT_NAME' => $refundBankAcoountName,
				'REFBANKCODE_NUM' => $refundBank,
				'REFBANKACCOUNT' => $refundBankacoount,
				'REFDEPOSIT_DATE' => $refundDate,
				'REFUND_AMOUNT' => $refundAmount
			);	
			
			$upData = array(
				'CASHRECEIPT_YN' => $cashReceiptYn,
				'TAXBILL_YN' => $taxBillYn					
			);
			
			$result = $this->order_model->setOrderInfoUpdate($this->_ordNum, $upData);
			
			*/						
			
			//if (!empty($payDate)) $upData['PAY_DATE'] = $payDate;
		}
		else if (in_array($payCode, array(5510, 5560))) //카드결제,휴대폰
		{
			//카드취소 결과 처리는 PG사 연동후		
		}
		
		//$result = $this->order_model->setOrderInfoUpdate($this->_ordNum, $upData);
		
		$upData = array(
			'CASHRECEIPT_YN' => $cashReceiptYn,
			'TAXBILL_YN' => $taxBillYn
		);
			
		$result = $this->order_model->setOrderInfoUpdate($this->_ordNum, $upData);
		
		//if($result > 0)
		//{
			$this->common->message('변경 되었습니다.', 'reload', 'parent');
		//}		
	}
	
	/**
	 * @method name : setUserInfoUpdate
	 * 주문상세 -> 주문자 정보 update
	 * 
	 */
	private function setUserInfoUpdate()
	{
		$mobile1 = $this->input->post_get('order_mobile1', TRUE);
		$mobile2 = $this->input->post_get('order_mobile2', TRUE);
		$mobile3 = $this->input->post_get('order_mobile3', TRUE);
		$mobileEnc = $this->common->sqlEncrypt($mobile1.'-'.$mobile2.'-'.$mobile3, $this->_encKey);
		$orderEmail = $this->input->post_get('order_email', TRUE);
		$orderEmailEnc = $this->common->sqlEncrypt($orderEmail, $this->_encKey);
		$orderContent = $this->input->post_get('order_content', TRUE);
		
		$upData = array(
			'ORDER_MOBILE' => $mobileEnc,
			'ORDER_EMAIL' => $orderEmailEnc,
			'ORDER_CONTENT' => $orderContent
		);
		
		$result = $this->order_model->setOrderInfoUpdate($this->_ordNum, $upData); 
		
		if($result > 0)
		{
			$this->common->message('변경 되었습니다.', 'reload', 'parent');
		}		
	}
	
	/**
	 * @method name : setRecInfoUpdate
	 * 주문상세 -> 수령인 정보 update
	 * 
	 */
	private function setRecInfoUpdate()
	{
		$recName = $this->input->post_get('rec_name', TRUE);
		$mobile1 = $this->input->post_get('rec_mobile1', TRUE);
		$mobile2 = $this->input->post_get('rec_mobile2', TRUE);
		$mobile3 = $this->input->post_get('rec_mobile3', TRUE);
		$mobileEnc = $this->common->sqlEncrypt($mobile1.'-'.$mobile2.'-'.$mobile3, $this->_encKey);
		$zip = $this->input->post_get('rec_zip', TRUE);
		$addr1 = $this->input->post_get('rec_addr1', TRUE);
		$addr2 = $this->input->post_get('rec_addr2', TRUE);
		$addrJibun = $this->input->post_get('rec_addr_jibun', TRUE);
		$zipEnc = $this->common->sqlEncrypt($zip, $this->_encKey);
		$addr1Enc = $this->common->sqlEncrypt($addr1, $this->_encKey);
		$addr2Enc = $this->common->sqlEncrypt($addr2, $this->_encKey);
		$addrJibunEnc = $this->common->sqlEncrypt($addrJibun, $this->_encKey);
		
		$upData = array(
			'RECIPIENT_NAME' => $recName,
			'RECIPIENT_MOBILE' => $mobileEnc,
			'RECIPIENT_ZIP' => $zipEnc,
			'RECIPIENT_ADDR1' => $addr1Enc,
			'RECIPIENT_ADDR2' => $addr2Enc,
			'RECIPIENT_ADDR_JIBUN' => $addrJibunEnc
		);
		
		$result = $this->order_model->setOrderInfoUpdate($this->_ordNum, $upData);
		
		if($result > 0)
		{
			$this->common->message('변경 되었습니다.', 'reload', 'parent');
		}
	}
	
	/**
	 * @method name : getOrderHistoryDataList
	 * 주문 히스토리 내역 
	 * 
	 */
	private function getOrderHistoryDataList()
	{
		$this->_data = $this->order_model->getOrderHistoryDataList($this->_sendData, FALSE);
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
	 * @method name : getCancelResonRowData
	 * 히스토리의 가장 최근 사유등록 내용
	 * REASON_AUTOWRITE_YN = 'N' 인것만 
	 * orderstate별 
	 * 
	 */
	private function getCancelResonRowData()
	{
		$this->_data['reaSet'] = $this->order_model->getCancelResonRowData($this->_sendData, FALSE);
	}
	
	/**
	 * @method name : setCancelReasonInsert
	 * 히스토리에 사유 등록(insert만)
	 * 자동이 아닌 수동으로 입력 
	 * 
	 */
	private function setCancelReasonInsert()
	{
		$dummyUser = $this->common->getUserInfo('dummy');
		$insData = array(
			'ORDERPART_NUM' => $this->_ordPtNum,
			'ORDSTATECODE_NUM' => $this->input->post_get('orderstate', TRUE),
			'RECISIONCODE_NUM' => $this->input->post_get('recision_code', TRUE),
			'REASON_CONTENT' => $this->input->post_get('reason_content', TRUE),
			'REASON_AUTOWRITE_YN' => 'N', //사유작성이 자동인지 혹은 수동으로 작성됐는지 여부
			'REASONUSER_NUM' => $this->common->getSession('user_num'),
			'ANSWERUSER_NUM' => $dummyUser['NUM'],
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->order_model->setCancelReasonInsert($this->_ordNum, $this->_ordPtNum, $insData);
		
		if($result > 0)
		{
			$this->common->message('처리 되었습니다.', 'reload', 'parent.parent');
		}		
	}
	
	/**
	 * @method name : setCancelDenyUpdate
	 * 불가사유 등록
	 * 등록후 신청상태에서 불가상태로 변경 
	 * 
	 */
	private function setCancelDenyUpdate()
	{
		$hisNumOrg = $this->input->post_get('hisno_org', TRUE); //답변을 달 사유원본 고유번호
		$orderState = $this->input->post_get('orderstate', TRUE);
		$currentOrdStateTitle = $this->common->getCodeTitleByCodeNum($orderState); 
		$dummyUser = $this->common->getUserInfo('dummy');
		
		if (in_array($orderState, array(5110, 5130, 5160, 5190)))
		{
			//불가상태로 변경
			switch($orderState)
			{
				case 5110:
					$orderState = 5115;
					break;
				case 5130:
					$orderState = 5135;
					break;
				case 5160:
					$orderState = 5165;
					break;
				case 5190:
					$orderState = 5195;
					break;
			}
		}
		
		$changeOrdStateTitle = $this->common->getCodeTitleByCodeNum($orderState);
		
		//답변내용 업데이트
		$upData = array(
			'ORDSTATECODE_NUM' => $orderState,
			'ANSWER_CONTENT' => $this->input->post_get('deny_reason', TRUE),
			'ANSWERUSER_NUM' => $this->common->getSession('user_num'),
			'ANSWER_AUTOWRITE_YN' => 'N', //답변작성이 자동인지 혹은 수동으로 작성됐는지 여부
			'ANSWER_DATE' => date('Y-m-d H:i:s')		
		);
		
		//상태변경 ORDERPART 업데이트
		$upPtData = array(
			'ORDSTATECODE_NUM' => $orderState,
			'ORDSTATE_UPDATE_DATE' => date('Y-m-d H:i:s'),
			'UPDATE_DATE' => date('Y-m-d H:i:s')
		);		

		//상태변경에 따른 히스토리 추가
		$hisContent = '주문상태를 '.$currentOrdStateTitle.' 에서 '.$changeOrdStateTitle.' 상태로 변경';
		$insHisData = array(
			'ORDSTATECODE_NUM' => $orderState,
			'REASON_CONTENT' => $hisContent,
			'ORDERPART_NUM' => $this->_ordPtNum,
			'REASONUSER_NUM' => $this->common->getSession('user_num'),
			'ANSWERUSER_NUM' => $dummyUser['NUM'],
			'RECISIONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('RECISION', 'NONE'),
			'REMOTEIP' => $this->input->ip_address()
		);		
		
		$result = $this->order_model->setCancelDenyUpdate(
			$hisNumOrg, 
			$this->_ordNum, 
			$this->_ordPtNum, 
			$upData,
			$upPtData,
			$insHisData
		);
		
		if($result > 0)
		{
			$this->common->message('처리 되었습니다.\\n*변경내역에서 답변 내용 및 원문을 확인할 수 있습니다.', 'reload', 'parent.parent');
		}		
	}
	
	/**
	 * @method name : setDeliveryInfoInsert
	 * 택배정보 입력(1건)
	 * PG사에 택배정보 전송
	 * 
	 */
	private function setDeliveryInfoInsert()
	{
		require_once $_SERVER["DOCUMENT_ROOT"].'/pg/cfg/site_conf_inc.php';
		
		$tno = $this->input->post_get('tno', FALSE);
		$invoiceNo = $this->input->post_get('invoiceno', FALSE);
		$deliveryCodeNum = $this->input->post_get('deliverytype', FALSE);
		$deliveryCodeTitle = $this->common->getCodeTitleByCodeNum($deliveryCodeNum);
		$result = $this->order_model->getOrderBaseRowData(array('ordNum' => $this->_ordNum));
		
		$deliveryCnt = $result['DELIVERY_CNT']; //배송정보 기록한 카운트
		
		//주문번호에 의해 통보됨으로 한주문건안에 샵별 송장번호 입력시 2건째부터는 PG update가 필요치 않음
		if ($deliveryCnt == 0 && $result) 
		{
			$tno = $result['PAYRESULT_ID'];
			$ordr_idxx = $result['ORDER_CODE'];
			
			//PG사 배송상태변경
			$tran_cd = '00200000';
			$trace_no = '';
			$cust_ip = $this->input->ip_address();
			$this->c_pp_cli->mf_clear();
			$this->c_pp_cli->mf_set_modx_data("tno", $tno);      // KCP 원거래 거래번호
			$this->c_pp_cli->mf_set_modx_data("mod_ip", $cust_ip);      // 변경 요청자 IP
			$this->c_pp_cli->mf_set_modx_data("mod_desc", '배송정보 변경');      // 변경 사유
			$this->c_pp_cli->mf_set_modx_data('mod_type', 'STE1');      // 원거래 변경 요청 종류
			$this->c_pp_cli->mf_set_modx_data("deli_numb", $invoiceNo );      // 운송장 번호
			$this->c_pp_cli->mf_set_modx_data("deli_corp", $deliveryCodeTitle );      // 택배 업체명
			$this->c_pp_cli->mf_do_tx(
				$trace_no,
				$g_conf_home_dir,
				$g_conf_site_cd,
				$g_conf_site_key,
				$tran_cd,
				"",
				$g_conf_gw_url,
				$g_conf_gw_port,
				"payplus_cli_slib",
				$ordr_idxx,
				"",
				$g_conf_log_level,
				0,
				0,
				$g_conf_log_dir
			); // 응답 전문 처리

			$res_cd  = $this->c_pp_cli->m_res_cd;  // 결과 코드
			$res_msg = $this->c_pp_cli->m_res_msg; // 결과 메시지
			$res_msg =  iconv("EUC-KR", "UTF-8", $res_msg);
			if ($res_cd != '0000')
			{
				$this->common->message('PG배송정보 update 실패\\nCode : '.$res_cd.'\\n'.$res_msg, 'reload', 'parent.parent');
			}
		}
		else
		{
			$this->common->message('데이터 오류로 처리되지 않았습니다.', 'reload', 'parent.parent');
		}
		
		$upData = array(
			'DELIVERYCODE_NUM' => $this->input->post_get('deliverytype', FALSE),
			'INVOICE_WRITE_DATE' => date('Y-m-d H:i:s'),
			'INVOICE_NO' => $this->input->post_get('invoiceno', FALSE)
		);
		
		$result = $this->order_model->setDeliveryInfoInsert($this->_ordNum, $this->_ordPtNum, $upData);
		if($result > 0)
		{
			$this->common->message('처리 되었습니다.', 'reload', 'parent.parent');
		}			
	}
	
	private function setChangePaymentUpdate()
	{
		require_once $_SERVER["DOCUMENT_ROOT"].'/pg/cfg/site_conf_inc.php';
		
		$bankacoountName = $this->input->post_get('bankacoount_name', FALSE);
		$bankCodeNum = $this->input->post_get('bank', FALSE);
		$bankAcoount = $this->input->post_get('bankacoount', FALSE);
		$type = $this->input->post_get('type', FALSE); //cancel or refund
		$amount = $this->input->post_get('amount', FALSE);
		$date = $this->input->post_get('date', FALSE);
		$type = $this->input->post_get('type', FALSE); //cancel or refund

		$result = $this->order_model->getOrderBaseRowData(
			array(
				'ordNum' => $this->_ordNum
			)
		);

		$vcnt_yn = 'N';
		$mod_account = $y_refund_account = $mod_depositor = $y_refund_nm = $mod_bankcode  = $y_bank_code = '';
		if ($type == 'refund' || $type == 'refundall' || $type == 'refundonce' || $type == 'cancelonce')
		{
			$mod_account = $y_refund_account = str_replace('-', '', $bankAcoount);
			$mod_depositor = $y_refund_nm = $bankacoountName;
			$bankCodeNum = str_replace('undefined', '', $bankCodeNum);
			$mod_bankcode  = $y_bank_code = (!empty($bankCodeNum)) ? $this->common->getEtcTitleByCodeNum($bankCodeNum) : '';
		}
		
		if ($result)
		{
			$tno = $result['PAYRESULT_ID'];
			$ordr_idxx = $result['ORDER_CODE'];
			$orderConfirmYn = $result['ORDERCONFIRM_YN'];
			$payCode = $result['PAYCODE_NUM'];
			$deliveryCnt = $result['DELIVERY_CNT']; //배송전환된 카운트(1개라도 있으면 유효)
			$ordPartCnt = $result['TOTPART_COUNT'];
			$cust_ip = $this->input->ip_address();
			$mod_type = '';
		
			if ($payCode == 5510)  //card
			{
				//카드는 취소 또는 부분취소만 가능
				if ($type == 'cancelpart')
				{
					$mod_type = 'STE9_CP'; //구매확인 후 부분취소
				}
				else if ($type == 'cancelonce') //한번에 모두 취소
				{
					if ($orderConfirmYn == 'Y')
					{
						$mod_type = 'STE9_C'; //구매확인 후 취소
					}
					else
					{
						if ($deliveryCnt == 0)
						{
							$mod_type = 'STSC'; //배송전 취소
						}
						else
						{
							$mod_type = 'STE4'; //배송후 취소
						}						
					}
				}
			}
			else if ($payCode == 5520) //무통장
			{
				//구매확인 후 부분환불없음
				if ($type == 'cancelall')
				{
					if ($orderConfirmYn == 'Y')
					{
						$mod_type = 'STE9_A'; //구매확인 후 취소
					}
				}
				else if ($type == 'cancelpart')
				{
					$mod_type = 'STE9_AP'; //구매확인 후 부분취소
				}
				else if ($type == 'refund')
				{
					$mod_type = 'STE9_AR'; //구매확인 후 환불(부분환불(X))
				}
				else if ($type == 'cancelonce') //한번에 모두 취소
				{
					if ($deliveryCnt == 0)
					{
						$mod_type = 'STSC'; //배송전 취소
					}
					else
					{
						$mod_type = 'STE4'; //배송후 취소
					}
				}				
			}
			else if ($payCode == 5530) //가상계좌
			{
				//구매확인 후 취소 없음
				if ($type == 'refundall')
				{
					$mod_type = 'STE9_V'; //구매확인 후 환불
				}
				else if ($type == 'refund')
				{
					//부분환불
					$mod_type = 'STE9_VP'; //구매확인 후 부분환불
				}
				else if ($type == 'cancelonce') //한번에 모두 취소
				{
					if ($deliveryCnt == 0)
					{
						$mod_type = 'STE2'; //배송전 취소
					}
					else
					{
						$mod_type = 'STE4'; //배송후 취소
					}
				}					
			}
			else if ($payCode == 5560) //휴대폰 결제 (매뉴얼로 봤을때 취소 여부가 명확치 않음)
			{
				if ($deliveryCnt == 0)
				{
					$mod_type = 'STE2'; //배송전 취소
				}
				else
				{
					$mod_type = 'STE4'; //배송후 취소
				}
			}
				
			$y_rem_mny = $result['TOTFINAL_AMOUNT']; //남은 금액
			$y_mod_mny = $amount; //취소,또는 환불금액
			$y_mod_desc_cd = 'CA06'; //기타
			$y_mod_desc = '가맹점-결제상태 변경';
			
			//PG사 상태변경
			$trace_no = '';
			$this->c_pp_cli->mf_clear();
			$this->c_pp_cli->mf_set_modx_data("tno", $tno);      // KCP 원거래 거래번호
			$this->c_pp_cli->mf_set_modx_data("mod_ip", $cust_ip);      // 변경 요청자 IP
			$this->c_pp_cli->mf_set_modx_data("mod_desc", $y_mod_desc);      // 변경 사유
			//$this->c_pp_cli->mf_set_modx_data('mod_type', $mod_type);      // 원거래 변경 요청 종류
			
			if( $mod_type == "STE9_C"  || $mod_type == "STE9_CP" ||
					$mod_type == "STE9_A"  || $mod_type == "STE9_AP" ||
					$mod_type == "STE9_AR" || $mod_type == "STE9_V"  ||
					$mod_type == "STE9_VP" )
			{
				$tran_cd = "70200200";
				$this->c_pp_cli->mf_set_modx_data( "mod_type"    , "STE9"         );
				$this->c_pp_cli->mf_set_modx_data( "mod_desc_cd" , $y_mod_desc_cd );
				$this->c_pp_cli->mf_set_modx_data( "mod_desc"    , $y_mod_desc    );
			
				if( $mod_type == "STE9_C" )
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STSC"            );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC03"          );
				}
				else if( $mod_type == "STE9_CP" )
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STPC"            );
					$this->c_pp_cli->mf_set_modx_data( "part_canc_yn"    , "Y"               );
					$this->c_pp_cli->mf_set_modx_data( "rem_mny"         , $y_rem_mny        );
					$this->c_pp_cli->mf_set_modx_data( "amount"          , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC03"          );
					//$this->c_pp_cli->mf_set_modx_data( "tax_flag"        , "TG03"            ); // 복합과세 부분취소
					//$this->c_pp_cli->mf_set_modx_data( "mod_tax_mny"     , $y_tax_mny        ); // 공급가 부분취소 금액
					//$this->c_pp_cli->mf_set_modx_data( "mod_free_mny"    , $y_free_mod_mny   ); // 비과세 부분취소 금액
					//$this->c_pp_cli->mf_set_modx_data( "mod_vat_mny"     , $y_add_tax_mny    ); // 부가세 부분취소 금액
				}
				else if( $mod_type == "STE9_A")
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STSC"            );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC03"          );
				}
				else if( $mod_type == "STE9_AP")
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STPC"            );
					$this->c_pp_cli->mf_set_modx_data( "part_canc_yn"    , "Y"               );
					$this->c_pp_cli->mf_set_modx_data( "rem_mny"         , $y_rem_mny        );
					$this->c_pp_cli->mf_set_modx_data( "amount"          , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC04"          );
					//$this->c_pp_cli->mf_set_modx_data( "tax_flag"        , "TG03"            ); // 복합과세 부분취소
					//$this->c_pp_cli->mf_set_modx_data( "mod_tax_mny"     , $y_tax_mny        ); // 공급가 부분취소 금액
					//$this->c_pp_cli->mf_set_modx_data( "mod_free_mny"    , $y_free_mod_mny   ); // 비과세 부분취소 금액
					//$this->c_pp_cli->mf_set_modx_data( "mod_vat_mny"     , $y_add_tax_mny    ); // 부가세 부분취소 금액
				}
				else if( $mod_type == "STE9_AR")
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STHD"            );
					$this->c_pp_cli->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC04"          );
					$this->c_pp_cli->mf_set_modx_data( "mod_bankcode"    , $y_bank_code      );
					$this->c_pp_cli->mf_set_modx_data( "mod_account"     , $y_refund_account );
					$this->c_pp_cli->mf_set_modx_data( "mod_depositor"   , $y_refund_nm      );
				}
				else if( $mod_type == "STE9_V")
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STHD"            );
					$this->c_pp_cli->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC00"          );
					$this->c_pp_cli->mf_set_modx_data( "mod_bankcode"    , $y_bank_code      );
					$this->c_pp_cli->mf_set_modx_data( "mod_account"     , $y_refund_account );
					$this->c_pp_cli->mf_set_modx_data( "mod_depositor"   , $y_refund_nm      );
				}
				else if( $mod_type == "STE9_VP")
				{
					$this->c_pp_cli->mf_set_modx_data( "sub_mod_type"    , "STPD"            );
					$this->c_pp_cli->mf_set_modx_data( "mod_mny"         , $y_mod_mny        );
					$this->c_pp_cli->mf_set_modx_data( "rem_mny"         , $y_rem_mny        );
					$this->c_pp_cli->mf_set_modx_data( "mod_sub_type"    , "MDSC04"          );
					$this->c_pp_cli->mf_set_modx_data( "mod_bankcode"    , $y_bank_code      );
					$this->c_pp_cli->mf_set_modx_data( "mod_account"     , $y_refund_account );
					$this->c_pp_cli->mf_set_modx_data( "mod_depositor"   , $y_refund_nm      );
					//$this->c_pp_cli->mf_set_modx_data( "tax_flag"        , "TG03"            ); // 복합과세 부분취소
					//$this->c_pp_cli->mf_set_modx_data( "mod_tax_mny"     , $y_tax_mny        ); // 공급가 부분취소 금액
					//$this->c_pp_cli->mf_set_modx_data( "mod_free_mny"    , $y_free_mod_mny   ); // 비과세 부분취소 금액
					//$this->c_pp_cli->mf_set_modx_data( "mod_vat_mny"     , $y_add_tax_mny    ); // 부가세 부분취소 금액
					$this->c_pp_cli->mf_set_modx_data( "part_canc_yn"    , "Y"               );
				}
			}
			else
			{
				$tran_cd = "00200000";
				if ( $mod_type == "STE1")                                                                  // 상태변경 타입이 [배송요청]인 경우
				{
					$this->c_pp_cli->mf_set_modx_data( "deli_numb", $_POST[ "deli_numb" ] );      // 운송장 번호
					$this->c_pp_cli->mf_set_modx_data( "deli_corp", $_POST[ "deli_corp" ] );      // 택배 업체명
				}
				if ( $mod_type == "STE2" || $mod_type == "STE4" )                                       // 상태변경 타입이 [즉시취소] 또는 [취소]인 계좌이체, 가상계좌의 경우
				{
					if ( $vcnt_yn == "Y" )
					{
						$this->c_pp_cli->mf_set_modx_data( "refund_account", $mod_account    );  // 환불수취계좌번호
						$this->c_pp_cli->mf_set_modx_data( "refund_nm",      $mod_depositor  );  // 환불수취계좌주명
						$this->c_pp_cli->mf_set_modx_data( "bank_code",      $mod_bankcode      );  // 환불수취은행코드
					}
				}
			}			

			$this->c_pp_cli->mf_set_modx_data( "mod_type",   $mod_type);      // 원거래 변경 요청 종류
			
			/*
			echo '<br/>tno='.$tno;
			echo '<br/>type='.$type;
			echo '<br/>mod_type='.$mod_type;
			echo '<br/>mod_account='.$mod_account;
			echo '<br/>mod_depositor='.$mod_depositor;
			echo '<br/>mod_bankcode='.$mod_bankcode;
			echo '<br/>trace_no='.$trace_no;
			echo '<br/>g_conf_home_dir='.$g_conf_home_dir;
			echo '<br/>g_conf_site_cd='.$g_conf_site_cd;
			echo '<br/>g_conf_site_key='.$g_conf_site_key;
			echo '<br/>tran_cd='.$tran_cd;
			echo '<br/>g_conf_gw_url='.$g_conf_gw_url;
			echo '<br/>g_conf_gw_port='.$g_conf_gw_port;
			echo '<br/>ordr_idxx='.$ordr_idxx;
			echo '<br/>order_no='.$this->_ordNum;
			echo '<br/>g_conf_log_path='.$g_conf_log_path;
			echo '<br/>rem_mny='.$y_rem_mny;
			echo '<br/>mod_mny='.$y_mod_mny;			
			echo '<br/>vcnt_yn='.$vcnt_yn;
			*/
			
			$this->c_pp_cli->mf_do_tx(
				$trace_no,
				$g_conf_home_dir,
				$g_conf_site_cd,
				$g_conf_site_key,
				$tran_cd,
				"",
				$g_conf_gw_url,
				$g_conf_gw_port,
				"payplus_cli_slib",
				$ordr_idxx,
				$cust_ip,
				$g_conf_log_level,
				0,
				0,
				$g_conf_log_path
			); // 응답 전문 처리
		
			$res_cd  = $this->c_pp_cli->m_res_cd;  // 결과 코드
			$res_msg = $this->c_pp_cli->m_res_msg; // 결과 메시지
			$res_msg =  iconv("EUC-KR", "UTF-8", $res_msg);
			//echo '<br/><br/>res_cd='.$res_cd;
			//echo '<br/>res_msg='.$res_msg;
			//exit;
			if ($res_cd != '0000')
			{
				$this->common->message('PG결제정보 update 실패\\nCode : '.$res_cd.'\\n'.$res_msg, 'reload', 'parent');
			}
		}		
		else
		{
			//$this->common->message('데이터 오류로 처리되지 않았습니다.', 'reload', 'parent');
		}

		if (strpos($type, 'refund') !== FALSE)
		{
			$ordStatCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDSTATE', 'REFUNDFINISH');
			$upData = array(
				'REFPAYCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'BANK'),
				'REFBANKACCOUNT_NAME' => $refundBankAcoountName,
				'REFBANKCODE_NUM' => $bankCodeNum,
				'REFBANKACCOUNT' => $refundBankacoount,
				'REFUND_END_DATE' => $date,
				'REFUND_PRICE' => $amount,
				'REFUND_YN' => 'Y',
				'ORDSTATECODE_NUM' => $ordStatCodeNum,
				'ORDSTATE_UPDATE_DATE' => date('Y-m-d H:i:s')
			);			
		}
		else
		{
			$ordStatCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDSTATE', 'CANCELFINISH');
			$upData = array(
				'CANCEL_END_DATE' => date('Y-m-d H:i:s'),
				'CANCEL_PRICE' => $amount,
				'CANCEL_YN' => 'Y',
				'ORDSTATECODE_NUM' => $ordStatCodeNum,
				'ORDSTATE_UPDATE_DATE' => date('Y-m-d H:i:s')					
			);			
		}
		$ordStatCodeTitle = $this->common->getCodeTitleByCodeNum($ordStatCodeNum);
		$dummyUser = $this->common->getUserInfo('dummy');
		
		//상태변경에 따른 히스토리 추가
		$hisContent = '주문상태를 '.$ordStatCodeTitle.' 상태로 변경';
		$insHisData = array(
			'ORDSTATECODE_NUM' => $ordStatCodeNum,
			'REASON_CONTENT' => $hisContent,
			'REASONUSER_NUM' => $this->common->getSession('user_num'),
			'ANSWERUSER_NUM' => $dummyUser['NUM'],
			'RECISIONCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('RECISION', 'NONE'),
			'REMOTEIP' => $this->input->ip_address()
		);		

		$result = $this->order_model->setChangePaymentUpdate(
			$type, 
			$this->_ordNum, 
			$this->_ordPtNum, 
			$upData, 
			$insHisData
		);
		
		if ($result > 0)
		{
			//통계반영(오늘 일자만)
			$toDate = date('Y-m-d');
			$stateData = array(
				'sDate' => $toDate,
				'eDate' =>$toDate
			);
			$this->job_model->setOrderStateDay($stateData);
			$this->job_model->setShopOrderStateDay($stateData);
			
			$this->common->message('처리되었습니다.', 'reload', 'parent');
		}
	}
}