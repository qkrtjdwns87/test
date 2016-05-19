<?
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Main_model
 *
 *
 * @author : Administrator
 * @date    : 2016. 02
 * @version:
 */
class Main_model extends CI_Model{

	protected $_fileTbl = 'COMMON_FILE';
	
	protected $_encKey = '';
	
	public function __construct() {
		parent::__construct();

		$this->load->library(array('session'));
		$this->load->database(); // Database Load
		$this->tbl = 'MALLMAIN';
		
		$this->_encKey = $this->config->item('encryption_key');
		
		//여기서 사용될 TABLE CODE.NUM
		$this->_tblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', $this->tbl);		
	}
	
	/**
	 * @method name : getStoryMainRowData
	 * 메인 스토리 내용 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $uNum (회원고유번호 - 로그인한경우등)
	 * @param unknown $isDelView
	 */
	public function getStoryMainRowData($mmNum, $uNum = 0, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'STORY');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
		
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N' AND b.DEL_YN = 'N'" : '';
			
			$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
			if (isset($uNum))
			{
				$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'STORY');
				$addSelect = ($uNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$flagTblCodeNum."
						AND TBL_NUM = c.NUM
						AND USER_NUM = ".$uNum."
						AND DEL_YN = 'N'
					) AS ITEM_FLAG,
					EXISTS (
						SELECT 1 FROM ORDERITEM
						WHERE SHOPITEM_NUM = e.NUM
						AND ORDERPART_NUM IN (
							SELECT NUM FROM ORDERPART
							WHERE ORDERS_NUM IN (
								SELECT NUM FROM ORDERS WHERE USER_NUM = ".$uNum." AND DEL_YN = 'N'
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
				a.NUM AS MALLMAIN_NUM,
				".$addSelect.",					
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_STORY AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->join('STORY AS c', 'b.STORY_NUM = c.NUM', 'left outer');
			$this->db->join('SHOP AS d', 'b.SHOP_NUM = d.NUM', 'left outer');
			$this->db->join('SHOPITEM AS e', 'b.SHOPITEM_NUM = e.NUM', 'left outer');	
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
			
			//등록 내용
			$this->db->select("
				b.*,
				c.NUM AS STORY_NUM,
				c.TITLE AS STORY_TITLE,
				c.USER_NAME AS STORY_USER_NAME,
				d.SHOP_NAME,
				d.SHOP_CODE,
				d.SHOPUSER_NAME,
				AES_DECRYPT(UNHEX(d.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
				e.SHOP_NUM,
				e.ITEM_NAME,
				e.ITEM_CODE,
				e.STOCKFREE_YN,
				e.STOCK_COUNT,	
				e.ITEMSTATECODE_NUM,
				e.ITEM_PRICE,
				e.DISCOUNT_YN,
				e.DISCOUNT_PRICE,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM SHOPITEM_FILE
					WHERE SHOPITEM_NUM = e.NUM 
					AND DEL_YN = 'N'
					AND FILE_USE = 'W'
					ORDER BY NUM LIMIT 1
				) AS FILE_INFO,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM SHOPITEM_FILE
					WHERE SHOPITEM_NUM = e.NUM 
					AND DEL_YN = 'N'
					AND FILE_USE = 'M'
					ORDER BY NUM LIMIT 1
				) AS M_FILE_INFO
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_STORY AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->join('STORY AS c', 'b.STORY_NUM = c.NUM', 'left outer');
			$this->db->join('SHOP AS d', 'b.SHOP_NUM = d.NUM', 'left outer');
			$this->db->join('SHOPITEM AS e', 'b.SHOPITEM_NUM = e.NUM', 'left outer');			
			$this->db->where($whSql);
			$this->db->order_by('b.STORY_ORDER', 'ASC');
			$result['recStorySet'] = $this->db->get()->result_array();

			//log_message('debug', $this->db->last_query());

			//파일등록정보(STORY_CONTENT)
			$this->db->select('*');
			$this->db->from($this->_fileTbl);
			$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
			$this->db->where("TBL_NUM = ".$mmNum);
			$this->db->where("DEL_YN", "N");
			$this->db->order_by('FILE_ORDER', 'ASC');
			$this->db->order_by('NUM', 'ASC');
			$result['fileSet'] = $this->db->get()->result_array();			

			log_message('debug', $this->db->last_query());
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}
	
	/**
	 * @method name : setStoryMainDataInsert
	 * 메인 스토리 구성
	 * 한개의 data만 유지 
	 *  
	 * @param unknown $insData
	 * @param unknown $storyData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setStoryMainDataInsert($insData, $storyData, $isUpload)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'STORY');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
		
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setStoryMainDataUpdate($mmNum, $insData, $storyData, $isUpload);
		}
		else 
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
			
			//story 세부 내용
			if ($resultNum > 0 && !empty($storyData))
			{
				usort($storyData, $this->common->msort(['order', SORT_ASC]));
				for($i=0; $i<count($storyData); $i++)
				{
					//$storyNum = (!empty($storyData[$i]['storyno'])) ? $storyData[$i]['storyno'] : $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'STORY');
					$this->db->insert(
						$this->tbl.'_STORY',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'SHOPITEM_NUM' => (!empty($storyData[$i]['itemno'])) ? $storyData[$i]['itemno'] : NULL,
							'SHOP_NUM' => NULL,	//$storyData[$i]['itemno'],
							'STORY_NUM' => (!empty($storyData[$i]['storyno'])) ? $storyData[$i]['storyno'] : NULL,
							'STORY_ORDER' => $i,
							'CONTENT' => $storyData[$i]['storycontent']
						)
					);
					$storyResultNum = $this->db->insert_id();
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
							$this->db->insert($this->_fileTbl, $uploadResult[$i]);
						}
					}
				}
			}
			
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $resultNum;		
	}
	
	/**
	 * @method name : setStoryMainDataUpdate
	 * 메인 스토리 구성 update
	 * 
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $storyData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setStoryMainDataUpdate($mmNum, $upData, $storyData, $isUpload)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);			
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
			
			usort($storyData, $this->common->msort(['order', SORT_ASC]));			
			for($i=0; $i<count($storyData); $i++)
			{
				//$storyNum = (!empty($storyData[$i]['storyno'])) ? $storyData[$i]['storyno'] : $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'STORY');
					
				$this->db->where('NUM', $storyData[$i]['num']);
				$this->db->update(
					$this->tbl.'_STORY',
					array(
						'SHOPITEM_NUM' => (!empty($storyData[$i]['itemno'])) ? $storyData[$i]['itemno'] : NULL,
						'SHOP_NUM' => NULL,	//$storyData[$i]['itemno'],
						'STORY_NUM' => (!empty($storyData[$i]['storyno'])) ? $storyData[$i]['storyno'] : NULL,
						'CONTENT' => $storyData[$i]['storycontent']
					)
				);
			}			
			
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$mmNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $mmNum,
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
						$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
						$this->db->where('TBL_NUM', $mmNum);
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
				}
			}			
			
			//Transaction 자동 커밋
			$this->db->trans_complete();			
		}
		
		return $mmNum;
	}
	
	/**
	 * @method name : setStoryMainContentDelete
	 * 메인 스토리 컨텐츠 삭제 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $contentType
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @return number
	 */
	public function setStoryMainContentDelete($mmNum, $contentType, $contentNum, $contentOrder)
	{
		$result = 0;
		if ($mmNum > 0)
		{
			if ($contentType == 'file')
			{
				$tmpData = array(
					'FILE_NAME' => '',
					'FILE_TEMPNAME' => '',
					'FILE_TYPE' => '',
					'FILE_SIZE' => 0,
					'IMAGE_YN' => 'N',
					'THUMB_YN' => 'N',
					'IMAGE_WIDTH' => 0,
					'IMAGE_HEIGHT' => 0
				);
				
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->_fileTbl, $tmpData);
				$result = $this->db->affected_rows();
			}
			else if ($contentType == 'story')
			{
				$this->db->set('STORY_NUM', NULL);
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->tbl.'_STORY');
				$result = $this->db->affected_rows();				
			}
			else if ($contentType == 'shop')
			{
				$this->db->set('SHOP_NUM', NULL);
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->tbl.'_STORY');
				$result = $this->db->affected_rows();
			}	
			else if ($contentType == 'item')
			{
				$this->db->set('SHOPITEM_NUM', NULL);
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->tbl.'_STORY');
				$result = $this->db->affected_rows();
			}			
		}
		
