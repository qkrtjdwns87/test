<?
defined('BASEPATH') or exit('No direct script access allowed');
require_once($_SERVER["DOCUMENT_ROOT"].'/inc/adm/phpmailer/class.phpmailer.php');
/**
 * Common
 *
 * 공통 Library
 * @author : Administrator
 * @date    : 2015. 12
 * @version:
 */
class Common {
	
	protected $_userTbl = 'USER';
	
	protected $_encKey = '';
	
	//생성자
	public function __construct()
	{
		$this->CI = & get_instance();
		$this->CI->load->helper(array('cookie'));
		$this->CI->load->library(array('session'));
		$this->CI->load->database(); // Database Load
		
		$this->_encKey = $this->CI->config->item('encryption_key');
	}
	/**
	 * @method name : app_script 
	 * 자바스크립 alert 처리
	 * 줄넘김 시에는 \\n
	 * 
	 * @param string $str
	
	 * @return void
	 */
	public function app_script($str)
	{
		//$str = addslashes( $str );	//줄넘김이 안됨
		echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">";
		echo "<script type=\"text/javascript\" src=\"/js/app/jquery-1.9.1.js\"></script>";
		echo "<script type=\"text/javascript\" src=\"/js/app/ui.js\"></script>";
		echo "<script type=\"text/javascript\" src=\"/js/common.js\"></script>";
		echo "<script type=\"text/javascript\" src=\"/js/app_common.js\"></script>";

		echo "<SCRIPT LANGUAGE=\"JavaScript\">";
		echo $str;
		echo "</SCRIPT>";
		exit();
	}
	
	/**
	 * @method name : message 
	 * 자바스크립 alert 처리
	 * 줄넘김 시에는 \\n
	 * 
	 * @param string $str
	 * @param string $url
	 * @param string $target
	 * @return void
	 */
	public function message($str, $url = "", $target = "self")
	{
		//$str = addslashes( $str );	//줄넘김이 안됨
		echo "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=utf-8\">";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">";
		//echo "<!--\n";
		if($str)
		{
			echo "alert(\"$str\");"; //echo "alert(\"$str\");\n";
		}
		if ($target == 'js') //javascript 함수 호출
		{
			echo $url;
		}
		else 
		{
			if($url == "")
			{
				echo "history.go(-1);";
			}
			else if($url == "-")
			{
				echo "";	//액션없이 메세지만
			}
			else if($url == "close" || $target == "close")
			{
				echo "self.close();";
			}
			else if($url == "reload")
			{
				echo $target.".location.reload();";
			}
			else
			{
				echo $target.".location.href='" . $url . "';";
				if ($target == 'opener')
				{
					echo "self.close();";
				}
			}			
		}

		//echo "//-->";
		echo "</SCRIPT>";
		exit();
	}
	
	/**
	 * @method name : htmlDocToString 
	 * Html문서파일을 읽고 string으로 변환
	 * 
	 * @param string $filepath
	 * @return return_type
	 */
	public function htmlDocToString($filepath = '')
	{
		if($filepath != '')
		{
			return file_get_contents( $filepath );
		}
	}
	
	/**
	 * @method name : segmentExplode 
	 * 세크먼트 앞뒤 '/' 제거후 uri를 배열로 반환
	 * 
	 * @param string $seg 대상문자열 url
	 * @return array
	 */
	public function segmentExplode($seg)
	{
		$len = strlen($seg);
		if(substr($seg, 0, 1) == '/')
		{
			$seg = substr($seg, 1, $len);
		}
		$len = strlen($seg);
	
		if(substr($seg, -1) == '/')
		{
			$seg = substr($seg, 0, $len-1);
		}
		$seg_exp = explode("/", $seg);
	
		return $seg_exp;
	}
	
	/**
	 * @method name : urlExplode 
	 * url중 키값을 매칭 구분하여 값을 반환
	 * 
	 * @param string $url
	 * @param string $key
	 * @return array
	 */
	public function urlExplode($url, $key)
	{
		$cnt = count($url);
		for($i=0; $cnt>$i; $i++)
		{
			if($url[$i] ==$key)
			{
				$k = $i+1;
				if (isset($url[$k]))
				{
					return $url[$k];
				}
				else
				{
					return '';
				}
			}
		}
	}	
	
	/**
	 * @method name : stringReplaceMatchValue 
	 * string replace with separator and value (ex : mailform variable)
	 * 
	 * @param string $str
	 * @param string $text
	 * @param string $val
	 * @return string
	 */
	public function stringReplaceMatchValue($str = '', $text = '', $val = '') 
	{
		$arrText = explode( '|', $text ); // 구분 문자열
		$arrVal = explode( '|', $val ); // 구분 값
		
		for($i = 0; $i < count($arrText); $i ++)
		{
			$str = str_replace('{'.$arrText[$i].'}', $arrVal[$i], $str);
		}
		
		return $str;
	}
	
