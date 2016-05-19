<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Order_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 01
 * @version:
 */
class Order_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_ordPartTbl = 'ORDERPART';
	
	protected $_ordItemTbl = 'ORDERITEM';
	
	protected $_ordHisTbl = 'ORDERPART_HISTORY';
	
	protected $_cartTbl = 'CART';
	
	protected $_tblCodeNum = 0;
	
	protected $_encKey = '';	
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'ORDERS';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 주로 사용될 TABLE CODE.NUM
		$this->tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	/**
	 * @method name : getOrderDataList
	 * 주문리스트
	 * ORDERPART(샵단위)까지 return 
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getOrderDataList($qData, $isDelView)
	{
		$whSql = '1 = 1';
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		if (isset($qData['ordStateScopeLow']) || isset($qData['ordStateScopeHigh']) || isset($qData['ordStateScopeExcept']))
		{
			if (!empty($qData['ordStateScopeExcept']))
			{
				$whSql .= ' AND b.ORDSTATECODE_NUM NOT IN ('.$qData['ordStateScopeExcept'].')';
			}
				
			if (!empty($qData['ordStateScopeLow']) && !empty($qData['ordStateScopeHigh']))
			{
				$whSql .= ' AND b.ORDSTATECODE_NUM >= '.$qData['ordStateScopeLow'];
				$whSql .= ' AND b.ORDSTATECODE_NUM <= '.$qData['ordStateScopeHigh'];
			}
		}
		
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ? " AND a.".$qData['searchKey']." LIKE '%a.".$qData['searchWord']."%'" : "";
		$whSql .= (!empty($qData['grpPayType'])) ? " AND a.PAYCODE_NUM IN (".$qData['grpPayType'].")" : '';		
		$whSql .= (!empty($qData['grpOrderState'])) ? " AND b.ORDSTATECODE_NUM IN (".$qData['grpOrderState'].")" : '';		
		$whSql .= (!empty($qData['shopName'])) ? " AND c.SHOP_NAME LIKE '%".$qData['shopName']."%'" : '';
		$whSql .= (!empty($qData['shopCode'])) ? " AND c.SHOP_CODE = '".$qData['shopCode']."'" : '';
		$whSql .= (!empty($qData['deliveryType'])) ? " AND b.DELIVERYCODE_NUM = '".$qData['deliveryType']."' OR b.EXCHGITEM_DELIVERYCODE_NUM = '".$qData['deliveryType']."'" : '';
		$whSql .= (!empty($qData['sNum'])) ? " AND b.SHOP_NUM = ".$qData['sNum'] : '';
		if (!empty($qData['invoiceYn']))
		{
			$whSql .= ($qData['invoiceYn'] == 'Y') ? " AND b.INVOICE_NO IS NOT NULL" : " AND b.INVOICE_NO IS NULL";			
		}
		
		if (!empty($qData['itemName']))
		{
			$whSql .= " 
				AND b.SHOP_NUM IN
					(
						SELECT SHOP_NUM FROM SHOPITEM
						WHERE ITEM_NAME LIKE '%".$qData['itemName']."%'
						AND DEL_YN = 'N'
			)";
		}
		
		if (!empty($qData['itemCode']))
		{
			$whSql .= " 
				AND b.SHOP_NUM IN
					(
						SELECT SHOP_NUM FROM SHOPITEM
						WHERE ITEM_CODE = '".$qData['itemCode']."'
						AND DEL_YN = 'N'
			)";
		}		

		if (!empty($qData['sDate']) && !empty($qData['eDate']))
		{
			if ($qData['dateSearchKey'] == 'order')
			{
				//주문일
				$whSql .= " AND a.CREATE_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' ";				
			}
			else if ($qData['dateSearchKey'] == 'pay')
			{
				//결제일
				$whSql .= " AND a.PAY_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' ";
			}
			else if ($qData['dateSearchKey'] == 'payverify')
			{
				//입금확인일(결제일과 동일)
				$whSql .= " AND a.PAY_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' ";
			}			
		}
		
		if (!empty($qData['ordSearchKey']) && !empty($qData['ordSearchWord']))
		{
			$fieldNm = '';
			$mobileEnc = ''; 
			switch($qData['ordSearchKey'])
			{
				case 'ordcode':
					$fieldNm = 'a.ORDER_CODE';
					break;
				case 'ordname':
					$fieldNm = 'a.ORDER_NAME';
					break;
				case 'ordmobile':
					$fieldNm = 'a.ORDER_MOBILE';
					$mobileEnc = $this->common->sqlEncrypt($qData['ordSearchWord'], $this->_encKey);
					break;
				case 'ordrecname':
					$fieldNm = 'a.RECIPIENT_NAME';
					break;
				case 'ordrecmobile':
					$fieldNm = 'a.RECIPIENT_MOBILE';
					$mobileEnc = $this->common->sqlEncrypt($qData['ordSearchWord'], $this->_encKey);
					break;
				case 'ordinvoiceno':
					$fieldNm = 'b.INVOICE_NO';
					break;					
			}					
			
			if (in_array($qData['ordSearchKey'], array('ordmobile', 'ordrecmobile')))
			{
				$whSql .= " AND ".$fieldNm." = '".$mobileEnc."'";
			}
			else 
			{
				$whSql .= " AND ".$fieldNm." = '".$qData['ordSearchWord']."'";
			}
		}
		
		if (isset($qData['uNum']))
		{
			if ($qData['uNum'] > 0)
			{
				$whSql .= " AND a.USER_NUM = '".$qData['uNum']."'";
			}
		}

		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.' AS a');
		$this->db->join($this->_ordPartTbl.' AS b', 'a.NUM = b.ORDERS_NUM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			AES_DECRYPT(UNHEX(ORDER_EMAIL), '".$this->_encKey."') AS ORDER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(ORDER_MOBILE), '".$this->_encKey."') AS ORDER_MOBILE_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_MOBILE), '".$this->_encKey."') AS RECIPIENT_MOBILE_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ZIP), '".$this->_encKey."') AS RECIPIENT_ZIP_DEC,				
			AES_DECRYPT(UNHEX(RECIPIENT_ADDR1), '".$this->_encKey."') AS RECIPIENT_ADDR1_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ADDR2), '".$this->_encKey."') AS RECIPIENT_ADDR2_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ADDR_JIBUN), '".$this->_encKey."') AS RECIPIENT_ADDR_JIBUN_DEC,				
			(SELECT TITLE FROM CODE WHERE NUM = a.PAYCODE_NUM) AS PAYCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = a.PAYRESULT_BANKCODE_NUM) AS BANKCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = b.ORDSTATECODE_NUM) AS ORDSTATECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = b.DELIVERYCODE_NUM) AS DELIVERYCODE_TITLE,
			b.NUM AS ORDERPART_NUM,
			b.ORDSTATECODE_NUM,
			b.DELIVERYCODE_NUM,
			b.DELIVERY_PRICE,
			b.DELIVERY_DATE,
			b.PART_PRICE,
			b.PART_AMOUNT,
			b.PARTITEM_COUNT,
			b.INVOICE_NO,
			b.INVOICE_WRITE_DATE,
			b.PART_ORDER,
			b.CANCEL_YN, 
			b.CANCEL_DATE, 
			b.CANCEL_END_DATE, 
			b.CANCEL_REJECT_DATE,
			b.EXCHANGE_YN, 
			b.EXCHANGE_DATE, 
			b.EXCHANGE_END_DATE, 
			b.EXCHANGE_REJECT_DATE,
			b.REFUND_YN, 
			b.REFUND_DATE, 
			b.REFUND_END_DATE, 
			b.REFUND_REJECT_DATE,
			b.RETURN_YN, 
			b.RETURN_DATE, 
			b.RETURN_END_DATE, 
			b.RETURN_REJECT_DATE,				
			b.DELIVERY_END_DATE,
			b.EXCHGITEM_DELIVERY_DATE, 
			b.EXCHGITEM_DELIVERY_END_DATE, 
			b.EXCHGITEM_INVOICE_NO, 
			b.EXCHGITEM_INVOICE_WRITE_DATE,
			b.EXCHGITEM_DELIVERYCODE_NUM,
			b.SHOP_NUM,
			b.CHECK_YN,
			b.CHECK_DATE,
			c.USER_NUM AS SHOPUSER_NUM,
			c.SHOP_NAME,
			c.SHOPUSER_NAME,
			c.SHOP_CODE,
			AES_DECRYPT(UNHEX(c.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
			AES_DECRYPT(UNHEX(c.SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = 
						(
							SELECT SHOPITEM_NUM FROM ORDERITEM
							WHERE ORDERPART_NUM = b.NUM 
							AND DEL_YN = 'N' 
							ORDER BY NUM LIMIT 1
				) 
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS FIRST_FILE_INFO,						
			(
				SELECT CONCAT(NUM, '|', ITEM_NAME, '|', ITEM_CODE) 
				FROM SHOPITEM
				WHERE NUM = 
						(
							SELECT SHOPITEM_NUM FROM ORDERITEM
							WHERE ORDERPART_NUM = b.NUM
							AND DEL_YN = 'N'
							ORDER BY NUM LIMIT 1
				)
			) AS FIRST_ITEM_INFO
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join($this->_ordPartTbl.' AS b', 'a.NUM = b.ORDERS_NUM');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM');
		$this->db->where($whSql);
		$this->db->order_by('a.NUM', 'DESC');
		$this->db->order_by('b.PART_ORDER', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		$this->db->last_query();
		
		return $result;
	}
	
	/**
	 * @method name : getOrderViewDataList
	 * 주문고유번호별 상세 구매정보 List
	 * ITEM수만큼 return
	 * 
	 * @param unknown $qData
	 */
	public function getOrderViewDataList($qData)
	{
		$whSql = "1 = 1";
		$ShopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
		if (isset($qData['ordNum']))
		{
			if ($qData['ordNum'] > 0)
			{
				$whSql .= ' AND a.NUM = '.$qData['ordNum'];		
			}
		}		
		$whSql .= (!$qData['isDelView']) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= " AND b.DEL_YN = 'N'";
		$whSql .= " AND c.DEL_YN = 'N'";
		$whSql .= (!empty($qData['sNum'])) ? " AND b.SHOP_NUM = ".$qData['sNum'] : '';
		if (isset($qData['ordPartNum']))
		{
			if ($qData['ordPartNum'] > 0)
			{
				$whSql .= ' AND b.NUM = '.$qData['ordPartNum'];				
			}
		}
		
		$this->db->select("
			a.*,
			AES_DECRYPT(UNHEX(ORDER_EMAIL), '".$this->_encKey."') AS ORDER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(ORDER_MOBILE), '".$this->_encKey."') AS ORDER_MOBILE_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_MOBILE), '".$this->_encKey."') AS RECIPIENT_MOBILE_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ZIP), '".$this->_encKey."') AS RECIPIENT_ZIP_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ADDR1), '".$this->_encKey."') AS RECIPIENT_ADDR1_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ADDR2), '".$this->_encKey."') AS RECIPIENT_ADDR2_DEC,
			AES_DECRYPT(UNHEX(RECIPIENT_ADDR_JIBUN), '".$this->_encKey."') AS RECIPIENT_ADDR_JIBUN_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = a.PAYCODE_NUM) AS PAYCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = a.PAYRESULT_BANKCODE_NUM) AS BANKCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = b.REFBANKCODE_NUM) AS REFBANKCODE_TITLE,				
			(SELECT TITLE FROM CODE WHERE NUM = b.ORDSTATECODE_NUM) AS ORDSTATECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = b.DELIVERYCODE_NUM) AS DELIVERYCODE_TITLE,
			b.ORDERS_NUM,
			b.ORDSTATECODE_NUM,
			b.SHOP_NUM,
			b.DELIVERY_PRICE,
			b.DELIVERY_DATE,
			b.PART_QUANTITY,
			b.PART_PRICE,
			b.PART_AMOUNT,
			b.PARTITEM_COUNT,
			b.INVOICE_NO,
			b.INVOICE_WRITE_DATE,
			b.PART_ORDER,
			b.CHECK_YN,
			b.CHECK_DATE,				
			b.ORDERPART_CONTENT,
			b.REFBANKACCOUNT_NAME,
			b.REFBANKACCOUNT,
			b.REFUND_END_DATE,
			b.EXCHANGE_YN, b.REFUND_YN, b.CANCEL_YN, b.RETURN_YN, 
			b.EXCHANGE_DATE, b.REFUND_DATE, b.CANCEL_DATE, b.RETURN_DATE,				
			(SELECT TITLE FROM CODE WHERE NUM = b.DELIVERYCODE_NUM) AS DELIVERYCODE_TITLE,
			c.NUM AS ORDERITEM_NUM,
			c.ORDERPART_NUM,
			c.SHOPITEM_NUM,
			c.ITEM_ORDER,
			c.QUANTITY,
			c.PRICE,
			c.AMOUNT,
			c.ITEMOPTION_PRICE,
			c.ORIGIN_PRICE,
			c.ITEM_CHARGE,
			c.PAY_CHARGE,
			c.TAX_CHARGE,
			(SELECT TITLE FROM CODE WHERE NUM = c.ORDSTATECODE_NUM) AS ORDITEM_ORDSTATECODE_TITLE,
			d.ITEM_NAME,
			d.ITEM_CODE,
			d.ITEMSHOP_CODE,
			d.ITEM_PRICE,
			d.PAYAFTER_CANCEL_YN,
			d.MADEAFTER_REFUND_YN,
			d.MADEAFTER_CHANGE_YN,
			d.REFPOLICYCODE_NUM,
			d.REFPOLICY_CONTENT,
			e.SHOP_CODE,
			e.SHOP_NAME,
			e.SHOPUSER_NAME,
			AES_DECRYPT(UNHEX(e.SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
			AES_DECRYPT(UNHEX(e.SHOP_TEL), '".$this->_encKey."') AS SHOP_TEL_DEC,
			AES_DECRYPT(UNHEX(e.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
			( 
				SELECT 
					CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
				AND TBL_NUM = b.SHOP_NUM
				AND DEL_YN = 'N'
				ORDER BY NUM LIMIT 1
			) AS PROFILE_FILE_INFO,					
			(
				SELECT 
					CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = c.SHOPITEM_NUM 
				AND DEL_YN = 'N' 
				ORDER BY NUM LIMIT 1
			) AS FIRST_FILE_INFO,					
			(
				SELECT
					GROUP_CONCAT(itOpt.ITEMOPTION SEPARATOR '-')
				FROM 
				(
					SELECT 
						at.ORDERITEM_NUM, 
						bt.NUM AS SHOPITEM_OPTION_SUB_NUM, bt.OPTSUB_TITLE, bt.OPTION_PRICE, bt.SOLDOUT_YN,
						CONCAT(ct.OPT_TITLE, '|', bt.NUM, '|', bt.OPTSUB_TITLE, '|', bt.OPTION_PRICE, '|', bt.SOLDOUT_YN) AS ITEMOPTION      
					FROM ORDERITEM_OPTION AS at INNER JOIN SHOPITEM_OPTION_SUB AS bt
					ON at.SHOPITEM_OPTION_SUB_NUM = bt.NUM INNER JOIN SHOPITEM_OPTION ct
					ON bt.SHOPITEM_OPTION_NUM = ct.NUM
					ORDER BY at.NUM
				) AS itOpt
				GROUP BY ORDERITEM_NUM
				HAVING itOpt.ORDERITEM_NUM = c.NUM				
			) AS ITEMOPTION_INFO
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join($this->_ordPartTbl.' AS b', 'a.NUM = b.ORDERS_NUM');
		$this->db->join($this->_ordItemTbl.' AS c', 'b.NUM = c.ORDERPART_NUM');
		$this->db->join('SHOPITEM AS d', 'c.SHOPITEM_NUM = d.NUM');
		$this->db->join('SHOP AS e', 'd.SHOP_NUM = e.NUM');
		$this->db->where($whSql);
		$this->db->order_by('a.NUM', 'DESC');
		$this->db->order_by('b.PART_ORDER', 'DESC');
		$this->db->order_by('c.ITEM_ORDER', 'DESC');
		$result = $this->db->get()->result_array();

		return $result;
	}
	
	/**
	 * @method name : getOrderBaseRowData
	 * ORDERS 의 내용
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getOrderBaseRowData($qData)
	{
		$whSql = '1 = 1';
		if (isset($qData['ordNum']))
		{
			$whSql .= ' AND NUM = '.$qData['ordNum'];
		}
		
		if (isset($qData['orderCode']))
		{
			$whSql .= " AND ORDER_CODE = '".$qData['orderCode']."'";
		}		
		
		if (isset($qData['tno']))
		{
			$whSql .= " AND PAYRESULT_ID = '".$qData['tno']."'";
		}		
		
		$this->db->select("
			a.*,
			(
				SELECT COUNT(*) FROM ORDERPART 
				WHERE ORDERS_NUM = a.NUM AND DEL_YN = 'N' AND DELIVERYCODE_NUM > 10000 
			) AS DELIVERY_CNT 
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->where($whSql);
		$result = $this->db->get()->row_array();

		return $result;
	}
	
	/**
	 * @method name : getOrderFinalRowData
	 * 주문완료시 보여줄 주문 data 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getOrderFinalRowData($qData)
	{
		$whSql = 'a.NUM = '.$qData['ordNum'];
		$whSql .= (!empty($qData['uNum'])) ? " AND a.USER_NUM = ".$qData['uNum'] : '';
		
		$this->db->select("
			a.ORDER_CODE, 
			a.USER_NUM, 
			a.TOT_QUANTITY, 
			a.TOT_PRICE, 
			a.TOTDELIVERY_PRICE, 
			a.TOTADD_PRICE, 
			a.TOTOPTION_PRICE, 
			a.TOT_AMOUNT, 
			a.TOTFINAL_AMOUNT, 
			a.TOTPART_COUNT, 
			a.TOTITEM_COUNT, 
			a.PAYCODE_NUM, 
			(SELECT TITLE FROM CODE WHERE NUM = a.PAYCODE_NUM) AS PAYCODENUM_TITLE,				
			a.PAY_DATE, 
			a.PAYAUTO_YN, 
			a.ORDER_NAME, 
			a.ORDER_MOBILE, 
			a.ORDER_EMAIL,
			AES_DECRYPT(UNHEX(a.ORDER_MOBILE), '".$this->_encKey."') AS ORDER_MOBILE_DEC,
			AES_DECRYPT(UNHEX(a.ORDER_EMAIL), '".$this->_encKey."') AS ORDER_EMAIL_DEC,
			a.ORDER_CONTENT, 
			a.RECIPIENT_NAME, 
			a.RECIPIENT_MOBILE, 
			a.RECIPIENT_ZIP, 
			a.RECIPIENT_ADDR1, 
			a.RECIPIENT_ADDR2, 
			a.RECIPIENT_ADDR_JIBUN, 
			AES_DECRYPT(UNHEX(a.RECIPIENT_MOBILE), '".$this->_encKey."') AS RECIPIENT_MOBILE_DEC,
			AES_DECRYPT(UNHEX(a.RECIPIENT_ZIP), '".$this->_encKey."') AS RECIPIENT_ZIP_DEC,				
			AES_DECRYPT(UNHEX(a.RECIPIENT_ADDR1), '".$this->_encKey."') AS RECIPIENT_ADDR1_DEC,
			AES_DECRYPT(UNHEX(a.RECIPIENT_ADDR2), '".$this->_encKey."') AS RECIPIENT_ADDR2_DEC,
			AES_DECRYPT(UNHEX(a.RECIPIENT_ADDR_JIBUN), '".$this->_encKey."') AS RECIPIENT_ADDR_JIBUN_DEC,				
			a.PAYRESULT_ID, 
			a.PAYRESULT_BANKCODE_NUM, 
			(SELECT TITLE FROM CODE WHERE NUM = a.PAYRESULT_BANKCODE_NUM) AS PAYRESULT_BANKCODENUM_TITLE,
			a.PAYRESULT_BANKACCOUNT, 
			a.PAYRESULT_BANKACCOUNT_NAME, 
			a.PAYRESULT_CODE, 
			a.PAYRESULT_CODENAME, 
			a.TMP_ORDER_CODE, 
			a.CREATE_DATE,
			(
				SELECT 
					CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = (
					SELECT SHOPITEM_NUM FROM ORDERITEM
					WHERE ORDERPART_NUM = (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM = ".$qData['ordNum']."
						ORDER BY NUM ASC LIMIT 1
					)
					ORDER BY NUM ASC LIMIT 1
				)
				AND DEL_YN = 'N' 
				ORDER BY NUM LIMIT 1
			) AS FIRST_FILE_INFO,
			(
				SELECT 
					CONCAT(NUM, '|', ITEM_NAME, '|', ITEM_CODE, '|', SHOP_NUM) 
				FROM SHOPITEM
				WHERE NUM = (
					SELECT SHOPITEM_NUM FROM ORDERITEM
					WHERE ORDERPART_NUM = (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM = ".$qData['ordNum']."
						ORDER BY NUM ASC LIMIT 1
					)
					ORDER BY NUM ASC LIMIT 1
				)
			) AS FIRST_ITEM_INFO				
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->where($whSql);
		$result = $this->db->get()->row_array();

		return $result;
	}
	
	/**
	 * @method name : getOrderStateStatsDataList
	 * 주문현황(주문상태별 각각의 통계 수치) 
	 * 
	 * @param unknown $qData
	 */
	public function getOrderStateStatsDataList($qData)
	{
		$whSql = "1 = 1";
		$toDate = date("Ymd", mktime(0,0,0,date("m"), date("d"), date("Y")));		
		if ($qData['statsSearchKey'] == 'today' )
		{
			$statsTbl = (!empty($qData['sNum']) && $qData['sNum'] > 0) ? 'STATS_ORDSTATE_SHOP_DAY' : 'STATS_ORDSTATE_DAY';
			$whSql .= " AND STATS_DATE = ".$toDate;
		}
		else if ($qData['statsSearchKey'] == 'toweek' )
		{
			$statsTbl = (!empty($qData['sNum']) && $qData['sNum'] > 0) ? 'STATS_ORDSTATE_SHOP_WEEK' : 'STATS_ORDSTATE_WEEK';
			$toWeek = date('oW', strtotime($toDate));
			$whSql .= " AND STATS_DATE = ".$toWeek;
		}		
		else if ($qData['statsSearchKey'] == 'tomonth' )
		{
			$statsTbl = (!empty($qData['sNum']) && $qData['sNum'] > 0) ? 'STATS_ORDSTATE_SHOP_MONTH' : 'STATS_ORDSTATE_MONTH';			
			$toMonth = date('Ym', strtotime($toDate));
			$whSql .= " AND STATS_DATE = ".$toMonth;
		}		
		else if ($qData['statsSearchKey'] == 'toyear' )
		{
			$statsTbl = (!empty($qData['sNum']) && $qData['sNum'] > 0) ? 'STATS_ORDSTATE_SHOP_YEAR' : 'STATS_ORDSTATE_YEAR';			
			$toYear = date('Y', strtotime($toDate));
			$whSql .= " AND STATS_DATE = ".$toYear;
		}
		else if ($qData['statsSearchKey'] == 'term' )
		{
			//기간인 경우 기간 조회후 
			//컬럼별 SUM 한후 바로 return
			if (!empty($qData['sNum']) && $qData['sNum'] > 0)
			{
				$statsTbl = 'STATS_ORDSTATE_SHOP_DAY';
			}
			else
			{
				$statsTbl = 'STATS_ORDSTATE_DAY';				
			}
			
			$whSql .= " AND STATS_DATE >= ".str_replace('-', '', $qData['sDate']);
			$whSql .= " AND STATS_DATE <= ".str_replace('-', '', $qData['eDate']);			
			
			$ordCodeGrpList  = $this->common->getCodeListByGroup('ORDSTATE');
			$payCodeGrpList = $this->common->getCodeListByGroup('ORDPAY');
			$selCol = '1 ';
			foreach ($ordCodeGrpList  as $rs): //합산(SUM)컬럼 생성
				$selCol .= ', SUM(STATE_'.$rs['NUM'].') AS STATE_'.$rs['NUM'];
				if ($rs['NUM'] == 5080)
				{
					//입금확인시 결제수단 구분을 위해 컬럼 추가
					foreach ($payCodeGrpList as $prs):
						$selCol .= ', SUM(STATE_'.$rs['NUM'].'_'.$prs['NUM'].') AS STATE_'.$rs['NUM'].'_'.$prs['NUM'];
					endforeach;					
				}
			endforeach;
			
			$sql = "
				SELECT ".$selCol."
				FROM 
				(
					SELECT * FROM ".$statsTbl." WHERE ".$whSql."
				) stTb
			";
			$result = $this->db->query($sql)->row_array();

			return $result;
		}		
		else 
		{
			//통합통계
			$statsTbl = (!empty($qData['sNum']) && $qData['sNum'] > 0) ? 'STATS_ORDSTATE_SHOP' : 'STATS_ORDSTATE';			
		}

		$this->db->from($statsTbl);
		$this->db->where($whSql);
		$result = $this->db->get()->row_array();
		
		return $result;
	}
	
	/**
	 * @method name : getOrderStateRowData
	 * 주문아이템의 상태 조회(1건) 
	 * 
	 * @param unknown $ordPtNum
	 * @return boolean
	 */
	public function getOrderStateRowData($ordPtNum)
	{
		$this->db->select('ORDSTATECODE_NUM AS ORDSTATE');
		$this->db->from($this->_ordPartTbl);
		$this->db->where("NUM = ".$ordPtNum);

		return $this->db->get()->row()->ORDSTATE;
	}
	
	/**
	 * @method name : setOrderStateDataChange
	 * 주문상태변경 
	 * 
	 * @param unknown $orderState
	 * @param unknown $selValue
	 * @param unknown $insHisData
	 * @return number
	 */
	public function setOrderStateDataChange($orderState, $selValue, $insHisData)
	{
		$result = 0;
		$itemStateCodeNum = 0;
		$selValue = explode(',', $selValue);
 
		if (is_array($selValue))
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			$tmpOrdPartNum = 0;
			$hisContent = $insHisData['REASON_CONTENT'];
			unset($insHisData['REASON_CONTENT']);
			foreach ($selValue as $val)
			{
				//$val = [ORDERPART고유번호|아이템고유번호]
				$arrVal = explode('|', $val);
				$ordPartNum = (count($arrVal) > 0) ? $arrVal[0] : $val; 
				$currentOrdState = $this->getOrderStateRowData($ordPartNum);
				$currentOrdStateTitle = $this->common->getCodeTitleByCodeNum($currentOrdState);
				
				//if ($currentOrdState !== $orderState)
				//{
					if ($ordPartNum != $tmpOrdPartNum) //ORDERPART_NUM 중복제거
					{
						$whSql = 'NUM = '.$ordPartNum;
						$upData = array(
							'ORDSTATECODE_NUM' => $orderState,
							'ORDSTATE_UPDATE_DATE' => date('Y-m-d H:i:s'),
							'UPDATE_DATE' => date('Y-m-d H:i:s')
						);
						
						if ($orderState == 5070) //입금확인 처리한 경우(카드결제, 가상계좌가 아닌경우만)
						{
							$this->db->set('PAY_DATE', date('Y-m-d H:i:s'));
							$this->db->set('PAYAUTO_YN', 'N');
							$this->db->where(
								'NUM', 
								"(
									SELECT ORDERS_NUM FROM ".$this->_ordPartTbl." 
									WHERE NUM = ".$ordPartNum."
								)", 
								FALSE
							);
							$this->db->where('PAYCODE_NUM NOT IN (5510, 5530)');
							$this->db->update($this->tbl);
						}
						else if ($orderState == 5110) //취소신청
						{
							$whSql .= ' AND CANCEL_DATE IS NULL'; //이미 입력되어 있는 경우 제외
							$upDate['CANCEL_DATE'] = date('Y-m-d H:i:s');
						}
						else if ($orderState == 5115) //취소불가
						{
							$whSql .= ' AND CANCEL_REJECT_DATE IS NULL';
							$upDate['CANCEL_REJECT_DATE'] = date('Y-m-d H:i:s');
						}
						else if ($orderState == 5120) //취소완료
						{
							$whSql .= ' AND CANCEL_END_DATE IS NULL';
							$upDate['CANCEL_END_DATE'] = date('Y-m-d H:i:s');
							$upDate['CANCEL_YN'] = 'Y';							
						}
						else if ($orderState == 5130) //환불신청
						{
							$whSql .= ' AND REFUND_DATE IS NULL';
							$upDate['REFUND_DATE'] = date('Y-m-d H:i:s');
						}						
						else if ($orderState == 5135) //환불불가
						{
							$whSql .= ' AND REFUND_REJECT_DATE IS NULL';
							$upDate['REFUND_REJECT_DATE'] = date('Y-m-d H:i:s');
						}						
						else if ($orderState == 5150) //환불완료(승인)
						{
							$whSql .= ' AND REFUND_END_DATE IS NULL';
							$upDate['REFUND_END_DATE'] = date('Y-m-d H:i:s');
							$upDate['REFUND_YN'] = 'Y';
						}
						else if ($orderState == 5160) //반품신청
						{
							$whSql .= ' AND RETURN_DATE IS NULL';
							$upDate['RETURN_DATE'] = date('Y-m-d H:i:s');
						}
						else if ($orderState == 5165) //반품불가
						{
							$whSql .= ' AND RETURN_REJECT_DATE IS NULL';
							$upDate['RETURN_REJECT_DATE'] = date('Y-m-d H:i:s');
						}
						else if ($orderState == 5180) //반품완료(승인)
						{
							$whSql .= ' AND RETURN_END_DATE IS NULL';
							$upDate['RETURN_END_DATE'] = date('Y-m-d H:i:s');
							$upDate['RETURN_YN'] = 'Y';
						}
						else if ($orderState == 5190) //교환신청
						{
							$whSql .= ' AND EXCHANGE_DATE IS NULL';
							$upDate['EXCHANGE_DATE'] = date('Y-m-d H:i:s');
							$insHisData['SHOPITEM_NUM'] = $arrVal[1];
							$ordPtUpData['EXCHANGE_SHOPITEM_NUM'] = $arrVal[1];
						}
						else if ($orderState == 5195) //교환불가
						{
							$whSql .= ' AND EXCHANGE_REJECT_DATE IS NULL';
							$upDate['EXCHANGE_REJECT_DATE'] = date('Y-m-d H:i:s');
						}
						else if ($orderState == 5210) //교환완료(승인)
						{
							$whSql .= ' AND EXCHANGE_END_DATE IS NULL';
							$upDate['EXCHANGE_END_DATE'] = date('Y-m-d H:i:s');
							$upDate['EXCHANGE_YN'] = 'Y';
						}		
						else if ($orderState == 5220) //배송중
						{
							//$whSql .= ' AND DELIVERY_DATE IS NULL';
							$upDate['DELIVERY_DATE'] = date('Y-m-d H:i:s');
						}
						else if ($orderState == 5230) //배송완료
						{
							//$whSql .= ' AND DELIVERY_END_DATE IS NULL';
							$upDate['DELIVERY_END_DATE'] = date('Y-m-d H:i:s');
						}	
						
						if ($orderState == 5050)
						{
							$currentState = $this->getOrderStateRowData($ordPartNum);
							
							$isCheckUpdate = TRUE;
							if ($currentState < 5060)
							{
								//현주문상태가 주문확인 이하면 상태값 변경
								$this->db->where($whSql);
								$this->db->update($this->_ordPartTbl, $upData);
								$result = $this->db->affected_rows();
								
								if ($currentState == 5050) $isCheckUpdate = FALSE; //원래상태가 주문확인인 경우 update하지 않는다 
							}
							
							if ($isCheckUpdate)
							{
								//주문내역에 직접 상태를 변경시키지 않고 주문확인 체크여부 update
								$this->db->set('CHECK_YN', 'Y');
								$this->db->set('CHECK_DATE', date('Y-m-d H:i:s'));
								$this->db->where('NUM', $ordPartNum);
								$this->db->where('CHECK_YN', 'N');
								$this->db->update($this->_ordPartTbl);
								$result = $this->db->affected_rows();								
							}
						}
						else 
						{
							$this->db->where($whSql);
							$this->db->update($this->_ordPartTbl, $upData);
							$result = $this->db->affected_rows();
						}
						
						//취소완료, 환불완료, 반품완료, 교환완료 시 재고환원
						if (in_array($orderState, array(5120, 5150, 5180, 5210)))
						{
							$this->setOrderPartStockUpdate($ordPartNum, '+', TRUE);
						}
							
						//히스토리 처리
						$content = '주문상태를 '.$currentOrdStateTitle.' 에서 '.$hisContent;
						$insHisData['REASON_CONTENT'] = $content;
						$insHisData['ORDERPART_NUM'] = $ordPartNum;
						$this->db->insert($this->_ordHisTbl, $insHisData);
						$hisNum = $this->db->insert_id();
						
						//마지막 히스토리 번호 update
						$ordPtUpData['LASTHISTORY_NUM'] = $hisNum;
						$this->db->where('NUM', $ordPartNum);
						$this->db->update($this->_ordPartTbl, $ordPtUpData);
					}
				//}
				
				$tmpOrdPartNum = $ordPartNum;
			}
				
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
			
		return $result;					
	}
	
	/**
	 * @method name : setOrderInfoUpdate
	 * 주문상세 정보 update 
	 * 
	 * @param unknown $ordNum
	 * @param unknown $upData
	 * @return Ambiguous
	 */
	public function setOrderInfoUpdate($ordNum, $upData)
	{
		$this->db->where('NUM', $ordNum);
		$this->db->update($this->tbl, $upData);
		$result = $this->db->affected_rows();
		
		return $result;
	}
	
	/**
	 * @method name : getOrderHistoryDataList
	 * 주문히스토리 내역 
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getOrderHistoryDataList($qData, $isDelView)
	{
		$whSql = "b.DEL_YN = 'N'";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['ordStateCodeNum']) > 0) ? " AND b.ORDSTATECODE_NUM = ".$qData['ordStateCodeNum'] : '';
		$whSql .= (!empty($qData['sNum'])) ? " AND b.SHOP_NUM = ".$qData['sNum'] : '';
		if (!empty($qData['ordNum']) && !empty($qData['ordPtNum']))
		{
			//ORDERS_NUM, ORDERPART_NUM 모두 값이 있는 경우
			$whSql .= " AND a.NUM = ".$qData['ordNum']." AND b.NUM = ".$qData['ordPtNum'];
		}
		else 
		{
			if (!empty($qData['ordNum']))
			{
				//주문단위로 보고자 하는 경우
				$whSql .= " AND a.NUM = ".$qData['ordNum'];
			}
			else if (!empty($qData['ordPtNum']))
			{
				//ORDERPART_NUM 이 있는 경우(샵단위로 보고자 하는 경우)
				$whSql .= " AND b.NUM = ".$qData['ordPtNum'];
			}			
		}
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl.' AS a');
		$this->db->join($this->_ordPartTbl.' AS b', 'a.NUM = b.ORDERS_NUM');		
		$this->db->join($this->_ordHisTbl.' AS c', 'b.NUM = c.ORDERPART_NUM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
  			c.*,
			(SELECT USER_NAME FROM USER WHERE NUM = c.REASONUSER_NUM) AS RESONUSER_NAME,
			(SELECT USER_NAME FROM USER WHERE NUM = c.ANSWERUSER_NUM) AS ANSWERUSER_NAME,
			(SELECT TITLE FROM CODE WHERE NUM = c.ORDSTATECODE_NUM) AS ORDSTATECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = c.RECISIONCODE_NUM) AS RECISIONCODE_TITLE
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join($this->_ordPartTbl.' AS b', 'a.NUM = b.ORDERS_NUM');		
		$this->db->join($this->_ordHisTbl.' AS c', 'b.NUM = c.ORDERPART_NUM');
		$this->db->where($whSql);
		$this->db->order_by('c.NUM', 'DESC');
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;		
	}
	
	/**
	 * @method name : getCancelResonRowData
	 * 히스토리의 가장 최근 사유등록 내용 
	 * orderstate별
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getCancelResonRowData($qData, $isDelView)
	{
		$whSql = "ORDERPART_NUM = ".$qData['ordPtNum'];
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : "";
		$whSql .= " AND ORDSTATECODE_NUM = ".$qData['orderState'];
		$whSql .= " AND REASON_AUTOWRITE_YN = 'N'";
		
		$this->db->select("
			*,
			(SELECT USER_NAME FROM USER WHERE NUM = REASONUSER_NUM) AS RESONUSER_NAME,
			(SELECT USER_NAME FROM USER WHERE NUM = ANSWERUSER_NUM) AS ANSWERUSER_NAME,				
			(SELECT TITLE FROM CODE WHERE NUM = RECISIONCODE_NUM) AS RECISIONCODE_TITLE
		");
		$this->db->limit(1);
		$this->db->from($this->_ordHisTbl);
		$this->db->where($whSql);
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		
		return $result;
	}
	
	/**
	 * @method name : setCancelReasonInsert
	 * 히스토리에 사유 등록 
	 * 
	 * @param unknown $ordNum
	 * @param unknown $ordPtNum
	 * @param unknown $insData
	 * @return Ambiguous
	 */
	public function setCancelReasonInsert($ordNum, $ordPtNum, $insData)
	{
		$this->db->insert($this->_ordHisTbl, $insData);
		$hisNum = $this->db->insert_id();
		
		return $hisNum;
	}
	
	/**
	 * @method name : setCancelDenyUpdate
	 * 불가사유 등록
	 * 등록후 신청상태에서 불가상태로 변경 
	 * 
	 * @param unknown $hisNumOrg
	 * @param unknown $ordNum
	 * @param unknown $ordPtNum
	 * @param unknown $upData
	 * @param unknown $upPtData
	 * @param unknown $insHisData
	 * @return Ambiguous
	 */
	public function setCancelDenyUpdate($hisNumOrg, $ordNum, $ordPtNum, $upData, $upPtData, $insHisData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		//답변 내용 업데이트
		$this->db->where('NUM', $hisNumOrg);
		$this->db->update($this->_ordHisTbl, $upData);
		$result = $this->db->affected_rows();
				
		//ORDERPART update
		$this->db->where('NUM', $ordPtNum);
		$this->db->update($this->_ordPartTbl, $upPtData);
			
		//히스토리 처리
		$this->db->insert($this->_ordHisTbl, $insHisData);
		$hisNum = $this->db->insert_id();
		
		//마지막 히스토리 번호 update
		$this->db->set('LASTHISTORY_NUM', $hisNum);
		$this->db->where('NUM', $ordPtNum);
		$this->db->update($this->_ordPartTbl);		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;
	}
	
	/**
	 * @method name : setDeliveryInfoInsert
	 * 택배정보 입력(1건)
	 * 
	 * @param unknown $ordNum
	 * @param unknown $ordPtNum
	 * @param unknown $upData
	 */
	public function setDeliveryInfoInsert($ordNum, $ordPtNum, $upData)
	{
		$this->db->where('NUM', $ordPtNum);
		$this->db->update($this->_ordPartTbl, $upData);
		return $this->db->affected_rows();
	}
	
	/**
	 * @method name : getCartDataList
	 * 카트 목록 
	 * 
	 * @param unknown $qData
	 * @param string $isDelView
	 * @return Ambiguous
	 */
	public function getCartDataList($qData, $isDelView = FALSE)
	{
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		if (!empty($qData['uNum']))
		{
			$whSql .= " AND a.USER_NUM = ".$qData['uNum'];
		}
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->_cartTbl.' AS a');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		$this->db->select("
  			a.*,
			b.SHOP_NAME,
			b.SHOP_CODE,
			c.REFDELIVERYCODE_NUM,
			c.DELIVPOLICYCODE_NUM,
			c.DELIVERY_PRICE
		");
		$this->db->from($this->_cartTbl.' AS a');
		$this->db->join('SHOP AS b', 'a.SHOP_NUM = b.NUM');
		$this->db->join('SHOP_POLICY AS c', 'b.NUM = c.SHOP_NUM', 'left outer');
		$this->db->where($whSql);
		$this->db->order_by('a.SHOP_NUM', 'ASC');
		$rowData = $this->db->get()->result_array();
		for($i=0; $i<count($rowData); $i++) //장바구니 아이템 정보
		{
			$this->db->select("
	  			a.*,
				(
					SELECT
						CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
					FROM SHOPITEM_FILE
					WHERE SHOPITEM_NUM = a.SHOPITEM_NUM
					AND DEL_YN = 'N'
					ORDER BY NUM LIMIT 1
				) AS FIRST_FILE_INFO,
				b.ITEM_NAME,
				b.ITEM_CODE,
				b.ITEM_PRICE,
				b.DISCOUNT_YN,
				b.DISCOUNT_PRICE,
				b.STOCKFREE_YN,
				b.STOCK_COUNT,
				b.ITEMSTATECODE_NUM,
				b.MAXBUY_COUNT,
				b.OPTION_YN,
				(
					SELECT
						GROUP_CONCAT(itOpt.ITEMOPTION SEPARATOR '-')
					FROM 
					(
						SELECT 
							at.CARTITEM_NUM, 
							bt.NUM AS SHOPITEM_OPTION_SUB_NUM, bt.OPTSUB_TITLE, bt.OPTION_PRICE, bt.SOLDOUT_YN,
							CONCAT(ct.OPT_TITLE, '|', bt.NUM, '|', bt.OPTSUB_TITLE, '|', bt.OPTION_PRICE, '|', bt.SOLDOUT_YN) AS ITEMOPTION      
						FROM CARTITEM_OPTION AS at INNER JOIN SHOPITEM_OPTION_SUB AS bt
						ON at.SHOPITEM_OPTION_SUB_NUM = bt.NUM INNER JOIN SHOPITEM_OPTION ct
						ON bt.SHOPITEM_OPTION_NUM = ct.NUM
						ORDER BY at.NUM
					) AS itOpt
					GROUP BY CARTITEM_NUM
					HAVING itOpt.CARTITEM_NUM = a.NUM				
				) AS ITEMOPTION_INFO					
			");
			$this->db->from($this->_cartTbl.'ITEM AS a');
			$this->db->join('SHOPITEM AS b', 'a.SHOPITEM_NUM = b.NUM');
			$this->db->where('a.DEL_YN', 'N');
			$this->db->where('a.CART_NUM', $rowData[$i]['NUM']);
			$this->db->where('b.SHOP_NUM', $rowData[$i]['SHOP_NUM']);
			$this->db->order_by('a.NUM', 'ASC');
			$rowSubData = $this->db->get()->result_array();
			if ($rowSubData)
			{
				$rowData[$i]['cartItemSet'] = $rowSubData;
			}
			else 
			{
				$rowData[$i]['cartItemSet'] = array();
			}
		}

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;

		return $result;		
	}
	
	/**
	 * @method name : getCartOrderDataList
	 * 카트에서 주문전환된 내용
	 * 
	 * @param unknown $uNum
	 * @return Ambiguous
	 */
	public function getCartOrderDataList($uNum)
	{
		$whSql = "a.DEL_YN = 'N'";
		if (!empty($uNum))
		{
			$whSql .= " AND a.USER_NUM = ".$uNum;
		}
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('CARTITEM_ORDER AS a');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		$sql = "
			SELECT 
				DISTINCT
				a.ORDER_CONTENT,
				a.CART_NUM,
	  			b.SHOP_NUM,
				b.USER_NUM,
				c.SHOP_NAME,
				c.SHOP_CODE,
				d.REFDELIVERYCODE_NUM,
				d.DELIVPOLICYCODE_NUM,
				d.DELIVERY_PRICE,
				(SELECT COUNT(*) FROM CARTITEM WHERE DEL_YN = 'N' AND CART_NUM = a.CART_NUM) AS ITEM_CNT
			FROM CARTITEM_ORDER AS a INNER JOIN ".$this->_cartTbl." AS b
			ON a.CART_NUM = b.NUM INNER JOIN SHOP AS c
			ON b.SHOP_NUM = c.NUM LEFT OUTER JOIN SHOP_POLICY AS d
			ON c.NUM = d.SHOP_NUM
			WHERE ".$whSql."
			ORDER BY b.SHOP_NUM ASC
		";
		$rowData = $this->db->query($sql)->result_array();
		for($i=0; $i<count($rowData); $i++) //장바구니 아이템 정보
		{
			$this->db->select("
				a.NUM AS CARTORD_NUM,
				a.QUANTITY,
				b.NUM AS CARTITEM_NUM,
				b.CART_NUM,
				b.SHOPITEM_NUM,
				b.QUANTITY AS CART_QUANTITY,
				(
					SELECT
						CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
					FROM SHOPITEM_FILE
					WHERE SHOPITEM_NUM = b.SHOPITEM_NUM
					AND DEL_YN = 'N'
					ORDER BY NUM LIMIT 1
				) AS FIRST_FILE_INFO,
				c.ITEM_NAME,
				c.ITEM_CODE,
				c.ITEM_PRICE,
				c.DISCOUNT_YN,
				c.DISCOUNT_PRICE,
				c.STOCKFREE_YN,
				c.STOCK_COUNT,
				c.ITEMSTATECODE_NUM,
				c.MAXBUY_COUNT,
				c.OPTION_YN,
				c.CHARGE_TYPE,
				c.ITEM_CHARGE,
				c.PAY_CHARGE,
				c.TAX_CHARGE,
				(
					SELECT
						GROUP_CONCAT(itOpt.ITEMOPTION SEPARATOR '-')
					FROM 
					(
						SELECT 
							at.CARTITEM_NUM, 
							bt.NUM AS SHOPITEM_OPTION_SUB_NUM, bt.OPTSUB_TITLE, bt.OPTION_PRICE, bt.SOLDOUT_YN,
							CONCAT(ct.OPT_TITLE, '|', bt.NUM, '|', bt.OPTSUB_TITLE, '|', bt.OPTION_PRICE, '|', bt.SOLDOUT_YN) AS ITEMOPTION      
						FROM CARTITEM_OPTION AS at INNER JOIN SHOPITEM_OPTION_SUB AS bt
						ON at.SHOPITEM_OPTION_SUB_NUM = bt.NUM INNER JOIN SHOPITEM_OPTION ct
						ON bt.SHOPITEM_OPTION_NUM = ct.NUM
						ORDER BY at.NUM
					) AS itOpt
					GROUP BY CARTITEM_NUM
					HAVING itOpt.CARTITEM_NUM = a.CARTITEM_NUM				
				) AS ITEMOPTION_INFO					
			");
			$this->db->from('CARTITEM_ORDER AS a');
			$this->db->join($this->_cartTbl.'ITEM AS b', 'a.CART_NUM = b.CART_NUM AND a.CARTITEM_NUM = b.NUM');
			$this->db->join('SHOPITEM AS c', 'b.SHOPITEM_NUM = c.NUM');
			$this->db->where($whSql);
			$this->db->where('a.CART_NUM', $rowData[$i]['CART_NUM']);
			$this->db->where('c.SHOP_NUM', $rowData[$i]['SHOP_NUM']);
			$this->db->order_by('b.NUM', 'ASC');
			$rowSubData = $this->db->get()->result_array();
			if ($rowSubData)
			{
				$rowData[$i]['cartItemSet'] = $rowSubData;
			}
		}
	
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
	
		return $result;
	}	
	
	/**
	 * @method name : setCartDataUpdate
	 * 카트내용 update 
	 * 
	 * @param unknown $ctData
	 */
	public function setCartDataUpdate($ctData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$i = 0;
		foreach ($ctData as $ct)
		{
			$checkYn = (isset($ct['checkyn'])) ? strtoupper($ct['checkyn']) : 'N';
			$this->db->set('DEL_YN', ($checkYn == 'Y') ? 'N' : 'Y');
			$this->db->where('NUM', $ct['no']);
			$this->db->update($this->_cartTbl);
			foreach ($ct['item'] as $cts)
			{
				$itemCheckYn = (isset($cts['checkyn'])) ? strtoupper($cts['checkyn']) : 'N';
				$this->db->set('DEL_YN', ($itemCheckYn == 'Y') ? 'N' : 'Y');
				$this->db->set('QUANTITY', $cts['quantity']);
				$this->db->where('NUM', $cts['no']);
				$this->db->update($this->_cartTbl.'ITEM');				
			}
			
			$i++;
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $i;
	}
	
	/**
	 * @method name : setCartDataDelete
	 * 카트 삭제 
	 * 
	 * @param unknown $cartNum
	 * @param unknown $cartItemNum
	 * @return Ambiguous
	 */
	public function setCartDataDelete($cartNum, $cartItemNum, $userNum)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('NUM', $cartItemNum);
		$this->db->where('CART_NUM', $cartNum);		
		$this->db->update($this->_cartTbl.'ITEM');
		$result = $this->db->affected_rows();
		
		$this->db->select("
			EXISTS (
				SELECT 1 FROM ".$this->_cartTbl."ITEM
				WHERE CART_NUM = ".$cartNum."
				AND DEL_YN = 'N'
			) AS RESULT
		");
		$isExist = $this->db->get()->row()->RESULT;
		if (!$isExist) //카트에 등록되어 있는 아이템이 없는 경우
		{
			$this->db->set('DEL_YN', 'Y');
			$this->db->where('NUM', $cartNum);
			$this->db->update($this->_cartTbl);
			$result = $this->db->affected_rows();
		}
		
		//USER 테이블에 현재 CART ITEM갯수 update
		$this->db->set(
			'CART_COUNT',
			"(
				SELECT COUNT(*) FROM CARTITEM
				WHERE DEL_YN = 'N'
				AND CART_NUM IN (
					SELECT NUM FROM CART WHERE DEL_YN = 'N' AND USER_NUM = ".$userNum."
				)
			)",
			FALSE
		);
		$this->db->where('NUM', $userNum);
		$this->db->update('USER');		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;
	}
	
	/**
	 * @method name : setCartToOrderDataUpdate
	 * 장바구니에서 선택된 내용 주문전환 
	 * 
	 * @param unknown $ctData
	 * @return number
	 */
	public function setCartToOrderDataUpdate($ctData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		//기존 주문전환 내용 삭제
		$uNum = $ctData['uNum'];
		unset($ctData['uNum']);
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('USER_NUM', $uNum);
		$this->db->update($this->_cartTbl.'ITEM_ORDER');
		
		$i = 0;
		foreach ($ctData as $ct)
		{
			$checkYn = (isset($ct['checkyn'])) ? strtoupper($ct['checkyn']) : 'N';
			if ($checkYn == 'Y')
			{
				foreach ($ct['item'] as $cts)
				{
					$itemCheckYn = (isset($cts['checkyn'])) ? strtoupper($cts['checkyn']) : 'N';
					if ($itemCheckYn == 'Y')
					{
						$insData = array(
							'USER_NUM' => $uNum,
							'CART_NUM' => $ct['no'],
							'CARTITEM_NUM' => $cts['no'],
							'QUANTITY' => $cts['quantity'],
							'ORDER_CONTENT' => $ct['content']
						);
						$this->db->insert($this->_cartTbl.'ITEM_ORDER', $insData);
					}
				}
			}
				
			$i++;
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $i;
	}
	
	/**
	 * @method name : setCartToOrderDirectDataUpdate
	 * 즉시구매시
	 * 카트에서 DIRECT_YN = 'Y' 인 내용 한건을 불러온다
	 * 주문내역으로 전환후 즉시주문건 카트는 즉시 삭제 
	 * 
	 * @param unknown $uNum
	 * @param unknown $ctNum
	 * @return number
	 */
	public function setCartToOrderDirectDataUpdate($uNum, $ctNum)
	{
		$result = 0;
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		//기존 주문전환 내용 삭제
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('USER_NUM', $uNum);
		$this->db->update($this->_cartTbl.'ITEM_ORDER');
		
		$this->db->select("*");
		$this->db->from($this->_cartTbl.'ITEM');
		$this->db->where('CART_NUM', $ctNum);
		$this->db->where('DEL_YN', 'N');
		$ctData = $this->db->get()->row_array();

		if ($ctData)
		{
			$insData = array(
				'USER_NUM' => $uNum,
				'CART_NUM' => $ctNum,
				'CARTITEM_NUM' => $ctData['NUM'],
				'QUANTITY' => $ctData['QUANTITY']
			);
			$this->db->insert($this->_cartTbl.'ITEM_ORDER', $insData);
			$result = $this->db->insert_id();
		}
		
		//주문단계 전환후 즉시구매 장바구니 바로 삭제
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('CART_NUM', $ctNum);
		$this->db->update($this->_cartTbl.'ITEM');
		
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('NUM', $ctNum);
		$this->db->update($this->_cartTbl);		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;
	}
	
	/**
	 * @method name : getRecentDeliveryInfoData
	 * 최근 배송지 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getRecentDeliveryInfoData($qData)
	{
		$whSql = "1 = 1";
		$whSql .= (!empty($qData['uNum'])) ? " AND a.USER_NUM = ".$qData['uNum'] : '';
		
		$sql = "
			SELECT *
			FROM (
				SELECT 
					DISTINCT 
					a.RECIPIENT_NAME,
					a.RECIPIENT_MOBILE,
					a.RECIPIENT_ZIP,
					a.RECIPIENT_ADDR1,
					a.RECIPIENT_ADDR2,
					a.RECIPIENT_ADDR_JIBUN,
					AES_DECRYPT(UNHEX(a.RECIPIENT_MOBILE), '".$this->_encKey."') AS RECIPIENT_MOBILE_DEC,
					AES_DECRYPT(UNHEX(a.RECIPIENT_ZIP), '".$this->_encKey."') AS RECIPIENT_ZIP_DEC,
					AES_DECRYPT(UNHEX(a.RECIPIENT_ADDR1), '".$this->_encKey."') AS RECIPIENT_ADDR1_DEC,
					AES_DECRYPT(UNHEX(a.RECIPIENT_ADDR2), '".$this->_encKey."') AS RECIPIENT_ADDR2_DEC,
					AES_DECRYPT(UNHEX(a.RECIPIENT_ADDR_JIBUN), '".$this->_encKey."') AS RECIPIENT_ADDR_JIBUN_DEC				
				FROM ".$this->tbl." AS a
				WHERE ".$whSql."
				ORDER BY a.NUM DESC LIMIT 3
			) tb
			WHERE RECIPIENT_ZIP_DEC <> ''
		";
		$result = $this->db->query($sql)->result_array();

		return $result; 
	}
	
	/**
	 * @method name : setChangePaymentUpdate
	 * 결제 정보 변경(환불, 취소건) 
	 * 취소의 경우만 재고 수량 환원함
	 * 환불도 재고수량 환원이 필요한 경우 취소의 내용 참조
	 * 
	 * @param unknown $type
	 * @param unknown $ordNum
	 * @param unknown $ordPtNum
	 * @param unknown $upData
	 * @return Ambiguous
	 */
	public function setChangePaymentUpdate($type, $ordNum, $ordPtNum, $upData, $insHisData)
	{
		$this->db->select("NUM, PART_AMOUNT, DELIVERY_PRICE");
		$this->db->from($this->_ordPartTbl);
		$this->db->where('ORDERS_NUM', $ordNum);
		$this->db->where('DEL_YN', 'N');
		$partData = $this->db->get()->result_array();
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		if (strpos($type, 'refund') !== FALSE)
		{
			//환불
			if ($ordPtNum == 0)
			{
				if ($partData)
				{
					unset($upData['REFUND_PRICE']);
					foreach ($partData as $rs)
					{
						$upData['REFUND_PRICE'] = $rs['PART_AMOUNT'] + $rs['DELIVERY_PRICE'];
						$this->db->where('NUM', $rs['NUM']);
						$this->db->update($this->_ordPartTbl, $upData);
						unset($upData['REFUND_PRICE']);
					}
				}
			}
			else if ($ordPtNum > 0)
			{
				$this->db->where('NUM', $ordPtNum);
				$this->db->update($this->_ordPartTbl, $upData);
			}

			$this->db->set(
				'TOTREFUND_AMOUNT',
				"(SELECT SUM(REFUND_PRICE) FROM ".$this->_ordPartTbl."
					WHERE ORDERS_NUM = ".$ordNum." AND DEL_YN = 'N')",
				FALSE
			);
			$this->db->where('NUM', $ordNum);
			$this->db->update($this->tbl);
		}
		else
		{
			//취소
			if ($ordPtNum == 0)
			{
				if ($partData)
				{
					unset($upData['CANCEL_PRICE']);
					foreach ($partData as $rs)
					{
						$upData['CANCEL_PRICE'] = $rs['PART_AMOUNT'] + $rs['DELIVERY_PRICE'];
						$this->db->where('NUM', $rs['NUM']);
						$this->db->update($this->_ordPartTbl, $upData);
						unset($upData['CANCEL_PRICE']);
						
						//재고 환원
						$this->setOrderPartStockUpdate($rs['NUM'], '+', TRUE);
					}
				}
			}
			else if ($ordPtNum > 0)
			{
				$this->db->where('NUM', $ordPtNum);
				$this->db->update($this->_ordPartTbl, $upData);
				
				//재고 환원
				$this->setOrderPartStockUpdate($ordPtNum, '+', TRUE);				
			}
			
			$this->db->set(
				'TOTCANCEL_AMOUNT',
				"(SELECT SUM(CANCEL_PRICE) FROM ".$this->_ordPartTbl."
					WHERE ORDERS_NUM = ".$ordNum." AND DEL_YN = 'N')",
				FALSE
			);
			$this->db->where('NUM', $ordNum);
			$this->db->update($this->tbl);
		}

		//주문 최종 금액
		$this->db->set(
			'TOTFINAL_AMOUNT',
			"(SELECT (SUM(PART_AMOUNT + DELIVERY_PRICE)) - (SUM(CANCEL_PRICE + REFUND_PRICE)) FROM ".$this->_ordPartTbl."
				WHERE ORDERS_NUM = ".$ordNum." AND DEL_YN = 'N')",
			FALSE
		);
		$this->db->where('NUM', $ordNum);
		$this->db->update($this->tbl);
		$result = $this->db->affected_rows();

		if ($partData)
		{
			foreach ($partData as $rs)
			{
				$insHisData['ORDERPART_NUM'] = $rs['NUM'];
				
				//히스토리 처리
				$this->db->insert($this->_ordHisTbl, $insHisData);
				$hisNum = $this->db->insert_id();
				
				//마지막 히스토리 번호 update
				$this->db->set('LASTHISTORY_NUM', $hisNum);
				$this->db->where('NUM', $ordPtNum);
				$this->db->update($this->_ordPartTbl);
								
				unset($insHisData['ORDERPART_NUM']);
			}
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $result;		
	}
	
	/**
	 * @method name : setOrderDataInsert
	 * 주문정보 insert 
	 * PART_ORDER, ITEM_ORDER 반드시 부여 ORDER는 0 이 아닌 1 부터
	 * 역순으로 부여(예 3개면 3,2,1)
	 * -- 관리자에서 리스트 표현(tr,td,rowspan)문제로 order가 중요함
	 * -- 주문내용과 동일한 순서유지를 하기위해 rowspan 문제와 더불어 order 역순 부여함
	 * 
	 * @param unknown $ordData
	 * @return Ambiguous
	 */
	public function setOrderDataInsert($ordData)
	{
		$orderAmount = $ordData['orderAmount']; //PG사를 통한 실결제금액
		unset($ordData['orderAmount']);
		
		//PG주문코드 일치를 위해 주석 처리
		//$ordData['ORDER_CODE'] = $ordData['TMP_ORDER_CODE']; //임시주문코드 다시 임시부여
		$coData = $this->getCartOrderDataList($ordData['USER_NUM']); //cartToOrder data list
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();

		$itemCnt = 0;
		$partCnt = count($coData['recordSet']);
		$i = $partCnt;
		$totAmount = 0; //전체 구매금액
		$totPrice = 0; //순수금액 합계
		$totQuantity = 0;
		$totOptionPrice = 0;
		$totShopAmount = 0; //샵별 합산 금액
		$totDeliveryPrice = 0; //전체 배송 금액
		$deliveryCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('DELIVERY', 'NONE');
		$ordStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('ORDSTATE', 'ORDER');
		
		//ORDERS insert
		$this->db->insert($this->tbl, $ordData);
		$resultOrdNum = $this->db->insert_id();

		foreach ($coData['recordSet'] as $rs)
		{
			$deliverPrice = $rs['DELIVERY_PRICE'];
			
			//ORDERPART insert
			$odpData = array(
				'ORDERS_NUM' => $resultOrdNum,
				'SHOP_NUM' => $rs['SHOP_NUM'],
				'PART_ORDER' => $i,
				'ORDSTATECODE_NUM' => $ordStateCodeNum,
				'ORDERPART_CONTENT' => $rs['ORDER_CONTENT'],
				'DELIVERY_PRICE' => $deliverPrice,
				'DELIVERYCODE_NUM' => $deliveryCodeNum,
				'EXCHGITEM_DELIVERYCODE_NUM' => $deliveryCodeNum,
				'REFPAYCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('ORDPAY', 'NONE'),
				'REFBANKCODE_NUM' => $this->common->getCodeNumByCodeGrpNCodeId('BANK', 'NONE')					
			);
			$this->db->insert('ORDERPART', $odpData);
			$resultOrdPartNum = $this->db->insert_id();			
			
			$partItemCnt = count($rs['cartItemSet']);
			$t = $partItemCnt;
			$shopAmount = 0; //샵별 합계총금액
			$shopPrice = 0; //샵별순수 합계금액
			$shopOptPrice = 0; //샵별옵션 합계금액
			$shopQuantity = 0;
			foreach ($rs['cartItemSet'] as $irs)
			{
				$price = ($irs['DISCOUNT_YN'] == 'Y') ? $irs['DISCOUNT_PRICE'] : $irs['ITEM_PRICE'];
				$quantity = $irs['QUANTITY']; //구매수량
				$arrOpt = (!empty($irs['ITEMOPTION_INFO'])) ? explode('-', $irs['ITEMOPTION_INFO']) : array();
				$optAmount = $optPrice = 0;
				$optTitle = '';
				foreach ($arrOpt as $ot) //옵션선택사항
				{
					$arrOptInfo = explode('|', $ot);
					$optTitle .= $arrOptInfo[0].':'.$arrOptInfo[2].'<br />';
					$optPrice = $optPrice + $arrOptInfo[3]; //옵션가격
					$optAmount = $optAmount + ($arrOptInfo[3] * $quantity);
				}				
				$amount = ($price * $quantity) + $optAmount;
				$shopPrice = $shopPrice + $price;
				$shopOptPrice = $shopOptPrice + $optPrice;
				$shopQuantity = $shopQuantity + $quantity;
				$totOptionPrice = $totOptionPrice + $optPrice;
				$totQuantity = $totQuantity + $quantity;
				$totPrice = $totPrice + $price;

				//ORDERITEM insert
				$odpiData = array(
					'ORDERPART_NUM' => $resultOrdPartNum,
					'SHOPITEM_NUM' => $irs['SHOPITEM_NUM'],
					'QUANTITY' => $quantity,
					'PRICE' =>$price,
					'AMOUNT' => $amount,
					'ITEMOPTION_PRICE' => $optPrice,
					'ITEMADD_PRICE' => $optPrice, //$optPrice + 기타비용						
					'ORIGIN_PRICE' => $irs['ITEM_PRICE'],
					'ORDSTATECODE_NUM' => $ordStateCodeNum,
					'ITEM_CHARGE' => $irs['ITEM_CHARGE'],
					'PAY_CHARGE' => $irs['PAY_CHARGE'],
					'TAX_CHARGE' => $irs['TAX_CHARGE'],
					'ITEM_ORDER' => $t
				);
				$this->db->insert('ORDERITEM', $odpiData);
				$resultOrdItemNum = $this->db->insert_id();
				
				foreach ($arrOpt as $ot) //옵션선택사항 ($resultOrdItemNum 를 받아야 하므로 여기서 다시 loop)
				{
					$arrOptInfo = explode('|', $ot);
						
					//ORDERITEM_OPTION insert
					$odpioData = array(
						'ORDERITEM_NUM' => $resultOrdItemNum,
						'SHOPITEM_OPTION_SUB_NUM' => $arrOptInfo[1], //옵션고유번호
						'OPTION_PRICE' => $arrOptInfo[3] //옵션 가격
					);
					$this->db->insert('ORDERITEM_OPTION', $odpioData);
					$resultOrdOptNum = $this->db->insert_id();
				}
				
				//주문전환된 카트 내용 삭제 - CARTITEM
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('NUM', $irs['CARTITEM_NUM']);
				$this->db->update('CARTITEM');
				
				$shopAmount = $shopAmount + $amount;
				$t--;
			};
			
			//ORDERPART update
			$odpUpData = array(
				'PARTITEM_COUNT' => $partItemCnt,
				'PART_QUANTITY' => $shopQuantity,
				'PART_PRICE' => $shopPrice,
				'PARTOPTION_PRICE' => $shopQuantity,
				'PARTADD_PRICE' => $shopOptPrice, //$shopOptPrice + 기타비용
				'PART_AMOUNT' => $shopAmount
			);
			$this->db->where('NUM', $resultOrdPartNum);
			$this->db->update('ORDERPART', $odpUpData);
			
			//주문전환된 카트 내용 삭제 - CART
			if ($rs['ITEM_CNT'] == $partItemCnt)
			{
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('NUM', $rs['CART_NUM']);
				$this->db->update('CART');			
			}
			
			//재고 업데이트
			$this->setOrderPartStockUpdate($resultOrdPartNum, '-', TRUE);			
			
			$itemCnt = $itemCnt + $partItemCnt;
			$totDeliveryPrice = $totDeliveryPrice + $deliverPrice;
			$totShopAmount = $totShopAmount + $shopAmount;
			$i--;
		};

		$totAmount = $totShopAmount;
		
		$now = DateTime::createFromFormat('U.u', microtime(true));
		//PG주문코드 일치를 위해 주석 처리
		//$realOrdCode = 'OD'.$now->format("ymd").str_pad($resultOrdNum, 8, '0', STR_PAD_LEFT); //실주문번호
		//ORDER update
		$ordUpData = array(
			//'ORDER_CODE' => $realOrdCode,
			'TOTITEM_COUNT' => $itemCnt,
			'TOTPART_COUNT' => $partCnt,
			'TOT_QUANTITY' => $totQuantity,
			'TOT_PRICE' => $totPrice,
			'TOTOPTION_PRICE' => $totOptionPrice,
			'TOTADD_PRICE' => $totOptionPrice,	//$totOptionPrice + 기타비용
			'TOTDELIVERY_PRICE' => $totDeliveryPrice,
			'TOT_AMOUNT' => $totAmount,
			'TOTFINAL_AMOUNT' => $totAmount + $totDeliveryPrice //환불이나 교환이 있을경우 완료된 상태의 최종 총 주문금액
		);
		$this->db->where('NUM', $resultOrdNum);
		$this->db->update($this->tbl, $ordUpData);		
		
		/* 앞단에서 금액검증 이루어짐
		if (($totAmount + $totDeliveryPrice) != $orderAmount)
		{
			//echo '<br />totAmount='.$totAmount;
			//echo '<br />totShopAmount='.$totShopAmount;
			//echo '<br />totOptionPrice='.$totOptionPrice;
			//echo '<br />totDeliveryPrice='.$totDeliveryPrice;
			//echo '<br />orderAmount='.$orderAmount;
			//exit;			
			//Transaction 롤백
			$this->db->trans_rollback();
			$this->common->message('실결제금액과 검증금액이 일치하지 않습니다.\\n주문이 취소됩니다.', '-', '');
		}
		*/
		
		//USER 테이블에 현재 CART ITEM갯수 update
		$this->db->set(
			'CART_COUNT',
			"(
				SELECT COUNT(*) FROM CARTITEM
				WHERE DEL_YN = 'N'
				AND CART_NUM IN (
					SELECT NUM FROM CART 
					WHERE DEL_YN = 'N'
					AND DIRECT_YN = 'N'
					AND USER_NUM = ".$ordData['USER_NUM']."
				)
			)",
			FALSE
		);
		$this->db->where('NUM', $ordData['USER_NUM']);
		$this->db->update('USER');		

		//Transaction 자동 커밋
		$this->db->trans_complete();

		return $resultOrdNum;
	}
	
	/**
	 * @method name : setInputCart
	 * 아이템 카트에 넣기(현재 앱에서만 사용) 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setInputCart($qData)
	{
		//바로 구매인 경우 무조건 전체 카트 row 생성
		//즉시구매는 카트에서 바로 주문전환되면서 삭제되므로
		//DIRECT_YN = 'Y' 로 하면 항상 없는 조회결과가 되어 신규입력 상태가 됌
		$whSql = ($qData['directYn'] == 'Y') ? " AND a.DIRECT_YN = 'Y'" : ""; 
		$sql = "
			SELECT 
				b.CART_NUM,
				b.NUM AS CARTITEM_NUM,
				(
					SELECT
						GROUP_CONCAT(itOpt.OPT_NUM SEPARATOR '|')
					FROM 
					(
						SELECT 
							CARTITEM_NUM, SHOPITEM_OPTION_SUB_NUM AS OPT_NUM      
						FROM CARTITEM_OPTION
					) AS itOpt
					GROUP BY CARTITEM_NUM
					HAVING itOpt.CARTITEM_NUM = b.NUM
				) AS CARTITEM_OPTION_INFO				
			FROM ".$this->_cartTbl." AS a INNER JOIN ".$this->_cartTbl."ITEM AS b
			ON a.NUM = b.CART_NUM 
			WHERE a.USER_NUM = ".$qData['uNum']."
			AND a.SHOP_NUM = ".$qData['sNum']."
			AND b.SHOPITEM_NUM = ".$qData['siNum']."
			AND a.DEL_YN = 'N'
			AND b.DEL_YN = 'N'
			".$whSql."
			ORDER BY b.NUM 	
		";
		$result = $this->db->query($sql)->result_array();		

		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$arrOpt = explode('|', $qData['sioptsNum']); //카트에 넣으려는 옵션고유번호
		if ($result) //동일한 아이템이 카트에 있는 경우
		{
			$isSame = FALSE;
			foreach ($result as $rs)
			{
				$arrCartOpt = (!empty($rs['CARTITEM_OPTION_INFO'])) ? explode('|', $rs['CARTITEM_OPTION_INFO']) : array(NULL);
				$isEqual = $this->common->array_equal($arrCartOpt, $arrOpt);
				if ($isEqual)
				{
					//기존 옵션 내용이 있는 경우 - 수량 update
					$this->db->set('QUANTITY', 'QUANTITY + '.$qData['siQuantity'], FALSE);
					$this->db->where('NUM', $rs['CARTITEM_NUM']);
					$this->db->update($this->_cartTbl.'ITEM');
					$resultNum = $this->db->affected_rows();
					$isSame = TRUE;
					break;
				}
			}
			
			if (!$isSame)
			{
				//기존 옵션 내용과 차이가 있는 경우 - 카트아이템 내용 추가 생성
				$insItemData = array(
					'CART_NUM' => $rs['CART_NUM'],
					'SHOPITEM_NUM' => $qData['siNum'],
					'QUANTITY' => $qData['siQuantity']
				);
				$this->db->insert($this->_cartTbl.'ITEM', $insItemData);
				$resultItNum = $resultNum = $this->db->insert_id();
				foreach ($arrOpt as $ot)
				{
					$val = (!empty($ot)) ? $ot : NULL;
					$insOptData = array(
						'CARTITEM_NUM' => $resultItNum,
						'SHOPITEM_OPTION_SUB_NUM' => $val
					);
					$this->db->insert($this->_cartTbl.'ITEM_OPTION', $insOptData);
					$resultCtNum = $this->db->insert_id();
				}
			}
		}
		else 
		{
			//동일한 아이템이 카트에 없는 경우 동일한 샵이 있는지 확인
			$sql = "
				SELECT
					a.NUM
				FROM ".$this->_cartTbl." AS a 
				WHERE a.USER_NUM = ".$qData['uNum']."
				AND a.SHOP_NUM = ".$qData['sNum']."
				AND a.DEL_YN = 'N'
				".$whSql." LIMIT 1
			";
			$result = $this->db->query($sql)->row_array();
			if ($result)
			{
				//동일한 샵이 있는 경우
				$resultNum = $result['NUM']; //CART_NUM
			}
			else
			{
				//동일한 샵이 없는 경우 새로 생성 (directYn = 'Y' 인 경우 무조건 이쪽으로)
				$insData = array(
					'SHOP_NUM' => $qData['sNum'],
					'USER_NUM' => $qData['uNum']
				);
				$directYn = 'N';
				if (isset($qData['directYn']))
				{
					$directYn = (!empty($qData['directYn'])) ? strtoupper($qData['directYn']) : 'N';
					$insData['DIRECT_YN'] = $directYn;
				}
				$this->db->insert($this->_cartTbl, $insData);
				$resultNum = $this->db->insert_id();
			}
			
			$insItemData = array(
				'CART_NUM' => $resultNum,
				'SHOPITEM_NUM' => $qData['siNum'],
				'QUANTITY' => $qData['siQuantity']
			);
			$this->db->insert($this->_cartTbl.'ITEM', $insItemData);
			$resultItNum = $this->db->insert_id();
			
			foreach ($arrOpt as $ot)
			{
				$val = (!empty($ot)) ? $ot : NULL;				
				$insOptData = array(
					'CARTITEM_NUM' => $resultItNum,
					'SHOPITEM_OPTION_SUB_NUM' => $val
				);
				$this->db->insert($this->_cartTbl.'ITEM_OPTION', $insOptData);
				$resultCtNum = $this->db->insert_id();
			}
		}
		
		//USER 테이블에 현재 CART ITEM갯수 update
		$this->db->set(
			'CART_COUNT',
			"(
				SELECT COUNT(*) FROM CARTITEM
				WHERE DEL_YN = 'N'
				AND CART_NUM IN (
					SELECT NUM FROM CART 
					WHERE DEL_YN = 'N'
					AND DIRECT_YN = 'N'
					AND USER_NUM = ".$qData['uNum']."
				)
			)",
			FALSE
		);
		$this->db->where('NUM', $qData['uNum']);
		$this->db->update('USER');		

		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $resultNum;
	}
	
	/**
	 * @method name : setShopItemStockUpdate
	 * 아이템 재고 UPDATE(SHOPITEM_NUM으로) 
	 * 
	 * @param unknown $siNum
	 * @param unknown $cnt
	 * @param unknown $type +,-
	 * @param bool $isSellAdd 판매갯수와 금액도 반영할지 여부
	 * @return Ambiguous
	 */
	public function setShopItemStockUpdate($siNum, $cnt, $amount, $type = '-', $isSellAdd = FALSE)
	{
		$stockSql = 'STOCK_COUNT - '.$cnt;
		$sellCntSql = 'TOTSELL_COUNT + '.$cnt;
		$sellAmountSql = 'TOTSELL_AMOUNT + '.$amount;
		if ($type == '+')
		{
			$stockSql = 'STOCK_COUNT + '.$cnt;
			$sellCntSql = 'TOTSELL_COUNT - '.$cnt;
			$sellAmountSql = 'TOTSELL_AMOUNT - '.$amount;
		}
		
		$upData = array('STOCK_COUNT' => $stockSql);
		
		if ($isSellAdd)
		{
			$upData['TOTSELL_COUNT'] = $sellCntSql;
			$upData['TOTSELL_AMOUNT'] = $sellAmountSql;
				
			$this->db->set('STOCK_COUNT', $stockSql, FALSE);
			$this->db->set('TOTSELL_COUNT', $sellCntSql, FALSE);
			$this->db->set('TOTSELL_AMOUNT', $sellAmountSql, FALSE);
			$this->db->where('NUM', $siNum);
			$this->db->update('SHOPITEM');
			$result = $this->db->affected_rows();			
		}
		else 
		{
			$this->db->set('STOCK_COUNT', $stockSql, FALSE);
			$this->db->where('NUM', $siNum);
			$this->db->update('SHOPITEM');
			$result = $this->db->affected_rows();			
		}
		
		//마이너스가 되는 경우 보정
		$this->db->set('STOCK_COUNT', 0);
		$this->db->where('NUM', $siNum);
		$this->db->where('STOCK_COUNT < 0');
		$this->db->update('SHOPITEM');
		
		if ($isSellAdd)
		{
			//마이너스가 되는 경우 보정
			$this->db->set('TOTSELL_COUNT', 0);
			$this->db->where('NUM', $siNum);
			$this->db->where('TOTSELL_COUNT < 0');
			$this->db->update('SHOPITEM');			
			
			//마이너스가 되는 경우 보정
			$this->db->set('TOTSELL_AMOUNT', 0);
			$this->db->where('NUM', $siNum);
			$this->db->where('TOTSELL_AMOUNT < 0');
			$this->db->update('SHOPITEM');			
		}		
		
		return $result;
	}
	
	/**
	 * @method name : setOrderPartStockUpdate
	 * 재고 카운트 update (ORDERPART_NUM으로)
	 * 
	 * @param unknown $ordPartNum
	 * @param string $type
	 * @param bool $isSellAdd 판매갯수와 금액도 반영할지 여부
	 * @return number
	 */
	public function setOrderPartStockUpdate($ordPartNum, $type = '-', $isSellAdd = FALSE)
	{
		$totResult = $result = 0;
		$this->db->select("SHOPITEM_NUM, QUANTITY, AMOUNT");
		$this->db->from('ORDERITEM');
		$this->db->where('ORDERPART_NUM', $ordPartNum);
		$this->db->where('DEL_YN', 'N');
		$data = $this->db->get()->result_array();
		if ($data)
		{
			foreach ($data as $dt)
			{
				$result = $this->setShopItemStockUpdate(
					$dt['SHOPITEM_NUM'], 
					$dt['QUANTITY'], 
					$dt['AMOUNT'],
					$type,
					$isSellAdd
				);
				$totResult = $totResult + $result;
			}
		}
		
		return $totResult;
	}
	
	/**
	 * @method name : setPGReturnData
	 * PG사로 부터 받은 통보데이터 내용 그대로 히스토리 기록 
	 * 
	 * @param unknown $insData
	 * @return Ambiguous
	 */
	public function setPGReturnData($insData)
	{
		$this->db->insert('PG_RETURN', $insData);
		$resultNum = $this->db->insert_id();
		
		return $resultNum;
	}
	
	/**
	 * @method name : setPGOrderConfirmData
	 * PG사로 부터 통보받는 구매확인/취소 여부 
	 * 
	 * @param unknown $upData
	 * @return Ambiguous
	 */
	public function setPGOrderConfirmData($upData)
	{
		$tno = $upData['tno'];
		unset($upData['tno']);

		$this->db->where('PAYRESULT_ID', $tno);
		$this->db->update($this->tbl, $upData);
		$result = $this->db->affected_rows();
		
		return $result;
	}
}
?>