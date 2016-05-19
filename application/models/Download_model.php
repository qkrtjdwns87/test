<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Download_model
 * 다운로드 될 파일정보   
 * 다운로드 카운트 수 증가
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Download_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_shopItemfileTbl = 'SHOPITEM_FILE';
	
	protected $_profilefileTbl = 'PROFILE_FILE';
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load		
	}
	
	public function getRowDataList($fNum = 0){
		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from($this->_fileTbl);
		$this->db->where("(NUM = ".$fNum.")");
		
		return $this->db->get()->row_array();		
	}
	
	public function setDataUpdate($fNum = 0){
		$this->db->set('DOWN_COUNT', 'DOWN_COUNT + 1', FALSE);
		$this->db->where('NUM', $fNum);
		$this->db->update($this->_fileTbl);
	}
	
	/**
	 * @method name : getShopItemRowDataList
	 * SHOPITEM 파일 내용
	 * 
	 * @param number $fNum
	 */
	public function getShopItemRowDataList($fNum = 0){
		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from($this->_shopItemfileTbl);
		$this->db->where("NUM = ".$fNum);		
	
		return $this->db->get()->row_array();
	}
	
	/**
	 * @method name : setShopItemDataUpdate
	 * SHOPITEM 파일 다운로드 카운트 update
	 * 
	 * @param number $fNum
	 */
	public function setShopDataUpdate($fNum = 0){
		$this->db->set('DOWN_COUNT', 'DOWN_COUNT + 1', FALSE);
		$this->db->where('NUM', $fNum);
		$this->db->update($this->_shopItemfileTbl);
	}	
	
	/**
	 * @method name : getProfileRowDataList
	 * PROFILE 파일 내용
	 *
	 * @param number $fNum
	 */
	public function getProfileRowDataList($fNum = 0){
		$this->db->select('*');
		$this->db->limit(1);
		$this->db->from($this->_profilefileTbl);
		$this->db->where("NUM = ".$fNum);
	
		return $this->db->get()->row_array();
	}
	
	/**
	 * @method name : setProfileDataUpdate
	 * PROFILE 파일 다운로드 카운트 update
	 *
	 * @param number $fNum
	 */
	public function setProfileDataUpdate($fNum = 0){
		$this->db->set('DOWN_COUNT', 'DOWN_COUNT + 1', FALSE);
		$this->db->where('NUM', $fNum);
		$this->db->update($this->_profilefileTbl);
	}	
}
?>