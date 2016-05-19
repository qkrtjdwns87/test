<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Main_m
 * 
 *
 * @author : Administrator
 * @date    : 2016. 02.
 * @version:
 */
class Message_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer MESSAGE 고유번호
	 */
	protected $_msgNum = 0;
	
	/**
	 * @var integer 대화리스트 상한선
	 * 페이징 리스트시 신규생성대화와의 중복 방지
	 * ajax 리스팅 일때만 필요
	 */
	protected $_maxMsgNum = 0;	
	
	/**
	 * @var integer 연결되는 메시지 그룹(원본번호)
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 */
	protected $_msgGrpNum = 0;
	
	/**
	 * @var integer SHOP 고유번호(샵관리자 로그인시 session으로 _sNum 고정)
	 */
	protected $_sNum = 0;
	
	/**
	 * @var integer MESSAGE TYPE (1:1, 일반메시지)
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 */
	protected $_msgType = 0;
	
	/**
	 * @var string listview 에서 리스팅 해줄 날짜
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 */
	protected $_msgToDate = '';
	
	/**
	 * @var integer 대화가 연결되는 경우 깊이
	 * 파라메터와 겹치는 부분이 있어 uri로 운영
	 * DEPTH는 원글에 쌓여진 ORDER와 같은 역할이나
	 * 현재는 원글(DEPTH = 0)인것만을 판단하므로 1로 고정함
	 * 
	 */
	protected $_msgDepth = 0;	
	
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
	
	protected $_tbl = 'MESSAGE';
	
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
		$this->load->model(array('message_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->loginCheck();
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'list': //전체 리스트
			case 'listuser': //써커스와 회원과의 메시지 리스트
			case 'listusershop': //샵과 회원과의 메시지 리스트
			case 'listshop': //써커스와 샵과의 메시지 리스트
				$this->getMessageDataViewList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/message/message_list', $data);
				break;
			case 'listview': //전체 대화리스트 
			case 'listviewuser': //써커스와 회원과의 메시지 대화 리스트
			case 'listviewusershop': //샵과 회원과의 메시지 대화 리스트
			case 'listviewshop': //써커스와 샵과의 메시지 대화 리스트
				$this->getMessageDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/message/message_view_list', $data);
				break;				
			case 'writeform':
			case 'writeformuser':
			case 'writeformusershop':				
			case 'writeformshop':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/message/message_write', $data);
				break;
			case 'writeuserformpop':				
			case 'writeshopformpop':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/message/message_write_pop', $data);
				break;				
			case 'write';
			case 'writeuser':
			case 'writeusershop':				
			case 'writeshop':
			// 대화 보기 리스트에서 메시지 작성된 경우
			case 'writeview':
			case 'writeviewuser':
			case 'writeviewusershop':
			case 'writeviewshop':				
			//팝업에서 작성된 경우
			case 'writeuserpop':				
			case 'writeshoppop':				
				$this->setMessageDataInsert();
				break;
			case 'view': //1건 상세보기
			case 'viewuser':
			case 'viewusershop':
			case 'viewshop':
				$this->getMessageRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/message/message_view', $data);
				break;
			case 'updateform':
				break;
			case 'delete':
			case 'deleteuser':
			case 'deleteusershop':
			case 'deleteshop':
				$this->setMessageDataDelete();
				break;				
			case 'grpdelete':
			case 'grpdeleteuser':
			case 'grpdeleteusershop':				
			case 'grpdeleteshop':
				$this->setMessageGroupDataDelete();
				break;
			//SMS 발송
			case 'smsformpop':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/message/sms_write_pop', $data);
				break;	
			case 'smswritepop': //SMS발송
				$this->setSMSsend();
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
		
		if (in_array('msgno', $this->_arrUri))
		{
			$this->_msgNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'msgno')));
		}
		$this->_msgNum = $this->common->nullCheck($this->_msgNum, 'int', 0);
		
		/* ajax 리스트 형식이 아닌 이상 필요없음
		if (in_array('maxmsgno', $this->_arrUri))
		{
			$this->_maxMsgNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'maxmsgno')));
		}
		$this->_maxMsgNum = $this->common->nullCheck($this->_maxMsgNum, 'int', 0);
		*/		
		
		if (in_array('msggrpno', $this->_arrUri))
		{
			$this->_msgGrpNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'msggrpno')));
		}
		$this->_msgGrpNum = $this->common->nullCheck($this->_msgGrpNum, 'int', 0);
		
		if (in_array('msgdepth', $this->_arrUri))
		{
			$this->_msgDepth = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'msgdepth')));
		}
		$this->_msgDepth = $this->common->nullCheck($this->_msgDepth, 'int', 0);		
		
		if (in_array('msgtype', $this->_arrUri))
		{
			$this->_msgType = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'msgtype')));
		}
		$this->_msgType = $this->common->nullCheck($this->_msgType, 'int', 0);
		
		if (in_array('msgtodate', $this->_arrUri))
		{
			$this->_msgToDate = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'msgtodate')));
		}
		$this->_msgToDate = $this->common->nullCheck($this->_msgToDate	, 'str', '');		
		
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
		
		$readYn = $this->input->post_get('read_yn', TRUE);
		if (!empty($readYn)) $this->_currentParam .= '&read_yn='.$readYn;		
		
		$sendUserNum = $this->input->post_get('senduserno', TRUE);
		if (!empty($sendUserNum)) $this->_currentParam .= '&senduserno='.$sendUserNum;
		
		$sendUserTxt = $this->input->post_get('sendusertxt', TRUE);
		if (!empty($sendUserTxt)) $this->_currentParam .= '&sendusertxt='.$sendUserTxt;
		
		$sendShopNum = $this->input->post_get('sendshopno', TRUE);
		if (!empty($sendShopNum)) $this->_currentParam .= '&sendshopno='.$sendShopNum;
		
		$sendShopTxt = $this->input->post_get('sendshoptxt', TRUE);
		if (!empty($sendShopTxt)) $this->_currentParam .= '&sendshoptxt='.$sendShopTxt;
		
		$sendPhone = $this->input->post_get('sendphone', TRUE);
		if (!empty($sendPhone)) $this->_currentParam .= '&sendphone='.$sendPhone;
		
		if ($this->_uLevelType == 'SHOP')
		{
			//샵관리자로 로그인한 경우
			$this->_sNum = $this->common->getSession('shop_num');
		}
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=message'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_msgNum > 0) ? '/msgno/'.$this->_msgNum : '';
		//$this->_currentUrl .= ($this->_maxMsgNum > 0) ? '/maxmsgno/'.$this->_maxMsgNum : '';
		$this->_currentUrl .= ($this->_msgGrpNum > 0) ? '/msggrpno/'.$this->_msgGrpNum : '';
		$this->_currentUrl .= ($this->_msgDepth > 0) ? '/msgdepth/'.$this->_msgDepth : '';
		$this->_currentUrl .= (!empty($this->_msgType)) ? '/msgtype/'.$this->_msgType : '';
		
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
			'readYn' => $readYn,
			'sendUserNum' => $sendUserNum,
			'sendUserTxt' => $sendUserTxt,
			'sendShopNum' => $sendShopNum,			
			'sendShopTxt' => $sendShopTxt,	
			'sendPhone' => $sendPhone,
			'pageMethod' => $this->_uriMethod,
			'msgNum' => $this->_msgNum,
			//'maxMsgNum' => $this->common->nullCheck($this->_maxMsgNum, 'int', 0),
			'msgGrpNum' => $this->common->nullCheck($this->_msgGrpNum, 'int', 0),	
			'msgType' => $this->_msgType,
			'msgToDate' => $this->_msgToDate,
			'msgDepth' => $this->common->nullCheck($this->_msgDepth, 'int', 0),
			'sNum' => $this->_sNum,				
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
	 * @method name : getMessageDataList
	 * MESSAGE에서 리스트 뷰
	 * 
	 */
	private function getMessageDataList()
	{
		/* 일자별 목록 에서 페이징으로 기획이 변경됨
		if (strpos($this->_uriMethod, 'listview') !== FALSE) //대화 리스트를 보고자 하는 경우
		{
			unset($this->_sendData['listCount']);
			$this->_sendData['listCount'] = 1000; //메시지대화 리스트 개수는 사실상 무한대(해당일자별로)
			$result = $this->message_model->getListViewMessageDate($this->_sendData);
			unset($this->_sendData['msgToDate']); //getListViewMessageDate 에서 일자를 다시 받으므로
			$this->_sendData = $this->_sendData + $result;
		}
		*/
		
		$result = $this->message_model->getListViewMessageDate($this->_sendData);
		$this->_sendData = $this->_sendData + $result;

		
		$this->_data = $this->message_model->getMessageDataList($this->_sendData, FALSE);
		//페이징으로 보낼 데이터
		$pgData = array(
			'rsTotalCount' => $this->_data['rsTotalCount'],
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentUrl' => $this->_currentUrl,
			'currentParam' => $this->_currentParam
		);
		
		$this->_data['pagination'] = $this->common->listAdminPagingUrl($pgData);
		
		//메시지 읽음 처리
		$msgNum = '';
		foreach ($this->_data['recordSet'] as $rs)
		{
			$msgNum .= $rs['NUM'].',';
		}

		if (!empty($msgNum))
		{
			$msgNum = substr($msgNum, 0, -1);
			$this->message_model->setReadMessage($msgNum, $this->common->getSession('user_num'));
		}		
	}
	
	/**
	 * @method name : getMessageDataViewList
	 * VIEW_MESSAGE 에서 리스트
	 * 
	 */
	private function getMessageDataViewList()
	{
		/* 일자별 목록 에서 페이징으로 기획이 변경됨
		if (strpos($this->_uriMethod, 'listview') !== FALSE) //대화 리스트를 보고자 하는 경우
		{
			unset($this->_sendData['listCount']);
			$this->_sendData['listCount'] = 1000; //메시지대화 리스트 개수는 사실상 무한대(해당일자별로)
			$result = $this->message_model->getListViewMessageDate($this->_sendData);
			unset($this->_sendData['msgToDate']); //getListViewMessageDate 에서 일자를 다시 받으므로
			$this->_sendData = $this->_sendData + $result;
		}
		*/
		
		$result = $this->message_model->getListViewMessageDate($this->_sendData);
		$this->_sendData = $this->_sendData + $result;
	
		$this->_data = $this->message_model->getMessageDataViewList($this->_sendData, FALSE);
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
	 * @method name : getMessageRowData
	 * 메세지 1건에 대한 상세보기 
	 * 
	 */
	private function getMessageRowData()
	{
		$this->_data = $this->message_model->getMessageRowData($this->_msgNum, FALSE);
	}	
	
	/**
	 * @method name : setMessageDataInsert
	 * 메세지 발송
	 * 전체발송은 시스템부하문제가 있어 잠시 보류
	 * 
	 */
	private function setMessageDataInsert()
	{
		$sNum = 0;
		$sendallYn = $this->input->post_get('sendall_yn', TRUE);
		$ordNum = $this->common->nullCheck($this->input->post_get('ordno', TRUE), 'int', 0);
		$itemNum = $this->common->nullCheck($this->input->post_get('itemno', TRUE), 'int', 0);
		$targetNum = $this->input->post_get('targetno', TRUE); //보내고자 하는 상대
		$msgFrom = $this->input->post_get('msgfrom', TRUE); //listview에서 작성된 대화인지 판단
	
		//msgType은 없는경우 여기서 반드시 확정하여 넣어준다
		$msgType = (empty($this->_msgType)) ? $this->common->getCodeNumByCodeGrpNCodeId('MSGTYPE', 'NONE') : $this->_msgType;
		if (in_array($this->_uriMethod, array('list', 'listuser', 'writeuser', 'writeviewuser', 'writeuserpop')))
		{
			//써커스와 회원과의 메시지
			$msgType = 17030; //문의성 대화 	17150; //일반 대화			
		}
		else if (in_array($this->_uriMethod, array('listshop', 'writeshop', 'writeviewshop', 'writeshoppop')))
		{
			//써커스와 샵과의 메시지
			$msgType = 17020; //문의성 대화 	17140; //일반 대화
		}
		else if (in_array($this->_uriMethod, array('listusershop', 'writeusershop', 'writeviewusershop')))
		{
			//회원과 샵과의 메시지
			$msgType = 17040; //문의성 대화 	17160; //일반 대화
		}
		
		if ($this->_isAdmin)
		{
			$senderType = 'M'; //발송자가 circus 자격으로 발송
		}
		else 
		{
			if ($this->_uLevelType == 'SHOP')
			{
				$senderType = 'S'; //발송자가 Shop 자격으로 발송
				$sNum = $this->common->getSession('shop_num');
			}
			else 
			{
				$senderType = 'U'; //발송자가 일반회원으로서 발송
			}
		}
		
		$targetType = 'U'; //발송대상자가 회원, 샵 혹은 몰(circus)인지 구분
		if ($this->_uriMethod == 'writeshop' || $this->_uriMethod == 'writeshoppop')
		{
			$targetType = ($this->_isAdmin) ? 'S' : 'M'; //샵관리자에서는 M에게 발송
			
			if ($this->_isAdmin)
			{
				$sNum = $targetNum; //샵연관된 내용으로
				$targetNum = $this->common->getUserNumByShopNum($targetNum); //샵작가의 회원고유번호 조회				
			}
		}
		else if ($this->_uriMethod == 'writemall')
		{
			$targetType = 'M';
		}

		//list_view에서 대화작성이 아닌 경우 
		//관리자(M 또는 S)와의 대화 생성
		//동일한 샵 혹은 사용자는 2개이상의 대화방이 개설되어서는 안됨
		if ($msgFrom != 'listview')
		{
			if ($senderType == 'M' || $senderType == 'S')
			{
				if ($this->_isAdmin)
				{
					//써커스에서 발송
					$pageMethod = ($targetType == 'S') ? 'new_shop_mallq' : 'new_user_mallq';
					$initSenderType = 'M';
					$initTargetType = ($pageMethod == 'new_shop_mallq') ? 'S' : 'U';
				}
				else
				{
					//샵관리자에서 발송
					$pageMethod = ($senderType == 'S') ? 'new_shop_mallq' : 'new_user_mallq';
					$initSenderType = 'S';
					$initTargetType = ($pageMethod == 'new_shop_mallq') ? 'M' : 'U';
				}
					
				/*
				 if (strpos($pageMethod, 'shop') !== FALSE)
				 {
				 $sNum = $targetNum; //샵연관된 내용으로
				 $targetNum = $this->common->getUserNumByShopNum($targetNum);
				 }
				 */
					
				$qData = array(
					'pageMethod' => $pageMethod,
					'uNum' => $this->common->getSession('user_num'),
					'sNum' => $sNum,
					'msgType' => $msgType,
					'targetNum' => $targetNum,
					'senderType' => $initSenderType,
					'targetType' => $initTargetType
				);
				$result = $this->message_model->setAdminNewMessageInit($qData);
				if ($result)
				{
					unset($this->_sendData['maxMsgNum']);
					unset($this->_sendData['msgGrpNum']);
					$this->_sendData['maxMsgNum'] = $result['maxMsgNum'];
					$this->_sendData['msgGrpNum'] = $result['msgGrpNum'];
				}
			}
		}

		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'CONTENT' => $this->input->post_get('msg_content', FALSE),
			'SENDER_TYPE' => $senderType,				
			'TARGET_TYPE' => $targetType,
			'MSGTYPECODE_NUM' => $msgType,				
			'ORDERS_NUM' => (!empty($ordNum)) ? $ordNum : NULL, //관련 주문번호가 있는 경우
			'SHOPITEM_NUM' => (!empty($itemNum)) ? $itemNum : NULL, //관련 아이템번호가 있는 경우
			'REMOTEIP' => $this->input->ip_address()
		);
		
		if ($sNum > 0) $insData['SHOP_NUM'] = $sNum; //샵이 연관된 경우
		
		$result = $this->message_model->setMessageDataInsert(
			$this->_sendData,
			$sendallYn,
			$targetNum,
			$insData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		$addUrl = (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$addUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$addUri = '/msgno/'.$result.'/msggrpno/'.$this->_msgGrpNum.'/msgdepth/'.$this->_msgDepth;
		//필요없는 uri -> $addUri .= '/msgtype/'.$this->_msgType.'/msgtodate/'.date('Y-m-d'); //$this->_msgToDate; 신규는 항상 오늘자
		//$returnUrl = 'list';
		
		if ($this->_uriMethod == 'writeuserpop' || $this->_uriMethod == 'writeshoppop')
		{
			$this->common->message('발송 되었습니다.', 'top.layerPopClose();', 'js');
		}
		else 
		{
			if (strpos($this->_uriMethod, 'writeview') !== FALSE)
			{
				$returnUrl = str_replace('writeview', 'listview', $this->_uriMethod).$addUri;
			}
			else
			{
				$returnUrl = str_replace('write', 'list', $this->_uriMethod);				
			}

			if ($result > 0)
			{
				$this->common->message('발송 되었습니다.', '/manage/message_m/'.$returnUrl, 'top');
			}
		}
	}
	
	/**
	 * @method name : setMessageDataDelete
	 * MESSAGE 삭제 (1건)
	 *
	 */
	private function setMessageDataDelete()
	{
		$result = $this->message_model->setMessageDataDelete($this->_msgNum);
	
		//$listUrl = '/manage/message_m/list';
		$listUrl = str_replace('grpdelete', 'list', $this->_uriMethod);
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');
	}	
	
	/**
	 * @method name : setMessageGroupDataDelete
	 * MESSAGE 삭제 (체크된 내용 모두 삭제)
	 * 개별 고유번호별 삭제에서 -> MESSAGE_GROUPNUM 을 통해 삭제로 변경
	 */
	private function setMessageGroupDataDelete()
	{
		$delData = $this->input->post_get('selval', TRUE);
		//$this->message_model->setMessageGroupDataDelete($delData);
		$this->message_model->setMessageGroupNumDataDelete($delData);		
		
		//$listUrl = '/manage/message_m/list';
		$listUrl = str_replace('grpdelete', 'list', $this->_uriMethod);
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');
	}
	
	/**
	 * @method name : setSMSsend
	 * SMS 발송 
	 * 
	 */
	private function setSMSsend()
	{
		$qData = array(
			'phoneNum' => $this->input->post_get('sendphone', TRUE),
			'smsContent' => $this->input->post_get('sms_content', TRUE),
			'smsSubject' => $this->input->post_get('sms_subject', TRUE),
			'smsType' => $this->input->post_get('smstype', TRUE)
		);
		
		$this->common->smsSend($qData);
		$this->common->message('발송 하였습니다.', 'reload', 'parent');
	}
}