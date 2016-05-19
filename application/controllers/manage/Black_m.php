<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Black_m
 * 미완료
 * 추후 관리자 메뉴생성시 개발 
 *
 * @author : Administrator
 * @date    : 2016. 02.
 * @version:
 */
class Black_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer USER_BLACK 고유번호
	 */
	protected $_ubkNum = 0;
	
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
	
	protected $_tbl = 'USER_BLACK';
	
	/**
	 * @var bool 파일 업로드 여부
	 * spam 근거 자료 첨부할 경우
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
		$this->load->model(array('black_model'));
		
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
				$this->getBlackDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/black/black_list', $data);
				break;
			case 'writeform':
				break;				
			case 'write';
				$this->setBlackDataInsert();
				break;
			case 'view':
				$this->getBlackRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/black/black_view', $data);
				break;
			case 'updateform':
				break;
			case 'delete':
				$this->setBlackDataDelete();
				break;				
			case 'grpdelete':
				$this->setBlackGroupDataDelete();
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
		
		if (in_array('ubkno', $this->_arrUri))
		{
			$this->_ubkNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'ubkno')));
		}
		$this->_ubkNum = $this->common->nullCheck($this->_ubkNum, 'int', 0);
		
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
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=black'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_ubkNum > 0) ? '/ubkno/'.$this->_ubkNum : '';
		
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
			'pageMethod' => $this->_uriMethod,
			'ubkNum' => $this->_ubkNum,
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
	
	private function getBlackDataList()
	{
		$this->_data = $this->black_model->getBlackDataList($this->_sendData, FALSE);
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
	
	private function getBlackRowData()
	{
		$this->_data = $this->black_model->getMessageRowData($this->_msgNum, FALSE);
	}	
	
	/**
	 * @method name : setBlackDataInsert
	 * 
	 * 
	 * 
	 */
	private function setBlackDataInsert()
	{
		$userNum = $this->common->nullCheck($this->input->post_get('userno', TRUE), 'int', 0);
		$insData = array(
			'USER_NUM' => $userNum, //BLACK에 등록될 회원고유번호
			'APPOINTUSER_NUM' => $this->common->getSession('user_num'), //신고자
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->black_model->setBlackDataInsert(
			$this->_sendData,
			$insData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$this->common->message('등록 되었습니다.', '/manage/black_m/list', 'top');
		}
	}
	
	/**
	 * @method name : setBlackDataDelete
	 * BLACK 삭제 (1건)
	 *
	 */
	private function setBlackDataDelete()
	{
		$result = $this->black_model->setBlackDataDelete($this->_msgNum);
	
		$listUrl = '/manage/black_m/list';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');
	}	
	
	/**
	 * @method name : setMessageGroupDataDelete
	 * BLACK 삭제 (체크된 내용 모두 삭제)
	 *
	 */
	private function setBlackGroupDataDelete()
	{
		$delData = $this->input->post_get('selval', TRUE);
		$this->black_model->setBlackGroupDataDelete($delData);
		
		$listUrl = '/manage/black_m/list';
		$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';
		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');
	}
}