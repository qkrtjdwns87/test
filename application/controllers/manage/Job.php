<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Job
 * scheduler
 * 개별 통계 작성
 * 일자별 통계를 나열하고자 할 경우 STATS_DATE 를 outer join을 하여 나열 
 *
 * @author : Administrator
 * @date    : 2016. 02.
 * @version:
 */
class Job extends CI_Controller {

	protected $_uri = '';
	
	protected $_arrUri = array();
	
	protected $_currentUrl = '';
	
	protected $_currentParam = '';	
	
	protected $_currentPage = 1;
	
	protected $_uriMethod = '';
	
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
	
	protected $_encKey = '';
	
	public function __construct()
	{
		parent::__construct ();
		
		$this->load->helper(array('url'));
		$this->load->model(array('job_model'));
		
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function _remap()
	{
		//$this->loginCheck();
		$this->setPrecedeValues();
		
		if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
		{
			@set_time_limit(3000);
		}		
		
		/*
		$startDate = new DateTime('2015-12-01');
		$endDate = new DateTime('2016-02-29');
		
		$interval = date_diff($startDate, $endDate);
		echo $interval->format('%m');
		exit;
		*/
		
		/* uriMethod 처리 */
		switch($this->_uriMethod)
		{
			case 'ordstate':
				$this->setOrderState();
				break;			
			case 'ordstateday':
				$this->setOrderStateDay();
				break;			
			case 'ordstateweek':
				$this->setOrderStateWeek();
				break;				
			case 'ordstatemonth':
				$this->setOrderStateMonth();
				break;
			case 'ordstateyear':
				$this->setOrderStateYear();
				break;
			case 'ordstatefull': //해당파트 전체 통계 작성
				$this->setOrderStateFull();
				break;				
			case 'ordshopstate':
				$this->setShopOrderState();
				break;
			case 'ordshopstateday':
				$this->setOrderStateDay();
				break;
			case 'ordshopstateweek':
				$this->setOrderStateWeek();
				break;
			case 'ordshopstatemonth':
				$this->setOrderStateMonth();
				break;
			case 'ordshopstateyear':
				$this->setOrderStateYear();
				break;	
			case 'ordshopstateFull': //해당파트 전체 통계 작성
				$this->setShopOrderStateFull();
				break;				
			case 'shopstats':
				$this->setShopStats();
				break;	
			case 'shopitemstats':
				$this->setShopItemStats();
				break;
			case 'setshopitemstatsfull': //해당파트 전체 통계 작성
				$this->setShopItemStatsFull();
				break;					
			case 'salestatsday':
				$this->setSalesStatsDay();
				break;
			case 'salestatsweek':
				$this->setSalesStatsWeek();
				break;				
			case 'salestatsmonth':
				$this->setSalesStatsMonth();
				break;				
			case 'salestatsyear':
				$this->setSalesStatsYear();
				break;				
			case 'salestats':
				$this->setSalesStats();
				break;
			case 'salestatsfull': //해당파트 전체 통계 작성
				$this->setSalesStatsFull();
				break;				
			case 'shopsalestatsday':
				$this->setShopSalesStatsDay();
				break;
			case 'shopsalestatsweek':
				$this->setShopSalesStatsWeek();
				break;
			case 'shopsalestatsmonth':
				$this->setShopSalesStatsMonth();
				break;
			case 'shopsalestatsyear':
				$this->setShopSalesStatsYear();
				break;
			case 'shopsalestats':
				$this->setShopSalesStats();
				break;
			case 'shopsalestatsfull': //해당파트 전체 통계 작성
				$this->setShopSalesStatsFull();
				break;				
			case 'itemsalestatsday':
				$this->setItemSalesStatsDay();
				break;
			case 'itemsalestatsweek':
				$this->setItemSalesStatsWeek();
				break;
			case 'itemsalestatsmonth':
				$this->setItemSalesStatsMonth();
				break;
			case 'itemsalestatsyear':
				$this->setItemSalesStatsYear();
				break;
			case 'itemsalestats':
				$this->setItemSalesStats();
				break;
			case 'itemsalestatsfull': //해당파트 전체 통계 작성
				$this->setItemSalesStatsFull();
				break;				
			case 'statsfull': //모든 통계 작성
				$this->setStatsFull();
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
		
		if (in_array('sno', $this->_arrUri))
		{
			$this->_sNum = urldecode($this->security->xss_clean($this->common->urlExplode($this->_arrUri, 'sno')));
		}
		$this->_sNum = $this->common->nullCheck($this->_sNum, 'int', 0);
		
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
		
		$this->_currentParam = (!empty($this->_currentParam)) ? '?cs=memo'.$this->_currentParam : '';
		$this->_currentUrl = '/'.$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->_uriMethod;
	
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
			'sDate' => $sDate,
			'eDate' => $eDate,				
			'pageMethod' => $this->_uriMethod,
			'sNum' => $this->_sNum				
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
	 * @method name : setStatsFull
	 * 모든 통계 작성 
	 * 
	 */
	private function setStatsFull() 
	{
		$this->setOrderStateFull();
		$this->setShopOrderStateFull();
		$this->setShopItemStatsFull();
		$this->setSalesStatsFull();
	}
	
	/**
	 * @method name : setOrderStatefull
	 * 주문상태별 통계(전체모두) 
	 * 
	 */
	private function setOrderStateFull()
	{
		$this->setOrderStateDay();
		$this->setOrderStateWeek();
		$this->setOrderStateMonth();
		$this->setOrderStateYear();
		$this->setOrderState();
	}
	
	/**
	 * @method name : setOrderStateDay
	 * 주문상태별 통계(일자별) 
	 * 
	 */
	private function setOrderStateDay()
	{
		$result = $this->job_model->setOrderStateDay($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setOrderStateWeek
	 * 주문상태별 통계(주차별) 
	 * 
	 */
	private function setOrderStateWeek()
	{
		$result = $this->job_model->setOrderStateWeek($this->_sendData);
		echo $result.'건 update<br />';
	}	
	
	/**
	 * @method name : setOrderStateMonth
	 * 주문상태별 통계(월별) 
	 * 
	 */
	private function setOrderStateMonth()
	{
		$result = $this->job_model->setOrderStateMonth($this->_sendData);
		echo $result.'건 update<br />';
	}	
	
	/**
	 * @method name : setOrderStateYear
	 * 주문상태별 통계(년별) 
	 * 
	 */
	private function setOrderStateYear()
	{
		$result = $this->job_model->setOrderStateYear($this->_sendData);
		echo $result.'건 update<br />';
	}	
	
	/**
	 * @method name : setOrderState
	 * 주문상태별 통계(통합) 
	 * 
	 */
	private function setOrderState()
	{
		$result = $this->job_model->setOrderState($this->_sendData);
		echo $result.'건 update<br />';
	}	
	
	/**
	 * @method name : setShopOrderStateFull
	 * 주문상태별 통계(전체모두) - Craft Shop
	 * 
	 */
	private function setShopOrderStateFull()
	{
		$this->setShopOrderStateDay();
		$this->setShopOrderStateWeek();
		$this->setShopOrderStateMonth();
		$this->setShopOrderStateYear();
		$this->setShopOrderState();
	}
	
	/**
	 * @method name : setShopOrderStateDay
	 * 주문상태별 통계(일자별) - Craft Shop
	 *
	 */
	private function setShopOrderStateDay()
	{
		$result = $this->job_model->setShopOrderStateDay($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setShopOrderStateWeek
	 * 주문상태별 통계(주차별) - Craft Shop
	 *
	 */
	private function setShopOrderStateWeek()
	{
		$result = $this->job_model->setShopOrderStateWeek($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setShopOrderStateMonth
	 * 주문상태별 통계(월별) - Craft Shop
	 *
	 */
	private function setShopOrderStateMonth()
	{
		$result = $this->job_model->setShopOrderStateMonth($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setShopOrderStateYear
	 * 주문상태별 통계(년별) - Craft Shop
	 *
	 */
	private function setShopOrderStateYear()
	{
		$result = $this->job_model->setShopOrderStateYear($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setShopOrderState
	 * 주문상태별 통계(통합) - Craft Shop
	 *
	 */
	private function setShopOrderState()
	{
		$result = $this->job_model->setShopOrderState($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setShopStatsFull
	 * Craft Shop 통계 전체
	 * 판매순위, 플래그순위
	 * 
	 */
	private function setShopItemStatsFull()
	{
		$this->setShopStats();
		$this->setShopItemStats();
	}
	
	/**
	 * @method name : setShopStats
	 * Craft Shop 통계
	 * 판매순위, 플래그순위
	 * 
	 */
	private function setShopStats()
	{
		$this->_sendData['intervalDay'] = 7; //이전통계전환 갱신주기(day)
		$result = $this->job_model->setShopStats($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setShopItemStats
	 * Craft Shop Item 통계
	 * 아이템별 판매순위, 플래그순위
	 * 
	 */
	private function setShopItemStats()
	{
		$this->_sendData['intervalDay'] = 7; //이전통계전환 갱신주기(day)
		$result = $this->job_model->setShopItemStats($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsFull
	 * 매출액 통계 (전체모두)
	 * 
	 */
	private function setSalesStatsFull()
	{
		$this->setSalesStatsDay();
		$this->setSalesStatsWeek();
		$this->setSalesStatsMonth();
		$this->setSalesStatsYear();
	}
	
	/**
	 * @method name : setSalesStatsDay
	 * 일별 매출 통계
	 * 
	 */
	private function setSalesStatsDay()
	{
		$result = $this->job_model->setSalesStatsDay($this->_sendData);
		echo $result.'건 update<br />';		
	}
	
	/**
	 * @method name : setSalesStatsWeek
	 * 주차별 매출 통계 
	 * 
	 */
	private function setSalesStatsWeek()
	{
		$result = $this->job_model->setSalesStatsWeek($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsMonth
	 * 월별 매출 통계 
	 * 
	 */
	private function setSalesStatsMonth()
	{
		$result = $this->job_model->setSalesStatsMonth($this->_sendData);
		echo $result.'건 update<br />';
	}	
	
	/**
	 * @method name : setSalesStatsYear
	 * 년도별 매출 통계 
	 * 
	 */
	private function setSalesStatsYear()
	{
		$result = $this->job_model->setSalesStatsYear($this->_sendData);
		echo $result.'건 update<br />';
	}	
	
	/**
	 * @method name : setShopSalesStatsFull
	 * 샵별 매출액 통계 (전체모두)
	 * 
	 */
	private function setShopSalesStatsFull()
	{
		$this->setShopSalesStatsDay();
		$this->setShopSalesStatsWeek();
		$this->setShopSalesStatsMonth();
		$this->setShopSalesStatsYear();
	}	
	
	/**
	 * @method name : setSalesStatsDay
	 * 샵별 일별 매출 통계
	 *
	 */
	private function setShopSalesStatsDay()
	{
		$result = $this->job_model->setShopSalesStatsDay($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsWeek
	 * 샵별 주차별 매출 통계
	 *
	 */
	private function setShopSalesStatsWeek()
	{
		$result = $this->job_model->setShopSalesStatsWeek($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsMonth
	 * 샵별 월별 매출 통계
	 *
	 */
	private function setShopSalesStatsMonth()
	{
		$result = $this->job_model->setShopSalesStatsMonth($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsYear
	 * 샵별 년도별 매출 통계
	 *
	 */
	private function setShopSalesStatsYear()
	{
		$result = $this->job_model->setShopSalesStatsYear($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setItemSalesStatsFull
	 * 아이템별 매출액 통계 (전체모두)
	 *
	 */
	private function setItemSalesStatsFull()
	{
		$this->setItemSalesStatsDay();
		$this->setItemSalesStatsWeek();
		$this->setItemSalesStatsMonth();
		$this->setItemSalesStatsYear();
	}
	
	/**
	 * @method name : setSalesStatsDay
	 * 아이템별 일별 매출 통계
	 *
	 */
	private function setItemSalesStatsDay()
	{
		$result = $this->job_model->setItemSalesStatsDay($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsWeek
	 * 아이템별 주차별 매출 통계
	 *
	 */
	private function setItemSalesStatsWeek()
	{
		$result = $this->job_model->setItemSalesStatsWeek($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsMonth
	 * 아이템별 월별 매출 통계
	 *
	 */
	private function setItemSalesStatsMonth()
	{
		$result = $this->job_model->setItemSalesStatsMonth($this->_sendData);
		echo $result.'건 update<br />';
	}
	
	/**
	 * @method name : setSalesStatsYear
	 * 아이템별 년도별 매출 통계
	 *
	 */
	private function setItemSalesStatsYear()
	{
		$result = $this->job_model->setItemSalesStatsYear($this->_sendData);
		echo $result.'건 update<br />';
	}	
}