<?php

function get_wm_option(){
    $array_wm_option = array();
    $array_wm_option[WM_TOKEN] = get_option(WM_TOKEN);
    $array_wm_option[WM_WELCOME] = get_option(WM_WELCOME);
	$array_wm_option[WM_THUMB] = get_option(WM_THUMB);
    $array_wm_option[WM_BDAK] = get_option(WM_BDAK);
    $array_wm_option[WM_TRANSLATE_APPID] = get_option(WM_TRANSLATE_APPID);
    $array_wm_option[WM_TRANSLATE_KEY] = get_option(WM_TRANSLATE_KEY);
	$array_wm_option[WM_POST_NUMS] = get_option(WM_POST_NUMS);

   return $array_wm_option;
}

function update_weixinpress_option(){
    if($_POST['action']='保存设置'){
        update_option(WM_TOKEN, $_POST['wm_token']);
        update_option(WM_WELCOME, $_POST['wm_welcome']);
		update_option(WM_THUMB, $_POST['wm_thumb']);
        update_option(WM_BDAK, $_POST['wm_bdak']);
        update_option(WM_TRANSLATE_APPID, $_POST['wm_translate_appid']);
        update_option(WM_TRANSLATE_KEY, $_POST['wm_translate_key']);
		update_option(WM_POST_NUMS, $_POST['wm_post_nums']);
    }
	echo '<div class="updated fade" id="message"><p>恭喜，更新配置成功</p></div>';

}

/** custom message **/
function wm_topbarmessage($msg) {
     echo '<div class="updated fade" id="message"><p>' . $msg . '</p></div>';
}
function wechat_manager_optionpage(){

?>
    <style type="text/css">
        h2{
            height:36px;
            line-height: 36px;
        }
        label{
            display: inline-block;
            font-weight: bold;
        }
        textarea{
            width:450px;
            height:80px;
        }
        input{
            width: 450px;
            height: 30px;
        }
        table{
            border: 0px solid #ececec;
        }
        tr{
            margin: 20px 0px;
        }
        .right{
            vertical-align: top;
            padding-top: 10px;
            width:100px;
            text-align: right;
        }
        .left{
            width: 500px;
            padding-left:50px;
            text-align: left;
        }
        .wm-logo{
            background: url(<?php echo WXP_URL; ?>/images/weixin-big.png) 0px 0px no-repeat;
            background-size: 36px 36px;
            height: 36px;
            width: 36px;
            float: left;
        }
        .wm-notes{
            margin: 10px 0px 30px 0px;
            display: inline-block;
            width: 450px;
        }
        .wm-submit-btn{
            height: 30px;
            width: 150px;
            background-color: #21759b;
            font-weight: bold;
            color: #ffffff;
            font-family: "Microsoft YaHei";
        }
        .wm-center{
            text-align: center;
        }
        .wm-btn-box{
            margin: 15px 0px;
        }
        .wm-option-main{
            margin: 5px 0px;
            width: 650px;
            float:left;
        }
    </style>

    <div class="wm-option-container">
        <div class="wm-header">
            <div class="wm-logo"></div>
            <h2>Wechat Manager 设置</h2>
        </div>
        <?php
        if(isset($_POST['action'])){
            if($_POST['action']=='保存设置'){
                update_weixinpress_option();
            }
        }
        $array_wm_option = get_wm_option();
        ?>
        <div class="wm-option-main">
            <form name="wm-options" method="post" action="">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="right"><label>微信TOKEN：</label></td>
                        <td class="left">
                            <input type="text" name="wm_token" value="<?php echo $array_wm_option[WM_TOKEN]; ?>"/>
                            <span class="wm-notes">填写用于微信接口的TOKEN，与微信后台设置一致</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right"><label>欢迎信息：</label></td>
                        <td class="left">
                            <textarea name="wm_welcome"><?php echo $array_wm_option[WM_WELCOME]; ?></textarea>
                            <span class="wm-notes">填写用于用户订阅时发送的欢迎信息</span>
                        </td>
                    </tr>
					<tr>
                        <td class="right"><label>默认缩略图：</label></td>
                        <td class="left">
							<input type="text" name="wm_thumb" value="<?php echo $array_wm_option[WM_THUMB]; ?>"/>
                            <span class="wm-notes">当文章中没有缩略图时显示</span>
                        </td>
                    </tr>
					<tr>
                        <td class="right"><label>返回文章条数：</label></td>
                        <td class="left">
							<input type="text" name="wm_post_nums" value="<?php echo $array_wm_option[WM_POST_NUMS]; ?>"/>
                            <span class="wm-notes">返回文章的条数，请设置一个小于10的数，否则会出错</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right"><label>百度API密钥：</label></td>
                        <td class="left">
                            <input type="text" name="wm_bdak" value="<?php echo $array_wm_option[WM_BDAK]; ?>"/>
                            <span class="wm-notes">用于天气查询，申请地址<a href="http://lbsyun.baidu.com/apiconsole/key?application=key" target="_blank">百度地图密钥</a></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right"><label>百度翻译APPID：</label></td>
                        <td class="left">
                            <input type="text" name="wm_translate_appid" value="<?php echo $array_wm_option[WM_TRANSLATE_APPID]; ?>"/>
                            <span class="wm-notes">用于翻译，申请地址<a href="http://api.fanyi.baidu.com/api/trans/product/index" target="_blank">百度地图密钥</a></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="right"><label>百度翻译KEY：</label></td>
                        <td class="left">
                            <input type="text" name="wm_translate_key" value="<?php echo $array_wm_option[WM_TRANSLATE_KEY]; ?>"/>
                            <span class="wm-notes">用于翻译，申请地址<a href="http://api.fanyi.baidu.com/api/trans/product/index" target="_blank">百度地图密钥</a></span>
                        </td>
                    </tr>
					<tr>
                        <td colspan="2" class="wm-center wm-btn-box">
                            <input type="submit" class="wxp-submit-btn" name="action" value="保存设置"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>




<?php
}