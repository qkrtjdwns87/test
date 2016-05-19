<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Memo_m
 * 
 *
 * @author : Administrator
 * @date    : 2016. 01.
 * @version:
 */
class Memo_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer MEMO 고유번호
	 */
	protected $_mNum = 0;
	
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
	
	protected $_tbl = 'COMMON_MEMO';
	
	/**
	 * @var string COMMON_MEMO를 사용하고자 하는 Table 명
	 * 암호화 되어 있음
	 */
	protected $_tblInfo = '';
	
	/**
	 * @var integer COMMON_MEMO를 사용하고자 하는 Table 고유번호
	 */
	protected $_tNum = 0;	
	
	/**
	 * @var bool 파일 업로드 여부
	 */
	protected $_isUpload = FALSE;
	
	/**
	 * @var integer 파일첨부갯수
	 */
	protected $_fileCnt = 0;
	
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
		$this->load->model('memo_model');
		
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
				$this->getMemoDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/common/memo_list', $data);
				break;
			case 'write':
				$this->setMemoDataInsert();
				break;
			case 'update':
				break;
			case 'delete':
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
		
		if (in_array('mno', $this->_arrUri))
		{
			$this->_mNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'mno')));
		}
		$this->_mNum = $this->common->nullCheck($this->_mNum, 'mno', 0);
		
		if (in_array('t_info', $this->_arrUri))
		{
			$this->_tblInfo = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 't_info')));
		}
		$this->_tblInfo = $this->common->nullCheck($this->_tblInfo, 'str', '');
		
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
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=memo'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= '/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
		$this->_currentUrl .= ($this->_mNum > 0) ? '/mno/'.$this->_mNum : '';
	
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
			'pageMethod' => $this->_uriMethod,
			'mNum' => $this->_mNum,
			'tNum' => $this->_tNum,				
			'tbl' => $this->_tbl,
			'tblInfo' => $this->_tblInfo, //암호화된 관련 테이블 명				
			'fileCnt' => $this->_fileCnt,
			'isLogin' => $this->common->getIsLogin(),
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
	
	private function getMemoDataList()
	{
		$this->_data = $this->memo_model->getMemoDataList($this->_sendData, FALSE);
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
	
	private function setMemoDataInsert()
	{
		$tblInfo = $this->common->sqlDecrypt($this->_tblInfo, $this->config->item('encryption_key')); //암호화된 테이블명 복호화
		$priorityYn = $this->input->post_get('priority_yn', TRUE);
		$insData = array(
			'TBLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $tblInfo),
			'TBL_NUM' => $this->_tNum,
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),
			'USER_NAME' => $this->common->getSession('user_name'),				
			'USER_NICK' => $this->common->getSession('user_nick'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'HTML_YN' => 'N',
			'PRIORITY_YN' => (empty($priorityYn)) ? 'N' : $priorityYn,	
			'CONTENT' => $this->input->post_get('memo_content', TRUE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$result = $this->memo_model->setMemoDataInsert($insData);
		
		if ($result > 0)
		{
			$listUrl = '/manage/memo_m/list/t_no/'.$this->_tNum.'/t_info/'.$this->_tblInfo;
			$this->common->message('등록 되었습니다.', $listUrl, 'parent');
		}
	}
}