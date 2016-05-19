<?
$sms_url = "https://sslsms.cafe24.com/sms_remain.php"; // SMS 잔여건수 요청 URL
$sms['user_id'] = base64_encode("SMS 아이디"); // SMS 아이디
$sms['secure'] = base64_encode("인증키") ;//인증키

$sms['mode'] = base64_encode("1"); // base64 사용시 반드시 모드값을 1로 주셔야 합니다.

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
    $Count = $msg[1]; //잔여건수
    echo $Count;
}
else {
    echo "Connection Failed";
}
?>