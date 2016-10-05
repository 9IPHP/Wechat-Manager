<?php
add_action( 'admin_init', 'wm_options_init' );
// add_action( 'admin_menu', 'wm_options_add_page' );

function wm_options_init(){
    register_setting( 'wm_options', 'wm_options', 'wm_options_validate' );
}

function wm_options_add_page() {
    add_theme_page( __( 'Theme Options', 'sampletheme' ), __( 'Theme Options', 'sampletheme' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

function wm_options_validate( $input ) {
    return $input;
}

/** custom message **/
function wm_topbarmessage($msg) {
     echo '<div class="updated fade" id="message"><p>' . $msg . '</p></div>';
}
function wechat_manager_optionpage(){
    $default = array(
        'welcome' => "欢迎关注！\n输入 n 返回最新文章！\n输入 r 返回随机文章！\n输入 c7 返回一周内最多评论文章！\n输入 c30 返回一月内最多评论文章！\n输入 c365 返回一年内最多评论文章！\n输入 c 返回所有文章中最多评论文章！\n搜索文章直接输入搜索词即可！\n输入 help 或 ? 获取帮助信息！",
        'no_post' => 'Sorry，暂无相关匹配结果！',
        'post_num' => 5,
        'post_new' => 'n',
        'post_comment_week' => 'c7',
        'post_comment_month' => 'c30',
        'post_comment_year' => 'c365',
        'post_comment_all' => 'c',
    );
    if ( ! isset( $_REQUEST['settings-updated'] ) )
        $_REQUEST['settings-updated'] = false;
?>
    <?php if ( false !== $_REQUEST['settings-updated'] ) :
        wp_cache_delete( 'options_setting', 'wm_options' );
    ?>
        <div class="updated fade"><p><strong>Options saved!</strong></p></div>
    <?php endif; ?>

<div class="wrap">
    <div class="wm-option-container">
        <div class="wm-header">
            <div class="wm-logo"></div>
            <h1>Wechat Manager 设置</h1>
            <p style="color:red">以下设置中，文章关键词若为英文，请使用小写形式</p>
            <p>帮助信息的关键词为：help,h,?,？ 用户输入四个中任意一个都会返回帮助信息</p>
        </div>
        <div class="wm-option-main">
            <form name="wm-options" method="post" action="options.php">
                <?php settings_fields( 'wm_options' ); ?>
                <?php $options = get_option( 'wm_options' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th>微信 Token</th>
                            <td>
                                <input id="token" class="regular-text" type="text" name="wm_options[token]" value="<?php esc_attr_e( $options['token'] ); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>默认缩略图</th>
                            <td>
                                <input type="url" name="wm_options[thumb]" id="thumb" class="type-image regular-text" value="<?php esc_attr_e( $options['thumb'] ); ?>">
                                <input type="button" class="wm_upload button" value="选择图片">
                            </td>
                        </tr>
                        <tr>
                            <th>欢迎/帮助信息</th>
                            <td>
                                <textarea id="welcome" class="large-text" cols="30" rows="8" name="wm_options[welcome]"><?php echo $options['welcome'] ? esc_textarea( $options['welcome'] ) : $default['welcome'] ; ?></textarea>
                                <p class="description">当用户订阅及输入帮助信息时的回复</p>
                            </td>
                        </tr>
                        <tr>
                            <th>无匹配结果的回复</th>
                            <td>
                                <input id="no_post" class="regular-text" type="text" name="wm_options[no_post]" value="<?php echo esc_attr( $options['no_post'] ); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>文章返回最大数</th>
                            <td>
                                <input id="post_num" class="regular-text" type="text" name="wm_options[post_num]" value="<?php echo esc_attr( $options['post_num'] ); ?>" placeholder="6"/>
                                <p class="description">返回文章个数(最多为 10 个)</p>
                            </td>
                        </tr>
                        <tr>
                            <th>最新文章关键词</th>
                            <td>
                                <input id="post_new" class="regular-text" type="text" name="wm_options[post_new]" value="<?php echo esc_attr( $options['post_new'] ); ?>"  placeholder="n"/>
                                <p class="description">返回最新文章的关键词</p>
                            </td>
                        </tr>
                        <tr>
                            <th>随机文章关键词</th>
                            <td>
                                <input id="post_rand" class="regular-text" type="text" name="wm_options[post_rand]" value="<?php esc_attr_e( $options['post_rand'] ); ?>"  placeholder="r"/>
                                <p class="description">随机返回文章的关键词(建议不启用，因为比较消耗资源)(留空则不启用)</p>
                            </td>
                        </tr>
                        <tr>
                            <th>一周内热门文章关键词</th>
                            <td>
                                <input id="post_comment_week" class="regular-text" type="text" name="wm_options[post_comment_week]" value="<?php echo esc_attr( $options['post_comment_week'] ); ?>"  placeholder="c7"/>
                                <p class="description">返回一周内发布文章中评论最多的关键词</p>
                            </td>
                        </tr>
                        <tr>
                            <th>一月内热门文章关键词</th>
                            <td>
                                <input id="post_comment_month" class="regular-text" type="text" name="wm_options[post_comment_month]" value="<?php echo esc_attr( $options['post_comment_month'] ); ?>"  placeholder="c30"/>
                                <p class="description">返回一月内发布文章中评论最多的关键词</p>
                            </td>
                        </tr>
                        <tr>
                            <th>一年内热门文章关键词</th>
                            <td>
                                <input id="post_comment_year" class="regular-text" type="text" name="wm_options[post_comment_year]" value="<?php echo esc_attr( $options['post_comment_year'] ); ?>"  placeholder="c365"/>
                                <p class="description">返回一年内发布文章中评论最多的关键词</p>
                            </td>
                        </tr>
                        <tr>
                            <th>所有文章热门文章关键词</th>
                            <td>
                                <input id="post_comment_all" class="regular-text" type="text" name="wm_options[post_comment_all]" value="<?php echo esc_attr( $options['post_comment_all'] ); ?>"  placeholder="c"/>
                                <p class="description">返回所有文章中评论最多的关键词</p>
                            </td>
                        </tr>
                        <!-- <tr>
                            <th>帮助关键词</th>
                            <td>
                                <input id="help" class="regular-text" type="text" name="wm_options[help]" value="<?php echo $options['help'] ? esc_attr( $options['help'] ) : $default['help']; ?>" />
                                <p class="description">帮助信息关键词</p>
                            </td>
                        </tr> -->
                        <tr>
                            <th>微信公众平台 AppID</th>
                            <td>
                                <input id="appid" class="regular-text" type="text" name="wm_options[appid]" value="<?php esc_attr_e( $options['appid'] ); ?>" />
                                <p class="description">微信公众平台开发者ID信息，用于创建自定义菜单</p>
                            </td>
                        </tr>
                        <tr>
                            <th>微信公众平台 AppSecret</th>
                            <td>
                                <input id="appsecret" class="regular-text" type="text" name="wm_options[appsecret]" value="<?php esc_attr_e( $options['appsecret'] ); ?>" />
                            </td>
                        </tr>
                        <!-- <tr>
                            <th>贺卡</th>
                            <td>
                                <input id="card" class="regular-text" type="text" name="wm_options[card]" value="<?php esc_attr_e( $options['card'] ); ?>" />
                                <p class="description">(留空则不启用)</p>
                            </td>
                        </tr>
                        <tr>
                            <th>百度翻译 AppID</th>
                            <td>
                                <input id="trans_appid" class="regular-text" type="text" name="wm_options[trans_appid]" value="<?php esc_attr_e( $options['trans_appid'] ); ?>" />
                                <p class="description">用于翻译，申请地址<a href="http://api.fanyi.baidu.com/api/trans/product/index" target="_blank">百度翻译API</a>(留空则不启用)</p>
                            </td>
                        </tr>
                        <tr>
                            <th>百度翻译 KEY</th>
                            <td>
                                <input id="trans_key" class="regular-text" type="text" name="wm_options[trans_key]" value="<?php esc_attr_e( $options['trans_key'] ); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>百度API密钥（天气查询）</th>
                            <td>
                                <input id="bd_key" class="regular-text" type="text" name="wm_options[bd_key]" value="<?php esc_attr_e( $options['bd_key'] ); ?>" />
                                <p class="description">用于天气查询，申请地址<a href="http://lbsyun.baidu.com/apiconsole/key?application=key" target="_blank">百度地图密钥</a>，其中“应用类型”应选择“服务端”(留空则不启用)</p>
                            </td>
                        </tr> -->
                        <!-- <tr>
                            <th>是否已获得高级接口权限</th>
                            <td>
                                <label for="advanced_api">
                                    <input type="checkbox" name="wm_options[advanced_api]" id="advanced_api" class="type-checkbox " value="1" <?php if($options['advanced_api']) echo 'checked';?> >
                                    如果已获得高级接口权限，请勾选此项，如无，切勿勾选！
                                </label>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
                <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
            </form>
        </div>
    </div>
</div>

<?php
}

function wechat_manager_optionpage_menu()
{
    global $wm_custom_menus, $id, $succeed_msg;

    $wm_custom_menus = get_option('wm-custom-menus');
    if(!$wm_custom_menus) $wm_custom_menus = array();

    if(isset($_GET['delete']) && isset($_GET['id']) && $_GET['id']) {
        unset($wm_custom_menus[$_GET['id']]);
        update_option('wm-custom-menus',$wm_custom_menus);
        $succeed_msg = '删除成功';
    }

    if(isset($_GET['sync'])) {
        $succeed_msg = apply_filters('wm_post_custom_menus','', $wm_custom_menus);
    } elseif (isset($_GET['deleteAll'])) {
        update_option('wm-custom-menus', '');
    } elseif(isset($_GET['edit']) && isset($_GET['id'])){
        $id = (int)$_GET['id'];
    }

    if( $_SERVER['REQUEST_METHOD'] == 'POST' ){

        if ( !wp_verify_nonce($_POST['wm_custom_menu_nonce'],'wm-options') ){
            ob_clean();
            wp_die('非法操作');
        }

        $is_sub = isset($_POST['is_sub'])?1:0;

        $data = array(
            'name'          => stripslashes( trim( $_POST['name'] )),
            'type'          => stripslashes( trim( $_POST['type'] )),
            'key'           => stripslashes( trim( $_POST['key'] )),
            'position'      => $is_sub?'0':stripslashes( trim( $_POST['position'] )),
            'parent'        => $is_sub?stripslashes( trim( $_POST['parent'] )):'0',
            'sub_position'  => $is_sub?stripslashes( trim( $_POST['sub_position'] )):'0',
        );

        if(empty($id)){
            if($wm_custom_menus){
                end($wm_custom_menus);
                $id = key($wm_custom_menus)+1;
            }else{
                $id = 1;
            }
            $wm_custom_menus[$id]=$data;
            update_option('wm-custom-menus',$wm_custom_menus);
            $succeed_msg = '添加成功';
            $id = 0;
        }else{
            $wm_custom_menus[$id]=$data;
            update_option('wm-custom-menus',$wm_custom_menus);
            $succeed_msg = '修改成功';
        }
    }
?>
    <div class="wrap">
        <h2>自定义菜单</h2>
        <p>需要已经获得该权限才可使用，请在微信公众平台后台查看是否已获得该权限。</p>
        <?php if(!empty($succeed_msg)){?>
        <div class="updated">
            <p><?php echo $succeed_msg;?></p>
        </div>
        <?php }?>
        <?php wm_custom_menu_list(); ?>
        <?php wm_custom_menu_add(); ?>
    </div>

<?php
}
function wm_custom_menu_list(){
    global $plugin_page;

    $wm_custom_menus = get_option('wm-custom-menus');
    if(!$wm_custom_menus) $wm_custom_menus = array();
    ?>

    <h3>自定义菜单列表 <small><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&deleteAll'); ?>">清空</a></small></h3>
    <?php if($wm_custom_menus) { ?>
    <?php $wm_ordered_custom_menus = wm_get_ordered_custom_menus($wm_custom_menus);?>
    <form action="<?php echo admin_url('admin.php?page='.$plugin_page); ?>" method="POST">
        <table class="widefat" cellspacing="0">
        <thead>
            <tr>
                <th>按钮</th>
                <th>按钮位置/子按钮位置</th>
                <th>类型</th>
                <th>Key/URL</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php $alternate = '';?>
        <?php foreach($wm_ordered_custom_menus as $wm_custom_menu){ $alternate = $alternate?'':'alternate'; ?>
            <?php if(isset($wm_custom_menu['parent'])){?>
            <tr class="<?php echo $alternate; ?>">
                <td><?php echo $wm_custom_menu['parent']['name']; ?></td>
                <td><?php echo $wm_custom_menu['parent']['position']; ?></td>
                <td><?php echo $wm_custom_menu['parent']['type']; ?></td>
                <td><?php echo $wm_custom_menu['parent']['key']; ?></td>
                <?php $id = $wm_custom_menu['parent']['id'];?>
                <td><span><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&edit&id='.$id.'#edit'); ?>">编辑</a></span> | <span class="delete"><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&delete&id='.$id); ?>">删除</a></span></td>
            </tr>
            <?php } ?>
            <?php if(isset($wm_custom_menu['sub'])){  ?>
            <?php foreach($wm_custom_menu['sub'] as $wm_custom_menu_sub){ $alternate = $alternate?'':'alternate';?>
            <tr colspan="4" class="<?php echo $alternate; ?>">
                <td> └── <?php echo $wm_custom_menu_sub['name']; ?></td>
                <td> └── <?php echo $wm_custom_menu_sub['sub_position']; ?></td>
                <td><?php echo $wm_custom_menu_sub['type']; ?></td>
                <td><?php echo $wm_custom_menu_sub['key']; ?></td>
                <?php $id = $wm_custom_menu_sub['id'];?>
                <td><span><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&edit&id='.$id.'#edit'); ?>">编辑</a></span> | <span class="delete"><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&delete&id='.$id); ?>">删除</a></span></td>
            <tr>
            <?php }?>
            <?php } ?>
        <?php } ?>
        </tbody>
        </table>
        <p class="submit"><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&sync'); ?>" class="button-primary">同步自定义菜单</a></p>
    </form>
    <?php } ?>
<?php
}

// 后台表单 JS
add_action('admin_enqueue_scripts', 'wm_enqueue_scripts');
function wm_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('wm-setting', plugins_url('/include/wm-setting.js', __FILE__), array('jquery'));
    wp_localize_script('wm-setting', 'wm_setting', array(
        'ajax_url'  => admin_url('admin-ajax.php'),
        'nonce'     => wp_create_nonce('wm_setting_nonce')
    ));
}

function wm_get_ordered_custom_menus($wm_custom_menus){
    $wm_ordered_custom_menus = array();

    foreach ($wm_custom_menus as $id => $wm_custom_menu) {
        $wm_custom_menu['id'] = $id;
        if($wm_custom_menu['parent']){
            $wm_ordered_custom_menus[$wm_custom_menu['parent']]['sub'][$wm_custom_menu['sub_position']] = $wm_custom_menu;
        }else{
            $wm_ordered_custom_menus[$wm_custom_menu['position']]['parent'] = $wm_custom_menu;
        }
    }

    ksort($wm_ordered_custom_menus);

    foreach ($wm_ordered_custom_menus as $key => $wm_ordered_custom_menu) {
        if(isset($wm_ordered_custom_menu['sub'])){
            ksort($wm_ordered_custom_menus[$key]['sub']);
        }
    }

    return $wm_ordered_custom_menus;
}

function wm_custom_menu_add(){
    global $id, $plugin_page;

    $wm_custom_menus = get_option('wm-custom-menus');
    $wm_custom_menu = array();
    if($id && $wm_custom_menus && isset($wm_custom_menus[$id])){
        $wm_custom_menu = $wm_custom_menus[$id];
    }

    $parent_options         = array('0'=>'','1'=>'1','2'=>'2','3'=>'3');
    $position_options       = array('1'=>'1','2'=>'2','3'=>'3');
    $sub_position_options   = array('0'=>'','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5');
    $type_options           = array('click'=>'点击事件', 'view'=>'访问网页');
?>
    <h3 id="edit"><?php echo $id?'修改':'新增';?>自定义菜单 <?php if($id) { ?> <a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&add'); ?>" class="add-new-h2">新增另外一条自定义菜单</a> <?php } ?></h3>

     <form method="post" action="<?php echo admin_url('admin.php?page='.$plugin_page.'&edit&id='.$id.'#edit'); ?>" enctype="multipart/form-data" id="form">
        <table class="form-table" cellspacing="0">
            <tbody>
                <tr valign="top" id="tr_name">
                    <th scope="row"><label for="name">按钮名称</label></th>
                    <td>
                        <input type="text" name="name" id="name" class="type-text regular-text" value="<?php echo $wm_custom_menu ? $wm_custom_menu['name'] : ''; ?>">
                        <p class="description">按钮描述，既按钮名字，不超过16个字节，子菜单不超过40个字节</p>
                    </td>
                </tr>
                <tr valign="top" id="tr_type">
                    <th scope="row"><label for="type">按钮类型</label></th>
                    <td>
                        <select name="type" id="type" class="type-select ">
                            <option value="click" <?php echo ($wm_custom_menu && $wm_custom_menu['type'] == 'click') ? 'selected' : ''; ?>>点击事件</option>
                            <option value="view" <?php echo ($wm_custom_menu && $wm_custom_menu['type'] == 'view') ? 'selected' : ''; ?>>打开网页</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top" id="tr_key">
                    <th scope="row"><label for="key">按钮KEY值/URL</label></th>
                    <td>
                        <input type="text" name="key" id="key" class="type-text regular-text" value="<?php echo $wm_custom_menu ? $wm_custom_menu['key'] : ''; ?>">
                        <p class="description">用于消息接口（event类型）推送，不超过128字节，如果按钮还有子按钮，可以不填，其他必填，否则报错。<br>如果类型为点击事件时候，则为按钮KEY值，如果类型为浏览网页，则为URL。<br>点击事件的KEY值应设置为关键词回复中设置好的关键词。</p>
                    </td>
                </tr>
                <tr valign="top" id="tr_is_sub">
                    <th scope="row"><label for="is_sub">子按钮</label></th>
                    <td>
                        <input type="checkbox" name="is_sub" id="is_sub" class="type-checkbox is_sub" value="1" <?php echo ($wm_custom_menu && $wm_custom_menu['parent'] != 0) ? 'checked': ''; ?>>
                        <label for="is_sub" class="is_sub">是否激活</label>
                        <p class="description">选择激活，则显示为子菜单，需要选择父级菜单及子菜单位置</p>
                    </td>
                </tr>
                    <tr valign="top" id="tr_position">
                        <th scope="row"><label for="position">位置</label></th>
                        <td>
                            <select name="position" id="position" class="type-select ">
                                <option value="1" <?php echo ($wm_custom_menu && $wm_custom_menu['position'] == 1) ? 'selected' : ''; ?>>1</option>
                                <option value="2" <?php echo ($wm_custom_menu && $wm_custom_menu['position'] == 2) ? 'selected' : ''; ?>>2</option>
                                <option value="3" <?php echo ($wm_custom_menu && $wm_custom_menu['position'] == 3) ? 'selected' : ''; ?>>3</option>
                            </select>
                            <p class="description">设置按钮的位置</p>
                        </td>
                </tr>
                <tr valign="top" id="tr_parent">
                    <th scope="row"><label for="parent">所属父按钮位置</label></th>
                    <td>
                        <select name="parent" id="parent" class="type-select ">
                            <option value="0"></option>
                            <option value="1" <?php echo ($wm_custom_menu && $wm_custom_menu['parent'] == 1) ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?php echo ($wm_custom_menu && $wm_custom_menu['parent'] == 2) ? 'selected' : ''; ?>>2</option>
                            <option value="3" <?php echo ($wm_custom_menu && $wm_custom_menu['parent'] == 3) ? 'selected' : ''; ?>>3</option>
                        </select>
                        <p class="description">如果是子按钮则需要设置所属父按钮的位置</p>
                    </td>
                </tr>
                <tr valign="top" id="tr_sub_position">
                    <th scope="row"><label for="sub_position">子按钮的位置</label></th>
                    <td>
                        <select name="sub_position" id="sub_position" class="type-select ">
                            <option value="0"></option>
                            <option value="1" <?php echo ($wm_custom_menu && $wm_custom_menu['sub_position'] == 1) ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?php echo ($wm_custom_menu && $wm_custom_menu['sub_position'] == 2) ? 'selected' : ''; ?>>2</option>
                            <option value="3" <?php echo ($wm_custom_menu && $wm_custom_menu['sub_position'] == 3) ? 'selected' : ''; ?>>3</option>
                            <option value="4" <?php echo ($wm_custom_menu && $wm_custom_menu['sub_position'] == 4) ? 'selected' : ''; ?>>4</option>
                            <option value="5" <?php echo ($wm_custom_menu && $wm_custom_menu['sub_position'] == 5) ? 'selected' : ''; ?>>5</option>
                        </select>
                        <p class="description">设置子按钮的位置</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field('wm-options','wm_custom_menu_nonce'); ?>
        <input type="hidden" name="action" value="edit" />
        <p class="submit"><input class="button-primary" type="submit" value="<?php echo $id?'修改':'新增';?>" /></p>
    </form>

    <script type="text/javascript">
    jQuery(function(){
        <?php if( $id && $wm_custom_menu['parent'] ){?>
            jQuery('#tr_position').hide();
        <?php } else {?>
            jQuery('#tr_parent').hide();
            jQuery('#tr_sub_position').hide();
        <?php }?>

        jQuery('.is_sub').mousedown(function(){
            jQuery('#tr_parent').toggle();
            jQuery('#tr_sub_position').toggle();
            jQuery('#tr_position').toggle();
        });

    });
    </script>
<?php
}

add_filter('wm_post_custom_menus','wm_post_custom_menus',10,2);
function wm_post_custom_menus($message, $wm_custom_menus){

    if(wm_get_setting('appid') && wm_get_setting('appsecret')){
        $wm_access_token = wm_get_access_token();
        if($wm_access_token){
            $url =  'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$wm_access_token;
            $request = wm_create_buttons_request($wm_custom_menus);
            $result = wm_post_custom_menus_core($url,urldecode(json_encode($request)));

            $message = $message?$message.'<br />':$message;
            return $message.'微信：'.$result;
        }else{
            return 'No Token';
        }
    }else{
        return 'Error';
    }
    return $message;
}

function wm_post_custom_menus_core($url,$request){

    $response = wp_remote_post($url,array( 'body' => $request,'sslverify'=>false));

    if(is_wp_error($response)){
        return $response->get_error_code().'：'. $response->get_error_message();
    }

    $response = json_decode($response['body'],true);

    if($response['errcode']){
        return $response['errcode'].': '.$response['errmsg'];
    }else{
        return '自定义菜单成功同步';
    }
}

function wm_create_buttons_request($wm_custom_menus){

    $wm_ordered_custom_menus = wm_get_ordered_custom_menus($wm_custom_menus);

    $request = $buttons_json = $button_json = $sub_buttons_json = $sub_button_json = array();

    foreach($wm_ordered_custom_menus as $wm_custom_menu){
        if(isset($wm_custom_menu['parent']) && isset($wm_custom_menu['sub'])){
            $button_json['name']    = urlencode($wm_custom_menu['parent']['name']);

            foreach($wm_custom_menu['sub'] as $wm_custom_menu_sub){
                $sub_button_json['type']    = $wm_custom_menu_sub['type'];
                $sub_button_json['name']    = urlencode($wm_custom_menu_sub['name']);
                if($sub_button_json['type'] == 'click'){
                    $sub_button_json['key']     = urlencode($wm_custom_menu_sub['key']);
                }elseif($sub_button_json['type'] == 'view'){
                    $sub_button_json['url']     = urlencode($wm_custom_menu_sub['key']);
                }
                $sub_buttons_json[]         = $sub_button_json;
                unset($sub_button_json);
            }

            $button_json['sub_button']      = $sub_buttons_json;

            unset($sub_buttons_json);

            $buttons_json[]                 = $button_json;
        }elseif(isset($wm_custom_menu['parent'])){
            $button_json['type']    = $wm_custom_menu['parent']['type'];
            $button_json['name']    = urlencode($wm_custom_menu['parent']['name']);
            if($button_json['type'] == 'click'){
                $button_json['key']     = urlencode($wm_custom_menu['parent']['key']);
            }elseif($button_json['type'] == 'view'){
                $button_json['url']     = urlencode($wm_custom_menu['parent']['key']);
            }
            $buttons_json[]         = $button_json;
        }

        unset($button_json);
    }
    $request['button'] = $buttons_json;
    unset($buttons_json);
    return $request;
}
function wechat_manager_optionpage_reply () {
    global $wpdb, $succeed_msg, $message_table, $plugin_page;
    $message_table = $wpdb->prefix . "wx_message";
    $cacheKey = 'wechat-manager-custom-reply-key';
?>
    <div class="wrap">
        <h2>自定义回复 <small><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&action=add'); ?>" class="add-new-h2">新增自定义回复</a></small></h2>
        <?php
    if ($_GET['id'] && $_GET['action'] == 'update') {
        $id = intval($_GET['id']);
        wp_custom_reply_add($id);
        wp_cache_delete($cacheKey);
    } elseif ($_GET['id'] && $_GET['action'] == 'delete') {
        $id = intval($_GET['id']);
        $redirect = admin_url('admin.php?page='.$plugin_page);
        if($wpdb->delete($message_table, array('id' => $id))){
            $succeed_msg = '删除成功，<a href="' . $redirect . '">返回列表</a>';
            wp_cache_delete($cacheKey);
        } else {
            $succeed_msg = '删除失败，<a href="' . $redirect . '">返回列表</a>';
        }
    } elseif($_GET['action'] == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = intval($_GET['id']);
        $keyword = $_POST['keyword'];
        $type = $_POST['type'];
        $content = $_POST['content'];

        $data = array('keyword' => $keyword, 'type' => $type, 'content' => $content);
        if ($id) {
            $redirect = admin_url('admin.php?page='.$plugin_page.'&action=update&id=' . $id);
            if($wpdb->update($message_table, $data, array('id' => $id))){
                $succeed_msg = '更新成功，<a href="' . admin_url('admin.php?page='.$plugin_page) . '">返回列表</a>';
            } else {
                $succeed_msg = '更新失败，<a href="' . $redirect . '">点此返回</a>';
            }
        } else {
            $redirect = admin_url('admin.php?page='.$plugin_page.'&action=add');
            if (empty($keyword) || empty($type) || empty($content)) {
                $succeed_msg = '所有项都为必填项，<a href="' . $redirect . '">点此返回</a>';
            }
            if ($wpdb->insert($message_table, $data)) {
                $succeed_msg = '添加成功，<a href="' . $redirect . '">继续添加</a>';
            } else {
                $succeed_msg = '添加失败，<a href="' . $redirect . '">点此返回</a>';
            }
        }
        wp_cache_delete($cacheKey);
    } elseif ($_GET['action'] == 'add') {
        wp_custom_reply_add();
        wp_cache_delete($cacheKey);
    } else {
        wm_custom_reply_list();
    }
    if(!empty($succeed_msg)){?>
        <div class="updated">
            <p><?php echo $succeed_msg;?></p>
        </div>
    <?php }
}

function wm_custom_reply_list () {
    global $wpdb, $message_table, $plugin_page;

    $sql = 'SELECT * FROM ' . $message_table . ' ORDER BY id desc';
    $results = $wpdb->get_results($sql, ARRAY_A);
    $type_options = array('text'=>'文本', 'post'=>'文章');
?>
        <table class="widefat" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>关键词</th>
                    <th>类型</th>
                    <th>值</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($results as $i => $r) { ?>
                    <tr <?php if ($i % 2) echo 'class="alternate"';?>>
                        <td><?php echo $r['id'];?></td>
                        <td><?php echo $r['keyword'];?></td>
                        <td><?php echo $type_options[$r['type']];?></td>
                        <td><?php echo $r['content'];?></td>
                        <td><a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&action=update&id=' . $r['id'])?>">修改</a>&nbsp;<a href="<?php echo admin_url('admin.php?page='.$plugin_page.'&action=delete&id=' . $r['id'])?>">删除</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
<?php }

function wp_custom_reply_add ($id = '') {
    global $wpdb, $plugin_page, $message_table;

    $formUrl = admin_url('admin.php?page='.$plugin_page.'&action=save&id=' . $id);
    $id = intval($_GET['id']);
    $sql = 'SELECT * FROM ' . $message_table . ' WHERE id = ' . $id . ' LIMIT 1';
    $reply = $wpdb->get_row($sql);
    $type_options = array('text'=>'文本', 'post'=>'文章');
?>
    <h3 id="edit"><?php echo $id ? '修改' : '新增';?>自定义回复</h3>
    <form method="post" action="<?php echo $formUrl; ?>" id="form">
        <table class="form-table" cellspacing="0">
            <tbody>
                <tr valign="top" id="tr_name">
                    <th scope="row"><label for="name">关键词</label></th>
                    <td>
                        <input type="text" name="keyword" id="keyword" class="type-text regular-text" value="<?php echo $reply ? $reply->keyword : ''; ?>">
                        <p class="description">关键词，不超过15个字节</p>
                    </td>
                </tr>
                <tr valign="top" id="tr_type">
                    <th scope="row"><label for="type">回复类型</label></th>
                    <td>
                        <select name="type" id="type" class="type-select ">
                            <?php foreach ($type_options as $type => $label){ ?>
                            <option value="<?php echo $type;?>" <?php echo ($reply && $reply->type == $type) ? 'selected' : ''; ?>><?php echo $label;?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top" id="tr_key">
                    <th scope="row"><label for="key">值</label></th>
                    <td>
                        <textarea name="content" id="content" class="type-text regular-text" cols='46' rows='6'><?php echo $reply ? $reply->content : '';?></textarea>
                        <p class="description">若类型为文章，则填写文章的ID，多个ID直接以英文逗号分隔；<br>若为文本直接填写即可。</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php wp_nonce_field('wm-options','wm_custom_menu_nonce'); ?>
        <input type="hidden" name="action" value="edit" />
        <p class="submit"><input class="button-primary" type="submit" value="<?php echo $id ? '修改' : '新增';?>" /></p>
    </form>
<?php
}