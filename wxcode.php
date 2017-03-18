<?php
/*
 * 包含wechat_class.php文件
 * */
require "wxApi.php";
define("TOKEN", "echoso");
define("APPID", "wx0b0b9449118256f3");
define("APPSECRET", "28efc088e60a03ecea09990183932fd8");
define("SETTYPE",'1');
$wechatObj = new wechatCallbackapi(APPID,APPSECRET);

if(isset($_GET['echostr'])){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}


/*
 *  获取 AccessToken ,判断时间是否已经超过 7200秒
 *  获取 access_token 有三种方法：
 *  1. memcache 获取缓存   （SAE）
 *  2. 数据库存表数据获取
 *  3. 文件读写操作
 * SETTYPE  为1使用SAE Memcache
 *          为2使用数据库
 *          为3使用文件读写操作（SAE不支持此操作方案）
 * */
if(SETTYPE == '1')
{
    $accessToken = $wechatObj->return_AccessToken();
    //bAKblZRNRnyFXrkw_Ena-PoNpAateufeni-97eIFTUTR96cruPDZwKtdb2EtZQeuYZKTYuLGXBNbfcrr2eStQda9MNCTs1J36Ua4QmdLPKd7ksaIk-clmG_HBVLc0oU8HHTgAAAUUG
}
elseif (SETTYPE == '2')
{

}
else
{

}


/*
 *  设置 [自定义菜单]
 *  return：
 *          Array ( [errcode] => 0 [errmsg] => ok )
 *  PS:
 *      后期后台设置操作，数据库查询获取自定义菜单
 * */
if(!empty($accessToken)){
    $jsonmenu = '
        {
            "button": [
                {
                    "name": "菜单", 
                    "sub_button":
                    [
                        {
                          "type":"click",
                          "name":"了解EcshoSo",
                          "key":"ABOUT"
                        },
                        {	
                           "type":"click",
                           "name":"跳转",
                           "key":"view",
                           "url":"http://www.soso.com/"
                        },
                        {	
                           "type":"click",
                           "name":"扫描场景",
                           "key":"scan"
                        },
                        {
                            "name": "上传位置", 
                            "type": "location_select", 
                            "key": "location"
                        },
                        {
                            "name": "扫码带提示", 
                            "type": "scancode_waitmsg", 
                            "key": "scancode_waitmsg", 
                        }
                    ]
                }
            ]
        }
        ';
    $wechatObj->set_men($accessToken,$jsonmenu);
}

/*
 *  返回[自定义菜单]JSON格式数据
 * */
if(!empty($accessToken)){
    //print_r($wechatObj->return_setmenu_json($accessToken));
}

/*
 *  删除[自定义菜单]
 * */
if(!empty($accessToken)){
    //print_r($wechatObj->del_setmenu($accessToken));
}

?>