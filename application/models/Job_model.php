<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Job_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Job_model extends CI_Model{

	protected $_encKey = '';
	
	protected $_toDate = '';
	
	protected $_ordCodeGrpList = array();
	
	protected $_payCodeGrpList = array();	
	
	public function __construct() {
		parent::__construct();

		$this->load->library(array('session'));
		$this->load->database(); // Database Load
		
		$this->_toDate = date('Y-m-d H:i:s');
		$this->_encKey = $this->config->item('encryption_key');
		
		$this->_ordCodeGrpList  = $this->common->getCodeListByGroup('ORDSTATE');
		$this->_payCodeGrpList = $this->common->getCodeListByGroup('ORDPAY');
	}
	
	/**
	 * @method name : setOrderStateDay
	 * 주문상태별 통계(일자별) 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setOrderStateDay($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];	
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";		
		
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_DAY', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_DAY', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');				
				endforeach;					
			}
		endforeach;
		$this->setYearMonthDay($sDate, $eDate); //일자 생성		
		
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT DATE_FORMAT(CREATE_DATE, '%Y%m%d') AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')                
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')                                
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')                                
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY ORDSTATECODE_NUM, ORDSTATECODE_NUM2, DATE_FORMAT(CREATE_DATE, '%Y%m%d')
		";
		$result = $this->db->query($sql)->result_array();

		//Transaction 시작 (자동 수행)
		$this->db->trans_start();		
		
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ORDSTATE_DAY
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_DAY',
					array(
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);			
			}
			
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_DAY
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_DAY
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);				
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_DAY
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%Y%m%d') = '".$rs['CREATE_DATE']."'
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%Y%m%d') = '".$rs['CREATE_DATE']."'
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_DAY
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560				
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;
	}
	
	/**
	 * @method name : setOrderStateWeek
	 * 주문상태별 통계(주차별) 
	 * 
	 * MYSQL mode default is 0 (Sunday), 1 (Monday) ...7
	 * 		 YEARWEEK(date, 0)
	 * PHP 에서는 date("oW",mktime(0, 0, 0, 1, 1, 2012)); // outputs 201152
	 * 			  date('oW', strtotime($startDate))
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setOrderStateWeek($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];	
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_WEEK', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_WEEK', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}		
		endforeach;
		$this->setYearWeek($sDate, $eDate); //일자 생성
	
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT YEARWEEK(CREATE_DATE) AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY ORDSTATECODE_NUM, ORDSTATECODE_NUM2, YEARWEEK(CREATE_DATE)
		";		
		$result = $this->db->query($sql)->result_array();
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
					EXISTS (
						SELECT 1 FROM STATS_ORDSTATE_WEEK
						WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					) AS RESULT
				");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_WEEK',
					array(
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_WEEK
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_WEEK
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}	
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			//week는 일요일을 기준으로 총 53주차 (월요일 기준인 경우 '%x%v'
			$sql = "
				UPDATE STATS_ORDSTATE_WEEK
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%X%V') = '".$rs['CREATE_DATE']."'
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%X%V') = '".$rs['CREATE_DATE']."'
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_WEEK
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setOrderStateMonth
	 * 주문상태별 통계(월별) 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setOrderStateMonth($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];	
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_MONTH', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_MONTH', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}
		endforeach;
		$this->setYearMonth($sDate, $eDate); //일자 생성
		
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY ORDSTATECODE_NUM, ORDSTATECODE_NUM2, DATE_FORMAT(CREATE_DATE, '%Y%m')
		";
		$result = $this->db->query($sql)->result_array();
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ORDSTATE_MONTH
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_MONTH',
					array(
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_MONTH
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_MONTH
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}		
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_MONTH
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%Y%m') = '".$rs['CREATE_DATE']."'
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%Y%m') = '".$rs['CREATE_DATE']."'
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_MONTH
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setOrderStateYear
	 * 주문상태별 통계(년별)
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setOrderStateYear($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];	
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_YEAR', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_YEAR', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}		
		endforeach;
		$this->setYear($sDate, $eDate); //일자 생성
		
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT DATE_FORMAT(CREATE_DATE, '%Y') AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY ORDSTATECODE_NUM, ORDSTATECODE_NUM2, DATE_FORMAT(CREATE_DATE, '%Y')
		";
		$result = $this->db->query($sql)->result_array();
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ORDSTATE_YEAR
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_YEAR',
					array(
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_YEAR
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_YEAR
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				";
				$this->db->query($sql);
			}	
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_YEAR
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%Y') = '".$rs['CREATE_DATE']."'
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%Y') = '".$rs['CREATE_DATE']."'
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			";	
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_YEAR
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setOrderState
	 * 주문상태별 통계(통합) 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setOrderState($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];	
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}		
		endforeach;
		
		$this->db->select("
			COUNT(*) AS CNT FROM STATS_ORDSTATE
		");
		$result = $this->db->get()->row()->CNT;
		if ($result == 0) //빈레코드생성
		{
			$this->db->insert('STATS_ORDSTATE', array('CREATE_DATE' => $this->_toDate));
		}		
	
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT ORDSTATECODE_NUM,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY ORDSTATECODE_NUM, ORDSTATECODE_NUM2
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		foreach ($result as $rs):
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
				";
				$this->db->query($sql);
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
						)
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopOrderStateDay
	 * 주문상태별 통계(일자별) - 샵별
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopOrderStateDay($qData)
	{
		$shopNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;				
			}
		}
			
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_SHOP_DAY', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_SHOP_DAY', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}
		endforeach;
		$this->setYearMonthDay($sDate, $eDate); //일자 생성
		
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT SHOP_NUM, DATE_FORMAT(CREATE_DATE, '%Y%m%d') AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT SHOP_NUM, ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY SHOP_NUM, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, DATE_FORMAT(CREATE_DATE, '%Y%m%d')
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ORDSTATE_SHOP_DAY
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_SHOP_DAY',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_DAY
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_DAY
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_SHOP_DAY
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%Y%m%d') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%Y%m%d') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."						
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_SHOP_DAY
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopOrderStateWeek
	 * 주문상태별 통계(주차별) - 샵별
	 *
	 * MYSQL mode default is 0 (Sunday), 1 (Monday) ...7
	 * 		 YEARWEEK(date, 0)
	 * PHP 에서는 date("oW",mktime(0, 0, 0, 1, 1, 2012)); // outputs 201152
	 * 			  date('oW', strtotime($startDate))
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopOrderStateWeek($qData)
	{
		$shopNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}
		}
		
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_SHOP_WEEK', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
				$this->setColumn('STATS_ORDSTATE_SHOP_WEEK', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}
		endforeach;
		$this->setYearWeek($sDate, $eDate); //일자 생성
	
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT SHOP_NUM, YEARWEEK(CREATE_DATE) AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT SHOP_NUM, ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY SHOP_NUM, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, YEARWEEK(CREATE_DATE)
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ORDSTATE_SHOP_WEEK
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_SHOP_WEEK',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}		
			
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_WEEK
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_WEEK
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			//week는 일요일을 기준으로 총 53주차 (월요일 기준인 경우 '%x%v'
			$sql = "
				UPDATE STATS_ORDSTATE_SHOP_WEEK
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%X%V') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%X%V') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_SHOP_WEEK
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setShopOrderStateMonth
	 * 주문상태별 통계(월별) - 샵별
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopOrderStateMonth($qData)
	{
		$shopNum = 0;		
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_SHOP_MONTH', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_SHOP_MONTH', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}
		endforeach;
		$this->setYearMonth($sDate, $eDate); //일자 생성
	
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT SHOP_NUM, DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT SHOP_NUM, ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY SHOP_NUM, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, DATE_FORMAT(CREATE_DATE, '%Y%m')
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
					EXISTS (
						SELECT 1 FROM STATS_ORDSTATE_SHOP_MONTH
						WHERE STATS_DATE = ".$rs['CREATE_DATE']."
						AND SHOP_NUM = ".$rs['SHOP_NUM']."
					) AS RESULT
				");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_SHOP_MONTH',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}		
			
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
						UPDATE STATS_ORDSTATE_SHOP_MONTH
							SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
								UPDATE_DATE = '".$this->_toDate."'
						WHERE STATS_DATE = ".$rs['CREATE_DATE']."
						AND SHOP_NUM = ".$rs['SHOP_NUM']."
					";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_MONTH
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_SHOP_MONTH
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%Y%m') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%Y%m') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_SHOP_MONTH
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopOrderStateYear
	 * 주문상태별 통계(년별) - 샵별
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopOrderStateYear($qData)
	{
		$shopNum = 0;		
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}		
	
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_SHOP_YEAR', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_SHOP_YEAR', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}
		endforeach;
		$this->setYear($sDate, $eDate); //일자 생성
		
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT SHOP_NUM, DATE_FORMAT(CREATE_DATE, '%Y') AS CREATE_DATE, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT SHOP_NUM, ORDSTATECODE_NUM, CREATE_DATE,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY SHOP_NUM, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, DATE_FORMAT(CREATE_DATE, '%Y')
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ORDSTATE_SHOP_YEAR
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ORDSTATE_SHOP_YEAR',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}	
			
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_YEAR
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP_YEAR
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_SHOP_YEAR
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND DATE_FORMAT(CREATE_DATE, '%Y') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND DATE_FORMAT(CREATE_DATE, '%Y') = '".$rs['CREATE_DATE']."'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						)
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_SHOP_YEAR
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setShopOrderState
	 * 주문상태별 통계(통합) - 샵별
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopOrderState($qData)
	{
		$shopNum = 0;		
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		foreach ($this->_ordCodeGrpList  as $rs): //컬럼 생성
			$this->setColumn('STATS_ORDSTATE_SHOP', 'STATE_'.$rs['NUM'], 'int');
			if ($rs['NUM'] == 5080)
			{
				//입금확인시 결제수단 구분을 위해 컬럼 추가
				foreach ($this->_payCodeGrpList as $prs):
					$this->setColumn('STATS_ORDSTATE_SHOP', 'STATE_'.$rs['NUM'].'_'.$prs['NUM'], 'int');
				endforeach;
			}
		endforeach;
		
		$this->db->select("
			COUNT(*) AS CNT FROM STATS_ORDSTATE_SHOP
		");
		$result = $this->db->get()->row()->CNT;
		if ($result == 0) //빈레코드생성
		{
			if ($shopNum > 0)
			{
				$this->db->insert(
					'STATS_ORDSTATE_SHOP', 
					array(
						'CREATE_DATE' => $this->_toDate,
						'SHOP_NUM' => $shopNum		
					)
				);
			}
			else
			{
				$this->db->select('NUM');
				$this->db->where("DEL_YN = 'N'");
				//$this->db->where('SHOPSTATECODE_NUM = 3060'); //운영중인 샵만
				$this->db->from('SHOP');
				$result = $this->db->get()->result_array();
				foreach ($result  as $rs): //샵별 생성
					$this->db->insert(
						'STATS_ORDSTATE_SHOP',
						array(
							'CREATE_DATE' => $this->_toDate,
							'SHOP_NUM' => $rs['NUM']
						)
					);
				endforeach;
			}
		}
	
		//입금확인시 결제수단 구분을 위해 CASE 사용 - 결제수단 변경이 되는 경우 컬럼조정필요
		$sql = "
			SELECT SHOP_NUM, ORDSTATECODE_NUM, ORDSTATECODE_NUM2, COUNT(*) AS CNT
			FROM
			(
			  	SELECT SHOP_NUM, ORDSTATECODE_NUM,
					(
						CASE
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5510
								THEN CONCAT(ORDSTATECODE_NUM,'_5510')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5520
								THEN CONCAT(ORDSTATECODE_NUM,'_5520')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5530
								THEN CONCAT(ORDSTATECODE_NUM,'_5530')
							WHEN ORDSTATECODE_NUM = 5080 AND (SELECT PAYCODE_NUM FROM ORDERS WHERE NUM = ORDERPART.ORDERS_NUM) = 5560
								THEN CONCAT(ORDSTATECODE_NUM,'_5560')
							ELSE
							ORDSTATECODE_NUM
						END
					) AS ORDSTATECODE_NUM2
				FROM ORDERPART
				WHERE DEL_YN = 'N'
			  	".$whSql."
			) tb
			GROUP BY SHOP_NUM, ORDSTATECODE_NUM, ORDSTATECODE_NUM2
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			if ($rs['ORDSTATECODE_NUM'] == 5080)
			{
				//입금확인시 결제수단 구분
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP
						SET STATE_".$rs['ORDSTATECODE_NUM2']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			else
			{
				$sql = "
					UPDATE STATS_ORDSTATE_SHOP
						SET STATE_".$rs['ORDSTATECODE_NUM']." = ".$rs['CNT'].",
							UPDATE_DATE = '".$this->_toDate."'
					WHERE SHOP_NUM = ".$rs['SHOP_NUM']."
				";
				$this->db->query($sql);
			}
			
			//주문접수(일자범위안의 주문된 모든 건수), 주문확인 건수 update
			$sql = "
				UPDATE STATS_ORDSTATE_SHOP
					SET
						STATE_5040 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						),
						STATE_5050 = (
							SELECT COUNT(*) FROM ORDERPART
							WHERE DEL_YN = 'N'
							AND (CHECK_YN = 'Y' OR ORDSTATECODE_NUM = 5050)
							AND SHOP_NUM = ".$rs['SHOP_NUM']."
						)
				WHERE SHOP_NUM = ".$rs['SHOP_NUM']."
			";
			$this->db->query($sql);			
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ORDSTATE_SHOP
				SET
					STATE_5080 = (
						STATE_5080_5510+
						STATE_5080_5520+
						STATE_5080_5530+
						STATE_5080_5560
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopStats
	 * Craft Shop 통계
	 * 판매순위, 플래그순위 
	 * 샵생성시 레코드 생성되어야함
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopStats($qData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		//interval 기간내 데이터가 없다면 통계작성 설정기간이 되었으므로 
		//현재 통계 내용을 이전 통계내용으로 옮긴다
		$sql = "
			UPDATE STATS_SHOP
				SET
					BEFORE_SELLCOUNT_RANK = SELLCOUNT_RANK,
					BEFORE_FLAGCOUNT_RANK = FLAGCOUNT_RANK,
					BEFORE_SELLAMOUNT_RANK = SELLAMOUNT_RANK,
					BEFORE_TOTSELL_COUNT = TOTSELL_COUNT,
					BEFORE_TOTFLAG_COUNT = TOTFLAG_COUNT,
					BEFORE_TOTSELL_AMOUNT = TOTSELL_AMOUNT,				
					BEFORE_UPDATE_DATE = UPDATE_DATE
			WHERE BEFORE_UPDATE_DATE <= DATE_ADD(now(), interval -".$qData['intervalDay']." day)
		";
		$this->db->query($sql);			

		$sql = "
			UPDATE STATS_SHOP a INNER JOIN SHOP b
			ON a.SHOP_NUM = b.NUM
				SET
					a.TOTSELL_COUNT = b.TOTSELL_COUNT,
					a.TOTFLAG_COUNT = b.TOTFLAG_COUNT,
					a.TOTSELL_AMOUNT = b.TOTSELL_AMOUNT,
					a.UPDATE_DATE = '".$this->_toDate."'
			WHERE a.SHOP_NUM = b.NUM				
		";
		$this->db->query($sql);
		$result = $this->db->affected_rows();
		
		//순위부여 - SELL_COUNT
		$sql = "
			UPDATE STATS_SHOP a INNER JOIN 
				(
					SELECT  T1.SHOP_NUM,
						MIN(T1.TOTSELL_COUNT) AS SCORE,
						COUNT(T2.SHOP_NUM) +1 AS RANK
					FROM    STATS_SHOP T1 LEFT OUTER JOIN STATS_SHOP T2
					ON T1.TOTSELL_COUNT < T2.TOTSELL_COUNT
					GROUP BY T1.SHOP_NUM
					ORDER BY RANK				
				) b
			ON a.SHOP_NUM = b.SHOP_NUM
				SET
					a.SELLCOUNT_RANK = b.RANK
			WHERE a.SHOP_NUM = b.SHOP_NUM
		";		
		$this->db->query($sql);
		
		//순위부여 - FLAG_COUNT
		$sql = "
			UPDATE STATS_SHOP a INNER JOIN
				(
					SELECT  T1.SHOP_NUM,
						MIN(T1.TOTFLAG_COUNT) AS SCORE,
						COUNT(T2.SHOP_NUM) +1 AS RANK
					FROM    STATS_SHOP T1 LEFT OUTER JOIN STATS_SHOP T2
					ON T1.TOTFLAG_COUNT < T2.TOTFLAG_COUNT
					GROUP BY T1.SHOP_NUM
					ORDER BY RANK
				) b
			ON a.SHOP_NUM = b.SHOP_NUM
				SET
					a.FLAGCOUNT_RANK = b.RANK
			WHERE a.SHOP_NUM = b.SHOP_NUM
		";
		$this->db->query($sql);		
		
		//순위부여 - SELL_AMOUNT
		$sql = "
			UPDATE STATS_SHOP a INNER JOIN
				(
					SELECT  T1.SHOP_NUM,
						MIN(T1.TOTSELL_AMOUNT) AS SCORE,
						COUNT(T2.SHOP_NUM) +1 AS RANK
					FROM    STATS_SHOP T1 LEFT OUTER JOIN STATS_SHOP T2
					ON T1.TOTSELL_AMOUNT < T2.TOTSELL_AMOUNT
					GROUP BY T1.SHOP_NUM
					ORDER BY RANK
				) b
			ON a.SHOP_NUM = b.SHOP_NUM
				SET
					a.SELLAMOUNT_RANK = b.RANK
			WHERE a.SHOP_NUM = b.SHOP_NUM
		";
		$this->db->query($sql);	
		
		//랭킹 순위변화 차이 update
		$sql = "
			UPDATE STATS_SHOP
				SET
					SELL_RANK_GAP = BEFORE_SELLCOUNT_RANK - SELLCOUNT_RANK
			WHERE TOTSELL_COUNT > 0
		";
		$this->db->query($sql);		
		
		$sql = "
			UPDATE STATS_SHOP
				SET
					FLAG_RANK_GAP = BEFORE_FLAGCOUNT_RANK - FLAGCOUNT_RANK
			WHERE TOTFLAG_COUNT > 0
		";
		$this->db->query($sql);
		
		$sql = "
			UPDATE STATS_SHOP
				SET
					SELLAMOUNT_RANK_GAP = BEFORE_SELLAMOUNT_RANK - SELLAMOUNT_RANK
			WHERE TOTSELL_AMOUNT > 0
		";
		$this->db->query($sql);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();	
		
		return $result;
	}
	
	/**
	 * @method name : setShopItemStats
	 * Craft Shop Item 통계
	 * 아이템별 판매순위, 플래그순위
	 * 아이템생성시 레코드 생성되어야함
	 *  
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopItemStats($qData)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();

		//interval 기간과 같거나 지난 일자가 있는 경우 통계작성 설정기간이 되었으므로
		//현재 통계 내용을 이전 통계내용으로 옮긴다
		$sql = "
			UPDATE STATS_SHOPITEM
				SET
					BEFORE_SELLCOUNT_RANK = SELLCOUNT_RANK,
					BEFORE_FLAGCOUNT_RANK = FLAGCOUNT_RANK,
					BEFORE_SELLAMOUNT_RANK = SELLAMOUNT_RANK,
					BEFORE_SCORE_RANK = SCORE_RANK,
					BEFORE_TOTSELL_COUNT = TOTSELL_COUNT,
					BEFORE_TOTFLAG_COUNT = TOTFLAG_COUNT,
					BEFORE_TOTSELL_AMOUNT = TOTSELL_AMOUNT,
					BEFORE_TOTSCORE = TOTSCORE,
					BEFORE_UPDATE_DATE = UPDATE_DATE
			WHERE BEFORE_UPDATE_DATE <= DATE_ADD(now(), interval -".$qData['intervalDay']." day)
		";
		$this->db->query($sql);
		
		$sql = "
			UPDATE STATS_SHOPITEM a INNER JOIN SHOPITEM b
			ON a.SHOPITEM_NUM = b.NUM
				SET
					a.TOTSELL_COUNT = b.TOTSELL_COUNT,
					a.TOTFLAG_COUNT = b.TOTFLAG_COUNT,
					a.TOTSELL_AMOUNT = b.TOTSELL_AMOUNT,
					a.TOTSCORE = b.TOTSCORE,
					a.UPDATE_DATE = '".$this->_toDate."'
			WHERE a.SHOPITEM_NUM = b.NUM
		";
		$this->db->query($sql);
		$result = $this->db->affected_rows();
		
		//순위부여 - SELL_COUNT
		$sql = "
			UPDATE STATS_SHOPITEM a INNER JOIN
				(
					SELECT  T1.SHOPITEM_NUM,
						MIN(T1.TOTSELL_COUNT) AS SCORE,
						COUNT(T2.SHOPITEM_NUM) +1 AS RANK
					FROM    STATS_SHOPITEM T1 LEFT OUTER JOIN STATS_SHOPITEM T2
					ON T1.TOTSELL_COUNT < T2.TOTSELL_COUNT
					GROUP BY T1.SHOPITEM_NUM
					ORDER BY RANK
				) b
			ON a.SHOPITEM_NUM = b.SHOPITEM_NUM
				SET
					a.SELLCOUNT_RANK = b.RANK
			WHERE a.SHOPITEM_NUM = b.SHOPITEM_NUM
		";
		$this->db->query($sql);
		
		//순위부여 - FLAG_COUNT
		$sql = "
			UPDATE STATS_SHOPITEM a INNER JOIN
				(
					SELECT  T1.SHOPITEM_NUM,
						MIN(T1.TOTFLAG_COUNT) AS SCORE,
						COUNT(T2.SHOPITEM_NUM) +1 AS RANK
					FROM    STATS_SHOPITEM T1 LEFT OUTER JOIN STATS_SHOPITEM T2
					ON T1.TOTFLAG_COUNT < T2.TOTFLAG_COUNT
					GROUP BY T1.SHOPITEM_NUM
					ORDER BY RANK
				) b
			ON a.SHOPITEM_NUM = b.SHOPITEM_NUM
				SET
					a.FLAGCOUNT_RANK = b.RANK
			WHERE a.SHOPITEM_NUM = b.SHOPITEM_NUM
		";
		$this->db->query($sql);
		
		//순위부여 - SCORE
		$sql = "
			UPDATE STATS_SHOPITEM a INNER JOIN
				(
					SELECT  T1.SHOPITEM_NUM,
						MIN(T1.TOTSCORE) AS SCORE,
						COUNT(T2.SHOPITEM_NUM) +1 AS RANK
					FROM    STATS_SHOPITEM T1 LEFT OUTER JOIN STATS_SHOPITEM T2
					ON T1.TOTSCORE < T2.TOTSCORE
					GROUP BY T1.SHOPITEM_NUM
					ORDER BY RANK
				) b
			ON a.SHOPITEM_NUM = b.SHOPITEM_NUM
				SET
					a.SCORE_RANK = b.RANK
			WHERE a.SHOPITEM_NUM = b.SHOPITEM_NUM
		";
		$this->db->query($sql);		
		
		//순위부여 - SELL_AMOUNT
		$sql = "
			UPDATE STATS_SHOPITEM a INNER JOIN
				(
					SELECT  T1.SHOPITEM_NUM,
						MIN(T1.TOTSELL_AMOUNT) AS SCORE,
						COUNT(T2.SHOPITEM_NUM) +1 AS RANK
					FROM    STATS_SHOPITEM T1 LEFT OUTER JOIN STATS_SHOPITEM T2
					ON T1.TOTSELL_AMOUNT < T2.TOTSELL_AMOUNT
					GROUP BY T1.SHOPITEM_NUM
					ORDER BY RANK
				) b
			ON a.SHOPITEM_NUM = b.SHOPITEM_NUM
				SET
					a.SELLAMOUNT_RANK = b.RANK
			WHERE a.SHOPITEM_NUM = b.SHOPITEM_NUM
		";
		$this->db->query($sql);
		
		//랭킹 순위변화 차이 update
		$sql = "
			UPDATE STATS_SHOPITEM
				SET
					SELL_RANK_GAP = BEFORE_SELLCOUNT_RANK - SELLCOUNT_RANK
			WHERE TOTSELL_COUNT > 0
		";
		$this->db->query($sql);		
		
		$sql = "
			UPDATE STATS_SHOPITEM
				SET
					FLAG_RANK_GAP = BEFORE_FLAGCOUNT_RANK - FLAGCOUNT_RANK
			WHERE TOTFLAG_COUNT > 0
		";
		$this->db->query($sql);
		
		$sql = "
			UPDATE STATS_SHOPITEM
				SET
					SELLAMOUNT_RANK_GAP = BEFORE_SELLAMOUNT_RANK - SELLAMOUNT_RANK
			WHERE TOTSELL_AMOUNT > 0
		";
		$this->db->query($sql);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;
	}
	
	/**
	 * @method name : setSalesStatsDay
	 * 일별 매출 통계
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setSalesStatsDay($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SALES_DAY', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SALES_DAY', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearMonthDay($sDate, $eDate); //일자 생성	
		
		$sql = "
			SELECT 
				DATE_FORMAT(CREATE_DATE, '%Y%m%d') AS CREATE_DATE, 
				PAYCODE_NUM, 
				SUM(TOT_AMOUNT) AS AMOUNT, 
				COUNT(*) AS COUNT
			FROM 
			(
				SELECT 
					CREATE_DATE, TOT_AMOUNT, PAYCODE_NUM
				FROM ORDERS 
				WHERE DEL_YN = 'N'
				AND NUM IN (
					SELECT ORDERS_NUM FROM ORDERPART 
					WHERE DEL_YN = 'N' 
					AND ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m%d'), PAYCODE_NUM
		";
		$result = $this->db->query($sql)->result_array();		
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_SALES_DAY
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_SALES_DAY',
					array(
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
				
			$sql = "
				UPDATE STATS_SALES_DAY
					SET 
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",							
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
		
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SALES_DAY
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)				
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);		

		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;		
	}
	
	/**
	 * @method name : setSalesStatsWeek
	 * 주차별 매출 통계
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setSalesStatsWeek($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SALES_WEEK', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SALES_WEEK', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearWeek($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				YEARWEEK(CREATE_DATE) AS CREATE_DATE,
				PAYCODE_NUM,
				SUM(TOT_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					CREATE_DATE, TOT_AMOUNT, PAYCODE_NUM
				FROM ORDERS
				WHERE DEL_YN = 'N'
				AND NUM IN (
					SELECT ORDERS_NUM FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				)
				".$whSql."
			) tb
			GROUP BY YEARWEEK(CREATE_DATE), PAYCODE_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
		//해당일자 내용이 존재하는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_SALES_WEEK
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			) AS RESULT
		");
		$isExist = $this->db->get()->row()->RESULT;
		if (!$isExist) //없는경우 data 생성
		{
			$this->db->insert(
				'STATS_SALES_WEEK',
				array(
						'STATS_DATE' => $rs['CREATE_DATE']
				)
			);
		}
	
		$sql = "
			UPDATE STATS_SALES_WEEK
				SET
					PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
					PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
					UPDATE_DATE = '".$this->_toDate."'
			WHERE STATS_DATE = ".$rs['CREATE_DATE']."
		";
		$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SALES_WEEK
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setSalesStatsMonth
	 * 월별 매출 통계 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setSalesStatsMonth($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SALES_MONTH', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SALES_MONTH', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearMonth($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE,
				PAYCODE_NUM,
				SUM(TOT_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					CREATE_DATE, TOT_AMOUNT, PAYCODE_NUM
				FROM ORDERS
				WHERE DEL_YN = 'N'
				AND NUM IN (
					SELECT ORDERS_NUM FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m'), PAYCODE_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
		//해당일자 내용이 존재하는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_SALES_MONTH
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			) AS RESULT
		");
		$isExist = $this->db->get()->row()->RESULT;
		if (!$isExist) //없는경우 data 생성
		{
			$this->db->insert(
				'STATS_SALES_MONTH',
				array(
					'STATS_DATE' => $rs['CREATE_DATE']
				)
			);
		}
	
		$sql = "
			UPDATE STATS_SALES_MONTH
				SET
					PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
					PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
					UPDATE_DATE = '".$this->_toDate."'
			WHERE STATS_DATE = ".$rs['CREATE_DATE']."
		";
		$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SALES_MONTH
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setSalesStatsYear
	 * 년도별 매출 통계
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setSalesStatsYear($qData)
	{
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SALES_YEAR', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SALES_YEAR', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYear($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE,
				PAYCODE_NUM,
				SUM(TOT_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					CREATE_DATE, TOT_AMOUNT, PAYCODE_NUM
				FROM ORDERS
				WHERE DEL_YN = 'N'
				AND NUM IN (
					SELECT ORDERS_NUM FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m'), PAYCODE_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
		//해당일자 내용이 존재하는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_SALES_YEAR
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			) AS RESULT
		");
		$isExist = $this->db->get()->row()->RESULT;
		if (!$isExist) //없는경우 data 생성
		{
			$this->db->insert(
				'STATS_SALES_YEAR',
				array(
					'STATS_DATE' => $rs['CREATE_DATE']
				)
			);
		}
	
		$sql = "
			UPDATE STATS_SALES_YEAR
				SET
					PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
					PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
					UPDATE_DATE = '".$this->_toDate."'
			WHERE STATS_DATE = ".$rs['CREATE_DATE']."
		";
		$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SALES_MONTH
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}

	/**
	 * @method name : setShopSalesStatsDay
	 * 샵별 일별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopSalesStatsDay($qData)
	{
		$shopNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SHOPSALES_DAY', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SHOPSALES_DAY', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearMonthDay($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m%d') AS CREATE_DATE,
				SHOP_NUM,
				PAYCODE_NUM,
				SUM(PART_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT 
					a.CREATE_DATE, a.PAYCODE_NUM, b.PART_AMOUNT, b.SHOP_NUM
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m%d'), PAYCODE_NUM, SHOP_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_SHOPSALES_DAY
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_SHOPSALES_DAY',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_SHOPSALES_DAY
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SHOPSALES_DAY
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopSalesStatsWeek
	 * 샵별 주차별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopSalesStatsWeek($qData)
	{
		$shopNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}		
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SHOPSALES_WEEK', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SHOPSALES_WEEK', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearWeek($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				YEARWEEK(CREATE_DATE) AS CREATE_DATE,
				SHOP_NUM,				
				PAYCODE_NUM,
				SUM(PART_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT 
					a.CREATE_DATE, a.PAYCODE_NUM, b.PART_AMOUNT, b.SHOP_NUM
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY YEARWEEK(CREATE_DATE), PAYCODE_NUM, SHOP_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_SHOPSALES_WEEK
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_SHOPSALES_WEEK',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],							
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_SHOPSALES_WEEK
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."						
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SHOPSALES_WEEK
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopSalesStatsMonth
	 * 샵별 월별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopSalesStatsMonth($qData)
	{
		$shopNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SHOPSALES_MONTH', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SHOPSALES_MONTH', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearMonth($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE,
				SHOP_NUM,
				PAYCODE_NUM,
				SUM(PART_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT 
					a.CREATE_DATE, a.PAYCODE_NUM, b.PART_AMOUNT, b.SHOP_NUM
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m'), PAYCODE_NUM, SHOP_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_SHOPSALES_MONTH
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_SHOPSALES_MONTH',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],							
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_SHOPSALES_MONTH
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."						
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SHOPSALES_MONTH
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setShopSalesStatsYear
	 * 샵별 년도별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setShopSalesStatsYear($qData)
	{
		$shopNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_SHOPSALES_YEAR', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_SHOPSALES_YEAR', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYear($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE,
				SHOP_NUM,
				PAYCODE_NUM,
				SUM(PART_AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT 
					a.CREATE_DATE, a.PAYCODE_NUM, b.PART_AMOUNT, b.SHOP_NUM
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m'), PAYCODE_NUM, SHOP_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_SHOPSALES_YEAR
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_SHOPSALES_YEAR',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],							
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_SHOPSALES_YEAR
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_SHOPSALES_YEAR
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setItemSalesStatsDay
	 * 아이템별 일별 매출 통계
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setItemSalesStatsDay($qData)
	{
		$shopNum = 0;
		$itemNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		if (isset($qData['itemNum'])) //특정 아이템만 통계작성을 하는 경우
		{
			if ($qData['itemNum'] > 0)
			{
				$itemNum = $qData['itemNum'];
				$whSql .= " AND c.SHOPITEM_NUM = ".$itemNum;
			}			
		}		
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_ITEMSALES_DAY', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_ITEMSALES_DAY', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearMonthDay($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m%d') AS CREATE_DATE,
				SHOP_NUM,
				SHOPITEM_NUM,
				PAYCODE_NUM,
				SUM(AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					a.CREATE_DATE, a.PAYCODE_NUM, b.SHOP_NUM, c.SHOPITEM_NUM, c.AMOUNT
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM INNER JOIN ORDERITEM c
				ON b.NUM = c.ORDERPART_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m%d'), PAYCODE_NUM, SHOP_NUM, SHOPITEM_NUM				
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
					EXISTS (
						SELECT 1 FROM STATS_ITEMSALES_DAY
						WHERE STATS_DATE = ".$rs['CREATE_DATE']."
						AND SHOP_NUM = ".$rs['SHOP_NUM']."
						AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
					) AS RESULT
				");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ITEMSALES_DAY',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'SHOPITEM_NUM' => $rs['SHOPITEM_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_ITEMSALES_DAY
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
				AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ITEMSALES_DAY
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setItemSalesStatsWeek
	 * 아이템별 주차별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setItemSalesStatsWeek($qData)
	{
		$shopNum = 0;
		$itemNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		if (isset($qData['itemNum'])) //특정 아이템만 통계작성을 하는 경우
		{
			if ($qData['itemNum'] > 0)
			{
				$itemNum = $qData['itemNum'];
				$whSql .= " AND c.SHOPITEM_NUM = ".$itemNum;
			}
		}		
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_ITEMSALES_WEEK', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_ITEMSALES_WEEK', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearWeek($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				YEARWEEK(CREATE_DATE) AS CREATE_DATE,
				SHOP_NUM,
				SHOPITEM_NUM,
				PAYCODE_NUM,
				SUM(AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					a.CREATE_DATE, a.PAYCODE_NUM, b.PART_AMOUNT, b.SHOP_NUM, c.SHOPITEM_NUM, c.AMOUNT
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM INNER JOIN ORDERITEM c
				ON b.NUM = c.ORDERPART_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY YEARWEEK(CREATE_DATE), PAYCODE_NUM, SHOP_NUM, SHOPITEM_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ITEMSALES_WEEK
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
					AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ITEMSALES_WEEK',
					array(
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'SHOPITEM_NUM' => $rs['SHOPITEM_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_ITEMSALES_WEEK
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
				AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ITEMSALES_WEEK
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setItemSalesStatsMonth
	 * 아이템별 월별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setItemSalesStatsMonth($qData)
	{
		$shopNum = 0;
		$itemNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		if (isset($qData['itemNum'])) //특정 아이템만 통계작성을 하는 경우
		{
			if ($qData['itemNum'] > 0)
			{
				$itemNum = $qData['itemNum'];
				$whSql .= " AND c.SHOPITEM_NUM = ".$itemNum;
			}			
		}		
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_ITEMSALES_MONTH', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_ITEMSALES_MONTH', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYearMonth($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE,
				SHOP_NUM,
				SHOPITEM_NUM,
				PAYCODE_NUM,
				SUM(AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					a.CREATE_DATE, a.PAYCODE_NUM, b.SHOP_NUM, c.SHOPITEM_NUM, c.AMOUNT
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM INNER JOIN ORDERITEM c
				ON b.NUM = c.ORDERPART_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m'), PAYCODE_NUM, SHOP_NUM, SHOPITEM_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
			//해당일자 내용이 존재하는지 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM STATS_ITEMSALES_MONTH
					WHERE STATS_DATE = ".$rs['CREATE_DATE']."
					AND SHOP_NUM = ".$rs['SHOP_NUM']."
					AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는경우 data 생성
			{
				$this->db->insert(
					'STATS_ITEMSALES_MONTH',
					array(
						'SHOPITEM_NUM' => $rs['SHOPITEM_NUM'],							
						'SHOP_NUM' => $rs['SHOP_NUM'],
						'STATS_DATE' => $rs['CREATE_DATE']
					)
				);
			}
		
			$sql = "
				UPDATE STATS_ITEMSALES_MONTH
					SET
						PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
						PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
						UPDATE_DATE = '".$this->_toDate."'
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
				AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."						
			";
			$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ITEMSALES_MONTH
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}
	
	/**
	 * @method name : setItemSalesStatsYear
	 * 아이템별 년도별 매출 통계
	 *
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function setItemSalesStatsYear($qData)
	{
		$shopNum = 0;
		$itemNum = 0;
		$sDate = $qData['sDate'];
		$eDate = $qData['eDate'];
		if (empty($sDate) || empty($eDate))
		{
			//기본 2달전 데이터만 비교
			$sDate = date("Y-m-d",strtotime("-2 month"));
			$eDate = substr($this->_toDate, 0, 10);
		}
		$whSql = " AND a.CREATE_DATE BETWEEN '".$sDate." 00:00:00' AND '".$eDate." 23:59:59'";
		if (isset($qData['shopNum'])) //특정 샵만 통계작성을 하는 경우
		{
			if ($qData['shopNum'] > 0)
			{
				$shopNum = $qData['shopNum'];
				$whSql .= " AND SHOP_NUM = ".$shopNum;
			}			
		}
		
		if (isset($qData['itemNum'])) //특정 아이템만 통계작성을 하는 경우
		{
			if ($qData['itemNum'] > 0)
			{
				$itemNum = $qData['itemNum'];
				$whSql .= " AND c.SHOPITEM_NUM = ".$itemNum;
			}			
		}		
	
		//결제수단 구분을 위해 컬럼 추가
		foreach ($this->_payCodeGrpList as $prs):
			$this->setColumn('STATS_ITEMSALES_YEAR', 'PAY_'.$prs['NUM'].'_COUNT', 'int');
			$this->setColumn('STATS_ITEMSALES_YEAR', 'PAY_'.$prs['NUM'].'_AMOUNT', 'int');
		endforeach;
		$this->setYear($sDate, $eDate); //일자 생성
	
		$sql = "
			SELECT
				DATE_FORMAT(CREATE_DATE, '%Y%m') AS CREATE_DATE,
				SHOP_NUM,
				SHOPITEM_NUM,
				PAYCODE_NUM,
				SUM(AMOUNT) AS AMOUNT,
				COUNT(*) AS COUNT
			FROM
			(
				SELECT
					a.CREATE_DATE, a.PAYCODE_NUM, b.SHOP_NUM, c.SHOPITEM_NUM, c.AMOUNT
				FROM ORDERS a INNER JOIN ORDERPART b
				ON a.NUM = b.ORDERS_NUM INNER JOIN ORDERITEM c
				ON b.NUM = c.ORDERPART_NUM
				WHERE a.DEL_YN = 'N'
				AND b.DEL_YN = 'N'
				AND b.ORDSTATECODE_NUM IN (5080, 5220, 5530, 5380)
				".$whSql."
			) tb
			GROUP BY DATE_FORMAT(CREATE_DATE, '%Y%m'), PAYCODE_NUM, SHOP_NUM, SHOPITEM_NUM
		";
		$result = $this->db->query($sql)->result_array();
	
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
	
		foreach ($result as $rs):
		//해당일자 내용이 존재하는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_ITEMSALES_YEAR
				WHERE STATS_DATE = ".$rs['CREATE_DATE']."
				AND SHOP_NUM = ".$rs['SHOP_NUM']."
				AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
			) AS RESULT
		");
		$isExist = $this->db->get()->row()->RESULT;
		if (!$isExist) //없는경우 data 생성
		{
			$this->db->insert(
				'STATS_ITEMSALES_YEAR',
				array(
					'SHOP_NUM' => $rs['SHOP_NUM'],
					'SHOPITEM_NUM' => $rs['SHOPITEM_NUM'],							
					'STATS_DATE' => $rs['CREATE_DATE']
				)
			);
		}
	
		$sql = "
			UPDATE STATS_ITEMSALES_YEAR
				SET
					PAY_".$rs['PAYCODE_NUM']."_COUNT = ".$rs['COUNT'].",
					PAY_".$rs['PAYCODE_NUM']."_AMOUNT = ".$rs['AMOUNT'].",
					UPDATE_DATE = '".$this->_toDate."'
			WHERE STATS_DATE = ".$rs['CREATE_DATE']."
			AND SHOP_NUM = ".$rs['SHOP_NUM']."
			AND SHOPITEM_NUM = ".$rs['SHOPITEM_NUM']."
		";
		$this->db->query($sql);
		endforeach;
		$result = $this->db->affected_rows();
	
		//결제 수단별 총합을 결제확인 카운트에 다시 반영
		$sql = "
			UPDATE STATS_ITEMSALES_YEAR
				SET
					TOTSELL_COUNT = (
						PAY_5510_COUNT+
						PAY_5520_COUNT+
						PAY_5530_COUNT+
						PAY_5560_COUNT
					),
					TOTSELL_AMOUNT = (
						PAY_5510_AMOUNT+
						PAY_5520_AMOUNT+
						PAY_5530_AMOUNT+
						PAY_5560_AMOUNT
					)
			WHERE DATE_FORMAT(UPDATE_DATE, '%Y-%m-%d') = '".substr($this->_toDate, 0, 10)."'
		";
		$this->db->query($sql);
	
		//Transaction 자동 커밋
		$this->db->trans_complete();
	
		return $result;
	}	
	
	/**
	 * @method name : setColumn
	 * 컬럼 생성 
	 * 
	 * @param unknown $tableName
	 * @param unknown $colname
	 * @param unknown $colType
	 * @param number $colLength
	 */
	public function setColumn($tableName, $colname, $colType, $colLength = 20)
	{
		$this->db->select("
			EXISTS (
				SELECT 1 FROM Information_schema.columns
				WHERE table_schema = '".$this->db->database."' 
				AND table_name = '".$tableName."' 
				AND column_name = '".$colname."'
			) AS RESULT				
		");
		$result = $this->db->get()->row()->RESULT;
		
		if (!$result)
		{
			//컬럼 생성
			if ($colType == 'int')
			{
				$sql = "ALTER TABLE ".$tableName." ADD ".$colname." INT DEFAULT 0";				
			}
			else 
			{
				$sql = "ALTER TABLE ".$tableName." ADD ".$colname." VARCHAR(".$colLength.")";
			}

			$this->db->query($sql);
		}
	}

	/**
	 * @method name : setYearMonthDay
	 * 일자 생성
	 * 
	 * @param unknown $tableName
	 * @param unknown $colname
	 * @param unknown $startDate
	 * @param unknown $endDate
	 * @param unknown $idxNum
	 */
	public function setYearMonthDay($startDate, $endDate)
	{
		//일자가 기 생성되어 있는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".str_replace('-', '', $startDate)."
				AND DATE_TYPE = 'D'
			) AS RESULT
		");
		$stExist = $this->db->get()->row()->RESULT;		
		
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".str_replace('-', '', $endDate)."
				AND DATE_TYPE = 'D'
			) AS RESULT
		");
		$enExist = $this->db->get()->row()->RESULT;		
		
		if (!$stExist || !$enExist) //둘중 하나라도 생성된 일자가 없다면
		{
			for($i=strtotime($startDate),$e=strtotime($endDate);$i<=$e;$i+=86400)
			{
				//echo $i.'<br />';
				$dt = date('Y-m-d',$i);
				$dt = str_replace('-', '', $dt);
			
				$this->db->select("
					EXISTS (
						SELECT 1 FROM STATS_DATE WHERE STATS_DATE = ".$dt." AND DATE_TYPE = 'D'
					) AS RESULT
				");
				$isExist = $this->db->get()->row()->RESULT;
					
				if (!$isExist)
				{
					$insDt = array(
						'STATS_DATE' => $dt,
						'DATE_TYPE' => 'D'
					);
					$this->db->insert('STATS_DATE', $insDt);
				}
			}
		}
	}
	
	/**
	 * @method name : setYearWeek
	 * 년주차 생성 
	 * 
	 * @param unknown $startDate
	 * @param unknown $endDate
	 */
	public function setYearWeek($startDate, $endDate)
	{
		$stYear = substr($startDate, 0, 4);
		$enYear = substr($endDate, 0, 4);
		//일자가 기 생성되어 있는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".$stYear."01
				AND DATE_TYPE = 'W'
			) AS RESULT
		");
		$stExist = $this->db->get()->row()->RESULT;
		
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".$enYear."01
				AND DATE_TYPE = 'W'
			) AS RESULT
		");
		$enExist = $this->db->get()->row()->RESULT;
		
		if (!$stExist || !$enExist) //둘중 하나라도 생성된 일자가 없다면
		{
			for($i=0, $e=(intval($enYear)-intval($stYear)); $i<=$e; $i++)
			{
				$dt = intval($stYear) + $i;
				for($t=1; $t<=53; $t++) //총 53주차
				{
					$dtWeek = $dt.str_pad($t, 2, '0', STR_PAD_LEFT);
					$this->db->select("
						EXISTS (
							SELECT 1 FROM STATS_DATE WHERE STATS_DATE = ".$dtWeek." AND DATE_TYPE = 'W'
						) AS RESULT
					");
					$isExist = $this->db->get()->row()->RESULT;
			
					if (!$isExist)
					{
						$insDt = array(
							'STATS_DATE' => $dtWeek,
							'DATE_TYPE' => 'W'
						);						
						$this->db->insert('STATS_DATE', $insDt);
					}
				}
			}			
		}
	}	
	
	/**
	 * @method name : setYearMonth
	 * 일자생성(년월) 
	 * 
	 * @param unknown $startDate
	 * @param unknown $endDate
	 */
	public function setYearMonth($startDate, $endDate)
	{
		$stDate = new DateTime($startDate);
		$enDate = new DateTime($endDate);
		//일자가 기 생성되어 있는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".date('Ym', strtotime($startDate))."
				AND DATE_TYPE = 'M'
			) AS RESULT
		");
		$stExist = $this->db->get()->row()->RESULT;
		
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".date('Ym', strtotime($endDate))."
				AND DATE_TYPE = 'M'
			) AS RESULT
		");
		$enExist = $this->db->get()->row()->RESULT;		
		
		if (!$stExist || !$enExist) //둘중 하나라도 생성된 일자가 없다면
		{
			$interval = date_diff($stDate, $enDate);
			$mn = $interval->format('%m'); //날짜간 개월수 차이
			$dt = date('Ym', strtotime($startDate));
				
			for($i=0; $i<=($mn+1); $i++)
			{
				$this->db->select("
					EXISTS (
						SELECT 1 FROM STATS_DATE WHERE STATS_DATE = ".$dt." AND DATE_TYPE = 'M'
					) AS RESULT
				");
				$isExist = $this->db->get()->row()->RESULT;
					
				if (!$isExist)
				{
					$insDt = array(
						'STATS_DATE' => $dt,
						'DATE_TYPE' => 'M'
					);	
					$this->db->insert('STATS_DATE', $insDt);
				}
					
				$dt = date('Ym', strtotime($startDate.'+'.$i.' month'));
			}
		}
	}
	
	/**
	 * @method name : setYear
	 * 일자생성(년)
	 * 
	 * @param unknown $startDate
	 * @param unknown $endDate
	 */
	public function setYear($startDate, $endDate)
	{
		$stYear = substr($startDate, 0, 4);
		$enYear = substr($endDate, 0, 4);
		//일자가 기 생성되어 있는지 확인
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".$stYear."
				AND DATE_TYPE = 'Y'
			) AS RESULT
		");
		$stExist = $this->db->get()->row()->RESULT;
		
		$this->db->select("
			EXISTS (
				SELECT 1 FROM STATS_DATE
				WHERE STATS_DATE = ".$enYear."
				AND DATE_TYPE = 'Y'
			) AS RESULT
		");
		$enExist = $this->db->get()->row()->RESULT;
		
		if (!$stExist || !$enExist) //둘중 하나라도 생성된 일자가 없다면
		{
			$dt = intval($stYear);
			for($i=0, $e=(intval($enYear)-intval($stYear)); $i<=$e; $i++)
			{
				$this->db->select("
					EXISTS (
						SELECT 1 FROM STATS_DATE WHERE STATS_DATE = ".($dt + $i)." AND DATE_TYPE = 'Y' 
					) AS RESULT
				");
				$isExist = $this->db->get()->row()->RESULT;
			
				if (!$isExist)
				{
					$insDt = array(
						'STATS_DATE' => ($dt + $i),
						'DATE_TYPE' => 'Y'
					);	
					$this->db->insert('STATS_DATE', $insDt);
				}
			}			
		}
	}	
}