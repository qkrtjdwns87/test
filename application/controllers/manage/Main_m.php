<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Main_m
 * 
 *
 * @author : Administrator
 * @date    : 2016. 01.
 * @version:
 */
class Main_m extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_listCount = 0;
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = 'list';
	
	/**
	 * @var integer MALLMAIN 고유번호
	 */
	protected $_mmNum = 0;	
	
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
	
	protected $_tbl = 'MALLMAIN';
	
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
	 * @var integer 신상품 등록 갯수
	 */
	protected $_newItemCnt = 40;
	
	/**
	 * @var integer 베스트 상품 등록 갯수
	 */
	protected $_bestItemCnt = 40;
	
	public function __construct()
	{
		parent::__construct ();
		
		$this->load->helper(array('url'));
		$this->load->model(array('main_model', 'board_model'));
		
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
				break;
			case 'writeform':
				break;
			case 'view':
				break;
			case 'updateform':
				break;
			case 'storyform':
				$this->getStoryMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_story', $data);
				break;
			case 'storywrite':
				$this->setStoryMainDataInsert();
				break;
			case 'storycontentdelete':
				$this->setStoryMainContentDelete();
				break;
			case 'visualform':
				$this->getVisualMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_visual', $data);
				break;
			case 'visualwrite':
				$this->setVisualMainDataInsert();
				break;	
			case 'visualcontentdelete':
				$this->setVisualMainContentDelete();
				break;
			case 'todayform':
				$this->getTodayMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_today', $data);
				break;
			case 'todaywrite':
				$this->setTodayMainDataInsert();
				break;
			case 'todaycontentdelete':
				$this->setTodayMainContentDelete();
				break;		
			case 'trendform':
				$this->getTrendMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_trend', $data);
				break;
			case 'trendwrite':
				$this->setTrendMainDataInsert();
				break;
			case 'trendcontentdelete':
				$this->setTrendMainContentDelete();
				break;	
			case 'recommsearchform':
				$this->getRecommSearchMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_recommsearch', $data);
				break;	
			case 'recommsearchwrite':
				$this->setRecommSearchMainDataInsert();
				break;
			case 'newitemform':
				$this->getNewItemMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_newitem', $data);
				break;
			case 'newitemwrite':
				$this->setNewItemMainDataInsert();
				break;
			case 'newitemcontentdelete':
				$this->setNewItemMainContentDelete();
				break;				
			case 'bestitemform':
				$this->getBestItemMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_bestitem', $data);
				break;
			case 'bestitemwrite':
				$this->setBestItemMainDataInsert();
				break;
			case 'bestitemcontentdelete':
				$this->setBestItemMainContentDelete();
				break;
			case 'passchangeform':
				$this->getPassChangeMainRowData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_passchange', $data);
				break;
			case 'passchangewrite':
				$this->setPassChangeMainDataInsert();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main/main_passchange', $data);
				break;	
			case 'main';
				$this->getAdminMainData();
				$data = array_merge($this->_data, $this->_sendData);
				$this->load->view('manage/main', $data);
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
		
		if (in_array('mmno', $this->_arrUri))
		{
			$this->_mmNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'mmno')));
		}
		$this->_mmNum = $this->common->nullCheck($this->_mmNum, 'int', 1);		
		
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
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=main'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
		
		/*
		 * 파라메터 처럼 array로 view 나 model 또는 다른Class로 넘겨줄 데이터들의 모음
		 */
		$this->_sendData = array(
			'currentUri' => $this->_uri,
			'currentUrl' => $this->_currentUrl,
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentParam' => $this->_currentParam,
			'newItemCnt' => $this->_newItemCnt,
			'bestItemCnt' => $this->_bestItemCnt,				
			'searchKey' => $searchKey,
			'searchWord' => $searchWord,
			'sDate' => $sDate,
			'eDate' => $eDate,
			'pageMethod' => $this->_uriMethod,
			'mmNum' => $this->_mmNum,
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
			$this->common->message('관리자만 이용하실 수 있습니다.', '', 'top');
		}
	}
	
	/**
	 * @method name : getStoryMainRowData
	 * 메인 스토리 내용 
	 * 
	 */
	private function getStoryMainRowData()
	{
		$this->_data = $this->main_model->getStoryMainRowData($this->_mmNum, FALSE); 
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setStoryMainDataInsert
	 * 메인 스토리 구성
	 * 한개의 data만 유지  
	 * 
	 */
	private function setStoryMainDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'TITLE' => '메인 스토리',
			'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'STORY'),
			'USE_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$storyData = $this->input->post_get('storymn', FALSE);
		$result = $this->main_model->setStoryMainDataInsert(
			$insData,
			$storyData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
		
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/storyform', 'top');
		}
	}
	
	/**
	 * @method name : setStoryMainContentDelete
	 * 메인 스토리 컨텐츠 내용삭제 
	 * 
	 */
	private function setStoryMainContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentType = $this->input->post_get('type', TRUE);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
		
		$result = $this->main_model->setStoryMainContentDelete(
			$this->_mmNum,
			$contentType,
			$contentNum,
			$contentOrder
		);
		
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}
	
	/**
	 * @method name : getVisualMainRowData
	 * 메인 비주얼  
	 * 
	 */
	private function getVisualMainRowData()
	{
		$this->_data = $this->main_model->getVisualMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setVisualMainDataInsert
	 * 메인 비주얼 구성
	 * 한개의 data만 유지 
	 * 
	 */
	private function setVisualMainDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'TITLE' => '메인 비주얼',
			'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'MAIN'),
			'USE_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
		
		$visualData = $this->input->post_get('visualmn', FALSE);
		$result = $this->main_model->setVisualMainDataInsert(
			$insData,
			$visualData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);

		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/visualform', 'top');
		}
	}
	
	/**
	 * @method name : setVisualMainContentDelete
	 * 메인 비주얼 컨텐츠 내용 삭제 
	 * 
	 */
	private function setVisualMainContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentType = $this->input->post_get('type', TRUE);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
	
		$result = $this->main_model->setVisualMainContentDelete(
			$this->_mmNum,
			$contentType,
			$contentNum,
			$contentOrder
		);
	
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}
	
	/**
	 * @method name : getTodayMainRowData
	 * 메인 투데이스 픽 구성 
	 * 
	 */
	private function getTodayMainRowData()
	{
		$this->_data = $this->main_model->getTodayMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setTodayMainDataInsert
	 * 메인 투데이스 픽 구성
	 * 한개의 data만 유지 
	 * 
	 */
	private function setTodayMainDataInsert()
	{
		$insData = array(
				'USER_NUM' => $this->common->getSession('user_num'),
				'TITLE' => '메인 Todays Pick',
				'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'TODAYPICK'),
				'USE_YN' => 'Y',
				'REMOTEIP' => $this->input->ip_address()
		);
	
		$todayData = $this->input->post_get('todaymn', FALSE);
		$result = $this->main_model->setTodayMainDataInsert(
			$insData,
			$todayData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/todayform', 'top');
		}
	}	
	
	/**
	 * @method name : setTodayMainContentDelete
	 * 메인 투데이스 픽 컨텐츠 삭제 
	 * 
	 */
	private function setTodayMainContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentType = $this->input->post_get('type', TRUE);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
	
		$result = $this->main_model->setTodayMainContentDelete(
			$this->_mmNum,
			$contentType,
			$contentNum,
			$contentOrder
		);
	
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}
	
	/**
	 * @method name : getTodayMainRowData
	 * 메인 트랜드 내용 
	 * 
	 */
	private function getTrendMainRowData()
	{
		$this->_data = $this->main_model->getTrendMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setTrendMainDataInsert
	 * 메인 트랜드 구성
	 * 한개의 data만 유지  
	 * 
	 */
	private function setTrendMainDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'TITLE' => '메인 New Trending',
			'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'NEWTREND'),
			'USE_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$trendData = $this->input->post_get('trendmn', FALSE);
		$result = $this->main_model->setTrendMainDataInsert(
			$insData,
			$trendData,
			$this->_isUpload	//파일 업로드 여부 (TRUE, FALSE)
		);
	
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/trendform', 'top');
		}
	}
	
	/**
	 * @method name : setTrendMainContentDelete
	 * 메인 트랜드 컨텐츠 내용 삭제  
	 * 
	 */
	private function setTrendMainContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentType = $this->input->post_get('type', TRUE);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
	
		$result = $this->main_model->setTrendMainContentDelete(
			$this->_mmNum,
			$contentType,
			$contentNum,
			$contentOrder
		);
	
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}	
	
	/**
	 * @method name : getRecommSearchMainRowData
	 * 메인 추천검색어 내용
	 * 
	 */
	private function getRecommSearchMainRowData()
	{
		$this->_data = $this->main_model->getRecommSearchMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setRecommSearchMainDataInsert
	 * 메인 추천검색어 구성 insert
	 * 
	 */
	private function setRecommSearchMainDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'TITLE' => '추천검색어',
			'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'RECOMMSEARCH'),
			'USE_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$recommData = $this->input->post_get('searchmn', FALSE);
		$result = $this->main_model->setRecommSearchMainDataInsert(
			$insData,
			$recommData
		);
	
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/recommsearchform', 'top');
		}
	}	
	
	/**
	 * @method name : getNewItemMainRowData
	 * 메인 신상품 구성
	 *
	 */
	private function getNewItemMainRowData()
	{
		$this->_data = $this->main_model->getNewItemMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setNewItemMainDataInsert
	 * 메인 신상품 구성
	 * 한개의 data만 유지
	 *
	 */
	private function setNewItemMainDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'TITLE' => '메인 신상품',
			'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'NEWITEM'),
			'USE_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$newItemData = $this->input->post_get('newmn', FALSE);
		$result = $this->main_model->setNewItemMainDataInsert(
			$insData,
			$newItemData
		);
		
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/newitemform', 'top');
		}
	}
	
	/**
	 * @method name : setNewItemMainContentDelete
	 * 메인 신상품 컨텐츠 삭제
	 *
	 */
	private function setNewItemMainContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
	
		$result = $this->main_model->setNewItemMainContentDelete(
			$this->_mmNum,
			$contentNum,
			$contentOrder
		);
	
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}	
	
	/**
	 * @method name : getBestItemMainRowData
	 * 메인 베스트셀러 구성
	 *
	 */
	private function getBestItemMainRowData()
	{
		$this->_data = $this->main_model->getBestItemMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['MALLMAIN_NUM'] : 0;
	}
	
	/**
	 * @method name : setBestItemMainDataInsert
	 * 메인 베스트셀러 구성
	 * 한개의 data만 유지
	 *
	 */
	private function setBestItemMainDataInsert()
	{
		$insData = array(
			'USER_NUM' => $this->common->getSession('user_num'),
			'TITLE' => '메인 베스트셀러 상품',
			'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'BESTITEM'),
			'USE_YN' => 'Y',
			'REMOTEIP' => $this->input->ip_address()
		);
	
		$bestItemData = $this->input->post_get('bestmn', FALSE);
		$result = $this->main_model->setBestItemMainDataInsert(
			$insData,
			$bestItemData
		);
	
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/bestitemform', 'top');
		}
	}
	
	/**
	 * @method name : setBestItemMainContentDelete
	 * 메인 베스트셀러 컨텐츠 삭제
	 *
	 */
	private function setBestItemMainContentDelete()
	{
		$this->_returnUrl = base64_decode($this->_returnUrl);
		$contentNum = $this->input->post_get('no', TRUE);
		$contentOrder = $this->input->post_get('cnorder', TRUE);
	
		$result = $this->main_model->setBestItemMainContentDelete(
			$this->_mmNum,
			$contentNum,
			$contentOrder
		);
	
		if($result > 0)
		{
			$this->common->message('삭제 되었습니다.', $this->_returnUrl, 'top');
		}
	}
	
	/**
	 * @method name : getPassChangeMainRowData
	 * 비번관리 
	 * 
	 */
	private function getPassChangeMainRowData()
	{
		$this->_data = $this->main_model->getPassChangeMainRowData($this->_mmNum, FALSE);
		unset($this->_data['mmNum']);
		$this->_data['mmNum'] = (isset($this->_data['recordSet'])) ? $this->_data['recordSet']['NUM'] : 0;		
	}
	
	/**
	 * @method name : setPassChangeMainDataInsert
	 * 비번관리 insert, update 
	 * 
	 */
	private function setPassChangeMainDataInsert()
	{
		$insData = array(
				'USER_NUM' => $this->common->getSession('user_num'),
				'TITLE' => '비밀번호 변경 주기',
				'ORDER' => $this->input->post_get('passcycle', TRUE),
				'MALLCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'PASSCHANGE'),
				'USE_YN' => 'Y',
				'REMOTEIP' => $this->input->ip_address()
		);
		$result = $this->main_model->setPassChangeMainDataInsert($insData);
		
		if ($result > 0)
		{
			$this->common->message('처리 되었습니다.', '/manage/main_m/passchangeform', 'top');
		}
	}
	
	private function getAdminMainData()
	{
		$shopNum = 0;
		if ($this->_uLevelType == 'SHOP')
		{
			//샵관리자로 로그인한 경우
			$shopNum = $this->common->getSession('shop_num');
		}	
		
		$qData = array(
			'userLevel' => $this->_uLevelType,
			'userNum' => $this->common->getSession('user_num'),
			'shopNum' => $this->common->nullCheck($shopNum, 'int', 0)
		);

		$this->_data = $this->main_model->getAdminMainData($qData);
		
		$this->_listCount = 10;
		$result = $this->board_model->getBoardDataList(
			array(
				'setNum' =>	9020,
				'listCount' => $this->_listCount,
				'currentPage' => $this->_currentPage
			),
			FALSE
		);
		//페이징으로 보낼 데이터
		/*
		$pgData = array(
			'rsTotalCount' => $result['rsTotalCount'],
			'listCount' => $this->_listCount,
			'currentPage' => $this->_currentPage,
			'currentUrl' => $this->_currentUrl,
			'currentParam' => $this->_currentParam
		);
		
		$result['pagination'] = $this->common->listAdminPagingUrl($pgData);
		*/
		$this->_data['notiSet'] = $result;
	}
}