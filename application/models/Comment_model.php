<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Comment_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Comment_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected  $_tblCodeNum = 0;
	
	protected $_encKey = '';
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'COMMON_COMMENT';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	/**
	 * @method name : getCommentDataList
	 * 댓글(흔적남기기) 리스트
	 * 
	 * @param $qData 검색 조건
	 * @param bool $isDelView 삭제된 내용 보기 여부
	 * @return array
	 */
	public function getCommentDataList($qData, $isDelView = FALSE)
	{
		//data 총 갯수 select
		$tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $qData['tblInfo']);		
		$proFileTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$whSql = "1 = 1";		
		$whSql .= " AND a.TBLCODE_NUM = ".$tblCodeNum;
		$whSql .= ($qData['tNum'] > 0) ? " AND a.TBL_NUM = ".$qData['tNum'] : '';
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
			" AND a.".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : '';
		$whSql .= (!empty($qData['sNum'])) ? " AND b.SHOP_NUM = ".$qData['sNum'] : '';		
		if (!empty($qData['sendItemNum'])) //검색시 선택한 아이템 그룹
		{
			$whSql .= ' AND b.NUM IN ('.$qData['sendItemNum'].')';
		}
		
		if (!empty($qData['sendShopNum'])) //검색시 선택한 샵 그룹
		{
			$whSql .= ' AND c.NUM IN ('.$qData['sendShopNum'].')';
		}
		
		if (isset($qData['maxNum'])) //ajax listing시 중복 리스트 방지
		{
			if ($qData['maxNum'] > 0)
			{
				$whSql .= ' AND a.NUM <= '.$qData['maxNum'];
			}
		}
		
		$this->db->select('COUNT(*) AS COUNT');		
		$this->db->from($this->tbl.' AS a');
		$this->db->join('SHOPITEM AS b', 'a.TBL_NUM = b.NUM', 'left outer');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM', 'left outer');		
		$this->db->where($whSql);		
		$totalCount = $this->db->get()->row()->COUNT;

		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			(
				SELECT 
					CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$proFileTblCodeNum."
				AND TBL_NUM = a.USER_NUM
				AND DEL_YN = 'N' 
				ORDER BY NUM LIMIT 1
			) AS PROFILE_FILE_INFO,					
			AES_DECRYPT(UNHEX(a.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			b.NUM AS ITEM_NUM,
			b.SHOP_NUM,
			b.ITEM_NAME,
			b.ITEM_CODE,
			b.ITEMSHOP_CODE,
			c.SHOP_NAME,
			c.SHOP_CODE
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('SHOPITEM AS b', 'a.TBL_NUM = b.NUM', 'left outer');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM', 'left outer');		
		$this->db->where($whSql);
		$this->db->order_by('THREAD', 'DESC');
		$this->db->order_by('NUM', 'DESC');		
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
	
		//echo $this->db->last_query().'<br /><br />';
		//echo $totalCount;
		
		return $result;
	}
	
	/**
	 * @method name : getCommentRowData
	 *댓글(흔적남기기) 상세
	 *
	 * @param int $comtNum
	 * @param bool $isDelView TRUE: 삭제표기된 내용도 확인
	 * @return Ambiguous
	 */
	public function getCommentRowData($comtNum, $isDelView)
	{
		$whSql = "a.NUM = ".$comtNum;
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';		
		$this->db->select("
			a.*,
			AES_DECRYPT(UNHEX(a.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			b.NUM AS ITEM_NUM,				
			b.SHOP_NUM,
			b.ITEM_NAME,
			b.ITEM_CODE,
			b.ITEMSHOP_CODE,
			c.SHOP_NAME,
			c.SHOP_CODE				
		");
		$this->db->limit(1);
		$this->db->from($this->tbl.' AS a');
		$this->db->join('SHOPITEM AS b', 'a.TBL_NUM = b.NUM', 'left outer');
		$this->db->join('SHOP AS c', 'b.SHOP_NUM = c.NUM', 'left outer');
		$this->db->where($whSql);		
		$result['recordSet'] = $this->db->get()->row_array();

		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
		$this->db->where("TBL_NUM = ".$comtNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');
		$result['fileSet'] = $this->db->get()->result_array();		
	
		return $result;
	}	

	/**
	 * @method name : setCommentDataInsert
	 * 댓글(흔적남기기) 게시글 작성
	 * 
	 * @param array $insData
	 * @param int $threadIntv
	 * @param bool $isUpload	파일 업로드 여부 (TRUE, FALSE)
	 * @return Ambiguous
	 */
	public function setCommentDataInsert($insData, $threadIntv, $isUpload)
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
		
		$upData = array(
			'GROUPNUM' => $resultNum
		);
		$this->db->where('NUM', $resultNum);
		$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
		
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
		
					//Transaction 커밋					
					$this->db->trans_commit();
				}
			}
		}
		
		return $resultNum;
	}	

	/**
	 * @method name : setCommentReplyDataInsert
	 * 댓글(흔적남기기) 리스트에 댓글달기
	 * 
	 * @param int $pThread	부모글 THREAD
	 * @param int $pDepth	부모글 DEPTH
	 * @param array $insData
	 * @param int $threadIntv	Thread 간격
	 * @param bool $isUpload	파일 업로드 여부 (TRUE, FALSE)
	 * @return int	db insert 직후 고유번호 NUM
	 */
	public function setCommentReplyDataInsert($pThread, $pDepth, $insData, $threadIntv, $isUpload)
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
		
					//Transaction 커밋
					$this->db->trans_commit();
				}
			}
		}
		
		return $resultNum;	
	}
	
	/**
	 * @method name : setCommentDataUpdate
	 * 댓글(흔적남기기) update
	 * 
	 * @param unknown $comtNum
	 * @param unknown $upData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setCommentDataUpdate($comtNum, $upData, $isUpload)
	{
		if ($comtNum > 0)
		{
			/*
			$result = $this->getCommentRowData($comtNum, FALSE);
			
			if (count($result) > 0){
				$orgWriteUnum =  $result['recordSet']['USER_NUM'];
			}
			
			if ($orgWriteUnum != $this->common->getSession('user_num')){
				$this->common->message('작성자만 수정할 수 있습니다.', '', 'self');
			}
			*/			
			
			//Transaction 시작(수동수행)  - 파일업로드 오류 또는 제약사항에 걸리는 경우에 대비
			if ($isUpload) $this->db->trans_begin();

			$this->db->where('NUM', $comtNum);
			$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
			
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$comtNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $comtNum
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
						$this->db->where('TBL_NUM', $comtNum);
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
		
		return $comtNum;
	}

	/**
	 * @method name : setCommentDataDelete
	 * 댓글(흔적남기기) 리스트 1건 삭제
	 * 
	 * @param unknown $comtNum
	 * @param unknown $isAuthCheck 본인 여부 확인
	 * @param unknown $format ajax, json
	 */
	public function setCommentDataDelete($comtNum, $isAuthCheck = FALSE, $format = '')
	{
		$groupNum = $depth = $orgWriteUnum = 0;		
		$result = $this->getCommentRowData($comtNum, TRUE);

		if (count($result['recordSet']) > 0)
		{
			$groupNum = $result['recordSet']['GROUPNUM'];
			$depth =  $result['recordSet']['DEPTH'];
			$orgWriteUnum =  $result['recordSet']['USER_NUM'];
		}
		
		if ($isAuthCheck)
		{
			if ($orgWriteUnum != $this->common->getSession('user_num'))
			{
				if ($format == 'json') exit('-1000');
				$this->common->message('작성자만 삭제할 수 있습니다.', '-', '');
			}			
		}

		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
				
		if ($depth == 0 && $groupNum > 0)
		{
			//최초원글이 삭제되므로 하위글 모두 삭제
			$this->db->set('DEL_YN', 'Y');			
			$this->db->where('GROUPNUM', $groupNum);
			$this->db->update($this->tbl);
			$result = $this->db->affected_rows();
		}
		else
		{
			//본인글만 삭제			
			$this->db->set('DEL_YN', 'Y');			
			$this->db->where('NUM', $comtNum);
			$this->db->update($this->tbl);
			$result = $this->db->affected_rows();
		}
		
		//원본글 replycount차감(주의-부모글이 아닌 원본글만 update함)
		$this->db->set('REPLYCOUNT', 'REPLYCOUNT - 1', FALSE);
		$this->db->where('NUM', $groupNum);
		$this->db->where('REPLYCOUNT > 0');		
		$this->db->update($this->tbl);
		
		//Transaction 자동 커밋
		$this->db->trans_complete();
		
		return $result;
	}
	
	/**
	 * @method name : setCommentGroupDataDelete
	 * 댓글(흔적남기기) 리스트 체크된 내용 모두 삭제
	 * 
	 * @param unknown $delData
	 */
	public function setCommentGroupDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt) 
		{
			$this->setCommentDataDelete($dt);
		}
	}
	
	/**
	 * @method name : setSpamUpdate
	 * 댓글(흔적남기기) 리스트 스팸 처리 
	 * 
	 * @param unknown $comtNum
	 */
	public function setSpamUpdate($comtNum)
	{
		$this->db->set('SPAM_YN', 'Y');
		$this->db->where('NUM', $comtNum);
		$this->db->update($this->tbl);
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