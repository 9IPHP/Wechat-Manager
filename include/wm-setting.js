jQuery(function ($) {
    //上传单个图片
    $(document).on("click", '.wm_upload', function (e) {
        e.preventDefault(); // 阻止事件默认行为。

        var $prev_input = $(this).prev("input");

        var custom_uploader = wp.media({
            title: '选择图片',
            library: {type: 'image'},
            button: {text: '选择图片'},
            multiple: false
        }).on('select', function () {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $prev_input.val(attachment.url);
            $('.media-modal-close').trigger('click');
        }).open();

        return false;
    });

    var $menuType = $("#menu_type");

    if ($menuType.length > 0) {
        $menuType.change(function () {
            var type = $(this).val();
            if (type === 'click') {
                $("#type_click").show();
                $("#type_url").hide();
            } else {
                $("#type_click").hide();
                $("#type_url").show();
            }
        });
        $('#menu_type').change();
    }
})