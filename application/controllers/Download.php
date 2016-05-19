<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Download
 *
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Download extends CI_Controller {
	
	/**
	 * @var array 최종 data set
	 */
	protected $_data = array();
	
	/**
	 * @var integer 파일 이름
	 */
	protected $_fName = 0;
	
	/**
	 * @var integer 파일 고유 번호
	 */
	protected $_fNum = 0;
	
	/**
	 * @var integer SHOPITEM 고유번호
	 */
	protected $_siNum = 0;
	
	/**
	 * @var integer SHOP 고유번호(프로필 이미지)
	 */
	protected $_sNum = 0;	
	
	/**
	 * @var integer USER 고유번호(프로필 이미지)
	 */
	protected $_uNum = 0;	
		
	function __construct()
	{
		parent::__construct();
		
		$this->load->library(array('session'));		
		$this->load->helper(array('download', 'file'));
		$this->load->model('download_model');
	}
	
	function route() 
	{
		/*
		 * uri 처리관련
		 */
		//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
		$strUri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
		$arrUri = $this->common->segmentExplode($strUri);
		
		//파일고유번호 혹은 파일명으로 다운로드 시도
		if (in_array('fno', $arrUri))
		{
			$this->_fNum = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'fno')));
		}
		$this->_fNum = $this->common->nullCheck($this->_fNum, 'int', 0);
		
		//파일명으로 다운로드 path포함해야 함 (받기전 패스는 base64 인코딩처리되어있어야함)
		if (in_array('fname', $arrUri))
		{
			$this->_fName = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'fname')));
		}
		$this->_fName = $this->common->nullCheck($this->_fName, 'str', '');
		
		if (in_array('sno', $arrUri))
		{
			$this->_sNum = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'sno')));
		}		
		$this->_sNum = $this->common->nullCheck($this->_sNum, 'int', 0);
		
		if (in_array('sino', $arrUri))
		{
			$this->_siNum = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'sino')));
		}
		$this->_siNum = $this->common->nullCheck($this->_siNum, 'int', 0);
		
		if (in_array('uno', $arrUri))
		{
			$this->_uNum = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'uno')));
		}
		$this->_uNum = $this->common->nullCheck($this->_uNum, 'int', 0);		
		
		if ($this->_fNum > 0)
		{
			$this->download();
		}
		
		if ($this->_fName !== '')
		{
			$this->_fName = base64_decode($this->_fName);
			$this->downloadFile();			
		}		
	}
	
	function download()
	{
		//바로 메소드로 접근하는 경우		
		if (empty($this->fNum) || !isset($this->fNum))
		{
			//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
			$strUri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
			$arrUri = $this->common->segmentExplode($strUri);
			
			if (in_array('fNo', $arrUri))
			{
				$this->_fNum = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'fno')));
			}
			
			$this->_fNum = $this->common->nullCheck($this->_fNum, 'int', 0);
		}
		
		if ($this->_fNum > 0)
		{
			if ($this->_sNum > 0 || $this->_uNum > 0)
			{
				$this->_data = $this->download_model->getProfileRowDataList($this->_fNum);
			}
			else if ($this->_siNum > 0)
			{
				$this->_data = $this->download_model->getShopItemRowDataList($this->_fNum);
			}			
			else 
			{
				$this->_data = $this->download_model->getRowDataList($this->_fNum);
			}
			
			if (count($this->_data) > 0)
			{
				$tmpFileName = $this->_data['FILE_TEMPNAME'];
				$fileName = $this->_data['FILE_NAME'];
				$baseUploadPath = $this->_data['FILE_PATH'];	//$this->config->item('base_uploadPath');
				//$fileData = file_get_contents('.'.$baseUploadDir.'/"+ $tmpFileName);
				//force_download($fileName, $fileData);	
				//force_download가 제대로 작동을 안하는 이유로 아래의 내용으로 대체
				$fileData = $this->input->server('DOCUMENT_ROOT')."/".$baseUploadPath.$tmpFileName;
				
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename(iconv('UTF-8','EUC-KR',$fileName)));
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				ob_clean();
				flush();
				readfile($fileData);
				
				if ($this->_sNum > 0 || $this->_uNum > 0)
				{
					$this->download_model->setProfileDataUpdate($this->_fNum);	//다운로드 카운트 증가
				}				
				else if ($this->_siNum > 0)
				{
					$this->download_model->setShopItemDataUpdate($this->_fNum);	//다운로드 카운트 증가
				}
				else
				{
					$this->download_model->setDataUpdate($this->_fNum);	//다운로드 카운트 증가					
				}
			}
			else
			{
				$this->_data = array('heading' => '02', 'message' => '요청내용을 찾을 수 없습니다.');
				$this->load->view('errors/html/error_general', $this->_data);			
			}
		}
		else
		{
			$this->_data = array('heading' => '01', 'message' => '요청형식이 올바르지 않습니다.');
			$this->load->view('errors/html/error_general', $this->_data);			
		}
	}
	
	
	/**
	 * @method name : downloadFile 
	 * 미구현 - 사용하게되면 수정이 꼭 필요
	 * 
	 * @param 
	 * @return return_type
	 */
	public function downloadFile()
	{
		//바로 메소드로 접근하는 경우		
		if (empty($this->_fName) || !isset($this->_fName))
		{
			//$this->uri->uri_string()는 맨앞에 '/'가 붙지 않음
			$strUri = $this->utf8->convert_to_utf8($this->uri->uri_string(), 'EUC-KR'); //한글깨짐 방지
			$arrUri = $this->common->segmentExplode($strUri);
			
			if (in_array('fname', $arrUri))
			{
				$this->_fName = urldecode($this->security->xss_clean($this->common->urlExplode($arrUri, 'fname')));			
			}
			
			$this->_fName = $this->common->nullCheck($this->_fName, 'str', '');
			
			if ($this->_fName !== '')
			{
				$this->_fName = base64_decode($this->_fName);
			}
		}		
		
		if ($this->_fName !== '')
		{
			//$this->_data = file_get_contents($this->_fName); // Read the file's contents
			$fileName = $this->common->getFileName($this->_fName);
			$baseUploadPath = $this->config->item('base_uploadPath');
			//force_download($fileName, $this->_data);
			
			$fileData = $this->input->server('DOCUMENT_ROOT')."/".$baseUploadPath."/".$fileName;
			
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename(iconv('UTF-8','EUC-KR',$fileName)));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			ob_clean();
			flush();
			readfile($fileData);			
		}
		else
		{
			$this->_data = array('heading' => '01', 'message' => '파일명이 올바르지 않습니다.');
			$this->load->view('errors/html/error_general', $this->_data);
		}
	}
	
	/**
	 * @method name : downloadItem
	 * 상품이미지 다운로드
	 * 
	 */
	public function downloadItem()
	{
		
	}
}
?>