<?
defined('BASEPATH') or exit ('No direct script access allowed');
/**
 * Apitest
 * curl을 이용한 rest api client
 *
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Apitest extends CI_Controller {
	public function __construct() {
		parent::__construct ();
	
		$this->load->helper(array('url'));
	}
	
	public function mainlist(){
		$reqUrl = $this->common->getDomain()."/api/mains/main";	//post
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqUrl);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		//curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		//curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	
		$post = array(
			'method' => '',
			'page' => 1,
			'listcount' => 10
		);
	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // TimeOut 값
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // 결과값을 받을것인지
		//curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE); // required as of PHP 5.6.0
		$response = curl_exec($ch);
		$sHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//print "<pre>\n";
		//print_r(curl_getinfo($ch));  // get error info
		//echo "\n\ncURL error number:" .curl_errno($ch); // print error info
		//echo "\n\ncURL error:" . curl_error($ch);
		//echo "\n";
		print_r($response);
		//echo "\n";
		//print "</pre>\n";
		curl_close ($ch);
	}	
	
	public function index(){
		//$apiDomain = $this->common->getDomain();
		$apiDomain = 'http://api.circusflag.com';
		//$apiDomain = 'http://circus.artistchai.com';
		//$reqUrl = "http://circus.artistchai.com/api/messages/message";	//post
		//$reqUrl = "http://circus.artistchai.com/api/items/item";	//post
		//$reqUrl = $this->common->getDomain()."/api/items/item";	//post
		//$reqUrl = $this->common->getDomain()."/api/items/item";	//post

		$reqUrl = $apiDomain."/api/messages/message";	//post
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqUrl);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		//curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
		//curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	
		$file = ''; //realpath('./upload/item/4/88165ca959e7aa3aa9911e0a20e8aa3a_s.jpg');
		$post = array(
				'method' => 'update',
				'targetno' => 8, //샵고유번호 (샵작가회원번호(X))
				'touserno' => 3,
				'enno' => 1,
				'sno' => 10,
				'sino' => 48, //48,//39, //18
				'type' => 'story',
				'no' => 1,
				//'sioptsno' => '2|4', //195|342|233', //로컬 optnum 105  '195|342|233'
				//'sioptsno' => 42, //차이테스트 optnum 22
				//'ordno' => 56,
				'marketyn' => 'N',
				'urgencyyn' => 'Y',
				'openyn' => 'Y',
				'ordptno' => 83,
				'directyn' => 'Y',
				'sort' => 'pop',
				'siqty' => 1,
				'rvno' => 8,
				'msgset' => '{"msgGrpNum":"56","msgToDate":"2016-03-16","msgTargetNum":0,"msgType":17150}',
				'listcount' => 10,
				'page' => 1,				
				'item_listcount' => 5,
				'item_page' => 1,
				//'msgdate' => '2016-03-17',
				'maxmsgno' => 10,
				'msggrpno' => 10,
				'msg_content' => '메시지 테스트',
				'sword' => '1번샵',
				'deviceid' => 'af284944-bdc0-3765-a738-4e567d78019e',
				'pushid' => 'testpushid',
				//'authkey' => '5B4DD7513A41E257C8E1632F89C3A011' //로컬 usernum = 2
				//'authkey' => 'C94CB6C21677F75500E39403E1F227E2' //실서버 usernum = 8
				'authkey' => '2F377010ECEDF582B05A0F0C9464B9DC'
				//'authkey' => 'C94CB6C21677F75500E39403E1F227E2' //차이테스트 usernum = 8
		);
		if (!empty($file))
		{
			$post['userfile0'] = new CurlFile($file);
		}
	
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // TimeOut 값
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // 결과값을 받을것인지
		//curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE); // required as of PHP 5.6.0
		$response = curl_exec($ch);
		$sHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		//print "<pre>\n";
		//print_r(curl_getinfo($ch));  // get error info
		//echo "\n\ncURL error number:" .curl_errno($ch); // print error info
		//echo "\n\ncURL error:" . curl_error($ch);
		//echo "\n";
		print_r($response);
		//echo "\n";
		//print "</pre>\n";
		curl_close ($ch);
	}	
}