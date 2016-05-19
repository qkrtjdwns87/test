<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Comment_m
 * 가상 테이블명 혼용
 * (파일첨부 기능이 사용될 경우 - SHOPITEM_COMMENT인경우 SHOPITEM과의 중복을 피하기위해)
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Comment_m extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';
		
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer 코멘트 고유번호
	 */
	protected $_comtNum = 0;
	
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
	
	protected $_tbl = 'COMMON_COMMENT';
	
	/**
	 * @var string COMMON_COMMENT를 사용하고자 하는 Table 명
	 * 암호화 되어 있음
	 */
	protected $_tblInfo = '';
	
	/**
	 * @var integer COMMON_COMMENT를 사용하고자 하는 Table 고유번호
	 */
	protected $_tNum = 0;	
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = TRUE;
	
	/**
	 * @var integer 파일첨부갯수
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

		$this->load->helper(array('url', 'text'));
		$this->load->model(array('comment_model', 'black_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->loginCheck();
		$this->setPrecedeValues();
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'list':
				$this->getCommentDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/comment/comment_list', $data);
				break;
			case 'writeform':
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/comment/comment_write', $data);
				break;
			case 'view':
				$this->getCommentRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/comment/comment_view', $data);
				break;				
			case 'updateform':
				$this->getCommentRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/comment/comment_write', $data);
				break;				
			case 'replyform':
				$this->getCommentRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/comment/comment_write', $data);
				break;
			case 'write':
				$this->setCommentDataInsert();
				break;	
			case 'reply':
				$this->setCommentReplyDataInsert();
				break;				
			case 'update':
				$this->setCommentDataUpdate();
				break;				
			case 'delete':
				$this->setCommentDataDelete();				
				break;
			case 'grpdelete':
				$this->setCommentGroupDataDelete();
				break;	
			case 'spamwrite':
				$this->setBlackDataInsert();
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
		$this->_uriMethod = $this->common->nullCheck($this->_uriMethod, 'str', 'list');
		
		if (in_array('page', $this->_arrUri))
		{
			$this->_currentPage = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'page')));
		}
		$this->_currentPage = $this->common->nullCheck($this->_currentPage, 'int', 1);		
		
		if (in_array('comtno', $this->_arrUri))
		{
			$this->_comtNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'comtno')));
		}
		$this->_comtNum = $this->common->nullCheck($this->_comtNum, 'int', 0);
		
		if (in_array('t_info', $this->_arrUri))
		{
			$this->_tblInfo = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 't_info')));
		}
		$this->_tblInfo = $this->common->nullCheck($this->_tblInfo, 'str', ''); //가상 테이블명 사용(파일업로드 운영시 SHOPITEM과의 중복방지)
		$this->_tblInfo = (empty($this->_tblInfo)) ? $this->common->sqlEncrypt('SHOPITEM_COMMENT', $this->_encKey) : $this->_tblInfo; //가상테이블명
		
		if (in_array('t_no', $this->_arrUri))
		{
			$this->_tNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 't_no')));
		}
		$this->_tNum = $this->common->nullCheck($this->_tNum, 't_no', 0);		
		
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
		
		$sendItemNum = $this->input->post_get('senditemno', TRUE);
		if (!empty($sendItemNum)) $this->_currentParam .= '&senditemno='.$sendItemNum;
		
		$sendItemTxt = $this->input->post_get('senditemtxt', TRUE);
		if (!empty($sendItemTxt)) $this->_currentParam .= '&senditemtxt='.$sendItemTxt;
		
		$sendShopNum = $this->input->post_get('sendshopno', TRUE);
		if (!empty($sendShopNum)) $this->_currentParam .= '&sendshopno='.$sendShopNum;
		
		$sendShopTxt = $this->input->post_get('sendshoptxt', TRUE);
		if (!empty($sendShopTxt)) $this->_currentParam .= '&sendshoptxt='.$sendShopTxt;			
		
		if ($this->_uLevelType == 'SHOP')
		{
			//샵관리자로 로그인한 경우
			$this->_sNum = $this->common->getSession('shop_num');
		}
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=comment'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_tNum > 0) ? '/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo : '';		
		$this->_currentUrl .= ($this->_comtNum > 0) ? '/comtno/'.$this->_comtNum : '';
		
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
			'sendItemNum' => $sendItemNum,
			'sendItemTxt' => $sendItemTxt,
			'sendShopNum' => $sendShopNum,			
			'sendShopTxt' => $sendShopTxt,
			'pageMethod' => $this->_uriMethod,
			'comtNum' => $this->_comtNum,
			'tNum' => $this->_tNum,
			'sNum' => $this->_sNum,				
			'tbl' => $this->_tbl,
			'tblInfo' => $this->_tblInfo, //암호화된 관련 테이블 명				
			'fileCnt' => $this->_fileCnt,
			'isLogin' => $this->common->getIsLogin(),
			'isAdmin' => $this->_isAdmin,
			'userLevelType' => $this->_uLevelType,				
			'sessionData' => $this->common->getSessionAll()
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
	 * @method name : getCommentDataList
	 * 댓글(흔적남기기) 리스트 
	 * 
	 */
	private function getCommentDataList()
	{
		unset($this->_sendData['tblInfo']);
		$this->_sendData['tblInfo'] = $this->common->sqlDecrypt($this->_tblInfo, $this->_encKey); //암호화된 테이블명 복호화 
		$this->_data = $this->comment_model->getCommentDataList($this->_sendData, FALSE);
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
	 * @method name : getCommentRowData
	 * 댓글(흔적남기기) 리스트 1행의 데이터만 불러온다
	 *
	 * @param :
	 * @return void
	 */
	private function getCommentRowData()
	{
		$this->_data = $this->comment_model->getCommentRowData($this->_comtNum, FALSE);
	}	
	
	/**
	 * @method name : setCommentDataInsert
	 * 댓글(흔적남기기) 게시글 작성 
	 * 
	 */
	private function setCommentDataInsert()
	{
		$tblInfo = $this->common->sqlDecrypt($this->_tblInfo, $this->_encKey); //암호화된 테이블명 복호화
		$itemNum = $this->common->nullCheck($this->input->post_get('itemno', FALSE), 'int', 0);
		$insData = array(
			'TBLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $tblInfo),
			'TBL_NUM' => ($itemNum > 0) ? $itemNum : $this->_tNum, //SHOPITEM_COMMENT인경우 SHOPITEM 고유번호
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),				
			'USER_NAME' => $this->common->getSession('user_name'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'CONTENT' => $this->input->post_get('brd_content', FALSE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->comment_model->setCommentDataInsert(
			$insData,
			$this->config->item('board_thread_interval'),
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		$listUrl = '/manage/comment_m/list/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';		
	
		if ($result > 0)
		{
			$this->common->message('등록 되었습니다.', $listUrl, 'top');
		}
	}
	
	/**
	 * @method name : setCommentReplyDataInsert
	 * 댓글(흔적남기기) 리스트에 댓글달기 
	 * 
	 */
	private function setCommentReplyDataInsert()
	{
		$tblInfo = $this->common->sqlDecrypt($this->_tblInfo, $this->config->item('encryption_key')); //암호화된 테이블명 복호화
		$itemNum = $this->common->nullCheck($this->input->post_get('itemno', FALSE), 'int', 0);		
		$insData = array(
			'TBLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $tblInfo),
			'TBL_NUM' => ($itemNum > 0) ? $itemNum : $this->_tNum, //SHOPITEM_COMMENT인경우 SHOPITEM 고유번호
			"GROUPNUM" => $this->input->post_get('pgroupno', TRUE),
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),				
			'USER_NAME' => $this->common->getSession('user_name'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'CONTENT' => $this->input->post_get('brd_content', FALSE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->comment_model->setCommentReplyDataInsert(
			$this->input->post_get('pthread', TRUE),	//부모글 THREAD
			$this->input->post_get('pdepth', TRUE),		//부모글 DEPTH		
			$insData,
			$this->config->item('board_thread_interval'),
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$listUrl = '/manage/comment_m/list/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
			
			$this->common->message('등록 되었습니다.', $listUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setCommentDataUpdate
	 * 댓글(흔적남기기) update
	 * 
	 */
	private function setCommentDataUpdate()
	{
		$tblInfo = $this->common->sqlDecrypt($this->_tblInfo, $this->config->item('encryption_key')); //암호화된 테이블명 복호화
		$itemNum = $this->common->nullCheck($this->input->post_get('itemno', FALSE), 'int', 0);		
		$upData = array(
			'TBL_NUM' => ($itemNum > 0) ? $itemNum : $this->_tNum, //SHOPITEM_COMMENT인경우 SHOPITEM 고유번호				
			'CONTENT' => $this->input->post_get('brd_content', FALSE)
		);
		
		$result = $this->comment_model->setCommentDataUpdate(
			$this->_comtNum,				
			$upData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$listUrl = '/manage/comment_m/list/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
			
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setCommentDataDelete
	 * 댓글(흔적남기기) 리스트 1건 삭제
	 * 
	 */
	private function setCommentDataDelete()
	{
		$result = $this->comment_model->setCommentDataDelete($this->_comtNum);
		
		$listUrl = '/manage/comment_m/list/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');		
	}
	
	/**
	 * @method name : setCommentGroupDataDelete
	 * 댓글(흔적남기기) 리스트 체크된 내용 모두 삭제
	 * 
	 */
	private function setCommentGroupDataDelete()
	{
		$delData = $this->input->post_get('selval', TRUE);
		$this->comment_model->setCommentGroupDataDelete($delData);
		$listUrl = '/manage/comment_m/list/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
		$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');		
	}
	
	/**
	 * @method name : setBlackDataInsert
	 * 댓글(흔적남기기) 리스트 스팸 처리 
	 * 
	 */
	private function setBlackDataInsert()
	{
		$userNum = $this->input->post_get('userno', TRUE);
		$blackIp = $this->input->post_get('blackip', TRUE);
		$this->_returnUrl = base64_decode($this->_returnUrl);		
		$insData = array(
			'TBLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->_tbl), //블랙처리시 관련된 테이블고유번호
			'TBL_NUM' => $this->_comtNum, //블랙처리시 관련된 테이블의 고유번호필드의 고유번호
			'REASON' => '관리자-게시물관리-댓글내용에 대해 스팸처리함',
			'BLACKIP' => $blackIp,
			'USER_NUM' => $userNum, //BLACK에 등록될 회원고유번호
			'APPOINTUSER_NUM' => $this->common->getSession('user_num'), //신고자
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->black_model->setBlackDataInsert(
			array(),
			$insData,
			FALSE	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$this->comment_model->setSpamUpdate($this->_comtNum);
			$this->common->message('스팸 처리 되었습니다.', $this->_returnUrl, 'top');
		}
	}
}
?>