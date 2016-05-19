<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Black_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Black_model extends CI_Model{

	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_encKey = '';
	
	public function __construct() {
		parent::__construct();

		$this->load->library(array('session'));
		$this->load->database(); // Database Load
		$this->tbl = 'USER_BLACK';
		
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);		
	}
	
	public function getBlackDataList($qData, $isDelView)
	{
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
					" AND a.".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : '';
		$whSql .= (empty($qData['readYn'])) ? '' : " AND a.READ_YN = '".strtoupper($qData['readYn'])."'";
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');		
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			b.USER_NAME AS BLACK_USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS BLACK_USER_EMAIL_DEC
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');		
		$this->db->where($whSql);
		$this->db->order_by('a.NUM', $listOrderBy);
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}	
	
	public function getBlackRowData($ubkNum, $isDelView)
	{
		$whSql = "a.NUM = ".$ubkNum;
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
		$this->db->select("
			a.*,
			b.USER_NAME AS BLACK_USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS BLACK_USER_EMAIL_DEC
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');		
		$this->db->where($whSql);
		$this->db->limit(1);
		$result['recordSet'] = $this->db->get()->row_array();
		
		//파일등록정보
		$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
		$this->db->where("TBL_NUM = ".$ubkNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');
		$result['fileSet'] = $this->db->get()->result_array();
		
		return $result;
	}
	
	/**
	 * @method name : setBlackDataInsert
	 * Black list 등록 
	 * 
	 * @param unknown $targetType - user, shop
	 * @param unknown $insData
	 * @param unknown $isUpload
	 * @return unknown
	 */
	public function setBlackDataInsert($qData, $insData, $isUpload)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$resultNum = 0;
		
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();
		
		if ($resultNum > 0)
		{
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$resultNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $resultNum
					)
				);
		
				$uploadResult = $this->common->fileUpload($upConfig);
		
				if (array_key_exists('error', $uploadResult))
				{
					//Transaction 롤백
					$this->db->trans_rollback();
					$errMsg = str_replace('<p>', '', $uploadResult['error']);
					$errMsg = str_replace('</p>', '', $errMsg);
					$this->common->message($errMsg, '-', '');
				}
				else
				{
					for($i=0; $i<count($uploadResult); $i++)
					{
						$this->db->insert($this->_fileTbl, $uploadResult[$i]);
					}
				}
			}
		}		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $resultNum;
	}
	
	/**
	 * @method name : setBlackDataDelete
	 * Black 삭제 (1건)
	 *
	 * @param unknown $ubkNum
	 */
	public function setBlackDataDelete($ubkNum)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
	}
	
	/**
	 * @method name : setBlackGroupDataDelete
	 * Black 삭제 (체크된 내용 모두 삭제)
	 *
	 * @param unknown $delData
	 */
	public function setBlackGroupDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt)
		{
			$this->setBlackDataDelete($dt);
		}
	}	
	
	/**
	 * @method name : getUploadOption
	 * 업로드 환경 설정
	 *
	 * @param string $subdir 업로드될 경로
	 * @return string[]|number[]|boolean[]
	 */
	private function getUploadOption($subdir = '')
	{
		$config = array();
		$baseUploadPath = '.'.$this->config->item('base_uploadPath');
		if (!empty($subdir))
		{
			$uploadPath = $baseUploadPath.$subdir;
		}
		$config['upload_path'] = $uploadPath;
		$config['allowed_types'] = 'doc|hwp|pdf|ppt|xls|pptx|docx|xlsx|zip|rar|gif|jpg|png|psd';
		$config['max_size'] = (1024 * 10);	// 10메가 (단위 KB)
		//$config['max_width'] = '1024';
		//$config['max_height'] = '768';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$config['remove_spaces'] = TRUE;
		$config['create_thumbnail'] = TRUE;
	
		return $config;
	}	
}