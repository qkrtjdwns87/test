<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Shop_model
 *
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Shop_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_commentTbl = 'COMMON_COMMENT';
	
	protected  $_tblCodeNum = 0;
	
	protected $_encKey = '';
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'SHOP';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	public function getShopDataList($qData, $isDelView = FALSE){
		$emailEnc = $userEmailEnc = '';
		if (isset($qData['shopEmail']))
		{
			$emailEnc = $this->common->sqlEncrypt($qData['shopEmail'], $this->_encKey);
		}
		
		if (isset($qData['userEmail']))
		{
			$userEmailEnc = $this->common->sqlEncrypt($qData['userEmail'], $this->_encKey);			
		}

		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ? " AND ".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : "";
		$whSql .= (!empty($qData['shopState'])) ? " AND SHOPSTATECODE_NUM = ".$qData['shopState'] : "";
		$whSql .= (!empty($qData['shopName'])) ? " AND SHOP_NAME LIKE '%".$qData['shopName']."%'" : "";
		$whSql .= (!empty($qData['shopCode'])) ? " AND SHOP_CODE = '".$qData['shopCode']."'" : "";
		$whSql .= (!empty($qData['shopEmail'])) ? " AND SHOP_EMAIL = '".$emailEnc."'" : "";
		$whSql .= (!empty($qData['shopUserName'])) ? " AND SHOPUSER_NAME LIKE '%".$qData['shopUserName']."%'": "";
		$whSql .= (!empty($qData['sellerType'])) ? " AND SELLERTYPECODE_NUM = ".$qData['sellerType'] : "";
		if (!empty($qData['userEmail']))
		{
			$whSql .= " 
				AND USER_NUM IN (
					SELECT NUM FROM USER WHERE USER_EMAIL = '".$userEmailEnc."' 
			)";
		}
		
		if (isset($qData['pageMethod']))
		{
			if ($qData['pageMethod'] == 'apprlist')
			{
				//승인전 단계			
				$whSql .= " AND SHOPSTATECODE_NUM < 3060";			
				$whSql .= (!empty($qData['sDate']) && !empty($qData['eDate'])) ? " AND APPROVAL_FIRSTREQ_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' " : "";
				
				//승인이후 단계
				//$whSql .= " AND SHOPSTATECODE_NUM > 3050";
				//$whSql .= (!empty($qData['sDate']) && !empty($qData['eDate'])) ? " AND CREATE_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' " : "";
			}
		}

		if (!empty($qData['authortype']))
		{
			$whSql .= ($qData['authortype'] == 'totay') ? " AND TODAYAUTHOR_YN = 'Y'" : " AND POPAUTHOR_YN = 'Y'";
		}
		
		if (!empty($qData['sNum']))
		{
			$whSql .= " AND NUM = ".$qData['sNum'];
		}	
		
		$isTotalSearch = FALSE;
		if (isset($qData['isTotalSearch'])) //OR 전체 검색
		{
			if ($qData['isTotalSearch'])
			{
				$isTotalSearch = $qData['isTotalSearch'];
				$whSql = str_replace('AND', 'OR', $whSql);
				$whSql = str_replace("OR DEL_YN = 'N'", "AND DEL_YN = 'N'", $whSql).')';
				$whSql = str_replace("OR SHOP_NAME", "AND (SHOP_NAME", $whSql);
			}
		}		
		
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND SHOPSTATECODE_NUM IN (3060)" : "";
		}		
		
		$addSelect = " 0 AS SHOP_FLAG ";
		if (isset($qData['userNum']))
		{
			$addSelect = ($qData['userNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = ".$this->tbl.".NUM
					AND USER_NUM = ".$qData['userNum']."
					AND DEL_YN = 'N'
				) AS SHOP_FLAG
			" : $addSelect;
		}		
		
		//data 총 갯수 select		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			".$addSelect.",
			AES_DECRYPT(UNHEX(SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
			AES_DECRYPT(UNHEX(SHOP_TEL), '".$this->_encKey."') AS SHOP_TEL_DEC,
			AES_DECRYPT(UNHEX(SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".SHOPSTATECODE_NUM) AS SHOPSTATECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".SELLERTYPECODE_NUM) AS SELLERTYPECODE_TITLE,
			(SELECT USER_NAME FROM USER WHERE NUM = ".$this->tbl.".MANAGEUSER_NUM) AS MANAGERUSER_NAME,
			(
				SELECT 
					CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
				AND TBL_NUM = ".$this->tbl.".NUM
				AND DEL_YN = 'N' 
				ORDER BY NUM LIMIT 1
			) AS PROFILE_FILE_INFO				
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
	
	/**
	 * @method name : getShopHistoryDataList
	 * 샵관련 히스토리 목록 
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getShopHistoryDataList($qData, $isDelView)
	{
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND ".$this->tbl."_HISTORY.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['sNum'])) ? " AND SHOP_NUM = ".$qData['sNum'] : '';
		if (isset($qData['shopState']))
		{
			if ($qData['shopState'] == 'lowerApproval')
			{
				//승인전 단계
				$whSql .= " AND SHOPSTATECODE_NUM < 3060";
			}
			else if ($qData['shopState'] == 'upperApproval')
			{
				//승인이후 단계
				$whSql .= " AND SHOPSTATECODE_NUM > 3050";
			}
		}
		$whSql .= ($qData['shopStateCodeNum'] > 0) ? " SHOPSTATECODE_NUM = ".$qData['shopStateCodeNum'] : '';		
				
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.'_HISTORY');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
  			".$this->tbl."_HISTORY.*,
			(SELECT SHOP_NAME FROM ".$this->tbl." WHERE NUM = ".$this->tbl."_HISTORY.SHOP_NUM) AS SHOP_NAME,
			(
				SELECT USER_NAME 
				FROM USER
				WHERE NUM IN (
					SELECT MANAGEUSER_NUM FROM ".$this->tbl." 
					WHERE NUM = ".$this->tbl."_HISTORY.SHOP_NUM
				)
			) AS MANAGERUSER_NAME,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl."_HISTORY.SHOPSTATECODE_NUM) AS SHOPSTATECODE_TITLE,
			AES_DECRYPT(UNHEX(USER.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(USER.USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
			AES_DECRYPT(UNHEX(USER.USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
			USER.USER_NAME,
			USER.USER_NICK
		");
		$this->db->from($this->tbl.'_HISTORY');
		$this->db->join('USER', $this->tbl.'_HISTORY.ADMINUSER_NUM = USER.NUM', 'left outer');		
		$this->db->where($whSql);
		$this->db->order_by($this->tbl.'_HISTORY.NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
		
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;

		return $result;		
	}
	
	/**
	 * @method name : getShopRowData
	 * 한번에 모든 data 불러오기
	 * SHOP_POLICY_AREA 제외, FILE은 1개만
	 * SHOP_HISTORY는 가장최근것 1개만 
	 * 
	 * @param unknown $sNum
	 * @param uNum 회원고유번호
	 * @param unknown $isDelView
	 */
	public function getShopRowData($sNum, $uNum = 0, $isDelView = FALSE)
	{
		$addSelect = " 0 AS SHOP_FLAG ";
		if (isset($uNum))
		{
			$addSelect = ($uNum > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = a.NUM
					AND USER_NUM = ".$uNum."
					AND DEL_YN = 'N'
				) AS SHOP_FLAG
			" : $addSelect;
		}
		
		$this->db->select("
			*,
			".$addSelect.",
			AES_DECRYPT(UNHEX(SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
			AES_DECRYPT(UNHEX(SHOP_TEL), '".$this->_encKey."') AS SHOP_TEL_DEC,
			AES_DECRYPT(UNHEX(SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".SHOPSTATECODE_NUM) AS SHOPSTATECODE_TITLE,
			AES_DECRYPT(UNHEX(CO_ZIP), '".$this->_encKey."') AS CO_ZIP_DEC,
			AES_DECRYPT(UNHEX(CO_ADDR1), '".$this->_encKey."') AS CO_ADDR1_DEC,
			AES_DECRYPT(UNHEX(CO_ADDR2), '".$this->_encKey."') AS CO_ADDR2_DEC,
			AES_DECRYPT(UNHEX(CO_ADDR_JIBUN), '".$this->_encKey."') AS CO_ADDR_JIBUN_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
				AND TBL_NUM = ".$this->tbl.".NUM 
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS PROFILE_FILE_INFO				
		");
		$this->db->limit(1);
		$this->db->from($this->tbl);
		$this->db->join($this->tbl.'_POLICY', $this->tbl.'.NUM = '.$this->tbl.'_POLICY.SHOP_NUM', 'left outer');
		$this->db->join($this->tbl.'_INFORM', $this->tbl.'.NUM = '.$this->tbl.'_INFORM.SHOP_NUM', 'left outer');		
		$this->db->join($this->tbl.'_HISTORY', $this->tbl.'.NUM = '.$this->tbl.'_HISTORY.SHOP_NUM', 'left outer');		
		$this->db->join($this->_fileTbl, $this->_fileTbl.'.TBLCODE_NUM = '.$this->_tblCodeNum.' AND '.$this->tbl.'.NUM = '.$this->_fileTbl.'.TBL_NUM', 'left outer');		
		$this->db->where($this->tbl.".SHOP_NUM = ".$sNum);
		if (!$isDelView) $this->db->where($this->tbl.".DEL_YN = 'N'");
		$this->db->order_by($this->tbl.'_HISTORY.NUM', 'DESC');				
		$this->db->order_by('FILE_ORDER', 'ASC');
		
		return $this->db->get()->row_array();
	}
	
	/**
	 * @method name : getShopBaseRowData
	 * 샵 기본정보 data
	 * 
	 */
	public function getShopBaseRowData($sNum, $uNum = 0)
	{
		$addSelect = " 0 AS SHOP_FLAG ";
		if (isset($uNum))
		{
			$addSelect = ($uNum > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = a.NUM
					AND USER_NUM = ".$uNum."
					AND DEL_YN = 'N'
				) AS SHOP_FLAG
			" : $addSelect;
		}
		
		$sql = "
			SELECT
				a.*,
				".$addSelect.",
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = a.NUM 
					AND DEL_YN = 'N'
					AND FILE_ORDER = 0
					ORDER BY NUM LIMIT 1
				) AS PROFILE_FILE_INFO,					
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = a.NUM 
					AND DEL_YN = 'N'
					AND FILE_ORDER = 2
					ORDER BY NUM LIMIT 1
				) AS MAIN_FILE_INFO,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = a.NUM 
					AND DEL_YN = 'N'
					AND FILE_ORDER = 3
					ORDER BY NUM LIMIT 1
				) AS MAIN_M_FILE_INFO,							
				(SELECT TITLE FROM CODE WHERE NUM = a.SHOPSTATECODE_NUM) AS SHOPSTATECODE_TITLE,
				(SELECT TITLE FROM CODE WHERE NUM = a.SELLERTYPECODE_NUM) AS SELLERTYPECODE_TITLE,
				AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
				AES_DECRYPT(UNHEX(b.USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
				AES_DECRYPT(UNHEX(b.USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
				AES_DECRYPT(UNHEX(SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
				AES_DECRYPT(UNHEX(SHOP_TEL), '".$this->_encKey."') AS SHOP_TEL_DEC,
				AES_DECRYPT(UNHEX(SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
				c.NUM AS MANAGERUSER_NUM,
				c.USER_NAME AS MANAGERUSER_NAME,
				AES_DECRYPT(UNHEX(c.USER_EMAIL), '".$this->_encKey."') AS MANAGER_EMAIL_DEC,
				AES_DECRYPT(UNHEX(c.USER_TEL), '".$this->_encKey."') AS MANAGER_TEL_DEC,
				AES_DECRYPT(UNHEX(c.USER_MOBILE), '".$this->_encKey."') AS MANAGER_MOBILE_DEC,
				d.NUM, 
				d.USER_NAME AS APPROVALUSER_NAME
			FROM ".$this->tbl." a INNER JOIN USER b
			ON a.USER_NUM = b.NUM LEFT OUTER JOIN USER c
			ON a.MANAGEUSER_NUM = c.NUM LEFT OUTER JOIN USER d
			ON a.APPROVALUSER_NUM = d.NUM
			WHERE a.NUM = ".$sNum."
			LIMIT 1
		";
		
		return $this->db->query($sql)->row_array();		
	}
	
	/**
	 * @method name : getShopPolicyRowData
	 * 샵 정책 
	 * 
	 * @param int $sNum
	 */
	public function getShopPolicyRowData($sNum)
	{
		$this->db->select("
			*,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl."_POLICY.REFPOLICYCODE_NUM) AS REFPOLICYCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl."_POLICY.CALCYCLECODE_NUM) AS CALCYCLECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl."_POLICY.CALBANKCODE_NUM) AS CALBANKCODE_TITLE,				
			AES_DECRYPT(UNHEX(REFUND_TEL), '".$this->_encKey."') AS REFUND_TEL_DEC,
			AES_DECRYPT(UNHEX(REFUND_ZIP), '".$this->_encKey."') AS REFUND_ZIP_DEC,
			AES_DECRYPT(UNHEX(REFUND_ADDR1), '".$this->_encKey."') AS REFUND_ADDR1_DEC,
			AES_DECRYPT(UNHEX(REFUND_ADDR2), '".$this->_encKey."') AS REFUND_ADDR2_DEC,
			AES_DECRYPT(UNHEX(REFUND_ADDR_JIBUN), '".$this->_encKey."') AS REFUND_ADDR_JIBUN_DEC
		");
		$this->db->limit(1);
		$this->db->from($this->tbl.'_POLICY');
		$this->db->where("SHOP_NUM = ".$sNum);
		
		return $this->db->get()->row_array();
	}

	/**
	 * @method name : getShopPolicyAreaDataList
	 * 샵정책 중 지역정책 data 리스트
	 *
	 */
	public function getShopPolicyAreaDataList($sNum)
	{
		$this->db->select("
			".$this->tbl."_POLICY_AREA.*,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl."_POLICY_AREA.AREACODE_NUM) AS AREA_TITLE
		");
		$this->db->from($this->tbl.'_POLICY_AREA');
		$this->db->where($this->tbl.'_POLICY_AREA.SHOP_NUM = '.$sNum);
	
		return $this->db->get()->result_array();
	}
	
	/**
	 * @method name : getStandardShopPolicyRowData
	 * 기준샵에서 정책 조회
	 * USERLEVEL - STDSHOP 인 회원이 생성한 SHOP
	 * STDSHOP 권한을 가지고 생성한 SHOP은 꼭 한개여야만 함
	 *
	 */
	public function getStandardShopPolicyRowData()
	{
		return $this->db->query("
			SELECT 
				a.USER_NUM,
				b.*,
				(SELECT TITLE FROM CODE WHERE NUM = b.REFPOLICYCODE_NUM) AS REFPOLICYCODE_TITLE
			FROM ".$this->tbl." a INNER JOIN ".$this->tbl."_POLICY b
			ON a.NUM = b.SHOP_NUM
			AND a.USER_NUM IN 
					(
						SELECT NUM FROM USER 
						WHERE ULEVELCODE_NUM = ".$this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'STDSHOP')."
		) LIMIT 1")->row_array();
	}	
	
	/**
	 * @method name : getShopInformRowData
	 * 샵 부가정보(사업자정보...) 
	 * 
	 * @param unknown $sNum
	 */
	public function getShopInformRowData($sNum)
	{
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(CO_TEL), '".$this->_encKey."') AS CO_TEL_DEC,
			AES_DECRYPT(UNHEX(CO_CEOEMAIL), '".$this->_encKey."') AS CO_CEOEMAIL_DEC,
			AES_DECRYPT(UNHEX(CO_ZIP), '".$this->_encKey."') AS CO_ZIP_DEC,
			AES_DECRYPT(UNHEX(CO_ADDR1), '".$this->_encKey."') AS CO_ADDR1_DEC,
			AES_DECRYPT(UNHEX(CO_ADDR2), '".$this->_encKey."') AS CO_ADDR2_DEC,
			AES_DECRYPT(UNHEX(CO_ADDR_JIBUN), '".$this->_encKey."') AS CO_ADDR_JIBUN_DEC
		");
		$this->db->limit(1);
		$this->db->from($this->tbl.'_INFORM');
		$this->db->where("SHOP_NUM = ".$sNum);
		
		return $this->db->get()->row_array();
	}
	
	/**
	 * @method name : getShopStatsRowData
	 * 아이템과 관련된 Craft Shop 통계
	 *
	 * @param unknown $sNum
	 */
	public function getShopStatsRowData($sNum)
	{
		$this->db->select('*');
		$this->db->from('STATS_SHOP');
		$this->db->where("SHOP_NUM = ".$sNum);
		$this->db->limit(1);
	
		return $this->db->get()->row_array();
	}	
	
	/**
	 * @method name : getShopfileDataList
	 * 샵과 관련된 파일첨부 data
	 * 
	 * @param unknown $sNum
	 */
	public function getShopfileDataList($sNum)
	{
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
		$this->db->where("TBL_NUM = ".$sNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		
		return $this->db->get()->result_array();
	}
	
	/**
	 * @method name : getShopProfileFileDataList
	 * 샵 프로필 파일 첨부 data List
	 *
	 * @param unknown $siNum
	 */
	public function getShopProfileFileDataList($sNum)
	{
		$this->db->select('*');
		$this->db->from('PROFILE_FILE');
		$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
		$this->db->where("TBL_NUM = ".$sNum);
		$this->db->where("DEL_YN = 'N'");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');
	
		return $this->db->get()->result_array();
	}	

	/**
	 * @method name : setShopDataInsert
	 * 신규 샵 등록 
	 * 
	 * @param unknown $insData
	 * @param unknown $insInfoData
	 * @param unknown $insPlData
	 * @param number $userNum 신규샵 생성시 생성된 회원고유번호
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setShopDataInsert($insData, $insInfoData, $insPlData, $userNum = 0, $isUpload = FALSE){
		$resultNum = 0;
		$now = DateTime::createFromFormat('U.u', microtime(true));
		$tmpCode = $now->format("ymdHisu");	//$now->format("m-d-Y H:i:s.u");
		$tmpCode = substr($tmpCode, 0, -2);
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert(
			$this->tbl, 
			$insData  + array(
				'USER_NUM' => $userNum,
				'SHOP_CODE' => 'S'.$tmpCode	//str_pad($userNum, 7, '0', STR_PAD_LEFT)	//임시부여  	
			)
		);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		
		//사업자정보
		$this->db->insert($this->tbl.'_INFORM', $insInfoData  + array('SHOP_NUM' => $resultNum));
		
		//정책 정보
		$this->db->insert($this->tbl.'_POLICY', $insPlData  + array('SHOP_NUM' => $resultNum));
		
		//히스토리 처리
		//최초 Shop 저장시 샵등록 담당자는 dummy유저의 고유번호를 임시로 부여한다
		$this->db->insert(
			$this->tbl.'_HISTORY',
			array(
				'SHOP_NUM' => $resultNum,
				'SHOPSTATECODE_NUM' => $insData['SHOPSTATECODE_NUM'],
				'ADMINUSER_NUM' => $this->common->getSession('user_num')						
			)
		);
		$hisNum = $this->db->insert_id();
		
		//마지막 히스토리 번호, 실사용 shopcode update
		$this->db->set('LASTHISTORY_NUM', $hisNum);
		$this->db->set('SHOP_CODE', 'SH'.$now->format("ymd").str_pad($resultNum, 5, '0', STR_PAD_LEFT));
		$this->db->where('NUM', $resultNum);
		$this->db->update($this->tbl);		
		
		//통계용 기본 데이터 insert
		$this->db->insert('STATS_SHOP', array('SHOP_NUM' => $resultNum));
		
		if ($isUpload)
		{
			//추가할 FILE 컬럼을 config에 같이 추가
			$upConfig = array_merge(
				$this->getUploadOption('/profile/'.strtolower($this->tbl).'/'.$resultNum.'/'),
				array(
					'TBLCODE_NUM' => $this->_tblCodeNum,
					'TBL_NUM' => $resultNum,
					'IS_FILEUSE' => TRUE //파일 사용처 구분 여부(W:웹, M:모바일 구분)
				)
			);
		
			$uploadResult = $this->common->fileUpload($upConfig, TRUE);
		
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
					$this->db->insert('PROFILE_FILE', $uploadResult[$i]);
				}
			}
		}		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $resultNum;
	}
	
	/**
	 * @method name : setShopDataUpdate
	 * 샵정보 수정 
	 * 
	 * @param unknown $sNum
	 * @param unknown $upData
	 * @param unknown $upInfoData
	 * @param unknown $upPlData
	 * @param unknown $insHisData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setShopDataUpdate($sNum, $upData, $upInfoData, $upPlData, $insHisData, $isUpload = FALSE)
	{
		if ($sNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			if (!empty($upData['USER_PASS'])) //비밀번호 update
			{
				$sql = "
					UPDATE USER
						SET
							USER_PASS = '".$upData['USER_PASS']."'
					WHERE NUM IN (
						SELECT USER_NUM FROM ".$this->tbl." WHERE NUM = ".$sNum."
					)
				";
				$this->db->query($sql);
			}
			unset($upData['USER_PASS']); //사용하지 않으므로 삭제
			
			$this->db->where('NUM', $sNum);
			$this->db->update($this->tbl, $upData);		
			
			//사업자 정보
			$this->db->where('SHOP_NUM', $sNum);
			$this->db->update($this->tbl.'_INFORM', $upInfoData);			
			
			if (count($upPlData) > 0)
			{
				//정책관련
				$this->db->where('SHOP_NUM', $sNum);
				$this->db->update($this->tbl.'_POLICY', $upPlData);
			}
			
			//히스토리 처리
			$this->db->insert($this->tbl.'_HISTORY', $insHisData);
			$hisNum = $this->db->insert_id();
			
			//마지막 히스토리 번호 update
			$this->db->set('LASTHISTORY_NUM', $hisNum);
			$this->db->where('NUM', $sNum);
			$this->db->update($this->tbl);
			
			if ($isUpload)
			{
				//추가할 FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/profile/'.strtolower($this->tbl).'/'.$sNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $sNum,
						'IS_FILEUSE' => TRUE //파일 사용처 구분 여부(W:웹, M:모바일 구분)
					)
				);
					
				$uploadResult = $this->common->fileUpload($upConfig, TRUE, 1);
					
				if (array_key_exists('error', $uploadResult))
				{
					$errMsg = str_replace('<p>', '', $uploadResult['error']);
					$errMsg = str_replace('</p>', '', $errMsg);
					$this->common->message($errMsg, '-', '');
				}
				else
				{
					for($i=0; $i<count($uploadResult); $i++)
					{
						//if ($i ==0 || $i > 1) //temp로 올린 파일은 제외 (모바일용 프로필 이미지가 필요한 경우 활용)
						//{
							//비교를 위해 기존 업로드된 내용을 확인한다
							$this->db->select('*');
							$this->db->limit(1);
							$this->db->from('PROFILE_FILE');
							$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
							$this->db->where('TBL_NUM', $sNum);
							$this->db->where('FILE_ORDER', $i);
							$this->db->where('DEL_YN', 'N');
							$oldFile = $this->db->get()->row_array();
								
							if (count($oldFile) > 0)
							{
								if ($this->common->nullCheck($uploadResult[$i]['FILE_NAME'], 'str', '') != '')
								{
									if ($oldFile['FILE_NAME'] != $uploadResult[$i]['FILE_NAME'] || $oldFile['FILE_SIZE'] != $uploadResult[$i]['FILE_SIZE'])
									{
										//파일명 또는 파일사이즈가 다른 경우 삭제 플래그 만 변경
										$upData = array('DEL_YN' => 'Y');	//배열로 업데이트
										$this->db->where('NUM', $oldFile['NUM']);
										$this->db->update('PROFILE_FILE', $upData);
										//update after insert
										$this->db->insert('PROFILE_FILE', $uploadResult[$i]);
									}
								}
							}
							else
							{
								$this->db->insert('PROFILE_FILE', $uploadResult[$i]);
							}							
						//}
					}
				}
			}			
			
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $sNum;
	}
	
	/**
	 * @method name : setShopDataReUpdate
	 * 신규샵 입력후 입력정보 확인단계에서 재수정
	 * 히스토리 기록(X) 
	 * 
	 * @param unknown $sNum
	 * @param unknown $upData
	 * @param unknown $upInfoData
	 */
	public function setShopDataReUpdate($sNum, $upData, $upInfoData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->where('NUM', $sNum);
		$this->db->update($this->tbl, $upData);
			
		//사업자 정보
		$this->db->where('SHOP_NUM', $sNum);
		$this->db->update($this->tbl.'_INFORM', $upInfoData);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
	}
	
	/**
	 * @method name : setApprovalRequest
	 * 신규입력후 확인페이지에서 승인요청 
	 * 
	 * @param unknown $insHisData
	 * @return Ambiguous
	 */
	public function setApprovalRequest($insHisData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert($this->tbl.'_HISTORY', $insHisData);
		$hisNum = $this->db->insert_id();
			
		//마지막 히스토리 번호 및 상태정보 update
		$this->db->set('SHOPSTATECODE_NUM', $insHisData['SHOPSTATECODE_NUM']);		
		$this->db->set('APPROVAL_FIRSTREQ_DATE', date('Y-m-d H:i:s'));		
		$this->db->set('APPROVAL_REQ_DATE', date('Y-m-d H:i:s'));		
		$this->db->set('LASTHISTORY_NUM', $hisNum);
		$this->db->where('NUM', $insHisData['SHOP_NUM']);
		$this->db->update($this->tbl);	
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $hisNum;
	}
	
	/**
	 * @method name : setProfileFileDelete
	 * 개별 파일 삭제
	 * 웹, 모바일 한쌍중 나머지가 삭제되었는지 확인
	 * 다른 나머지도 삭제되었다면 한쌍이(W,M) 모두 삭제된셈이므로
	 * 한쌍을 제외한 나머지 FILE_ORDER를 -1 해준다(상위 ORDER의 파일만)
	 * 
	 * @param int $siNum	SHOPITEM 고유번호
	 * @param int $fNum
	 * @param int $fIndex
	 */
	public function setProfileFileDelete($sNum, $fNum, $fIndex)
	{
		$otherIndex = (($fIndex % 2) == 0) ? ($fIndex + 1) : ($fIndex - 1);
		
		$this->db->select('*');
		$this->db->from('PROFILE_FILE');
		$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
		$this->db->where('TBL_NUM', $sNum);
		$this->db->where('FILE_ORDER', $otherIndex);
		$this->db->where('DEL_YN', 'N');
		$result = $this->db->get()->row_array();
		
		if ($result)
		{
			if ($result['DEL_YN'] == 'Y' || $result['FILE_NAME'] == '')
			{
				//쌍으로 삭제
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
				$this->db->where('TBL_NUM', $sNum);
				$this->db->where('FILE_ORDER IN ('.$fIndex.', '.$otherIndex.')');
				$this->db->update('PROFILE_FILE');
					
				//FILE_ORDER 조정
				$this->db->set('FILE_ORDER', 'FILE_ORDER - 2', FALSE);
				$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
				$this->db->where('TBL_NUM', $sNum);
				$this->db->where('DEL_YN', 'N');
				$this->db->where('FILE_ORDER > '.$fIndex);
				$this->db->update('PROFILE_FILE');
			}
			else
			{
				//삭제이나 삭제하지 않고 빈데이터만 구성
				$upData = array(
						'FILE_NAME' => '',
						'FILE_TEMPNAME' => '',
						'FILE_TYPE' => '',
						'FILE_SIZE' => 0,
						'IMAGE_YN' => 'N',
						'THUMB_YN' => 'N',
						'IMAGE_WIDTH' => 0,
						'IMAGE_HEIGHT' => 0,
						'FILE_USE' => 'W'
				);
					
				$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
				$this->db->where('TBL_NUM', $sNum);
				$this->db->where('NUM', $fNum);
				$this->db->update('PROFILE_FILE', $upData);
			}
		}		
	}	
	
	/**
	 * @method name : getShopBestItemDataList
	 * 샵 대표 Item 내용
	 *
	 * @param unknown $sNum
	 * @param unknown $isDelView
	 */
	public function getShopBestItemDataList($sNum, $isDelView = FALSE)
	{
		$result = array();
		if ($sNum > 0)
		{
			$whSql = "a.SHOP_NUM = ".$sNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			$this->db->select("
				a.*,
				b.SHOP_NAME,
				b.SHOP_CODE,
				b.SHOPUSER_NAME,
				AES_DECRYPT(UNHEX(b.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
				c.ITEM_NAME,
				c.ITEM_CODE,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
					FROM SHOPITEM_FILE
					WHERE SHOPITEM_NUM = c.NUM
					AND DEL_YN = 'N'
					AND FILE_USE = 'W'
					ORDER BY NUM LIMIT 1
				) AS FILE_INFO,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
					FROM SHOPITEM_FILE
					WHERE SHOPITEM_NUM = c.NUM
					AND DEL_YN = 'N'
					AND FILE_USE = 'M'
					ORDER BY NUM LIMIT 1
				) AS M_FILE_INFO					
			");
			$this->db->from('SHOP_BESTITEM AS a');
			$this->db->join('SHOP AS b', 'a.SHOP_NUM = b.NUM');
			$this->db->join('SHOPITEM AS c', 'a.SHOPITEM_NUM = c.NUM', 'left outer');			
			$this->db->where($whSql);
			$this->db->order_by('a.BESTITEM_ORDER', 'ASC');
			$result['recordSet'] = $this->db->get()->result_array();
		}
		
		return $result;
	}
	
	/**
	 * @method name : setShopBestItemDataInsert
	 * 샵 대표 Item 구성
	 *
	 * @param unknown $sNum
	 * @param unknown $insData
	 * @return Ambiguous
	 */
	public function setShopBestItemDataInsert($sNum, $insData)
	{
		$this->db->select('NUM');
		$this->db->from('SHOP_BESTITEM');
		$this->db->where('SHOP_NUM', $sNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->order_by('NUM', 'ASC');
		$result = $this->db->get()->row_array();
		$sbiNum = ($result) ? $result['NUM'] : 0;
	
		$resultNum = 0;
		if ($sbiNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setShopBestItemDataUpdate($sNum, $insData);
		}
		else
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			usort($insData, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($insData); $i++)
			{
				$this->db->insert(
					'SHOP_BESTITEM',
					array(
						'SHOP_NUM' => $sNum,
						'SHOPITEM_NUM' => (!empty($insData[$i]['itemno'])) ? $insData[$i]['itemno'] : NULL,
						'BESTITEM_ORDER' => $insData[$i]['order']
					)
				);
				$resultNum = $this->db->insert_id();
				
				if (!empty($insData[$i]['itemno']))
				{
					//해당 아이템을 대표 아이템으로 update
					$this->db->where('NUM', $insData[$i]['itemno']);
					$this->db->update('SHOPITEM', array('REPRESENT_YN' => 'Y'));
				}
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $resultNum;
	}
	
	/**
	 * @method name : setShopBestItemDataUpdate
	 * 샵 대표 Item update
	 *
	 * @param unknown $sNum
	 * @param unknown $upData
	 * @return Ambiguous
	 */
	public function setShopBestItemDataUpdate($sNum, $upData)
	{
		if ($sNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			usort($upData, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($upData); $i++)
			{
				$this->db->select("
					EXISTS (
						SELECT 1 FROM SHOP_BESTITEM
						WHERE NUM = ".$upData[$i]['num']."
					) AS RESULT
				");
				$isExist = $this->db->get()->row()->RESULT;
				if (!$isExist) //없는경우 data 생성
				{
					$this->db->insert(
						'SHOP_BESTITEM',
						array(
							'SHOP_NUM' => $sNum,
							'SHOPITEM_NUM' => (!empty($upData[$i]['itemno'])) ? $upData[$i]['itemno'] : NULL,
							'BESTITEM_ORDER' => $upData[$i]['order']
						)
					);
				}
				else
				{
					$this->db->where('NUM', $upData[$i]['num']);
					$this->db->update(
						'SHOP_BESTITEM',
						array(
							'SHOPITEM_NUM' => (!empty($upData[$i]['itemno'])) ? $upData[$i]['itemno'] : NULL,
							'BESTITEM_ORDER' => $upData[$i]['order']
						)
					);
				}
				
				if (!empty($upData[$i]['itemno']))
				{
					//해당 아이템을 대표 아이템으로 update
					$this->db->where('NUM', $upData[$i]['itemno']);
					$this->db->update('SHOPITEM', array('REPRESENT_YN' => 'Y'));
				}				
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $sNum;
	}
	
	/**
	 * @method name : setShopBestItemContentDelete
	 * 샵 대표 Item 컨텐츠 삭제
	 *
	 * @param unknown $sNum
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @param unknown $itemNum
	 * @return number
	 */
	public function setShopBestItemContentDelete($sNum, $contentNum, $contentOrder, $itemNum)
	{
		$result = 0;
		if ($sNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$this->db->set('SHOPITEM_NUM', NULL);
			$this->db->where('NUM', $contentNum);
			$this->db->where('SHOP_NUM', $sNum);
			$this->db->update('SHOP_BESTITEM');
			$result = $this->db->affected_rows();
			
			//해당 아이템을 대표 아이템에서 삭제 update
			$this->db->where('NUM', $itemNum);
			$this->db->where('SHOP_NUM', $sNum);
			$this->db->update('SHOPITEM', array('REPRESENT_YN' => 'N'));

			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $result;
	}	

	/**
	 * @method name : getUploadOption
	 * 업로드 환경 설정
	 *
	 * @param string $subdir 업로드될 경로
	 * @return string[]|number[]|boolean[]
	 */
	private function getUploadOption($subdir = ''){
		$config = array();
		$baseUploadPath = '.'.$this->config->item('base_uploadPath');
		if (!empty($subdir))
		{
			$uploadPath = $baseUploadPath.$subdir;
		}
		$config['upload_path'] = $uploadPath;
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size'] = (1024 * 10);	// 10메가 (단위 KB)
		//$config['max_width'] = '1024';
		//$config['max_height'] = '768';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$config['remove_spaces'] = TRUE;
		$config['create_thumbnail'] = TRUE;
	
		return $config;
	}	

	/**
	 * @method name : setShopBestItemContentDelete
	 * @author gilbert
	 * 샵 대표 Item 컨텐츠 삭제
	 *
	 * @param unknown $sino
	 * @return table
	 */
	public function getShopMasterInfoBySiNo_sp($sino)
	{
		$query = $this->db->query("call getShopMasterInfoBySiNo('{$sino}');");
		
		mysqli_next_result( $this->db->conn_id );

		return $query->row_array();
	}

}
?>