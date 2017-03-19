<?php
/*
 * 包含wechat_class.php文件
 * */
require "wxApi.php";
define("TOKEN", "echoso");
define("APPID", "wx0b0b9449118256f3");
define("APPSECRET", "28efc088e60a03ecea09990183932fd8");
define("SETTYPE",'3');
define("SETMENU",false);
define("SRETURNMENU",false);
define("DELMENU",false);

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
        //$accessToken = $wechatObj->return_AccessToken();
    }
    elseif (SETTYPE == '2')
    {

    }else
    {
        $accessToken = $wechatObj->file_ReturnAccessToken();
    }


/*
 *  设置 [自定义菜单]
 *  return：
 *          Array ( [errcode] => 0 [errmsg] => ok )
 *  PS:
 *      后期后台设置操作，数据库查询获取自定义菜单
 * */
    if(SETMENU){
        if(!empty($accessToken)){
            $jsonmenu = '{
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
                                }
                                {
                                    "name": "上传位置", 
                                    "type": "location_select", 
                                    "key": "location"
                                }
                            ]
                        },
                        {
                            "name": "发图", 
                            "sub_button": [
                                {
                                    "type": "pic_sysphoto", 
                                    "name": "系统拍照发图", 
                                    "key": "rselfmenu_1_0", 
                                   "sub_button": [ ]
                                 }, 
                                {
                                    "type": "pic_photo_or_album", 
                                    "name": "拍照或者相册发图", 
                                    "key": "rselfmenu_1_1", 
                                    "sub_button": [ ]
                                }, 
                                {
                                    "type": "pic_weixin", 
                                    "name": "微信相册发图", 
                                    "key": "rselfmenu_1_2", 
                                    "sub_button": [ ]
                                }
                            ]
                        }
                    ]
                }
                ';
            $wechatObj->set_menu($accessToken,$jsonmenu);
        }
    }
    /*
     *  返回[自定义菜单]JSON格式数据
     * */
    if(SRETURNMENU){
        if(!empty($accessToken)){
            print_r($wechatObj->return_setmenu_json($accessToken));
        }
    }

    /*
     *  删除[自定义菜单]
     * */
    if(DELMENU){
        if(!empty($accessToken)){
            print_r($wechatObj->del_setmenu($accessToken));
        }
    }
?>