<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Board_model
 *
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Board_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_commentTbl = 'COMMON_COMMENT';
	
	protected  $_tblCodeNum = 0;
	
	protected $_encKey = '';
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'BOARD';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	/**
	 * @method name : getBoardDataList
	 * 
	 * 
	 * @param $qData 검색 조건
	 * @param bool $isDelView 삭제된 내용 보기 여부
	 * @return array
	 */
	public function getBoardDataList($qData, $isDelView = FALSE)
	{
		//data 총 갯수 select
		$toDate = date('Y-m-d');
		$whSql = "SET_NUM = ".$qData['setNum'];
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : '';
		if (isset($qData['replyState']))
		{
			if ($qData['replyState'] == 'Y')
			{
				$whSql .= ' AND REPLYCOUNT > 0';
			}
			else if ($qData['replyState'] == 'Y')
			{
				$whSql .= ' AND REPLYCOUNT = 0';
			}			
		}
		
		if (in_array($qData['setNum'], array(9100, 9110, 9130, 9140))) //샵-써커스QNA, 회원-써커스QNA, FAQ, TERMS
		{			
			$whSql .= ' AND DEPTH = 0';
			$whSql .= (!empty($qData['boardCate'])) ? ' AND CATECODE_NUM = '.$qData['boardCate'] : '';
			if (!empty($qData['replyDateType']) && !empty($qData['sDate']) && !empty($qData['eDate']))
			{
				if ($qData['replyDateType'] == 'create')
				{
					$whSql .= " AND CREATE_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' ";
				}
				else if ($qData['replyDateType'] == 'reply')
				{
					$whSql .= " AND REPLY_UPDATE_DATE BETWEEN '".$qData['sDate']." 00:00:00' AND '".$qData['eDate']." 23:59:59' ";
				}
			}			
		}
		
		if (!empty($qData['applyYn'])) //약관 사용중 여부 검색
		{
			$whSubSql = ($qData['applyYn'] == 'Y') ? "APPLY_DATE <= '".$toDate."'" : "APPLY_DATE > '".$toDate."'";
			$whSql .= " 
				AND NUM IN 
					(
						SELECT NUM
						FROM
						(
							SELECT CATECODE_NUM, MAX(NUM) AS NUM
							FROM   
							(
								SELECT NUM, CATECODE_NUM FROM ".$this->tbl."
								WHERE SET_NUM = 9140
								AND DEL_YN = 'N'
								AND ".$whSubSql."
							) tb
							GROUP BY CATECODE_NUM
						) gtb					
			)";			
		}
		
		if (!empty($qData['uNum']))
		{
			$whSql .= " AND USER_NUM = ".$qData['uNum'];
		}	
		
		if (isset($qData['urgencyYn']))
		{
			if (!empty($qData['urgencyYn']))
			{
				$whSql .= " AND URGENCY_YN = '".$qData['urgencyYn']."'";
			}
		}
		
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
			" AND ".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : '';
		
		$this->db->select('COUNT(*) AS COUNT');		
		$this->db->from($this->tbl);
		$this->db->where($whSql);		
		$totalCount = $this->db->get()->row()->COUNT;

		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = ".$this->tbl.".CATECODE_NUM) AS BOARDCATECODE_TITLE,
			(
				CASE SET_NUM IN (9100, 9110)
					WHEN CATECODE_NUM > 1300
					THEN
						(
							SELECT USER_NAME FROM USER 
						 	WHERE NUM IN (
									SELECT USER_NUM FROM ".$this->tbl."
									WHERE DEPTH = 1
									AND GROUPNUM = ".$this->tbl.".GROUPNUM
									AND DEL_YN = 'N'
			 						ORDER BY NUM DESC 
							) LIMIT 1
						)
					ELSE ''
				END
			) AS REPLYUSER_NAME,
			IF(CREATE_DATE > DATE_ADD(now(), INTERVAL -1 day), 'Y', 'N') AS NEW_YN	
		");
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$this->db->order_by('THREAD', 'DESC');
		$this->db->order_by('NUM', 'DESC');		
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();
		
		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}
	
	/**
	 * @method name : getBoardRowData
	 *
	 *
	 * @param int $bNum
	 * @param bool $isDelView TRUE: 삭제표기된 내용도 확인
	 * @return Ambiguous
	 */
	public function getBoardRowData($bNum, $isDelView)
	{
		$whSql = "a.NUM = ".$bNum;
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';		
		$this->db->select("
			a.*,
			AES_DECRYPT(UNHEX(a.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			(SELECT TITLE FROM CODE WHERE NUM = a.CATECODE_NUM) AS QNACATECODE_TITLE,
			b.USER_NAME AS REPLYUSER_NAME,
			b.CONTENT AS REPLY_CONTENT,
			b.NUM AS REPLYBOARD_NUM
		");
		$this->db->limit(1);
		$this->db->from($this->tbl.' AS a');
		$this->db->join($this->tbl.' AS b', 'a.GROUPNUM = b.GROUPNUM AND b.DEPTH = 1', 'left outer');
		$this->db->where($whSql);		
		$result['recordSet'] = $this->db->get()->row_array();
	
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
		$this->db->where("TBL_NUM = ".$bNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');		
		$result['fileSet'] = $this->db->get()->result_array();
		
		if (in_array($result['recordSet']['SET_NUM'], array(9100, 9110))) //샵-써커스QNA, 회원-써커스QNA
		{
			if ($result['recordSet']['REPLYBOARD_NUM'] > 0)
			{
				//답변내용에 해당하는 파일 리스트
				$this->db->select('*');
				$this->db->from($this->_fileTbl);
				$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
				$this->db->where("TBL_NUM = ".$result['recordSet']['REPLYBOARD_NUM']);
				$this->db->where("DEL_YN", "N");
				$this->db->order_by('FILE_ORDER', 'ASC');
				$this->db->order_by('NUM', 'ASC');
				$result['replyFileSet'] = $this->db->get()->result_array();
			}				
		}
	
		return $result;
	}	

	/**
	 * @method name : setBoardDataInsert
	 * 게시글 작성
	 * 
	 * @param int $setNum
	 * @param array $insData
	 * @param int $threadIntv
	 * @param bool $isUpload	파일 업로드 여부 (TRUE, FALSE)
	 * @return Ambiguous
	 */
	public function setBoardDataInsert($setNum, $insData, $threadIntv, $isUpload)
	{
		$thread = $threadIntv;
		$this->db->select_max('THREAD');
		$query = $this->db->get($this->tbl);
		$maxThread = $query->row()->THREAD;
		$tmp =$maxThread;
		$maxThread = $maxThread +  $thread;		
		$addData = array('THREAD' => $maxThread);
		$insData = $insData + $addData;
		
		//Transaction 시작  - 파일업로드 오류 또는 제약사항에 걸리는 경우에 대비
		if ($isUpload) $this->db->trans_begin();
		
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		
		$upData = array('GROUPNUM' => $resultNum);
		$this->db->where('NUM', $resultNum);
		$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
		
		if ($insData['CATECODE_NUM'] == 1540)
		{
			//배송 및 환불정책인 경우 기준샵에도 내용 update
			$this->setRefundPolicyUpdateToStdShop();
		}
		
		if ($resultNum > 0)
		{
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$setNum.'/'), 
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
		
					//Transaction 커밋					
					$this->db->trans_commit();
				}
			}
		}
		
		return $resultNum;
	}	

	/**
	 * @method name : setBoardReplyDataInsert
	 * 댓글달기
	 * 
	 * @param int $setNum
	 * @param int $pThread	부모글 THREAD
	 * @param int $pDepth	부모글 DEPTH
	 * @param array $insData
	 * @param int $threadIntv	Thread 간격
	 * @param bool $isUpload	파일 업로드 여부 (TRUE, FALSE)
	 * @return int	db insert 직후 고유번호 NUM
	 */
	public function setBoardReplyDataInsert($setNum, $pThread, $pDepth, $insData, $threadIntv, $isUpload)
	{
		//Transaction 시작  - 파일업로드 오류 또는 제약사항에 걸리는 경우에 대비
		if ($isUpload) $this->db->trans_begin();
		
		$thread = $pThread - 1;
		
		if (($pThread % $threadIntv) > 0)
		{
			$prevParentThread = floor($pThread / $threadIntv) * $threadIntv;
		}
		else
		{
			$prevParentThread = $pThread - $threadIntv;
		}
		
		$insDepth = $pDepth + 1;
		
		//범위내 thread 조정
		$this->db->set('THREAD', 'THREAD - 1', FALSE);		
		$this->db->where('THREAD > '.$prevParentThread.' AND THREAD < '.$pThread);
		$this->db->update($this->tbl);		

		//업데이트될 추가 내용		
		$addData = array('THREAD' => $thread, 'DEPTH' => $insDepth);
		$insData = $insData + $addData;
		
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		
		//원본글 replycount와 update date 갱신(주의-부모글이 아닌 원본글만 update함)
		$this->db->set('REPLYCOUNT', 'REPLYCOUNT + 1', FALSE);
		$this->db->set('REPLY_UPDATE_DATE', date('Y-m-d H:i:s'));		
		$this->db->where('NUM', $insData['GROUPNUM']);
		$this->db->update($this->tbl);		
		
		/*
		if ($insData['CATECODE_NUM'] == 1540)
		{
			//배송 및 환불정책인 경우 기준샵에도 내용 update
			$this->setRefundPolicyUpdateToStdShop();
		}
		*/
		
		if ($resultNum > 0)
		{
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$setNum.'/'), 
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
		
					//Transaction 커밋
					$this->db->trans_commit();
				}
			}
		}
		
		return $resultNum;	
	}
	
	public function setBoardDataUpdate($setNum, $bNum, $upData, $isUpload)
	{
		if ($bNum > 0)
		{
			/*
			$result = $this->getBoardRowData($bNum, FALSE);
			
			if (count($result) > 0){
				$orgWriteUnum =  $result['recordSet']['USER_NUM'];
			}
			
			if ($orgWriteUnum != $this->common->getSession('user_num')){
				$this->common->message('작성자만 수정할 수 있습니다.', '', 'self');
			}
			*/			
			
			//Transaction 시작(수동수행)  - 파일업로드 오류 또는 제약사항에 걸리는 경우에 대비
			if ($isUpload) $this->db->trans_begin();
			
			$this->db->where('NUM', $bNum);
			$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
			
			if ($upData['CATECODE_NUM'] == 1540)
			{
				//배송 및 환불정책인 경우 기준샵에도 내용 update
				$this->setRefundPolicyUpdateToStdShop();
			}			
			
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$setNum.'/'), 
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $bNum
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
						//비교를 위해 기존 업로드된 내용을 확인한다 
						$this->db->select('*');
						$this->db->limit(1);
						$this->db->from($this->_fileTbl);
						$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
						$this->db->where('TBL_NUM', $bNum);
						$this->db->where('FILE_ORDER', $i);
						$this->db->where('DEL_YN', 'N');
						$oldFile = $this->db->get()->row_array();
						
						if (count($oldFile) > 0)
						{
							if ($oldFile['FILE_NAME'] != $uploadResult[$i]['FILE_NAME'] || $oldFile['FILE_SIZE'] != $uploadResult[$i]['FILE_SIZE'])
							{
								//파일명 또는 파일사이즈가 다른 경우 삭제 플래그 만 변경
								$upData = array('DEL_YN' => 'Y');	//배열로 업데이트 가능
								$this->db->where('NUM', $oldFile['NUM']);
								$this->db->update($this->_fileTbl, $upData);
								//update after insert
								$this->db->insert($this->_fileTbl, $uploadResult[$i]);								
							}
						}
						else
						{
							$this->db->insert($this->_fileTbl, $uploadResult[$i]);
						}
					}
						
					//Transaction 커밋
					$this->db->trans_commit();
				}
			}
		}
		
		return $bNum;
	}

	/**
	 * @method name : setBoardDataDelete
	 * 1건 삭제
	 * 
	 * @param unknown $bNum
	 */
	public function setBoardDataDelete($bNum)
	{
		$groupNum = $depth = 0;		
		$result = $this->getBoardRowData($bNum, TRUE);

		if (count($result['recordSet']) > 0)
		{
			$groupNum = $result['recordSet']['GROUPNUM'];
			$depth =  $result['recordSet']['DEPTH'];
			$orgWriteUnum =  $result['recordSet']['USER_NUM'];
		}
		
		/*
		if ($orgWriteUnum != $this->common->getSession('user_num'))
		{
			$this->common->message('작성자만 삭제할 수 있습니다.', '', 'self');
		}
		*/
		
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
				
		/* 실제 삭제
		$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
		$this->db->where('TBL_NUM', $bNum);
		$this->db->delete($this->_fileTbl);
		
		$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
		$this->db->where('TBL_NUM', $bNum);
		$this->db->update($this->_commentTbl);		
		
		if ($depth == 0 && $groupNum > 0){
			//최초원글이 삭제되므로 하위글 모두 삭제			
			$this->db->where('GROUPNUM', $groupNum);
			$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능		
		}else{
			//본인글만 삭제		
			$this->db->where('NUM', $bNum);
			$this->db->delete($this->tbl);		
		}
		*/
		
		//삭제 플래그 변경
		/*부모삭제시 쓸일이 없으므로 굳이 삭제하지 않는다(데이타 복원시에도 부모만 복원)
		$this->db->set('DEL_YN', 'Y');
		$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
		$this->db->where('TBL_NUM', $bNum);
		$this->db->update($this->_fileTbl);		
		*/
		
		$this->db->set('DEL_YN', 'Y');		
		$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
		$this->db->where('TBL_NUM', $bNum);
		$this->db->update($this->_commentTbl);		
		
		if ($depth == 0 && $groupNum > 0)
		{
			//최초원글이 삭제되므로 하위글 모두 삭제
			$this->db->set('DEL_YN', 'Y');			
			$this->db->where('GROUPNUM', $groupNum);
			$this->db->update($this->tbl);		
		}
		else
		{
			//본인글만 삭제			
			$this->db->set('DEL_YN', 'Y');			
			$this->db->where('NUM', $bNum);
			$this->db->update($this->tbl);			
		}
		
		//원본글 replycount차감(주의-부모글이 아닌 원본글만 update함)
		$this->db->set('REPLYCOUNT', 'REPLYCOUNT - 1', FALSE);
		$this->db->where('NUM', $groupNum);
		$this->db->where('REPLYCOUNT > 0');		
		$this->db->update($this->tbl);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
	}
	
	/**
	 * @method name : setBoardGroupDataDelete
	 * 체크된 내용 모두 삭제
	 * 
	 * @param unknown $delData
	 */
	public function setBoardGroupDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt) 
		{
			$this->setBoardDataDelete($dt);
		}
	}
	
	/**
	 * @method name : setRefundPolicyUpdateToStdShop
	 * 약관관리의 배송/환불정책 등록,변경시 기준샵에 update
	 * 
	 * @param unknown $upContent
	 * @return number
	 */
	public function setRefundPolicyUpdateToStdShop()
	{
		$result = 0;
		$toDate = date('Y-m-d');
		$sql = "SELECT CONTENT FROM ".$this->tbl."
				WHERE NUM = (
							    SELECT MAX(NUM) AS NUM FROM ".$this->tbl."
							    WHERE SET_NUM = 9140
							    AND CATECODE_NUM = 1540
							    AND DEL_YN = 'N'
							    AND APPLY_DATE <= '".$toDate."'				
				)
		"; //배송정책 유효일자안의 최근 내용1건을 조회
		
		$refundContent = $this->db->query($sql)->row()->CONTENT;
		
		$stdShopNum = $this->common->getStandardShopInfo();		
		$stdShopNum = $this->common->nullCheck($stdShopNum['NUM'], 'int', 0);
		
		if ($stdShopNum > 0 && !empty($refundContent))
		{
			$this->db->set('REFPOLICY_CONTENT', $refundContent);
			$this->db->where('SHOP_NUM', $stdShopNum);
			$this->db->update('SHOP_POLICY');
			$result = $this->db->affected_rows();
		}
		
		return $result;
	}
	
	/**
	 * @method name : getAppNoticeUrgencyRowData
	 * 앱공지사항 중 긴급공지 1건 
	 * 
	 * @return Ambiguous
	 */
	public function getAppNoticeUrgencyRowData()
	{
		$this->db->select("
			*
		");
		$this->db->limit(1);
		$this->db->from($this->tbl);
		$this->db->where('SET_NUM', 9150);
		$this->db->where('URGENCY_YN', 'Y');
		$this->db->where('DEL_YN', 'N');
		$this->db->order_by('NUM', 'DESC');
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
	private function getUploadOption($subdir = '')
	{
		$config = array();
		$baseUploadPath = '.'.$this->config->item('base_uploadPath');
		if (!empty($subdir))
		{
			$uploadPath = $baseUploadPath.$subdir;
		}
		$config['upload_path'] = $uploadPath;
		$config['allowed_types'] = 'doc|hwp|pdf|ppt|xls|pptx|docx|xlsx|zip|rar|gif|jpg|png';
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