<?php
/**
 * Created by PhpStorm.
 * User: xghongbin
 * Date: 2016/10/25
 * Time: 21:47
 */


class wechatCallbackapiTest
{

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }


    //	校验微信加密签名Signature
    private function checkSignature()
    {
        //  1.接受微信服务器get请求过来的4个参数
        $signature = $_GET["signature"];//微信加密签名
        $timestamp = $_GET["timestamp"];//随机时间戳
        $nonce = $_GET["nonce"];//随机数


        /*
         *  2.加密/校验
         *  token、timestamp、nonce进行字典序排序
         *  三个参数的字符串拼接成一个字符串进行sha1加密
         * */
        $tmpArr = array(TOKEN, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        //	微信加密签名 与 开发者获得加密后的字符串 互相对比，标示请求来源
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    //返回数据信息
    public function responseMsg()
    {
        //访问获取原始 POST 数据
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (!empty($postStr)){

            libxml_disable_entity_loader(true);

            //  转换成XML对象并获取相对于的数据源
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //  获取消息类型
            $RX_TYPE = $postObj->MsgType;

            //  switch判断用户发送的消息类型
            switch ($RX_TYPE){
                case "text"://文本消息
                    $result = $this->receiveText($postObj);
                    break;
                case "image"://图片消息
                    $result = $this->receiveImage($postObj);
                    break;
                case "voice"://语音消息
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video"://视频消息
                    $result = $this->receiveVideo($postObj);
                    break;
                case "location"://位置消息
                    $result = $this->receiveLocation($postObj);
                    break;
                case "link"://链接消息
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknow msg type:".$RX_TYPE;
                    break;

            }

            if(!empty($result)){
                echo $result;
            }else{
                echo "result null error";
            }

        }else {
            echo "";
            exit;
        }
    }
}