<?php
/**
 * 用户类
 * @author: Specs
 * Email: specs@9iphp.com
 * Blog: http://www.9iphp.com
 */

class User {


	public $openId = '';
	public $data = array();   //用户数据
	public $error  = "";
	private $wx_user = "";
	/**
	 * 构造函数
	 * @param $openId 用户的OpenID
	 */
	function User($openId) {

		global $wpdb;
		$time = time();
		$this->openId = $openId;
		$this->wx_user = $wpdb->prefix.'wx_user';
		$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$this->wx_user}` WHERE openid=%s",$this->openId));

		//$this->error = $user;
		if ($user) { //如果已存在存在
			$data = array("logintime"=>$time,"subscribe"=>1);
			$wpdb->update($this->wx_user, $data, array("openid"=>$this->openId));
			$user->logintime = $time;
			$this->data = $user; //读出用户数据
		} else {
			//如果不存在，则插入数据库
			$data = array(
				"openid" => $this->openId,
				"jointime" => $time,
				"logintime" => $time,
				"subscribe" => 1,
			);
			$format = array("%s","%d","%d");
			$wpdb->insert($this->wx_user, $data, $format);
			$this->data = $data;
		}
	}

	/**
	 * 删除用户
	 */
	public function delete() {
		global $wpdb;
		$data = array(
			"subscribe" => 0,
		);
		$wpdb->update($this->wx_user, $data, array("openid"=>$this->openId));
	}

	/**
	 * 设置App和Stage值
	 * @param unknown_type $app
	 */
	function setApp($app) {
		global $wpdb;
		$data = array(
			"app" => $app,
			"logintime" => time(),
		);
		$wpdb->update($this->wx_user, $data, array("openid"=>$this->openId));
		$this->data->app = $app;
	}

	/**
	 * 取App值
	 */
	function getApp(){
		return $this->data->app;
	}


	/**
	 * 设置键值: $key=$value
	 */
	function set($key, $value)
	{
		global $wpdb;
		$data = array(
			"{$key}" => $value,
		);
		$wpdb->update($this->wx_user, $data, array("openid"=>$this->openId));
		$this->data->{$key} = $value;
	}

	/**
	 * 读取键值
	 */
	function get($key) { return $this->data->$key;}

	/**
	 *设置经纬度
	 */
	function setPos($lng,$lat){
		global $wpdb;
		$wpdb->show_errors();
		$data = array(
			"lng" => $lng,
			"lat" => $lat,
		);
		$result = $wpdb->update($this->wx_user, $data, array("openid"=>$this->openId));
		//$wpdb->print_error();
		if($result){
			return true;
		}else{
			$this->error = mysql_error();
			return false;
		}

	}
}
?>
