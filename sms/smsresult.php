<?PHP
$sms_url = "https://sslsms.cafe24.com/sms_list.php"; // 전송요청 URL
$sms['user_id'] = ""; // SMS 아이디
$sms['secure'] = "" ;//인증키
$sms['date'] = "" ;//조회 기준일
$sms['day'] = "1" ;//조회 범위
$sms['startNo'] = "0" ;//조회 시작번호
$sms['displayNo'] = "10" ;//출력 갯수
$sms['sendType'] = "" ;//발송형태
$sms['sendStatus'] = "" ;//발송상태
$sms['receivePhone'] = "" ;//검색할 수신번호
$sms['sendPhone'] = "" ;//검색할 발신번호
$sms['smsType'] = "" ;// LMS, MMS 조회인경우 lms
                
$host_info = explode("/", $sms_url);
$host = $host_info[2];
$path = $host_info[3]."/".$host_info[4];
srand((double)microtime()*1000000);
$boundary = "---------------------".substr(md5(rand(0,32000)),0,10);

// 헤더 생성
$header = "POST /".$path ." HTTP/1.0\r\n";
$header .= "Host: ".$host."\r\n";
$header .= "Content-type: multipart/form-data, boundary=".$boundary."\r\n";

// 본문 생성
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
    echo $msg[1];
}
else {
    echo "Connection Failed";
}
?>