<?php

if(!function_exists('get_post_excerpt')){
    //获取日志摘要
    function get_post_excerpt($post){
        $post_excerpt = strip_tags($post->post_excerpt);
        if(!$post_excerpt){
            $post_excerpt = mb_substr(trim(strip_tags($post->post_content)),0,120);
        }
        return $post_excerpt;
    }
}

if(!function_exists('get_post_first_image')){
	function get_post_first_image($post_content){
		preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i', do_shortcode($post_content), $matches);
		if($matches){
			return $matches[1][0];
		}else{
			return false;
		}
	}
}
// 判断当前用户操作是否在微信内置浏览器中
if(!function_exists('is_weixin')){
	function is_weixin(){
		if ( isset($_SERVER['HTTP_USER_AGENT']) ) {
			if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone') !== false ) {
				return true;
			}
		}
		return false;
	}
}
//获取博客文章
function wm_query_posts($q, $s=""){
	global $wp_query;
	$articles = array();
	$query_base= array(
		'ignore_sticky_posts'	=> true,
		'posts_per_page'		=> POSTNUM,
		'post_status'			=> 'publish',
	);
	if(empty($s)){
		switch($q){
			case "c7":
				$query_more = array(
					"order"	=> "DESC",
					"orderby" => "comment_count",
					'date_query' => array(
						array(
							'after'  => '1 week ago',
						),
					),
				);
				break;
			case "c30":
				$query_more = array(
					"order"	=> "DESC",
					"orderby" => "comment_count",
					'date_query' => array(
						array(
							'after'  => '1 month ago',
						),
					),
				);
				break;
			case "c365":
				$query_more = array(
					"order"	=> "DESC",
					"orderby" => "comment_count",
					'date_query' => array(
						array(
							'after'  => '1 year ago',
						),
					),
				);
				break;
			case "c":
				$query_more = array(
					"order"	=> "DESC",
					"orderby" => "comment_count",
				);
				break;
			case "n":
				$query_more = array(
					"order"	=> "DESC",
					"orderby" => "date",
				);
				break;
			case "r":
				$query_more = array(
					"orderby" => "rand",
				);
				break;
			default:
				$query_more = array();
				break;
		}
	}else{
		$query_more = array(
			's' => $s,
		);
	}
	$weixin_query_array = array_merge($query_base, $query_more);
	$weixin_query_array = apply_filters('weixin_query',$weixin_query_array);
	$wp_query->query($weixin_query_array);
	if(have_posts()){
		while (have_posts()) {
			the_post();

			global $post;

			$title =get_the_title();
			$excerpt = get_post_excerpt($post);

			$thumbnail_id = get_post_thumbnail_id($post->ID);
			if($thumbnail_id ){
				$thumb = wp_get_attachment_image_src($thumbnail_id, 'full');
				$thumb = $thumb[0];
			}else{
				$thumb = get_post_first_image($post->post_content);
			}
			global $wm_thumb;
			if(empty($thumb) && !empty($wm_thumb)){
				$thumb = $wm_thumb;
			}
			$link = get_permalink();
			$articles[] = array($title,$excerpt,$link,$thumb);
		}
	}
	return $articles;
}

function send_post($object, $type='', $value='')
{
	$articles = wm_query_posts($type, $value);
	if (empty($articles)) {
		$object->sendText("暂无相关文章");
	}
    foreach($articles as $v){
        $object->addNews($v['0'],$v['1'],$v['2'],$v['3']);
    }
    $object->sendNews();
}

function wm_query_posts_by_ids($ids) {
    global $wp_query;
    $articles = array();
    $query_base= array(
        'ignore_sticky_posts'	=> true,
        'posts_per_page'		=> POSTNUM,
        'post_status'			=> 'publish',
    );
    $idArr = explode(',', $ids);
    $query_base['post__in'] = $idArr;
    $weixin_query_array = apply_filters('weixin_query',$query_base);
    $wp_query->query($weixin_query_array);
    if(have_posts()){
        while (have_posts()) {
            the_post();

            global $post;

            $title =get_the_title();
            $excerpt = get_post_excerpt($post);

            $thumbnail_id = get_post_thumbnail_id($post->ID);
            if($thumbnail_id ){
                $thumb = wp_get_attachment_image_src($thumbnail_id, 'full');
                $thumb = $thumb[0];
            }else{
                $thumb = get_post_first_image($post->post_content);
            }
            global $wm_thumb;
            if(empty($thumb) && !empty($wm_thumb)){
                $thumb = $wm_thumb;
            }
            $link = get_permalink();
            $articles[] = array($title,$excerpt,$link,$thumb);
        }
    }
    return $articles;
}

