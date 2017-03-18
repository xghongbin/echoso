<?php
/*
 * 包含wechat_class.php文件
 * */
require "wxApi.php";
define("TOKEN", "echoso");
$wechatObj = new wechatCallbackapi();

if(isset($_GET['echostr'])){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}



?>