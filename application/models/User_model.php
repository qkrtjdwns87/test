<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User_model
 * 
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class User_model extends CI_Model{

	protected $_query;
	
	protected $_encKey = '';
	
	protected $_tblCodeNum = 0;	
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'USER';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 주로 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);		
	}
	
	/**
	 * @method name : getUserDataList
	 * 회원리스트
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getUserDataList($qData, $isDelView = FALSE)
	{
		$whSql = 'ULEVELCODE_NUM > 600 AND ULEVELCODE_NUM < 800';
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : "";
		if (isset($qData['searchKey']) && isset($qData['searchWord']))
		{
			$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ? " AND a.".$qData['searchKey']." LIKE '%a.".$qData['searchWord']."%'" : "";			
		}
		
		if (isset($qData['userState']))
		{
			$whSql .= (!empty($qData['userState'])) ? " AND USTATECODE_NUM = '".$qData['userState']."'" : "";			
		}
		
		if (isset($qData['userLevel']))
		{
			$whSql .= (!empty($qData['userLevel'])) ? " AND ULEVELCODE_NUM = ".$qData['userLevel'] : "";			
		}
		
		if (isset($qData['userEmail']))
		{
			$emailEnc = $this->common->sqlEncrypt($qData['userEmail'], $this->_encKey);			
			$whSql .= (!empty($qData['userEmail'])) ? " AND USER_EMAIL = '".$emailEnc."'" : "";			
		}
		
		if (isset($qData['userMobile']))
		{
			$mobileEnc = $this->common->sqlEncrypt($qData['userMobile'], $this->_encKey);			
			$whSql .= (!empty($qData['userMobile'])) ? " AND USER_MOBILE = '".$mobileEnc."'" : "";			
		}
		
		if (isset($qData['userName']))
		{
			$whSql .= (!empty($qData['userName'])) ? " AND USER_NAME = '".$qData['userName']."'" : "";			
		}
		
		if (isset($qData['userGender']))
		{
			$whSql .= (!empty($qData['userGender'])) ? " AND USER_GENDER = '".$qData['userGender']."'" : "";			
		}
		
		if (isset($qData['emailYn']))
		{
			$whSql .= (!empty($qData['emailYn'])) ? " AND EMAIL_YN = '".$qData['emailYn']."'" : "";			
		}
		
		if (isset($qData['smsYn']))
		{
			$whSql .= (!empty($qData['smsYn'])) ? " AND SMS_YN = '".$qData['smsYn']."'" : "";			
		}
		
		if (isset($qData['leaveAdminYn']))
		{
			$whSql .= (!empty($qData['leaveAdminYn'])) ? " AND LEAVE_ADMIN_YN = '".$qData['leaveAdminYn']."'" : "";			
		}
		
		if (isset($qData['leaveReason']))
		{
			$whSql .= (!empty($qData['leaveReason'])) ? " AND LEAVE_RESONCODE_NUM = '".$qData['leaveReason']."'" : "";			
		}
		
		if (isset($qData['pageMethod']))
		{
			$whSql .= ($qData['pageMethod'] == 'leavelist') ? ' AND USTATECODE_NUM = 980' : ' AND USTATECODE_NUM < 980';
			
			if ($qData['pageMethod'] == 'leavelist')
			{
				$whSql .= (!empty($qData['sDate']) && !empty($qData['eDate'])) ? " AND LEAVE_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' " : "";
			}
			else
			{
				$whSql .= (!empty($qData['sDate']) && !empty($qData['eDate'])) ? " AND CREATE_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' " : "";
			}			
		}

		if (isset($qData['logincheckDay']))
		{
			if ((!empty($qData['logincheckDay'])))
			{
				if ($qData['logincheckDay'] > 0)
				{
					$compDate = date("Y-m-d",strtotime("-".$qData['logincheckDay']." day"));
					$whSql .= " AND LASTLOGIN_DATE < '".$compDate."' OR LASTLOGIN_DATE IS NULL";
				}
			}			
		}
				
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
			AES_DECRYPT(UNHEX(USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".ULEVELCODE_NUM) AS ULEVELCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".USTATECODE_NUM) AS USTATECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".LEAVE_RESONCODE_NUM) AS LEAVE_RESONCODE_TITLE,
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
	 * @method name : getFollowUserDataList
	 * Follower, Following User List
	 * 
	 * @param unknown $qData
	 * @param string $isDelView
	 * @return Ambiguous
	 */
	public function getFollowUserDataList($qData, $isDelView = FALSE)
	{
		$whSql = 'ULEVELCODE_NUM > 600 AND ULEVELCODE_NUM < 800';
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : "";
		if (isset($qData['pageMethod']))
		{
			if ($qData['pageMethod'] == 'followerlist') //본인을 팔로워 하고 있는 사람들(userNum은 본인의고유번호)
			{
				$whSql .= " AND NUM IN (
					SELECT USER_NUM FROM FOLLOW WHERE DEL_YN = 'N' AND TO_USER_NUM = ".$qData['userNum']."
				)";
			}
			else if ($qData['pageMethod'] == 'followinglist') //본인이 팔로윙 하고 있는 사람들(userNum은 본인의고유번호)
			{
				$whSql .= " AND NUM IN (
					SELECT TO_USER_NUM FROM FOLLOW WHERE DEL_YN = 'N' AND USER_NUM = ".$qData['userNum']."
				)";
			}	
			else if ($qData['pageMethod'] == 'followerlistuser') //타인을 팔로워 하고 있는 사람들(userNum은 타인의고유번호)
			{
				$whSql .= " AND NUM IN (
					SELECT USER_NUM FROM FOLLOW WHERE DEL_YN = 'N' AND TO_USER_NUM = ".$qData['userNum']."
				)";
			}
			else if ($qData['pageMethod'] == 'followinglistuser') //타인이 팔로윙 하고 있는 사람들(userNum은 타인의고유번호)
			{
				$whSql .= " AND NUM IN (
					SELECT TO_USER_NUM FROM FOLLOW WHERE DEL_YN = 'N' AND USER_NUM = ".$qData['userNum']."
				)";
			}			
		}
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			NUM,
			USER_NAME,
			USER_GENDER,
			USER_BIRTH,
			TOTORDER_COUNT,
			TOTITEMFLAG_COUNT,
			TOTSHOPFLAG_COUNT,
			TOTSTORYFLAG_COUNT,
			TOTSENDMSG_COUNT,
			FOLLOWER_COUNT,
			FOLLOWING_COUNT,
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
			AES_DECRYPT(UNHEX(USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".ULEVELCODE_NUM) AS ULEVELCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".USTATECODE_NUM) AS USTATECODE_TITLE,
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
	 * @method name : isSNSUserRegistered
	 * SNS를 통해 회원가입된 이력이 있는지 확인 
	 * 
	 * @param unknown $qData
	 * @return boolean
	 */
	public function isSNSUserRegistered($qData)
	{
		$emailEnc = $this->common->sqlEncrypt($qData['SNS_EMAIL'], $this->_encKey);		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		//$this->db->where("SNSCODE_NUM = ".$qData['SNSCODE_NUM']);		
		$this->db->where("SNS_EMAIL = '".$emailEnc."'");
		$totalCount = $this->db->get()->row()->COUNT;
		
		return ($totalCount > 0) ? TRUE : FALSE;
	}
	
	/**
	 * @method name : getUserRowData
	 * 회원정보 조회 
	 * 
	 * @param unknown $qData
	 * @param string $searchKey (sns, email, num[USER.NUM])
	 */
	public function getUserRowData($qData = array(), $searchKey = 'num')
	{
		$result = array();
		if ($searchKey == 'snsemail')
		{
			$emailEnc = $this->common->sqlEncrypt($qData['SNS_EMAIL'], $this->_encKey);
			$this->db->select("
				*,
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
			$this->db->where("SNSCODE_NUM = ".$qData['SNSCODE_NUM']);			
			$this->db->where("SNS_EMAIL = '".$emailEnc."'");
			$result = $this->db->get()->row_array();			
		}
		else if ($searchKey == 'snsid') //SNS ID로만 조회
		{
			$this->db->select("
				*,
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
			$this->db->where('SNS_ID', $qData['SNS_ID']);
			$this->db->where('SNSCODE_NUM', $qData['SNSCODE_NUM']);
			$result = $this->db->get()->row_array();
		}		
		else if ($searchKey == 'email')
		{
			$emailEnc = $this->common->sqlEncrypt($qData['USER_EMAIL'], $this->_encKey);
			//USER정보와 USER_HISTORY USTATECODE_NUM의 최근상태 하나를 가져온다
			$sql = "
				SELECT 
					*,
					AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
					AES_DECRYPT(UNHEX(USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
					AES_DECRYPT(UNHEX(USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,					
					IFNULL((
						SELECT USTATECODE_NUM FROM ".$this->tbl."_HISTORY 
						WHERE USER_NUM = ".$this->tbl.".NUM ORDER BY NUM DESC LIMIT 1
					), 0) AS USTATECODE_NUM,
					IFNULL((
						SELECT NUM FROM SHOP 
						WHERE USER_NUM = ".$this->tbl.".NUM LIMIT 1
					), 0) AS SHOP_NUM,
					(
						SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
						FROM PROFILE_FILE
						WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
						AND TBL_NUM = ".$this->tbl.".NUM 
						AND DEL_YN = 'N'
						ORDER BY NUM LIMIT 1
					) AS PROFILE_FILE_INFO										
				FROM ".$this->tbl."			
				WHERE USER_EMAIL = '".$emailEnc."'
			";
			
			$result = $this->db->query($sql)->row_array();			
		}
		else
		{
			$uNum = (!isset($qData['NUM'])) ? $this->common->getSession('user_num') : $qData['NUM'];
			//회원고유 번호로 조회
			$this->db->select("
				*,
				AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
				AES_DECRYPT(UNHEX(USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC,
				AES_DECRYPT(UNHEX(USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
				(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".ULEVELCODE_NUM) AS ULEVELCODE_TITLE,
				(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".USTATECODE_NUM) AS USTATECODE_TITLE,
				(
					SELECT 
						CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
					FROM COMMON_FILE
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = ".$this->tbl.".NUM
					AND DEL_YN = 'N' 
					ORDER BY NUM LIMIT 1
				) AS FILE_INFO,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$this->_tblCodeNum."
					AND TBL_NUM = ".$this->tbl.".NUM 
					AND DEL_YN = 'N'
					ORDER BY NUM LIMIT 1
				) AS PROFILE_FILE_INFO,					
				IFNULL((
					SELECT NUM FROM SHOP 
					WHERE USER_NUM = ".$this->tbl.".NUM LIMIT 1
				), 0) AS SHOP_NUM					
			");
			$this->db->limit(1);
			$this->db->from($this->tbl);
			$this->db->where('NUM', $uNum);
			$result = $this->db->get()->row_array();
		}
		
		return $result;
	}
	
	/**
	 * @method name : setSnsUserDataInsert
	 * SNS 신규회원등록
	 * 
	 * @param unknown $uData
	 * @return number
	 */
	public function setSnsUserDataInsert($insData)
	{
		$resultNum = 0;
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		
		return $resultNum;
	}
	
	/**
	 * @method name : setUserDataInsert
	 * 신규 회원가입
	 * 
	 * @param array $insData
	 * @param unknown $isUpload
	 * @return int
	 */
	public function setUserDataInsert($insData, $isUpload = FALSE){
		$resultNum = 0;
		$uStatCodeNum = 0;
		
		if (isset($insData['USTATECODE_NUM']))
		{
			$uStatCodeNum = $insData['USTATECODE_NUM'];
			//unset($insData['USTATECODE_NUM']);
		}
		
		if (!isset($insData['LEAVE_RESONCODE_NUM']))
		{
			$insData['LEAVE_RESONCODE_NUM'] = $this->common->getCodeNumByCodeGrpNCodeId('LEAVE_REASON', 'NONE');
		}		
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		
		//히스토리 처리
		$this->db->insert(
			$this->tbl.'_HISTORY',
			array(
				'USER_NUM' => $resultNum,
				'REMOTEIP' => $this->input->ip_address(),
				'USTATECODE_NUM' => ($uStatCodeNum == 0) ? $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'NONE') : $uStatCodeNum						
			)
		);
		$hisNum = $this->db->insert_id();
		
		//마지막 히스토리 번호 update
		$this->db->set('LASTHISTORY_NUM', $hisNum);
		$this->db->where('NUM', $resultNum);
		$this->db->update($this->tbl);
		
		if ($isUpload)
		{
			//추가할 FILE 컬럼을 config에 같이 추가
			$upConfig = array_merge(
				$this->getUploadOption('/profile/'.strtolower($this->tbl).'/'.$resultNum.'/'),
				array(
					'TBLCODE_NUM' => $this->_tblCodeNum,
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
					$this->db->insert('PROFILE_FILE', $uploadResult[$i]);
				}
			}
		}		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $resultNum;
	}	
	
	/**
	 * @method name : setUserDataUpdate
	 * user정보 update 
	 * 
	 * @param unknown $uNum
	 * @param unknown $upData
	 * @param string $isUpload
	 * @return number
	 */
	public function setUserDataUpdate($uNum, $upData, $isUpload = FALSE)
	{
		$result = 0;
		if ($uNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$upData['UPDATE_DATE'] = date('Y-m-d H:i:s');
			$this->db->where('NUM', $uNum);
			$this->db->update($this->tbl, $upData);
			$result = $this->db->affected_rows();
			
			if ($isUpload)
			{
				//추가할 FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/profile/'.strtolower($this->tbl).'/'.$uNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $uNum
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
						$this->db->from('PROFILE_FILE');
						$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
						$this->db->where('TBL_NUM', $uNum);
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
					}
				}
			}			
			
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $result;
	}
	
	/**
	 * @method name : getShopInfoByUserNum
	 * 회원고유번호로 샵정보 조회
	 * 
	 * @param int $uNum
	 * @return array
	 */
	public function getShopInfoByUserNum($uNum)
	{
		$this->db->select('*');		
		$this->db->limit(1);
		$this->db->from('SHOP');
		$this->db->where("USER_NUM = ".$uNum);
		$result = $this->db->get()->row_array();
		
		return $result;
	}
	
	/**
	 * @method name : setUserLastLoginUpdate
	 * 로그인 최종 일자 update
	 * 
	 * @param int $uNum
	 */
	public function setUserLastLoginUpdate($uNum)
	{
		$this->db->set('LASTLOGIN_DATE', date('Y-m-d H:i:s'));
		$this->db->set('LASTLOGIN_REMOTEIP', $this->input->ip_address());		
		$this->db->where('NUM', $uNum);
		$this->db->update($this->tbl);
	}
	
		/**
	 * @method name : setItemDataChange
	 * 상태변경 버튼별 액션 처리
	 * 
	 * @param unknown $method
	 * @param unknown $selValue
	 * @return number
	 */
	public function setUserDataChange($method, $selValue)
	{
		$result = 0;
		$selValue = explode(',', $selValue);
		
		if (is_array($selValue))
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			foreach ($selValue as $val)
			{
				if ($method == 'dormant')
				{
					//휴면처리
					$userStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'DORMANT');
					$this->db->set('USTATECODE_NUM', $userStateCodeNum);
					$this->db->set('DORMANT_DATE', date('Y-m-d H:i:s'));
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
				else if ($method == 'delete')
				{
					//탈퇴처리
					$sql = 'CREATE TABLE IF NOT EXISTS '.$this->tbl.'_TMP LIKE USER';
					$this->db->query($sql);
					$sql = 'INSERT INTO '.$this->tbl.'_TMP SELECT * FROM USER WHERE NUM = '.$val;
					$this->db->query($sql);
						
					$leaveResonCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('LEAVE_REASON', 'ETC');
					$userStateCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERSTATE', 'LEAVE');
					$emailEnc = $this->common->sqlEncrypt('circus_del_'.$val.'@circus.ci', $this->_encKey);
					$this->db->set('SNS_ID', '');
					$this->db->set('SNS_NAME', '');
					$this->db->set('SNS_NICK', '');
					$this->db->set('SNS_EMAIL', '');
					$this->db->set('SNSPROFILE_IMG', '');
					$this->db->set('USER_BIRTH', '');
					$this->db->set('USER_TEL', '');
					$this->db->set('USER_MOBILE', '');
					//$this->db->set('USER_EMAIL', $emailEnc); //이메일은 그대로 둬야 할지?
					$this->db->set('USTATECODE_NUM', $userStateCodeNum);
					$this->db->set('LEAVE_RESONCODE_NUM', $leaveResonCodeNum);
					$this->db->set('LEAVE_RESON', '관리자에 의한 탈퇴처리');
					$this->db->set('DEL_YN', 'Y');
					$this->db->set('LEAVE_DATE', date('Y-m-d H:i:s'));
					$this->db->set('LEAVE_ADMIN_YN', 'Y');
					$this->db->where('NUM', $val);
					$this->db->update($this->tbl);
					$result = $this->db->affected_rows();
				}
			}
				
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $result;		
	}
	
	/**
	 * @method name : getManagerDataList
	 * 관리자 리스트 
	 * 
	 * @param unknown $qData
	 * @param unknown $isDelView
	 * @return Ambiguous
	 */
	public function getManagerDataList($qData, $isDelView)
	{
		if (!empty($qData['userEmail']))
		{
			$emailEnc = $this->common->sqlEncrypt($qData['userEmail'], $this->_encKey);			
		}
		$whSql = "ULEVELCODE_NUM BETWEEN 610 AND 630";
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : '';		
		$whSql .= ($qData['userUseYn'] == 'Y') ? " AND USE_YN = 'Y'" : ""; //이용중인 관리자만
		$whSql .= (!empty($qData['userName'])) ? " AND USER_NAME = '".$qData['userName']."'" : "";
		$whSql .= (!empty($qData['userEmail'])) ? " AND USER_EMAIL = '".$emailEnc."'" : "";

		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
		
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".ULEVELCODE_NUM) AS ULEVELCODE_TITLE,
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			AES_DECRYPT(UNHEX(USER_TEL), '".$this->_encKey."') AS USER_TEL_DEC,
			AES_DECRYPT(UNHEX(USER_MOBILE), '".$this->_encKey."') AS USER_MOBILE_DEC
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
	 * @method name : setSnsUserEmailUpdate
	 * SNS 로그인후 이메일인증 진행시 
	 * 
	 * @param unknown $uNum
	 * @param unknown $email
	 */
	public function setSnsUserEmailUpdate($uNum, $emailEnc)
	{

		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from($this->tbl);
		$this->db->where('USER_EMAIL', $emailEnc);
		$cnt = $this->db->get()->row()->COUNT;

		if($cnt == 0){ 

			$uLevelCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'SNS');
			//$emailEnc = $this->common->sqlEncrypt($email, $this->_encKey);
			$this->db->set('USER_EMAIL', $emailEnc);
			//$this->db->set('SNS_EMAIL', $emailEnc); //SNS원본 이메일 주소는 그대로 유지
			$this->db->set('ULEVELCODE_NUM', $uLevelCodeNum);
			$this->db->where('NUM', $uNum);
			$this->db->update($this->tbl);
			$result = $this->db->affected_rows();
		}else{
			$result=0;	
		}
		return $result;		
	}


	/**
	 * @method name : setProfileFileDelete
	 * 프로필 파일 삭제
	 *
	 * @param int $fNum
	 */
	public function setProfileFileDelete($fNum)
	{
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('NUM', $fNum);
		$this->db->update('PROFILE_FILE');
	
		return $this->db->affected_rows();
	}
	
	/**
	 * @method name : setUserPasswordReissue
	 * 임시 비밀번호 발송
	 * 
	 * @param unknown $reqType
	 * @param unknown $userInfo
	 * @return Ambiguous
	 */
	public function setUserPasswordReissue($reqType, $userInfo)
	{
		$tmpPasswd = mt_rand(1000000,9999999); //7자리 랜덤 생성
		$tmpPasswdEnc = sha1($tmpPasswd);
		
		$insData = array(
			'USER_NUM' => $userInfo['NUM'],
			'TMP_PASSWD' => $tmpPasswdEnc,
			'REMOTEIP' =>  $this->input->ip_address()				
		);
		
		$this->db->insert('USER_PASSWD', $insData);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM

		//여기서 회원테이블(USER)의 내용도 비번을 동기화 하려면 아래 주석 제거
		/*
		$this->db->set('USER_PASS', $tmpPasswdEnc);
		$this->db->where('NUM', $userInfo['NUM']);
		$this->db->update($this->tbl);		
		*/
		
		return $tmpPasswd;
	}
	
	/**
	 * @method name : getPasswordReissueNumber
	 * 임시 비밀번호 확인 
	 * 
	 * @param unknown $uNum
	 * @param unknown $passwd
	 * @return boolean
	 */
	public function getPasswordReissueNumber($uNum, $passwd)
	{
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('USER_PASSWD');
		$this->db->where('TMP_PASSWD', $passwd);
		$this->db->where('USER_NUM', $uNum);
		$this->db->where('CREATE_DATE > DATE_ADD(now(), INTERVAL -24 hour)');
		$result = $this->db->get()->row()->COUNT;

		return ($result == 0) ? FALSE : TRUE;
	}
	
	/**
	 * @method name : setAppInfoUpdate
	 * 사용자 앱고유정보 관리 
	 * 사용자 고유번호와 매칭되는 deviceid와 pushid 는 모두 N의 관계
	 * (1, 1a, 1p) - (1, 1a, 2p) - (1, 2a, 2p) 등과 같은 조합이 나올수 있음
	 * -> 
	 * deviceid, pushid는 1개만 존재로 변경 
	 * 사용자 고유번호와 매칭되는 deviceid와 pushid 는 합쳐서 N의 관계
	 * (1, 1a, 1p) - (1, 2a, 2p) - (1, 3a, 3p) 등과 같은 조합이 나올수 있음
	 * (1, 1a, 2p)(X)
	 * @param unknown $uNum
	 * @param unknown $deviceId
	 * @param unknown $pushId
	 * @return number
	 */
	public function setAppInfoUpdate($uNum, $deviceId, $pushId)
	{
		$resultNum = 0;
		$this->db->select("
			EXISTS (
				SELECT 1 FROM USER_APPINFO
				WHERE USER_NUM = ".$uNum."
				AND DEL_YN = 'N'
			) AS RESULT
		");
		$isExist = $this->db->get()->row()->RESULT;
		if (!$isExist) //없는 경우
		{
			$insData = array(
				'USER_NUM' => $uNum,
				'DEVICE_ID' => $deviceId,
				'PUSH_ID' => $pushId,
				'REMOTEIP' => $this->input->ip_address()
			);
			$this->db->insert('USER_APPINFO', $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM			
		}
		else 
		{
			//deviceid 여부 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM USER_APPINFO
					WHERE USER_NUM = ".$uNum."
					AND DEVICE_ID = '".$deviceId."'
					AND DEL_YN = 'N'
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는 경우
			{
				$insData = array(
					'USER_NUM' => $uNum,
					'DEVICE_ID' => $deviceId,
					'PUSH_ID' => $pushId,
					'REMOTEIP' => $this->input->ip_address()
				);
				$this->db->insert('USER_APPINFO', $insData);
				$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM				
			}
			
			//pushid 여부 확인
			$this->db->select("
				EXISTS (
					SELECT 1 FROM USER_APPINFO
					WHERE USER_NUM = ".$uNum."
					AND PUSH_ID = '".$pushId."'
					AND DEL_YN = 'N'
				) AS RESULT
			");
			$isExist = $this->db->get()->row()->RESULT;
			if (!$isExist) //없는 경우
			{
				$insData = array(
					'USER_NUM' => $uNum,
					'DEVICE_ID' => $deviceId,
					'PUSH_ID' => $pushId,
					'REMOTEIP' => $this->input->ip_address()
				);
				$this->db->insert('USER_APPINFO', $insData);
				$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
			}			
		}
		
		return $resultNum;
	}
	
	/**
	 * @method name : setFlagOpenUpdate
	 * 플래그 공개 여부 설정
	 * 
	 * @param unknown $uNum
	 * @param unknown $openYn
	 */
	public function setFlagOpenUpdate($uNum, $openYn)
	{
		$this->db->set('FLAGOPEN_YN', strtoupper($openYn));
		$this->db->where('NUM', $uNum);
		$this->db->update($this->tbl);

		return $this->db->affected_rows();
	}
	
	/**
	 * @method name : setFollow
	 * 팔로윙 또는 팔로워 하기
	 * 
	 * @param unknown $qData
	 * @return number
	 */
	public function setFollow($qData)
	{
		$result = 0;
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		if ($qData['pageMethod'] == 'follower') //본인을 팔로윙하는 경우
		{
			$sql = "
				SELECT * FROM FOLLOW
				WHERE USER_NUM = ".$qData['toUserNum']."
				AND TO_USER_NUM = ".$qData['userNum']."
				AND DEL_YN = 'N'
			";
			$foDt = $this->db->query($sql)->row_array();
			if ($foDt)
			{
				if ($foDt['DEL_YN'] == 'N')
				{
					$this->db->set('DEL_YN', 'Y');
					$this->db->where('NUM', $foDt['NUM']);
					$this->db->update('FOLLOW');
					$result = 0; //언팔로윙한 상태
				}
				else 
				{
					$this->db->set('DEL_YN', 'N');
					$this->db->where('NUM', $foDt['NUM']);
					$this->db->update('FOLLOW');
					$result = 1; //팔로윙한 상태					
				}
			}
			else 
			{
				$this->db->set('USER_NUM', $qData['toUserNum']);
				$this->db->set('TO_USER_NUM', $qData['userNum']);
				$this->db->insert('FOLLOW');
				$result = $this->db->insert_id();
			}
		}
		else if ($qData['pageMethod'] == 'following') //본인이 팔로워하는 경우
		{
			$sql = "
				SELECT * FROM FOLLOW
				WHERE USER_NUM = ".$qData['userNum']."
				AND TO_USER_NUM = ".$qData['toUserNum']."
				AND DEL_YN = 'N'
			";
			$foDt = $this->db->query($sql)->row_array();
			if ($foDt)
			{
				if ($foDt['DEL_YN'] == 'N')
				{
					$this->db->set('DEL_YN', 'Y');
					$this->db->where('NUM', $foDt['NUM']);
					$this->db->update('FOLLOW');
					$result = 0; //언팔로윙한 상태
				}
				else 
				{
					$this->db->set('DEL_YN', 'N');
					$this->db->where('NUM', $foDt['NUM']);
					$this->db->update('FOLLOW');
					$result = 1; //팔로윙한 상태					
				}
			}
			else
			{
				$this->db->set('USER_NUM', $qData['userNum']);
				$this->db->set('TO_USER_NUM', $qData['toUserNum']);
				$this->db->insert('FOLLOW');
				$result = $this->db->insert_id();
			}			
		}
		
		//본인을 팔로워하는 총개수 업데이트
		$this->db->set('FOLLOWER_COUNT', "(SELECT COUNT(*) FROM FOLLOW WHERE DEL_YN = 'N' AND TO_USER_NUM = ".$qData['userNum'].")", FALSE);
		$this->db->where('NUM', $qData['userNum']);
		$this->db->update($this->tbl);		

		//본인이 팔로윙하는 총개수 업데이트
		$this->db->set('FOLLOWING_COUNT', "(SELECT COUNT(*) FROM FOLLOW WHERE DEL_YN = 'N' AND USER_NUM = ".$qData['userNum'].")", FALSE);
		$this->db->where('NUM', $qData['userNum']);
		$this->db->update($this->tbl);
		
		//상대방을 팔로워하는 총개수 업데이트
		$this->db->set('FOLLOWER_COUNT', "(SELECT COUNT(*) FROM FOLLOW WHERE DEL_YN = 'N' AND TO_USER_NUM = ".$qData['toUserNum'].")", FALSE);
		$this->db->where('NUM', $qData['toUserNum']);
		$this->db->update($this->tbl);
		
		//상대방이 팔로윙하는 총개수 업데이트
		$this->db->set('FOLLOWING_COUNT', "(SELECT COUNT(*) FROM FOLLOW WHERE DEL_YN = 'N' AND USER_NUM = ".$qData['toUserNum'].")", FALSE);
		$this->db->where('NUM', $qData['toUserNum']);
		$this->db->update($this->tbl);		
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $result;
	}
	
	/**
	 * @method name : setAppPushConfigUpdate
	 * 앱 푸시설정 업데이트
	 * 
	 * @param unknown $qData
	 * @param unknown $upData
	 */
	public function setAppPushConfigUpdate($qData, $upData)
	{
		$this->db->where('DEVICE_ID', $qData['deviceId']);
		$this->db->where('PUSH_ID', $qData['pushId']);
		$this->db->update('USER_APPINFO', $upData);
		
		return $this->db->affected_rows();
	}
	
	public function getAppPushConfigData($qData)
	{
		$this->db->select('*');
		$this->db->from('USER_APPINFO');
		$this->db->where('DEVICE_ID', $qData['deviceId']);
		$this->db->where('PUSH_ID', $qData['pushId']);
		$this->db->limit(1);
		$result = $this->db->get()->row_array();
		
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
	 * @method name : setUserPassWordUpdate
	 * user정보 update 
	 * 
	 * @param unknown $uNum
	 * @param unknown $upData
	 * @param string $isUpload
	 * @return number
	 */
	public function setUserPassWordUpdate_sp($uNum, $userPass, $isUpload = FALSE)
	{
		$result = 0;
		
		if ($uNum > 0)
		{
			$query = $this->db->query("call setUserPassWordUpdate_sp('{$uNum}','{$userPass}');");
			mysqli_next_result( $this->db->conn_id );
			$result = $this->db->affected_rows();
		}
		return $result;
	}	

	
}	
?>	