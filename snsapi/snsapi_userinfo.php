<?php
require "../wxApi.php";
define("TOKEN", "echoso");
define("APPID", "wx0b0b9449118256f3");
define("APPSECRET", "28efc088e60a03ecea09990183932fd8");

$wechatObj = new wechatCallbackapi(APPID,APPSECRET);

$base_accessToken_url = "http://echoso.s3.natapp.cc/echoso/snsapi/snsapi_userinfo.php";
$state_date = "123321";
$result = $wechatObj->snsapi_userinfo($base_accessToken_url,$state_date);

echo "<pre/>";
var_dump($result);
/*
 * array(5) {
  ["access_token"]=>
  string(107) "vx60MI6FLQL6U_C8nNUDSX0w4ylQb35Q2CxEEaqxn_s43LEdu5yU8oyiIxODb_rIgwnVdb9rOGOjTrn5TSrcwQ2scYrxhYRwzNY_63IwDds"
  ["expires_in"]=>
  int(7200)
  ["refresh_token"]=>
  string(107) "hwxc0zFGhqurNAXRuKb9qmhVeXAPdhHV7U2tGeA63xf_gVBIn0U3jXkIZ27sl6hnX66zWTvPT2OfVqMZN9yg9tb0wj4oHncV3UihciUTIF4"
  ["openid"]=>
  string(28) "os58pwuB175d91PvOp1Bkcc9yZHI"
  ["scope"]=>
  string(11) "snsapi_base"
}
 * */
?>