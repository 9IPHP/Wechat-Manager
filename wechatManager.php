<?php
/*
Plugin Name: Wechat Manager 微信公众平台管家
Plugin URI: http://9iphp.com/opensystem/wordpress/1043.html
Description: 通过简单配置，可在微信中获得博客的最新文章、热点文章，实现搜索功能，并且添加了天气查询与英汉互译两个小工具，后续会根据需求添加更多小工具
Version: 1.0
Author: Specs
Author URI: http://9iphp.com
*/
define("WM_TOKEN","wm_token");
define("WM_WELCOME","wm_welcome");
define("WM_THUMB","wm_thumb");
define("WM_BDAK","wm_bdak");
define("WM_TRANSLATE_APPID","wm_translate_appid");
define("WM_TRANSLATE_KEY","wm_translate_key");
define("WM_POST_NUMS","wm_post_nums");
//define("WM_ERROR_MESSAGE","wm_error_message");
//define("WM_MESSAGE","wm_message");

$wm_token = get_option(WM_TOKEN);
$wm_welcome = get_option(WM_WELCOME);
$wm_thumb = get_option(WM_THUMB);
$wm_bdak = get_option(WM_BDAK);
$wm_translate_appid = get_option(WM_TRANSLATE_APPID);
$wm_translate_key = get_option(WM_TRANSLATE_KEY);
$wm_post_nums = get_option(WM_POST_NUMS,5);
define("TOKEN",$wm_token);  //TOKEN值

$siteurl = get_option('siteurl');
define('WECHAT_MANAGER_FOLDER', dirname(plugin_basename(__FILE__)));
define('WECHAT_MANAGER_STATIC', $siteurl.'/wp-content/plugins/' . WECHAT_MANAGER_FOLDER);
define('WECHAT_MANAGER_PLUGIN_URL', plugins_url('', __FILE__));
define('WECHAT_MANAGER_PLUGIN_DIR', WP_PLUGIN_DIR.'/'. dirname(plugin_basename(__FILE__)));
define('WECHAT_MANAGER_PLUGIN_FILE',  __FILE__);

define("WM_ERROR_MESSAGE","没有相关选项，你可以回复以下内容进行选择：");
define("WM_MESSAGE","回复括号内文字选择项目：\r\n[h] 热点文章\r\n[n] 最新文章\r\n[r] 随机文章\r\n[搜索@关键词] 根据关键词搜索文章\r\n[翻译@词语]英汉互译\r\n[天气@城市名] 天气查询\r\n[help或?或？] 返回帮助信息\n\n更多精彩，即将亮相，敬请期待");

include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-class.php');				// 微信类库
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-functions.php');			//函数库
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-hook.php');			//
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-user.php');				// 用户类库
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-option.php');				// 用户类库

add_action('pre_get_posts', 'wm_preprocess', 4);
function wm_preprocess($wp_query){
	global $object;
	if(!isset($object)){
		//创建一个WeChat类的实例, 回调函数名称为"onMessage",即消息处理函数
		$object = new WeChat(TOKEN, "onMessage");
		$object->process();  //处理消息
		return;
	}
}


//消息处理函数
function onMessage(WeChat $object, $messageType, $content, $arg1, $arg2) {
	$user = new User($object->fromUser);  //创建一个用户
	//处理subscribe和unsubscribe消息
	switch ($messageType) {
		case "subscribe":   //当用户关注
			global $wm_welcome;
			$object->addNews($wm_welcome,"","","");
			$object->addNews(WM_MESSAGE,"","","");
			$object->sendNews();
			break;
		case "unsubscribe": //当用户取消关注
			$user->delete();
			break;
		case "text":
			$keyword = strtolower(trim($content));
			switch($keyword){
				case "n":
					$articles = query("n");
					foreach($articles as $v){
						$object->addNews($v['0'],$v['1'],$v['2'],$v['3']);
					}
					$object->sendNews();
					break;
				case "h":
					$articles = query("h");
					foreach($articles as $v){
						$object->addNews($v['0'],$v['1'],$v['2'],$v['3']);
					}
					$object->sendNews();
					break;
				case "r":
					$articles = query("r");
					foreach($articles as $v){
						$object->addNews($v['0'],$v['1'],$v['2'],$v['3']);
					}
					$object->sendNews();
					break;
				case "help":
				case "?":
				case "？":
					global $wm_welcome;
					$object->addNews($wm_welcome,"","","");
					$object->addNews(WM_MESSAGE,"","","");
					$object->sendNews();
					break;
				default:
					switchFunc($object, $keyword);
					break;
			}
			break;
		default:
			$object->sendText("暂无设置此功能，你可以到我们的<a href='http://wx.wsq.qq.com/177325859'>微社区</a>提交相关问题"); //否则，显示出错信息
	}
}

function switchFunc(WeChat $object, $keyword){

	$matches = explode("@", $keyword);
	$key = $matches[0];
	$value = $matches[1];
	switch($key){
		case "翻译":
			$content = wm_translate($value);
			$object->sendText($content);
			break;
		case "天气":
			$content = wm_weather($value);
			if(!is_array($content)){
				$object->sendText($content);
			}else{
				foreach($content as $v){
					$object->addNews($v['title'],"","",$v['pic']);
				}
				$object->sendNews();
			}
			break;
		case "搜索":
			$articles = query("", $value);
			if(empty($articles)){
				$object->sendText("没有找到相关文章");
			}else{
				foreach($articles as $v){
					$object->addNews($v['0'],$v['1'],$v['2'],$v['3']);
				}
				$object->sendNews();
			}
			break;
		default:
			$object->addNews(WM_ERROR_MESSAGE,"","","");
			$object->addNews(WM_MESSAGE,"","","");
			$object->sendNews();
			break;
	}
}

?>