<?php
/*
Plugin Name: Wechat Manager 微信公众平台管家
Plugin URI: http://9iphp.com/opensystem/wordpress/1043.html
Description: 通过简单配置，可在微信中获得博客的最新文章、热点文章，实现搜索功能，并且添加了天气查询与英汉互译两个小工具，后续会根据需求添加更多小工具
Version: 2.1
Author: Specs
Author URI: http://9iphp.com
*/

$siteurl = get_option('siteurl');
define('WECHAT_MANAGER_FOLDER', dirname(plugin_basename(__FILE__)));
define('WECHAT_MANAGER_STATIC', $siteurl.'/wp-content/plugins/' . WECHAT_MANAGER_FOLDER);
define('WECHAT_MANAGER_PLUGIN_URL', plugins_url('', __FILE__));
define('WECHAT_MANAGER_PLUGIN_DIR', WP_PLUGIN_DIR.'/'. dirname(plugin_basename(__FILE__)));
define('WECHAT_MANAGER_PLUGIN_FILE',  __FILE__);

include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-class.php');             // 微信类库
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-functions.php');         //函数库
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-hook.php');          //
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-user.php');
include(WECHAT_MANAGER_PLUGIN_DIR.'/wechat-manager-option.php');

define("TOKEN",wm_get_setting('token'));  //TOKEN值
define("POSTNUM",wm_get_setting('post_num'));  //TOKEN值
$wm_thumb = wm_get_setting('thumb');
$wm_bdak = wm_get_setting('bd_key');
$wm_translate_appid = wm_get_setting('trans_appid');
$wm_translate_key = wm_get_setting('trans_key');

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
            $object->addNews('感谢关注',"","","");
            $object->addNews(wm_get_setting('welcome'),"","","");
            $object->sendNews();
            break;
        case "unsubscribe": //当用户取消关注
            $user->delete();
            break;
        case "text":
            $keyword = strtolower(trim($content));

            switch($keyword){
                case wm_get_setting('post_new'):
                    send_post($object, 'n');
                    break;
                case wm_get_setting('post_comment_week'):
                    send_post($object, 'c7');
                    break;
                case wm_get_setting('post_comment_month'):
                    send_post($object, 'c30');
                    break;
                case wm_get_setting('post_comment_year'):
                    send_post($object, 'c365');
                    break;
                case wm_get_setting('post_comment_all'):
                    send_post($object, 'c');
                    break;
                case wm_get_setting('post_rand'):
                    send_post($object, 'r');
                    break;
                case wm_get_setting('card'):
                    $object->addNews('微信贺卡',"定制自己的贺卡发送给亲朋好友\n\n点击图文开始制作吧!","http://9iphp.com/card/",WECHAT_MANAGER_STATIC."/include/weixin-card.jpg");
                    $object->sendNews();
                    break;
                case "help":
                case "h":
                case "?":
                case "？":
                    $object->addNews('帮助信息',"","","");
                    $object->addNews(wm_get_setting('welcome'),"","","");
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
    global $wpdb;
    //关键词回复
    $cacheKey = 'wechat-manager-custom-reply-key';
    if (!$cacheVal = wp_cache_get($cacheKey)) {
        $message_table = $wpdb->prefix . "wx_message";
        $sql = 'SELECT * FROM ' . $message_table . ' ORDER BY id DESC';
        $results = $wpdb->get_results($sql, ARRAY_A);
        foreach ($results as $r) {
            $cacheVal[$r['keyword']] = $r;
        }
        wp_cache_set($cacheKey, $cacheVal);
    }
    if ($cacheVal && in_array($keyword, array_keys($cacheVal))) {
        $reply = $cacheVal[$keyword];
        switch ($reply['type']) {
            case 'post':
                send_post_by_ids($object, $reply['content']);
                break;
            default:
                $object->sendText(stripslashes($reply['content']));
                break;
        }
    }

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
        default:
            send_post($object, '', $key);
            break;
    }
}

?>
