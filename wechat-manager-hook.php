<?php
//添加插件的菜单
add_action('admin_menu','wechat_manager_menu');

function wechat_manager_menu() {
    //下面的这个函数是Wordpress核心函数，请看本文菜单函数部分
    add_menu_page(
        "微信公众平台管家",
        "Wechat Manager",
        8,
        "wechat-manager-option",
        "wechat_manager_optionpage",
        WECHAT_MANAGER_STATIC."/imgs/weixin.png"
    );
}

register_activation_hook( WECHAT_MANAGER_PLUGIN_FILE, 'wechat_manager_install' );

function wechat_manager_install () {
    global $wpdb;
	$wechat_manager_db_version = "0.1";

    $table_name = $wpdb->prefix . "wx_user";
    if($wpdb->get_var("show tables like ".$table_name) != $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`openid` varchar(30) NOT NULL DEFAULT '',
				`nickname` varchar(100) NOT NULL DEFAULT '',
				`sex` tinyint(4) NOT NULL,
				`province` varchar(100) NOT NULL DEFAULT '',
				`country` varchar(100) NOT NULL DEFAULT '',
				`headimgurl` varchar(255) NOT NULL DEFAULT '',
				`jointime` int(11) NOT NULL,
				`logintime` int(11) NOT NULL,
				`lng` double NOT NULL COMMENT '经度',
				`lat` double NOT NULL COMMENT '纬度',
				`subscribe` tinyint(2) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`)
			  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");
        dbDelta($sql);
		add_option("wechat_manager_db_version", $wechat_manager_db_version);
		$installed_ver = get_option('wechat_manager_db_version');
		if($installed_ver != $wechat_manager_db_version){
			$sql = "CREATE TABLE `".$table_name."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`openid` varchar(30) NOT NULL DEFAULT '',
				`nickname` varchar(100) NOT NULL DEFAULT '',
				`sex` tinyint(4) NOT NULL,
				`province` varchar(100) NOT NULL DEFAULT '',
				`country` varchar(100) NOT NULL DEFAULT '',
				`headimgurl` varchar(255) NOT NULL DEFAULT '',
				`jointime` int(11) NOT NULL,
				`logintime` int(11) NOT NULL,
				`lng` double NOT NULL COMMENT '经度',
				`lat` double NOT NULL COMMENT '纬度',
				`subscribe` tinyint(2) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			require_once(ABSPATH . "wp-admin/includes/upgrade.php");
			dbDelta($sql);
			update_option("wechat_manager_db_version", $wechat_manager_db_version);
		}

    }
}
?>