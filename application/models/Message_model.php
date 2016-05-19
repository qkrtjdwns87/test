<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Message_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Message_model extends CI_Model{

	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_encKey = '';
	
	public function __construct() {
		parent::__construct();

		$this->load->library(array('session'));
		$this->load->database(); // Database Load
		$this->tbl = 'MESSAGE';
		
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);		
	}
	
	public function getMessageDataList($qData, $isDelView = FALSE)
	{
		$listOrderBy = "DESC";
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
					" AND a.".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : '';
		$whSql .= (empty($qData['readYn'])) ? '' : " AND a.READ_YN = '".strtoupper($qData['readYn'])."'";
		if (isset($qData['pageMethod']))
		{
			if ($qData['pageMethod'] == 'listuser')
			{
				//써커스와 회원과의 메시지
				$whSql .= " AND a.MSGTYPECODE_NUM IN (17030, 17150)";
			}
			else if ($qData['pageMethod'] == 'listshop')
			{
				//써커스와 샵과의 메시지
				$whSql .= " AND a.MSGTYPECODE_NUM IN (17020, 17140)";
			}
			else if ($qData['pageMethod'] == 'listusershop')
			{
				//회원과 샵과의 메시지
				$whSql .= " AND a.MSGTYPECODE_NUM IN (17040, 17160)";
			}
			else
			{
				//모든 메시지
				//listmall과 동일할 수 있음
				//$whSql .= " AND a.MSGTYPECODE_NUM IN ()";
			}

			/* 일자별 목록 에서 페이징으로 기획이 변경됨
			if (strpos($qData['pageMethod'], 'listview') !== FALSE) //대화 리스트를 보고자 하는 경우
			{
				if (!empty($qData['msgToDate'])) //listview
				{
					$listOrderBy = "ASC";
					$whSql .= " AND a.CREATE_DATE BETWEEN '".$qData['msgToDate']." 00:00:00' AND '".$qData['msgToDate']." 23:59:59' ";
				}
				else
				{
					$whSql .= " AND a.NUM < 0"; //데이터가 안나오게
				}
			}
			*/										
		}
		
		$msgGrpNum = isset($qData['msgGrpNum']) ? $qData['msgGrpNum'] : 0;
		if ($msgGrpNum > 0) //대화방과 연관된 대화메세지(최우선)
		{
			$whSql .= " AND a.MESSAGE_GROUPNUM = ".$qData['msgGrpNum'];
		}
		else 
		{
			if (isset($qData['msgType'])) //연관된 대화메세지
			{
				if (!empty($qData['msgType']))
				{
					$whSql .= ' AND a.MSGTYPECODE_NUM = '.$qData['msgType'];
				}
			}
			
			if (isset($qData['sendUserNum'])) //검색된 회원들 대상
			{
				if (!empty($qData['sendUserNum']))
				{
					$whSql .= ' AND a.MESSAGE_GROUPNUM IN (SELECT MESSAGE_GROUPNUM FROM '.$this->tbl.' WHERE USER_NUM IN ('.$qData['sendUserNum'].')';
				}
			}
			
			if (isset($qData['sendShopNum'])) //검색된 샵들 대상
			{
				if (!empty($qData['sendShopNum']))
				{
					$whSql .= ' AND c.NUM IN ('.$qData['sendShopNum'].')';
				}
			}
			
			if (isset($qData['uNum'])) //회원번호와 연관된 대화메세지
			{
				if (!empty($qData['uNum']))
				{
					$whSql .= ' AND (a.USER_NUM = '.$qData['uNum'].' OR a.TOUSER_NUM = '.$qData['uNum'].')';
				}
			}
			
			if (isset($qData['sNum'])) //샵과 연관된 대화메세지
			{
				if (!empty($qData['sNum']))
				{
					$whSql .= ' AND a.SHOP_NUM = '.$qData['sNum'];
				}
			}
			
			if (isset($qData['siNum'])) //아이템과 연관된 대화메세지
			{
				if (!empty($qData['siNum']))
				{
					$whSql .= ' AND a.SHOPITEM_NUM = '.$qData['siNum'];
				}
			}
			
			if (isset($qData['ordNum'])) //주문과 연관된 대화메세지
			{
				if (!empty($qData['ordNum']))
				{
					$whSql .= ' AND a.ORDERS_NUM = '.$qData['ordNum'];
				}
			}
		}
		
		if (isset($qData['maxMsgNum'])) //대화목록 페이징 상한선
		{
			if ($qData['maxMsgNum'] > 0)
			{
				$whSql .= " AND a.NUM <= ".$qData['maxMsgNum'];				
			}
		}

		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.NUM = c.USER_NUM', 'left outer');		
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		$UserTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$ShopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			HEX(AES_ENCRYPT(CONCAT(a.USER_NUM, '_', b.ULEVELCODE_NUM), '".$this->_encKey."')) AS USER_NUM_ENC,
			(SELECT USER_NUM FROM MESSAGE WHERE NUM = a.MESSAGE_GROUPNUM) AS ORG_USER_NUM,				
			(SELECT TITLE FROM CODE WHERE NUM = a.MSGTYPECODE_NUM) AS MSGTYPECODE_TITLE,
			b.USER_NAME AS SEND_USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS SEND_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.USER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_FILE_INFO,				
			c.NUM AS SEND_SHOP_NUM,
			c.SHOP_NAME AS SEND_SHOP_NAME,
			c.SHOP_CODE AS SEND_SHOP_CODE,
			AES_DECRYPT(UNHEX(c.SHOP_EMAIL), '".$this->_encKey."') AS SEND_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = c.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_SHOP_FILE_INFO,					
			d.USER_NAME AS TO_USER_NAME,
			AES_DECRYPT(UNHEX(d.USER_EMAIL), '".$this->_encKey."') AS TO_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.TOUSER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_FILE_INFO,				
			e.NUM AS TO_SHOP_NUM,
			e.SHOP_NAME AS TO_SHOP_NAME,
			e.SHOP_CODE AS TO_SHOP_CODE,
			AES_DECRYPT(UNHEX(e.SHOP_EMAIL), '".$this->_encKey."') AS TO_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = e.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_SHOP_FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
				AND TBL_NUM = a.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO					
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.NUM = c.USER_NUM', 'left outer');
		$this->db->join('USER AS d', 'a.TOUSER_NUM = d.NUM');
		$this->db->join('SHOP AS e', 'd.NUM = e.USER_NUM', 'left outer');		
		$this->db->where($whSql);
		$this->db->order_by('a.NUM', $listOrderBy);
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
		
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}	
	
	/**
	 * @method name : getMessageDataViewList
	 * VIEW_MESSAGE 리스트 (대화방으로 묶음 - MESSAGE_GROUPNUM) 
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getMessageDataViewList($qData, $isDelView)
	{
		$listOrderBy = "DESC";	
		$addSelect = '';
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
			" AND a.MESSAGE_GROUPNUM IN (SELECT MESSAGE_GROUPNUM FROM ".$this->tbl." WHERE ".$qData['searchKey']." LIKE '%".$qData['searchWord']."%')" : '';
		$whSql .= (empty($qData['readYn'])) ? '' : " AND a.READ_YN = '".strtoupper($qData['readYn'])."'";
		if (isset($qData['pageMethod']))
		{
			if ($qData['pageMethod'] == 'listuser')
			{
				//써커스와 회원과의 메시지
				$whSql .= " AND a.MSGTYPECODE_NUM IN (17030, 17150)";
			}
			else if ($qData['pageMethod'] == 'listshop')
			{
				//써커스와 샵과의 메시지
				$whSql .= " AND a.MSGTYPECODE_NUM IN (17020, 17140)";
			}
			else if ($qData['pageMethod'] == 'listusershop')
			{
				//회원과 샵과의 메시지
				$whSql .= " AND a.MSGTYPECODE_NUM IN (17040, 17160)";
			}
			else
			{
				//모든 메시지
				//$whSql .= " AND a.MSGTYPECODE_NUM IN ()";
			}
		}
		
		if (isset($qData['msgType'])) //연관된 대화메세지
		{
			if (!empty($qData['msgType']))
			{
				$whSql .= ' AND a.MSGTYPECODE_NUM = '.$qData['msgType'];
			}
		}		
	
		if (isset($qData['sendUserNum'])) //검색된 회원들 대상
		{
			if (!empty($qData['sendUserNum']))
			{
				$whSql .= " 
					AND a.MESSAGE_GROUPNUM IN (
						SELECT MESSAGE_GROUPNUM FROM ".$this->tbl." 
						WHERE DEL_YN != 'Y' 
						AND (USER_NUM IN (".$qData['sendUserNum'].") OR TOUSER_NUM IN (".$qData['sendUserNum']."))
				)";
			}
		}
		
		if (isset($qData['sendShopNum'])) //검색된 샵들 대상
		{
			if (!empty($qData['sendShopNum']))
			{
				//$whSql .= ' AND c.NUM IN ('.$qData['sendShopNum'].')';
				$whSql .= " 
					AND a.MESSAGE_GROUPNUM IN (
						SELECT MESSAGE_GROUPNUM FROM ".$this->tbl." 
						WHERE DEL_YN != 'Y' 
						AND SHOP_NUM IN (".$qData['sendShopNum'].")
					)
				";
			}
		}
		
		if (isset($qData['uNum'])) //회원번호와 연관된 대화메세지
		{
			if (!empty($qData['uNum']))
			{
				$whSql .= ' AND (a.USER_NUM = '.$qData['uNum'].' OR a.TOUSER_NUM = '.$qData['uNum'].')';
				$addSelect = "(
					SELECT COUNT(*) FROM MESSAGE
					WHERE MESSAGE_GROUPNUM = a.MESSAGE_GROUPNUM
					AND DEL_YN = 'N'
					AND USER_NUM = ".$qData['uNum']."
				) AS USER_MSG_COUNT, "; //보낸 메시지수
				$addSelect .= "(
					SELECT COUNT(*) FROM MESSAGE
					WHERE MESSAGE_GROUPNUM = a.MESSAGE_GROUPNUM
					AND DEL_YN = 'N'
					AND TOUSER_NUM = ".$qData['uNum']."
				) AS TOUSER_MSG_COUNT, "; //받은 메시지수				
				$addSelect .= "(
					SELECT COUNT(*) FROM MESSAGE
					WHERE MESSAGE_GROUPNUM = a.MESSAGE_GROUPNUM
					AND DEL_YN = 'N'
					AND READ_YN = 'N'
					AND TOUSER_NUM = ".$qData['uNum']."
				) AS TOUSER_UNREAD_COUNT, "; //읽지않은 메시지 수				
			}
		}
		
		if (isset($qData['sNum'])) //샵과 연관된 대화메세지
		{
			if (!empty($qData['sNum']))
			{
				$whSql .= " AND a.MESSAGE_GROUPNUM IN (SELECT MESSAGE_GROUPNUM FROM ".$this->tbl." 
								WHERE DEL_YN != 'Y' AND SHOP_NUM = ".$qData['sNum'].")";
			}
		}		
		
		if (isset($qData['siNum'])) //아이템과 연관된 대화메세지
		{
			if (!empty($qData['siNum']))
			{
				$whSql .= ' AND a.SHOPITEM_NUM = '.$qData['siNum'];
			}
		}	
		
		if (isset($qData['ordNum'])) //주문과 연관된 대화메세지
		{
			if (!empty($qData['ordNum']))
			{
				$whSql .= ' AND a.ORDERS_NUM = '.$qData['ordNum'];
			}
		}		
		
		if (isset($qData['msgGrpNum'])) //대화방과 연관된 대화메세지
		{
			if (!empty($qData['msgGrpNum']))
			{
				$whSql .= " AND a.MESSAGE_GROUPNUM = ".$qData['msgGrpNum'];
			}
		}		
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('VIEW_'.$this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.NUM = c.USER_NUM', 'left outer');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		$UserTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$ShopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			HEX(AES_ENCRYPT(CONCAT(a.USER_NUM, '_', b.ULEVELCODE_NUM), '".$this->_encKey."')) AS USER_NUM_ENC,				
			".$addSelect."
			(SELECT TITLE FROM CODE WHERE NUM = a.MSGTYPECODE_NUM) AS MSGTYPECODE_TITLE,
			b.USER_NAME AS SEND_USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS SEND_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.USER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_FILE_INFO,
			c.NUM AS SEND_SHOP_NUM,
			c.SHOP_NAME AS SEND_SHOP_NAME,
			c.SHOP_CODE AS SEND_SHOP_CODE,
			AES_DECRYPT(UNHEX(c.SHOP_EMAIL), '".$this->_encKey."') AS SEND_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = c.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_SHOP_FILE_INFO,
			d.USER_NAME AS TO_USER_NAME,
			AES_DECRYPT(UNHEX(d.USER_EMAIL), '".$this->_encKey."') AS TO_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.TOUSER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_FILE_INFO,
			e.NUM AS TO_SHOP_NUM,
			e.SHOP_NAME AS TO_SHOP_NAME,
			e.SHOP_CODE AS TO_SHOP_CODE,
			AES_DECRYPT(UNHEX(e.SHOP_EMAIL), '".$this->_encKey."') AS TO_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = e.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_SHOP_FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
				AND TBL_NUM = a.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO
		"); //MAXMSGNO, ORG_SHOP_NAME 는 app에서 사용
		$this->db->from('VIEW_'.$this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.NUM = c.USER_NUM', 'left outer');
		$this->db->join('USER AS d', 'a.TOUSER_NUM = d.NUM');
		$this->db->join('SHOP AS e', 'd.NUM = e.USER_NUM', 'left outer');
		$this->db->where($whSql);
		$this->db->order_by('a.NUM', $listOrderBy);
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
	
		return $result;
	}	
	
	/**
	 * @method name : getMessageRowData
	 * 메세지 1건에 대한 상세보기 
	 * 
	 * @param unknown $msgNum
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getMessageRowData($msgNum, $isDelView)
	{
		$UserTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$ShopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
		
		$whSql = "a.NUM = ".$msgNum;
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
		$this->db->select("
			a.*,
			(SELECT TITLE FROM CODE WHERE NUM = a.MSGTYPECODE_NUM) AS MSGTYPECODE_TITLE,
			b.USER_NAME AS SEND_USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS SEND_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.USER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_FILE_INFO,					
			c.NUM AS SEND_SHOP_NUM,
			c.SHOP_NAME AS SEND_SHOP_NAME,
			c.SHOP_CODE AS SEND_SHOP_CODE,
			AES_DECRYPT(UNHEX(c.SHOP_EMAIL), '".$this->_encKey."') AS SEND_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = c.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_SHOP_FILE_INFO,				
			d.USER_NAME AS TO_USER_NAME,
			AES_DECRYPT(UNHEX(d.USER_EMAIL), '".$this->_encKey."') AS TO_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.TOUSER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_FILE_INFO,					
			e.NUM AS TO_SHOP_NUM,
			e.SHOP_NAME AS TO_SHOP_NAME,
			e.SHOP_CODE AS TO_SHOP_CODE,
			AES_DECRYPT(UNHEX(e.SHOP_EMAIL), '".$this->_encKey."') AS TO_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = e.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_SHOP_FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
				AND TBL_NUM = a.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO				
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.NUM = c.USER_NUM', 'left outer');
		$this->db->join('USER AS d', 'a.TOUSER_NUM = d.NUM');
		$this->db->join('SHOP AS e', 'd.NUM = e.USER_NUM', 'left outer');		
		$this->db->where($whSql);
		$this->db->limit(1);
		$result['recordSet'] = $this->db->get()->row_array();
		
		//파일등록정보
		$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$tblSubCodeNum);
		$this->db->where("TBL_NUM = ".$msgNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');
		$result['fileSet'] = $this->db->get()->result_array();
		
		return $result;
	}
	
	/**
	 * @method name : getMessageGroupRowData
	 * groupnum 원본 메세지 1건에 대한 상세보기
	 *
	 * @param unknown $msgNum
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */	
	public function getMessageGroupRowData($msgGrpNum)
	{
		$UserTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$ShopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
		
		$whSql = "MESSAGE_DEPTH = 0 AND a.MESSAGE_GROUPNUM = ".$msgGrpNum;
		$this->db->select("
			a.*,
			(SELECT TITLE FROM CODE WHERE NUM = a.MSGTYPECODE_NUM) AS MSGTYPECODE_TITLE,
			b.USER_NAME AS SEND_USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS SEND_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.USER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_FILE_INFO,
			c.NUM AS SEND_SHOP_NUM,
			c.SHOP_NAME AS SEND_SHOP_NAME,
			c.SHOP_CODE AS SEND_SHOP_CODE,
			AES_DECRYPT(UNHEX(c.SHOP_EMAIL), '".$this->_encKey."') AS SEND_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = c.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS SEND_SHOP_FILE_INFO,
			d.USER_NAME AS TO_USER_NAME,
			AES_DECRYPT(UNHEX(d.USER_EMAIL), '".$this->_encKey."') AS TO_USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$UserTblCodeNum."
				AND TBL_NUM = a.TOUSER_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_FILE_INFO,
			e.NUM AS TO_SHOP_NUM,
			e.SHOP_NAME AS TO_SHOP_NAME,
			e.SHOP_CODE AS TO_SHOP_CODE,
			AES_DECRYPT(UNHEX(e.SHOP_EMAIL), '".$this->_encKey."') AS TO_SHOP_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = e.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS TO_SHOP_FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
				AND TBL_NUM = a.NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('USER AS b', 'a.USER_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.NUM = c.USER_NUM', 'left outer');
		$this->db->join('USER AS d', 'a.TOUSER_NUM = d.NUM');
		$this->db->join('SHOP AS e', 'd.NUM = e.USER_NUM', 'left outer');
		$this->db->where($whSql);
		$this->db->limit(1);
		$result['recordSet'] = $this->db->get()->row_array();

		return $result;
	}
	
	/**
	 * @method name : setMessageDataInsert
	 * 메세지 발송 
	 * 
	 * @param unknown $targetType - user, shop
	 * @param unknown $sendallYn - 전체발송 여부 (시스템부하문제가 있어 잠시 보류)
	 * @param unknown $targetNum
	 * @param unknown $insData
	 * @param unknown $isUpload
	 * @return unknown
	 */
	public function setMessageDataInsert($qData, $sendallYn, $targetNum, $insData, $isUpload)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$resultNum = $msgGrpNum = 0;
		$arrNo = explode(',', $targetNum);
		$arrNo = array_unique($arrNo); //중복제거
		if (isset($qData['msgGrpNum']))
		{
			if ($qData['msgGrpNum'] > 0)
			{
				$msgGrpNum = $qData['msgGrpNum'];
			}
		}
		
		if ($msgGrpNum > 0)
		{
			//대화가 이어지는 경우(원글과 이어지는 경우)
			//DEPTH는 원글에 쌓여진 ORDER와 같은 역할이나
			//현재는 원글(DEPTH = 0)인것만을 판단하므로 1로 고정함 
			//$msgdepth = $this->common->nullCheck($qData['msgDepth'], 'int', 0);
			$insData['MESSAGE_DEPTH'] = 1;//intval($qData['msgDepth']) + 1;
			$insData['MESSAGE_GROUPNUM'] = $msgGrpNum;
		}
		
		for($i=0; $i<count($arrNo); $i++)
		{
			$targetNum = ($insData['TARGET_TYPE'] == 'M') ? $this->common->getSuperAdminUserNum() : $arrNo[$i]; //MALL 소유자의 회원고유번호 조회(슈퍼어드민)
			$insData['TOUSER_NUM'] = $targetNum;
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();

			if ($i == 0 && $msgGrpNum == 0) //처음한번만(최초 개설되는 원글만 해당)
			{
				//insert후 반영된 최종 NUM
				//최초원본구분을 위해 $resultNum를 MESSAGE_GROUPNUM에 update
				$this->db->set('MESSAGE_GROUPNUM', $resultNum);
				$this->db->where('NUM', $resultNum);
				$this->db->update($this->tbl);
				$insData['MESSAGE_GROUPNUM'] = $resultNum; //다음 data의 MESSAGE_GROUPNUM insert를 하기위해
				$msgGrpNum = $resultNum; 
			}

			if ($msgGrpNum > 0)
			{
				//바로전 데이터의 샵, 아이템, 주문번호를 일치시켜 선행메세지와 동일함을 유지한다
				//원본의 내용을 신규생성된 메시지에 update
				//샵과 주문번호만 일치시키도록 변경
				$this->db->select('SHOP_NUM, SHOPITEM_NUM, ORDERS_NUM, MSGTYPECODE_NUM');
				$this->db->from($this->tbl);
				$this->db->order_by('NUM', 'DESC');
				$this->db->where("NUM < ".$resultNum);
				$this->db->where("(DEL_YN IN ('N', 'M'))");
				$this->db->where('MESSAGE_GROUPNUM', $msgGrpNum); //같은 그룹번호 안에서				
				$this->db->limit(1);				
				$data = $this->db->get()->row_array();
				if ($data)
				{
					$upData = array(
						'SHOP_NUM' => $data['SHOP_NUM'],
						//'SHOPITEM_NUM' => $data['SHOPITEM_NUM']
					);
					
					if (!empty($insData['ORDERS_NUM']))
					{
						$upData['ORDERS_NUM'] = $insData['ORDERS_NUM'];
					}
					if ($insData['MSGTYPECODE_NUM'] == 17000)
					{
						$upData['MSGTYPECODE_NUM'] = $data['MSGTYPECODE_NUM'];
					}
					$this->db->where('NUM', $resultNum);
					$this->db->update($this->tbl, $upData);					
				}
				
				//원글(대화방개설글)의 UPDATE_DATE, MSG_COUNT 갱신
				$msgCount = $this->getMessageGroupRecordCount($msgGrpNum);
				$this->db->set('UPDATE_DATE', date('Y-m-d H:i:s'));
				$this->db->set('MESSAGE_COUNT', $msgCount);
				$this->db->where('NUM', $msgGrpNum);
				$this->db->update($this->tbl);
			}			
		}

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

						//대화 타입 update
						$this->db->set('MSGCONTENT_TYPE', 'F'); //I :아이템대화(아이템내용), O:주문대화(주문내용), F:파일첨부(이미지), N:일반대화(normal)
						$this->db->where('NUM', $resultNum);
						$this->db->update($this->tbl);						
					}
				}
			}
		}		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $resultNum;
	}
	
	/**
	 * @method name : getListViewMessageDate
	 * 메시지 대화보기에서 리스트가 존재하는 전,후일자 조회 
	 * 메시지 리스트가 일자에서 페이징으로 바뀌면서
	 * msgNum -> msgGrpNum 조회로 변경
	 * 
	 * @param unknown $qData
	 * @return unknown[]|string[]
	 */
	public function getListViewMessageDate($qData)
	{
		$rowDt = array();
		if (isset($qData['msgGrpNum']))
		{
			//해당글 정보
			$result = $this->getMessageGroupRowData($qData['msgGrpNum'], FALSE);
			$rowDt = $result['recordSet'];
		}
		
		/* 일자별 목록 에서 페이징으로 기획이 변경됨		
		$toName = $prevDate = $nextDate = $toDate = $dtToDate = '';
		$toDate = $qData['msgToDate'];
		if (isset($qData['msgNum']))
		{
			//해당글 정보
			$result = $this->getMessageRowData($qData['msgNum'], FALSE); //메시지 한건 조회
			$rowDt = $result['recordSet'];
			if ($rowDt)
			{
				$dtToDate = substr($rowDt['CREATE_DATE'], 0, 10);
			}	
		}

		if (empty($toDate)) $toDate = $dtToDate;  //비어있으면 오늘 날짜로 대체
		if (empty($toDate)) $toDate = date('Y-m-d');

		//$result = $this->getListViewMessageDateWithUser($rowDt, $qData['msgToDate']);
		//toDate를 제외한 이전 대화 일자
		$whSql = "DEL_YN = 'N' AND MESSAGE_GROUPNUM = ".$qData['msgGrpNum'];			
		$whSql .= " AND CREATE_DATE < '".$toDate." 00:00:00'";
		$this->db->select("CREATE_DATE");
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$this->db->order_by('NUM', 'DESC');
		$this->db->limit(1);
		$result = $this->db->get()->row_array();
		$prevDate = (!empty($result['CREATE_DATE'])) ? substr($result['CREATE_DATE'], 0, 10) : '';
		
		//toDate를 제외한 이후 대화 일자
		$whSql = "DEL_YN = 'N' AND MESSAGE_GROUPNUM = ".$qData['msgGrpNum'];			
		$whSql .= " AND CREATE_DATE > '".$toDate." 23:59:59'";
		$this->db->select("CREATE_DATE");
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$this->db->order_by('NUM', 'ASC');
		$this->db->limit(1);
		$result = $this->db->get()->row_array();
		$nextDate = (!empty($result['CREATE_DATE'])) ? substr($result['CREATE_DATE'], 0, 10) : '';		
		
		$result = array(
			'msgData' => $rowDt,
			'msgToDate' => $toDate,
			'msgPrevDate' => $prevDate,
			'msgNextDate' => $nextDate
		);
		*/
		
		return array('msgData' => $rowDt);
	}
	
	/**
	 * @method name : getListViewMessageDateWithUser
	 * MESSAGE_GROUPNUM을 운영하지 않는 경우(미완료 개발중지)
	 * 
	 * @param unknown $rowDt
	 * @param unknown $toDate
	 */
	public function getListViewMessageDateWithUser($rowDt, $toDate)
	{
		$result = array();
		if ($rowDt)
		{
			if (empty($toDate)) $toDate = substr($rowDt['CREATE_DATE'], 0, 10);
			$msgType = $rowDt['MSGTYPECODE_NUM'];
			$sendUserNum = $rowDt['USER_NUM'];
			$toUserNum = $rowDt['TOUSER_NUM'];
				
			//이전 대화 일자
			$whSql = "(USER_NUM = ".$sendUserNum." OR TOUSER_NUM = ".$sendUserNum.")";
			$whSql .= " AND ";
			$whSql .= "(USER_NUM = ".$toUserNum." OR TOUSER_NUM = ".$toUserNum.")";
			$this->db->select("
				*,
			");
			$this->db->from($this->tbl);
			$this->db->where($whSql);
			$result = $this->db->get()->row_array();			
		}
		
		return $result;
	}
	
	/**
	 * @method name : getMessageGroupRecordCount
	 * GROUP 안에 등록된 메시지 총 갯수
	 * (개설된 대화방내 총 메시지 등록개수) 
	 * 
	 * @param unknown $msgGrpNum
	 */
	public function getMessageGroupRecordCount($msgGrpNum)
	{
		$whSql = "DEL_YN = 'N' AND MESSAGE_GROUPNUM = ".$msgGrpNum;
		$this->db->select("COUNT(*) AS COUNT");
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		
		return $this->db->get()->row()->COUNT;
	}

	/**
	 * @method name : getMessageGroupNum
	 * 메시지 원글의 고유번호 조회
	 * 
	 * @param unknown $msgNum
	 */
	public function getMessageGroupNum($msgNum)
	{
		$whSql = "NUM = ".$msgNum;
		$this->db->select("MESSAGE_GROUPNUM");
		$this->db->from($this->tbl);
		$this->db->where($whSql);
	
		return $this->db->get()->row()->MESSAGE_GROUPNUM;
	}	

	/**
	 * @method name : setMessageDataDelete
	 * MESSAGE 삭제 (1건) - 개별 고유번호
	 *
	 * @param unknown $msgNum
	 */
	public function setMessageDataDelete($msgNum)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		$msgGrpNum = $this->getMessageGroupNum($msgNum);
		
		//삭제 플래그 변경
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('NUM', $msgNum);
		$this->db->update($this->tbl);
	
		//원글(대화방개설글)의 MSG_COUNT 갱신
		$msgCount = $this->getMessageGroupRecordCount($msgGrpNum);
		$this->db->set('MESSAGE_COUNT', $msgCount);
		$this->db->where('NUM', $msgGrpNum);
		$this->db->update($this->tbl);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
	}
	
	/**
	 * @method name : setMessageGNumDataDelete
	 * MESSAGE 삭제 (1건) - MESSAGE_GROUPNUM 전체
	 * 
	 * @param unknown $msgGrpNum
	 */
	public function setMessageGNumDataDelete($msgGrpNum)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		//삭제 플래그 변경
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('MESSAGE_GROUPNUM', $msgGrpNum);
		$this->db->update($this->tbl);
	
		//원글(대화방개설글)의 MSG_COUNT 갱신
		$msgCount = $this->getMessageGroupRecordCount($msgGrpNum);
		$this->db->set('MESSAGE_COUNT', $msgCount);
		$this->db->where('NUM', $msgGrpNum);
		$this->db->update($this->tbl);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	}	
	
	/**
	 * @method name : setMessageGroupDataDelete
	 * MESSAGE 삭제 (체크된 내용 모두 삭제 - 고유번호)
	 *
	 * @param unknown $delData
	 */
	public function setMessageGroupDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt)
		{
			$this->setMessageDataDelete($dt);
		}
	}	
	
	/**
	 * @method name : setMessageGroupNumDataDelete
	 * MESSAGE 삭제 (체크된 내용 모두 삭제 - 그룹번호)
	 * MESSAGE_GROUPNUM이 원글 번호
	 * 
	 * @param unknown $delData
	 */
	public function setMessageGroupNumDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt)
		{
			$this->setMessageGNumDataDelete($dt);
		}
	}	
	
	public function getMessageContinueData()
	{
		
	}
	
	/**
	 * @method name : getMaxMsgNumWithGrpNum
	 * 메시지 그룹번호안에서 최대값 
	 * 
	 * @param unknown $msgGrpNum
	 */
	public function getMaxMsgNumWithGrpNum($msgGrpNum)
	{
		$result = $this->db->query("
			SELECT MAX(NUM) AS MAXNUM FROM ".$this->tbl." 
			WHERE DEL_YN IN ('N', 'M')
			AND MESSAGE_GROUPNUM = ".$msgGrpNum."
		")->row()->MAXNUM;

		return $result;
	}
	
	/**
	 * @method name : setNewMessageInit
	 * 최초 메시지 창을 띄울때(APP)
	 * 
	 * @param unknown $qData
	 * @return unknown[]|number[]
	 */
	public function setNewMessageInit($qData)
	{
		$result = array();
		$msgGrpNum = $maxMsgNum = 0; //$maxMsgNum 페이징 리스트시 신규생성대화와의 중복 방지
		$msgToDate = '';
		$msgTargetNum = 0;
		$msgType = $qData['msgType'];
		$msgToDate = date('Y-m-d');		
		$isApp = (isset($qData['isApp'])) ? $qData['isApp'] : FALSE;
		//유저 샵간의 신규메시지 생성시(제품상세에서 메시지 작성등...)
		//해당 아이템으로 대화한 이력이 있는지 확인
		//있는 경우 해당 그룹번호 이어서 작성
		//없는 경우 아이템 대화 한건 자동 생성
		if ($qData['pageMethod'] == 'new_user_shop' || $qData['pageMethod'] == 'new_user_shopq')
		{
			$msgGrpNum = $msgTargetNum = 0;
			$msgTargetNum = $this->common->getUserNumByShopNum($qData['sNum']);
			$senderType = 'U';
			$targetType = 'S';
			
			if ($qData['ordNum'] > 0)
			{
				//주문번호가 들어오는 경우 주문내역에 대한 대화메세지 자동 생성
				//동일 샵관련 메시지 내용이 있는지 확인후
				$whSql = "(USER_NUM = ".$qData['uNum']." OR TOUSER_NUM = ".$qData['uNum'].")";
				$whSql .= " AND SHOP_NUM = ".$qData['sNum'];
				$whSql .= " AND MSGTYPECODE_NUM IN (17040, 17160)";
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$this->db->limit(1);
				$result = $this->db->get()->row_array();
				if ($result)
				{
					//관련된 내용이 있는 경우 groupnum 연결하여 진행 시킨다
					$msgGrpNum = $result['MESSAGE_GROUPNUM'];
					$maxMsgNum = $this->getMaxMsgNumWithGrpNum($msgGrpNum);
				}
				
				//동일 주문 내용이 있는지 확인
				$whSql = "(USER_NUM = ".$qData['uNum']." OR TOUSER_NUM = ".$qData['uNum'].")";
				$whSql .= " AND SHOP_NUM = ".$qData['sNum'];
				$whSql .= " AND ORDERS_NUM = ".$qData['ordNum'];
				$whSql .= " AND MSGTYPECODE_NUM IN (17040, 17160)";
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$this->db->limit(1);
				$result = $this->db->get()->row_array();
				if (!$result)
				{
					//주문내용 조회
					$ordDt = $qData['ordData'];
					if ($ordDt)
					{
						$msgContent = substr($ordDt['CREATE_DATE'], 0, 10).'|';
						$msgContent .= $ordDt['ORDER_CODE'];
					}
					
					$msgContentType = 'O'; //I :아이템대화(아이템내용), O:주문대화(주문내용), F:파일첨부(이미지), N:일반대화(normal)
					
					$insData = array(
						'USER_NUM' => $qData['uNum'],
						'CONTENT' => $msgContent,
						'SENDER_TYPE' => $senderType,
						'TARGET_TYPE' => $targetType,
						'MSGTYPECODE_NUM' => $msgType,
						'SHOP_NUM' => (!empty($qData['sNum'])) ? $qData['sNum'] : NULL, //관련 샵번호가 있는 경우
						'ORDERS_NUM' => $qData['ordNum'], //관련 주문번호가 있는 경우
						'REMOTEIP' => $this->input->ip_address(),
						'MSGCONTENT_TYPE' => $msgContentType
					);
					$resultNum = $this->setMessageDataInsert(
						array(
							'msgType' => $msgType,
							'msgGrpNum' => $msgGrpNum
						),
						'N', //전체 발송 여부
						$msgTargetNum,
						$insData,
						FALSE	//파일 업로드 여부 (TRUE, FALSE)
					);
					
					//GROUPNUM을 얻기위해 가장최근 대화 내용 한건을 조회
					$whSql = "NUM = ".$resultNum;
					$this->db->select("*");
					$this->db->from($this->tbl);
					$this->db->where($whSql);
					$result = $this->db->get()->row_array();
					$msgGrpNum = $result['MESSAGE_GROUPNUM'];
					$maxMsgNum = $result['NUM'];
				}
			}
			else 
			{
				//동일 샵관련 내용이 있는지 확인후
				$whSql = "(USER_NUM = ".$qData['uNum']." OR TOUSER_NUM = ".$qData['uNum'].")";
				$whSql .= " AND SHOP_NUM = ".$qData['sNum'];
				//$whSql .= " AND SHOPITEM_NUM = ".$qData['siNum'];
				$whSql .= " AND MSGTYPECODE_NUM IN (17040, 17160)";
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$this->db->limit(1);
				$result = $this->db->get()->row_array();
				if ($result)
				{
					//관련된 내용이 있는 경우 groupnum 연결하여 진행 시킨다
					$msgGrpNum = $result['MESSAGE_GROUPNUM'];
					$maxMsgNum = $this->getMaxMsgNumWithGrpNum($msgGrpNum);
				}				

				$siNum = 0;
				if (isset($qData['siNum']))
				{
					if ($qData['siNum'] > 0)
					{
						$siNum = $qData['siNum'];						
					}
				}

				//동일 샵관련 아이템 내용이 있는지 다시 확인
				$whSql = "(USER_NUM = ".$qData['uNum']." OR TOUSER_NUM = ".$qData['uNum'].")";
				$whSql .= " AND SHOP_NUM = ".$qData['sNum'];
				$whSql .= " AND SHOPITEM_NUM = ".$siNum;
				$whSql .= " AND MSGTYPECODE_NUM IN (17040, 17160)";
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$this->db->limit(1);
				$result = $this->db->get()->row_array();
				if (!$result && $siNum > 0)
				{
					//아이템과 관련된 대화내용이 없는경우
					//아이템 대화 내용 한건을 자동 생성
					$itDt = $qData['itemData'];
					$itemName = $itDt['ITEM_NAME'];
					$arrFile = explode('|', ($isApp) ? $itDt['M_FILE_INFO'] : $itDt['FILE_INFO']);
					$img = '';
					$defaultImg = '';
					if (!empty($arrFile[0]))
					{
						if ($arrFile[4] == 'Y')	//썸네일생성 여부
						{
							$img = str_replace('.', '_s.', $arrFile[2].$arrFile[3]);
						}
						else
						{
							$img = $arrFile[2].$arrFile[3];
						}
					}
					$fileName = (!empty($img)) ? $img : $defaultImg;
					$msgContent = $fileName.'|'.$itemName;

					$msgContentType = 'I'; //I :아이템대화(아이템내용), O:주문대화(주문내용), F:파일첨부(이미지), N:일반대화(normal)
				
					$insData = array(
						'USER_NUM' => $qData['uNum'],
						'CONTENT' => $msgContent,
						'SENDER_TYPE' => $senderType,
						'TARGET_TYPE' => $targetType,
						'MSGTYPECODE_NUM' => $msgType,						
						'SHOP_NUM' => (!empty($qData['sNum'])) ? $qData['sNum'] : NULL, //관련 샵번호가 있는 경우
						'SHOPITEM_NUM' => (!empty($qData['siNum'])) ? $qData['siNum'] : NULL, //관련 아이템번호가 있는 경우
						'REMOTEIP' => $this->input->ip_address(),
						'MSGCONTENT_TYPE' => $msgContentType
					);
				
					$resultNum = $this->setMessageDataInsert(
						array(
							'msgType' => $msgType,
							'msgGrpNum' => $msgGrpNum
						),
						'N', //전체 발송 여부
						$msgTargetNum,
						$insData,
						FALSE	//파일 업로드 여부 (TRUE, FALSE)
					);
					
					//GROUPNUM을 얻기위해 가장최근 대화 내용 한건을 조회
					$whSql = "NUM = ".$resultNum;
					$this->db->select("*");
					$this->db->from($this->tbl);
					$this->db->where($whSql);
					$result = $this->db->get()->row_array();
					$msgGrpNum = $result['MESSAGE_GROUPNUM'];
					$maxMsgNum = $result['NUM'];
				}				
			}

			$qData = array(
				'msgToDate' => $msgToDate,
				'maxMsgNum' => $maxMsgNum,
				'msgGrpNum' => $msgGrpNum,
				'msgType' => $msgType,
				'uNum' => $qData['uNum'],
				'sNum' => $qData['sNum'],
				'listCount' => $qData['listCount'],
				'currentPage' => $qData['currentPage']
			);
			
			if ($result) $qData = $qData + $result; 
	
			$msgDt = $this->getMessageDataList($qData);
		}
		else if ($qData['pageMethod'] == 'new_user_mall' || $qData['pageMethod'] == 'new_user_mallq')
		{
			//여기서 tatgetnum은 큰의미가 없음
			$msgGrpNum = $msgTargetNum = 0;
			$senderType = 'U';
			$targetType = 'M';			
			//$msgType = ($qData['pageMethod'] == 'new_user_mall') ? 17150 : 17030;
			//가장최근 대화 내용 한건을 조회
			$whSql = "(USER_NUM = ".$qData['uNum']." OR TOUSER_NUM = ".$qData['uNum'].")";
			$whSql .= " AND MSGTYPECODE_NUM IN (17030, 17150)";
			$this->db->select("*");
			$this->db->from($this->tbl);
			$this->db->where($whSql);
			$this->db->limit(1);
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			if ($result)
			{
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $this->getMaxMsgNumWithGrpNum($msgGrpNum);
			}
			else 
			{
				//써커스와의 대화시작을 알리는 대화메시지 하나를 생성(사용자쪽에서는 보여지면 안됨)
				//msgGrpNum 생성을 위해 필요
				$insData = array(
					'USER_NUM' => $qData['uNum'],
					'CONTENT' => 'Circus와의 대화를 시작합니다.',
					'SENDER_TYPE' => $senderType,
					'TARGET_TYPE' => $targetType,
					'MSGTYPECODE_NUM' => $msgType,
					'REMOTEIP' => $this->input->ip_address(),
					'MSGCONTENT_TYPE' => 'N',
					'DEL_YN' => 'M'
				);
				
				$resultNum = $this->setMessageDataInsert(
					array('msgType' => $msgType),
					'N', //전체 발송 여부
					$msgTargetNum,
					$insData,
					FALSE	//파일 업로드 여부 (TRUE, FALSE)
				);
					
				//GROUPNUM을 얻기위해 가장최근 대화 내용 한건을 조회
				$whSql = "NUM = ".$resultNum;
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$result = $this->db->get()->row_array();
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $resultNum;				
			}
			
			$qData = array(
				'msgToDate' => $msgToDate,
				'maxMsgNum' => $maxMsgNum,					
				'msgType' => $msgType,
				'uNum' => $qData['uNum'],
				'listCount' => $qData['listCount'],
				'currentPage' => $qData['currentPage']
			);
			if ($msgGrpNum > 0) $qData['msgGrpNum'] = $msgGrpNum;
			
			$msgDt = $this->getMessageDataList($qData);
		}
		else if ($qData['pageMethod'] == 'new_shop_mall' || $qData['pageMethod'] == 'new_shop_mallq')
		{
			//여기서 tatgetnum은 큰의미가 없음
			$msgGrpNum = $msgTargetNum = 0;
			$senderType = 'S';
			$targetType = 'M';			
			//$msgType = ($qData['pageMethod'] == 'new_shop_mall') ? 17140 : 17020;
			//가장최근 대화 내용 한건을 조회
			$whSql = "(USER_NUM = ".$qData['uNum']." OR TOUSER_NUM = ".$qData['uNum'].")";
			$whSql .= " AND SHOP_NUM = ".$qData['sNum'];
			$whSql .= " AND MSGTYPECODE_NUM IN (17020, 17140)";
			$this->db->select("*");
			$this->db->from($this->tbl);
			$this->db->where($whSql);
			$this->db->limit(1);
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			if ($result)
			{
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $this->getMaxMsgNumWithGrpNum($msgGrpNum);
			}
			else
			{
				//써커스와의 대화시작을 알리는 대화메시지 하나를 생성(사용자쪽에서는 보여지면 안됨)
				//msgGrpNum 생성을 위해 필요
				$insData = array(
					'USER_NUM' => $qData['uNum'],
					'CONTENT' => 'Circus와의 대화를 시작합니다.',
					'SENDER_TYPE' => $senderType,
					'TARGET_TYPE' => $targetType,
					'MSGTYPECODE_NUM' => $msgType,
					'REMOTEIP' => $this->input->ip_address(),
					'MSGCONTENT_TYPE' => 'N',
					'DEL_YN' => 'M'
				);
			
				$resultNum = $this->setMessageDataInsert(
					array('msgType' => $msgType),
					'N', //전체 발송 여부
					$msgTargetNum,
					$insData,
					FALSE	//파일 업로드 여부 (TRUE, FALSE)
				);
					
				//GROUPNUM을 얻기위해 가장최근 대화 내용 한건을 조회
				$whSql = "NUM = ".$resultNum;
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$result = $this->db->get()->row_array();
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $resultNum;
			}
			
			$qData = array(
				'msgToDate' => $msgToDate,
				'maxMsgNum' => $maxMsgNum,
				'msgType' => $msgType,
				'uNum' => $qData['uNum'],
				'sNum' => $qData['sNum'],
				'listCount' => $qData['listCount'],
				'currentPage' => $qData['currentPage']
			);
			if ($msgGrpNum > 0) $qData['msgGrpNum'] = $msgGrpNum;
				
			$msgDt = $this->getMessageDataList($qData);			
		}
		
		/*
		$dateDt = array();
		if ($msgGrpNum > 0)
		{
			//$msgToDate를 기준으로 전,후 대화일자
			$dateDt = $this->getListViewMessageDate(
				array(
					'msgToDate' => $msgToDate,
					'msgGrpNum' => $msgGrpNum
				)
			);
			unset($dateDt['msgData']);
		}
		*/
		
		return array(
			//'msgDateSet' => $dateDt,
			'msgDataSet' => $msgDt,
			'maxMsgNum' => $maxMsgNum,				
			'msgGrpNum' => $msgGrpNum,
			'msgToDate' => $msgToDate,
			'msgTargetNum' => $msgTargetNum,
			'msgType' => $msgType
		);
	}
	
	public function setAdminNewMessageInit($qData)
	{
		$result = array();
		$msgGrpNum = $maxMsgNum = 0; //$maxMsgNum 페이징 리스트시 신규생성대화와의 중복 방지
		$msgToDate = '';
		$msgTargetNum = $qData['targetNum'];
		$msgType = $qData['msgType'];
		
		if ($qData['pageMethod'] == 'new_user_mall' || $qData['pageMethod'] == 'new_user_mallq')
		{
			//여기서 tatgetnum은 큰의미가 없음
			$msgGrpNum = 0;
			
			//target(TOUSER_NUM)으로 가장최근 대화 내용 한건을 조회
			$whSql = "TOUSER_NUM = ".$msgTargetNum;
			$whSql .= " AND DEL_YN = 'M'";
			$whSql .= " AND MSGTYPECODE_NUM IN (17030, 17150)";
			$this->db->select("*");
			$this->db->from($this->tbl);
			$this->db->where($whSql);
			$this->db->limit(1);
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			if ($result)
			{
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $this->getMaxMsgNumWithGrpNum($msgGrpNum);
			}
			else
			{
				//써커스와의 대화시작을 알리는 대화메시지 하나를 생성(사용자쪽에서는 보여지면 안됨)
				//msgGrpNum 생성을 위해 필요
				$insData = array(
					'USER_NUM' => $qData['uNum'],
					'CONTENT' => 'Circus와의 대화를 시작합니다.',
					'SENDER_TYPE' => $qData['senderType'],
					'TARGET_TYPE' => $qData['targetType'],
					'MSGTYPECODE_NUM' => $msgType,
					'REMOTEIP' => $this->input->ip_address(),
					'MSGCONTENT_TYPE' => 'N',
					'DEL_YN' => 'M'
				);
	
				$resultNum = $this->setMessageDataInsert(
					array('msgType' => $msgType),
					'N', //전체 발송 여부
					$msgTargetNum,
					$insData,
					FALSE	//파일 업로드 여부 (TRUE, FALSE)
				);
					
				//GROUPNUM을 얻기위해 가장최근 대화 내용 한건을 조회
				$whSql = "NUM = ".$resultNum;
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$result = $this->db->get()->row_array();
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $resultNum;
			}
		}
		else if ($qData['pageMethod'] == 'new_shop_mall' || $qData['pageMethod'] == 'new_shop_mallq')
		{
			$msgGrpNum = 0;

			$whSql = "TOUSER_NUM = ".$msgTargetNum;
			$whSql .= " AND DEL_YN = 'M'";			
			$whSql .= " AND SHOP_NUM = ".$qData['sNum'];
			$whSql .= " AND MSGTYPECODE_NUM IN (17020, 17140)";
			$this->db->select("*");
			$this->db->from($this->tbl);
			$this->db->where($whSql);
			$this->db->limit(1);
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			if ($result)
			{
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $this->getMaxMsgNumWithGrpNum($msgGrpNum);
			}
			else
			{
				//써커스와의 대화시작을 알리는 대화메시지 하나를 생성(사용자쪽에서는 보여지면 안됨)
				//msgGrpNum 생성을 위해 필요
				$insData = array(
					'USER_NUM' => $qData['uNum'],
					'CONTENT' => 'Circus와의 대화를 시작합니다.',
					'SENDER_TYPE' => $qData['senderType'],
					'TARGET_TYPE' => $qData['targetType'],
					'MSGTYPECODE_NUM' => $msgType,
					'SHOP_NUM' => $qData['sNum'],
					'REMOTEIP' => $this->input->ip_address(),
					'MSGCONTENT_TYPE' => 'N',
					'DEL_YN' => 'M'
				);
					
				$resultNum = $this->setMessageDataInsert(
					array('msgType' => $msgType),
					'N', //전체 발송 여부
					$msgTargetNum,
					$insData,
					FALSE	//파일 업로드 여부 (TRUE, FALSE)
				);
					
				//GROUPNUM을 얻기위해 가장최근 대화 내용 한건을 조회
				$whSql = "NUM = ".$resultNum;
				$this->db->select("*");
				$this->db->from($this->tbl);
				$this->db->where($whSql);
				$result = $this->db->get()->row_array();
				$msgGrpNum = $result['MESSAGE_GROUPNUM'];
				$maxMsgNum = $resultNum;
			}
		}
	
		return array(
			'maxMsgNum' => $maxMsgNum,
			'msgGrpNum' => $msgGrpNum
		);
	}	
	
	/**
	 * @method name : getMessageTargetDataList
	 * 나의 메시지 대상 리스트(App) 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getMessageTargetDataList($qData)
	{
		$userTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$shopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
		$sqlTB = "
			SELECT *
			FROM
			(
				SELECT
					DISTINCT 
					(
						CASE 
							WHEN TARGET_TYPE = 'S'
							THEN
								a.SHOP_NUM
							ELSE 
								a.TOUSER_NUM 
						END
					) AS USER_NUM,
					(
						CASE 
							WHEN TARGET_TYPE = 'S'
							THEN
								(
									SELECT SHOP_NAME FROM SHOP WHERE NUM = a.SHOP_NUM 
								)
							ELSE 
								(
									SELECT AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC
									FROM USER WHERE NUM = a.TOUSER_NUM 
								) 
						END
					) AS USER_NAME,
					(
						CASE 
							WHEN TARGET_TYPE = 'S'
							THEN
								(
									SELECT 
										CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
									FROM PROFILE_FILE
									WHERE TBLCODE_NUM = ".$shopTblCodeNum."
									AND TBL_NUM = a.SHOP_NUM
									AND DEL_YN = 'N' 
									ORDER BY NUM LIMIT 1
								)
							ELSE 
								(
									SELECT 
										CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
									FROM PROFILE_FILE
									WHERE TBLCODE_NUM = ".$userTblCodeNum."
									AND TBL_NUM = a.TOUSER_NUM
									AND DEL_YN = 'N' 
									ORDER BY NUM LIMIT 1
								)
						END
					) AS PROFILE_FILE_INFO,
					TARGET_TYPE				
				FROM MESSAGE a
				WHERE a.USER_NUM = ".$qData['userNum']."
				AND a.DEL_YN = 'N'					
			) tb
			WHERE USER_NUM IS NOT NULL AND USER_NUM <> 2
		";
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('('.$sqlTB.') AS a');
		$totalCount = $this->db->get()->row()->COUNT;		
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->from('('.$sqlTB.') AS a');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
		
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}
	
	/**
	 * @method name : setReadMessage
	 * 메시지 읽음 처리 
	 * 
	 * @param unknown $msgNum
	 * @param int $userNum 본인이 받은 메시지만
	 */
	public function setReadMessage($msgNum, $userNum)
	{
		$this->db->set('READ_YN', 'Y');
		$this->db->set('READ_DATE', date('Y-m-d H:i:s'));
		$this->db->where('NUM IN ('.$msgNum.')');
		$this->db->where('TOUSER_NUM', $userNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('READ_YN', 'N');		
		$this->db->update($this->tbl);
		
		return $this->db->affected_rows();
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