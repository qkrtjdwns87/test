<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Order_a
 * 
 *
 * @author : Administrator
 * @date    : 2016. 01.
 * @version:
 */
class Order_a extends CI_Controller {

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
	 * @var integer SHOP 고유번호
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
		$this->load->library('c_pp_cli');
		$this->load->model(array('item_model', 'shop_model', 'order_model', 'user_model', 'job_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'cartlist':
				$this->getCartDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/cart', $data);
				break;			
			case 'cartorder':
				$this->setCartToOrder();
				break;
			case 'cartdelete':
				$this->setCartDataDelete();
				break;
			case 'orderform':
				$this->getCartOrderDataList();
				$this->getOrderUserRowData();
				$this->getRecentDeliveryInfoData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/buy', $data);
				break;
			case 'order':
				$this->setOrderDataInsert();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/buy_result', $data);				
				break;
			case 'orderfinish':
				$this->getOrderFinalRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/buy_result', $data);
				break;
			case 'orderfail':
				$this->load->view('app/order/order_error');
				break;
			case 'list': //주문정보
				$this->getOrderDataList();
				$data = array_merge($this->_data, $this->_sendData);
				//$this->load->view('app/order/order_list_old', $data);
				($this->_outformat == 'json') ? $this->getOrderDataListToJson() : $this->load->view('app/order/order_list', $data);				
				break;
			case 'partview': //샵별 주문상세 정보
				$this->getOrderViewDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/order_part_view', $data);
				break;
			case 'cancelreqform'; //취소신청폼
				$this->getRecisionGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/order_cancel_request', $data);
				break;
			case 'refundreqform'; //환불신청폼
				$this->getRecisionGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/order_refund_request', $data);
				break;
			case 'exchangereqform'; //교환신청폼
				$this->getRecisionGroupCodeDataList();
				$this->getOrderViewDataList();
				$data = array_merge($this->_data, $this->_sendData);				
				$this->load->view('app/order/order_exchange_request', $data);
				break;
			case 'cancelreq':
			case 'refundreq':
			case 'exchangereq':
				$this->setOrderStateDataChange();
				break;
			case 'addrsearch':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('app/order/addr_search', $data);				
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
		
		$payType = $this->input->post_get('paytype', FALSE);
		if (!empty($payType)) $this->_currentParam .= '&paytype='.$payType;
		
		$grpPayType = $this->input->post_get('grppaytype', FALSE);
		if (!empty($grpPayType)) $this->_currentParam .= '&grppaytype='.$grpPayType;		
		
		$ordSearchKey = $this->input->post_get('ordsearchkey', TRUE);
		if (!empty($ordSearchKey)) $this->_currentParam .= '&ordsearchkey='.$ordSearchKey;		
		
		$ordSearchWord = $this->input->post_get('ordsearchword', TRUE);
		if (!empty($ordSearchWord)) $this->_currentParam .= '&ordsearchword='.$ordSearchWord;
		
		$deliveryType = $this->input->post_get('deliverytype', FALSE);
		if (!empty($deliveryType)) $this->_currentParam .= '&deliverytype='.$deliveryType;
		
		$invoiceYn = $this->input->post_get('invoiceyn', FALSE);
		if (!empty($invoiceYn)) $this->_currentParam .= '&invoiceyn='.$invoiceYn;
		
		$this->_deviceId = $this->input->post_get('deviceid', TRUE);
		$this->_pushId = $this->input->post_get('pushid', TRUE);
		
		$this->_uLevelType = $this->common->getSessionUserLevelCodeId();
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
		// yong mod
		log_message('debug', 'url : ' . $url);

		//if (!$this->common->getIsLogin(TRUE))
		if ($this->common->nullCheck(get_cookie('usernum'), 'int', 0) == 0)
		{
			//$this->common->message('로그인후 이용하실 수 있습니다.', '/app/user_a/login', 'top');
			$this->common->message('로그인후 이용하실 수 있습니다.', "app_showMenuWindow('로그인', '".$url."');", 'js');
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
	
	/**
	 * @method name : getRecisionGroupCodeDataList
	 * 취소, 환불 사유 groupcode list 
	 * 
	 */
	private function getRecisionGroupCodeDataList()
	{
		$this->_data['reasonCdSet'] = $this->common->getCodeListByGroup('RECISION');
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
		$qData = array(
			'statsSearchKey' => 'today',
			'sNum' => $this->_sNum
		);
		$this->_data['ordStatsToDaySet'] = $this->order_model->getOrderStateStatsDataList($qData);
		
		$qData = array(
			'statsSearchKey' => '',
			'sNum' => $this->_sNum				
		);
		$this->_data['ordStatsSet'] = $this->order_model->getOrderStateStatsDataList($qData);
	}
	
	private function getOrderDataList()
	{
		$this->loginCheck();
		$this->_sendData['uNum'] = get_cookie('usernum'); //$this->common->getSession('user_num');		
		$this->_data = $this->order_model->getOrderDataList($this->_sendData, FALSE);
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
	
	private function getOrderDataListToJson()
	{
		echo $this->common->arrayToJson($this->_data);
	}	
	
	/**
	 * @method name : getOrderViewDataList
	 * 주문상세정보 list Data 
	 * 
	 */
	private function getOrderViewDataList()
	{
		$this->loginCheck();
		$qData = array(
			'ordNum' => $this->_ordNum,
			'ordPartNum' => $this->_ordPtNum,
			'isDelView' => FALSE
		);
		$this->_data['ordSet'] = $this->order_model->getOrderViewDataList($qData);
		log_message('debug', 'joon sql ');
		log_message('debug', 'joon sql ' .$this->db->last_query());

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
	 * 주문상태변경 (취소요청, 환불요청, 교환요청) 
	 * 
	 */
	private function setOrderStateDataChange()
	{
		$this->loginCheck();
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$orderState = $this->input->post_get('orderstate', FALSE);
		$reasonCodeNum = $this->input->post_get('reason_cd', FALSE);
		$reasonContent = $this->input->post_get('reason_content', FALSE);
		$itemNum = $this->input->post_get('item_no', FALSE);
		$orderStateTitle = $this->common->getCodeTitleByCodeNum($orderState);		
		$selValue = $this->_ordPtNum.'|'.$this->common->nullCheck($itemNum, 'int', 0); //구성=[ORDERPART고유번호|아이템고유번호]
		$dummyUser = $this->common->getUserInfo('dummy');
		
		if ($this->_uriMethod == 'cancelreq')
		{
			//주문취소인 경우			
			require_once $_SERVER["DOCUMENT_ROOT"].'/pg/cfg/site_conf_inc.php';
			
			$orderState = $this->common->getCodeNumByCodeGrpNCodeId('ORDSTATE', 'PAYCANCEL');
			$orderStateTitle = $this->common->getCodeTitleByCodeNum($orderState);
			$result = $this->order_model->getOrderBaseRowData(
				array(
					'ordNum' => $this->_ordNum
				)
			);		
			
			$tran_cd = '00200000';
			$trace_no = '';
			$tno = $result['PAYRESULT_ID'];
			$ordr_idxx = $result['ORDER_CODE'];
			$orderConfirmYn = $result['ORDERCONFIRM_YN'];
			$payCode = $result['PAYCODE_NUM'];
			$deliveryCnt = $result['DELIVERY_CNT']; //배송전환된 카운트(1개라도 있으면 유효)
			$ordPartCnt = $result['TOTPART_COUNT'];
			$cust_ip = $this->input->ip_address();
			$mod_type = 'STSC';
			$y_rem_mny = $result['TOTFINAL_AMOUNT']; //남은 금액
			$y_mod_mny = $result['TOTFINAL_AMOUNT']; //취소,또는 환불금액
			$y_mod_desc_cd = 'CA06'; //기타
			$y_mod_desc = '구매자 구매취소';
				
			//PG사 상태변경
			$trace_no = '';
			$this->c_pp_cli->mf_clear();
			$this->c_pp_cli->mf_set_modx_data("tno", $tno);      // KCP 원거래 거래번호
			$this->c_pp_cli->mf_set_modx_data("mod_ip", $cust_ip);      // 변경 요청자 IP
			$this->c_pp_cli->mf_set_modx_data("mod_desc", $y_mod_desc);      // 변경 사유
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

		//히스토리
		$insHisData = array(
			'ORDSTATECODE_NUM' => $orderState,
			'REASONUSER_NUM' => get_cookie('usernum'), //$this->common->getSession('user_num'),
			'REASON_CONTENT' => $orderStateTitle.' 상태로 변경<br /><br />사유:'.$reasonContent,
			'ANSWERUSER_NUM' => $dummyUser['NUM'],
			'RECISIONCODE_NUM' => $reasonCodeNum,
			'REASON_AUTOWRITE_YN' => 'N',
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
		$this->common->message('변경 되었습니다.', $this->_returnUrl, 'parent');
		//}
	}
	
	/**
	 * @method name : setPayinfoUpdate
	 * 주문상세 -> 결제정보 update 
	 * 
	 */
	private function setPayInfoUpdate()
	{	
		$payCode = $this->input->post_get('paycode', FALSE);		
		$resultBankAcoountName = $this->input->post_get('result_bankacoount_name', FALSE);
		$resultBank = $this->input->post_get('result_bank', FALSE);
		$resultBank = (!empty($resultBank)) ? $resultBank : $this->common->getCodeNumByCodeGrpNCodeId('BANK', 'NONE');
		$resultBankAcoount = $this->input->post_get('result_bankacoount', FALSE);
		$payDate = $this->input->post_get('paydate', FALSE);
		$cashReceiptYn = $this->input->post_get('cashreceipt_yn', FALSE);
		$taxBillYn = $this->input->post_get('taxbill_yn', FALSE);
		$refundBankAcoountName = $this->input->post_get('refund_bankacoount_name', FALSE);
		$refundBank = $this->input->post_get('refund_bank', FALSE);
		$refundBankacoount = $this->input->post_get('refund_bankacoount', FALSE);
		$refundDate = $this->input->post_get('refund_date', FALSE);
		$refundAmount = $this->input->post_get('refund_amount', FALSE);
		
		$upData = array(
			'TAXBILL_YN' => $taxBillYn
		);
		
		if (in_array($payCode, array(5520, 5530))) //무통장
		{
			$upData = $upData + array(
				'PAYRESULT_BANKACCOUNT_NAME' => $resultBankAcoountName,
				'PAYRESULT_BANKCODE_NUM' => $resultBank,
				'PAYRESULT_BANKACCOUNT' => $resultBankAcoount,
				'CASHRECEIPT_YN' => $cashReceiptYn,
				'REFBANKACCOUNT_NAME' => $refundBankAcoountName,
				'REFBANKCODE_NUM' => $refundBank,
				'REFBANKACCOUNT' => $refundBankacoount,
				'REFDEPOSIT_DATE' => $refundDate,
				'REFUND_AMOUNT' => $refundAmount
			);	
			
			if (!empty($payDate)) $upData['PAY_DATE'] = $payDate;
		}
		else if (in_array($payCode, array(5510, 5560))) //카드결제,휴대폰
		{
			//카드취소 결과 처리는 PG사 연동후		
		}
		
		$result = $this->order_model->setOrderInfoUpdate($this->_ordNum, $upData);
		
		if($result > 0)
		{
			$this->common->message('변경 되었습니다.', 'reload', 'parent');
		}		
	}
	
	/**
	 * @method name : setUserInfoUpdate
	 * 주문상세 -> 주문자 정보 update
	 * 
	 */
	private function setUserInfoUpdate()
	{
		$mobile1 = $this->input->post_get('order_mobile1', FALSE);
		$mobile2 = $this->input->post_get('order_mobile2', FALSE);
		$mobile3 = $this->input->post_get('order_mobile3', FALSE);
		$mobileEnc = $this->common->sqlEncrypt($mobile1.'-'.$mobile2.'-'.$mobile3, $this->_encKey);
		$orderEmail = $this->input->post_get('order_email', FALSE);
		$orderEmailEnc = $this->common->sqlEncrypt($orderEmail, $this->_encKey);
		$orderContent = $this->input->post_get('order_content', FALSE);
		
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
	 * @method name : getCartDataList
	 * 카트 리스트
	 * 
	 */
	private function getCartDataList()
	{
		$this->loginCheck();
		$this->_sendData['uNum'] = get_cookie('usernum'); //$this->common->getSession('user_num');
		$this->_data = $this->order_model->getCartDataList($this->_sendData, FALSE);
	}
	
	private function getCartOrderDataList()
	{
		$this->loginCheck();
		$uNum = get_cookie('usernum'); //$this->common->getSession('user_num');
		$this->_data = $this->order_model->getCartOrderDataList($uNum);
		$now = DateTime::createFromFormat('U.u', microtime(true));
		$tmpCode = $now->format("ymdHisu");
		$this->_data['orderCode'] = 'OD'.substr($tmpCode, 0 , -4); //신규 주문코드 생성
	}
	
	/**
	 * @method name : setCartToOrder
	 * 카트내용 업데이트 후 주문으로 전환
	 * 
	 */
	private function setCartToOrder()
	{
		$this->loginCheck();
		$this->setCartToOrderDataUpdate();
		
		echo 
			"<script>
		        var IOSframe = document.createElement('iframe');
		        IOSframe.style.display = 'none';
		        IOSframe.src = 'jscall://showPurchaseWindow';
		        document.documentElement.appendChild(IOSframe);
			</script>";
		
		//$this->common->message('', '/app/order_a/orderform', 'parent');
		$this->common->message('', 'parent.app_showPurchaseWindow();', 'js');
	}
	
	/**
	 * @method name : setCartDataUpdate
	 * 카트내용 update(선택사항 업데이트)
	 * 
	 */
	private function setCartDataUpdate()
	{
		$cart = $this->input->post_get('cart', FALSE);
		$result = $this->order_model->setCartDataUpdate($cart);
	}
	
	/**
	 * @method name : setCartDataDelete
	 * 카트 삭제(ajax)
	 * 
	 */
	private function setCartDataDelete()
	{
		$cartNum = $this->input->post_get('crno', FALSE);
		$cartItemNum = $this->input->post_get('critno', FALSE);
		$userNum = get_cookie('usernum'); //$this->common->getSession('user_num');
		echo $this->order_model->setCartDataDelete($cartNum, $cartItemNum, $userNum);
	}
	
	/**
	 * @method name : setCartToOrderDataUpdate
	 * 선택된 카트내용만 order 로 전환 
	 * 
	 */
	private function setCartToOrderDataUpdate()
	{
		$cart = $this->input->post_get('cart', FALSE);
		$cart['uNum'] = get_cookie('usernum'); //$this->common->getSession('user_num');
		$result = $this->order_model->setCartToOrderDataUpdate($cart);		
	}
	
	/**
	 * @method name : getOrderUserRowData
	 * 주문자 정보
	 * 
	 */
	private function getOrderUserRowData()
	{
		$this->_data['userInfo'] = $this->user_model->getUserRowData(
			array(
				'NUM' => get_cookie('usernum')
			)
		);
	}
	
	/**
	 * @method name : getRecentDeliveryInfoData
	 * 최근 배송지 목록 
	 * 
	 */
	private function getRecentDeliveryInfoData()
	{
		$qData = array(
			'uNum' => get_cookie('usernum') //$this->common->getSession('user_num')
		);
		$this->_data['deliveryInfo'] = $this->order_model->getRecentDeliveryInfoData($qData);
	}
	
	/**
	 * @method name : setOrderDataInsert
	 * 주문
	 * 주문코드는 getCartOrderDataList 에서 생성
	 * 
	 */
	private function setOrderDataInsert()
	{
		$this->loginCheck();	
		
		require_once $_SERVER["DOCUMENT_ROOT"].'/pg/cfg/site_conf_inc.php';		
		/* ============================================================================== */
		/* =   지불 결과                                                                = */
		/* = -------------------------------------------------------------------------- = */
		$site_cd          = $this->input->post_get("site_cd", FALSE);      // 사이트 코드
		$req_tx           = $this->input->post_get("req_tx" , FALSE);      // 요청 구분(승인/취소)
		$use_pay_method   = $this->input->post_get("use_pay_method", FALSE);      // 사용 결제 수단
		$bSucc            = $this->input->post_get("bSucc"  , FALSE);      // 업체 DB 정상처리 완료 여부
		/* = -------------------------------------------------------------------------- = */
		$res_cd           = $this->input->post_get("res_cd" , FALSE);      // 결과 코드
		$res_msg          = $this->input->post_get("res_msg", FALSE);      // 결과 메시지
		$res_msg_bsucc    = "";
		/* = -------------------------------------------------------------------------- = */
		$amount           = $this->input->post_get("amount" , FALSE);      // 금액
		$tno              = $this->input->post_get("tno"    , FALSE);      // KCP 거래번호
		$ordr_idxx        = $this->input->post_get("ordr_idxx", FALSE);      // 주문번호
		$good_name        = $this->input->post_get("good_name", FALSE);      // 상품명
		$good_mny         = $this->input->post_get("good_mny" , FALSE);      // 결제 금액
		$buyr_name        = $this->input->post_get("buyr_name", FALSE);      // 구매자명
		$buyr_tel1        = $this->input->post_get("buyr_tel1", FALSE);      // 구매자 전화번호
		$buyr_tel2        = $this->input->post_get("buyr_tel2", FALSE);      // 구매자 휴대폰번호
		$buyr_mail        = $this->input->post_get("buyr_mail", FALSE);      // 구매자 E-Mail
		/* = -------------------------------------------------------------------------- = */
		$app_time         = $this->input->post_get("app_time" , FALSE);      // 승인시간 (공통)
		$pnt_issue        = $this->input->post_get("pnt_issue", FALSE);      // 포인트 서비스사
		/* = -------------------------------------------------------------------------- = */
		$card_cd          = $this->input->post_get("card_cd", FALSE);      // 카드 코드
		$card_name        = $this->input->post_get("card_name", FALSE);      // 카드명
		$app_no           = $this->input->post_get("app_no" , FALSE);      // 승인번호
		$noinf            = $this->input->post_get("noinf"  , FALSE);      // 무이자 여부
		$quota            = $this->input->post_get("quota"  , FALSE);      // 할부개월
		$partcanc_yn      = $this->input->post_get("partcanc_yn", FALSE);      // 부분취소 여부
		/* = -------------------------------------------------------------------------- = */
		$bank_name        = $this->input->post_get("bank_name", FALSE);      // 은행명
		$bank_code        = $this->input->post_get("bank_code", FALSE);      // 은행코드
		/* = -------------------------------------------------------------------------- = */
		$bankname         = $this->input->post_get("bankname" , FALSE);      // 입금할 은행
		$bankcode        = $this->input->post_get("bankcode", FALSE);      // 은행코드
		$depositor        = $this->input->post_get("depositor", FALSE);      // 입금할 계좌 예금주
		$account          = $this->input->post_get("account", FALSE);      // 입금할 계좌 번호
		$va_date          = $this->input->post_get("va_date", FALSE);      // 입금마감시간
		/* = -------------------------------------------------------------------------- = */
		$add_pnt          = $this->input->post_get("add_pnt", FALSE);      // 발생 포인트
		$use_pnt          = $this->input->post_get("use_pnt", FALSE);      // 사용가능 포인트
		$rsv_pnt          = $this->input->post_get("rsv_pnt", FALSE);      // 적립 포인트
		$pnt_app_time     = $this->input->post_get("pnt_app_time", FALSE);      // 승인시간
		$pnt_app_no       = $this->input->post_get("pnt_app_no" , FALSE);      // 승인번호
		$pnt_amount       = $this->input->post_get("pnt_amount" , FALSE);      // 적립금액 or 사용금액
		/* = -------------------------------------------------------------------------- = */
		$commid           = $this->input->post_get("commid" , FALSE);      // 통신사 코드
		$mobile_no        = $this->input->post_get("mobile_no", FALSE);      // 휴대폰 번호
		/* = -------------------------------------------------------------------------- = */
		$tk_van_code      = $this->input->post_get("tk_van_code", FALSE);      // 발급사 코드
		$tk_app_no        = $this->input->post_get("tk_app_no", FALSE);      // 승인 번호
		/* = -------------------------------------------------------------------------- = */
		$cash_yn          = $this->input->post_get("cash_yn", FALSE);      // 현금 영수증 등록 여부
		$cash_authno      = $this->input->post_get("cash_authno", FALSE);      // 현금 영수증 승인 번호
		$cash_tr_code     = $this->input->post_get("cash_tr_code", FALSE);      // 현금 영수증 발행 구분
		$cash_id_info     = $this->input->post_get("cash_id_info", FALSE);      // 현금 영수증 등록 번호
		/* = -------------------------------------------------------------------------- = */
		$escw_yn          = $this->input->post_get("escw_yn", FALSE);      // 에스크로 사용 여부
		$escw_yn          = $this->input->post_get("escw_yn", FALSE);      // 에스크로 사용 여부
		$pay_mod          = $this->input->post_get("pay_mod", FALSE);      // 에스크로 결제처리 모드
		$deli_term        = $this->input->post_get("deli_term", FALSE);      // 배송 소요일
		$bask_cntx        = $this->input->post_get("bask_cntx", FALSE);      // 장바구니 상품 개수
		$good_info        = $this->input->post_get("good_info", FALSE);      // 장바구니 상품 상세 정보
		$rcvr_name        = $this->input->post_get("rcvr_name", FALSE);      // 수취인 이름
		$rcvr_tel1        = $this->input->post_get("rcvr_tel1", FALSE);      // 수취인 전화번호
		$rcvr_tel2        = $this->input->post_get("rcvr_tel2", FALSE);      // 수취인 휴대폰번호
		$rcvr_mail        = $this->input->post_get("rcvr_mail", FALSE);      // 수취인 E-Mail
		$rcvr_zipx        = $this->input->post_get("rcvr_zipx", FALSE);      // 수취인 우편번호
		$rcvr_add1        = $this->input->post_get("rcvr_add1", FALSE);      // 수취인 주소
		$rcvr_add2        = $this->input->post_get("rcvr_add2", FALSE);      // 수취인 상세주소
		/* ============================================================================== */
		
		/* 기타 파라메터 추가 부분 - Start - */
		$param_opt_1     = $this->input->post_get("param_opt_1" , FALSE);      // 기타 파라메터 추가 부분
		$param_opt_2     = $this->input->post_get("param_opt_2" , FALSE);      // 기타 파라메터 추가 부분
		$param_opt_3     = $this->input->post_get("param_opt_3" , FALSE);      // 기타 파라메터 추가 부분
		/* 기타 파라메터 추가 부분 - End -   */
		
		$req_tx_name     = "";
		
		if ( $req_tx == "pay" )
		{
			$req_tx_name = "지불" ;
		}
		else if ( $req_tx == "mod" )
		{
			$req_tx_name = "취소/매입" ;
		}
		
		/* ============================================================================== */
		/* =   가맹점 측 DB 처리 실패시 상세 결과 메시지 설정                           = */
		/* = -------------------------------------------------------------------------- = */

		if ( $req_tx == "pay" )
		{
			// 업체 DB 처리 실패
			if ( $bSucc == "false" )
			{
				if ( $res_cd == "0000" )
				{
					$res_msg_bsucc = "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였습니다. <br> 쇼핑몰로 전화하여 확인하시기 바랍니다." ;
				}
				else
				{
					$res_msg_bsucc = "결제는 정상적으로 이루어졌지만 쇼핑몰에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였으나, <br> <b>취소가 실패 되었습니다.</b><br> 쇼핑몰로 전화하여 확인하시기 바랍니다" ;
				}
				
				$this->common->message('', '/app/order_a/orderfail?errcode='.$res_msg_bsucc, 'self');
			}
			else
			{
				if ($res_cd != "0000")
				{
					//8059 - 결제금액 불일치(위변조 가능성)
					//pp_cli_hub.php 에서 금액불일치시 자동 결제 취소되어 이쪽으로 넘어온다
					$res_cd .= $res_msg; 
					$this->common->message('', '/app/order_a/orderfail?errcode='.$res_cd, 'self');
				}
			}
		}
		
		/* = -------------------------------------------------------------------------- = */
		/* =   가맹점 측 DB 처리 실패시 상세 결과 메시지 설정 끝                        = */
		/* ============================================================================== */
		
		$recName = $rcvr_name;	//$this->input->post_get("rec_name", FALSE);
		$recMobile = $rcvr_tel2;	//$this->input->post_get("rec_mobile", FALSE);
		$recZip = $rcvr_zipx;	//$this->input->post_get("zip", FALSE);
		$recAddr1 = $rcvr_add1;	//$this->input->post_get("addr1", FALSE);
		$recAddr2 = $rcvr_add2;	//$this->input->post_get("addr2", FALSE);
		$recJibun = $param_opt_1;	//$this->input->post_get("addr_jibun", FALSE);
		
		$userEmailEnc = $this->common->sqlEncrypt($buyr_mail, $this->_encKey);
		$userMobile = $buyr_tel2;
		
		if (strlen($userMobile) == 10)
		{
			$userMobile = substr($userMobile, 0, 3).'-'.substr($userMobile, 3, 3).'-'.substr($userMobile, 6, 4);
		}
		else
		{
			$userMobile = substr($userMobile, 0, 3).'-'.substr($userMobile, 3, 4).'-'.substr($userMobile, 7, 4);
		}
		$userMobileEnc = $this->common->sqlEncrypt($userMobile, $this->_encKey);		

		if (strlen($recMobile) == 10)
		{
			$recMobile = substr($recMobile, 0, 3).'-'.substr($recMobile, 3, 3).'-'.substr($recMobile, 6, 4);
		}
		else
		{
			$recMobile = substr($recMobile, 0, 3).'-'.substr($recMobile, 3, 4).'-'.substr($recMobile, 7, 4);
		}
		$recMobileEnc = $this->common->sqlEncrypt($recMobile, $this->_encKey);
		
		$payDate = '';
		$payAutoYn = 'N';
		$payResultCardMonth = ''; //카드 할부 개월수
		$payResultInstall = ''; //무이자 여부
		$payResultCode = ''; //결제후 PG사로 부터 받는 지불수단에 따른 코드값
		$payResultCodeName = ''; //결제후 PG사로 부터 받는 지불수단에 따른 코드값 네임
		$payResultBankAccount = '';
		$payResultBankAccountName = '';
		$payCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'NONE');
		$payBankCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('BANK', 'NONE');
		if ($use_pay_method == "100000000000") //카드
        {
        	$payAutoYn = 'Y';
        	$payResultCode = $card_cd;
        	$payResultCodeName = $card_name;
        	$payResultCardMonth = $quota; //카드 할부 개월수
        	$payResultInstall = $noinf; //무이자 여부
        	$payDate = $app_time;
        	$payCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'CARD');
        }
        else if ($use_pay_method == "010000000000") //계좌이체(무통장)
        {
        	$payAutoYn = 'Y';
        	$payDate = $app_time;
        	$payBankCodeNum = $this->common->getCodeNumByCodeGrpNEtcText('BANK', $bank_code);
        	if ($payBankCodeNum == 12500) //$payBankCodeNum 이 그래도 공란인 경우
        	{
        		$payBankCodeNum = $this->common->getCodeNumByCodeGrpNTitle('BANK', $bank_name);
        	}        	
        	$payResultCode = $bank_code;
        	$payResultCodeName = $bank_name;
        	$payCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'BANK');
        }
        else if ($use_pay_method == "001000000000") //가상계좌
        {
        	$payAutoYn = 'N';
        	$payBankCodeNum = (empty($bankcode)) ? $payBankCodeNum : $this->common->getCodeNumByCodeGrpNEtcText('BANK', $bankcode);
        	if ($payBankCodeNum == 12500) //$payBankCodeNum 이 그래도 공란인 경우
        	{
        		$payBankCodeNum = $this->common->getCodeNumByCodeGrpNTitle('BANK', $bankname);
        	}
        	$payResultCode = $bankcode;
        	$payResultCodeName = $bankname;  
        	$payResultBankAccount = $account;
        	$payResultBankAccountName = $depositor;
        	$payResultBankDeadLine = $va_date;
        	$payCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'DIRECTBANK');
        }        
        else if ($use_pay_method == "000010000000") //휴대폰
        {
        	$payAutoYn = 'Y';
        	$payDate = $app_time;
        	$payResultCode = $commid;
        	$payResultCodeName = '';        	
        	$payCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'MOBILE');
        }        

		//$this->getCartDataList();
		$ordData = array(
			'orderAmount' => $good_mny, //결제 진행시 금액
			'ORDER_CODE' => $ordr_idxx,
			'USER_NUM' => get_cookie('usernum'), //$this->common->getSession('user_num'),
			'PAYCODE_NUM' => $payCodeNum,
			'PAY_DATE' => (!empty($payDate)) ? $payDate : NULL,
			'PAYAUTO_YN' => $payAutoYn,
			'ORDER_NAME' => $buyr_name,
			'ORDER_MOBILE' => $userMobileEnc,
			'ORDER_EMAIL' => $userEmailEnc,
			'RECIPIENT_NAME' => $recName,
			'RECIPIENT_MOBILE' => $recMobileEnc,
			'RECIPIENT_ZIP' => $this->common->sqlEncrypt($recZip, $this->_encKey),
			'RECIPIENT_ADDR1' => $this->common->sqlEncrypt($recAddr1, $this->_encKey),
			'RECIPIENT_ADDR2' => $this->common->sqlEncrypt($recAddr2, $this->_encKey),
			'RECIPIENT_ADDR_JIBUN' => $this->common->sqlEncrypt($recJibun, $this->_encKey),
			'PAYRESULT_ID' => $tno, //PG사 결제 코드
			'PAYRESULT_BANKCODE_NUM' => $payBankCodeNum,
			'PAYRESULT_BANKACCOUNT' => $payResultBankAccount,
			'PAYRESULT_BANKACCOUNT_NAME' => $payResultBankAccountName,
			'PAYRESULT_CARDMONTH' => $payResultCardMonth,
			'PAYRESULT_CARDINSTALL' => $payResultInstall,
			'PAYRESULT_CODE' => $payResultCode,
			'PAYRESULT_CODENAME' => $payResultCodeName,
			'TMP_ORDER_CODE' => $ordr_idxx, //임시부여됐던 주문번호(코드) -> PG주문번호 일치를 위해 임시코드를 실코드로 그대로 적용
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->order_model->setOrderDataInsert($ordData);

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
			$this->common->message('', '/app/order_a/orderfinish/ordno/'.$result, 'parent');
		}
	}
	
	/**
	 * @method name : getOrderFinalRowData
	 * 주문완료시 보여줄 주문 data 
	 * 
	 */
	private function getOrderFinalRowData()
	{
		$this->loginCheck();
		$qData = array(
			'ordNum' => $this->_ordNum,
			'uNum' => get_cookie('usernum') //$this->common->getSession('user_num')
		);
		$this->_data['orderSet'] = $this->order_model->getOrderFinalRowData($qData);
	}
}