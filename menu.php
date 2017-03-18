<?php
/**
 * Created by PhpStorm.
 * User: xghongbin  菜单设置、返回菜单json、删除菜单
 * Date: 2016/10/31
 * Time: 11:21 
 */

require "wxApi.php";
define("TOKEN", "echoso");
define("APPID", "wx0b0b9449118256f3");
define("APPSECRET", "28efc088e60a03ecea09990183932fd8");

$wechatObj = new wechatCallbackapi(APPID,APPSECRET);

$accessToken = $wechatObj->file_ReturnAccessToken();
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
 $return_menu   = $wechatObj->set_men($accessToken,$jsonmenu);
// $return_menu   = $wechatObj->return_setmenu_json($accessToken);
// $return_menu   = $wechatObj->del_setmenu($accessToken);

echo "<pre/>";
var_dump($return_menu);