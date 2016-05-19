<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Board_m
 *
 *
 * @author : Administrator
 * @date    : 2016. 01
 * @version:
 */
class Board_m extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';
		
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer 게시판 고유 아이디
	 */
	protected $_setNum = 9010; //전체공지
	
	/**
	 * @var integer 게시판 고유번호
	 */
	protected $_bNum = 0;
	
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
	
	protected $_tbl = 'BOARD';
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = TRUE;
	
	/**
	 * @var integer 파일첨부갯수
	 */
	protected $_fileCnt = 2;
	
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

		$this->load->helper(array ('url', 'text'));
		$this->load->model('board_model');
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		$this->loginCheck();
		$this->setPrecedeValues();
		
		if (in_array($this->_setNum, array(9120, 9130, 9140)))
		{
			$this->_fileCnt = 0;
			unset($this->_sendData['fileCnt']);
			$this->_sendData['fileCnt'] = $this->_fileCnt;
		}		
		
		/* View 페이지 정의 */
		switch($this->_setNum)
		{
			case 9110:
				$brdListView = 'manage/board/qna_list';
				break;
			default:
				$brdListView = 'manage/board/board_list';
				break;
		}
		
		$brdView = 'manage/board/board_view';
		$brdWriteView = 'manage/board/board_write';
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'list':
				$this->getBoardDataList();
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view($brdListView, $data);
				break;
			case 'writeform':
				$this->getGroupCodeDataList();			
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view($brdWriteView, $data);
				break;
			case 'view':
				$this->getBoardRowData();
				$this->getGroupCodeDataList();	
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view($brdView, $data);
				break;				
			case 'updateform':
				$this->getBoardRowData();
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view($brdWriteView, $data);
				break;				
			case 'replyform':
				$this->getBoardRowData();
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view($brdWriteView, $data);
				break;
			case 'write':
				$this->setBoardDataInsert();
				break;	
			case 'reply':
				$this->setBoardReplyDataInsert();
				break;				
			case 'update':
				$this->setBoardDataUpdate();
				break;				
			case 'delete':
				$this->setBoardDataDelete();				
				break;
			case 'grpdelete':
				$this->setBoardGroupDataDelete();
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
		
		if (in_array('setno', $this->_arrUri))
		{
			$this->_setNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'setno')));
		}
		$this->_setNum = $this->common->nullCheck($this->_setNum, 'int', 1);
		
		if (in_array('bno', $this->_arrUri))
		{
			$this->_bNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'bno')));
		}
		$this->_bNum = $this->common->nullCheck($this->_bNum, 'int', 0);
		
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
		
		//category가 연결되는 경우
		$boardCate = $this->input->post_get('boardcate', TRUE);
		if (!empty($boardCate)) $this->_currentParam .= '&boardcate='.$boardCate;
		
		$replyState = $this->input->post_get('replystate', TRUE);
		if (!empty($replyState)) $this->_currentParam .= '&replystate='.$replyState;
		
		$replyDateType = $this->input->post_get('replydatetype', TRUE);
		if (!empty($replyDateType)) $this->_currentParam .= '&replydatetype='.$replyDateType;
		
		$applyYn = $this->input->post_get('applyyn', TRUE); //약관관리의 사용여부 검색
		if (!empty($applyYn)) $this->_currentParam .= '&applyyn='.$applyYn;				
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=board'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= '/setno/'.$this->_setNum;
		$this->_currentUrl .= ($this->_bNum > 0) ? '/bno/'.$this->_bNum : '';
		
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
			'boardCate' => $boardCate,
			'replyState' => $replyState,
			'replyDateType' => $replyDateType,
			'applyYn' => $applyYn,
			'pageMethod' => $this->_uriMethod,
			'setNum' => $this->_setNum,
			'bNum' => $this->_bNum,
			'tbl' => $this->_tbl,
			'tblTitle' => $this->common->getCodeTitleByCodeNum($this->_setNum),
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
	
	private function getBoardDataList()
	{
		if ($this->_uLevelType == 'SHOP') $this->_sendData['uNum'] = $this->common->getSession('user_num');
		$this->_data = $this->board_model->getBoardDataList($this->_sendData, FALSE);
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
	 * @method name : getGroupCodeDataList
	 * 관계되는 모든 CODE Data List 
	 * 
	 */
	private function getGroupCodeDataList()
	{
		$this->_data['qnaCateCdSet'] = $this->common->getCodeListByGroup('QNACATE');
		$this->_data['faqCateCdSet'] = $this->common->getCodeListByGroup('FAQCATE');
		$this->_data['trmCateCdSet'] = $this->common->getCodeListByGroup('TERMSCATE');
	}
	
	/**
	 * @method name : getBoardRowData
	 * 1행의 데이터만 불러온다
	 *
	 * @param :
	 * @return void
	 */
	private function getBoardRowData()
	{
		$this->_data = $this->board_model->getBoardRowData($this->_bNum, FALSE);
		
		$snsMsg = $this->_data['recordSet']['CONTENT'];
		$snsMsg = $this->common->stripHtmlTags($snsMsg);
		$snsMsg = $this->common->cutStr($snsMsg, 60);
		$shortUrl = $this->common->getShortURL($this->common->getDomain().'/'.$this->_uri);
		//SNS에 공유할 내용
		$this->_data['snsSet'] = array(
			'facebook_appId' => $this->config->item('facebook_appid'),
			'twitter_key' => $this->config->item('twitter_consumer_key'),
			'kakao_Key' => $this->config->item('kakao_javascript_key'),
			'snsTitle' => $this->_data['recordSet']['TITLE'],
			'snsMsg' =>  $snsMsg,
			'snsLink' => $shortUrl,
			'snsImgUrl' => $this->common->getDomain().'/images/gray_bg.png',
			'snsDomain' => $this->common->getDomain()
		);
	}	
	
	private function setBoardDataInsert()
	{
		$boardCate = $this->input->post_get('board_cate', FALSE);
		if (in_array($this->_setNum, array(9100, 9110))) //샵-써커스QNA, 회원-써커스QNA
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('QNACATE', 'NONE');
		}
		else if (in_array($this->_setNum, array(9130)))	//FAQ
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('FAQCATE', 'NONE');
		}
		else if (in_array($this->_setNum, array(9140)))	//TERMS 약관
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('TERMSCATE', 'NONE');
			$applyDate = $this->input->post_get('apply_date', TRUE);
			$selectYn = $this->input->post_get('select_yn', TRUE);
		}
		else 
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('NOTICATE', 'NONE');
		}
		$urgencyYn = $this->input->post_get('urgency_yn', TRUE);
		
		$insData = array(
			'SET_NUM' => $this->_setNum,
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),				
			'USER_NAME' => $this->common->getSession('user_name'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'TITLE' => $this->input->post_get('title', TRUE),
			'CONTENT' => $this->input->post_get('brd_content', FALSE),
			'CATECODE_NUM' => (!empty($boardCate)) ? $boardCate : $tmpCate,				
			'HTML_YN' => 'Y',	
			'REMOTEIP' => $this->input->ip_address()
		);
		if (!empty($applyDate)) $insData['APPLY_DATE'] = $applyDate;
		if (!empty($selectYn)) $insData['SELECT_YN'] = $selectYn;
		if (!empty($urgencyYn)) $insData['URGENCY_YN'] = $urgencyYn;
		
		$result = $this->board_model->setBoardDataInsert(
			$this->_setNum,
			$insData,
			$this->config->item('board_thread_interval'),
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		if ($result > 0)
		{
			$this->common->message('등록 되었습니다.', '/manage/board_m/list/setno/'.$this->_setNum, 'top');
		}
	}
	
	private function setBoardReplyDataInsert()
	{
		$boardCate = $this->input->post_get('board_cate', FALSE);
		$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('BOARD', 'NONE');
		if (in_array($this->_setNum, array(9100, 9110))) //샵-써커스QNA, 회원-써커스QNA
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('QNACATE', 'NONE');
		}
		else if (in_array($this->_setNum, array(9130)))	//FAQ
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('FAQCATE', 'NONE');
		}
		else if (in_array($this->_setNum, array(9140)))	//TERMS 약관
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('TERMSCATE', 'NONE');
			$applyDate = $this->input->post_get('apply_date', TRUE);
			$selectYn = $this->input->post_get('select_yn', TRUE);
		}
		
		$insData = array(
			'SET_NUM' => $this->_setNum,
			"GROUPNUM" => $this->input->post_get('pgroupno', TRUE),
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),				
			'USER_NAME' => $this->common->getSession('user_name'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'TITLE' => $this->input->post_get('title', TRUE),
			'CONTENT' => $this->input->post_get('brd_content', FALSE),
			'CATECODE_NUM' => (!empty($boardCate)) ? $boardCate : $tmpCate,				
			'HTML_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
		if (!empty($applyDate)) $insData['APPLY_DATE'] = $applyDate;
		if (!empty($selectYn)) $insData['SELECT_YN'] = $selectYn;		
		
		$result = $this->board_model->setBoardReplyDataInsert(
			$this->_setNum,				
			$this->input->post_get('pthread', TRUE),	//부모글 THREAD
			$this->input->post_get('pdepth', TRUE),		//부모글 DEPTH		
			$insData,
			$this->config->item('board_thread_interval'),
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$listUrl = '/manage/board_m/list/setno/'.$this->_setNum;
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
			$this->common->message('등록 되었습니다.', $listUrl, 'top');
		}		
	}
	
	private function setBoardDataUpdate()
	{
		$boardCate = $this->input->post_get('board_cate', FALSE);
		$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('BOARD', 'NONE');
		if (in_array($this->_setNum, array(9100, 9110))) //샵-써커스QNA, 회원-써커스QNA
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('QNACATE', 'NONE');
		}
		else if (in_array($this->_setNum, array(9130)))	//FAQ
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('FAQCATE', 'NONE');
		}
		else if (in_array($this->_setNum, array(9140)))	//TERMS 약관
		{
			$tmpCate = $this->common->getCodeNumByCodeGrpNCodeId('TERMSCATE', 'NONE');
			$applyDate = $this->input->post_get('apply_date', TRUE);
			$selectYn = $this->input->post_get('select_yn', TRUE);
		}
		
		$emailEnc = $this->common->sqlEncrypt($this->input->post_get('email', TRUE), $this->config->item('encryption_key'));
		$upData = array(
			'TITLE' => $this->input->post_get('title', TRUE),
			'CONTENT' => $this->input->post_get('brd_content', FALSE),
			'CATECODE_NUM' => (!empty($boardCate)) ? $boardCate : $tmpCate,
			'UPDATE_DATE' => date('Y-m-d H:i:s')
		);
		if (!empty($applyDate)) $upData['APPLY_DATE'] = $applyDate;
		if (!empty($selectYn)) $upData['SELECT_YN'] = $selectYn;
		
		$result = $this->board_model->setBoardDataUpdate(
			$this->_setNum,				
			$this->_bNum,				
			$upData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$listUrl = '/manage/board_m/list/setno/'.$this->_setNum;
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';			
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}		
	}
	
	private function setBoardDataDelete()
	{
		$result = $this->board_model->setBoardDataDelete($this->_bNum);
		
		$listUrl = '/manage/board_m/list/setno/'.$this->_setNum;
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');		
	}
	
	private function setBoardGroupDataDelete()
	{
		$delData = $this->input->post_get('selval', TRUE);
		$this->board_model->setBoardGroupDataDelete($delData);
		$listUrl = '/manage/board_m/list/setno/'.$this->_setNum;
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');		
	}
}
?>