		return $result;
	}
	
	/**
	 * @method name : getVisualMainRowData
	 * 메인 비주얼 내용  
	 * 
	 * @param unknown $mmNum
	 * @param unknown $isDelView
	 */
	public function getVisualMainRowData($mmNum, $isDelView)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'MAIN');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
	
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			$this->db->select("
				*,
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_VISUAL AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
				
			//Main Visual등록 내용
			$this->db->select("
				b.*
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_VISUAL AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where('a.NUM = '.$mmNum);
			$this->db->where('a.DEL_YN', 'N');
			$this->db->order_by('b.VISUAL_ORDER', 'ASC');
			$result['recVisualSet'] = $this->db->get()->result_array();
	
			//파일등록정보(VISUAL_CONTENT)
			$this->db->select('*');
			$this->db->from($this->_fileTbl);
			$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
			$this->db->where("TBL_NUM = ".$mmNum);
			$this->db->where("DEL_YN", "N");
			$this->db->order_by('FILE_ORDER', 'ASC');
			$this->db->order_by('NUM', 'ASC');
			$result['fileSet'] = $this->db->get()->result_array();
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}	
	
	/**
	 * @method name : setVisualMainDataInsert
	 * 메인 비주얼 구성
	 * 한개의 data만 유지 
	 * 
	 * @param unknown $insData
	 * @param unknown $visualData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setVisualMainDataInsert($insData, $visualData, $isUpload)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'MAIN');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
		
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setVisualMainDataUpdate($mmNum, $insData, $visualData, $isUpload);
		}
		else 
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
			
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
			
			//visual 세부 내용
			if ($resultNum > 0 && !empty($visualData))
			{
				usort($visualData, $this->common->msort(['order', SORT_ASC]));				
				for($i=0; $i<count($visualData); $i++)
				{
					$this->db->insert(
						$this->tbl.'_VISUAL',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'BLANK_YN' => (!empty($visualData[$i]['blankyn'])) ? $visualData[$i]['blankyn'] : 'N',
							'VISUAL_LINK' => $visualData[$i]['link'],
							'VISUAL_ORDER' => $visualData[$i]['order']
						)
					);
					$visualResultNum = $this->db->insert_id();
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
							$this->db->insert($this->_fileTbl, $uploadResult[$i]);
						}
					}
				}
			}
			
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
		
		return $resultNum;		
	}
	
	/**
	 * @method name : setVisualMainDataUpdate
	 * 메인 비주얼 구성 update 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $visualData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setVisualMainDataUpdate($mmNum, $upData, $visualData, $isUpload)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
				
			usort($visualData, $this->common->msort(['order', SORT_ASC]));			
			for($i=0; $i<count($visualData); $i++)
			{
				$this->db->where('NUM', $visualData[$i]['num']);
				$this->db->update(
					$this->tbl.'_VISUAL',
					array(
						'BLANK_YN' => (!empty($visualData[$i]['blankyn'])) ? $visualData[$i]['blankyn'] : 'N',
						'VISUAL_LINK' => $visualData[$i]['link'],
						'VISUAL_ORDER' => $visualData[$i]['order']
					)
				);
			}
				
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$mmNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $mmNum,
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
						$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
						$this->db->where('TBL_NUM', $mmNum);
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
				}
			}
				
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $mmNum;
	}	
	
	/**
	 * @method name : setVisualMainContentDelete
	 * 메인 비주얼 컨텐츠 삭제 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $contentType
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @return number
	 */
	public function setVisualMainContentDelete($mmNum, $contentType, $contentNum, $contentOrder)
	{
		$result = 0;
		if ($mmNum > 0)
		{
			if ($contentType == 'file')
			{
				$tmpData = array(
					'FILE_NAME' => '',
					'FILE_TEMPNAME' => '',
					'FILE_TYPE' => '',
					'FILE_SIZE' => 0,
					'IMAGE_YN' => 'N',
					'THUMB_YN' => 'N',
					'IMAGE_WIDTH' => 0,
					'IMAGE_HEIGHT' => 0
				);
	
				$this->db->where('NUM', $mmNum);
				$this->db->where('FILE_ORDER', $contentOrder);
				$this->db->update($this->_fileTbl, $tmpData);
				$result = $this->db->affected_rows();
			}
		}
	
		return $result;
	}	
	
	/**
	 * @method name : getTodayMainRowData
	 * 메인 투데이스 픽 내용  
	 * 
	 * @param unknown $mmNum
	 * @param unknown $uNum (회원고유번호 - 로그인한경우등)
	 * @param unknown $isDelView
	 */
	public function getTodayMainRowData($mmNum, $uNum = 0, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'TODAYPICK');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
	
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			
			$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
			if (isset($uNum))
			{
				$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
				$addSelect = ($uNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$flagTblCodeNum."
						AND TBL_NUM = c.NUM
						AND USER_NUM = ".$uNum."
						AND DEL_YN = 'N'
					) AS ITEM_FLAG,
					EXISTS (
						SELECT 1 FROM ORDERITEM
						WHERE SHOPITEM_NUM = b.SHOPITEM_NUM
						AND ORDERPART_NUM IN (
							SELECT NUM FROM ORDERPART
							WHERE ORDERS_NUM IN (
								SELECT NUM FROM ORDERS WHERE USER_NUM = ".$uNum." AND DEL_YN = 'N'
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
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_TODAY AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
				
			//등록 내용
			$this->db->select("
				b.*,
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
				) AS M_FILE_INFO,		
				c.STOCKFREE_YN,
				c.STOCK_COUNT,	
				c.ITEMSTATECODE_NUM,
				c.ITEM_PRICE,
				c.DISCOUNT_YN,
				c.DISCOUNT_PRICE,					
				".$addSelect.",
				d.NUM AS SHOP_NUM,
				d.SHOP_NAME,
				d.SHOP_CODE,
				d.SHOPUSER_NAME,
				AES_DECRYPT(UNHEX(d.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_TODAY AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->join('SHOPITEM AS c', 'b.SHOPITEM_NUM = c.NUM', 'left outer');			
			$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM', 'left outer');
			$this->db->where('a.NUM = '.$mmNum);
			$this->db->where('a.DEL_YN', 'N');
			$this->db->order_by('b.TODAY_ORDER', 'ASC');
			$result['recTodaySet'] = $this->db->get()->result_array();
	
			//파일등록정보(TODAY_CONTENT)
			$this->db->select('*');
			$this->db->from($this->_fileTbl);
			$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
			$this->db->where("TBL_NUM = ".$mmNum);
			$this->db->where("DEL_YN", "N");
			$this->db->order_by('FILE_ORDER', 'ASC');
			$this->db->order_by('NUM', 'ASC');
			$result['fileSet'] = $this->db->get()->result_array();
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}	
	
	/**
	 * @method name : setTodayMainDataInsert
	 * 메인 투데이스 픽 구성
	 * 한개의 data만 유지  
	 * 
	 * @param unknown $insData
	 * @param unknown $todayData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setTodayMainDataInsert($insData, $todayData, $isUpload)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'TODAYPICK');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
	
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setTodayMainDataUpdate($mmNum, $insData, $todayData, $isUpload);
		}
		else
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
				
			//story 세부 내용
			if ($resultNum > 0 && !empty($todayData))
			{
				usort($todayData, $this->common->msort(['order', SORT_ASC]));				
				for($i=0; $i<count($todayData); $i++)
				{
					$this->db->insert(
						$this->tbl.'_TODAY',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'SHOPITEM_NUM' => (!empty($todayData[$i]['itemno'])) ? $todayData[$i]['itemno'] : NULL,
							'TODAY_ORDER' => $todayData[$i]['order']
						)
					);
					$storyResultNum = $this->db->insert_id();
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
		}
	
		return $resultNum;
	}
	
	/**
	 * @method name : setTodayMainDataUpdate
	 * 메인 투데이스 픽 update  
	 * 
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $todayData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setTodayMainDataUpdate($mmNum, $upData, $todayData, $isUpload)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
				
			usort($todayData, $this->common->msort(['order', SORT_ASC]));		
			for($i=0; $i<count($todayData); $i++)
			{
				$this->db->where('NUM', $todayData[$i]['num']);
				$this->db->update(
					$this->tbl.'_TODAY',
					array(
						'SHOPITEM_NUM' => (!empty($todayData[$i]['itemno'])) ? $todayData[$i]['itemno'] : NULL,
						'TODAY_ORDER' => $todayData[$i]['order']
					)
				);
			}
				
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$mmNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $mmNum
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
						$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
						$this->db->where('TBL_NUM', $mmNum);
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
				}
			}
				
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $mmNum;
	}
	
	/**
	 * @method name : setTodayMainContentDelete
	 * 메인 투데이스 픽 컨텐츠 삭제
	 * 
	 * @param unknown $mmNum
	 * @param unknown $contentType
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @return number
	 */
	public function setTodayMainContentDelete($mmNum, $contentType, $contentNum, $contentOrder)
	{
		$result = 0;
		if ($mmNum > 0)
		{
			if ($contentType == 'file')
			{
				$tmpData = array(
					'FILE_NAME' => '',
					'FILE_TEMPNAME' => '',
					'FILE_TYPE' => '',
					'FILE_SIZE' => 0,
					'IMAGE_YN' => 'N',
					'THUMB_YN' => 'N',
					'IMAGE_WIDTH' => 0,
					'IMAGE_HEIGHT' => 0
				);
	
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->_fileTbl, $tmpData);
				$result = $this->db->affected_rows();
			}
			else if ($contentType == 'item')
			{
				$this->db->set('SHOPITEM_NUM', NULL);
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->tbl.'_TODAY');
				$result = $this->db->affected_rows();
			}
		}
	
		return $result;
	}
	
	/**
	 * @method name : getTrendMainRowData
	 * 메인 트랜드 내용 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $uNum (회원고유번호 - 로그인한경우등)
	 * @param unknown $isDelView
	 */
	public function getTrendMainRowData($mmNum, $uNum = 0, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'NEWTREND');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
	
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			
			$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
			if (isset($uNum))
			{
				$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
				$addSelect = ($uNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$flagTblCodeNum."
						AND TBL_NUM = c.NUM
						AND USER_NUM = ".$uNum."
						AND DEL_YN = 'N'
					) AS ITEM_FLAG,
					EXISTS (
						SELECT 1 FROM ORDERITEM
						WHERE SHOPITEM_NUM = b.SHOPITEM_NUM
						AND ORDERPART_NUM IN (
							SELECT NUM FROM ORDERPART
							WHERE ORDERS_NUM IN (
								SELECT NUM FROM ORDERS WHERE USER_NUM = ".$uNum." AND DEL_YN = 'N'
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
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_TREND AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
	
			//등록 내용
			$this->db->select("
				b.*,
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
				) AS M_FILE_INFO,
				c.STOCKFREE_YN,
				c.STOCK_COUNT,	
				c.ITEMSTATECODE_NUM,
				c.ITEM_PRICE,					
				c.DISCOUNT_YN,
				c.DISCOUNT_PRICE,					
				".$addSelect.",
				d.NUM AS SHOP_NUM,
				d.SHOP_NAME,
				d.SHOP_CODE,
				d.SHOPUSER_NAME,
				AES_DECRYPT(UNHEX(d.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_TREND AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->join('SHOPITEM AS c', 'b.SHOPITEM_NUM = c.NUM', 'left outer');
			$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM', 'left outer');
			$this->db->where('a.NUM = '.$mmNum);
			$this->db->where('a.DEL_YN', 'N');
			$this->db->order_by('b.TREND_ORDER', 'ASC');
			$result['recTrendSet'] = $this->db->get()->result_array();
	
			//파일등록정보(TODAY_CONTENT)
			$this->db->select('*');
			$this->db->from($this->_fileTbl);
			$this->db->where("TBLCODE_NUM = ".$this->_tblCodeNum);
			$this->db->where("TBL_NUM = ".$mmNum);
			$this->db->where("DEL_YN", "N");
			$this->db->order_by('FILE_ORDER', 'ASC');
			$this->db->order_by('NUM', 'ASC');
			$result['fileSet'] = $this->db->get()->result_array();
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}
	
	/**
	 * @method name : setTrendMainDataInsert
	 * 메인 트랜드 구성
	 * 한개의 data만 유지 
	 * 
	 * @param unknown $insData
	 * @param unknown $trendData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setTrendMainDataInsert($insData, $trendData, $isUpload)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'NEWTREND');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
	
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setTrendMainDataUpdate($mmNum, $insData, $trendData, $isUpload);
		}
		else
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
	
			//story 세부 내용
			if ($resultNum > 0 && !empty($trendData))
			{
				usort($trendData, $this->common->msort(['order', SORT_ASC]));
				for($i=0; $i<count($trendData); $i++)
				{
					$this->db->insert(
						$this->tbl.'_TREND',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'SHOPITEM_NUM' => (!empty($trendData[$i]['itemno'])) ? $trendData[$i]['itemno'] : NULL,
							'TREND_ORDER' => $trendData[$i]['order']
						)
					);
					$trendResultNum = $this->db->insert_id();
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
		}
	
		return $resultNum;
	}
	
	/**
	 * @method name : setTrendMainDataUpdate
	 * 메인 트랜트 update 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $trendData
	 * @param unknown $isUpload
	 * @return Ambiguous
	 */
	public function setTrendMainDataUpdate($mmNum, $upData, $trendData, $isUpload)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
	
			usort($trendData, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($trendData); $i++)
			{
				$this->db->where('NUM', $trendData[$i]['num']);
				$this->db->update(
					$this->tbl.'_TREND',
					array(
						'SHOPITEM_NUM' => (!empty($trendData[$i]['itemno'])) ? $trendData[$i]['itemno'] : NULL,
						'TREND_ORDER' => $trendData[$i]['order']
					)
				);
			}
	
			if ($isUpload)
			{
				//추가할 COMMON_FILE 컬럼을 config에 같이 추가
				$upConfig = array_merge(
					$this->getUploadOption('/'.strtolower($this->tbl).'/'.$mmNum.'/'),
					array(
						'TBLCODE_NUM' => $this->_tblCodeNum,
						'TBL_NUM' => $mmNum
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
						$this->db->where('TBLCODE_NUM', $this->_tblCodeNum);
						$this->db->where('TBL_NUM', $mmNum);
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
				}
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $mmNum;
	}
	
	/**
	 * @method name : setTrendMainContentDelete
	 * 메인 트랜드 컨텐츠 삭제 
	 * 
	 * @param unknown $mmNum
	 * @param unknown $contentType
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @return number
	 */
	public function setTrendMainContentDelete($mmNum, $contentType, $contentNum, $contentOrder)
	{
		$result = 0;
		if ($mmNum > 0)
		{
			if ($contentType == 'file')
			{
				$tmpData = array(
					'FILE_NAME' => '',
					'FILE_TEMPNAME' => '',
					'FILE_TYPE' => '',
					'FILE_SIZE' => 0,
					'IMAGE_YN' => 'N',
					'THUMB_YN' => 'N',
					'IMAGE_WIDTH' => 0,
					'IMAGE_HEIGHT' => 0
				);
	
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->_fileTbl, $tmpData);
				$result = $this->db->affected_rows();
			}
			else if ($contentType == 'item')
			{
				$this->db->set('SHOPITEM_NUM', NULL);
				$this->db->where('NUM', $contentNum);
				$this->db->update($this->tbl.'_TREND');
				$result = $this->db->affected_rows();
			}
		}
	
		return $result;
	}	
	
	/**
	 * @method name : getRecommSearchMainRowData
	 * 메인 추천검색어 내용
	 * 
	 * @param unknown $mmNum
	 * @param unknown $isDelView
	 */
	public function getRecommSearchMainRowData($mmNum, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'RECOMMSEARCH');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
	
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			$this->db->select("
				*,
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_SEARCHWORD AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
	
			//Main Visual등록 내용
			$this->db->select("
				b.*
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_SEARCHWORD AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where('a.NUM = '.$mmNum);
			$this->db->where('a.DEL_YN', 'N');
			$this->db->order_by('b.SEARCHWORD_ORDER', 'ASC');
			$result['recommSearchSet'] = $this->db->get()->result_array();
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}	
	
	/**
	 * @method name : setRecommSearchMainDataInsert
	 * 메인 추천검색어 구성 insert
	 * 
	 * @param unknown $insData
	 * @param unknown $recommData
	 * @return Ambiguous
	 */
	public function setRecommSearchMainDataInsert($insData, $recommData)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'RECOMMSEARCH');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
	
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setRecommSearchMainDataUpdate($mmNum, $insData, $recommData);
		}
		else
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
				
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
				
			//RecommSearch 세부 내용
			if ($resultNum > 0 && !empty($recommData))
			{
				usort($recommData, $this->common->msort(['order', SORT_ASC]));
				for($i=0; $i<count($recommData); $i++)
				{
					$this->db->insert(
						$this->tbl.'_SEARCHWORD',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'SEARCHWORD' => $recommData[$i]['word'],
							'SEARCHWORD_ORDER' => $recommData[$i]['order']
						)
					);
					$recommResultNum = $this->db->insert_id();
				}
			}
				
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $resultNum;
	}
	
	/**
	 * @method name : setRecommSearchMainDataUpdate
	 * 메인 추천검색어 구성 update
	 * 
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $recommData
	 * @return Ambiguous
	 */
	public function setRecommSearchMainDataUpdate($mmNum, $upData, $recommData)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
	
			usort($recommData, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($recommData); $i++)
			{
				$this->db->where('NUM', $recommData[$i]['num']);
				$this->db->update(
					$this->tbl.'_SEARCHWORD',
					array(
						'SEARCHWORD' => $recommData[$i]['word'],
						'SEARCHWORD_ORDER' => $recommData[$i]['order']
					)
				);
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $mmNum;
	}
	
	/**
	 * @method name : getNewItemMainRowData
	 * 메인 신상품 내용
	 *
	 * @param unknown $mmNum
	 * @param unknown $uNum (회원고유번호 - 로그인한경우등)
	 * @param unknown $isDelView
	 */
	public function getNewItemMainRowData($mmNum, $uNum = 0, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'NEWITEM');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
	
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			
			$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
			if (isset($uNum))
			{
				$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
				$addSelect = ($uNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$flagTblCodeNum."
						AND TBL_NUM = c.NUM
						AND USER_NUM = ".$uNum."
						AND DEL_YN = 'N'
					) AS ITEM_FLAG,
					EXISTS (
						SELECT 1 FROM ORDERITEM
						WHERE SHOPITEM_NUM = b.SHOPITEM_NUM
						AND ORDERPART_NUM IN (
							SELECT NUM FROM ORDERPART
							WHERE ORDERS_NUM IN (
								SELECT NUM FROM ORDERS WHERE USER_NUM = ".$uNum." AND DEL_YN = 'N'
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
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_NEWITEM AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
	
			//등록 내용
			$this->db->select("
				b.*,
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
				) AS M_FILE_INFO,
				c.STOCKFREE_YN,
				c.STOCK_COUNT,	
				c.ITEMSTATECODE_NUM,
				c.ITEM_PRICE,					
				c.DISCOUNT_YN,
				c.DISCOUNT_PRICE,					
				".$addSelect.",
				d.NUM AS SHOP_NUM,
				d.SHOP_NAME,
				d.SHOP_CODE,
				d.SHOPUSER_NAME,
				AES_DECRYPT(UNHEX(d.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_NEWITEM AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->join('SHOPITEM AS c', 'b.SHOPITEM_NUM = c.NUM', 'left outer');
			$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM', 'left outer');
			$this->db->where('a.NUM = '.$mmNum);
			$this->db->where('a.DEL_YN', 'N');
			$this->db->order_by('b.NEWITEM_ORDER', 'ASC');
			$result['newItemSet'] = $this->db->get()->result_array();
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}
	
	/**
	 * @method name : getNewItemMainRowViewData
	 * NEW item 포함한 전체 item 리스트 (VIEW 테이블 구성되어 있음)
	 * (앱메인등에서 활용 new arrival)  
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getNewItemMainRowViewData($qData)
	{
		$whSql = '1 = 1';
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND VIEW_YN = 'Y' AND ITEMSTATECODE_NUM IN (8060, 8070)" : "";
		}
	
		$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
		if (isset($qData['uNum']))
		{
			$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
			$addSelect = ($qData['uNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$flagTblCodeNum."
					AND TBL_NUM = VIEW_MAIN_NEWITEM.SHOPITEM_NUM
					AND USER_NUM = ".$qData['uNum']."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = VIEW_MAIN_NEWITEM.SHOPITEM_NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$qData['uNum']." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
		}	
		
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('VIEW_MAIN_NEWITEM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,
			AES_DECRYPT(UNHEX(SHOP_TEL), '".$this->_encKey."') AS SHOP_TEL_DEC,
			AES_DECRYPT(UNHEX(SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
			".$addSelect.",
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = VIEW_MAIN_NEWITEM.SHOPITEM_NUM
				AND DEL_YN = 'N'
				AND FILE_USE = 'W'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = VIEW_MAIN_NEWITEM.SHOPITEM_NUM
				AND DEL_YN = 'N'
				AND FILE_USE = 'M'
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO
		");
		$this->db->from('VIEW_MAIN_NEWITEM');
		$this->db->order_by('AD_YN', 'DESC'); //광고 상품은 항상 위에
		$this->db->order_by('NEWITEM_ORDER', 'ASC');
		//$this->db->order_by('SHOPITEM_NUM', 'DESC');
		// yong mod - random 적용
		$this->db->order_by('SHOPITEM_NUM', 'RANDOM');
		$this->db->limit($qData['listCount'], $limitStart);
		$this->db->where($whSql);
		// log_message('debug', 'circus - ' . $this->db->last_query());
		$rowData = $this->db->get()->result_array();

		//log_message('debug', 'circus - ' . $this->db->last_query());

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;

		//log_message('debug', 'circus - ' . $this->db->last_query());
	
		return $result;
	}	
	
	/**
	 * @method name : setNewItemMainDataInsert
	 * 메인 신상품 구성
	 * 한개의 data만 유지
	 *
	 * @param unknown $insData
	 * @param unknown $newItemData
	 * @return Ambiguous
	 */
	public function setNewItemMainDataInsert($insData, $newItemData)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'NEWITEM');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
	
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setNewItemMainDataUpdate($mmNum, $insData, $newItemData);
		}
		else
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
	
			//story 세부 내용
			if ($resultNum > 0 && !empty($newItemData))
			{
				usort($newItemData, $this->common->msort(['order', SORT_ASC]));
				for($i=0; $i<count($newItemData); $i++)
				{
					$this->db->insert(
						$this->tbl.'_NEWITEM',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'SHOPITEM_NUM' => (!empty($newItemData[$i]['itemno'])) ? $newItemData[$i]['itemno'] : NULL,
							'NEWITEM_ORDER' => $newItemData[$i]['order']
						)
					);
					$newItemResultNum = $this->db->insert_id();
				}
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $resultNum;
	}
	
	/**
	 * @method name : setNewItemMainDataUpdate
	 * 메인 신상품 update
	 *
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $newItemData
	 * @return Ambiguous
	 */
	public function setNewItemMainDataUpdate($mmNum, $upData, $newItemData)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
	
			usort($newItemData, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($newItemData); $i++)
			{
				$this->db->select("
					EXISTS (
						SELECT 1 FROM ".$this->tbl."_NEWITEM
						WHERE NUM = ".$newItemData[$i]['num']."
					) AS RESULT
				");
				$isExist = $this->db->get()->row()->RESULT;
				if (!$isExist) //없는경우 data 생성
				{
					$this->db->insert(
						$this->tbl.'_NEWITEM',
						array(
							'MALLMAIN_NUM' => $mmNum,
							'SHOPITEM_NUM' => (!empty($newItemData[$i]['itemno'])) ? $newItemData[$i]['itemno'] : NULL,
							'NEWITEM_ORDER' => $newItemData[$i]['order']
						)
					);
				}
				else
				{
					$this->db->where('NUM', $newItemData[$i]['num']);
					$this->db->update(
						$this->tbl.'_NEWITEM',
						array(
							'SHOPITEM_NUM' => (!empty($newItemData[$i]['itemno'])) ? $newItemData[$i]['itemno'] : NULL,
							'NEWITEM_ORDER' => $newItemData[$i]['order']
						)
					);
				}
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $mmNum;
	}
	
	/**
	 * @method name : setNewMainContentDelete
	 * 메인 신상품 컨텐츠 삭제
	 *
	 * @param unknown $mmNum
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @return number
	 */
	public function setNewItemMainContentDelete($mmNum, $contentNum, $contentOrder)
	{
		$result = 0;
		if ($mmNum > 0)
		{
			$this->db->set('SHOPITEM_NUM', NULL);
			$this->db->where('NUM', $contentNum);
			$this->db->update($this->tbl.'_NEWITEM');
			$result = $this->db->affected_rows();
		}
	
		return $result;
	}	
	
	/**
	 * @method name : getBestItemMainRowData
	 * 메인 베스트셀러 내용
	 *
	 * @param unknown $mmNum
	 * @param unknown $uNum (회원고유번호 - 로그인한경우등)
	 * @param unknown $isDelView
	 */
	public function getBestItemMainRowData($mmNum, $uNum = 0, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'BESTITEM');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
	
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "a.NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND a.DEL_YN = 'N'" : '';
			
			$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
			if (isset($uNum))
			{
				$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
				$addSelect = ($uNum > 0) ? "
					EXISTS (
						SELECT 1 FROM FLAG
						WHERE TBLCODE_NUM = ".$flagTblCodeNum."
						AND TBL_NUM = c.NUM
						AND USER_NUM = ".$uNum."
						AND DEL_YN = 'N'
					) AS ITEM_FLAG,
					EXISTS (
						SELECT 1 FROM ORDERITEM
						WHERE SHOPITEM_NUM = b.SHOPITEM_NUM
						AND ORDERPART_NUM IN (
							SELECT NUM FROM ORDERPART
							WHERE ORDERS_NUM IN (
								SELECT NUM FROM ORDERS WHERE USER_NUM = ".$uNum." AND DEL_YN = 'N'
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
				(SELECT TITLE FROM CODE WHERE NUM = a.MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_BESTITEM AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->where($whSql);
			$this->db->order_by('a.NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
	
			//등록 내용
			$this->db->select("
				b.*,
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
				) AS M_FILE_INFO,		
				c.STOCKFREE_YN,
				c.STOCK_COUNT,	
				c.ITEMSTATECODE_NUM,	
				c.ITEM_PRICE,					
				c.DISCOUNT_YN,
				c.DISCOUNT_PRICE,					
				".$addSelect.",
				d.NUM AS SHOP_NUM,
				d.SHOP_NAME,
				d.SHOP_CODE,
				d.SHOPUSER_NAME,
				AES_DECRYPT(UNHEX(d.SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC
			");
			$this->db->from($this->tbl.' AS a');
			$this->db->join($this->tbl.'_BESTITEM AS b', 'a.NUM = b.MALLMAIN_NUM');
			$this->db->join('SHOPITEM AS c', 'b.SHOPITEM_NUM = c.NUM', 'left outer');
			$this->db->join('SHOP AS d', 'c.SHOP_NUM = d.NUM', 'left outer');
			$this->db->where('a.NUM = '.$mmNum);
			$this->db->where('a.DEL_YN', 'N');
			$this->db->order_by('b.BESTITEM_ORDER', 'ASC');



			$result['bestItemSet'] = $this->db->get()->result_array();
		}
		else
		{
			$result = array();
		}
		
		return $result;
	}
	
	/**
	 * @method name : getBestItemMainRowViewData
	 * BEST item 포함한 전체 item 리스트 (VIEW 테이블 구성되어 있음)
	 * (앱메인등에서 활용) 
	 * 
	 * @param unknown $qData
	 * @return Ambiguous
	 */
	public function getBestItemMainRowViewData($qData)
	{
		$whSql = '1 = 1';
		if (isset($qData['isValidData'])) //유효한 내용만(사용자페이지)
		{
			$whSql .= ($qData['isValidData']) ? " AND VIEW_YN = 'Y' AND ITEMSTATECODE_NUM IN (8060, 8070)" : "";
		}
		
		if (isset($qData['sNum']))
		{
			$whSql .= ' AND SHOP_NUM = '.$qData['sNum'];
		}

		log_message('debug', 'circus - ' . $whSql);

		$addSelect = '0 AS ITEM_FLAG, 0 AS ITEM_BUY';
		if (isset($qData['uNum']))
		{
			$flagTblCodeNum = $this->common->getCodeNumByCodeGrpNCodeId('TABLE', 'SHOPITEM');
			$addSelect = ($qData['uNum'] > 0) ? "
				EXISTS (
					SELECT 1 FROM FLAG
					WHERE TBLCODE_NUM = ".$flagTblCodeNum."
					AND TBL_NUM = VIEW_MAIN_BESTITEM.SHOPITEM_NUM
					AND USER_NUM = ".$qData['uNum']."
					AND DEL_YN = 'N'
				) AS ITEM_FLAG,
				EXISTS (
					SELECT 1 FROM ORDERITEM
					WHERE SHOPITEM_NUM = VIEW_MAIN_BESTITEM.SHOPITEM_NUM
					AND ORDERPART_NUM IN (
						SELECT NUM FROM ORDERPART
						WHERE ORDERS_NUM IN (
							SELECT NUM FROM ORDERS WHERE USER_NUM = ".$qData['uNum']." AND DEL_YN = 'N'
						)
						AND DEL_YN = 'N'
					)
					AND DEL_YN = 'N'
					LIMIT 1
				) AS ITEM_BUY
			" : $addSelect;
		}		
	
		$this->db->select('COUNT(*) AS COUNT');
		$this->db->from('VIEW_MAIN_BESTITEM');
		$this->db->where($whSql);
		$totalCount = $this->db->get()->row()->COUNT;		
	
		//페이징된 data select
		$limitStart = (($qData['currentPage']-1) * $qData['listCount']);
		$this->db->select("
			*,
			AES_DECRYPT(UNHEX(SHOP_EMAIL), '".$this->_encKey."') AS SHOP_EMAIL_DEC,				
			AES_DECRYPT(UNHEX(SHOP_TEL), '".$this->_encKey."') AS SHOP_TEL_DEC,
			AES_DECRYPT(UNHEX(SHOP_MOBILE), '".$this->_encKey."') AS SHOP_MOBILE_DEC,
			".$addSelect.",				
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = VIEW_MAIN_BESTITEM.SHOPITEM_NUM
				AND DEL_YN = 'N'
				AND FILE_USE = 'W'
				ORDER BY NUM LIMIT 1
			) AS FILE_INFO,
			(
				SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN)
				FROM SHOPITEM_FILE
				WHERE SHOPITEM_NUM = VIEW_MAIN_BESTITEM.SHOPITEM_NUM
				AND DEL_YN = 'N'
				AND FILE_USE = 'M'
				ORDER BY NUM LIMIT 1
			) AS M_FILE_INFO
		");
		$this->db->from('VIEW_MAIN_BESTITEM');
		$this->db->order_by('AD_YN', 'DESC'); //광고 상품은 항상 위에
		$this->db->order_by('BESTITEM_ORDER', 'ASC');
		$this->db->order_by('SHOPITEM_TOTSELL_COUNT', 'DESC');
		$this->db->order_by('SHOPITEM_TOTFLAG_COUNT', 'DESC');
		$this->db->order_by('SHOPITEM_NUM', 'DESC');
		$this->db->where($whSql);
		$this->db->limit($qData['listCount'], $limitStart);
		$rowData = $this->db->get()->result_array();

		$result['rsTotalCount'] = $totalCount;
		$result['recordSet'] = $rowData;

//		log_message('debug', $this->db->last_query());
	
		return $result;
	}	
	
	/**
	 * @method name : setBestItemMainDataInsert
	 * 메인 베스트셀러 구성
	 * 한개의 data만 유지
	 *
	 * @param unknown $insData
	 * @param unknown $BestItemData
	 * @return Ambiguous
	 */
	public function setBestItemMainDataInsert($insData, $bestItemData)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'BESTITEM');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
	
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setBestItemMainDataUpdate($mmNum, $insData, $bestItemData);
		}
		else
		{
			//없으면 insert
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
	
			//story 세부 내용
			if ($resultNum > 0 && !empty($bestItemData))
			{
				usort($bestItemData, $this->common->msort(['order', SORT_ASC]));
				for($i=0; $i<count($bestItemData); $i++)
				{
					$this->db->insert(
						$this->tbl.'_BESTITEM',
						array(
							'MALLMAIN_NUM' => $resultNum,
							'SHOPITEM_NUM' => (!empty($bestItemData[$i]['itemno'])) ? $bestItemData[$i]['itemno'] : NULL,
							'BESTITEM_ORDER' => $bestItemData[$i]['order']
						)
					);
					$bestItemResultNum = $this->db->insert_id();
				}
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $resultNum;
	}
	
	/**
	 * @method name : setBestItemMainDataUpdate
	 * 메인 베스트셀러 update
	 *
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @param unknown $bestItemData
	 * @return Ambiguous
	 */
	public function setBestItemMainDataUpdate($mmNum, $upData, $bestItemData)
	{
		if ($mmNum > 0)
		{
			//Transaction 시작 (자동 수행)
			$this->db->trans_start();
	
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
	
			usort($bestItemData, $this->common->msort(['order', SORT_ASC]));
			for($i=0; $i<count($bestItemData); $i++)
			{
				$this->db->select("
					EXISTS (
						SELECT 1 FROM ".$this->tbl."_BESTITEM
						WHERE NUM = ".$bestItemData[$i]['num']."
					) AS RESULT
				");
				$isExist = $this->db->get()->row()->RESULT;
				if (!$isExist) //없는경우 data 생성
				{
					$this->db->insert(
						$this->tbl.'_BESTITEM',
						array(
							'MALLMAIN_NUM' => $mmNum,
							'SHOPITEM_NUM' => (!empty($bestItemData[$i]['itemno'])) ? $bestItemData[$i]['itemno'] : NULL,
							'BESTITEM_ORDER' => $bestItemData[$i]['order']
						)
					);
				}
				else 
				{
					$this->db->where('NUM', $bestItemData[$i]['num']);
					$this->db->update(
						$this->tbl.'_BESTITEM',
						array(
							'SHOPITEM_NUM' => (!empty($bestItemData[$i]['itemno'])) ? $bestItemData[$i]['itemno'] : NULL,
							'BESTITEM_ORDER' => $bestItemData[$i]['order']
						)
					);					
				}
			}
	
			//Transaction 자동 커밋
			$this->db->trans_complete();
		}
	
		return $mmNum;
	}
	
	/**
	 * @method name : setBestItemMainContentDelete
	 * 메인 베스트셀러 컨텐츠 삭제
	 *
	 * @param unknown $mmNum
	 * @param unknown $contentNum
	 * @param unknown $contentOrder
	 * @return number
	 */
	public function setBestItemMainContentDelete($mmNum, $contentNum, $contentOrder)
	{
		$result = 0;
		if ($mmNum > 0)
		{
			$this->db->set('SHOPITEM_NUM', NULL);
			$this->db->where('NUM', $contentNum);
			$this->db->update($this->tbl.'_BESTITEM');
			$result = $this->db->affected_rows();
		}
	
		return $result;
	}	
	
	/**
	 * @method name : getPassChangeMainRowData
	 * 비번관리  
	 * 
	 * @param unknown $mmNum
	 * @param unknown $isDelView
	 */
	public function getPassChangeMainRowData($mmNum, $isDelView = FALSE)
	{
		if (empty($mmNum) || $mmNum == 0)
		{
			$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'PASSCHANGE');
			$this->db->select('NUM');
			$this->db->from($this->tbl);
			$this->db->where('MALLCODE_NUM', $codeNum);
			$this->db->where('DEL_YN', 'N');
			$this->db->where('USE_YN', 'Y');
			$this->db->order_by('NUM', 'DESC');
			$result = $this->db->get()->row_array();
			$mmNum = ($result) ? $result['NUM'] : 0;
		}
		
		if (!empty($mmNum) && $mmNum > 0)
		{
			$whSql = "NUM = ".$mmNum;
			$whSql .= (!$isDelView) ? " AND DEL_YN = 'N'" : '';
			$this->db->select("
				*,
				(SELECT TITLE FROM CODE WHERE NUM = MALLCODE_NUM) AS MALLCODE_TITLE,
			");
			$this->db->limit(1);
			$this->db->from($this->tbl);
			$this->db->where($whSql);
			$this->db->order_by('NUM', 'DESC');
			$result['recordSet'] = $this->db->get()->row_array();
		}
		else 
		{
			$result = array();			
		}
		
		return $result;
	}
	
	/**
	 * @method name : setPassChangeMainDataInsert
	 * 비번관리 insert
	 * 
	 * @param unknown $insData
	 * @return Ambiguous
	 */
	public function setPassChangeMainDataInsert($insData)
	{
		$codeNum = $this->common->getCodeNumByCodeGrpNCodeId('MALL', 'PASSCHANGE');
		$this->db->select('NUM');
		$this->db->from($this->tbl);
		$this->db->where('MALLCODE_NUM', $codeNum);
		$this->db->where('DEL_YN', 'N');
		$this->db->where('USE_YN', 'Y');
		$this->db->order_by('NUM', 'DESC');
		$result = $this->db->get()->row_array();
		$mmNum = ($result) ? $result['NUM'] : 0;
	
		if ($mmNum > 0)
		{
			//기존내용이 있는 경우 update
			$resultNum = $this->setPassChangeMainDataUpdate($mmNum, $insData);
		}
		else
		{
			//없으면 insert
			$this->db->insert($this->tbl, $insData);
			$resultNum = $this->db->insert_id();	//insert후 반영된 최종 NUM
		}
	
		return $resultNum;
	}	
	
	/**
	 * @method name : setPassChangeMainDataUpdate
	 * 비번관리 update
	 * 
	 * @param unknown $mmNum
	 * @param unknown $upData
	 * @return Ambiguous
	 */
	public function setPassChangeMainDataUpdate($mmNum, $upData)
	{
		if ($mmNum > 0)
		{
			unset($upData['USER_NUM']);
			unset($upData['MALLCODE_NUM']);
			unset($upData['USE_YN']);
			unset($upData['REMOTEIP']);
			$this->db->where('NUM', $mmNum);
			$this->db->update($this->tbl, $upData);
		}
	
		return $mmNum;
	}	
	
	
	/**
	 * @method name : getAdminMainData
	 * 관리자 메인
	 * 
	 * @param unknown $qData
	 * @param unknown $userNum
	 */
	public function getAdminMainData($qData)
	{
		$toDate = date('Y-m-d');
		
		//메시지 현황
		if ($qData['userLevel'] == 'SHOP')
		{
			//샵관리자인 경우
			$sql ="
				SELECT
					COUNT(*),
					(
						SELECT COUNT(*) FROM MESSAGE
						WHERE DEL_YN = 'N'
						AND READ_YN = 'N'
						AND TOUSER_NUM = ".$qData['userNum']."
						AND SENDER_TYPE = 'M'
					) AS NO_READ_SHOP_CNT,
					(
						SELECT COUNT(*) FROM MESSAGE
						WHERE DEL_YN = 'N'
						AND READ_YN = 'N'
						AND TOUSER_NUM = ".$qData['userNum']."
						AND SENDER_TYPE = 'U'
					) AS NO_READ_USER_CNT
				FROM MESSAGE
				WHERE DEL_YN = 'N'
				AND TOUSER_NUM = ".$qData['userNum']."
			";			
		}
		else 
		{
			$sql ="
				SELECT
					COUNT(*),
					(
						SELECT COUNT(*) FROM MESSAGE
						WHERE DEL_YN = 'N'
						AND READ_YN = 'N'
						AND TOUSER_NUM = ".$qData['userNum']."
						AND SENDER_TYPE = 'S'
					) AS NO_READ_SHOP_CNT,
					(
						SELECT COUNT(*) FROM MESSAGE
						WHERE DEL_YN = 'N'
						AND READ_YN = 'N'
						AND TOUSER_NUM = ".$qData['userNum']."
						AND SENDER_TYPE = 'U'
					) AS NO_READ_USER_CNT
				FROM MESSAGE
				WHERE DEL_YN = 'N'
				AND TOUSER_NUM = ".$qData['userNum']."
			";			
		}

		$result['m_msgSet'] = $this->db->query($sql)->row_array();

		//회원현황
		$sql = "
			SELECT 
				COUNT(*) AS USER_CNT,
				(
					SELECT COUNT(*) FROM USER
					WHERE DATE_FORMAT(CREATE_DATE, '%Y-%m-%d') = '".$toDate."'
					AND DEL_YN = 'N'
					AND ULEVELCODE_NUM < 800
				) AS JOIN_USER_CNT
			FROM USER
			WHERE DEL_YN = 'N'
			AND ULEVELCODE_NUM < 800							
		";
		$result['m_userSet'] = $this->db->query($sql)->row_array();
		
		//SHOP 현황
		$sql = "
			SELECT
				COUNT(*)AS SHOP_CNT,
				(
					SELECT COUNT(*) FROM SHOP
					WHERE DATE_FORMAT(APPROVAL_FIRSTREQ_DATE, '%Y-%m-%d') = '".$toDate."'
					AND DEL_YN = 'N'
					AND SHOPSTATECODE_NUM = 3020
				) AS REQ_APPR_SHOP_CNT
			FROM SHOP
			WHERE DEL_YN = 'N'
		";
		$result['m_shopSet'] = $this->db->query($sql)->row_array();
		
		//ITEM 현황
		$whSql = '';
		if (isset($qData['shopNum']))
		{
			if ($qData['shopNum'] > 0)
			{
				$whSql = ' AND SHOP_NUM = '.$qData['shopNum'];
			}
		}
				
		$sql = "
			SELECT
				COUNT(*) AS ITEM_CNT, 
				(
					SELECT COUNT(*) FROM SHOPITEM
					WHERE DATE_FORMAT(APPROVAL_FIRSTREQ_DATE, '%Y-%m-%d') = '".$toDate."'
					AND DEL_YN = 'N'
					AND ITEMSTATECODE_NUM = 8020
					".$whSql."
				) AS TODAY_REQ_APPR_ITEM_CNT,
		"; //총합, 오늘자 신규승인신청
		
		$sql .= "
				(
					SELECT COUNT(*) FROM SHOPITEM
					WHERE DEL_YN = 'N'
					AND ITEMSTATECODE_NUM = 8020
					".$whSql."
				) AS REQ_APPR_ITEM_CNT,
		"; //승인요청 아이템 - 누적		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM SHOPITEM
					WHERE DEL_YN = 'N'
					AND ITEMSTATECODE_NUM = 8060 
					".$whSql."
				) AS ING_ITEM_CNT,
		"; //판매중 아이템
		
		$sql .= "
				(
					SELECT COUNT(*) FROM SHOPITEM
					WHERE DEL_YN = 'N'
					AND ITEMSTATECODE_NUM = 8080
					".$whSql."
				) AS STOP_ITEM_CNT,
		"; //판매중지 아이템
		
		$sql .= "
				(
					SELECT COUNT(*) FROM SHOPITEM
					WHERE DEL_YN = 'N'
					AND ITEMSTATECODE_NUM = 8070
					AND STOCKFREE_YN = 'N'
					AND STOCK_COUNT = 0
					".$whSql."
				) AS SOLDOUT_ITEM_CNT
		"; //품절 아이템		
		
		$sql .= "
			FROM SHOPITEM
			WHERE DEL_YN = 'N'
			".$whSql."							
		";

		$result['m_itemSet'] = $this->db->query($sql)->row_array();
		
		//주문현황
		$whSql = '';		
		if (isset($qData['shopNum']))
		{
			if ($qData['shopNum'] > 0)
			{
				$whSql = ' AND SHOP_NUM = '.$qData['shopNum'];				
			}
		}
		
		$sql = "
			SELECT
				COUNT(*) AS TOT_ORD_CNT,
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND CHECK_YN = 'N'
					".$whSql."
				) AS TODAY_ORD_CNT, 
		"; //총합, 신규주문(주문확인 안한 주문건) - 누적
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5080, 5100)
					".$whSql."
				) AS ORD_DELIVERY_STANDBY_CNT,
		"; //배송정보 등록 대기중(누적)		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5110)
					".$whSql."
				) AS ORD_CANCEL_STANDBY_CNT,
		"; //취소신청 대기중(누적)		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5130)
					".$whSql."
				) AS ORD_REFUND_STANDBY_CNT,
		"; //환불신청 대기중(누적)
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5140)
					".$whSql."
				) AS ORD_REFUND_STANDBY_FIN_CNT,
		"; //환불완료 대기중 - 환불보류상태(누적)
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND CHECK_YN = 'Y'
					AND DATE_FORMAT(CHECK_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_CHECK_CNT,
		"; //오늘자 주문확인한 주문건		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(INVOICE_WRITE_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_DELIVERY_CNT,
		"; //오늘자 배송정보 등록한 주문건		

		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(DELIVERY_END_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_DELIVERY_FIN_CNT,
		"; //오늘자 배송완료한 주문건
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(CANCEL_REJECT_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_CANCEL_DENY_CNT,
		"; //오늘자 취소불가한 주문건
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(CANCEL_END_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_CANCEL_CNT,
		"; //오늘자 취소완료한 주문건
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(REFUND_REJECT_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_REFUND_DENY_CNT,
		"; //오늘자 환불불가한 주문건		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(REFUND_END_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_REFUND_FIN_CNT,
		"; //오늘자 환불완료한 주문건		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND DATE_FORMAT(CANCEL_END_DATE, '%Y-%m-%d') = '".$toDate."'
					".$whSql."
				) AS TODAY_ORD_CANCEL_FIN_CNT,
		"; //오늘자 주문취소 주문건		
		
		$compDate = date("Y-m-d",strtotime("-7 day")); //1주일
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND CHECK_YN = 'N'
					AND DATE_FORMAT(CREATE_DATE, '%Y-%m-%d') < '".$compDate."'
					".$whSql."
				) AS DELAY_ORD_CHECK_CNT,
		"; //지연된 주문 미확인		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND INVOICE_NO IS NULL
					AND DATE_FORMAT(CREATE_DATE, '%Y-%m-%d') < '".$compDate."'
					".$whSql."
				) AS DELAY_ORD_DELIVERY_CNT,
		"; //배송정보 미등록		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5110)
					AND DATE_FORMAT(CANCEL_DATE, '%Y-%m-%d') < '".$compDate."'
					".$whSql."
				) AS DELAY_ORD_DELIVERY_CNT,
		"; //취소지연 - 취소신청상태에서 취소신청일자가 1주일 이전인 상태		
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5130)
					AND DATE_FORMAT(REFUND_DATE, '%Y-%m-%d') < '".$compDate."'
					".$whSql."
				) AS DELAY_ORD_REFUND_CNT,
		"; //환불지연 - 환불신청상태에서 환불신청일자가 1주일 이전인 상태
		
		$sql .= "
				(
					SELECT COUNT(*) FROM ORDERPART
					WHERE DEL_YN = 'N'
					AND ORDSTATECODE_NUM IN (5190)
					AND DATE_FORMAT(REFUND_DATE, '%Y-%m-%d') < '".$compDate."'
					".$whSql."
				) AS DELAY_ORD_REFUND_CNT
		"; //교환지연 - 교환신청상태에서 교환신청일자가 1주일 이전인 상태
		
		$sql .= "
			FROM ORDERPART
			WHERE DEL_YN = 'N'
			".$whSql."							
		";
		$result['m_ordSet'] = $this->db->query($sql)->row_array();
		
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