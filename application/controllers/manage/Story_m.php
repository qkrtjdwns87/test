<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Story_m
 *
 *
 * @author : Administrator
 * @date    : 2016. 01
 * @version:
 */
class Story_m extends CI_Controller {
	
	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';
		
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer Story 고유번호
	 */
	protected $_stoNum = 0;
	
	/**
	 * @var integer COMMON_FILE 고유번호
	 */
	protected $_fNum = 0;
	
	/**
	 * @var integer COMMON_FILE 인덱스(FILE_ORDER)
	 */
	protected $_fIndex = 0;
	
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
	
	protected $_tbl = 'STORY';
	
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
		$this->load->model('story_model');
		
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
				$this->getStoryDataList();
				$this->getGroupCodeDataList();	
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/story/story_list', $data);
				break;
			case 'writeform':
				$this->getGroupCodeDataList();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/story/story_write', $data);				
				break;
			case 'view':
				$this->getStoryRowData();
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/story/story_view', $data);
				break;				
			case 'updateform':
				$this->getStoryRowData();
				$this->getGroupCodeDataList();				
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/story/story_write', $data);
				break;				
			case 'write':
				$this->setStoryDataInsert();
				break;	
			case 'update':
				$this->setStoryDataUpdate();
				break;				
			case 'delete':
				$this->setStoryDataDelete();				
				break;
			case 'grpdelete':
				$this->setStoryGroupDataDelete();
				break;		
			case 'filedelete':
				$this->setStoryFileDelete();
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
		
		if (in_array('stono', $this->_arrUri))
		{
			$this->_stoNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'stono')));
		}
		$this->_stoNum = $this->common->nullCheck($this->_stoNum, 'int', 1);
		
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
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=story'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		$this->_currentUrl .= ($this->_stoNum > 0) ? '/stono/'.$this->_stoNum : '';
		
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
			'stoNum' => $this->_stoNum,
			'tbl' => $this->_tbl,
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
	
	/**
	 * @method name : getGroupCodeDataList
	 * 관계되는 모든 CODE Data List 
	 * 
	 */
	private function getGroupCodeDataList()
	{
		$this->_data['storyStyleCdSet'] = $this->common->getCodeListByGroup('STORYSTYLE');
	}
	
	/**
	 * @method name : getStoryRowData
	 * 1행의 데이터만 불러온다
	 *
	 * @param :
	 * @return void
	 */
	private function getStoryRowData()
	{
		$qData = array();
		$this->_data = $this->story_model->getStoryRowData($this->_stoNum, $qData, FALSE);
	}	
	
	private function setStoryDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'USER_ID' => $this->common->getSession('user_id'),				
			'USER_NAME' => $this->common->getSession('user_name'),
			'USER_EMAIL' => $this->common->getSession('user_email'),
			'TITLE' => $this->input->post_get('title', TRUE),
			'STORY_CONTENT' => $this->input->post_get('story_content', FALSE),
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$storySub = $this->input->post_get('story', FALSE); //앱 선택 스타일 데이터
		$result = $this->story_model->setStoryDataInsert(
			$insData,
			$storySub,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		if ($result > 0)
		{
			$this->common->message('등록 되었습니다.', '/manage/story_m/list/', 'top');
		}
	}

	private function setStoryDataUpdate()
	{
		$upData = array(
			'TITLE' => $this->input->post_get('title', TRUE),
			'STORY_CONTENT' => $this->input->post_get('story_content', FALSE)
		);
		
		$storySub = $this->input->post_get('story', FALSE); //앱 선택 스타일 데이터		
		$result = $this->story_model->setStoryDataUpdate(
			$this->_stoNum,
			$upData,
			$storySub,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$listUrl = '/manage/story_m/list';
			$listUrl .= (!empty($this->_currentPage)) ? '/page/'.$this->_currentPage : '';
			$listUrl .= (!empty($this->_currentParam)) ? $this->_currentParam : '';			
			$this->common->message('수정 되었습니다.', $listUrl, 'top');
		}		
	}
	
	/**
	 * @method name : setStoryDataDelete
	 * STORY 삭제 (1건)  
	 * 
	 */
	private function setStoryDataDelete()
	{
		$result = $this->story_model->setStoryDataDelete($this->_stoNum);
		
		$listUrl = '/manage/story_m/list';
		$listUrl .= (!empty($currentParam)) ? $currentParam : '';		
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');		
	}
	
	/**
	 * @method name : setStoryGroupDataDelete
	 * STORY 삭제 (체크된 내용 모두 삭제) 
	 * 
	 */
	private function setStoryGroupDataDelete()
	{
		$delData = $this->input->post_get('selval', TRUE);
		$this->story_model->setStoryGroupDataDelete($delData);
		$listUrl = '/manage/story_m/list';
		$this->common->message('삭제 되었습니다.', $listUrl, 'top');		
	}
	
	/**
	 * @method name : setStoryFileDelete
	 * 아이템 파일첨부 내용 삭제(1건씩)
	 *
	 */
	private function setStoryFileDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$this->story_model->setStoryFileDelete($this->_stoNum, $this->_fNum, $this->_fIndex);
	
		$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		//$this->common->message('삭제 되었습니다.', 'reload', 'parent');
	}	
}
?>