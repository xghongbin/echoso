<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2016/10/25
 * Time: 23:54
 */

require("wx_sample.php");

$appid = "wx0b0b9449118256f3";
$appsecret = "28efc088e60a03ecea09990183932fd8";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;

$wx_obj = new wechatCallbackapiTest();
$access_token = $wx_obj->http_request($url);


return $access_token['access_token'];
