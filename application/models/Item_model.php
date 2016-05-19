<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Item_model
 *
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Item_model extends CI_Model{
	
	protected $_fileTbl = 'SHOPITEM_FILE';
	
	protected $_cateTbl = 'SHOPITEM_CATE';
	
	protected $_tagTbl = 'COMMON_TAG';	
	
	/**
	 * @var string 기획전(이벤트) 테이블명
	 */
	protected $_enTbl = 'EVENT';
	
	/**
	 * @var string 기획전에 속한 아이템 테이블명
	 */
	protected $_enitTbl = 'EVENT_SHOPITEM';
	
	protected $_tblCodeNum = 0;
	
	protected $_encKey = '';	
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'SHOPITEM';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 주로 사용될 TABLE CODE.NUM
		$this->tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	/**
	 * @method name : getItemCommonCateDataList
	 * 아이템등록을 위한 공통 카테고리 
	 * craft shop과 circus 카테고리 모두 포함 (all)
	 * 
	 * @param unknown $qData
	 */
	public function getItemCommonCateDataList($qData)
	{
		$result = array();
		$cc = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'NONE'); //써커스 생성 카테고리 관여 table Num
		$cs = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP'); //샵 생성 카테고리 관여 table Num
	
		if ($qData['searchKey'] == 'ALL')
		{
			//써커스 생성 카테고리와 해당 샵생성 카테고리 모두
			$this->db->select("*");
			if (!$qData['isDelView']) $this->db->where("DEL_YN = 'N'");
			if (!$qData['isUseNoView']) $this->db->where("USE_YN = 'Y'");			
			if (isset($qData['shopNum']))
			{
				$this->db->where('TBLCODE_NUM = '.$cs.' AND TBL_NUM = '.$qData['shopNum']);
				$this->db->or_where('TBLCODE_NUM = '.$cc);				
			}
			else
			{
				$this->db->where('TBLCODE_NUM = '.$cc);				
			}
			$this->db->from('COMMON_CATE');
			$this->db->order_by('CATE_TYPE', 'ASC');
			$this->db->order_by('CATE_ORDER', 'ASC');
			$result = $this->db->get()->result_array();
		}
		else if($qData['searchKey'] == 'MALL')
		{
			//써커스에서 생성한 아이템 카테고리만
			$this->db->select("
				COMMON_CATE.*,
				(
					SELECT COUNT(*) FROM ".$this->tbl." 
					WHERE DEL_YN = 'N'
					AND ITEMSTATECODE_NUM >= 8060
					AND NUM IN (
						SELECT SHOPITEM_NUM FROM SHOPITEM_CATE WHERE CATE_NUM = COMMON_CATE.NUM
					)
				) AS TOTITEM_COUNT,
				".$this->tbl.".ITEM_NAME,
				".$this->tbl.".ITEM_CODE,
				".$this->tbl.".ITEMSHOP_CODE,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM ".$this->tbl."_FILE
					WHERE SHOPITEM_NUM = COMMON_CATE.REPRESENT_SHOPITEM_NUM 
					AND DEL_YN = 'N' 
					AND FILE_USE = 'W'
					ORDER BY NUM LIMIT 1
				) AS FILE_INFO,					
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM ".$this->tbl."_FILE
					WHERE SHOPITEM_NUM = COMMON_CATE.REPRESENT_SHOPITEM_NUM 
					AND DEL_YN = 'N' 
					AND FILE_USE = 'M'
					ORDER BY NUM LIMIT 1
				) AS M_FILE_INFO					
			");
			if (!$qData['isDelView']) $this->db->where("COMMON_CATE.DEL_YN = 'N'");
			if (!$qData['isUseNoView']) $this->db->where("USE_YN = 'Y'");
			$this->db->where('TBLCODE_NUM = '.$cc);
			$this->db->from('COMMON_CATE');
			$this->db->join($this->tbl, 'COMMON_CATE.REPRESENT_SHOPITEM_NUM = '.$this->tbl.'.NUM', 'left outer');
			$this->db->order_by('CATE_ORDER', 'ASC');
			$result = $this->db->get()->result_array();		
		}
		else if($qData['searchKey'] == 'SHOP')
		{
			//샵에서 생성한 카테고리만
			$this->db->select("
				COMMON_CATE.*,
				(
					SELECT COUNT(*) FROM ".$this->tbl." 
					WHERE SHOP_NUM = ".$qData['shopNum']."
					AND DEL_YN = 'N'
					AND ITEMSTATECODE_NUM >= 8060
				) AS TOTITEM_COUNT,
				".$this->tbl.".ITEM_NAME,
				".$this->tbl.".ITEM_CODE,
				".$this->tbl.".ITEMSHOP_CODE,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM ".$this->tbl."_FILE
					WHERE SHOPITEM_NUM = COMMON_CATE.REPRESENT_SHOPITEM_NUM 
					AND DEL_YN = 'N' 
					AND FILE_USE = 'W'
					ORDER BY NUM LIMIT 1
				) AS FILE_INFO,					
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM ".$this->tbl."_FILE
					WHERE SHOPITEM_NUM = COMMON_CATE.REPRESENT_SHOPITEM_NUM 
					AND DEL_YN = 'N' 
					AND FILE_USE = 'M'
					ORDER BY NUM LIMIT 1
				) AS M_FILE_INFO					
			");
			if (!$qData['isDelView']) $this->db->where("COMMON_CATE.DEL_YN = 'N'");
			if (!$qData['isUseNoView']) $this->db->where("COMMON_CATE.USE_YN = 'Y'");			
			$this->db->where('TBLCODE_NUM = '.$cs.' AND TBL_NUM = '.$qData['shopNum']);
			$this->db->from('COMMON_CATE');
			$this->db->join($this->tbl, 'COMMON_CATE.REPRESENT_SHOPITEM_NUM = '.$this->tbl.'.NUM', 'left outer');			
			$this->db->order_by('CATE_ORDER', 'ASC');
			$result = $this->db->get()->result_array();			
		}
		else if($qData['searchKey'] == 'ALLSETUP')
		{
			//카테고리 목록 출력시 해당 Item이 설정한 카테고리가 설정되었는지 포함
			//$qData['item_num']
		}
		
		return $result;
	}
	
	/**
	 * @method name : getItemDataList
	 * 아이템 전체 리스트 
	 * 
	 * @param unknown $qData
	 * @param string $isDelView
	 * @return Ambiguous
	 */
	public function getItemDataList($qData, $isDelView = FALSE)
	{
		//data 총 갯수 select
		$whSql = '1 = 1';
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ? " AND a.".$qData['searchKey']." LIKE '%a.".$qData['searchWord']."%'" : "";
		$whSql .= (!empty($qData['viewYn'])) ? " AND a.VIEW_YN = '".$qData['viewYn']."'" : "";
		$whSql .= (!empty($qData['itemState'])) ? " AND a.ITEMSTATECODE_NUM = ".$qData['itemState'] : "";
		$whSql .= (!empty($qData['itemName'])) ? " AND a.ITEM_NAME LIKE '%".$qData['itemName']."%'" : "";
		$whSql .= (!empty($qData['itemCode'])) ? " AND a.ITEM_CODE = '".$qData['itemCode']."'" : "";
		$whSql .= (!empty($qData['shopName'])) ? " AND b.SHOP_NAME LIKE '%".$qData['shopName']."%'" : "";
		$whSql .= (!empty($qData['shopCode'])) ? " AND b.SHOP_CODE = '".$qData['shopCode']."'" : "";		
		$whSql .= (!empty($qData['shopUserName'])) ? " AND b.SHOPUSER_NAME LIKE '%".$qData['shopUserName']."%'" : "";
		if (isset($qData['sNum']))
		{
			$whSql .= ($qData['sNum'] > 0) ? " AND a.SHOP_NUM = '".$qData['sNum']."'" : "";			
		}

		if ((isset($qData['itemCate'])))
		{
			if (!empty($qData['itemCate']) && $qData['itemCate'] > 0)
			{
				$whSql .= "
					AND a.NUM IN (
						SELECT SHOPITEM_NUM FROM ".$this->tbl."_CATE
						WHERE CATE_NUM = ".$qData['itemCate']."
						AND DEL_YN = 'N'
					)
				";				
			}
		}
		
		if (!empty($qData['itemTag']))
		{
			$whSql .= "
				AND a.NUM IN (
					SELECT TBL_NUM FROM COMMON_TAG
					WHERE TBLCODE_NUM = ".$this->tblCodeNum."
					AND DEL_YN = 'N'
					AND TAG LIKE '%".$qData['itemTag']."%'											
				)
			";			
		}
		
		if (isset($qData['pageMethod']))
		{
			if ($qData['pageMethod'] == 'apprlist' || $qData['pageMethod'] == 'denylist' || $qData['pageMethod'] == 'modilist')
			{
				$whSql .= (!empty($qData['sDate']) && !empty($qData['eDate'])) ? " AND a.APPROVAL_REQ_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' " : "";
					
				if ($qData['pageMethod'] == 'apprlist')
				{
					$whSql .= " AND a.ITEMSTATECODE_NUM BETWEEN 8020 AND 8050";
				}
				else if ($qData['pageMethod'] == 'denylist')
				{
					$whSql .= " AND a.ITEMSTATECODE_NUM BETWEEN 8040 AND 8050";
				}
				else if ($qData['pageMethod'] == 'modilist')
				{
					$whSql .= " AND a.ITEMSTATECODE_NUM BETWEEN 7910 AND 7960";
				}
			}
			else
			{
				$whSql .= " AND a.ITEMSTATECODE_NUM > 8000";
				$whSql .= (!empty($qData['sDate']) && !empty($qData['eDate'])) ? " AND a.APPROVAL_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' " : "";
			}
			
			if ($qData['pageMethod'] == 'exceptbestlist')
			{
				$whSql .= " AND NUM NOT IN (
				)";
			}	
		}
		
		if (isset($qData['isTotalSearch'])) //OR 전체 검색
		{
			if ($qData['isTotalSearch'])
			{
				$whSql = str_replace('AND', 'OR', $whSql);
				$whSql = str_replace("OR DEL_YN = 'N'", "AND DEL_YN = 'N'", $whSql);
				$whSql = str_replace("OR a.DEL_YN = 'N'", "AND a.DEL_YN = 'N'", $whSql);				
				$whSql = str_replace("OR a.ITEM_NAME", "AND (a.ITEM_NAME", $whSql);
				$whSql = str_replace("OR TAG", "AND TAG", $whSql).')';
			}
		}
		
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND a.VIEW_YN = 'Y' AND a.ITEMSTATECODE_NUM IN (8060, 8070)" : "";
		}
				
		$addSelect = " 0 AS ITEM_FLAG, 0 AS ITEM_BUY ";
		if (isset($qData['userNum']))
		{
			$addSelect = ($qData['userNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->tblCodeNum."
					AND TBL_NUM = a.NUM
					AND USER_NUM = ".$qData['userNum']."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = a.NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART 
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$qData['userNum']." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
		}
		
		$orderBy = 'NUM DESC'; //정렬
		if (isset($qData['orderBy']))
		{
			if (!empty($qData['orderBy']))
			{
				switch($qData['orderBy'])
				{
					case 'new':
						$orderBy = 'NUM DESC';
						break;
					case 'pop':
						$orderBy = 'TOTSELL_COUNT DESC, TOTFLAG_COUNT DESC, READ_COUNT DESC, NUM DESC';
						break;
					case 'low';
						$orderBy = 'ITEM_PRICE ASC, NUM DESC';
						break;
					case 'high';
						$orderBy = 'ITEM_PRICE DESC, NUM DESC';
						break;
				}
				
			}
		}
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.' AS a');
		$this->db->join('SHOP AS b', 'a.SHOP_NUM = b.NUM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			a.NUM AS SHOPITEM_NUM,
			(SELECT CODE_ID FROM CODE WHERE NUM = a.REFPOLICYCODE_NUM) AS REFPOLICYCODE_ID,
			(SELECT TITLE FROM CODE WHERE NUM = a.REFPOLICYCODE_NUM) AS REFPOLICYCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = a.ITEMSTATECODE_NUM) AS ITEMSTATECODE_TITLE,
			".$addSelect.",
			b.SHOP_NAME,
			b.SHOP_CODE,
			b.SHOPUSER_NAME,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM ".$this->tbl."_FILE
				WHERE SHOPITEM_NUM = a.NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'W'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM ".$this->tbl."_FILE
				WHERE SHOPITEM_NUM = a.NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'M'
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO				
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('SHOP AS b', 'a.SHOP_NUM = b.NUM');
		$this->db->where($whSql);
		$this->db->order_by($orderBy);
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}
	
	/**
	 * @method name : getFlagItemDataList
	 * Flag 된 아이템 리스트 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getFlagItemDataList($qData)
	{
		//data 총 갯수 select
		$whSql = '1 = 1';
	
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND a.VIEW_YN = 'Y' AND a.ITEMSTATECODE_NUM IN (8060, 8070)" : "";
		}
	
		$addSelect = " 1 AS ITEM_FLAG, 0 AS ITEM_BUY ";
		if (isset($qData['userNum']))
		{
			$userNum = $qData['userNum'];
			if (isset($qData['seeUserNum']))
			{
				if ($qData['seeUserNum'] > 0)
				{
					//타인의 정보를 보는 사용자의 회원고유번호가 있는 경우
					$userNum = $qData['seeUserNum'];					
				}
			}
				
			$addSelect = ($userNum > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->tblCodeNum."
					AND TBL_NUM = a.SHOPITEM_NUM
					AND USER_NUM = ".$userNum."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = a.SHOPITEM_NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$userNum." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
			
			$whSql .= ($qData['userNum'] > 0) ? ' AND a.USER_NUM = '.$qData['userNum'] : '';
		}
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('VIEW_FLAG_ITEM AS a');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			".$addSelect.",				
			(SELECT TITLE FROM CODE WHERE NUM = a.ITEMSTATECODE_NUM) AS ITEMSTATECODE_TITLE,
		");
		$this->db->from('VIEW_FLAG_ITEM AS a');
		$this->db->where($whSql);
		$this->db->order_by('SHOPITEM_NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
	
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
	
		return $result;
	}
	
	/**
	 * @method name : getFlagShopDataList
	 * Flag 된 샵과 샵에 속한 아이템 리스트
	 * 
	 * @param unknown $qData
	 * @param unknown $isItemView 아이템 리스트 목록에 포함할지 여부
	 * @return Ambiguous
	 */
	public function getFlagShopDataList($qData, $isItemView = TRUE)
	{
		//data 총 갯수 select
		$whSql = '1 = 1';
	
		$addSelect = " 1 AS SHOP_FLAG ";
		$shopNum = (isset($qData['shopNum'])) ? $qData['shopNum'] : 0;
		//$userNum = (isset($qData['userNum'])) ? $qData['userNum'] : 0;
		if ($shopNum > 0)
		{
			$whSql .= ' AND a.SHOP_NUM = '.$shopNum;
			if (isset($qData['userNum']))
			{
				if ($qData['userNum'] > 0)
				{
					$whSql .= ' AND a.USER_NUM <> '.$qData['userNum']; //본인은 제외
				}				
			}
		}
		else 
		{
			if (isset($qData['userNum']))
			{
				$userNum = $qData['userNum'];
				if (isset($qData['seeUserNum']))
				{
					if ($qData['seeUserNum'] > 0)
					{
						//타인의 정보를 보는 사용자의 회원고유번호가 있는 경우
						$userNum = $qData['seeUserNum'];
					}
				}
			
				$tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
				$addSelect = ($userNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$tblCodeNum."
						AND TBL_NUM = a.SHOP_NUM
						AND USER_NUM = ".$userNum."
						AND DEL_YN = 'N'
					) AS SHOP_FLAG
				" : $addSelect;
					
				$whSql .= ($qData['userNum'] > 0) ? ' AND a.USER_NUM = '.$qData['userNum'] : '';
			}
		}
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('VIEW_FLAG_SHOP AS a');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			".$addSelect.",
			b.USER_NAME,
			AES_DECRYPT(UNHEX(b.USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,				
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER')."
				AND TBL_NUM = a.USER_NUM 
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS USER_PROFILE_FILE_INFO				
			
		");
		$this->db->from('VIEW_FLAG_SHOP AS a');
		$this->db->join('USER b', 'a.USER_NUM = b.NUM');
		$this->db->where($whSql);
		$this->db->order_by('SHOP_NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
		
		if ($rowData && $isItemView) //샵에 속한 Item리스트 만들어 주기
		{
			for($i=0; $i<count($rowData); $i++)
			{
				$qDt = array(
					'sNum' => $rowData[$i]['SHOP_NUM'],
					'userNum' => $qData['userNum'],						
					'listCount' => $qData['itemListCount'],
					'currentPage' => $qData['itemCurrentPage'],
					'isValidData' => $qData['isValidData']
				);
				
				$itemDt = $this->getItemDataList($qDt);				
				$rowData[$i]['itemSet'] = $itemDt;
			}
		}
	
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
	
		return $result;
	}	
	
	/**
	 * @method name : getItemHistoryDataList
	 * 아이템관련 히스토리 목록
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getItemHistoryDataList($qData, $isDelView)
	{
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['siNum'])) ? " AND a.SHOPITEM_NUM = ".$qData['siNum'] : '';
		if (isset($qData['itemState']))
		{
			if ($qData['itemState'] == 'lowerApproval')
			{
				//승인전 단계
				$whSql .= " AND a.ITEMSTATECODE_NUM < 8060";
			}
			else if ($qData['itemState'] == 'upperApproval')
			{
				//승인이후 단계
				$whSql .= " AND a.ITEMSTATECODE_NUM > 8050";
			}
		}
		$whSql .= ($qData['itemStateCodeNum'] > 0) ? " AND a.ITEMSTATECODE_NUM = ".$qData['itemStateCodeNum'] : '';
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.'_HISTORY AS a');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
  			a.*,
			(SELECT TITLE FROM CODE WHERE NUM = a.ITEMSTATECODE_NUM) AS ITEMSTATECODE_TITLE,
			AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(b.USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
			AES_DECRYPT(UNHEX(b.USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
			b.USER_NAME AS ADMINUSER_NAME,
			c.ITEM_NAME,
			c.ITEM_CODE,
			d.NUM AS SHOP_NUM,
			d.SHOP_NAME,
			d.SHOPUSER_NAME
		");
		$this->db->from($this->tbl.'_HISTORY AS a');
		$this->db->join('USER AS b', 'a.ADMINUSER_NUM = b.NUM', 'left outer');
		$this->db->join('SHOPITEM AS c', 'a.SHOPITEM_NUM = c.NUM');		
		$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM');
		$this->db->where($whSql);
		$this->db->order_by('a.NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
	
		return $result;
	}	
	
	/**
	 * @method name : getItemRowData
	 * 한번에 모든 data 불러오기 
	 * 
	 * @param unknown $siNum
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getItemRowData($siNum, $isDelView)
	{
		return $result;
	}
	
	/**
	 * @method name : getItemBaseRowData
	 * 아이템 기본정보 data 
	 * 
	 * @param unknown $siNum
	 * @param unknown $isDelView
	 */
	public function getItemBaseRowData($siNum, $userNum, $isDelView = FALSE)
	{
		$addSql = (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$addSelect = " 0 AS ITEM_FLAG, 0 AS ITEM_BUY ";
		if (isset($userNum))
		{
			$addSelect = ($userNum > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->tblCodeNum."
					AND TBL_NUM = a.NUM
					AND USER_NUM = ".$userNum."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = a.NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$userNum." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
		}		
		$sql = "
			SELECT
				a.*,
				(SELECT TITLE FROM CODE WHERE NUM = a.ITEMSTATECODE_NUM) AS ITEMSTATECODE_TITLE,
				(SELECT TITLE FROM CODE WHERE NUM = a.REFPOLICYCODE_NUM) AS REFPOLICYCODE_TITLE,				
				AES_DECRYPT(UNHEX(b.USER_EMAIL), '".$this->_encKey."') AS APPROVALUSER_EMAIL_DEC,
				AES_DECRYPT(UNHEX(b.USER_TEL), '".$this->_encKey."') AS APPROVALUSER_TEL_DEC,
				AES_DECRYPT(UNHEX(b.USER_MOBILE), '".$this->_encKey."') AS APPROVALUSER_MOBILE_DEC,
				b.USER_NAME AS APPROVALUSER_NAME,
				".$addSelect.",
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM ".$this->tbl."_FILE
					WHERE SHOPITEM_NUM = a.NUM 
					AND DEL_YN = 'N' 
					AND FILE_USE = 'W'
					ORDER BY NUM LIMIT 1
				) AS FILE_INFO,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM ".$this->tbl."_FILE
					WHERE SHOPITEM_NUM = a.NUM 
					AND DEL_YN = 'N' 
					AND FILE_USE = 'M'
					ORDER BY NUM LIMIT 1
				) AS M_FILE_INFO						
			FROM ".$this->tbl." a LEFT OUTER JOIN USER b
			ON a.APPROVALUSER_NUM = b.NUM
			WHERE a.NUM = ".$siNum." ".$addSql."
			LIMIT 1
		";

		// log_message('debug', "####################################");
		// log_message('debug', $this->db->last_query());	
		// log_message('debug', "####################################");

		return $this->db->query($sql)->row_array();		
	}
	
	/**
	 * @method name : getIsItemShopCodeExist
	 * 동일한 아이템 샵 생성 코드 존재 유무 확인 
	 * 
	 * @param unknown $code
	 */
	public function getIsItemShopCodeExist($code)
	{
		$this->db->select('COUNT(*) AS CNT');
		$this->db->from($this->tbl);
		$this->db->where("ITEMSHOP_CODE = '".$code."'");
		
		return ($this->db->get()->row()->CNT > 0) ? TRUE : FALSE;
	}
	
	/**
	 * @method name : getItemFileDataList
	 * 아이템 파일 첨부 data List 
	 * 
	 * @param unknown $siNum
	 */
	public function getItemFileDataList($siNum)
	{
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("SHOPITEM_NUM = ".$siNum);
		$this->db->where("DEL_YN = 'N'");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');

		$retarr = $this->db->get()->result_array();

		// log_message('debug', "####################################");
		// log_message('debug', $this->db->last_query());	
		// log_message('debug', "####################################");

		return $retarr;
	}
	
	/**
	 * @method name : getItemCateDataList
	 * 아이템 카테고리 설정 data List 
	 * 
	 * @param unknown $siNum
	 */
	public function getItemCateDataList($siNum)
	{
		$this->db->select('*');
		$this->db->from($this->_cateTbl);
		$this->db->where("SHOPITEM_NUM = ".$siNum);
		$this->db->where("DEL_YN = 'N'");
		$this->db->order_by('NUM', 'ASC');
		
		return $this->db->get()->result_array();		
	}
	
	/**
	 * @method name : getItemTagDataList
	 * 아이템 태그 설정 data list 
	 * 
	 * @param unknown $siNum
	 */
	public function getItemTagDataList($siNum)
	{
		$this->db->select('*');
		$this->db->from($this->_tagTbl);
		$this->db->where("TBLCODE_NUM = ".$this->tblCodeNum);
		$this->db->where("TBL_NUM = ".$siNum);
		$this->db->where("DEL_YN = 'N'");
		$this->db->order_by('NUM', 'ASC');
		
		return $this->db->get()->result_array();
	}
	
	/**
	 * @method name : getItemOptionRowData
	 * 아이템 옵션 설정 data List
	 * 옵션으로 이미 구매가 되었는지 여부도 확인 
	 * 
	 * @param unknown $siNum
	 * @return unknown[][]
	 */
	public function getItemOptionRowDataList($siNum)
	{
		$this->db->select("
			".$this->tbl."_OPTION.*,
			".$this->tbl."_OPTION_SUB.NUM AS SHOPITEM_OPTION_SUB_NUM,				
			".$this->tbl."_OPTION_SUB.SHOPITEM_OPTION_NUM,
			".$this->tbl."_OPTION_SUB.OPTSUB_TITLE,
			".$this->tbl."_OPTION_SUB.OPTION_PRICE,
			".$this->tbl."_OPTION_SUB.SOLDOUT_YN,
			(
				SELECT COUNT(NUM)
				FROM ORDERITEM_OPTION
				WHERE SHOPITEM_OPTION_SUB_NUM = ".$this->tbl."_OPTION_SUB.NUM
			) AS BUY_COUNT 				
		");
		$this->db->from($this->tbl.'_OPTION');
		$this->db->join($this->tbl.'_OPTION_SUB', $this->tbl.'_OPTION.NUM = '.$this->tbl.'_OPTION_SUB.SHOPITEM_OPTION_NUM');
		$this->db->where("SHOPITEM_NUM = ".$siNum);
		$this->db->where($this->tbl."_OPTION.DEL_YN = 'N'");
		$this->db->where($this->tbl."_OPTION_SUB.DEL_YN = 'N'");
		$this->db->order_by($this->tbl.'_OPTION.NUM', 'ASC');
		$this->db->order_by($this->tbl.'_OPTION_SUB.NUM', 'ASC');
		$optSet = $this->db->get()->result_array();
		$optSetDist = $this->common->array_distinct($optSet, 'SHOPITEM_OPTION_NUM');
		
		$arrOpt = array();
		for($i=0; $i<count($optSetDist); $i++)
		{
			$dt = $this->common->array_where($optSet, array('SHOPITEM_OPTION_NUM' => $optSetDist[$i]));
			$arrOpt[$i] = array(
				'NUM' => $dt[0]['NUM'],
				'OPT_TITLE' => $dt[0]['OPT_TITLE'],
				'optSubSet' => array()
			);

			$arrSubOpt = array();
			$buyCount = 0;	//하위옵션에서 구매된 숫자 파악후 상위에도 전체 구매수 표기
			for($j=0; $j<count($dt); $j++)
			{
				$arrSubOpt[] = array(
					'SHOPITEM_OPTION_SUB_NUM' => $dt[$j]['SHOPITEM_OPTION_SUB_NUM'],
					'OPTSUB_TITLE' => $dt[$j]['OPTSUB_TITLE'],
					'OPTION_PRICE' => $dt[$j]['OPTION_PRICE'],
					'BUY_COUNT' => $dt[$j]['BUY_COUNT'],
					'SOLDOUT_YN' => $dt[$j]['SOLDOUT_YN']
				);
				
				if ($dt[$j]['BUY_COUNT'] > 0) $buyCount = $buyCount + 1;
			}
		
			$arrOpt[$i]['optSubSet'] = $arrSubOpt;
			$arrOpt[$i]['buyCountAll'] = $buyCount;
		}
		
		return $arrOpt;
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
	 * @method name : getItemStatsRowData
	 * 아이템 통계 
	 * 
	 * @param unknown $siNum
	 */
	public function getItemStatsRowData($siNum)
	{
		$this->db->select('*');
		$this->db->from('STATS_SHOPITEM');
		$this->db->where("SHOPITEM_NUM = ".$siNum);
		$this->db->limit(1);
		
		return $this->db->get()->row_array();
	}
	
	/**
	 * @method name : getValidItemChargeRowData
	 * 적용일자 대비 유효한 수수료 data 
	 * 
	 * @param unknown $siNum
	 */
	public function getValidItemChargeRowData($siNum)
	{
		$toDate = date('Y-m-d');
		
		$this->db->select("*");
		$this->db->from($this->tbl.'_CHARGE');
		$this->db->where("DEL_YN = 'N'");
		$this->db->where("SHOPITEM_NUM", $siNum);
		$this->db->where("CHARGETYPE_UPDATE_DATE <= '".$toDate."'");
		$this->db->order_by('NUM', 'DESC');
		$this->db->limit(1);
		
		return $this->db->get()->row_array();		
	}
	
	/**
	 * @method name : setValidItemChargeUpdate
	 * CHARGE_TYPE = 'M' 인경우 수수료율 업데이트 
	 * 주문시 수수료 최종 반영할때 기준샵 조회하여 당시의 수수료로 환산해주어야 하며
	 * ITEM_CHARGE 에도 반영당시 기준샵 수수료율을 update한다
	 * 
	 * @param unknown $cgNum
	 * @param unknown $stdShopData
	 */
	public function setValidItemChargeUpdate($cgNum, $stdShopData)
	{
		$result = 0;
		if (count($stdShopData) > 0)
		{
			$this->db->set('ITEM_CHARGE', $stdShopData['ITEM_CHARGE']);
			$this->db->set('PAY_CHARGE', $stdShopData['PAY_CHARGE']);
			$this->db->set('TAX_CHARGE', $stdShopData['TAX_CHARGE']);
			$this->db->where('NUM', $cgNum);
			$this->db->update($this->tbl.'_CHARGE');
			$result = $this->db->affected_rows();
		}
		
		return $result;
	}
	
	/**
	 * @method name : setItemDataInsert
	 * 신규 아이템 등록 
	 * 
	 * @param array $insData
	 * @param string $insCate
	 * @param string $insTag
	 * @param array $insOpt
	 * @param array $insCharge
	 * @param bool $isUpload
	 * @return Ambiguous
	 */
	public function setItemDataInsert($insData, $insCate, $insTag, $insOpt, $insCharge, $isUpload)
	{
		$resultNum = 0;
 		$now = DateTime::createFromFormat('U.u', microtime(true));
 		$tmpCode = $now->format("ymdHisu");	//$now->format("m-d-Y H:i:s.u");
 		$tmpCode = substr($tmpCode, 0, -2);
 		
 		//아이템 복사로 등록하는경우
 		$pageMethod = $insData['pageMethod'];
 		$copyShopItemNum = $insData['copyShopItemNum']; //원본아이템 고유번호
 		$modiReason = $insData['modiReason'];
 		unset($insData['pageMethod']);
 		unset($insData['copyShopItemNum']);
 		unset($insData['modiReason']);
 		
 		$hisContent = '아이템 신규 등록';
 		$itemStateCodeNum = $insData['ITEMSTATECODE_NUM'];
 		if ($pageMethod == 'copyapprovalwrite')
 		{
 			$hisContent = '아이템 수정을 위한 승인절차 등록';
 			$itemStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ITEMSTATE', 'MODI_APP_REQUEST');
 		}
 		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert(
			$this->tbl,
			$insData  + array(
				'ITEM_CODE' => 'IT'.$tmpCode	//str_pad($tmpCode, 7, '0', STR_PAD_LEFT)	//임시부여
			)
		);
		$resultNum = $this->db->insert_id(); //insert후 반영된 최종 NUM			
		
		//카테고리 처리
		$arrCate = explode(',', $insCate);
		foreach ($arrCate as $cate)
		{
			$this->db->insert(
				'SHOPITEM_CATE',
				array(
					'SHOPITEM_NUM' => $resultNum,
					'CATE_NUM' => $cate
				)
			);
		}
		
		//태그 처리
		$arrTag = explode(',', $insTag);
		foreach ($arrTag as $tag)
		{
			$this->db->insert(
				'COMMON_TAG',
				array(
					'TBLCODE_NUM' => $this->tblCodeNum,
					'TBL_NUM' => $resultNum,
					'REMOTEIP' => $this->input->ip_address(),
					'TAG' => trim($tag)
				)
			);
		}
		
		//옵션 처리
		//배열 예 (후에 [order] 추가됨)
		/*
		Array
		(
				[0] => Array
				(
						[opt_title] => 사이즈
						[opt_title_org] => 사이즈
						[0] => Array
						(
								[optsub_title] => 큰거
								[optsub_title_org] => 큰거
								[optsub_price] => 1000
								[optsub_soldout] => Y
								[optsub_price_org] => 1000
								[optsub_soldout_org] => Y
						)
							
						[1] => Array
						(
								[optsub_title] => 작은거
								[optsub_title_org] => 작은거
								[optsub_price] => 2000
								[optsub_price_org] => 2000
								[optsub_soldout_org] => N
						)
				)
		)
		*/
		
		if ($insData['OPTION_YN'] == 'Y') //옵션있음 체크된 경우
		{
			usort($insOpt, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($insOpt); $i++)
			{
				//group_concat에 영향을 주는 특수기호 제거
				$optTitle = str_replace('-', ' ', $insOpt[$i]['opt_title']);
				$optTitle = str_replace('|', ' ', $optTitle);
				$this->db->insert(
					$this->tbl.'_OPTION',
					array(
						'SHOPITEM_NUM' => $resultNum,
						'OPT_TITLE' => $optTitle
					)
				);
				$optNum = $this->db->insert_id();
			
				for($j=0; $j<count($insOpt[$i])-3; $j++) //[order],[opt_title],[opt_title_org] 2개 요소 제외
				{
					if (!isset($insOpt[$i][$j]['optsub_soldout']))
					{
						$soldOutYn = 'N';
					}
					else
					{
						$soldOutYn = (!empty($insOpt[$i][$j]['optsub_soldout'])) ? $insOpt[$i][$j]['optsub_soldout'] : 'N';
					}
					
					//group_concat에 영향을 주는 특수기호 제거
					$optSubTitle = str_replace('-', ' ', $insOpt[$i][$j]['optsub_title']);
					$optSubTitle = str_replace('|', ' ', $optSubTitle);					
					$this->db->insert(
						$this->tbl.'_OPTION_SUB',
						array(
							'SHOPITEM_OPTION_NUM' => $optNum,
							'OPTSUB_TITLE' => $optSubTitle,
							'OPTION_PRICE' => $insOpt[$i][$j]['optsub_price'],
							'SOLDOUT_YN' => $soldOutYn
						)
					);
				}
			}				
		}
		
		//수수료 처리
		$this->db->insert(
			$this->tbl.'_CHARGE',
			$insCharge + array(
				'SHOPITEM_NUM' => $resultNum
			)
		);		
		
		//히스토리 처리
		//$dummyUser = $this->common->getUserInfo('dummy');	//임시 - 샵등록 담당자 고유번호 처리할것
		$this->db->insert(
			$this->tbl.'_HISTORY',
			array(
				'SHOPITEM_NUM' => $resultNum,
				'ITEMSTATECODE_NUM' => $itemStateCodeNum,
				'ADMINUSER_NUM' => $this->common->getSession('user_num'),
				'CONTENT' => $hisContent
			)
		);
		$hisNum = $this->db->insert_id();
		
		//마지막 히스토리 번호 update
		$this->db->set('LASTHISTORY_NUM', $hisNum);
		$this->db->set('ITEM_CODE', 'IT'.$now->format("ymd").str_pad($insData['SHOP_NUM'], 5, '0', STR_PAD_LEFT).str_pad($resultNum, 5, '0', STR_PAD_LEFT));			
		$this->db->where('NUM', $resultNum);
		$this->db->update($this->tbl);
		
		if ($pageMethod != 'copyapprovalwrite')
		{
			//등록 아이템개수 update
			$this->setItemRegistCountUpdateToShop($insData['SHOP_NUM'], $resultNum);
			
			//통계용 기본 데이터 insert
			$this->db->insert(
				'STATS_SHOPITEM',
				array(
					'SHOP_NUM' => $insData['SHOP_NUM'],
					'SHOPITEM_NUM' => $resultNum
				)
			);
		}
		
		if ($resultNum > 0){
			if ($isUpload){
				//추가할 FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/item/'.$resultNum.'/'),
					array(
						'SHOPITEM_NUM' => $resultNum,
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
					//수정승인 요청시 copyapprovalwrite 에는 파일정보 변경 필요없음
					/*복사등록 하는 경우 파일정보도 초기화 되어 등록되므로 아래 사항이 필요하지 않음
					if ($pageMethod == 'copywrite' || $pageMethod == 'copyapprovalwrite')
					{
						//아이템 복사로 등록하는경우 파일정보 유지
						$this->db->select('*');
						$this->db->from($this->_fileTbl);
						$this->db->where("SHOPITEM_NUM = ".$copyShopItemNum);
						$this->db->where("DEL_YN = 'N'");
						$this->db->order_by('FILE_ORDER', 'ASC');
						$this->db->order_by('NUM', 'ASC');
						
						$arrfile = $this->db->get()->result_array();
						
						if (count($arrfile) > 0)
						{
							$i = 0;
							foreach ($arrfile as $fi)
							{
								$isUpdate = FALSE;
								if (isset($uploadResult[$i]))
								{
									if (empty($uploadResult[$i]['FILE_NAME']) && !empty($fi['FILE_NAME']))
									{
										$isUpdate = TRUE;
									}									
								}
								
								if ($isUpdate)
								{
									$newfilePath = $this->config->item('base_uploadPath').'/item/'.$resultNum.'/';
									$uploadResult[$i]['FILE_NAME'] = $fi['FILE_NAME'];
									$uploadResult[$i]['FILE_TEMPNAME'] = $fi['FILE_TEMPNAME'];
									$uploadResult[$i]['FILE_TYPE'] = $fi['FILE_TYPE'];
									$uploadResult[$i]['FILE_SIZE'] = $fi['FILE_SIZE'];
									$uploadResult[$i]['FILE_PATH'] = $newfilePath; //$fi['FILE_PATH'] 원본 파일경로를 신규생성된 파일 경로로 변경
									$uploadResult[$i]['IMAGE_YN'] = $fi['IMAGE_YN'];
									$uploadResult[$i]['THUMB_YN'] = $fi['THUMB_YN'];
									$uploadResult[$i]['FILE_ORDER'] = $fi['FILE_ORDER'];
									$uploadResult[$i]['IMAGE_WIDTH'] = $fi['IMAGE_WIDTH'];
									$uploadResult[$i]['IMAGE_HEIGHT'] = $fi['IMAGE_HEIGHT'];
								}
									
								$i++;
							}
							
							//파일 카피
							$oldDir = '.'.$this->config->item('base_uploadPath').'/item/'.$copyShopItemNum;
							$newDir = '.'.$this->config->item('base_uploadPath').'/item/'.$resultNum;
							$this->common->smartCopy($oldDir, $newDir);
						}						
					}
					*/
					
					for($i=0; $i<count($uploadResult); $i++)
					{
						$this->db->insert($this->_fileTbl, $uploadResult[$i]);
					}
				}
			}
		}
		
		if ($pageMethod == 'copyapprovalwrite') //수정 요청 항목 작성되는 경우
		{
			$dummyUser = $this->common->getUserInfo('dummy');
			$this->db->set('ORIGINAL_ITEM_NUM', $copyShopItemNum);
			$this->db->set('ITEMSTATECODE_NUM', $itemStateCodeNum);
			$this->db->set('MODIFY_REASON', $modiReason);
			$this->db->set('APPROVAL_FIRSTREQ_DATE', date('Y-m-d H:i:s'));
			$this->db->set('APPROVAL_REQ_DATE', date('Y-m-d H:i:s'));
			$this->db->set('APPROVALUSER_NUM', $dummyUser['NUM']);
			$this->db->set('UPDATE_DATE', NULL);
			$this->db->set('CREATE_DATE', date('Y-m-d H:i:s'));
			$this->db->where('NUM', $resultNum);
			$this->db->update($this->tbl);

			//원본 히스토리에 기록
			$this->db->insert(
				$this->tbl.'_HISTORY',
				array(
					'SHOPITEM_NUM' => $copyShopItemNum,
					'ITEMSTATECODE_NUM' => $itemStateCodeNum,
					'ADMINUSER_NUM' => $this->common->getSession('user_num'),
					'CONTENT' => $hisContent
				)
			);
			$hisNum = $this->db->insert_id();
			
			//마지막 히스토리 번호 update - 원본
			$this->db->set('LASTHISTORY_NUM', $hisNum);
			$this->db->where('NUM', $copyShopItemNum);
			$this->db->update($this->tbl);			
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $resultNum;
	}
	
	/**
	 * @method name : setItemDataUpdate
	 * 아이템 상세설정 내용 update
	 * 
	 * @param int $sNum
	 * @param int $siNum
	 * @param array $upData
	 * @param array $insCate
	 * @param array $insTag
	 * @param array $insOpt
	 * @param array $insHisData
	 * @param array $upCharge
	 * @param bool $isUpload
	 * @return integer
	 */
	public function setItemDataUpdate($sNum, $siNum, $upData, $upCate, $upTag, $upOpt, $upHisData, $upCharge, $isUpload)
	{
		if ($siNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$isChargeUpdateDateUpdate = FALSE;
			if (isset($upData['isChargeUpdateDateUpdate']))
			{
				//최초 등록 아이템의 비어있는 적용일자 업데이트 여부(SHOPITEM_CHARGE)
				$isChargeUpdateDateUpdate = $upData['isChargeUpdateDateUpdate'];
				unset($upData['isChargeUpdateDateUpdate']);
			}
			
			$originalItemNum = $upData['originalItemNum'];
			unset($upData['originalItemNum']);
			
			//기본정보 업데이트
			$this->db->where('NUM', $siNum);
			$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
			
			//카테고리 처리(삭제변경후 다시 insert)
			//실제삭제해도 문제는 없어보이나 변경하는것으로 유지
			//$this->db->where('SHOPITEM_NUM', $siNum);
			//$this->db->delete($this->_cateTbl);
			$this->db->set('DEL_YN', 'Y');
			$this->db->where('SHOPITEM_NUM', $siNum);
			$this->db->update($this->_cateTbl);
			
			$arrCate = explode(',', $upCate);
			foreach ($arrCate as $cate)
			{
				$this->db->insert(
					'SHOPITEM_CATE',
					array(
						'SHOPITEM_NUM' => $siNum,
						'CATE_NUM' => $cate
					)
				);
			}
			
			//태그 처리(삭제변경후 다시 insert)
			if (!empty($upTag))
			{
				$arrTag = explode(',', $upTag);
				if (count($arrTag) > 0)
				{
					$this->db->set('DEL_YN', 'Y');
					$this->db->where('TBLCODE_NUM', $this->tblCodeNum);
					$this->db->where('TBL_NUM', $siNum);
					$this->db->update($this->_tagTbl);
				
					foreach ($arrTag as $tag)
					{
						$this->db->insert(
							'COMMON_TAG',
							array(
								'TBLCODE_NUM' => $this->tblCodeNum,
								'TBL_NUM' => $siNum,
								'REMOTEIP' => $this->input->ip_address(),
								'TAG' => trim($tag)
							)
						);
					}
				}				
			}
			
			//옵션 처리(삭제변경후 다시 insert) - 옵션 선택여부와 관계없이 무조건 삭제
			//실제 삭제하는 경우 삭제전 주문내용이 틀어질 수 있음
			//배열 예 (후에 [order] 추가됨)
			/*
			Array
			(
					[0] => Array
					(
							[opt_title] => 사이즈
							[opt_title_org] => 사이즈
							[0] => Array
							(
									[optsub_title] => 큰거
									[optsub_title_org] => 큰거
									[optsub_price] => 1000
									[optsub_soldout] => Y
									[optsub_price_org] => 1000
									[optsub_soldout_org] => Y
							)
			
							[1] => Array
							(
									[optsub_title] => 작은거
									[optsub_title_org] => 작은거
									[optsub_price] => 2000
									[optsub_price_org] => 2000
									[optsub_soldout_org] => N
							)
						)
			)
			*/
			$sql = "
				UPDATE SHOPITEM_OPTION_SUB
					SET DEL_YN = 'Y'
				WHERE SHOPITEM_OPTION_NUM IN 
					(SELECT NUM FROM SHOPITEM_OPTION WHERE SHOPITEM_NUM = ".$siNum.")
			";
			$this->db->query($sql);
			
			$sql = "UPDATE SHOPITEM_OPTION SET DEL_YN = 'Y' WHERE SHOPITEM_NUM = ".$siNum;
			$this->db->query($sql);
			
			if ($upData['OPTION_YN'] == 'Y') //옵션있음 체크된 경우
			{
				usort($upOpt, $this->common->msort(['order', SORT_ASC]));
				for($i=0; $i<count($upOpt); $i++)
				{
					//group_concat에 영향을 주는 특수기호 제거
					$optTitle = str_replace('-', ' ', $upOpt[$i]['opt_title']);
					$optTitle = str_replace('|', ' ', $optTitle);					
					$this->db->insert(
						$this->tbl.'_OPTION',
						array(																		
							'SHOPITEM_NUM' => $siNum,
							'OPT_TITLE' => $optTitle
						)
					);
					$optNum = $this->db->insert_id();
					
					for($j=0; $j<count($upOpt[$i])-3; $j++)	//[sub_order],[opt_title],[opt_title_org] 3개 요소 제외
					{
						if (!isset($upOpt[$i][$j]['optsub_soldout']))
						{
							$soldOutYn = 'N';
						}
						else 
						{
							$soldOutYn = (!empty($upOpt[$i][$j]['optsub_soldout'])) ? $upOpt[$i][$j]['optsub_soldout'] : 'N';
						}
						
						//group_concat에 영향을 주는 특수기호 제거
						$optSubTitle = str_replace('-', ' ', $upOpt[$i][$j]['optsub_title']);
						$optSubTitle = str_replace('|', ' ', $optSubTitle);						
						$this->db->insert(
							$this->tbl.'_OPTION_SUB',
							array(
								'SHOPITEM_OPTION_NUM' => $optNum,
								'OPTSUB_TITLE' => $optSubTitle,
								'OPTION_PRICE' => (!empty($upOpt[$i][$j]['optsub_price'])) ? $upOpt[$i][$j]['optsub_price'] : 0,
								'SOLDOUT_YN' => $soldOutYn		
							)
						);
					}
				}				
			}
			
			//수수료 처리
			if ($isChargeUpdateDateUpdate)
			{
				//최초 등록 아이템의 비어있는 적용일자 업데이트 여부(SHOPITEM_CHARGE)
				$this->db->set('CHARGETYPE_UPDATE_DATE', date('Y-m-d H:i:s'));
				$this->db->where('SHOPITEM_NUM', $siNum);
				$this->db->where('DEL_YN', 'N');
				$this->db->where("CHARGETYPE_UPDATE_DATE IS NULL || CHARGETYPE_UPDATE_DATE = ''");
				$this->db->update($this->tbl.'_CHARGE');
			}
			
			if (count($upCharge) > 0)
			{
				$this->db->insert(
					$this->tbl.'_CHARGE',
					$upCharge + array(
						'SHOPITEM_NUM' => $siNum
					)
				);				
			}
			
			//히스토리 처리
			$this->db->insert($this->tbl.'_HISTORY', $upHisData);
			$hisNum = $this->db->insert_id();
				
			//마지막 히스토리 번호 update
			$this->db->set('LASTHISTORY_NUM', $hisNum);
			$this->db->where('NUM', $siNum);
			$this->db->update($this->tbl);		
			
			//등록 아이템개수 update
			$this->setItemRegistCountUpdateToShop($sNum, $siNum);
			
			if ($isUpload)
			{
				//추가할 FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/item/'.$siNum.'/'),
					array(
						'SHOPITEM_NUM' => $siNum,
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
						//비교를 위해 기존 업로드된 내용을 확인한다
						$this->db->select('*');
						$this->db->limit(1);
						$this->db->from($this->_fileTbl);
						$this->db->where("SHOPITEM_NUM = ".$siNum);
						$this->db->where("FILE_ORDER = ".$i);
						$this->db->where("DEL_YN = 'N'");
						$oldFile = $this->db->get()->row_array();
						
						if (count($oldFile) > 0)
						{
							if ($this->common->nullCheck($uploadResult[$i]['FILE_NAME'], 'str', '') != '')
							{
								if ($oldFile['FILE_NAME'] != $uploadResult[$i]['FILE_NAME'] || $oldFile['FILE_SIZE'] != $uploadResult[$i]['FILE_SIZE'])
								{
									//파일명 또는 파일사이즈가 다른 경우 삭제 플래그 만 변경
									$this->db->set('DEL_YN', 'Y');	//배열로 업데이트 가능
									$this->db->where('NUM', $oldFile['NUM']);
									$this->db->update($this->_fileTbl);
									//update after insert
									$this->db->insert($this->_fileTbl, $uploadResult[$i]);
								}								
							}
						}
						else
						{
							$this->db->insert($this->_fileTbl, $uploadResult[$i]);
						}
					}
					
					//그외의 파일내용 삭제(플래그 변경)
					$this->db->set('DEL_YN', 'Y');
					$this->db->where('SHOPITEM_NUM', $siNum);
					$this->db->where('FILE_ORDER >= '.$i);
					$this->db->update($this->_fileTbl);					
				}
			}
					
			if ($upData['ITEMSTATECODE_NUM'] == 7960) //수정건 승인시
			{
				$this->setModiItemApproval($siNum, $originalItemNum, $upData['ITEMSTATECODE_NUM']);
			}			
			
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $siNum;
	}
	
	/**
	 * @method name : setItemFileDelete
	 * 개별 파일 삭제
	 * 웹, 모바일 한쌍중 나머지가 삭제되었는지 확인
	 * 다른 나머지도 삭제되었다면 한쌍이(W,M) 모두 삭제된셈이므로
	 * 한쌍을 제외한 나머지 FILE_ORDER를 -1 해준다(상위 ORDER의 파일만)
	 * 
	 * @param int $siNum	SHOPITEM 고유번호
	 * @param int $fNum
	 * @param int $fIndex
	 */
	public function setItemFileDelete($siNum, $fNum, $fIndex)
	{
		$otherIndex = (($fIndex % 2) == 0) ? ($fIndex + 1) : ($fIndex - 1);
		
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where('SHOPITEM_NUM', $siNum);
		$this->db->where('FILE_ORDER', $otherIndex);
		$this->db->where('DEL_YN', 'N');		
		$result = $this->db->get()->row_array();
		
		if ($result)
		{
			if ($result['DEL_YN'] == 'Y' || $result['FILE_NAME'] == '')
			{
				//쌍으로 삭제
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('SHOPITEM_NUM', $siNum);
				$this->db->where('FILE_ORDER IN ('.$fIndex.', '.$otherIndex.')');
				$this->db->update($this->_fileTbl);
					
				//FILE_ORDER 조정
				$this->db->set('FILE_ORDER', 'FILE_ORDER - 2', FALSE);
				$this->db->where('SHOPITEM_NUM', $siNum);
				$this->db->where('DEL_YN', 'N');
				$this->db->where('FILE_ORDER > '.$fIndex);
				$this->db->update($this->_fileTbl);
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
					
				$this->db->where('SHOPITEM_NUM', $siNum);
				$this->db->where('NUM', $fNum);
				$this->db->update($this->_fileTbl, $upData);				
			}
		}
	}
	
	/**
	 * @method name : setItemDataChange
	 * 상태변경 버튼별 액션 처리
	 * 
	 * @param unknown $method
	 * @param unknown $selValue
	 * @param unknown $insHisData
	 * @return number
	 */
	public function setItemDataChange($method, $selValue, $insHisData)
	{
		$result = 0;
		$itemStateCodeNum = 0;
		$selValue = explode(',', $selValue);
		
		if (is_array($selValue))
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			foreach ($selValue as $val)
			{
				if ($method == 'show')
				{
					$this->db->set('VIEW_YN', 'Y');
					$this->db->set('UPDATE_DATE', date('Y-m-d H:i:s'));
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
				else if ($method == 'hide')
				{
					$this->db->set('VIEW_YN', 'N');
					$this->db->set('UPDATE_DATE', date('Y-m-d H:i:s'));					
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();						
				}
				else if ($method == 'sell') //승인과 동일
				{
					$itemStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ITEMSTATE', 'RUN');
					unset($insHisData['ITEMSTATECODE_NUM']);
					$insHisData['ITEMSTATECODE_NUM'] = $itemStateCodeNum;
					$this->db->set('ITEMSTATECODE_NUM', $itemStateCodeNum);
					$this->db->set('APPROVAL_DATE', date('Y-m-d H:i:s'));
					$this->db->set('ITEMSTATE_UPDATE_DATE', date('Y-m-d H:i:s'));					
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
				else if ($method == 'soldout')
				{
					$itemStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ITEMSTATE', 'SOLDOUT');
					unset($insHisData['ITEMSTATECODE_NUM']);
					$insHisData['ITEMSTATECODE_NUM'] = $itemStateCodeNum;					
					$this->db->set('ITEMSTATECODE_NUM', $itemStateCodeNum);
					$this->db->set('ITEMSTATE_UPDATE_DATE', date('Y-m-d H:i:s'));					
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();				
				}
				else if ($method == 'runstop')
				{
					$itemStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ITEMSTATE', 'RUNSTOP');
					unset($insHisData['ITEMSTATECODE_NUM']);
					$insHisData['ITEMSTATECODE_NUM'] = $itemStateCodeNum;					
					$this->db->set('ITEMSTATECODE_NUM', $itemStateCodeNum);
					$this->db->set('ITEMSTATE_UPDATE_DATE', date('Y-m-d H:i:s'));					
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();				
				}
				else if ($method == 'delete')
				{
					$this->db->set('DEL_YN', 'Y');
					$this->db->set('UPDATE_DATE', date('Y-m-d H:i:s'));
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
			}
			
			if ($result > 0)
			{
				//등록 아이템개수 update
				$this->setItemRegistCountUpdateToShop(0, $val);
				
				//히스토리 처리
				$insHisData['SHOPITEM_NUM'] = $val;
				$this->db->insert($this->tbl.'_HISTORY', $insHisData);
				$hisNum = $this->db->insert_id();
					
				//마지막 히스토리 번호 update
				$this->db->set('LASTHISTORY_NUM', $hisNum);
				$this->db->where('NUM', $val);
				$this->db->update($this->tbl);
			}
			
			//Transaction 자동 커밋
			$this->db->trans_complete();			
		}
		
		return $result;
	}	
	
	/**
	 * @method name : setModiItemDataChange
	 * 수정아이템 상태변경 버튼별 액션 처리
	 *
	 * @param unknown $method
	 * @param unknown $selValue
	 * @param unknown $insHisData
	 * @return number
	 */
	public function setModiItemDataChange($method, $selValue, $insHisData)
	{
		$result = 0;
		$itemStateCodeNum = 0;
		$selValue = explode(',', $selValue);
	
		if (is_array($selValue))
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			foreach ($selValue as $val)
			{
				$arrVal = explode('|', $val); //[0]:해당아이템 고유번호, [1]: 원본 아이템 고유번호
				if ($method == 1000) //삭제
				{
					$this->db->set('DEL_YN', 'Y');
					$this->db->set('UPDATE_DATE', date('Y-m-d H:i:s'));
					$this->db->where('NUM', $arrVal[0]);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
				else if ($method == 7960) //수정 최종 승인
				{
					$this->setModiItemApproval($arrVal[0], $arrVal[1], $method);
				}
				else
				{
					$this->db->set('ITEMSTATECODE_NUM', $method);
					$this->db->set('ITEMSTATE_UPDATE_DATE', date('Y-m-d H:i:s'));
					$this->db->where('NUM', $arrVal[0]);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
			}
				
			if ($result > 0 && $method != 1000)
			{
				//히스토리 처리
				$insHisData['SHOPITEM_NUM'] = $arrVal[0];
				$this->db->insert($this->tbl.'_HISTORY', $insHisData);
				$hisNum = $this->db->insert_id();
					
				//마지막 히스토리 번호 update
				$this->db->set('LASTHISTORY_NUM', $hisNum);
				$this->db->where('NUM', $arrVal[0]);
				$this->db->update($this->tbl);
				
				//원본 히스토리 처리
				$insHisData['SHOPITEM_NUM'] = $arrVal[1];
				$this->db->insert($this->tbl.'_HISTORY', $insHisData);
				$hisNum = $this->db->insert_id();
					
				//원본 마지막 히스토리 번호 update
				$this->db->set('LASTHISTORY_NUM', $hisNum);
				$this->db->where('NUM', $arrVal[1]);
				$this->db->update($this->tbl);				
			}
				
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $result;
	}	
	
	/**
	 * @method name : setModiItemApproval
	 * 수정요청 아이템 리스트의 상태변경
	 * 
	 * @param unknown $siNum 수정본 아이템 고유번호
	 * @param unknown $orgSiNum 원본 아이템 고유번호
	 * @param unknown $itemStateCodeNum 수정본에 업데이트될 상태코드번호
	 */
	public function setModiItemApproval($siNum, $orgSiNum, $itemStateCodeNum)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		//수정본 상태 업데이트
		$this->db->set('ITEMSTATECODE_NUM', $itemStateCodeNum);
		$this->db->set('APPROVAL_DATE', date('Y-m-d H:i:s'));
		$this->db->set('ITEMSTATE_UPDATE_DATE', date('Y-m-d H:i:s'));
		$this->db->set('APPROVALUSER_NUM', $this->common->getSession('user_num'));
		$this->db->where('NUM', $siNum);
		$this->db->update($this->tbl);
		$result = $this->db->affected_rows();		
		
		//원본 서브 옵션 삭제 처리
		$sql = "
			UPDATE SHOPITEM_OPTION_SUB
				SET
					DEL_YN = 'Y'
			WHERE SHOPITEM_OPTION_NUM IN (
				SELECT NUM FROM SHOPITEM_OPTION WHERE SHOPITEM_NUM = ".$orgSiNum."
			)
		";
		$this->db->query($sql);
		
		//원본 옵션 삭제 처리
		$sql = "
			UPDATE SHOPITEM_OPTION
				SET
					DEL_YN = 'Y'
			WHERE SHOPITEM_NUM = ".$orgSiNum."
		";
		$this->db->query($sql);

		//수정요청건의 SHOPITEM_OPTION
		$this->db->select('*');
		$this->db->from('SHOPITEM_OPTION');
		$this->db->where('SHOPITEM_NUM', $siNum);
		$this->db->where('DEL_YN', 'N');
		$result = $this->db->get()->result_array(); 
		if ($result)
		{
			foreach ($result as $rs):
				$insData = array(
					'SHOPITEM_NUM' => $orgSiNum,
					'OPT_TITLE' => $rs['OPT_TITLE'],
					'DEL_YN' => 'N',
					'CREATE_DATE' => date('Y-m-d H:i:s')
				);
				$this->db->insert('SHOPITEM_OPTION', $insData);
				$resultNum = $this->db->insert_id();
				
				//수정요청건의 SHOPITEM_OPTION_SUB
				$this->db->select('*');
				$this->db->from('SHOPITEM_OPTION_SUB');
				$this->db->where('SHOPITEM_OPTION_NUM', $rs['NUM']);
				$this->db->where('DEL_YN', 'N');
				$result2 = $this->db->get()->result_array(); 
				if ($result2)
				{
					foreach ($result2 as $rs2):
						$insData2 = array(
							'SHOPITEM_OPTION_NUM' => $resultNum,
							'OPTSUB_TITLE' => $rs2['OPTSUB_TITLE'],
							'OPTION_PRICE' => $rs2['OPTION_PRICE'],
							'SOLDOUT_YN' => $rs2['SOLDOUT_YN'],
							'DEL_YN' => 'N',
							'CREATE_DATE' => date('Y-m-d H:i:s')
						);
						$this->db->insert('SHOPITEM_OPTION_SUB', $insData2);
						$resultNum2 = $this->db->insert_id();					
					endforeach;
				}
				
			endforeach;
		}
		
		$sql = "
			UPDATE SHOPITEM a INNER JOIN SHOPITEM b
			ON a.NUM = b.ORIGINAL_ITEM_NUM
				SET
					a.ITEM_NAME = b.ITEM_NAME,
					a.ITEM_PRICE = b.ITEM_PRICE,
					a.OPTION_YN = b.OPTION_YN,
					a.DISCOUNT_YN = b.DISCOUNT_YN, 
					a.DISCOUNT_PRICE = b.DISCOUNT_PRICE,
					a.REFPOLICYCODE_NUM = b.REFPOLICYCODE_NUM,
					a.REFPOLICY_CONTENT = b.REFPOLICY_CONTENT,
					a.PAYAFTER_CANCEL_YN = b.PAYAFTER_CANCEL_YN, 
					a.PAYAFTER_CANCEL_MEMO = b.PAYAFTER_CANCEL_MEMO, 
					a.MADEAFTER_REFUND_YN = b.MADEAFTER_REFUND_YN, 
					a.MADEAFTER_REFUND_MEMO = b.MADEAFTER_REFUND_MEMO, 
					a.MADEAFTER_CHANGE_YN = b.MADEAFTER_CHANGE_YN, 
					a.MADEAFTER_CHANGE_MEMO = b.MADEAFTER_CHANGE_MEMO,
					a.UPDATE_DATE = '".date('Y-m-d H:i:s')."'
			WHERE b.NUM = ".$siNum."				
		";
		$this->db->query($sql);		
		
		//등록 아이템개수 update
		$this->setItemRegistCountUpdateToShop(0, $orgSiNum);		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
	}
	
	/**
	 * @method name : getPrecedeModiItemRowData
	 * 선행 진행되고 있는 동일 아이템 수정건이 존재하는지 여부 판단
	 * 
	 * @param unknown $siNum
	 */
	public function getPrecedeModiItemRowData($siNum)
	{
		$this->db->select('*');
		$this->db->from($this->tbl);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('ORIGINAL_ITEM_NUM', $siNum);
		$this->db->order_by('NUM', 'DESC');
		$this->db->limit(1);
		
		return $this->db->get()->row_array();		
	}

	/**
	 * @method name : setItemDataDelete
	 * 아이템 개별 삭제
	 * 
	 * @param unknown $siNum
	 * @param unknown $insHisData
	 * @return integer
	 */
	public function setItemDataDelete($siNum, $insHisData)
	{
		$result = 0;
		
		$this->db->select('ITEMSTATECODE_NUM');
		$this->db->from($this->tbl);
		$this->db->where('NUM', $siNum);
		$itemState = $this->db->get()->row()->ITEMSTATECODE_NUM;
		$insHisData['ITEMSTATECODE_NUM'] = $itemState;
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		//아이템 삭제
		$this->db->set('DEL_YN', 'Y');
		$this->db->set('UPDATE_DATE', date('Y-m-d H:i:s'));		
		$this->db->where('NUM', $siNum);
		$this->db->update($this->tbl);
		$result = $this->db->affected_rows();
		
		//히스토리 처리
		$this->db->insert($this->tbl.'_HISTORY', $insHisData);
		$hisNum = $this->db->insert_id();
		
		//마지막 히스토리 번호 update
		$this->db->set('LASTHISTORY_NUM', $hisNum);
		$this->db->where('NUM', $siNum);
		$this->db->update($this->tbl);
		
		//등록 아이템개수 update
		$this->setItemRegistCountUpdateToShop(0, $siNum);		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $result;
	}
	
	/**
	 * @method name : setItemRegistCountUpdateToShop
	 * 등록 아이템개수 update
	 * 
	 * @param unknown $sNum
	 * @param unknown $siNum
	 */
	public function setItemRegistCountUpdateToShop($sNum, $siNum)
	{
		if ($sNum == 0)
		{
			$this->db->select('SHOP_NUM');
			$this->db->from($this->tbl);
			$this->db->where('NUM', $siNum);
			$sNum = $this->db->get()->row()->SHOP_NUM;
		}
		
		$sql = "
			UPDATE SHOP
				SET
					TOTITEM_COUNT = (
						SELECT COUNT(NUM) FROM ".$this->tbl." 
						WHERE SHOP_NUM = ".$sNum."
						AND ITEMSTATECODE_NUM IN (8060, 8070)
						AND DEL_YN = 'N'				
					)
			WHERE NUM = ".$sNum."
		";
		$this->db->query($sql);
	}
	
	/**
	 * @method name : getRecommendItemDataList
	 * Item상세보기와 연관된 추천 Item
	 * 
	 * @param unknown $qData
	 * @param string $isDelView
	 * @return Ambiguous
	 */
	public function getRecommendItemDataList($qData, $isDelView = FALSE)
	{
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['sNum'])) ? " AND a.SHOP_NUM = ".$qData['sNum'] : '';
		if (isset($qData['searchKey']))
		{
			if ($qData['searchKey'] == 'category')
			{
				$whSql .= " ";
				$whSql .= "AND a.SHOPITEM_NUM IN (";
				$whSql .= "		SELECT at.SHOPITEM_NUM"; 
				$whSql .= "		FROM ".$this->tbl."_CATE at INNER JOIN SHOPITEM bt";
				$whSql .= "		ON at.SHOPITEM_NUM = bt.NUM";
				$whSql .= "		WHERE at.CATE_NUM IN (".$qData['cateNum'].")";
				if (!empty($qData['sNum']))
				{
					$whSql .= "		AND bt.SHOP_NUM = ".$qData['sNum']."";
				}
				if (!empty($qData['siNum']))
				{
					$whSql .= "		AND at.SHOPITEM_NUM NOT IN (".$qData['siNum'].")";
				}				
				$whSql .= "		AND at.DEL_YN = 'N'";
				$whSql .= "		AND bt.DEL_YN = 'N'";
				$whSql .= "	)";
			}
		}
		
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND b.VIEW_YN = 'Y' AND b.ITEMSTATECODE_NUM IN (8060, 8070)" : "";
		}
		
		$addSelect = " 0 AS ITEM_FLAG, 0 AS ITEM_BUY ";
		if (isset($qData['userNum']))
		{
			$addSelect = ($qData['userNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->tblCodeNum."
					AND TBL_NUM = b.NUM
					AND USER_NUM = ".$qData['userNum']."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = a.SHOPITEM_NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART 
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$qData['userNum']." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
		}
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('SHOP_BESTITEM AS a');
		$this->db->join('SHOPITEM AS b', 'a.SHOPITEM_NUM = b.NUM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		$this->db->select("
  			a.*,
			(SELECT TITLE FROM CODE WHERE NUM = b.ITEMSTATECODE_NUM) AS ITEMSTATECODE_TITLE,
			".$addSelect.",
			b.ITEM_NAME,
			b.ITEM_CODE,
			b.STOCKFREE_YN,
			b.STOCK_COUNT,
			b.DISCOUNT_YN,
			b.ITEMSTATECODE_NUM,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM ".$this->tbl."_FILE
				WHERE SHOPITEM_NUM = b.NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'W'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM ".$this->tbl."_FILE
				WHERE SHOPITEM_NUM = b.NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'M'
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO,
			c.SHOP_NAME,
			c.SHOPUSER_NAME,
			c.SHOP_CODE
		");
		$this->db->from('SHOP_BESTITEM AS a');
		$this->db->join('SHOPITEM AS b', 'a.SHOPITEM_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM');
		$this->db->where($whSql);
		$this->db->order_by('b.TOTSELL_COUNT', 'DESC');
		$this->db->order_by('b.TOTFLAG_COUNT', 'DESC');
		$this->db->order_by('a.NUM', 'DESC');
		$this->db->limit($qData['listCount']);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;		
	}
	
	/**
	 * @method name : setItemReadCountUpdate
	 * 아이템 조회수 증가 
	 * 
	 * @param unknown $siNum
	 */
	public function setItemReadCountUpdate($siNum)
	{
		$this->db->set('READ_COUNT', 'READ_COUNT + 1', FALSE);
		$this->db->where('NUM', $siNum);
		$this->db->update($this->tbl);
	}
	
	/**
	 * @method name : getEventDataList
	 * 이벤트, 기획전 리스트
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getEventDataList($qData, $isDelView)
	{
		$toDate = date('Y-m-d');
		$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'EVENT');
		$order = 'NUM DESC';
		if (!empty($qData['orderType']))
		{
			$order = strtoupper($qData['orderType']).' DESC'.','.$order;
		}
		
		$whSql = "EVENT_TYPE = '".strtoupper($qData['eventType'])."'";
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ? " AND a.".$qData['searchKey']." LIKE '%a.".$qData['searchWord']."%'" : "";
		$whSql .= (!empty($qData['viewYn'])) ? " AND VIEW_YN = '".$qData['viewYn']."'" : "";
		if (isset($qData['alwaysYn'])) //상시진행포함 여부
		{
			$whSql .= (!empty($qData['alwaysYn'])) ? " OR ALWAYS_YN = '".$qData['alwaysYn']."'" : '';
		}

		if (!empty($qData['sDate']) && !empty($qData['eDate']))
		{
			$whSql .= " AND (END_DATE >= '".$qData['sDate']."' AND END_DATE >= '".$qData['eDate']."')";
		}
		
		if (!empty($qData['eventState']))
		{
			if ($qData['eventState'] == 'ing') //진행중
			{
				$whSql .= " AND START_DATE <= '".$toDate."' AND END_DATE >= '".$toDate."'";
			}
			else if ($qData['eventState'] == 'exp') //진행예정
			{
				$whSql .= " AND START_DATE > '".$toDate."'";
			}
			else if ($qData['eventState'] == 'end') //종료
			{
				$whSql .= " AND END_DATE < '".$toDate."'";
			}
		}
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->_enTbl);
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$tblCdNum."
				AND TBL_NUM = ".$this->_enTbl.".NUM
				AND DEL_YN = 'N'
				AND FILE_ORDER = 0
				ORDER BY NUM LIMIT 1
			) AS TOP_FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$tblCdNum."
				AND TBL_NUM = ".$this->_enTbl.".NUM
				AND DEL_YN = 'N'
				AND FILE_ORDER = 1
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$tblCdNum."
				AND TBL_NUM = ".$this->_enTbl.".NUM
				AND DEL_YN = 'N'
				AND FILE_ORDER = 2
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO				
		");
		$this->db->from($this->_enTbl);
		$this->db->where($whSql);
		$this->db->order_by($order);
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
		
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}
	
	/**
	 * @method name : getEventItemDataList
	 * 기획전에 등록한 아이템 리스트 
	 * 광고 item이 항상 위에
	 * 
	 * @param unknown $enNum
	 * @param unknown $userNum 로그인한 사용자가 있는 경우 
	 * @param unknown $isDelView
	 */
	public function getEventItemDataList($enNum, $userNum = 0, $isDelView = FALSE)
	{
		$whSql = "a.EVENT_NUM = ".$enNum;		
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
		
		$addSelect = " 0 AS ITEM_FLAG, 0 AS ITEM_BUY ";
		if (isset($userNum))
		{
			$addSelect = ($userNum > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$this->tblCodeNum."
					AND TBL_NUM = a.SHOPITEM_NUM
					AND USER_NUM = ".$userNum."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = a.SHOPITEM_NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$userNum." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
		}		
		
		$this->db->select("
			a.*,
			b.ITEM_CODE,
			b.ITEMSHOP_CODE,
			b.ITEM_NAME,
			(SELECT TITLE FROM CODE WHERE NUM = b.ITEMSTATECODE_NUM) AS ITEMSTATECODE_TITLE,
			b.STOCKFREE_YN,
			b.STOCK_COUNT,	
			b.ITEMSTATECODE_NUM,	
			".$addSelect.",				
			c.NUM AS SHOP_NUM,
			c.SHOP_NAME,
			c.SHOP_CODE,
			c.SHOPUSER_NAME,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = a.SHOPITEM_NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'W'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = a.SHOPITEM_NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'M'
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO				
		");
		$this->db->from($this->_enitTbl.' AS a');
		$this->db->join($this->tbl.' AS b', 'a.SHOPITEM_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM');
		$this->db->where($whSql);
		$this->db->order_by('b.AD_YN', 'DESC');		
		$this->db->order_by('a.ITEM_ORDER', 'ASC');
		$this->db->order_by('a.NUM', 'ASC');
		$result = $this->db->get()->result_array();
		
		return $result;
	}
	
	/**
	 * @method name : getEventRowData
	 * 기획전(Event) Data 
	 * 
	 * @param unknown $enNum
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getEventRowData($enNum, $isDelView)
	{
		$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'EVENT');
		
		$whSql = "NUM = ".$enNum;
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : '';
		$this->db->select("
			*,
		");
		$this->db->limit(1);
		$this->db->from($this->_enTbl);
		$this->db->where($whSql);
		$result['recordSet'] = $this->db->get()->row_array();
		
		$this->db->select('*');
		$this->db->from('COMMON_FILE');
		$this->db->where("TBLCODE_NUM = ".$tblCdNum);
		$this->db->where("TBL_NUM = ".$enNum);
		$this->db->where("DEL_YN", "N");	
		$this->db->order_by('FILE_ORDER', 'ASC');
		$result['fileSet'] = $this->db->get()->result_array();
		
		return $result;
	}
	
	/**
	 * @method name : setEventDataInsert
	 * 기획전(Event)신규 Data insert 
	 * 
	 * @param unknown $insData
	 * @param unknown $insItem
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setEventDataInsert($insData, $insItem, $isUpload)
	{
		$resultNum = 0;
		$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'EVENT');
			
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert($this->_enTbl, $insData);
		$resultNum = $this->db->insert_id(); //insert후 반영된 최종 NUM
		
		if ($upData['EVENT_TYPE'] != 'e')
		{
			//등록상품 처리
			usort($insItem, $this->common->msort(['item_order', SORT_ASC]));
			for($i=0; $i<count($insItem); $i++)
			{
				$this->db->insert(
					$this->_enitTbl,
					array(
						'EVENT_NUM' => $resultNum,
						'SHOPITEM_NUM' => $insItem[$i]['item_num'],
						'ITEM_ORDER' => $insItem[$i]['item_order']
					)
				);
			}		
		}
		
		if ($resultNum > 0)
		{
			if ($isUpload)
			{
				//추가할 FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/event/'.$resultNum.'/'),
					array(
						'TBLCODE_NUM' => $tblCdNum,
						'TBL_NUM' => $resultNum
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
						$this->db->insert('COMMON_FILE', $uploadResult[$i]);
					}
				}
			}
		}

		//기획전에 등록된 아이템 갯수 업데이트
		$this->db->set('TOTITEM_COUNT', count($insItem));
		$this->db->where('NUM', $resultNum);
		$this->db->update($this->_enTbl);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $resultNum;		
	}
	
	/**
	 * @method name : setEventDataUpdate
	 * 기획전(Event) data update
	 * 등록아이템 변경 유무 체크하여 update 
	 * 
	 * @param unknown $enNum
	 * @param unknown $upData
	 * @param unknown $upItem
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setEventDataUpdate($enNum, $upData, $upItem, $isUpload)
	{
		if ($enNum > 0)
		{
			$resultNum = 0;
			$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'EVENT');
				
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$isItemChanged = FALSE; //등록아이템 변화여부
			$orgItemList = $upData['orgItemList'];  //원본값과 수정값 변화를 판단하기 위한 변수
			unset($upData['orgItemList']); 			
			
			//기획전(Event) 기본 data insert
			$this->db->where('NUM', $enNum);
			$this->db->update($this->_enTbl, $upData);
			
			if ($upData['EVENT_TYPE'] != 'e')
			{
				//등록상품 처리
				$itemList = '';
				for($i=0; $i<count($upItem); $i++)
				{
					if ($upItem[$i]['item_num'] > 0)
					{
						$itemList .= $upItem[$i]['item_num'].',';
					}
					else
					{
						unset($upItem[$i]);
					}
				}
				$itemList = (strlen($itemList) > 0) ? substr($itemList, 0, -1) : '';
				$arrOrgItem = explode(',', $orgItemList);
				$arrItem = explode(',', $itemList);
				sort($arrOrgItem, SORT_NUMERIC);
				sort($arrItem, SORT_NUMERIC);
				usort($upItem, $this->common->msort(['item_order', SORT_ASC]));				
					
				if (count($arrOrgItem) != count($arrItem)) $isItemChanged = TRUE;
				if (count(array_diff($arrOrgItem, $arrItem)) > 0) $isItemChanged = TRUE;
					
				if ($isItemChanged)
				{
					$this->db->set('DEL_YN', 'Y');
					$this->db->where('EVENT_NUM', $enNum);
					$this->db->update($this->_enitTbl);
						
					for($i=0; $i<count($upItem); $i++)
					{
						$this->db->insert(
							$this->_enitTbl,
							array(
								'EVENT_NUM' => $enNum,
								'SHOPITEM_NUM' => $upItem[$i]['item_num'],
								'ITEM_ORDER' => $upItem[$i]['item_order']
							)
						);
					}
				}				
			}
			
			if ($isUpload)
			{
				//추가할 FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/event/'.$enNum.'/'),
					array(
						'TBLCODE_NUM' => $tblCdNum,
						'TBL_NUM' => $enNum
					)
				);
			
				$uploadResult = $this->common->fileUpload($upConfig, TRUE);
			
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
						//비교를 위해 기존 업로드된 내용을 확인한다
						$this->db->select('*');
						$this->db->limit(1);
						$this->db->from('COMMON_FILE');
						$this->db->where('TBLCODE_NUM', $tblCdNum);
						$this->db->where('TBL_NUM', $enNum);
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
									$this->db->update('COMMON_FILE', $upData);
									//update after insert
									$this->db->insert('COMMON_FILE', $uploadResult[$i]);
								}
							}
						}
						else
						{
							$this->db->insert('COMMON_FILE', $uploadResult[$i]);
						}
					}
				}
			}
			
			//기획전에 등록된 아이템 갯수 업데이트
			$this->db->set('TOTITEM_COUNT', count($upItem));
			$this->db->where('NUM', $enNum);
			$this->db->update($this->_enTbl);
			
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $enNum;		
	}
	
	/**
	 * @method name : setEventDataDelete
	 * 기획전(Event) 개별 삭제 
	 * 
	 * @param unknown $enNum
	 * @return Ambiguous
	 */
	public function setEventDataDelete($enNum)
	{
		$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'EVENT');
		
		$this->db->set('DEL_YN', 'Y');
		$this->db->where("NUM = ".$enNum);
		$this->db->update($this->_enTbl);
		$result = $this->db->affected_rows();
		
		return $result;
	}
	
	/**
	 * @method name : setEventGroupDataDelete
	 * 기획전(Event) 다중선택 삭제
	 * 
	 * @param unknown $selValue
	 * @return number
	 */
	public function setEventGroupDataDelete($selValue)
	{
		$result = 0;
		$selValue = explode(',', $selValue);		
		if (is_array($selValue))
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			foreach ($selValue as $val)
			{
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('NUM', $val);
				$this->db->update($this->_enTbl);
				$result = $this->db->affected_rows();			
			}
			
			//Transaction 자동 커밋
			$this->db->trans_complete();			
		}
		
		return $result;
	}

	/**
	 * @method name : setEventFileDelete
	 * 기획전(Event) 첨부된 파일 개별삭제  
	 * 
	 * @param unknown $enNum
	 * @param unknown $fNum
	 * @param unknown $fIndex 파일순서 (순서유지 중요함)
	 * @return Ambiguous
	 */
	public function setEventFileDelete($enNum, $fNum, $fIndex)
	{
		$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'EVENT');
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->set('DEL_YN', 'Y');
		$this->db->where("TBLCODE_NUM = ".$tblCdNum);
		$this->db->where("TBL_NUM = ".$enNum);
		$this->db->where("NUM = ".$fNum);
		$this->db->update('COMMON_FILE');
		$result = $this->db->affected_rows();
		
		$this->db->set('FILE_ORDER', 'FILE_ORDER - 1', FALSE);
		$this->db->where("TBLCODE_NUM = ".$tblCdNum);
		$this->db->where("TBL_NUM = ".$enNum);
		$this->db->where("FILE_ORDER > ".$fIndex);
		$this->db->update('COMMON_FILE');		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $result;		
	}
	
	/**
	 * @method name : setItemCategoryDataInsert
	 * 카테고리관리 - 카테고리 생성 
	 * 
	 * @param unknown $insData
	 * @param unknown $cateListOrder
	 * @param unknown $cateListNum
	 * @return Ambiguous
	 */
	public function setItemCategoryDataInsert($insData, $cateListOrder, $cateListNum)
	{
		$result = 0;
		$tbl = 'SHOP';
		if ($insData['CATE_TYPE'] == 'M')
		{
			//써커스 생성인 경우 관련된 테이블 없음
			$tbl = 'NONE';
		}
		$tblCdNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $tbl);
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert(
			'COMMON_CATE', 
			$insData + array(
				'TBLCODE_NUM' => $tblCdNum
			)
		);
		$resultNum = $this->db->insert_id(); //insert후 반영된 최종 NUM		
		
		$arrCateNum = explode(',', $cateListNum);
		$arrCateOrder = explode(',', $cateListOrder);
		if ($arrCateNum)
		{
			$i = 0;
			foreach ($arrCateNum as $val)
			{
				$this->db->set('CATE_ORDER', $arrCateOrder[$i]);
				$this->db->where("NUM", $val);
				$this->db->update('COMMON_CATE');
				$i++;
			}
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $resultNum;
	}
	
	/**
	 * @method name : setItemCategoryDataUpdate
	 * 카테고리 관리 카테고리 update 
	 * 
	 * @param unknown $upData
	 * @param unknown $cateListOrder
	 * @param unknown $cateListNum
	 * @return number
	 */
	public function setItemCategoryDataUpdate($upData, $cateListOrder, $cateListNum)
	{
		$result = 0;
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$updateCateNum = $upData['NUM'];
		if ($updateCateNum > 0)
		{
			unset($upData['NUM']);
			$this->db->where("NUM", $updateCateNum);
			$this->db->update('COMMON_CATE', $upData);
			$result = $this->db->affected_rows();
		}
		
		$arrCateNum = explode(',', $cateListNum);
		$arrCateOrder = explode(',', $cateListOrder);
		if ($arrCateNum)
		{
			$i = 0;
			foreach ($arrCateNum as $val)
			{
				$this->db->set('CATE_ORDER', $arrCateOrder[$i]);
				$this->db->where("NUM", $val);
				$this->db->update('COMMON_CATE');
				$i++;
			}
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $result;
	}
	
	/**
	 * @method name : getItemRankStatsDataList
	 * 샵아이템 랭킹 통계 데이터 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getItemRankStatsDataList($qData)
	{
		//data 총 갯수 select
		$whSql = '1 = 1';
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ? " AND b.".$qData['searchKey']." LIKE '%a.".$qData['searchWord']."%'" : "";
		$whSql .= (!empty($qData['itemState'])) ? " AND b.ITEMSTATECODE_NUM = ".$qData['itemState'] : "";
		$whSql .= (!empty($qData['itemName'])) ? " AND b.ITEM_NAME LIKE '%".$qData['itemName']."%'" : "";
		$whSql .= (!empty($qData['itemCode'])) ? " AND b.ITEM_CODE = '".$qData['itemCode']."'" : "";
		$whSql .= (!empty($qData['shopName'])) ? " AND c.SHOP_NAME LIKE '%".$qData['shopName']."%'" : "";
		$whSql .= (!empty($qData['shopCode'])) ? " AND c.SHOP_CODE = '".$qData['shopCode']."'" : "";
		$whSql .= (!empty($qData['shopUserName'])) ? " AND c.SHOPUSER_NAME = '".$qData['shopUserName']."'" : "";
		if (isset($qData['sNum']))
		{
			$whSql .= ($qData['sNum'] > 0) ? " AND b.SHOP_NUM = '".$qData['sNum']."'" : "";
		}
		
		if ((!empty($qData['itemCate'])))
		{
			$whSql .= "
				AND b.NUM IN (
					SELECT SHOPITEM_NUM FROM ".$this->tbl."_CATE
					WHERE CATE_NUM = ".$qData['itemCate']."
					AND DEL_YN = 'N'
				)
			";
		}
		
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND a.SHOPITEM_NUM IN (SELECT NUM FROM SHOPITEM WHERE DEL_YN = 'N' AND VIEW_YN = 'Y' AND ITEMSTATECODE_NUM IN (8060, 8070))" : "";
		}		

		//통계결과 정렬
		$orderBy = ($qData['orderBy'] == 'flag') ? 'a.FLAG_RANK_GAP' : 'a.SELLAMOUNT_RANK_GAP';
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('STATS_SHOPITEM AS a');
		$this->db->join($this->tbl.' AS b', 'a.SHOPITEM_NUM = b.NUM');		
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			b.ITEM_CODE,
			b.ITEM_NAME,
			c.SHOP_NAME,
			c.SHOP_CODE,
			c.SHOPUSER_NAME,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM ".$this->tbl."_FILE
				WHERE SHOPITEM_NUM = a.SHOPITEM_NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'W'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM ".$this->tbl."_FILE
				WHERE SHOPITEM_NUM = a.SHOPITEM_NUM 
				AND DEL_YN = 'N' 
				AND FILE_USE = 'M'
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO					
		");
		$this->db->from('STATS_SHOPITEM AS a');
		$this->db->join($this->tbl.' AS b', 'a.SHOPITEM_NUM = b.NUM');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM');
		$this->db->where($whSql);
		$this->db->order_by($orderBy, 'DESC');
		$this->db->order_by('NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
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
}
?>