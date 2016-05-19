<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Review_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Review_model extends CI_Model{
	
	protected $_fileTbl = 'COMMON_FILE';
	
	protected  $_tblCodeNum = 0;
	
	protected $_encKey = '';
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->load->database(); // Database Load
		$this->tbl = 'ORDERITEM_REVIEW';
		//$this->pkey = 'NUM';
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);
	}
	
	/**
	 * @method name : getReviewDataList
	 * 
	 * 
	 * @param $qData 검색 조건
	 * @param bool $isDelView 삭제된 내용 보기 여부
	 * @return array
	 */
	public function getReviewDataList($qData, $isDelView = FALSE)
	{
		//data 총 갯수 select
		$whSql = "1 = 1";		
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : "";
		$whSql .= (!empty($qData['searchKey']) && !empty($qData['searchWord'])) ?
						" AND a.".$qData['searchKey']." LIKE '%".$qData['searchWord']."%'" : '';
		$whSql .= (!empty($qData['sNum'])) ? " AND c.SHOP_NUM = ".$qData['sNum'] : '';		
		$whSql .= (!empty($qData['siNum'])) ? " AND c.NUM = ".$qData['siNum'] : '';		
		if (!empty($qData['sendItemNum']))
		{
			$whSql .= ' AND c.NUM IN ('.$qData['sendItemNum'].')';
		}
		
		if (!empty($qData['sendShopNum']))
		{
			$whSql .= ' AND d.NUM IN ('.$qData['sendShopNum'].')';
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
		$this->db->join('ORDERITEM AS b', 'a.ORDERITEM_NUM = b.NUM', 'left outer');		
		$this->db->join('SHOPITEM AS c', 'a.SHOPITEM_NUM = c.NUM');
		$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM');		
		$this->db->where($whSql);		
		$totalCount = $this->db->get()->row()->COUNT;

		//페이징된 data select
		$tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'USER');
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			a.*,
			(
				SELECT 
					CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM PROFILE_FILE
				WHERE TBLCODE_NUM = ".$tblCodeNum."
				AND TBL_NUM = a.USER_NUM
				AND DEL_YN = 'N' 
				ORDER BY NUM LIMIT 1
			) AS PROFILE_FILE_INFO,				
			AES_DECRYPT(UNHEX(a.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			c.NUM AS ITEM_NUM,
			c.SHOP_NUM,
			c.ITEM_NAME,
			c.ITEM_CODE,
			c.ITEMSHOP_CODE,
			d.SHOP_NAME,
			d.SHOP_CODE
		");
		$this->db->from($this->tbl.' AS a');
		$this->db->join('ORDERITEM AS b', 'a.ORDERITEM_NUM = b.NUM', 'left outer');		
		$this->db->join('SHOPITEM AS c', 'a.SHOPITEM_NUM = c.NUM');
		$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM');		
		$this->db->where($whSql);
		$this->db->order_by('NUM', 'DESC');		
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;
		
		return $result;
	}
	
	/**
	 * @method name : getReviewRowData
	 *
	 *
	 * @param int $rvNum
	 * @param bool $isDelView TRUE: 삭제표기된 내용도 확인
	 * @return Ambiguous
	 */
	public function getReviewRowData($rvNum, $isDelView = FALSE)
	{
		$whSql = "a.NUM = ".$rvNum;
		$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';		
		$this->db->select("
			a.*,
			AES_DECRYPT(UNHEX(a.USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
			c.NUM AS ITEM_NUM,
			c.SHOP_NUM,
			c.ITEM_NAME,
			c.ITEM_CODE,
			c.ITEMSHOP_CODE,
			d.SHOP_NAME,
			d.SHOP_CODE			
		");
		$this->db->limit(1);
		$this->db->from($this->tbl.' AS a');
		$this->db->join('ORDERITEM AS b', 'a.ORDERITEM_NUM = b.NUM', 'left outer');		
		$this->db->join('SHOPITEM AS c', 'a.SHOPITEM_NUM = c.NUM');
		$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM');
		$this->db->where($whSql);		
		$result['recordSet'] = $this->db->get()->row_array();
		
		$this->db->select('*');
		$this->db->from($this->_fileTbl);
		$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
		$this->db->where("TBL_NUM = ".$rvNum);
		$this->db->where("DEL_YN", "N");
		$this->db->order_by('FILE_ORDER', 'ASC');
		$this->db->order_by('NUM', 'ASC');
		$result['fileSet'] = $this->db->get()->result_array();		
	
		return $result;
	}	

	/**
	 * @method name : setReviewDataInsert
	 * 후기글 작성
	 * 
	 * @param array $insData
	 * @param bool $isUpload	파일 업로드 여부 (TRUE, FALSE)
	 * @return Ambiguous
	 */
	public function setReviewDataInsert($insData, $isUpload)
	{
		//Transaction 시작 (자동 수행)
		$this->db->trans_start();
		
		$this->db->insert($this->tbl, $insData);
		$resultNum = $this->db->insert_id();
		
		if ($resultNum > 0)
		{
			//별점 처리
			$score = $insData['SCORE'];
			$itemNum = $insData['SHOPITEM_NUM'];
			
			//총점 및 평균 update
			$this->db->set(
				'TOTSCORE',
				"(SELECT SUM(SCORE) FROM ".$this->tbl." WHERE DEL_YN = 'N' AND SHOPITEM_NUM = ".$itemNum.")",
				FALSE
			);
			$this->db->set(
				'TOTSCORE_AVG',
				"(SELECT AVG(SCORE) FROM ".$this->tbl." WHERE DEL_YN = 'N' AND SHOPITEM_NUM = ".$itemNum.")",
				FALSE
			);			
			$this->db->where('NUM', $itemNum);
			$this->db->update('SHOPITEM');

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
				}
			}
		}
		
		//Transaction 자동 커밋
		$this->db->trans_complete();		
		
		return $resultNum;
	}	

	public function setReviewDataUpdate($rvNum, $upData, $isUpload)
	{
		if ($rvNum > 0)
		{
			/*
			$result = $this->getReviewRowData($rvNum, FALSE);
			
			if (count($result) > 0){
				$orgWriteUnum =  $result['recordSet']['USER_NUM'];
			}
			
			if ($orgWriteUnum != $this->common->getSession('user_num')){
				$this->common->message('작성자만 수정할 수 있습니다.', '', 'self');
			}
			*/			
			
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();

			$this->db->where('NUM', $rvNum);
			$this->db->update($this->tbl, $upData);	//배열로 업데이트 가능
			
			//별점 처리
			$score = $upData['SCORE'];
			$itemNum = $upData['SHOPITEM_NUM'];
			
			//총점 및 평균 update
			$this->db->set(
				'TOTSCORE',
				"(SELECT SUM(SCORE) FROM ".$this->tbl." WHERE DEL_YN = 'N' AND SHOPITEM_NUM = ".$itemNum.")",
				FALSE
			);
			$this->db->set(
				'TOTSCORE_AVG',
				"(SELECT AVG(SCORE) FROM ".$this->tbl." WHERE DEL_YN = 'N' AND SHOPITEM_NUM = ".$itemNum.")",
				FALSE
			);
			$this->db->where('NUM', $itemNum);
			$this->db->update('SHOPITEM');			
			
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$rvNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $rvNum
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
						$this->db->where('TBL_NUM', $rvNum);
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
		
		return $rvNum;
	}

	/**
	 * @method name : setReviewDataDelete
	 * 1건 삭제
	 * 
	 * @param unknown $rvNum
	 */
	public function setReviewDataDelete($rvNum)
	{
		$result = $this->getReviewRowData($rvNum, TRUE);

		if (count($result['recordSet']) > 0)
		{
			$orgWriteUnum =  $result['recordSet']['USER_NUM'];
		}
		
		/*
		if ($orgWriteUnum != $this->common->getSession('user_num'))
		{
			$this->common->message('작성자만 삭제할 수 있습니다.', '', 'self');
		}
		*/
		
		//Transaction 시작 (자동 수행)
		//$this->db->trans_start();
				
		$this->db->set('DEL_YN', 'Y');			
		$this->db->where('NUM', $rvNum);
		$this->db->update($this->tbl);
		
		//Transaction 자동 커밋
		//$this->db->trans_complete();		
	}
	
	/**
	 * @method name : setReviewGroupDataDelete
	 * 체크된 내용 모두 삭제
	 * 
	 * @param unknown $delData
	 */
	public function setReviewGroupDataDelete($delData)
	{
		$arrDelData = explode(',', $delData);
		foreach ($arrDelData as $dt) 
		{
			$this->setReviewDataDelete($dt);
		}
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