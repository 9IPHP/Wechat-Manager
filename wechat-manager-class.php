<?php
/**
 * 微信(或易信)公共平台处理类
 *
 * 用于创建微信(或易信)公共平台服务
 *
 * @author: Specs
 * Email: specs@9iphp.com
 * Blog: http://www.9iphp.com
 */
class WeChat{

	private $token = "";  //TOKEN值
	private $callback_function = NULL; //回调函数名称
	private $articles = array(); //图文信息array

	public $debug = false;  //是否调试状态
	public $fromUser = "";  //当前消息的发送者
	public $toUser = "";    //当前消息的接收者

	/**
	 * 构造函数
	 * @param $token 设置在公共平台的TOKEN值
	 * @param $callback_function_name 回调函数名称
	 * @return 新生成的WeChat类实例
	 */
	function WeChat($token, $callback_function_name) {
		$this->token = $token;
		$this->callback_function = $callback_function_name;
	}

	/**
	 * 检查签名是否正确
	 * @return boolean 正确返回true,否则返回false
	 */
	private function checkSignature()	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$token = $this->token;
		//$fp=fopen("log.txt","w+");
		//$strText="TOKEN".$token."\r\n";
		//fwrite($fp,$strText);
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );


		if( $tmpStr == $signature )
			return true;
		else
			return false;
	}

	/**
	 * 验证签名是否有效
	 */
	protected function valid() {
		$echoStr = $_GET["echostr"];
		//valid signature , option
		if($this->checkSignature()) {
			echo $echoStr;
			exit;
		} else {
			echo 'error signature';
		}
	}

	/**
	 * 处理来自微信服务器的消息
	 */
	public function process() {
		//如果是验证请求,则执行签名验证并退出
		if (!empty($_GET["echostr"])) {
			$this->valid(); //验证签名是否有效
			return;		    //返回退出
		}

		//如果不是验证请求，则
		//首先，取得POST原始数据(XML格式)
		//$postData = $GLOBALS["HTTP_RAW_POST_DATA"];
                $postData = file_get_contents('php://input');
		if (empty($postData)) {echo '';return;}  //如果没有POST数据，则退出

		//如果验证签名不通过则退出 (debug状态下不验证)
		/*if (!$debug)
			if (!$this->checkSignature()) return;*/

		//测试：将POST数据存盘(存在当前脚本目录的子目录data中)
		if ($this->debug) $this->saveDataToFile('data/',$postData);

		//解析POST数据(XML格式)
		$object = simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);
		$messgeType = trim($object->MsgType);    //取得消息类型
		$this->fromUser = "".$object->FromUserName; //记录消息发送方(不是发送者的微信号，而是一个加密后的OpenID)
		$this->toUser = "".$object->ToUserName;     //记录消息接收方(就是公共平台的OpenID)

		//如果回调函数没有设置，则退出
		if (!is_callable($this->callback_function)) return;

		//根据不同的消息类型，分别处理
		switch($messgeType)
		{
			case "text":   //文本消息
				//调用回调函数
				call_user_func($this->callback_function, $this, "text", $object->Content, "", "");
				break;
			case "image":  //图片消息
				call_user_func($this->callback_function, $this, "image", $object->PicUrl, "", "");
				break;
			case "music":  //音乐消息(限于易信平台, 微信平台没有这种消息)
				call_user_func($this->callback_function, $this, "music", $object->url, $object->name, $object->desc);
				break;
			case "audio":  //音频消息(限于易信平台, 微信平台对应消息是voice)
				call_user_func($this->callback_function, $this, "audio", $object->url, $object->name, $object->mimeType);
				break;
			case "vioce":  //音频消息(限于微信平台, 易信平台对应消息是audio)
				call_user_func($this->callback_function, $this, "voice", $object->url, $object->MediaId, $object->Recognition);
				break;
			case "video":  //视频消息
				call_user_func($this->callback_function, $this, "video", $object->url, $object->name, $object->mimeType);
				break;
			case "location": //定位信息
				call_user_func($this->callback_function, $this, "location", $object->Label, $object->Location_X, $object->Location_Y);
				break;
			case "link":  //链接信息(限于微信平台, 易信平台无对应消息)
				call_user_func($this->callback_function, $this, "link", $object->Url, $object->Title, $object->Description);
				break;
			case "event":  //事件
				switch ($object->Event)
				{
					case "subscribe":   //订阅事件
						call_user_func($this->callback_function, $this, "subscribe", $object->FromUserName, "", "");
						break;
					case "unsubscribe": //取消订阅事件
						call_user_func($this->callback_function, $this, "unsubscribe", $object->FromUserName, "", "");
						break;
					case "CLICK":      //菜单点击事件
						call_user_func($this->callback_function, $this, "text", $object->EventKey, "", "");
						break;
					default :
						//Unknow Event
						break;
				}
				break;
			default:
				//Unknow msg type
				break;
		}
	}

	/**
	 * 形成 文本消息响应值
	 * @param unknown_type $toUser
	 * @param unknown_type $fromUser
	 * @param unknown_type $content
	 * @param unknown_type $flag
	 * @return string
	 */
	protected function textResponse($toUser, $fromUser, $content, $flag=0)	{
		$xmlTemplate = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
		$xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time(), $content, $flag);
		return $xmlText;
	}

	/**
	 * 形成 音乐消息响应值
	 * @param unknown_type $toUser
	 * @param unknown_type $fromUser
	 * @param unknown_type $title
	 * @param unknown_type $description
	 * @param unknown_type $url
	 * @param unknown_type $hq_url
	 * @return string
	 */
	protected function musicResponse($toUser, $fromUser, $title, $description, $url, $hq_url="") {
		$xmlTemplate = "<xml>
                     <ToUserName><![CDATA[%s]]></ToUserName>
                     <FromUserName><![CDATA[%s]]></FromUserName>
                     <CreateTime>%s</CreateTime>
                     <MsgType><![CDATA[music]]></MsgType>
                     <Music> <Title><![CDATA[%s]]></Title>
                     <Description><![CDATA[%s]]></Description>
                     <MusicUrl><![CDATA[%s]]></MusicUrl>
                     <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                     </Music>
                     </xml>";
		if (empty($hq_url)) $hq_url=$url;
		$xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time(),
				           $title, $description, $url, $hq_url);
		return $xmlText;
	}

	/**
	 * 形成 图文消息响应值
	 * @param unknown_type $toUser
	 * @param unknown_type $fromUser
	 * @param $articles 一个array，每个元素保存一条图文信息；每个元素也是一个array, 有Title,Description,PicUrl,Url四个键值
	 * @return string
	 */
	protected function newsResponse($toUser, $fromUser, $articles) {
		$xmlTemplate = "<xml>
    			    <ToUserName><![CDATA[%s]]></ToUserName>
    			    <FromUserName><![CDATA[%s]]></FromUserName>
    			    <CreateTime>%s</CreateTime>
    			    <MsgType><![CDATA[news]]></MsgType>
    			    ";
		$xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time());
		$xmlText .= '<ArticleCount>'. count($articles) .'</ArticleCount>';
		$xmlText .= '<Articles>';
		foreach($articles as  $article) {
			$xmlText .= '<item>';
			$xmlText .= '<Title><![CDATA[' . $article['Title'] . ']]></Title>';
			$xmlText .= '<Description><![CDATA[' . $article['Description'] . ']]></Description>';
			$xmlText .= '<PicUrl><![CDATA[' . $article['PicUrl'] . ']]></PicUrl>';
			$xmlText .= '<Url><![CDATA[' . $article['Url'] . ']]></Url>';
			$xmlText .= '</item>';
		}
		$xmlText .= '</Articles> </xml>';
		return $xmlText;
	}

	/**
	 * 形成 图片消息的响应值
	 * @param unknown_type $toUser
	 * @param unknown_type $fromUser
	 * @param unknown_type $url
	 * @return string
	 */
	protected function imageResponse($toUser, $fromUser, $url)	{
		$xmlTemplate = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    </xml>";
		$xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time(), $url);
		return $xmlText;
	}

	/**
	 * 形成 音频消息响应值
	 * @param unknown_type $toUser
	 * @param unknown_type $fromUser
	 * @param unknown_type $url
	 * @param unknown_type $name
	 * @param unknown_type $mimeType
	 * @return string
	 */
	protected function audioResponse($toUser, $fromUser, $url, $name, $mimeType='audio/aac') {
		$xmlTemplate = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[audio]]></MsgType>
                    <url><![CDATA[%s]]></url>
                    <name><![CDATA[%s]]></name>
                    <mimeType><![CDATA[%s]]></mimeType>
                    </xml>";
		$xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time(), $url, $name, $mimeType);
		return $xmlText;
	}

	/**
	 * 形成 视频消息响应值
	 * @param unknown_type $toUser
	 * @param unknown_type $fromUser
	 * @param unknown_type $url
	 * @param unknown_type $name
	 * @param unknown_type $mimeType
	 * @return string
	 */
	protected function videoResponse($toUser, $fromUser, $url, $name, $mimeType='video/mp4') {
		$xmlTemplate = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    <url><![CDATA[%s]]></url>
    			    <name><![CDATA[%s]]></name>
    			    <mimeType><![CDATA[%s]]></mimeType>
                    </xml>";
		$xmlText = sprintf($xmlTemplate, $toUser, $fromUser, time(), $url, $name, $mimeType);
		return $xmlText;
	}


	/**
	 * 发送文本内容
	 * @param $content 文本内容
	 */
	public function sendText($content) {
		echo $this->textResponse($this->fromUser, $this->toUser, $content);
	}


	/**
	 * 添加一条图文信息
	 * @param $title  标题
	 * @param $description 内容
	 * @param $url  网页链接URL
	 * @param $pictureUrl 图片的URL
	 */
	public function addNews($title, $description, $url, $pictureUrl) {
		$article = array('Title' => $title,
				'Description' => $description,
				'PicUrl' => $pictureUrl,
				'Url'=>$url);
		$this->articles[] = $article;
	}

	/**
	 * 发送图文信息
	 * 用法：首先用addNews()函数一条一条地添加图文信息，添加完成后用本函数发送
	 */
	public function sendNews() {
		echo $this->newsResponse($this->fromUser, $this->toUser, $this->articles);
	}

	/**
	 * 发送图片信息 (目前微信公共平台对普通订阅号不开放该类发送功能)
	 * @param $url 图片URL
	 */
	public function sendImage($url) {
		echo $this->imageResponse($this->fromUser, $this->toUser, $url);
	}

	/**
	 * 发送音乐信息 (目前微信公共平台对普通订阅号不开放该类发送功能)
	 * @param $url 音乐URL
	 * @param $title 标题
	 * @param $description 描述
	 * @param $hq_url 高质量音乐的URL
	 */
	public function sendMusic($url, $title='', $description='', $hq_url='') {
		echo $this->musicResponse($this->fromUser, $this->toUser, $title, $description, $url, $hq_url="");
	}

	/**
	 * 发送音频信息 (目前微信公共平台对普通订阅号不开放该类发送功能)
	 * @param $url 音频URL
	 * @param $name 音频文件名称
	 * @param $mimeType 音频文件的mime类型
	 */
	public function sendAudio($url, $name='', $mimeType='audio/aac') {
		echo $this->audioResponse($this->fromUser, $this->toUser, $url, $name, $mimeType);
	}

	/**
	 * 发送视频信息 (目前微信公共平台对普通订阅号不开放该类发送功能)
	 * @param $url 视频URL
	 * @param $name 视频文件名称
	 * @param $mimeType 视频文件的mime类型
	 */
	public function sendVideo($url, $name='', $mimeType='video/mp4') {
		echo $this->videoResponse($this->fromUser, $this->toUser, $url, $name, $mimeType);
	}

	/**
	 * 内部用函数：将数据存为文件(文件名自动编号)
	 * @param  $path 存盘目录
	 * @param  $data 要保存的数据
	 */
	private function saveDataToFile($path,$data) {
		$fnum = 1;
		$filename = "data".$fnum;
		while (file_exists($path.$filename)) {
			$fnum++;
			$filename = "data".$fnum;
		}
		$handle = fopen($path.$filename, "w");
		fwrite($handle,$data);
		fclose($handle);
	}

}
?>