	public function escapeJsonString($value) {
		# list from www.json.org: (\b backspace, \f formfeed)
		$escapers =     array("\\",     "/",   "\"",  "\n",  "\r",  "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t",  "\\f",  "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}	

	public function generatePassword() { 

	    $length = 8;
	    $password = '';
		$chars = array_merge(range('a', 'z'), range('0', '9'));
		$chars = array_flip($chars);

		while($length > 0) {
		    $password .= array_rand($chars);
		    $length--;
		}
	    return $password; 
	}
	
	/**
	 * @method name : emailSend 
	 * Email send
	 * 
	 * @param array $mailDt 메일 항목
	 * @param string $server 발송메일서버 
	 */
	public function emailSend($mailDt, $server = 'gmail') 
	{
		$this->CI->load->library('email');
		
		switch($server)
		{
			case 'smtp':
				/* 외부 smtp 이용 - 성공함 */
				$config ['protocol'] = 'smtp';
				$config ['smtp_host'] = 'mail.pixelize.co.kr';
				$config ['smtp_port'] = 587;
				$config ['smtp_timeout'] = 10;
				$config ['smtp_user'] = 'churk@pixelize.co.kr';
				$config ['smtp_pass'] = 'xxxxx';
				$config ['charset'] = 'utf-8';
				$config ['newline'] = "\r\n";
				$config ['mailtype'] = 'html'; // or html
				$config ['validation'] = TRUE; // bool whether to validate email or not
				$config ['wordwrap'] = TRUE;
				break;
			case 'gmail':
				/* gmail 이용 성공함 */
				$config['protocol'] = 'smtp';
				$config['smtp_host'] = 'ssl://smtp.gmail.com';
				$config['smtp_port'] = 465;
				$config['smtp_timeout'] = 10;
				$config['smtp_user'] = $this->CI->config->item('email_id');
				$config['smtp_pass'] = $this->CI->config->item('email_pwd');
				$config['charset'] = 'utf-8';
				$config['newline'] = "\r\n";
				$config['mailtype'] = 'html'; // or html
				$config['validation'] = TRUE; // bool whether to validate email or not
				$config['wordwrap'] = TRUE;
				break;
			case 'sendmail':
				/* 확인 안됨 */
				$config['protocol'] = 'sendmail';
				$config['mailpath'] = '/usr/sbin/sendmail';
				$config['charset'] = 'utf-8';
				$config['newline'] = "\r\n";
				$config['mailtype'] = 'html'; // or html
				$config['validation'] = TRUE; // bool whether to validate email or not
				$config['wordwrap'] = TRUE;				
				break;
		}
		
		$this->CI->email->initialize($config);
		
		$this->CI->email->from($mailDt['fromEmail'], $mailDt['fromName']);
		$this->CI->email->to($mailDt['toEmail']); // 여러개 동시에 보내는 경우 'one@example.com, two@example.com'
       	$this->CI->email->cc($mailDt['cc']);
        $this->CI->email->bcc($mailDt['bcc']);
		
		$this->CI->email->subject($mailDt['subject']);
		$this->CI->email->message($mailDt['content']);
		
		if (! $this->CI->email->send())
		{
			// Generate error
			echo $this->CI->email->print_debugger();
		}
	}
	
	/**
	 * @method name : smsSend
	 * SMS발송 (cafe24 api)
	 *
	 * @param unknown $qData
	 */
	public function smsSend($qData)
	{
		/* 실적용 샘플
			$qData = array(
			'phoneNum' => '01037599714',
			'smsContent' => '테스트입니다'.PHP_EOL.'실 테스트 입니다.',
			'smsType' => 'S'
			);
			$this->common->smsSend($qData);
			*/
	
		$callback = $this->CI->config->item('sms_callback');
		$arr_cb = explode('-', $callback);
	
		/******************** 인증정보 ********************/
		$sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // 전송요청 URL
		// $sms_url = "https://sslsms.cafe24.com/sms_sender.php"; // HTTPS 전송요청 URL
		$sms['user_id'] = base64_encode($this->CI->config->item('sms_id')); //SMS 아이디.
		$sms['secure'] = base64_encode($this->CI->config->item('sms_key')) ;//인증키
		$sms['msg'] = base64_encode(stripslashes($qData['smsContent']));
		if($qData['smsType'] == "L"){
			$sms['subject'] =  base64_encode($qData['smsSubject']);
		}
		$sms['rphone'] = base64_encode($qData['phoneNum']); //발송대상 번호
		$sms['sphone1'] = base64_encode($arr_cb[0]);
		$sms['sphone2'] = base64_encode($arr_cb[1]);
		$sms['sphone3'] = base64_encode($arr_cb[2]);
		//$sms['rdate'] = base64_encode($_POST['rdate']);
		//$sms['rtime'] = base64_encode($_POST['rtime']);
		$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.
		$sms['returnurl'] = ''; //base64_encode($_POST['returnurl']);
		$sms['testflag'] = ''; //base64_encode($_POST['testflag']);
		//$sms['destination'] = urlencode(base64_encode($_POST['destination']));
		//$returnurl = $_POST['returnurl'];
		$sms['repeatFlag'] = base64_encode('N'); //Y이면 발송성공시에도 동일한 내용 계속 발송
		$sms['repeatNum'] = base64_encode(3);
		$sms['repeatTime'] = base64_encode(15);
		$sms['smsType'] = base64_encode($qData['smsType']); // LMS일경우 L , 단문 S
		$nointeractive = 1; //$_POST['nointeractive']; //사용할 경우 : 1, 성공시 대화상자(alert)를 생략
	
		$host_info = explode("/", $sms_url);
		$host = $host_info[2];
		$path = $host_info[3]; //."/".$host_info[4];
	
		srand((double)microtime()*1000000);
		$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);
		//print_r($sms);
	
		// 헤더 생성
		$header = "POST /".$path ." HTTP/1.0\r\n";
		$header .= "Host: ".$host."\r\n";
		$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";
	
		// 본문 생성
		$data = '';
		foreach($sms AS $index => $value){
			$data .="--$boundary\r\n";
			$data .= "Content-Disposition: form-data; name=\"".$index."\"\r\n";
			$data .= "\r\n".$value."\r\n";
			$data .="--$boundary\r\n";
		}
		$header .= "Content-length: " . strlen($data) . "\r\n\r\n";
	
		$fp = fsockopen($host, 80);
	
		if ($fp) {
			fputs($fp, $header.$data);
			$rsp = '';
			while(!feof($fp)) {
				$rsp .= fgets($fp,8192);
			}
			fclose($fp);
			$msg = explode("\r\n\r\n",trim($rsp));
			$rMsg = explode(",", $msg[1]);
			$Result= $rMsg[0]; //발송결과
			$Count= $rMsg[1]; //잔여건수
	
			//발송결과 알림
			if($Result=="success") {
				$alert = "성공";
				$alert .= " 잔여건수는 ".$Count."건 입니다.";
			}
			else if($Result=="reserved") {
				$alert = "성공적으로 예약되었습니다.";
				$alert .= " 잔여건수는 ".$Count."건 입니다.";
			}
			else if($Result=="3205") {
				$alert = "잘못된 번호형식입니다.";
			}
	
			else if($Result=="0044") {
				$alert = "스팸문자는발송되지 않습니다.";
			}
	
			else {
				$alert = "[Error]".$Result;
			}
		}
		else {
			$alert = "Connection Failed";
		}
	
		if($nointeractive=="1" && ($Result!="success" && $Result!="Test Success!" && $Result!="reserved") ) {
			//echo "<script>alert('".$alert ."')</script>";
			$this->message($alert, '-', '');
		}
		//else if($nointeractive!="1") {
		//	echo "<script>alert('".$alert ."')</script>";
		//}
		//echo "<script>location.href='".$returnurl."';</script>";
	}	
	
	/**
	 * @method name : listPagination 
	 * 공통 페이징 처리 (CodeIgniter pagination을 사용하는 경우)
	 * 
	 * @param array $arrDt
	 * @return string
	 */
	public function listPagination($arrDt)
	{
		$this->CI->load->library('pagination');
		
		$config['base_url'] = $arrDt['currentUrl'].'/page/';
		$config['total_rows'] = $arrDt['rsTotalCount'];
		$config['per_page'] = $arrDt['listCount']; 
		$config['num_links'] = 4;
		$config['uri_segment'] = 4;	//uri 상에 페이지번호 위치
		$config['use_page_numbers'] = TRUE;	//False인경우 페이지 클릭시 url이 $perPage글 곱한것 만큼 출력됨
		$config['cur_page'] = $arrDt['currentPage'];
		
		$config['full_tag_open'] = '<p> ';
		$config['full_tag_close'] = '</p>';
		
		$config['first_link'] = '맨처음';	//이미지 등 가능, FALSE로 설정하면 렌더링 되지않음
		$config['first_tag_open'] = '<span> ';
		$config['first_tag_close'] = '</span>';
		$config['last_link'] = '제일끝';	//이미지 등 가능, FALSE로 설정하면 렌더링 되지않음
		$config['last_tag_open'] = '<span> ';
		$config['last_tag_close'] = '</span>';		
		
		$config['next_link'] = '&gt;';	//처음링크, FALSE로 설정하면 렌더링 되지않음
		$config['next_tag_open'] = '<span> ';
		$config['next_tag_close'] = '</span>';
		$config['prev_link'] = '&lt;';	//다음링크, FALSE로 설정하면 렌더링 되지않음
		$config['prev_tag_open'] = '<span> ';
		$config['prev_tag_close'] = '</span>';
		
		$config['cur_tag_open'] = ' <b>';	//현재 페이지
		$config['cur_tag_close'] = '</b>';		
		
		$config['num_tag_open'] = '<span> ';	//링크숫자 링크의 여는태그
		$config['num_tag_close'] = '</span>';
		
		$config['display_pages'] = ($arrDt['rsTotalCount'] === 0) ? FALSE : TRUE;
		
		//페이지네이션 클래스를 통해 생성된 모든 링크에 클래스 속성을 추가하고자 한다면  원하는 클래스 이름으로 설정
		//$config['anchor_class'] = 'css_name';
		
		$this->CI->pagination->initialize($config);
		$pagingString = $this->CI->pagination->create_links();
		
		return $pagingString;
	}
	
	/**
	 * @method name : listPagingUri
	 * 공통 페이징 처리 (CodeIgniter pagination을 사용하지 않는 경우)
	 * 페이지 클릭시 uri로 이동
	 * 정상작동은 미확인
	 * 
	 * @param array $arrDt
	 * @return string
	 */
	public function listPagingUri($arrDt)
	{
		$arrDt['rsTotalCount'] = $this->nullCheck($arrDt['rsTotalCount'], 'int', 0);
		$navigation ="";
		
		if ($arrDt['rsTotalCount'] > 0)
		{
			$pageDispCount = 10;	//페이지 나열수 [1][2]3[4]....
			if(!$arrDt['currentPage']) $arrDt['currentPage'] = 1;
			
			if($arrDt['rsTotalCount']%$arrDt['listCount']==0)
			{
				$tot_page = intval($arrDt['rsTotalCount'] / $arrDt['listCount']);
			}
			else
			{
				$tot_page = intval($arrDt['rsTotalCount'] / $arrDt['listCount']) + 1;
			}
			
			if($arrDt['currentPage'] % $pageDispCount == 0)
			{
				@$start_page = ((($arrDt['currentPage'] / $pageDispCount) - 1) * $pageDispCount) + 1;
			} 
			else
			{
				$start_page = (intval($arrDt['currentPage'] / $pageDispCount) * $pageDispCount) + 1;
			}
			$end_page = $start_page + $pageDispCount - 1;
			if($end_page > $tot_page)
			{
				$end_page = $tot_page;
			}
			
			//이전 10페이지
			if($start_page > $pageDispCount)
			{
				$pre_pages = $start_page - $pageDispCount;
				$navigation .="<a href='".$arrDt['currentUrl']."/page/".$pre_pages."'><img src='/images/common/btn/btn_prev.gif' alt='이전10페이지' /></a>\n";
			}
			else
			{
				$navigation .="<img src='/images/common/btn/btn_prev.gif' />\n";
			}
			
			//페이지 나열
			$navigation .= "<span>\n";
			for ($i=$start_page; $i<=$end_page; $i++) 
			{
				if ($i == $arrDt['currentPage'])
				{
					$navigation .= "<a class='this'>$i</a>\n";
				}
				else
				{
					$navigation .= "<a href='".$arrDt['currentUrl']."/page/".$i."'>";
					$navigation .= "$i";
					$navigation .= "</a>\n";
				}
			}
			$navigation .= "</span>\n";
			
			//다음 10페이지
			if($tot_page > $end_page)
			{
				$next_pages = $start_page + $pageDispCount;
				$navigation .="<a href='".$arrDt['currentUrl']."/page/".$next_pages."'><img src='/images/common/btn/btn_next.gif' alt='다음10페이지' /></a>\n";
			} 
			else
			{
				$navigation .="<img src='/images/common/btn/btn_next.gif' />\n";
			}
			
			$start = (($arrDt['currentPage'] -1) * $arrDt['listCount']) + 1;
			if($arrDt['currentPage'] == 1) $start = 1;
			$end = $arrDt['currentPage']  * $arrDt['listCount'];
			
			//처음, 마지막 페이지
			$navigation = "<a href='".$arrDt['currentUrl']."/page/1'><img src='/images/common/btn/btn_home.gif' alt='첫페이지' /></a>\n".$navigation;
			$navigation .= "<a href='".$arrDt['currentUrl']."/page/".$tot_page."'><img src='/images/common/btn/btn_end.gif' alt='마지막페이지' /></a>\n";			
		}
				
		return $navigation;
	}
	
	/**
	 * @method name : listPagingUrl
	 * 공통 페이징 처리 (CodeIgniter pagination을 사용하지 않는 경우)
	 * 페이지 클릭시 javascript function 으로 이동
	 * @param array $arrDt
	 * @return string
	 */
	public function listPagingUrl($arrDt)
	{
		$arrDt['rsTotalCount'] = $this->nullCheck($arrDt['rsTotalCount'], 'int', 0);
		$navigation ="";
		
		if ($arrDt['rsTotalCount'] > $arrDt['listCount'])
		{
			$pageDispCount = 10;	//페이지 나열수 [1][2]3[4]....
			if(!$arrDt['currentPage']) $arrDt['currentPage'] = 1;

			if($arrDt['rsTotalCount'] % $arrDt['listCount'] == 0)
			{
				$tot_page = intval($arrDt['rsTotalCount'] / $arrDt['listCount']);
			}
			else
			{
				$tot_page = intval($arrDt['rsTotalCount'] / $arrDt['listCount']) + 1;
			}
			
			if($arrDt['currentPage'] % $pageDispCount == 0)
			{
				@$start_page = ((($arrDt['currentPage'] / $pageDispCount) - 1) * $pageDispCount) + 1;
			} 
			else
			{
				$start_page = (intval($arrDt['currentPage'] / $pageDispCount) * $pageDispCount) + 1;
			}
			$end_page = $start_page + $pageDispCount - 1;
			if($end_page > $tot_page)
			{
				$end_page = $tot_page;
			}
			
			//이전 10페이지
			if($start_page > $pageDispCount)
			{
				$pre_pages = $start_page - $pageDispCount;
				$navigation .="<a href=\"javascript:paging('".$arrDt['currentUrl']."', ".$pre_pages.");\"><img src='/images/common/btn/btn_prev.gif' alt='이전10페이지' /></a>\n";
			}
			else
			{
				$navigation .="<img src='/images/common/btn/btn_prev.gif' />\n";
			}
			
			//페이지 나열
			$navigation .= "<span>\n";
			for ($i=$start_page; $i<=$end_page; $i++) 
			{
				if ($i == $arrDt['currentPage'])
				{
					$navigation .= "<a class='this'>$i</a>\n";
				}
				else
				{
					$navigation .= "<a href=\"javascript:paging('".$arrDt['currentUrl']."', ".$i.");\">";
					$navigation .= "$i";
					$navigation .= "</a>\n";
				}
			}
			$navigation .= "</span>\n";
			
			//다음 10페이지
			if($tot_page > $end_page)
			{
				$next_pages = $start_page + $pageDispCount;
				$navigation .="<a href=\"javascript:paging('".$arrDt['currentUrl']."', ".$next_pages.");\"><img src='/images/common/btn/btn_next.gif' alt='다음10페이지' /></a>\n";
			} 
			else
			{
				$navigation .="<img src='/images/common/btn/btn_next.gif' />\n";
			}
			
			$start = (($arrDt['currentPage'] -1) * $arrDt['listCount']) + 1;
			if($arrDt['currentPage'] == 1) $start = 1;
			$end = $arrDt['currentPage']  * $arrDt['listCount'];
			
			//처음, 마지막 페이지
			$navigation = "<a href=\"javascript:paging('".$arrDt['currentUrl']."', 1);\"><img src='/images/common/btn/btn_home.gif' alt='첫페이지' /></a>\n".$navigation;
			$navigation .= "<a href=\"javascript:paging('".$arrDt['currentUrl']."', ".$tot_page.");\"><img src='/images/common/btn/btn_end.gif' alt='마지막페이지' /></a>\n";
		}
	
		return $navigation;
	}	

	public function listAdminPagingUrl($arrDt)
	{
		$arrDt['rsTotalCount'] = $this->nullCheck($arrDt['rsTotalCount'], 'int', 0);
		$navigation ="";
	
		if ($arrDt['rsTotalCount'] > $arrDt['listCount'])
		{
			$pageDispCount = 10;	//페이지 나열수 [1][2]3[4]....
			if(!$arrDt['currentPage']) $arrDt['currentPage'] = 1;
	
			if($arrDt['rsTotalCount'] % $arrDt['listCount'] == 0)
			{
				$tot_page = intval($arrDt['rsTotalCount'] / $arrDt['listCount']);
			}
			else
			{
				$tot_page = intval($arrDt['rsTotalCount'] / $arrDt['listCount']) + 1;
			}
				
			if($arrDt['currentPage'] % $pageDispCount == 0)
			{
				@$start_page = ((($arrDt['currentPage'] / $pageDispCount) - 1) * $pageDispCount) + 1;
			}
			else
			{
				$start_page = (intval($arrDt['currentPage'] / $pageDispCount) * $pageDispCount) + 1;
			}
			$end_page = $start_page + $pageDispCount - 1;
			if($end_page > $tot_page)
			{
				$end_page = $tot_page;
			}
				
			//이전 10페이지
			if($start_page > $pageDispCount)
			{
				$pre_pages = $start_page - $pageDispCount;
				$navigation .="<a class=\"prev\" href=\"".$arrDt['currentUrl']."/page/".$pre_pages.$arrDt['currentParam']."\"><img src='/images/adm/btn_paging_prev.gif' alt='이전10페이지' /></a>\n";
			}
			else
			{
				$navigation .="<img src='/images/adm/btn_paging_prev.gif' />\n";
			}
				
			//페이지 나열
			//$navigation .= "<span>\n";
			for ($i=$start_page; $i<=$end_page; $i++)
			{
				if ($i == $arrDt['currentPage'])
				{
					$navigation .= "<a href=\"#\">";
					$navigation .= "<span class='on'>$i</span>";
					$navigation .= "</a>\n";
				}
				else
				{
					$navigation .= "<a href=\"".$arrDt['currentUrl']."/page/".$i.$arrDt['currentParam']."\">";
					$navigation .= "<span>".$i."</span>";
					$navigation .= "</a>\n";
				}
			}
			//$navigation .= "</span>\n";
				
			//다음 10페이지
			if($tot_page > $end_page)
			{
				$next_pages = $start_page + $pageDispCount;
				$navigation .="<a class=\"next\" href=\"".$arrDt['currentUrl']."/page/".$next_pages.$arrDt['currentParam']."\"><img src='/images/adm/btn_paging_next.gif' alt='다음10페이지' /></a>\n";
			}
			else
			{
				$navigation .="<img src='/images/adm/btn_paging_next.gif' />\n";
			}
				
			$start = (($arrDt['currentPage'] -1) * $arrDt['listCount']) + 1;
			if($arrDt['currentPage'] == 1) $start = 1;
			$end = $arrDt['currentPage']  * $arrDt['listCount'];
				
			//처음, 마지막 페이지
			//$navigation = "<a href=\"".$arrDt['currentUrl']."/page/1".$param."\"><img src='/images/common/btn/btn_home.gif' alt='첫페이지' /></a>\n".$navigation;
			//$navigation .= "<a href=\"".$arrDt['currentUrl']."/page/".$tot_page.$param."\"><img src='/images/common/btn/btn_end.gif' alt='마지막페이지' /></a>\n";
		}
	
		return $navigation;
	}
	
	/**
	 * @method name : fileUpload 
	 * File Upload 공통
	 * GD2 Library 필수 설치
	 * 
	 * @param array $config
	 * @param bool $isOrder 파일순서 모두 유지
	 * 			TRUE :중간에 첨부하지 않은 빈내용이 있어도 순번부여
	 *			FALSE :중간에 첨부하지 않은 빈내용이 있으면 순번제외 
	 * @return array -> fileInfo return
	 */
	public function fileUpload($config, $isOrder = FALSE)
	{
		$createdThumbYn = 'N'; //업로드 과정중 썸네일 생성여부 초기값
		$fileInsDt = array();
		$i = 0;
		foreach ($_FILES as $key => $value)
		{
			if ($value['name'] !== '')
			{
				$this->CI->load->library('upload');
				$this->CI->upload->initialize($config);
				
				if(!strtolower(is_dir($config['upload_path'])))
				{
					//없는 경우 폴더 생성
					@mkdir(strtolower($config['upload_path']), 0777, TRUE);
				}				
				
				if (!$this->CI->upload->do_upload($key))
				{
					$upResult = $this->CI->upload->data();
					//MIME타입 확인할 경우
					//$mimetype= $upResult['file_type'];
					//var_dump('Mime: ' . $mimetype);
					//var_dump($_FILES);
					//exit();
					$fileInsDt = array('error' => $this->CI->upload->display_errors());
					break;
				}
				
				$upResult = $this->CI->upload->data();
				
				/* 썸네일 생성 */
				if ($config['create_thumbnail'] && strpos($upResult['file_type'], 'image') !== FALSE)
				{
					$thumbConfig = array(
						'image_library' => 'gd2',
						'source_image' => strtolower($config['upload_path']).'/'.$upResult['raw_name'].$upResult['file_ext'],
						'new_image' => strtolower($config['upload_path']),
						'maintain_ratio' => TRUE,
						'create_thumb' => TRUE,
						'thumb_marker' => '_s',
						'width' => 250,
						'height' => 250
					);

					$this->CI->load->library('image_lib');
					$this->CI->image_lib->clear(); // clear
					$this->CI->image_lib->initialize($thumbConfig);
					
					$createdThumbYn = 'Y';
					if (!$this->CI->image_lib->resize()) 
					{
						$createdThumbYn = 'N';						
						$fileInsDt = array('error' => $this->CI->image_lib->display_errors());
						break;							
					}
				}
				
				//반환될 파일정보
				$tmpUpResult = array(
					'FILE_NAME' => $upResult['orig_name'],
					'FILE_TEMPNAME' => $upResult['raw_name'].$upResult['file_ext'],
					'FILE_TYPE' => $upResult['file_type'],
					'FILE_SIZE' => $upResult['file_size'],
					'FILE_PATH' => str_replace('./', '/', strtolower($config['upload_path'])),	//$upResult['file_path'], <- 절대경로가 들어감 (C:\upload...)							
					'IMAGE_YN' => ($upResult['is_image'] ? 'Y' : 'N'),							
					'THUMB_YN' => $createdThumbYn,							
					'FILE_ORDER' => $i,
					'IMAGE_WIDTH' => (empty($upResult['image_width'])) ? 0 : $upResult['image_width'],
					'IMAGE_HEIGHT' => (empty($upResult['image_height'])) ? 0 : $upResult['image_height']
				);
				
				$tmpUpResult = $tmpUpResult + $this->fileUploadAddinfo($config, $i);
				$fileInsDt[$i] = $tmpUpResult;

				if (!$isOrder) $i++;
			}
			else 
			{
				//파일순서 유지를 해야 하는 경우 빈데이터 유지하여 반환
				if ($isOrder)
				{
					//임시데이터 생성하여 빈데이터를 넘겨준다
					$tmpUpResult = array(
						'FILE_NAME' => '',
						'FILE_TEMPNAME' => '',
						'FILE_TYPE' => '',
						'FILE_SIZE' => 0,
						'FILE_PATH' => str_replace('./', '/', strtolower($config['upload_path'])),
						'IMAGE_YN' => 'N',
						'THUMB_YN' => 'N',
						'FILE_ORDER' => $i,
						'IMAGE_WIDTH' => 0,
						'IMAGE_HEIGHT' => 0
					);
					
					$tmpUpResult = $tmpUpResult + $this->fileUploadAddinfo($config, $i);
					$fileInsDt[$i] = $tmpUpResult;
				}
			}
			
			//파일순서 모두 유지를 해야 하는 경우
			if ($isOrder) $i++;
		}		
		
		return $fileInsDt;
	}
	
	/* @todo
	 *  - Should have rollback technique so it can undo the copy when it wasn't successful
     *  - Auto destination technique should be possible to turn off
     *  - Supporting callback function
     *  - May prevent some issues on shared enviroments : http://us3.php.net/umask
     * @param $source //file or folder
     * @param $dest ///file or folder
     * @param $options //folderPermission,filePermission
     * @return boolean
     */
    public function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755))
    {
        $result=false;
	
        if (is_file($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if (!file_exists($dest)) {
                    cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
                }
                $__dest=$dest."/".basename($source);
            } else {
                $__dest=$dest;
            }
            $result=copy($source, $__dest);
            chmod($__dest,$options['filePermission']);
	
        } elseif(is_dir($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if ($source[strlen($source)-1]=='/') {
                    //Copy only contents
                } else {
                    //Change parent itself and its contents
                    $dest=$dest.basename($source);
                    @mkdir($dest);
                    chmod($dest,$options['filePermission']);
                }
            } else {
                if ($source[strlen($source)-1]=='/') {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                }
            }
	
            $dirHandle=opendir($source);
            while($file=readdir($dirHandle))
            {
                if($file!="." && $file!="..")
                {
                     if(!is_dir($source."/".$file)) {
                        $__dest=$dest."/".$file;
                    } else {
                        $__dest=$dest."/".$file;
                    }
                    //echo "$source/$file ||| $__dest<br />";
                    $result=$this->smartCopy($source."/".$file, $__dest, $options);
                }
            }
            closedir($dirHandle);
	
        } else {
            $result=false;
        }
        return $result;
    }
		
	/**
	 * @method name : fileUploadAddinfo
	 * 공통파일내용 구성 외에 추가정보가 필요한 경우 
	 * 
	 * @param array $config
	 * @param array $fileIndex
	 * @return string,[]
	 */
	private function fileUploadAddinfo($config, $fileIndex)
	{
		$tmpResult = array();
		if (isset($config['TBLCODE_NUM']))
		{
			//공통 파일 테이블 insert 내용 추가
			$tmpResult = array(
				'TBLCODE_NUM' => $this->nullCheck($config['TBLCODE_NUM'], 'int', 0),
				'TBL_NUM' => $this->nullCheck($config['TBL_NUM'], 'int', 0)
			);
		}
		else if (isset($config['SHOPITEM_NUM']))
		{
			//SHOPITEM 파일 insert 내용 추가
			$tmpResult = array(
				'SHOPITEM_NUM' => $this->nullCheck($config['SHOPITEM_NUM'], 'int', 0)
			);
		}
		
		if (isset($config['IS_FILEUSE']))
		{
			if ($config['IS_FILEUSE']) //TRUE
			{
				$fileUse = (($fileIndex % 2) == 0) ? 'W' : 'M'; //파일사용 용도 (W:웹, M:모바일)				
				$tmpResult['FILE_USE'] = $fileUse;
			}
			unset($config['IS_FILEUSE']); //실제 사용되는 내용이 아니므로 삭제
		}
		
		return $tmpResult;
	}
	
	 /**
	  * @method name : getFileName 
	  * 디렉토리내 파일명 추출
	  * 
	  * @param string $fullFileName
	  * @return string
	  */
	 public function getFileName($fullFileName)
	 {
		 $array = explode('/', $fullFileName);
		 if (sizeof($array) <= 1)  return $fullFileName;
		 
		 return $array[sizeof($array) -1];
	 } 
	
	/**
	 * @method name : arrayToJson
	 * Array To Json string convert
	 * 
	 * @param array $arrdata json으로 변환될 레이어
	 * @param string $type 예외사항 파악을 위한 변수 (필요한 경우 사용)
	 */
	public function arrayToJson($arrdata, $type = '')
	{
		$resultJson = json_encode($arrdata, JSON_UNESCAPED_UNICODE);
		return $resultJson;
	}
	
	/**
	 * @method name : nullCheck
	 * null or whitespace check
	 * 
	 * @param string, numeric $val
	 * @param string $dataType
	 * @param string, numeric $defaultValue  
	 * @return string, numeric
	 */
	public function nullCheck($val, $dataType, $defaultVal)
	{
		$returnVal = $val;
		if (!is_array($val))
		{
			if (!isset($val) || trim($val)==='')
			{
				if ($dataType == 'int')
				{
					$returnVal = (empty($defaultVal)) ? 0 : $defaultVal;
				}
				else
				{
					$returnVal = (empty($defaultVal)) ? '' : $defaultVal;
				}
			}
		}
		
	    return $returnVal;
	}
	
	/**
	 * @method name : getDomain
	 * 프로토콜을 포함한 도메인 정보 (http, https)
	 * 기본 port가 아닐 경우 port붙음
	 * @return string
	 */
	public function getDomain()
	{
		return 'http://'.$_SERVER['HTTP_HOST'];
	}
	
	/**
	 * @method name : objectToArray
	 * stdClass -> Array 로 변경
	 * 
	 * @param object $d
	 * @return array
	 */
	public function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
	
		if (is_array($d)) {
			/*
			 * Return array converted to object
			 * Using __FUNCTION__ (Magic constant)
			 * for recursive call
			 */
			return array_map(__FUNCTION__, $d);
		} else {
			// Return array
			return $d;
		}
	}
	
	/**
	 * @method name : arrayToObject
	 * Array -> stdClass 로 변경 
	 * 
	 * @param array $d
	 * @return object Ambiguous
	 */
	public function arrayToObject($d) {
		if (is_array($d)) {
			/*
			 * Return array converted to object
			 * Using __FUNCTION__ (Magic constant)
			 * for recursive call
			 */
			return (object) array_map(__FUNCTION__, $d);
		} else {
			// Return object
			return $d;
		}
	}
	
	/**
	 * @method name : array_equal
	 * 배열 내용 비교 
	 * 
	 * @param unknown $a
	 * @param unknown $b
	 * @return boolean
	 */
	public function array_equal($a, $b) {
		return (is_array($a) && is_array($b) && array_diff($a, $b) === array_diff($b, $a));
	}
	
	/**
	 * @method name : getMobileCheck
	 * 모바일, PC웹페이지 구분
	 * 
	 * @return boolean
	 */
	public function getMobileCheck() {
		$HTTP_USER_AGENT =$_SERVER["HTTP_USER_AGENT"];
		$isMobile = FALSE;
		$MobileArray  = array(
			"iphone",
			"lgtelecom",
			"skt",
			"mobile",
			"samsung",
			"nokia",
			"blackberry",
			"android",
    		"sony",
			"phone"
		);
	
		$checkCount = 0;
		for($i=0; $i<sizeof($MobileArray); $i++)
		{
			if(preg_match("/$MobileArray[$i]/", strtolower($HTTP_USER_AGENT))){ $checkCount++; break; }
		}
	
		if($checkCount >= 1){
			//$checkCount1 = 0;
			//$MobileTab = array("ipad");
			// for($i=0; $i<sizeof($MobileTab); $i++){
			//   if(preg_match("/$MobileTab[$i]/", strtolower($HTTP_USER_AGENT))){ $checkCount1++; break; }
			//  }
			// if($checkCount1 >= 1) {
			//	 $str ="Tab" ;
			//	 if(preg_match("/FBAN/", $HTTP_USER_AGENT)){
			$str =TRUE;
			//	 }
			// }else{
			//	$str ="Mobile";
			//}
		}
	
		return $isMobile ;
	}	
	
	/**
	 * @method name : array_group_by
	 * 
	 * 예시:
	 * $rows = array(
	 * 	array('id'=>1, 'name'=>'한놈', 'department_id'=>1, 'birth_date'=>'1999-01-01'),
	 * 	array('id'=>2, 'name'=>'두시기', 'department_id'=>2, 'birth_date'=>'2000-01-01'),
	 * 	array('id'=>3, 'name'=>'석삼', 'department_id'=>2, 'birth_date'=>'1999-01-01'),
	 * 	array('id'=>4, 'name'=>'너구리', 'department_id'=>3, 'birth_date'=>'2000-01-01'),
	 * );
 	 * print_r( group_by('birth_date', $rows) );
	 * # Array
	 * # (
	 * #     [1999-01-01] => Array
	 * #         (
	 * #             [0] => Array
	 * #                 (
	 * #                     [id] => 1
	 * #                     [name] => 한놈
	 * #                     [department_id] => 1
	 * #                     [birth_date] => 1999-01-01
	 * #                 )
	 * # 
	 * #             [1] => Array
	 * #                 (
	 * #                     [id] => 3
	 * #                     [name] => 석삼
	 * #                     [department_id] => 2
	 * #                     [birth_date] => 1999-01-01
	 * #                 )
	 * #         )
	 * # 
	 * #     [2000-01-01] => Array
	 * #         (
	 * #             [0] => Array
	 * #                 (
	 * #                     [id] => 2
	 * #                     [name] => 두시기
	 * #                     [department_id] => 2
	 * #                     [birth_date] => 2000-01-01
	 * #                 )
	 * # 
	 * #             [1] => Array
	 * #                 (
	 * #                     [id] => 4
	 * #                     [name] => 너구리
	 * #                     [department_id] => 3
	 * #                     [birth_date] => 2000-01-01
	 * #                 )
	 * #         )
	 * # )
	 * @param unknown $column_name
	 * @param unknown $rows
	 * @return NULL[]
	 */
	public function array_group_by($column_name, $rows) 
	{
		$result = array();
		$groups = $this->array_distinct($rows, $column_name);
		foreach($groups as $group) 
		{
			$result[$group] = $this->array_where($rows, array($column_name=>$group));
		}
		
		return $result;
	}
	
	/**
	 * @method name : array_distinct
	 * 
	 * 예시:
	 * print_r( distinct($rows, 'department_id') );
	 * # Array
	 * # (
	 * #     [0] => 1
	 * #     [1] => 2
	 * #     [2] => 3
	 * # )
	 * print_r( distinct($rows, 'birth_date') );
	 * # Array
	 * # (
	 * #     [0] => 1999-01-01
	 * #     [1] => 2000-01-01
	 * # )
	 *  
	 * @param unknown $rows
	 * @param unknown $column_name
	 */
	public function array_distinct($rows, $column_name) 
	{
		$column_values = array();
		foreach($rows as $row) 
		{
			$column_values[$row[$column_name]] = 1;
		}
		
		return array_keys($column_values);
	}
	
	/**
	 * @method name : array_where
	 * 
	 * 예시:
 	 * $selected = where($rows, array('birth_date' => '1999-01-01'));
	 * print_r($selected);
	 * # Array
	 * # (
	 * #     [0] => Array
	 * #         (
	 * #             [id] => 1
	 * #             [name] => 한놈
	 * #             [depratment_id] => 1
	 * #             [birth_date] => 1999-01-01
	 * #         )
	 * # 
	 * #     [1] => Array
	 * #         (
	 * #             [id] => 3
	 * #             [name] => 석삼
	 * #             [depratment_id] => 2
	 * #             [birth_date] => 1999-01-01
	 * #         )
	 * # )
	 * $selected = where($rows, array('birth_date'=>'2000-01-01', 'department_id'=>2));
	 * print_r($selected);
	 * # Array
	 * # (
	 * #     [0] => Array
	 * #         (
	 * #             [id] => 2
	 * #             [name] => 두시기
	 * #             [department_id] => 2
	 * #             [birth_date] => 2000-01-01
	 * #         )
	 * # ) 
	 * @param unknown $rows
	 * @param unknown $params
	 * @return unknown[]
	 */
	public function array_where($rows, $params) 
	{
		$result = array();
		foreach($rows as $row) 
		{
			$row_matched = true;
			foreach($params as $column_name => $column_value) 
			{
				if(!array_key_exists($column_name, $row) 
						|| $row[$column_name] != $column_value) 
				{
					$row_matched = false;
					break;
				}
			}
			if($row_matched) $result[] = $row;
		}
		return $result;
	}
	
	/**
	 * @method name : msort - 배열정렬
	 * 1.It's reusable: you specify the sort column as a variable instead of hardcoding it.
	 * 2.It's flexible: you can specify multiple sort columns (as many as you want) -- additional columns are used as tiebreakers between items that initially compare equal.
	 * 3.It's reversible: you can specify that the sort should be reversed -- individually for each column.
	 * 4.It's extensible: if the data set contains columns that cannot be compared in a "dumb" manner (e.g. date strings) you can also specify how to convert these items to a value that can be directly compared (e.g. a DateTime instance).
	 * 5.It's associative if you want: this code takes care of sorting items, but you select the actual sort function (usort or uasort).
	 * 6.Finally, it does not use array_multisort: while array_multisort is convenient, it depends on creating a projection of all your input data before sorting. This consumes time and memory and may be simply prohibitive if your data set is large.
	 * 
	 * usort($data, msort(['number', SORT_DESC], ['name', SORT_DESC]));
	 * usort($data, msort(0)); // 0 = first numerically indexed column
	 */
	public function msort() {
	    // Normalize criteria up front so that the comparer finds everything tidy
	    $criteria = func_get_args();
	    foreach ($criteria as $index => $criterion) 
	    {
	        $criteria[$index] = is_array($criterion)
	            ? array_pad($criterion, 3, null)
	            : array($criterion, SORT_ASC, null);
	    }
	
	    return function($first, $second) use (&$criteria) 
	    {
	        foreach ($criteria as $criterion) 
	        {
	            // How will we compare this round?
	            list($column, $sortOrder, $projection) = $criterion;
	            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;
	
	            // If a projection was defined project the values now
	            if ($projection) 
	            {
	                $lhs = call_user_func($projection, $first[$column]);
	                $rhs = call_user_func($projection, $second[$column]);
	            }
	            else 
	            {
	                $lhs = $first[$column];
	                $rhs = $second[$column];
	            }
	
	            // Do the actual comparison; do not return if equal
	            if ($lhs < $rhs) 
	            {
	                return -1 * $sortOrder;
	            }
	            else if ($lhs > $rhs) 
	            {
	                return 1 * $sortOrder;
	            }
	        }
	
	        return 0; // tiebreakers exhausted, so $first == $second
	    };
	}	
	
	/**
	 * @method name : getShortURL
	 * short url api 
	 * 
	 * @param unknown $longURL
	 * @param string $shortURL_domain
	 * @return Ambiguous|boolean
	 */
	public function getShortURL($longURL, $shortURL_domain="durl.me") 
	{
		switch($shortURL_domain) 
		{
			
			case "to.ly" :
				$curlopt_url = "http://to.ly/api.php?longurl=".$longURL;
				break;
			case "durl.me" :
				$curlopt_url = "http://durl.me/api/Create.do?type=json&longurl=".$longURL;
				break;
			case "tinyurl" :
				$curlopt_url = "http://tinyurl.com/api-create.php?url=".$longURL;
				break;
		}
	
		$ch = curl_init();
		//$timeout = 10;
		curl_setopt($ch, CURLOPT_URL, $curlopt_url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		if($shortURL_domain == "goo.gl" || $shortURL_domain == "durl.me") 
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$jsonArray = array('longUrl' => $longURL);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
			$shortURL = curl_exec($ch);
			curl_close($ch);
			$result_array = json_decode($shortURL, true);
			if($result_array['shortUrl']) return $result_array['shortUrl'];
			// durl.me
			else if($result_array['id']) return $result_array['id'];
			// goo.gl
		}
		else
		{
			return false;
		}
		$shortURL = curl_exec($ch);
		curl_close($ch);
		// bit.ly(j.mp) 주소 끝에 붙은 줄바꿈 문자를 없앰
		if( ($shortURL_domain == "j.mp" || $shortURL_domain  == "bit.ly") && bin2hex(substr($shortURL, -1, 1)) == "0a") $shortURL = substr($shortURL, 0, strlen($shortURL)-1);
		return $shortURL;
	}
	
	/**
	 * @method name : cutStr
	 * 문자열 줄임 
	 * 
	 * @param unknown $msg
	 * @param unknown $cut_size
	 * @param string $tail
	 * @return Ambiguous|string
	 */
	public function cutStr($msg, $cut_size, $tail="...") {
		$han = $eng = '';
		if($cut_size <= 0) return $msg;
		$msg = strip_tags($msg);
		$msg = str_replace("&mp;quot;","\"",$msg);
		if(mb_strlen($msg, "utf-8") <= $cut_size) return $msg;
	
		for($i=0;$i<$cut_size;$i++) if(ord($msg[$i])>127) $han++; else $eng++;
		if($han%2) $han--;
	
		$cut_size = $han + $eng;
	
		$tmp = mb_substr($msg,0,$cut_size, "utf-8");
		$tmp .= $tail;
		
		return $tmp;
	}
	
	/**
	 * @method name : stripHtmlTags
	 * 모든 태그 삭제 후 한줄의 문자열로 반환
	 * 
	 * @param unknown $str
	 * @param string $allowStr 허용되는 태그
	 * @return Ambiguous
	 */
	public function stripHtmlTags($str, $allowStr ='')
	{
		$str = str_replace('&quot;', '"', $str);
		$str = str_replace('&#39;', "'", $str);
		$str = str_replace('&nbsp;', '', $str);
		$str = str_replace('&lt;', '<', $str);
		$str = str_replace('&gt;', '>', $str);
		$str = str_replace(chr(10), '', $str);
		$str = str_replace(chr(13), '', $str);
		$str = strip_tags($str, $allowStr);
		
		return $str;
	}
	
	/**
	 * @method name : ucn (userInfoHiddenConverting)
	 * 유저정보 가리기(이메일등) 
	 * 
	 * @param unknown $str
	 * @param number $padding
	 * @return string
	 */
	public function ucn($str, $padding = 0)
	{
		return substr($str, 0, 3).'****';
	}
	
	/**
	 * @method name : sqlEncrypt
	 * mysql select를 이용하여 암호화된 문자열을 반환
	 *
	 * @param string $val
	 * @param string $key
	 * @return string
	 */
	public function sqlEncrypt($val, $key)
	{
		$this->CI->db->select("HEX(AES_ENCRYPT('".$val."', '".$key."')) AS ENC");
		$result = $this->CI->db->get()->row()->ENC;
	
		return $result;
	}
	
	/**
	 * @method name : sqlDecrypt
	 * mysql select를 이용하여 복호화된 문자열을 반환
	 *
	 * @param string $val
	 * @param string $key
	 * @return string
	 */
	public function sqlDecrypt($val, $key)
	{
		$this->CI->db->select("AES_DECRYPT(UNHEX('".$val."'), '".$key."') AS ENC2DEC");
		$result = $this->CI->db->get()->row()->ENC2DEC;
	
		return $result;
	}
	
	/**
	 * @method name : getIsLogin
	 * 로그인 여부만 확인
	 *
	 * @param $isApp TRUE 앱로그인 체크여부 (쿠키값도 확인)
	 * @return bool
	 */
	public function getIsLogin($isApp = FALSE)
	{
		$session = $this->nullCheck($this->getSession('user_num'), 'int', 0);
		if ($isApp)
		{
			$session = ($session == 0) ? $this->nullCheck(get_cookie('usernum'), 'int', 0) : $session;			
		}
		return ($session > 0) ? TRUE : FALSE;
	}
	
	public function getSessionUserLevelCodeId()
	{
		$session = $this->nullCheck($this->getSession('user_level'), 'int', 0);
		$result = ($session > 0) ? $this->getCodeIdByCodeNum($session) : 0;
		return $result;
	}
	
	/**
	 * @method name : getSessionAll
	 * 모든 session정보
	 *
	 * @return array
	 */
	public function getSessionAll()
	{
		$result = $this->CI->session->all_userdata();
		return $result;
	}
	
	/**
	 * @method name : getSession
	 * session Key 내용 반환
	 * 로그인시 이메일은 암호화되어 세팅되어 있음
	 * 
	 * @param string $key
	 * @return var
	 */
	public function getSession($key)
	{
		return $this->CI->session->userdata($key);
	}
	
	/**
	 * @method name : setSession
	 * 세션값 세팅
	 * 로그인시 이메일은 암호화되어 세팅
	 * 
	 * @param unknown $key
	 * @param unknown $val
	 */
	public function setSession($key, $val)
	{
		$this->CI->session->set_userdata($key, $val);
	}
	
	/**
	 * @method name : getUserInfo
	 * 각종 검색조건에 속하는 USER 정보 조회(1건만 조회)
	 * 
	 * @param string $sItem
	 * @param string $val
	 */
	public function getUserInfo($type, $val = '')
	{
		$result = array();
		$resultUNum = 0;
		$userTblCode = $this->getCodeNumByCodeGrpNCodeId('TABLE', $this->_userTbl);
		if ($type == 'num')
		{
			$this->CI->db->select("
				*,
				AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$userTblCode."
					AND TBL_NUM = ".$this->_userTbl.".NUM 
					AND DEL_YN = 'N'
					ORDER BY NUM LIMIT 1
				) AS PROFILE_FILE_INFO							
			");
			$this->CI->db->limit(1);
			$this->CI->db->from($this->_userTbl);
			$this->CI->db->where("(NUM = ".$val.")");
			$result = $this->CI->db->get()->row_array();
		}
		else if ($type == 'email')
		{
			$userEmailEnc = $this->sqlEncrypt($val, $this->_encKey);
			$this->CI->db->select("
				*,
				AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$userTblCode."
					AND TBL_NUM = ".$this->_userTbl.".NUM 
					AND DEL_YN = 'N'
					ORDER BY NUM LIMIT 1
				) AS PROFILE_FILE_INFO						
			");
			$this->CI->db->limit(1);
			$this->CI->db->from($this->_userTbl);
			$this->CI->db->where("(USER_EMAIL = '".$userEmailEnc."')");
			$result = $this->CI->db->get()->row_array();
		}
		else if ($type == 'dummy')
		{
			$dummyCodeNum = $this->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'DUMMYUSER');
			$this->CI->db->select("
				*,
				AES_DECRYPT(UNHEX(USER_EMAIL), '".$this->_encKey."') AS USER_EMAIL_DEC,
				(
					SELECT CONCAT(NUM, '|', FILE_NAME, '|', FILE_PATH, '|', FILE_TEMPNAME, '|', THUMB_YN) 
					FROM PROFILE_FILE
					WHERE TBLCODE_NUM = ".$userTblCode."
					AND TBL_NUM = ".$this->_userTbl.".NUM 
					AND DEL_YN = 'N'
					ORDER BY NUM LIMIT 1
				) AS PROFILE_FILE_INFO						
			");
			$this->CI->db->limit(1);
			$this->CI->db->from($this->_userTbl);
			$this->CI->db->where("ULEVELCODE_NUM = '".$dummyCodeNum."'");
			$result = $this->CI->db->get()->row_array();
		}	
		
		$resultUNum = $result['NUM'];

		if ($resultUNum)
		{
			//플래그후 삭제등 유효하지 않게된 데이터(아이템,샵,스토리)가
			//있는경우 카운트를 맞추기위해 호출
			//부하적인 측면이 다소 우려됨 대안을 찾거나 별무리가 없다면 유지
			$this->setFlagCountReUpdate('item', $resultUNum);
			$this->setFlagCountReUpdate('shop', $resultUNum);
			$this->setFlagCountReUpdate('story', $resultUNum);
		}
		
		return $result;
	}
	
	/**
	 * @method name : getIsFollowUser
	 * 특정 다른회원을 내가 follow했는지 여부  
	 * 
	 * @param unknown $targetUserNum
	 * @param unknown $userNum
	 * @return Ambiguous
	 */
	public function getIsFollowUser($targetUserNum, $userNum)
	{
		$this->CI->db->select("
			EXISTS (
				SELECT 1 FROM FOLLOW
				WHERE USER_NUM = ".$userNum."
				AND TO_USER_NUM = ".$targetUserNum."
				AND DEL_YN = 'N'
			) AS RESULT
		");
		$result = $this->CI->db->get()->row()->RESULT;
		
		return $result;
	}
	
	/**
	 * @method name : getStandardShopInfo
	 * 기준샵 기본정보 조회
	 * USERLEVEL - STDSHOP 인 회원이 생성한 SHOP
	 * STDSHOP 권한을 가지고 생성한 SHOP은 꼭 한개여야만 함 
	 * 
	 */
	public function getStandardShopInfo()
	{
		return $this->CI->db->query("
			SELECT *
			FROM SHOP
			WHERE USER_NUM IN
					(
						SELECT NUM FROM USER
						WHERE ULEVELCODE_NUM = ".$this->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'STDSHOP')."
		) LIMIT 1")->row_array();		
	}
	
	/**
	 * @method name : getUserNumByShopNum
	 * 샵고유번호로 작가의 회원고유번호 조회
	 * 
	 * @param unknown $sNum
	 */
	public function getUserNumByShopNum($sNum)
	{
		return $this->CI->db->query("SELECT USER_NUM FROM SHOP WHERE NUM = ".$sNum."")->row()->USER_NUM;		
	}
	
	/**
	 * @method name : getSuperAdminUserNum
	 * 슈퍼관리자 회원고유번호 조회 
	 * 
	 */
	public function getSuperAdminUserNum()
	{
		return $this->CI->db->query("
			SELECT NUM FROM USER WHERE ULEVELCODE_NUM = 610 AND DEL_YN = 'N'
		")->row()->NUM;
	}	
	
	/**
	 * @method name : getCodeNumByCodeId
	 * CODE_ID 로 CODE.NUM 조회
	 *
	 * @param string $codeId
	 */
	public function getCodeNumByCodeId($codeId)
	{
		$this->CI->db->select('NUM');
		$this->CI->db->from('CODE');
		$this->CI->db->where("(UPPER(CODE_ID) = '".strtoupper($codeId)."')");
	
		return $this->CI->db->get()->row()->NUM;
	}
	
	/**
	 * @method name : getCodeNumByCodeGrpNCodeId
	 * CODE_GROUP과 CODE_ID 로 CODE.NUM 조회
	 * 
	 * @param string $codeGrp
	 * @param string $codeId
	 */
	public function getCodeNumByCodeGrpNCodeId($codeGrp, $codeId)
	{
		$this->CI->db->select('NUM');
		$this->CI->db->from('CODE');
		$this->CI->db->where("(UPPER(CODE_GROUP) = '".strtoupper($codeGrp)."')");
		$this->CI->db->where("(UPPER(CODE_ID) = '".strtoupper($codeId)."')");
	
		return $this->CI->db->get()->row()->NUM;
	}
	
	/**
	 * @method name : getCodeNumByCodeGrpNTitle
	 * CODE_GROUP과 TITLE 로 CODE.NUM 조회
	 * 
	 * @param unknown $codeGrp
	 * @param unknown $title
	 */
	public function getCodeNumByCodeGrpNTitle($codeGrp, $title)
	{
		$this->CI->db->select('NUM');
		$this->CI->db->from('CODE');
		$this->CI->db->where("(UPPER(CODE_GROUP) = '".strtoupper($codeGrp)."')");
		$this->CI->db->where("(UPPER(TITLE) = '".strtoupper($title)."')");

		return $this->CI->db->get()->row()->NUM;
	}	
	
	/**
	 * @method name : getCodeNumByCodeGrpNEtcText
	 * CODE_GROUP과 ETC_TEXT 로 CODE.NUM 조회
	 * 
	 * @param unknown $codeGrp
	 * @param unknown $txt
	 */
	public function getCodeNumByCodeGrpNEtcText($codeGrp, $txt)
	{
		$this->CI->db->select('NUM');
		$this->CI->db->from('CODE');
		$this->CI->db->where("(UPPER(CODE_GROUP) = '".strtoupper($codeGrp)."')");
		$this->CI->db->where("(UPPER(ETC_TEXT) = '".strtoupper($txt)."')");
		
		return $this->CI->db->get()->row()->NUM;
	}
	
	/**
	 * @method name : getEtcTitleByCodeNum
	 * CODE.NUM 으로 ETC_TEXT조회 
	 * 
	 * @param unknown $codeNum
	 */
	public function getEtcTitleByCodeNum($codeNum)
	{
		$this->CI->db->select('ETC_TEXT');
		$this->CI->db->from('CODE');
		$this->CI->db->where('NUM', $codeNum);
		
		return $this->CI->db->get()->row()->ETC_TEXT;
	}
	
	/**
	 * @method name : getCodeIdByCodeNum
	 * CODE.NUM으로 CODE.CODE_ID 조회
	 * 
	 * @param int $codeNum
	 */
	public function getCodeIdByCodeNum($codeNum)
	{
		$this->CI->db->select('CODE_ID');
		$this->CI->db->from('CODE');
		$this->CI->db->where('NUM', $codeNum);
		
		return $this->CI->db->get()->row()->CODE_ID;		
	}
	
	/**
	 * @method name : getCodeTitleByCodeNum
	 * CODE.NUM으로 CODE.TITLE 조회
	 * 
	 * @param unknown $codeNum
	 */
	public function getCodeTitleByCodeNum($codeNum)
	{
		$this->CI->db->select('TITLE');
		$this->CI->db->from('CODE');
		$this->CI->db->where('NUM', $codeNum);
		
		return $this->CI->db->get()->row()->TITLE;		
	}
	
	/**
	 * @method name : getCodeListByGroup
	 * GROUP 리스트 
	 * 
	 * @param unknown $grp
	 * @param unknown $isNoneView CODE_ID NONE 포함여부 - TRUE, FALSE
	 * @param unknown $isDelView DEL_YN = 'Y' 포함여부
	 */
	public function getCodeListByGroup($grp, $isNoneView = FALSE, $isDelView = FALSE)
	{
		$this->CI->db->from('CODE');
		$this->CI->db->where("CODE_GROUP = '".$grp."'");
		if (!$isDelView) $this->CI->db->where('DEL_YN', 'N');
		if (!$isNoneView) $this->CI->db->where_not_in('CODE_ID', array('NONE', 'TEMP_'));
		$this->CI->db->order_by('CODE_ORDER', 'ASC');
		$this->CI->db->order_by('NUM', 'ASC');
		
		return $this->CI->db->get()->result_array();		
	}
	
	/**
	 * @method name : setLogout
	 * 
	 * 
	 * @param bool $returnUrl 로그아웃후 돌아갈 페이지
	 * @param bool $isAlert	메세지 보임여부
	 */
	public function setLogout($returnUrl, $isAlert = TRUE)
	{
		$this->CI->session->sess_destroy();
		session_start();
		session_destroy();
		session_commit();

		// set_cookie('usernum', "",time()-3600, "/");
		// set_cookie('profileimg', "", time()-3600, "/");
		// set_cookie('authkey',"", time()-3600, "/");
		// set_cookie('deviceid', "", time()-3600, "/");
		// set_cookie('pushid', "", time()-3600, "/");

		// 20160511 yong mod - cookie delete 추가
		delete_cookie('usernum');
		delete_cookie('profileimg');
		delete_cookie('authkey');
		delete_cookie('deviceid');
		delete_cookie('pushid');

		$msg = ($isAlert) ? '로그아웃 되었습니다.' : '';
		$returnUrl = (empty($returnUrl)) ? '/' : $returnUrl;
		$this->message($msg, $returnUrl, 'self');
	}
	
	/**
	 * @method name : setAppLogout
	 * 앱에서 로그아웃
	 * 
	 */
	public function setAppLogout()
	{
 
		$this->CI->session->sess_destroy();
		session_start();
		session_destroy();
		session_commit();
	 
		// set_cookie('usernum', "",time()-3600, "/");
		// set_cookie('profileimg', "", time()-3600, "/");
		// set_cookie('authkey',"", time()-3600, "/");
		// set_cookie('deviceid', "", time()-3600, "/");
		// set_cookie('pushid', "", time()-3600, "/");
		 
		// 20160511 yong mod - cookie delete 추가
		delete_cookie('usernum');
		delete_cookie('profileimg');
		delete_cookie('authkey');
		delete_cookie('deviceid');
		delete_cookie('pushid');
	}	
	
	/**
	 * @method name : setFlag
	 * 플래그 반영
	 * 
	 * @param unknown $type
	 * @param unknown $num 관련된 고유번호 (shop, item, story)
	 * @param number $highNum 상위 고유번호가 존재하는 경우
	 * @return number
	 */
	public function setFlag($type, $num, $highNum = 0)
	{
		$flagResult = 0;
		$tblName = '';
		$type = strtoupper($type);
		if ($type == 'ITEMAPP') //highNum 사용을 구분하기 위해 app을 붙여서 구분함
		{
			$userNum = $highNum;
			$highNum = 0; //밑에서 잘못 쓰일수 있으므로 초기화
			$type = str_replace('APP', '', $type);
		}
		else if ($type == 'SHOPAPP')
		{
			$userNum = $highNum;
			$highNum = 0;
			$type = str_replace('APP', '', $type);			
		}
		else if ($type == 'STORYAPP')
		{
			$userNum = $highNum;
			$highNum = 0;
			$type = str_replace('APP', '', $type);
		}		
		else 
		{
			$sessionUserNum = $this->nullCheck($this->getSession('user_num'), 'int' , 0);
			$cookieUserNum = $this->nullCheck(get_cookie('usernum'), 'int', 0);
			
			if ($sessionUserNum > 0 || $cookieUserNum > 0)
			{
				if ($sessionUserNum > 0)
				{
					$userNum = $sessionUserNum;
				}
				else 
				{
					$userNum = $cookieUserNum;					
				}
			}
		}
		
		if ($userNum == 0) return -1; //로그인된 사용자가 아님
		
		$tblName = strtoupper($type);
		if ($type == 'ITEM')
		{
			$tblName = 'SHOP'.$tblName;
		}
		$tblCodeNum = $this->getCodeNumByCodeGrpNCodeId('TABLE', $tblName);
		
		//플래그 여부 확인
		$sql = "
			SELECT DEL_YN FROM FLAG
			WHERE TBLCODE_NUM = ".$tblCodeNum."
			AND TBL_NUM = ".$num."
			AND USER_NUM = ".$userNum."
		";
		$flagDt = $this->CI->db->query($sql)->row_array();		
		
		//Transaction 시작 (자동 수행)
		$this->CI->db->trans_start();
		
		if ($flagDt)
		{
			if ($flagDt['DEL_YN'] == 'Y')
			{
				$delYn = 'N';
				$flagResult = 1; //최종결과 flag				
			}
			else
			{
				$delYn = 'Y';
				$flagResult = 0; //최종결과 unflag
			}

			$this->CI->db->set('DEL_YN', $delYn);
			$this->CI->db->where('TBLCODE_NUM', $tblCodeNum);
			$this->CI->db->where('TBL_NUM', $num);
			$this->CI->db->where('USER_NUM', $userNum);
			$this->CI->db->update('FLAG');
		}
		else 
		{
			//플래깅 이력이 없는 경우이므로 추가
			$insData = array(
				'TBLCODE_NUM' => $tblCodeNum,
				'TBL_NUM' => $num,
				'USER_NUM' => $userNum,
				'REMOTEIP' => $this->CI->input->ip_address(),
				'DEL_YN' => 'N'
			);
			$this->CI->db->insert('FLAG', $insData);
			$flagResult = 1; //최종결과 flag
		}
		
		//$tblName의 총 플래그수 업데이트
		$sql = "(
			SELECT COUNT(*) FROM FLAG
			WHERE TBLCODE_NUM = ".$tblCodeNum."
			AND TBL_NUM = ".$num."
			AND DEL_YN = 'N'
		)";
		$this->CI->db->set('TOTFLAG_COUNT', $sql, FALSE);
		$this->CI->db->where('NUM', $num);
		$this->CI->db->update($tblName);		
		
		//플래깅 유저의 총 플래그수 업데이트
		$this->setFlagCountReUpdate($type, $userNum);
		
		if ($type == 'ITEM')
		{
			//샵에도 개수 반영
			$sql = "
				UPDATE SHOP
					SET
						TOTITEMFLAG_COUNT = (
							SELECT SUM(TOTFLAG_COUNT) FROM SHOPITEM
							WHERE SHOP_NUM IN (SELECT SHOP_NUM FROM SHOPITEM WHERE NUM = ".$num.")
						)
				WHERE NUM = (SELECT SHOP_NUM FROM SHOPITEM WHERE NUM = ".$num.")
			";
			$this->CI->db->query($sql);	
		}
		else if ($type == 'SHOP')
		{
			//반영할 내용 없음
		}
		else if ($type == 'STORY')
		{
			//반영할 내용 없음
		}		

		//Transaction 자동 커밋
		$this->CI->db->trans_complete();
		
		return $flagResult;
	}
	
	/**
	 * @method name : getAppAuthCheck
	 * 앱 사용자 유효성(인증) 체크 
	 * 
	 * @param unknown $authkey
	 * @param unknown $deviceId
	 * @param unknown $pushId
	 * @return boolean
	 */
	public function getAppAuthCheck($authkey, $deviceId, $pushId)
	{
		$result = FALSE;
		if ($authkey > 0 && !empty($deviceId) && !empty($pushId))
		{
			$this->CI->db->select("
				EXISTS (
					SELECT 1 FROM USER_APPINFO
					WHERE USER_NUM = ".$authkey."
					AND DEVICE_ID = '".$deviceId."'
					AND PUSH_ID = '".$pushId."'
					AND DEL_YN = 'N'
				) AS RESULT
			");
			$result = $this->CI->db->get()->row()->RESULT;
		}
		else if ($authkey == 0 && !empty($deviceId) && !empty($pushId))
		{
			$this->CI->db->select("
				EXISTS (
					SELECT 1 FROM USER_APPINFO
					WHERE DEVICE_ID = '".$deviceId."'
					AND PUSH_ID = '".$pushId."'
					AND DEL_YN = 'N'
				) AS RESULT
			");
			$result = $this->CI->db->get()->row()->RESULT;
		}
		
		log_message('debug', '[circus] - result : ' . $result);

		return $result;
	}
	
	/**
	 * @method name : getItemPolicyRowData
	 * 아이템 환불/교환정책 내용이 필요한 경우
	 *
	 */
	public function getItemPolicyRowData($siNum)
	{
		$this->CI->db->select("
			*
		");
		$this->CI->db->from('SHOPITEM');
		$this->CI->db->where("NUM = ".$siNum);
		
		return $this->CI->db->get()->row_array();		
	}
	
	/**
	 * @method name : getShopPolicyRowData
	 * 주문상세등에서 샵정책 내용이 필요한 경우
	 *
	 */
	public function getShopPolicyRowData($sNum)
	{
		$this->CI->db->select("
			*,
			(SELECT TITLE FROM CODE WHERE NUM = SHOP_POLICY.REFPOLICYCODE_NUM) AS REFPOLICYCODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = SHOP_POLICY.CALCYCLECODE_NUM) AS CALCYCLECODE_TITLE,
			(SELECT TITLE FROM CODE WHERE NUM = SHOP_POLICY.CALBANKCODE_NUM) AS CALBANKCODE_TITLE,				
			AES_DECRYPT(UNHEX(REFUND_TEL), '".$this->_encKey."') AS REFUND_TEL_DEC,
			AES_DECRYPT(UNHEX(REFUND_ZIP), '".$this->_encKey."') AS REFUND_ZIP_DEC,
			AES_DECRYPT(UNHEX(REFUND_ADDR1), '".$this->_encKey."') AS REFUND_ADDR1_DEC,
			AES_DECRYPT(UNHEX(REFUND_ADDR2), '".$this->_encKey."') AS REFUND_ADDR2_DEC,
			AES_DECRYPT(UNHEX(REFUND_ADDR_JIBUN), '".$this->_encKey."') AS REFUND_ADDR_JIBUN_DEC
		");
		$this->CI->db->from('SHOP_POLICY');
		$this->CI->db->where("SHOP_NUM = ".$sNum);
		
		return $this->CI->db->get()->row_array();
	}	
	
	/**
	 * @method name : getStandardShopPolicyRowData
	 * 기준샵에서 정책 조회
	 * USERLEVEL - STDSHOP 인 회원이 생성한 SHOP
	 * STDSHOP 권한을 가지고 생성한 SHOP은 꼭 한개여야만 함
	 *
	 */
	public function getStandardShopPolicyRowData()
	{
		return $this->CI->db->query("
			SELECT
				a.USER_NUM,
				b.*,
				(SELECT TITLE FROM CODE WHERE NUM = b.REFPOLICYCODE_NUM) AS REFPOLICYCODE_TITLE
				FROM SHOP a INNER JOIN SHOP_POLICY b
				ON a.NUM = b.SHOP_NUM
				AND a.USER_NUM IN
					(
						SELECT NUM FROM USER
						WHERE ULEVELCODE_NUM = ".$this->getCodeNumByCodeGrpNCodeId('USERLEVEL', 'STDSHOP')."
		) LIMIT 1")->row_array();
	}	
	
	/**
	 * @method name : getItemInfoByItemNum
	 * 아이템고유번호로 아이템 간단 정보 조회 
	 * 
	 * @param unknown $siNum
	 */
	public function getItemInfoByItemNum($siNum)
	{
		return $this->CI->db->query("
			SELECT
				SHOP_NUM,
				ITEM_CODE,
				ITEM_NAME,
				ITEM_PRICE
			FROM SHOPITEM
			WHERE NUM = ".$siNum."
		")->row_array();
	}

	/**
	 * @method name : getIsBlackUserIP
	 * 블랙리스트 확인
	 * 회원고유번호와 아이피가 모두 일치 할 경우
	 * 회원고유번호만 일치할 경우
	 * 아이피만 일치할 경우
	 * 중 어떤 기준을 적용할지 정해야함
	 * (현재는 아이피만 일치하면 블랙으로 간주)
	 *
	 * @param unknown $uNum
	 * @param unknown $remoteIP
	 * @return boolean
	 */
	public function getIsBlackUserIP($uNum, $remoteIP)
	{
		$this->CI->db->select('COUNT(*) AS COUNT');
		$this->CI->db->from('USER_BLACK');
		//$this->CI->db->where('USER_NUM', $uNum);
		$this->CI->db->where('REMOTEIP', $remoteIP);
		$this->CI->db->where('DEL_YN', 'N');
		$result = $this->CI->db->get()->row()->COUNT;
	
		return ($result == 0) ? FALSE : TRUE;
	}	
	
	/**
	 * @method name : abuseWordCheck
	 * 금지어 체크 
	 * 
	 * @param unknown $content
	 */
	public function abuseWordCheck($content)
	{
		$this->CI->db->select('AWORD');
		$this->CI->db->from('ABUSE_WORD');
		$abuseSet = $this->CI->db->get()->result_array();
		
		$mod_content = strtolower(strip_tags($content));
		$count = count($abuseSet);
		$error = '';
		$isChecked = FALSE;
		
		for ($i=0; $i<$count; $i++) 
		{
			$str = $abuseSet[$i]['AWORD'];
			// 내용 필터링 (찾으면 중지)
			$pos = strpos($mod_content, $str);
			if ($pos !== false) 
			{
				$isChecked = TRUE;
				$error = $str; //.= '내용에 금지단어(\''.$str.'\')가 포함되어있습니다.';
				break;
			}
		}
		
		return array(
			'isChecked' => $isChecked,
			'checkedWord' => $error
		);
	}
	
	/**
	 * @method name : setFlagCountReUpdate
	 * 플래그 이후 삭제되었거나 유효하지 않은 건수를 제외후
	 * 재취합 
	 * 
	 * @param unknown $type
	 * @param unknown $userNum
	 */
	public function setFlagCountReUpdate($type, $userNum)
	{
		$type = strtoupper($type);
		$tblName = $type;
		if ($type == 'ITEM')
		{
			$tblName = 'SHOP'.$tblName;
		}
		$tblCodeNum = $this->getCodeNumByCodeGrpNCodeId('TABLE', $tblName);
		
		//플래깅 유저의 총 플래그수 업데이트
		$userSql = "(
			SELECT COUNT(*) 
			FROM FLAG a INNER JOIN ".$tblName." b
				ON a.TBL_NUM = b.NUM					
			WHERE a.TBLCODE_NUM = ".$tblCodeNum."
			AND a.USER_NUM = ".$userNum."
			AND a.DEL_YN = 'N'
			AND b.DEL_YN = 'N'
		)";
		
		//플래깅 유저의 총 플래그수 업데이트
		$this->CI->db->set('TOT'.$type.'FLAG_COUNT', $userSql, FALSE);
		$this->CI->db->where('NUM', $userNum);
		$this->CI->db->update('USER');
	}
	
	/**
	 * @method name : getIsFlaged
	 * flag항목별로 플래그 되어 있는지 확인
	 * 
	 * @param unknown $type
	 * @param unknown $num
	 * @param unknown $userNum
	 */
	public function getIsFlaged($type, $num, $userNum)
	{
		$type = strtoupper($type);
		$tblName = $type;
		if ($type == 'ITEM')
		{
			$tblName = 'SHOP'.$tblName;
		}
		$tblCodeNum = $this->getCodeNumByCodeGrpNCodeId('TABLE', $tblName);
		
		$this->CI->db->select("
				EXISTS (
					SELECT 1 
					FROM FLAG a INNER JOIN ".$tblName." b
						ON a.TBL_NUM = ".$num."					
					WHERE a.TBLCODE_NUM = ".$tblCodeNum."
					AND a.USER_NUM = ".$userNum."
					AND a.DEL_YN = 'N'
				) AS RESULT
			");
		return $this->CI->db->get()->row()->RESULT;
	}

	public function sendEMailTemp($tmpData)
	{

		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP

		$userEmail = $tmpData["userEmail"];
		$newPass = $tmpData["newPass"];
		$userName = explode("@", $userEmail);
		$mailContent ='     새 비밀번호는 '.$newPass.'입니다.     ';

		try {
				$subjectData = $userEmail . " 's New Password";
				$bodyData = "* 문의자 이메일 : ";
				$bodyData .= $userEmail;
				$bodyData .= "<br>";

				$bodyData .= "* 내용 : ";
				$bodyData .= $mailContent;

				$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
				$mail->SMTPAuth   = true;                  // enable SMTP authentication
				$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
				$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
				$mail->Port       = 465;                   // set the SMTP port for the GMAßL server
				$mail->Username   = "mail@circusflag.com";  // GMAIL username
				$mail->Password   = "ghnowlxbglvkwfjw";            // GMAIL password

				$mail->AddAddress($userEmail, $userName[0]);

				$mail->SetFrom('mail@circusflag.com', 'CircusFlag');
				$mail->AddReplyTo('mail@circusflag.com', 'CircusFlag');
				$mail->Subject = $subjectData;
				$mail->AltBody = $bodyData; // optional - MsgHTML will create an alternate automatically

				$mail->MsgHTML($bodyData);
				$mail->Send();

		} catch (phpmailerException $e) {

			echo $e->errorMessage(); //Pretty error messages from PHPMailer

		} catch (Exception $e) {

			echo $e->getMessage(); //Boring error messages from anything else!

		}
	}

}
?>