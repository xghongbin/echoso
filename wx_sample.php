<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "echoso");
define("APPID", "wx0b0b9449118256f3");
define("APPSECRET", "28efc088e60a03ecea09990183932fd8");
define("SETTYPE",'1');

$wechatObj = new wechatCallbackapiTest(APPID,APPSECRET);





/*
 *  获取 AccessToken ,判断时间是否已经超过 7200秒
 *  获取 access_token 有三种方法：
 *  1. memcache 获取缓存   （SAE）
 *  2。数据库存表数据获取
 *  3. 文件读写操作
 * SETTYPE  为1使用SAE Memcache
 *          为2使用数据库
 *          为3使用文件读写操作（SAE不支持此操作方案）
 * */
    if(SETTYPE == '1')
    {
        $accessToken = $wechatObj->return_AccessToken();
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


//  先进行 token 的认证，再进行一下代码的运行
if(isset($_GET['echostr'])){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    /*
     *  成员属性
     * */
    private $appid;
    private $appsecret;

    public function __construct($appid = "",$appsecret = ""){
        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
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
		if (!defined("TOKEN")) {
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
		if( $tmpStr == $signature ){
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

    /*  设置消息类型
     *  根据类型，判断返回特定消息
     * */

	/*
	 * 	接收文本消息
     *  @param  $object
	 * */
	private function receiveText($object){
        $keyword = trim($object->Content);

        if($keyword == "echoso" ){
            //  先发送感谢消息
             $content = "多谢关注 EchoSo";
             $request = $this->transmitText($object,$content);

            //  拼接echoso二维码
            //  $echoSoPic = array(
            //   "PicUrl"=>"http://1.echosotoo.applinzi.com/img/echoso.png"
            //    );

            //$ImageRequest = $this->transmitImage($object,$echoSoPic);
            //$request .= $ImageRequest;
        }
        else if($keyword == "单图文"){
            $content = array();
            $content[] = array(
                "Title"=>"单图文标题",
                "Description"=>"单图文内容",
                "PicUrl"=>"http://1.echosotoo.applinzi.com/img/test.png",
                "Url"=>"www.baidu.com"
            );
            $request = $this->transmitNews($object,$content);

        }
        else if($keyword == "多图文"){
            $content = array();
            $content[] = array(
                "Title"=>"多图文1标题",
                "Description"=>"多图文1内容",
                "PicUrl"=>"http://1.echosotoo.applinzi.com/img/test.png",
                "Url"=>"www.baidu.com"
            );
            $content[] = array(
                "Title"=>"多图文2标题",
                "Description"=>"多图文2内容",
                "PicUrl"=>"http://1.echosotoo.applinzi.com/img/test.png",
                "Url"=>"www.sina.com"
            );
            $request = $this->transmitNews($object,$content);
        }
        else if($keyword == "音乐"){
            $musicContent = array(
                "Title"=>"我是天秤座",
                "Description"=>"歌手:徐梦园",
                "MusicUrl"=>"http://1.echosotoo.applinzi.com/music/tczzg.mp3",
                "HQMusicUrl"=>"http://1.echosotoo.applinzi.com/music/tczzg.mp3"
            );

            $request = $this->transmitMusic($object,$musicContent);
        }
        else{
            $content = "您发送的是文本，内容为:".$keyword;
            $request = $this->transmitText($object,$content);
        }
        return $request;
	}

	/*
	 * 	接收图片消息
     *  @param  $object
	 * */
	private function receiveImage($object){
        $ImageContent = array("MediaId"=>$object->MediaId);
        $ImageRequest = $this->transmitImage($object,$ImageContent);
        return $ImageRequest;

	}

	/*
	 * 	接收语音消息
	 *  接受语音时，判断是否开启语音识别
     *  @param  $object
	 * */
	private function receiveVoice($object){

        if(isset($object->Recognition) && !empty($object->Recognition)){
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
	 * 	接收视频消息
     *  @param  $object
	 * */
	private function receiveVideo($object){
        $VideoContent = array("MediaId"=>$object->MediaId,"ThumbMediaId"=>$object->ThumbMediaId,"Title"=>"您的视频","Description"=>"视频内容描述");
        $VideoRequest = $this->transmitVideo($object,$VideoContent);
        return $VideoRequest;

	}

	/*
	 * 	接收位置消息
     *  @param  $object
	 * */
	private function receiveLocation($object){
        $LocationContent = "您发送的是位置，纬度为".$object->Location_X.";经度为:".$object->Location_Y.";缩放级别为：".$object->Scale.";位置描述:".$object->Label;
        /*
         *  此处返回不了地理位置消息，以后再做打算，暂定返回文本消息
         * $LocationContent = array(
              "Location_X"=>$object->Location_X,
              "Location_Y"=>$object->Location_Y,
              "Scale"      =>$object->Scale,
              "Label"      =>$object->Label,
              "MsgId"    =>$object->MsgId
        );*/
        //$LocationContent = "id:".$object->MsgId;
        $LocationRequest = $this->transmitText($object,$LocationContent);
        //$LocationRequest = $this->transmitLocation($object,$LocationContent);
        return $LocationRequest;

	}

	/*
	 * 	接收链接消息
     *  @param  $object
	 * */
	private function receiveLink($object){
        $LinkContent = "您发送的是链接，纬度为".$object->Title.";内容为:".$object->Description.";链接地址为：".$object->Url;
        $LinkRequest = $this->transmitLink($object,$LinkContent);
        return $LinkRequest;

	}

    /*
     *  接收事件消息
     *  @param  $object
     * */
    private function receiveEvent($object){

        $eventContent = "";
        //判断事件类型
        switch ($object->Event){
            case "subscribe"://订阅
                $eventContent = "欢迎关注Echoso，您可以发送以下文字，获取测试\n 'ecsho'\n '音乐'\n '单图文'\n '多图文'";
                break;
            case "unsubscribe"://取消订阅
                $eventContent = "";
                break;
            case "CLICK":
                switch ($object->EventKey){
                    case "ABOUT":
                        $eventContent = array();
                        $eventContent[] = array(
                            "Title"=>"EchoSo",
                            "Description"=>"欢迎关注EchoSo",
                            "PicUrl"=>"http://1.echosotoo.applinzi.com/img/echoso.png",
                            "Url"=>"www.echoso.com"
                        );
                        break;
                    default:
                        $eventContent = "单击菜单:".$object->EventKey;
                        break;
                }
                break;
            case "view":
                $eventContent = "跳转链接:".$object->url;
                break;
            case "scan":
                $eventContent = "扫描场景:".$object->EventKey;
                break;
            case "location":
                $eventContent = "上传位置: ".$object->SendLocationInfo->Location_X.";经度".$object->SendLocationInfo->Location_Y."; 发送的位置信息".$object->SendLocationInfo->Label;
                break;
            case "scancode_waitmsg":
                $eventContent = "扫码带提示：类型".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
                break;
            case "scancode_push":
                $eventContent = "扫码推事件:";
                break;
            default:
                $eventContent = "know't type";
                break;

        }
        //echo $eventContent;
        if(is_array($eventContent)){
            if(isset($eventContent[0]['PicUrl'])){
                $EventRequest = $this->transmitNews($object,$eventContent);
            }else if(isset($eventContent['MusicUrl'])){
                $musicContent = array(
                    "Title"=>"我是天秤座",
                    "Description"=>"歌手:徐梦园",
                    "MusicUrl"=>"http://1.echosotoo.applinzi.com/music/tczzg.mp3",
                    "HQMusicUrl"=>"http://1.echosotoo.applinzi.com/music/tczzg.mp3"
                );
                $EventRequest = $this->transmitMusic($object,$musicContent);
            }
        }else{
            $EventRequest = $this->transmitText($object,$eventContent);
        }
        return $EventRequest;

    }


    /*
     *  回复文本消息
     *  @param  $object
     *  @param  $content  string
     **/
    private function transmitText($object,$content){
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
     *  回复图片消息
     *  @param  $object
     *  @param  $imageArray  Array
     **/
    private function transmitImage($object,$imageArray){
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
    private function transmitNews($object,$arr_item){

        //  参数预先判断
        if(!is_array($arr_item)) return;

        $item_str   =   "";

        //  多图文需要遍历循环
        foreach ($arr_item as $item){
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
     *  回复语音消息
     *  @param  $object
     *  @param  $voiceArray  Array
     * */
    private function transmitVoice($object,$voiceArray){
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
     *  回复视频消息
     *  @param  $object
     *  @param  $VideoArray  Array
     * */
    private function transmitVideo($object,$VideoArray){

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
     *  回复地理位置消息
     *  @param  $object
     *  @param  $LocationArray  Array
     * */
    private  function transmitLocation($object,$LocationArray){
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
     *  回复音乐消息
     * */
    private function transmitMusic($object,$MusicArray){
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
    public function http_request($url,$data = null){
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
        return json_decode($outopt,true);//将JSON数据变成数组返回
    }


    /*
     *  获取  Access_token
     *  获取之前，判断SAE memcache是否有access_token的值，若有，则返回，若过期，则重新获取
     *  bug : 获取 Access_token 使用的是   GET
     * */
    public function return_AccessToken(){
        //$cache_access_token = $this->_memcache_get("access_token");
        $cache_access_token = "";

        if(!$cache_access_token){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $access_token = $this->http_request($url);
            //$this->_memcache_set("access_token",$access_token['access_token'],6000);
            return $access_token['access_token'];
        }

        return $cache_access_token;
    }


    /*
     *  设置 自定义菜单
     *  bug: 设置自定义菜单 使用的是 POST
     *  若后期需要记录设置 [自定义菜单] 返回的错误时，可以使用 return 返回记录到log或者是添加错误记录到MYsql
     * */
    public function set_men($accessToken,$jsonArr){
        $menuURL = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
        return $result = $this->http_request($menuURL,$jsonArr);
    }

    /*
     *  查询当前菜单  GET
     *  @param $url 查询菜单的URL地址
     *  return 当前菜单的JSON格式数据
     * */
    public function return_setmenu_json ($accessToken){
        $menuJson_URL = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$accessToken;
        return $result = $this->http_request($menuJson_URL);

    }

    /*
     *  删除[自定义菜单]
     * */
    public function del_setmenu($accessToken){
        $DelMenuURL = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$accessToken;
        return $result = $this->http_request($DelMenuURL);
    }

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



}

?>






