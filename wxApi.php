<?php
/**
  * 微信类
  * wechat_class.php
  */
require ("Db/db.php");
//require ("function/function.php");
class wechatCallbackapi
{
    //  成员属性
    private $appid;
    private $appsecret;

    /*
     * 构造方法 对成员属性赋值
     * */
    public function __construct($appid = "",$appsecret = "")
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

    /*
     * 确认微信签名的比对返回随机字符串
     * */
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        if($this->checkSignature())
        {
            echo $echoStr;
            exit;
        }
    }

    /*
     *  校验签名方法
     * */
    private function checkSignature()
    {
        //	判断常量token是否有值
        if (!defined("TOKEN"))
        {
            throw new Exception('TOKEN is not defined!');
        };

        $signature = $_GET["signature"];//微信加密签名
        $timestamp = $_GET["timestamp"];//随机时间戳
        $nonce = $_GET["nonce"];//随机数

        //	字典序排序之后，进行拼接为一个字符串并sha1加密
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);

        //	use SORT_STRING rule  使用字典序排序
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        //	微信加密签名 与 开发者获得加密后的字符串 互相对比，标示请求来源
        if( $tmpStr == $signature )
        {
            return true;
        }else{
            return false;
        }
    }


    /*
     * 返回数据信息
     */
    public function responseMsg()
    {
        /*
         * 预定义变量，获取原生post数据 isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA']:
         * php5.6不建议使用$GLOBALS[]来接收POST数据,推荐改用  file_get_contents("php://input");
         *
         * */
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');

        if (!empty($postStr))
        {

            libxml_disable_entity_loader(true);
            /*
             *  转换成XML对象并获取相对于的数据源
             * simplexml_load_string(string,class,options)
             * */
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            //  获取消息类型
            $RX_TYPE = $postObj->MsgType;

            //  switch判断用户发送的消息类型
            switch ($RX_TYPE)
            {
                case "event"://事件消息
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text"://文本消息
                    $result = $this->receiveText($postObj);
                    break;
                case "voice"://语音消息
                    $result = $this->receiveVoice($postObj);
                    break;
                case "image"://图片消息
                    $result = $this->receiveImage($postObj);
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

            if(!empty($result))
            {
                echo $result;
            }else{
                echo "result null error";
            }

        } else {
            echo "HTTP_RAW_POST_DATA is null";
            exit;
        }

    }


/* [设置消息类型] 根据类型，判断返回特定消息  */

    /*
     * 	接收文本消息
     *  @param  $object
     * */
    private function receiveText($object)
    {

        $keyword = trim($object->Content);
        /*
         * 后期关键字回复从数据库中取出，后台设置关键字、回复内容等都从后台编辑。
         * */
        if(strtolower($keyword) == "echoso" )
        {
            /*
             * 这里可以有后台设置是文本回复还是echoso二维码回复
             * if判断回复类型
             * */

            //  文本回复感谢消息
            //  $content = "多谢关注 EchoSo";
            //  $request = $this->transmitText($object,$content);

            //  echoso二维码图文回复
            $content[] = array(
                "Title"=>"多谢关注 EchoSo",
                "Description"=>"关注Echoso，关注测试，关注PHPer的开发经历！",
                "PicUrl"=>"http://echoso.s3.natapp.cc/echoso/img/echoso.png",
                "Url"=>"www.baidu.com"
            );
            $request = $this->transmitNews($object,$content);

        }
        else if($keyword == "单图文")
        {
            $content = array();
            /*
             * 后台设置之后存入数据库，后期这里去数据可以从数据库中select出来
             * */
            $content[] = array(
                "Title"=>"单图文标题",
                "Description"=>"单图文内容",
                "PicUrl"=>"http://echoso.s3.natapp.cc/echoso/img/test.png",
                "Url"=>"www.baidu.com"
            );
            $request = $this->transmitNews($object,$content);

        }
        else if($keyword == "多图文")
        {
            $content = array();
            /*
             * 后台设置之后存入数据库，后期这里去数据可以从数据库中select出来[多图文默认超过10，则无响应]
             * */
            $content[] = array(
                "Title"=>"多图文1标题",
                "Description"=>"多图文1内容",
                "PicUrl"=>"http://echoso.s3.natapp.cc/echoso/img/test.png",
                "Url"=>"www.baidu.com"
            );
            $content[] = array(
                "Title"=>"多图文2标题",
                "Description"=>"多图文2内容",
                "PicUrl"=>"http://echoso.s3.natapp.cc/echoso/img/test.png",
                "Url"=>"www.sina.com"
            );
            $request = $this->transmitNews($object,$content);
        }
        else if($keyword == "音乐")
        {
            $musicContent = array(
                "Title"=>"我是天秤座",
                "Description"=>"歌手:徐梦园",
                "MusicUrl"=>"http://echoso.s3.natapp.cc/echoso/music/tczzg.mp3",
                "HQMusicUrl"=>"http://echoso.s3.natapp.cc/echoso/music/tczzg.mp3"
            );

            $request = $this->transmitMusic($object,$musicContent);

        } else {
            $content = "您发送的是文本，内容为:".$keyword;
            $request = $this->transmitText($object,$content);
        }
        return $request;
    }


    /*
     *  回复文本消息
     *  @param  $object
     *  @param  $content  string
     **/
    private function transmitText($object,$content)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                </xml>";

        $resultStr = sprintf($textTpl,$object->FromUserName,$object->ToUserName,time(),$content);
        return $resultStr;
    }

    /*
     * 	接收图片消息
     *  @param  $object
     * */
    private function receiveImage($object)
    {
        $ImageContent = array("MediaId"=>$object->MediaId);
        $ImageRequest = $this->transmitImage($object,$ImageContent);
        return $ImageRequest;

    }


    /*
     *  回复图片消息
     *  @param  $object
     *  @param  $imageArray  Array
     **/
    private function transmitImage($object,$imageArray)
    {
        $itemTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                        <Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
                    </xml>";

        $resultStr = sprintf($itemTpl,$object->FromUserName,$object->ToUserName,time(),$imageArray['MediaId']);
        return $resultStr;
    }


    /*
     *  回复图文消息
     *  @param  $object
     *  @param  $arr_item  Array
     * */
    private function transmitNews($object,$arr_item)
    {

        //  参数预先判断
        if(!is_array($arr_item)) return;

        //初始化
        $item_str   =   "";

        //  多图文需要遍历循环
        foreach ($arr_item as $item)
        {
            $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>";

            $item_str .= sprintf($itemTpl,$item['Title'],$item['Description'],$item['PicUrl'],$item['Url']);
        }

        $newsTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>".$item_str."</Articles>
                    </xml>";

        $resultStr = sprintf($newsTpl,$object->FromUserName,$object->ToUserName,time(),count($arr_item));
        return $resultStr;

    }


    /*
     * 	接收视频消息
     *  @param  $object
     * */
    private function receiveVideo($object)
    {
        $VideoContent = array("MediaId"=>$object->MediaId,"ThumbMediaId"=>$object->ThumbMediaId,"Title"=>"您的视频","Description"=>"视频内容描述");
        $VideoRequest = $this->transmitVideo($object,$VideoContent);
        return $VideoRequest;

    }

    /*
     *  回复视频消息
     *  @param  $object
     *  @param  $VideoArray  Array
     * */
    private function transmitVideo($object,$VideoArray)
    {

        $videoTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[video]]></MsgType>
                        <Video>
                            <MediaId><![CDATA[%s]]></MediaId>
                            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                            <Title><![CDATA[%s]]></Title>
                            <Description><![CDATA[%s]]></Description>
                        </Video>
                      </xml>";

        $resultStr = sprintf($videoTpl,$object->FromUserName,$object->ToUserName,time(),$VideoArray['MediaId'],$VideoArray['ThumbMediaId'],$VideoArray['Title'],$VideoArray['Description']);
        return $resultStr;
    }


    /*
     * 	接收位置消息
     *  @param  $object
     * */
    private function receiveLocation($object)
    {
        $LocationContent = "您发送的是位置，纬度为".$object->Location_X.";经度为:".$object->Location_Y.";缩放级别为：".$object->Scale.";位置描述:".$object->Label.";MsgId:".$object->MsgId;
        $LocationRequest = $this->transmitText($object,$LocationContent);
        /*
         *  此处验证200通过，但具体用途是发送“附件银行(地方名)”,返回具体附近地点等信息
         *  开发者服务器是不能返回地理位置消息显示的，所以这是一个误区
         * $LocationContent = array(
              "Location_X"=>$object->Location_X,
              "Location_Y"=>$object->Location_Y,
              "Scale"      =>$object->Scale,
              "Label"      =>$object->Label,
              "MsgId"    =>$object->MsgId
        );*/
        //$LocationContent = "id:".$object->MsgId;
        //$LocationRequest = $this->transmitLocation($object,$LocationContent);
        return $LocationRequest;

    }


    /*
     *  回复地理位置消息
     *  @param  $object
     *  @param  $LocationArray  Array
     * */
    private  function transmitLocation($object,$LocationArray)
    {
        $LocationTpl = "
                <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[event]]></MsgType>
                    <Event><![CDATA[location_select]]></Event>
                    <EventKey><![CDATA[location]]></EventKey>
                    <SendLocationInfo>
                        <Location_X><![CDATA[%s]]></Location_X>
                        <Location_Y><![CDATA[%s]]></Location_Y>
                        <Scale><![CDATA[%s]]></Scale>
                        <Label><![CDATA[%s]]></Label>
                        <Poiname><![CDATA[]]></Poiname>
                    </SendLocationInfo>
                </xml>
        ";
        $resultStr = sprintf($LocationTpl,$object->FromUserName,$object->ToUserName,time(),$LocationArray['Location_X'],$LocationArray['Location_Y'],$LocationArray['Scale'],$LocationArray['Label']);
        return $resultStr;
    }


    /*
     * 	接收链接消息
     *  @param  $object
     * */
    private function receiveLink($object)
    {
        $LinkContent = "您发送的是链接，纬度为".$object->Title.";内容为:".$object->Description.";链接地址为：".$object->Url;
        $LinkRequest = $this->transmitLink($object,$LinkContent);
        return $LinkRequest;

    }


    /*
     *  接收事件消息
     *  @param  $object
     * */
    private function receiveEvent($object)
    {
        $eventContent = "";

        //判断事件类型
        switch ($object->Event)
        {
            case "subscribe"://订阅
                //$eventContent = "欢迎关注Echoso，您可以发送以下文字，获取测试\n 'ecsho'\n '音乐'\n '单图文'\n '多图文'";

                /*
                 * 微信墙：请求获取用户基本信息接口的地址
                 * */
                $openid =   $object->FromUserName;
                $accessToken = $this->file_ReturnAccessToken();
                $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$accessToken}&openid={$openid}&lang=zh_CN";
                $userInfo = $this->http_request($url);
                //var_dump($userInfo['headimgurl']);
                //exit();
/*
 *
 * array(13) {
  ["subscribe"]=>
  int(1)
  ["openid"]=>
  string(28) "os58pwqkO8l7b_oCOU9Imt6VnPI8"
  ["nickname"]=>
  string(5) "csohi"
  ["sex"]=>
  int(2)
  ["language"]=>
  string(5) "zh_CN"
  ["city"]=>
  string(6) "广州"
  ["province"]=>
  string(6) "广东"
  ["country"]=>
  string(6) "中国"
  ["headimgurl"]=>
  string(139) "http://wx.qlogo.cn/mmopen/Q3auHgzwzM6yWrNoJKRtfSAgQ3763NOXGtcK9eLoeexUlK8f
P34xzJc3QCRDTicYPMqQJqK0orTibnsialSbkjOOMj2SYsxFibmsLMkG4AQlZYQ/0"
  ["subscribe_time"]=>
  int(1489931065)
  ["remark"]=>
  string(0) ""
  ["groupid"]=>
  int(0)
  ["tagid_list"]=>
  array(0) {
  }
}
 * */
                $openid = $userInfo['openid'];
                $nickname = $userInfo['nickname'];

                $sex = $userInfo['sex'];
                $city = $userInfo['city'];
                $province = $userInfo['province'];
                $country = $userInfo['country'];
               echo $headimgurl = $userInfo['headimgurl'];
                $subscribe_time = $userInfo['subscribe_time'];// 由于图片属于防盗链的方式，所以需要下载到本地
                $groupid = $userInfo['groupid'];

                //echo $headimgurl;
                // 将图片保存在自己的服务器上
                //$headimgurl = file_get_contents($headimgurl);file_get_contents('php://input')
                //$headimgurl = $this->http_request($headimgurl);

                $imgName = $openid.'jpg';
                //print_r($headimgurl);
                //print_r($imgName);
                //exit();
                //保存到本地上
                $save_dir = "http://echoso.s3.natapp.cc/echoso/userimg/";
                //$imgArray = $this->getImage($headimgurl,$save_dir,$imgName);
                $abbc = $this->downFile($headimgurl,$save_dir);

                var_dump($abbc);

                //$Conn = new Mysql();
                //$Conn->Parameter('127.0.0.1:80', 'root', 'qweasd', 'wechat', '', '');
                //$abbc = $Conn->querys("insert into userinfo(openid,nickname,sex,city,province,country,headimgurl,subscribe_time)VALUES('$openid','$nickname','$sex','$city','$province','$country','$imgArray[save_path]','$subscribe_time')");
                //echo $abbc;


                break;
            case "unsubscribe"://取消订阅
                /*
                 * 取消关注，用于账号解绑，取消和商城的一些账号解绑操作
                 * */
                $eventContent = "";
                break;
                /*
                 * 点击菜单拉取消息的时间推送，
                 * click可以根据自定义菜单接口中 Key 相互对应，任何值都可以
                 * */
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "ABOUT":
                        $eventContent = array();
                        $eventContent[] = array(
                            "Title"=>"EchoSo",
                            "Description"=>"欢迎关注EchoSo",
                            "PicUrl"=>"http://echoso.s3.natapp.cc/img/echoso.png",
                            "Url"=>"www.echoso.com"
                        );
                        break;
                    default:
                        $eventContent = "单击菜单:".$object->EventKey;
                        break;
                }
                break;
            case "view":    // 点击菜单拉取消息的时间推送
                $eventContent = "跳转链接:".$object->url;
                break;
            case "scan":    // 点击菜单拉取消息的时间推送
                $eventContent = "扫描场景:".$object->EventKey;
                break;
            case "location_select":    // 弹出地理位置选择器的事件推送
                $eventContent = "上传位置: ".$object->SendLocationInfo->Location_X.";经度".$object->SendLocationInfo->Location_Y."; 发送的位置信息".$object->SendLocationInfo->Label;
                break;
            case "scancode_waitmsg":    // 扫码推事件且弹出“消息接收中”提示框的事件推送
                $eventContent = "扫码带提示：类型".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
                break;
            case "scancode_push":   // 点击菜单拉取消息的时间推送
                $eventContent = "扫码推事件:";
                break;
            default:
                $eventContent = "know't type";
                break;

        }

        if(is_array($eventContent))
        {
            if(isset($eventContent[0]['PicUrl']))
            {
                $EventRequest = $this->transmitNews($object,$eventContent);
            }else if(isset($eventContent['MusicUrl']))
            {
                $musicContent = array(
                    "Title"=>"我是天秤座",
                    "Description"=>"歌手:徐梦园",
                    "MusicUrl"=>"http://echoso.s3.natapp.cc/echoso/music/tczzg.mp3",
                    "HQMusicUrl"=>"http://echoso.s3.natapp.cc/echoso/music/tczzg.mp3"
                );
                $EventRequest = $this->transmitMusic($object,$musicContent);
            }

        }else{
            $EventRequest = $this->transmitText($object,$eventContent);
        }
        return $EventRequest;

    }


    /*
     * 	接收语音消息
     *  接受语音时，判断是否开启语音识别
     *  @param  $object
     * */
    private function receiveVoice($object)
    {

        if(isset($object->Recognition) && !empty($object->Recognition))
        {
            $VoiceContent = "你发送的是语音，内容为：".$object->Recognition;
        }else{
            $VoiceContent = "未开启语音识别功能或者识别内容为空";
        }
        /*
         *  语音识别暂时不做，可以后期再进行代码的编辑
         * */

        $VoiceContent = array("MediaId"=>$object->MediaId);
        $VoiceRequest = $this->transmitVoice($object,$VoiceContent);
        return $VoiceRequest;

    }


    /*
     *  回复语音消息
     *  @param  $object
     *  @param  $voiceArray  Array
     * */
    private function transmitVoice($object,$voiceArray)
    {
        $voiceTpl   =   "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[voice]]></MsgType>
                            <Voice>
                                <MediaId><![CDATA[%s]]></MediaId>
                            </Voice>
                         </xml>";

        $resultStr = sprintf($voiceTpl,$object->FromUserName,$object->ToUserName,time(),$voiceArray['MediaId']);
        return $resultStr;

    }


    /*
     *  回复音乐消息
     * */
    private function transmitMusic($object,$MusicArray)
    {
        $MusicmTpl    =   "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        <Music>
                            <Title><![CDATA[%s]]></Title>
                            <Description><![CDATA[%s]]></Description>
                            <MusicUrl><![CDATA[%s]]></MusicUrl>
                            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        </Music>
                        </xml>";
        $resultStr = sprintf($MusicmTpl,$object->FromUserName,$object->ToUserName,time(),$MusicArray['Title'],$MusicArray['Description'],$MusicArray['MusicUrl'],$MusicArray['HQMusicUrl']);
        return $resultStr;

    }


    /*
     *  封装CURL，https请求  GET/POST
     *  @param  $url 请求的地址
     *  @param  $data  POST是需要想服务器提交数据的
     *  例子：$data = array("filename"=>"@img/ecshso.png");
     * */
    public function http_request($url,$data = null)
    {
        //  初始化CURL
        $ch = curl_init();

        //  设置传输选项
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);// 将页面以文件流的的形式保存，因为并非获取URL地址中的所有数据，只获取部分数据
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);

        //  由于POST是主要用于向服务器提交数据，所以需要用到以下参数配置
        if(!empty($data)){
            curl_setopt($ch,CURLOPT_POST,1);//  模拟POST请求向服务器提交数据
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//  POST提交的内容
        }

        //  执行并获取结果
        $outopt = curl_exec($ch);
        //  关闭CURL
        curl_close($ch);
        return json_decode($outopt,true);//餐数2将JSON数据变成数组返回
    }


    /*
     *  设置 自定义菜单
     *  bug: 设置自定义菜单 使用的是 POST
     *  若后期需要记录设置 [自定义菜单] 返回的错误时，可以使用 return 返回记录到log或者是添加错误记录到MYsql
     * */
    public function set_menu($accessToken,$jsonArr)
    {
        $menuURL = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
        return $result = $this->http_request($menuURL,$jsonArr);
    }


    /*
     *  查询当前菜单  GET
     *  @param $url 查询菜单的URL地址
     *  return 当前菜单的JSON格式数据
     * */
    public function return_setmenu_json ($accessToken)
    {
        $menuJson_URL = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$accessToken;
        return $result = $this->http_request($menuJson_URL);

    }


    /*
     *  删除[自定义菜单]
     * */
    public function del_setmenu($accessToken)
    {
        $DelMenuURL = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$accessToken;
        return $result = $this->http_request($DelMenuURL);
    }

    /*
     * 使用读写文件的方式获取 access_token
     * */
    public function file_ReturnAccessToken()
    {

        // 文件读取json数据
        $data = json_decode(@file_get_contents("_access_token.json"),true);

        if($data['expires_time'] < time())
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $AccessTokenResult = $this->http_request($url);
            $access_token = $AccessTokenResult['access_token'];

            // 将access_token缓存于文件中
            $data['expires_time'] = time() + 7000;
            $data['access_token'] = $access_token;

            $fp = @fopen("_access_token.json","w");
            @fwrite($fp,json_encode($data));
            @fclose($fp);

        }
        else{
            $access_token = $data['access_token'];
        }

        return $access_token;

    }




    /*
     *  获取  Access_token 借口调用凭证【使用memcache】
     *  获取之前，判断 SAE memcache 是否有 access_token的值，若有，则返回，若过期，则重新获取
     *  bug : 获取 Access_token 使用的是   GET
     *
    public function Mmc_ReturnAccessToken()
    {
        // 这里注意调用 appid 和 appsecret 使用$this-> 的要点

        $cache_access_token = $this->_memcache_get("access_token");

        if(!$cache_access_token){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $access_token = $this->http_request($url);
            $this->_memcache_set("access_token",$access_token['access_token'],6000);
            return $access_token['access_token'];
        }

        return $cache_access_token;
    }
    */

    /*
     *  实例化SAE  memcache
     *  具体使用信息，参照
     *  https://www.sinacloud.com/doc/sae/php/memcache.html#api-shi-yong-shou-ce
     *
    public function _memcache_init(){
        //  实例化SAE对象
        //$mmc = new Memcache();

        //  使用当前应用的memcache
        //$mmc ->connect();

        //return $mmc;
    }
    */

    /*
     *  设置 SAE memcache
     *  @param  $key    设置memcache值的名称
     *  @param  $value  设置memcache的值
     *  @param  $time   设置key的有效事件：0=永久有效  s
     *
    public function _memcache_set($key,$value,$time = 0){
        //$mmc = $this->_memcache_init();
        //$mmc->set($key,$value,0,$time);
    }
	*/

    /*
     *  获取 SAE memcache
     *  @param  $key    设置需要获取memcache的值的名称
     *
    public function _memcache_get($key){
        $mmc = $this->_memcache_init();
        return $mmc->get($key);
    }
	*/


    /*
     * 微信网页授权[base型授权]
     * */
    public function snsapi_base($redirect_uri,$state_data)
    {
        /*
         *  1.准备Scope为snsapi_base的网页授权页面 URL 地址
         * */
        $redirect_uri = urlencode($redirect_uri);
        $snsapi_base_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state_data}#wechat_redirect";

        /*
         * 2.引导用户点击url,根据是否成功跳转到回调链接地址，
         * 成功，返回code,判断code是否成功返回
         * 失败，继续回调跳转snsapi_base_url地址
         * */
        if(!isset($_GET['code']))
        {
            header("Location:{$snsapi_base_url}");
        }

        $base_code = $_GET['code'];
        /*
         * 成功，通过code换取网页授权access_token
         * 通过curl $GET方式请求
         * */
        $BaseWebAccessToken_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$base_code}&grant_type=authorization_code";
        $data = $this->http_request($BaseWebAccessToken_url);
        $data['state'] = $state_data;
        return $data;

    }

    /*
      * 微信网页授权[base型授权]
      * */
    public function snsapi_userinfo($redirect_uri,$state_data)
    {
        /*
         *  1.准备Scope为snsapi_userinfo的网页授权页面 URL 地址
         * */
        $redirect_uri = urlencode($redirect_uri);
        $snsapi_userinfo_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state_data}#wechat_redirect";

        /*
         * 2.用户手动授权,根据是否成功跳转到回调链接地址，
         * 成功，返回code,判断code是否成功返回
         * 失败，继续回调跳转snsapi_base_url地址
         * */
        if(!isset($_GET['code']))
        {
            header("Location:{$snsapi_userinfo_url}");
        }

        $userInfo_code = $_GET['code'];
        /*
         * 成功，通过code换取网页授权access_token
         * 通过curl $GET方式请求
         * */
        $UserInfoWebAccessToken_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$userInfo_code}&grant_type=authorization_code";
        $UserInfoResult =  $this->http_request($UserInfoWebAccessToken_url);

        $web_accessToken = $UserInfoResult['access_token'];
        $web_openId = $UserInfoResult['openid'];

        /*
         * 根据第三步获取的access_token和openid拉取用户信息
         * */
        $userinfo_url ="https://api.weixin.qq.com/sns/userinfo?access_token={$web_accessToken}&openid={$web_openId}&lang=zh_CN";
        $data =  $this->http_request($userinfo_url);
        $data['state'] = $state_data;
        return $data;
    }


    public function getImage($url,$save_dir='',$filename='',$type=1){
        //if(trim($url)==''){
        //    return array('file_name'=>'','save_path'=>'','error'=>1);
        //}
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            if($ext!='.gif'&&$ext!='.jpg'){
                return array('file_name'=>'','save_path'=>'','error'=>3);
            }
            $filename=time().$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }

    /**
     * CURL下载文件 成功返回文件名，失败返回false
     * @param $url
     * @param string $savePath
     * @return bool|string
     * @author Zou Yiliang
     */
    public function downFile($url, $savePath = './uploads')
    {
        //$url = 'http://www.baidu.com/img/bdlogo.png';
        /*
        HTTP/1.1 200 OK
        Connection: close
        Content-Type: image/jpeg
        Content-disposition: attachment; filename="cK4q4fLsp7YOlaqxluDOafB.jpg"
        Date: Sun, 18 Jan 2015 16:56:32 GMT
        Cache-Control: no-cache, must-revalidate
        Content-Length: 963704
        */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HEADER, TRUE);    //需要response header
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);    //需要response body

        $response = curl_exec($ch);

        //分离header与body
        $header = '';
        $body = '';
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE); //头信息size
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
        }

        curl_close($ch);

        //文件名
        $arr = array();
        if (preg_match('/filename="(.*?)"/', $header, $arr)) {

            $file = date('Ym') . '/' . $arr[1];
            $fullName = rtrim($savePath, '/') . '/' . $file;

            //创建目录并设置权限
            $basePath = dirname($fullName);
            if (!file_exists($basePath)) {
                @mkdir($basePath, 0777, true);
                @chmod($basePath, 0777);
            }

            if (file_put_contents($fullName, $body)) {
                return $file;
            }
        }

        return false;
    }



}//class
 
?>