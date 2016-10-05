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
        'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCwgMCwgNDAwLCA0MDAiPgogIDxnIGlkPSJMYXllciAxIj4KICAgIDxwYXRoIGQ9Ik0xMjAuNjAxLDQ2LjU3NiBDOS4yNDEsNjYuNDY2IC0yNy44NzksMTkyLjI4MSA2MC43LDI0OS44NjkgQzY1LjU1NywyNTIuOTkxIDY1LjU1NywyNTIuNjQ1IDU4LjI3MSwyNzQuMzg1IEw1Mi4wMjcsMjkzLjAwMiBMNzQuNDYxLDI4MC45NzYgTDk2Ljg5NSwyNjguOTUgTDEwOC44MDYsMjcxLjg0MSBDMTIxLjI5NCwyNzQuOTYzIDEzNy4yNTMsMjc3LjE2IDE0Ny44OTEsMjc3LjE2IEwxNTQuMjUyLDI3Ny4xNiBMMTUyLjA1NCwyNjguNzE4IEMxMzQuNTkzLDIwNC40MjMgMTk0Ljk1NiwxNDAuNzA2IDI3My40NzUsMTQwLjcwNiBMMjg0LjExNCwxNDAuNzA2IEwyODEuOTE3LDEzMy4wNzQgQzI2NC42ODYsNzIuODI2IDE5MS45NSwzMy44NTYgMTIwLjYwMSw0Ni41NzYgeiBNMTEwLjg4NywxMDIuODkyIEMxMjIuNjgyLDExMC44NzIgMTIzLjM3NiwxMjguMTAyIDExMi4wNDMsMTM1LjUwMyBDOTMuNjU3LDE0Ny41MjkgNzIuMTQ4LDEyNi4zNjcgODQuNTIxLDEwOC4zMjcgQzg5Ljk1NiwxMDAuMjMzIDEwMy4wMjQsOTcuNTczIDExMC44ODcsMTAyLjg5MiB6IE0yMDUuNzExLDEwMi44OTIgQzIyNS4xMzgsMTE1Ljk2IDIxMC41NjgsMTQ2LjE0MSAxODguODI3LDEzNy44MTUgQzE3My4xMDEsMTMxLjgwMiAxNzEuMjUsMTEwLjE3OCAxODUuOTM2LDEwMi40MyBDMTkxLjcxOCw5OS4zMDggMjAwLjczOCw5OS41MzkgMjA1LjcxMSwxMDIuODkyIHogTTI0OC42MTMsMTUwLjUzNiBDMTkzLjQ1MywxNjAuNTk2IDE1NS4xNzcsMjAyLjQ1NyAxNTcuMzc0LDI1MC41NjMgQzE2MC4yNjUsMzE0Ljk3NCAyMzUuNzc3LDM1OS4zNzkgMzA4LjI4MiwzMzkuNDg5IEwzMTYuODM5LDMzNy4xNzYgTDMzNC44NzksMzQ2Ljg5IEMzNDQuODI0LDM1Mi4zMjUgMzUzLjE1LDM1Ni4yNTcgMzUzLjM4MSwzNTUuNzk0IEMzNTMuNjEzLDM1NS4yMTYgMzUxLjY0NywzNDguMjc4IDM0OS4xMDMsMzQwLjI5OSBDMzQzLjMyMSwzMjIuNDkgMzQzLjIwNSwzMjMuNzYyIDM1MC45NTMsMzE4LjIxMiBDNDM4LjE0NCwyNTUuNjUxIDM2MS41OTIsMTMwLjA2OCAyNDguNjEzLDE1MC41MzYgeiBNMjQ2LjQxNiwyMDIuNDU3IEMyNTEuMjcyLDIwNS42OTUgMjUzLjgxNiwyMTMuNzkgMjUxLjczNSwyMTkuNjg4IEMyNDcuMzQxLDIzMi4yOTIgMjI4LjQ5MiwyMzMuMjE3IDIyMy40MDMsMjIxLjA3NSBDMjE3LjYyMSwyMDcuMDgzIDIzMy41OCwxOTQuMTMxIDI0Ni40MTYsMjAyLjQ1NyB6IE0zMjMuNjYyLDIwMy44NDUgQzMzMS4yOTQsMjExLjEzIDMzMC4wMjIsMjIzLjUwNCAzMjEuMTE4LDIyOC4xMjkgQzMwNy40NzMsMjM1LjA2NyAyOTMuMTM0LDIyMS4xOTEgMzAwLjE4OCwyMDcuODkyIEMzMDQuODEzLDE5OS4zMzUgMzE2LjcyNCwxOTcuMjU0IDMyMy42NjIsMjAzLjg0NSB6IE0yMjAuNDMsMzI4Ljc3MiIgZmlsbD0iI2ZmZiIvPgogIDwvZz4KICA8ZGVmcy8+Cjwvc3ZnPg=='
    );

    add_submenu_page( 'wechat-manager-option', '自定义菜单 &lsaquo; 微信公众平台管家', '自定义菜单', 'manage_options', 'wechat-manager-option-setting', 'wechat_manager_optionpage_menu');
    add_submenu_page( 'wechat-manager-option', '自定义回复 &lsaquo; 微信公众平台管家', '自定义回复', 'manage_options', 'wechat-manager-reply-setting', 'wechat_manager_optionpage_reply');
}

register_activation_hook( WECHAT_MANAGER_PLUGIN_FILE, 'wechat_manager_install' );

function wechat_manager_install () {
    global $wpdb;
    $wechat_manager_db_version = "2.1";


    $installed_ver = get_option('wechat_manager_db_version');

    require_once(ABSPATH . "wp-admin/includes/upgrade.php");

    $user_table = $wpdb->prefix . "wx_user";
    $menu_table = $wpdb->prefix . "wx_menu";
    $message_table = $wpdb->prefix . "wx_message";
    if($wpdb->get_var("show tables like ".$user_table) != $user_table) {
        $sql = "CREATE TABLE IF NOT EXISTS `".$user_table."` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `openid` varchar(30) NOT NULL DEFAULT '',
                `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '昵称',
                `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
                `phone` varchar(20) NOT NULL COMMENT '电话号码',
                `id_card` varchar(18) NOT NULL COMMENT '身份证',
                `address` text NOT NULL COMMENT '地址',
                `sex` tinyint(4) NOT NULL,
                `city` varchar(255) NOT NULL,
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
        dbDelta($sql);
        add_option("wechat_manager_db_version", $wechat_manager_db_version);
        /*if($installed_ver != $wechat_manager_db_version){
            $sql = "CREATE TABLE `".$table_name."` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `openid` varchar(30) NOT NULL DEFAULT '',
                `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '昵称',
                `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
                `phone` varchar(20) NOT NULL COMMENT '电话号码',
                `id_card` varchar(18) NOT NULL COMMENT '身份证',
                `address` text NOT NULL COMMENT '地址',
                `sex` tinyint(4) NOT NULL,
                `city` varchar(255) NOT NULL,
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
            dbDelta($sql);
            update_option("wechat_manager_db_version", $wechat_manager_db_version);
        }*/
    }
    if($wpdb->get_var("show tables like ".$message_table) != $message_table) {
        $sql = "CREATE TABLE IF NOT EXISTS `".$message_table."` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `keyword` varchar(30) NOT NULL DEFAULT '' COMMENT '关键词',
                `type` varchar(10) NOT NULL DEFAULT '' COMMENT '类型',
                `content` text NOT NULL COMMENT '内容',
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        dbDelta($sql);
        add_option("wechat_manager_db_version", $wechat_manager_db_version);
    }
}
?>