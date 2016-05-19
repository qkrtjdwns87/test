<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Memo_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 01.
 * @version:
 */
class Memo_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected  $_tblCodeNum = 0;
	
	protected $_encKey = '';
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'COMMON_MEMO';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
	}
	
	public function getMemoDataList($qData, $isDelView){
		$tblInfo = $this->common->sqlDecrypt($qData['tblInfo'], $this->_encKey); //암호화된 테이블명 복호화
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $tblInfo);
		$whSql = "TBLCODE_NUM = ".$this->_tblCodeNum;
		$whSql .= " AND TBL_NUM = ".$qData['tNum'];
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : "";
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC
		");
		$this->db->from($this->tbl);
		$this->db->where($whSql);		
		$this->db->order_by('NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;

		return $result;
	}
	
	public function setMemoDataInsert($insData)
	{
		$this->db->insert($this->tbl, $insData);
		
		return $this->db->insert_id();	//insert후 반영된 최종 NUM
	}
}
?>