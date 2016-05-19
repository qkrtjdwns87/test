<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Story_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 01
 * @version:
 */
class Story_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_commentTbl = 'COMMON_COMMENT';
	
	protected  $_tblCodeNum = 0;
	
	protected $_encKey = '';
	
	public function __construct() 
	{
		parent::__construct();
		
		//$this->load->helper(array('text'));
		$this->load->database(); // Database Load
		$this->tbl = 'STORY';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	/**
	 * @method name : getStoryDataList
	 * 
	 * 
	 * @param $qData 검색 조건
	 * @param bool $isDelView 삭제된 내용 보기 여부
	 * @return array
	 */
	public function getStoryDataList($qData, $isDelView = FALSE)
	{
		//data 총 갯수 select
		$toDate = date('Y-m-d');
		$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl.'_CONTENT');
		$whSql = "1 = 1";
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : '';
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
						" AND ".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : '';
		
		$addSelect = '0 AS ITEM_FLAG';
		if (isset($qData['userNum']))
		{
			$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'STORY');
			$addSelect = ($qData['userNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$flagTblCodeNum."
					AND TBL_NUM = ".$this->tbl.".NUM
					AND USER_NUM = ".$qData['userNum']."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG
			" : $addSelect;
		}		
		
		$this->db->select('COUNT(*) AS COUNT');		
		$this->db->from($this->tbl);
		$this->db->where($whSql);		
		$totalCount = $this->db->get()->row()->COUNT;

		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			".$addSelect.",
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$tblSubCodeNum."
				AND TBL_NUM = ".$this->tbl.".NUM
				AND DEL_YN = 'N' 
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO				
		");
		$this->db->from($this->tbl);
		$this->db->where($whSql);
		$this->db->order_by('NUM', 'DESC');		
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		//log_message('debug', $this->db->last_query());

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}
	
	/**
	 * @method name : getStoryRowData
	 *
	 *
	 * @param int $bNum
	 * @param int $qData
	 * @param bool $isDelView TRUE: 삭제표기된 내용도 확인
	 * @return Ambiguous
	 */
	public function getStoryRowData($stoNum, $qData, $isDelView = FALSE)
	{
		$whSql = "NUM = ".$stoNum;
		$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : '';

		$whSqlSub = '';
		if (isset($qData['isValidData'])) //아이템 유효한 내용만(사용자페이지)
		{
			$whSqlSub .= ($qData['isValidData']) ? " AND bin.VIEW_YN = 'Y' AND bin.ITEMSTATECODE_NUM IN (8060, 8070)" : "";
		}
		
		$ShopTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOP');
		$tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
		$addSelect = " 0 AS ITEM_FLAG, 0 AS ITEM_BUY";
		if (isset($qData['userNum']))
		{
			$addSelect = ($qData['userNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$tblCodeNum."
					AND TBL_NUM = ssatb.NUM
					AND USER_NUM = ".$qData['userNum']."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = ssatb.NUM
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
		
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC
		");
		$this->db->limit(1);
		$this->db->from($this->tbl);
		$this->db->where($whSql);		
		$result['recordSet'] = $this->db->get()->row_array();
		
		//모바일 APP 내용
		$this->db->select("
			*,
			(SELECT TITLE FROM CODE WHERE NUM = STORYSTYLECODE_NUM) AS STORYSTYLECODE_TITLE,
			( 
				CASE 
					WHEN STORYSTYLECODE_NUM = 1830
					THEN 
					( 
						SELECT CONCAT(SHOP_CODE, '|', SHOP_NAME, '|', SHOPUSER_NAME, '|', TODAYAUTHOR_YN, '|', POPAUTHOR_YN) 
						FROM SHOP WHERE NUM = a.CONTENT 
					) 
					ELSE '' 
				END 
			) AS SHOP_INFO,			
			( 
				CASE 
					WHEN STORYSTYLECODE_NUM = 1830
					THEN 
					( 
						SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
						FROM PROFILE_FILE
						WHERE TBLCODE_NUM = ".$ShopTblCodeNum."
						AND TBL_NUM = a.CONTENT
						AND DEL_YN = 'N'
						ORDER BY NUM LIMIT 1
					) 
					ELSE '' 
				END 
			) AS PROFILE_FILE_INFO,				
			(
				CASE  
					WHEN STORYSTYLECODE_NUM = 1830
					THEN 
					( 
						SELECT
							GROUP_CONCAT(tb.ITEM_INFO SEPARATOR '-')
						FROM 
						(
							SELECT
								SHOP_NUM,
								CONCAT(SHOP_NUM, '#', ITEM_NUM, '#', ITEM_FLAG, '#', ITEM_SUB_INFO, '#', FILE_INFO, '#', M_FILE_INFO) AS ITEM_INFO
							FROM 
							(
								SELECT 
									ssatb.SHOP_NUM, 
									ssatb.NUM AS ITEM_NUM,
									".$addSelect.", 
									CONCAT(ssatb.NUM, '|', SHOP_CODE, '|', SHOP_NAME, '|', SHOPUSER_NAME, '|', ITEM_NAME, '|', ITEM_CODE, '|',ITEMSHOP_CODE, '|', ITEM_PRICE, '|', ITEMSTATECODE_NUM, '|', DISCOUNT_YN, '|', DISCOUNT_PRICE, '|', STOCKFREE_YN, '|', STOCK_COUNT) AS ITEM_SUB_INFO,
									(
										SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
										FROM SHOPITEM_FILE
										WHERE SHOPITEM_NUM = ssatb.NUM 
										AND DEL_YN = 'N' 
										AND FILE_USE = 'W'
										ORDER BY NUM LIMIT 1
									) AS FILE_INFO,	
									(
										SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
										FROM SHOPITEM_FILE
										WHERE SHOPITEM_NUM = ssatb.NUM 
										AND DEL_YN = 'N' 
										AND FILE_USE = 'M'
										ORDER BY NUM LIMIT 1
									) AS M_FILE_INFO
								FROM SHOPITEM ssatb INNER JOIN SHOP ssbtb ON ssatb.SHOP_NUM = ssbtb.NUM
								WHERE ssatb.NUM IN (
									SELECT 
										bin.NUM
									FROM STORY_CONTENT ain INNER JOIN SHOPITEM bin
									ON ain.CONTENT = bin.SHOP_NUM
									WHERE ain.STORYSTYLECODE_NUM = 1830
									AND ain.DEL_YN = 'N'
									AND bin.DEL_YN = 'N'
									".$whSqlSub."
								)
								ORDER BY ssatb.NUM DESC LIMIT 8
							) AS stb
						) tb
						GROUP BY SHOP_NUM
					) 
					ELSE '' 
				END 
			) AS SHOPITEM_INFO				
		");
		$this->db->from($this->tbl.'_CONTENT AS a');
		$this->db->where('a.STORY_NUM = '.$stoNum);
		$this->db->where('a.DEL_YN', 'N');
		$this->db->order_by('a.STORY_ORDER', 'ASC');
		$result['recSubSet'] = $this->db->get()->result_array();

		//파일등록정보(STORY_CONTENT)
		$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl.'_CONTENT');
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$tblSubCodeNum);
		$this->db->where("TBL_NUM = ".$stoNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');
		$result['fileSet'] = $this->db->get()->result_array();
		
		return $result;
	}	

	/**
	 * @method name : setStoryDataInsert
	 * 게시글 작성
	 * 
	 * @param int $setNum
	 * @param array $insData
	 * @param int $threadIntv
	 * @param bool $isUpload	파일 업로드 여부 (TRUE, FALSE)
	 * @return Ambiguous
	 */
	public function setStoryDataInsert($insData, $storySub, $isUpload)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		
		//모바일 APP 내용
		if ($resultNum > 0 && !empty($storySub))
		{
			usort($storySub, $this->common->msort(['order', SORT_ASC]));			
			for($i=0; $i<count($storySub); $i++)
			{
				$contentTxt = $contentHtml = '';
				if ($storySub[$i]['style'] == 1820)
				{
					$contentTxt = $storySub[$i]['url'];
				}
				else if ($storySub[$i]['style'] == 1830)
				{
					$contentTxt = $storySub[$i]['shopno'];
				}
				else if ($storySub[$i]['style'] == 1840)
				{
					$contentHtml = $storySub[$i]['html'];
				}				
				
				$this->db->insert(
					$this->tbl.'_CONTENT',
					array(
						'STORY_NUM' => $resultNum,
						'STORY_ORDER' => $i,
						'STORYSTYLECODE_NUM' => $storySub[$i]['style'],
						'CONTENT' => $contentTxt,
						'HTML_CONTENT' => $contentHtml
					)
				);
				$storySubNum = $this->db->insert_id();
			}
		}
		
		if ($resultNum > 0)
		{
			if ($isUpload)
			{
				$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl.'_CONTENT');
				
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$resultNum.'/'), 
					array(
						'TBLCODE_NUM' => $tblSubCodeNum,
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
						$this->db->insert($this->_fileTbl, $uploadResult[$i]);
					}
				}
			}
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $resultNum;
	}	

	public function setStoryDataUpdate($stoNum, $upData, $storySub, $isUpload)
	{
		if ($stoNum > 0)
		{
			/*			
			$result = $this->getStoryRowData($stoNum, array(), FALSE);
			
			if (count($result) > 0){
				$orgWriteUnum =  $result['recordSet']['USER_NUM'];
			}
			
			if ($orgWriteUnum != $this->common->getSession('user_num')){
				$this->common->message('작성자만 수정할 수 있습니다.', '', 'self');
			}
			*/			
			
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$this->db->where('NUM', $stoNum);
			$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
			
			//모바일 APP 내용
			if (!empty($storySub))
			{
				//기존내용 전체 삭제
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('STORY_NUM', $stoNum);
				$this->db->update($this->tbl.'_CONTENT');
				
				usort($storySub, $this->common->msort(['order', SORT_ASC]));				
				for($i=0; $i<count($storySub); $i++)
				{
					$contentTxt = $contentHtml = '';
					if ($storySub[$i]['style'] == 1820)
					{
						$contentTxt = $storySub[$i]['url'];
					}
					else if ($storySub[$i]['style'] == 1830)
					{
						$contentTxt = $storySub[$i]['shopno'];
					}
					else if ($storySub[$i]['style'] == 1840)
					{
						$contentHtml = $storySub[$i]['html'];
					}					
			
					$this->db->insert(
						$this->tbl.'_CONTENT',
						array(
							'STORY_NUM' => $stoNum,
							'STORY_ORDER' => $i,
							'STORYSTYLECODE_NUM' => $storySub[$i]['style'],
							'CONTENT' => $contentTxt,
							'HTML_CONTENT' => $contentHtml
						)
					);
					$storySubNum = $this->db->insert_id();
				}
			}			
			
			if ($isUpload)
			{
				$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl.'_CONTENT');
				
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$stoNum.'/'), 
					array(
						'TBLCODE_NUM' => $tblSubCodeNum,
						'TBL_NUM' => $stoNum
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
						$this->db->where('TBLCODE_NUM', $tblSubCodeNum);
						$this->db->where('TBL_NUM', $stoNum);
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
									$this->db->set('DEL_YN', 'Y');
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
					$this->db->where('TBLCODE_NUM', $tblSubCodeNum);
					$this->db->where('TBL_NUM', $stoNum);
					$this->db->where('FILE_ORDER >= '.$i);
					$this->db->update($this->_fileTbl);
				}
			}
			
			//Transaction 자동 커밋
			$this->db->trans_complete();			
		}
		
		return $stoNum;
	}

	/**
	 * @method name : setStoryDataDelete
	 * STORY 삭제 (1건) 
	 * 
	 * @param unknown $stoNum
	 */
	public function setStoryDataDelete($stoNum)
	{
		/*
		$result = $this->getStoryRowData($stoNum, array(), TRUE);

		if (count($result['recordSet']) > 0)
		{
			$orgWriteUnum =  $result['recordSet']['USER_NUM'];
		}
		
		if ($orgWriteUnum != $this->common->getSession('user_num'))
		{
			$this->common->message('작성자만 삭제할 수 있습니다.', '', 'self');
		}
		*/
		
		//Transaction 시작 (자동 수행)
		//$this->db->trans_start();
				
		//삭제 플래그 변경
		$this->db->set('DEL_YN', 'Y');			
		$this->db->where('NUM', $stoNum);
		$this->db->update($this->tbl);
		
		//Transaction 자동 커밋
		//$this->db->trans_complete();		
	}
	
	/**
	 * @method name : setStoryGroupDataDelete
	 * STORY 삭제 (체크된 내용 모두 삭제)
	 * 
	 * @param unknown $delData
	 */
	public function setStoryGroupDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt) 
		{
			$this->setStoryDataDelete($dt);
		}
	}
	
	/**
	 * @method name : setStoryFileDelete
	 * 개별 파일 삭제
	 * 웹, 모바일 한쌍중 나머지가 삭제되었는지 확인
	 * 다른 나머지도 삭제되었다면 한쌍이(W,M) 모두 삭제된셈이므로
	 * 한쌍을 제외한 나머지 FILE_ORDER를 -1 해준다(상위 ORDER의 파일만) 
	 * 
	 * @param unknown $stoNum
	 * @param unknown $fNum
	 * @param unknown $fIndex
	 */
	public function setStoryFileDelete($stoNum, $fNum, $fIndex)
	{
		$tblSubCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl.'_CONTENT');
		$otherIndex = (($fIndex % 2) == 0) ? ($fIndex + 1) : ($fIndex - 1);
		
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where('TBLCODE_NUM', $tblSubCodeNum);
		$this->db->where('TBL_NUM', $stoNum);
		$this->db->where('FILE_ORDER', $fIndex);		
		$this->db->where('DEL_YN', 'N');
		$result = $this->db->get()->row_array();
		
		if ($result)
		{
			if ($result['DEL_YN'] == 'Y' || $result['FILE_NAME'] == '')
			{
				//쌍으로 삭제
				$this->db->set('DEL_YN', 'Y');
				$this->db->where('TBLCODE_NUM', $tblSubCodeNum);
				$this->db->where('TBL_NUM', $stoNum);
				$this->db->where('FILE_ORDER IN ('.$fIndex.', '.$otherIndex.')');
				$this->db->update($this->_fileTbl);
					
				//FILE_ORDER 조정
				$this->db->set('FILE_ORDER', 'FILE_ORDER - 2', FALSE);
				$this->db->where('TBLCODE_NUM', $tblSubCodeNum);
				$this->db->where('TBL_NUM', $stoNum);
				$this->db->where('DEL_YN', 'N');
				$this->db->where('FILE_ORDER > '.$fIndex);
				$this->db->update($this->_fileTbl);
			}
			else
			{
				//삭제이나 삭제하지 않고 빈데이만 구성
				$upData = array(
					'FILE_NAME' => '',
					'FILE_TEMPNAME' => '',
					'FILE_TYPE' => '',
					'FILE_SIZE' => 0,
					'IMAGE_YN' => 'N',
					'THUMB_YN' => 'N',
					'IMAGE_WIDTH' => 0,
					'IMAGE_HEIGHT' => 0
				);
					
				$this->db->where('TBLCODE_NUM', $tblSubCodeNum);
				$this->db->where('TBL_NUM', $stoNum);
				$this->db->where('NUM', $fNum);
				$this->db->update($this->_fileTbl, $upData);
			}
		}
	}
	
	/**
	 * @method name : getFlagStoryDataList
	 * 플래그한 스토리 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getFlagStoryDataList($qData)
	{
		//data 총 갯수 select
		$whSql = '1 = 1';
	
		if (isset($qData['userNum']))
		{
			$whSql .= ($qData['userNum'] > 0) ? ' AND a.USER_NUM = '.$qData['userNum'] : '';
		}
		
		$addSelect = " 1 AS STORY_FLAG ";
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
				
			$tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'STORY');
			$addSelect = ($userNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$tblCodeNum."
						AND TBL_NUM = a.STORY_NUM
						AND USER_NUM = ".$userNum."
						AND DEL_YN = 'N'
					) AS STORY_FLAG
				" : $addSelect;
				
			$whSql .= ($qData['userNum'] > 0) ? ' AND a.USER_NUM = '.$qData['userNum'] : '';
		}		
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('VIEW_FLAG_STORY AS a');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			".$addSelect.",
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
				FROM COMMON_FILE
				WHERE TBLCODE_NUM = ".$this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'STORY_CONTENT')."
				AND TBL_NUM = a.STORY_NUM
				AND DEL_YN = 'N' 
				AND FILE_ORDER = 0
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO					
		");
		$this->db->from('VIEW_FLAG_STORY AS a');
		$this->db->where($whSql);
		$this->db->order_by('STORY_NUM', 'DESC');
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
	private function getUploadOption($subdir = '')
	{
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