function send_post_by_ids($object, $ids)
{
    $articles = wm_query_posts_by_ids($ids);
    if (empty($articles)) {
        $object->sendText("暂无相关文章");
    }
    foreach($articles as $v){
        $object->addNews($v['0'],$v['1'],$v['2'],$v['3']);
    }
    $object->sendNews();
}

//英汉互译
function wm_translate($q){
	/*global $wm_bdak;
	if(empty($wm_bdak)){
		$content = "请先在后台配置 百度API密钥 后使用";
		return $content;
	}
	$url = "http://openapi.baidu.com/public/2.0/bmt/translate?client_id={$wm_bdak}&q={$value}&from=auto&to=auto";
	$content = json_decode(file_get_contents($url));
	$content = $content->trans_result;
	$content = $content[0]->dst;
	return $content;*/
	global $wm_translate_appid, $wm_translate_key;
	if(empty($wm_translate_appid) || empty($wm_translate_key)){
		$content = "请先在后台配置 百度翻译APPID和KEY 后使用";
		return $content;
	}
	if (empty($q)) return false;
	// $q = filter_allowed_words($q);

	$salt = rand(1, 999);
	$sign = md5($wm_translate_appid . $q . $salt . $wm_translate_key);
	$url = 'http://api.fanyi.baidu.com/api/trans/vip/translate?q=' . $q . '&appid=' . $wm_translate_appid . '&salt=' . $salt . '&from=auto&to=auto&sign=' . $sign;
	$result = json_decode(file_get_contents($url), true);
	$dst = $result['trans_result'][0]['dst'];
	return $dst ? $dst : false;
}
//天气查询
function wm_weather($city){
	global $wm_bdak;
	if(empty($wm_bdak)){
		$content = "请先在后台配置 百度API密钥 后使用";
		return $content;
	}
	$json = json_decode(file_get_contents("http://api.map.baidu.com/telematics/v3/weather?location={$city}&output=json&ak={$wm_bdak}"));
	$result = $json->results[0]->weather_data;
	if(!is_array($result)){
		$content = "找不到与 {$city} 有关的天气信息，请换一个关键词试试";
		return $content;
	}
	for($i=0; $i<4; $i++){
		$date = $result[$i]->date;
		$array[$i]['pic'] = $result[$i]->dayPictureUrl;
		$weather = $result[$i]->weather;
		$wind = $result[$i]->wind;
		$temp = $result[$i]->temperature;
		$array[$i]['title'] = $date." 天气:".$weather." 温度:".$temp." 风力:".$wind;
		if($i == 0){
			$array[$i]['pic'] = WECHAT_MANAGER_STATIC."/include/weather.jpg";
			$array[$i]['title'] = $city." ".$array[$i]['title'];
		}
	}
	return $array;
}


function wm_get_setting($opt)
{
	$options = wm_get_options();
	return $options[$opt] ? $options[$opt] : null;
}

function wm_get_options()
{
	if (!$options = wp_cache_get('options_setting', 'wm_options')) {
		$options = get_option( 'wm_options' );
		wp_cache_add( 'options_setting', $options, 'wm_options' );
	}
	return $options;
}

function wm_get_access_token(){

	if(wm_get_setting('appid') && wm_get_setting('appsecret')){

		$wm_access_token = get_transient('wm_access_token');

		if($wm_access_token === false){
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.wm_get_setting('appid').'&secret='.wm_get_setting('appsecret');
			$wm_access_token = wp_remote_get($url,array('sslverify'=>false));
			if(is_wp_error($wm_access_token)){
				echo '<div class="wrap"><div class="updated"><p>'.$wm_access_token->get_error_code().'：'. $wm_access_token->get_error_message().'</p></div></div>';
				exit;
			}
			$wm_access_token = json_decode($wm_access_token['body'],true);
			if(isset($wm_access_token['access_token'])){
				set_transient('wm_access_token',$wm_access_token['access_token'],$wm_access_token['expires_in']);
				return $wm_access_token['access_token'];
			}else{
				echo '<div class="wrap"><div class="updated"><p>错误代码：'.$wm_access_token['errcode'].'，信息：'.$wm_access_token['errmsg'].'</p></div></div>';
				return;
			}
		}else{
			return $wm_access_token;
		}
	}else{
		echo '<div class="wrap"><div class="updated"><p>请先设置 AppID 与 AppSecret</p></div></div>';
		return;
	}
}