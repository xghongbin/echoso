<?php
require('../wxApi.php');
$appid = "wx0b0b9449118256f3";
$appsecret = "28efc088e60a03ecea09990183932fd8";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$wechatObj = new wechatCallbackapi($appid,$appsecret);

$accessToken = $wechatObj->file_ReturnAccessToken();
print_r($accessToken);
/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
$jsoninfo = json_decode($output, true);
$access_token = $jsoninfo["access_token"];
print_r($access_token);
*/

?>