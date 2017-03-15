<?php
/**
 * Created by PhpStorm.
 * User: xghongbin 
 * Date: 2016/10/25
 * Time: 22:09 
 */
define("TOKEN","echoso");
define("AppID","wx335d4e53b0defe5c");
define("AppSecret","37e460b20ba8670b71d5c4d8bc885cf0");




//加载API文件
require ('weixinApi.php');

$echoStr  = $_GET["echostr"];//随机字符串
$wechatObj = new wechatCallbackapiTest();
if(isset($_GET['echostr'])){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

