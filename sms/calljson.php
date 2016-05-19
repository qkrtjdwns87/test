<?php
$oCurl = curl_init();
$url =  "https://sslsms.cafe24.com/smsSenderPhone.php";
$aPostData['userId'] = "circusflag";
$aPostData['passwd'] = "582f4c0c3b82b3f73dd4a39cb1c96c5d";
curl_setopt($oCurl, CURLOPT_URL, $url);
curl_setopt($oCurl, CURLOPT_POST, 1);
curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($oCurl, CURLOPT_POSTFIELDS, $aPostData);
curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0); 
$ret = curl_exec($oCurl);
echo $ret;
curl_close($oCurl);
?>