<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * EditorUpload
 * CKEditor image upload
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class EditorUpload extends CI_Controller {
	
	public function __construct() {
		parent::__construct ();
	}
	
	public function index()
	{
		$uploadResult = $this->common->fileUpload($this->getUploadOption('/editor/'));
		
		if (array_key_exists('error', $uploadResult))
		{
			$errMsg = str_replace('<p>', '', $uploadResult['error']);
			$errMsg = str_replace('</p>', '', $errMsg);
			$this->common->message($errMsg, '-', '');
		}
		else
		{
			$funcNum = $this->input->post_get('CKEditorFuncNum', FALSE);
			$fileName = $uploadResult[0]['FILE_TEMPNAME'];
			$fullUploadPath = $this->common->getDomain().$this->config->item('base_uploadPath').'/editor/'.$fileName;
			
			//CKEditor callback func 호출 - 이미지 경로를 넘겨줌
			echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$fullUploadPath', 'complete!');</script>";
		}		
	}
	
	/**
	 * @method name : getUploadOption
	 * 
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
		$config['max_size'] = (1024 * 2);	// 2메가 (단위 KB)
		//$config['max_width'] = '1024';
		//$config['max_height'] = '768';
		$config['overwrite'] = FALSE;
		$config['encrypt_name'] = TRUE;
		$config['remove_spaces'] = TRUE;		
		$config['create_thumbnail'] = FALSE;
		
		return $config;
	}	
}	