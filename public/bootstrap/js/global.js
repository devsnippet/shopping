$._messengerDefaults = {
    extraClasses: 'messenger-fixed messenger-on-top messenger-on-right',
    'theme': "air"
}


$.extend({
    buttonObject: false,
    /**
     * 错误信息提示
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-06T17:43:43+0800
     * @param    {[type]}                 info [description]
     * @return   {[type]}                      [description]
     */
    messageError: function(info) {
        this.globalMessenger().post({
            message: info,
            type: 'error'
        });
    },
    /**
     * 成功信息提示
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-06T17:43:57+0800
     * @param    {[type]}                 info [description]
     * @return   {[type]}                      [description]
     */
    messageSuccess: function(info) {
        this.globalMessenger().post({
            message: info,
            type: 'success'
        });
    },

    /**
     * 提交按钮禁用
     * @return {[type]} [description]
     */
    buttonDisabled: function() {
        this.buttonObject.attr('disabled', 'disabled');
        this.buttonObject.removeClass('btn-info');
        this.buttonObject.addClass('btn-warning');
        this.buttonObject.html('<i class="icon-spinner icon-spin"></i> 请稍后...');
    },

    /**
     * 提交铵钮启用
     * @return {[type]} [description]
     */
    buttonEnable: function() {
        this.buttonObject.removeClass('btn-warning');
        this.buttonObject.addClass('btn-info');
        this.buttonObject.html('提交');
        this.buttonObject.removeAttr('disabled');
    },
    /**
     * ajax请求成功数据处理
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-06T17:19:26+0800
     * @param    {[type]}                 result [description]
     * @return   {[type]}                        [description]
     */
    ajaxDoneResult: function(result) {
        if (result.status == 1) {
            if (typeof result.url == 'undefined') {
                result.url = '/admin/index/index';
            }

            if(typeof result.data == 'undefined'){
                result.data = '成功！';
            }

            $.messageSuccess(result.data);

            setTimeout(function() {
                    location.href = result.url;
                },
                1000);


        } else {
            if (typeof result.data == 'string') {
                $.messageError(result.data);
            } else {
                for (i in result.data) {
                    $.messageError(result.data[i]);
                }
            }
        }

        $.buttonEnable();
    },
    /**
     * ajax请求失败数据处理
     * @param  {[type]} result [description]
     * @return {[type]}        [description]
     */
    ajaxFailResult: function(result) {
        $.messageError('网络错误,请联系管理员');
        $.buttonEnable();
    },

});


$.fn.extend({
    /**
     * ajax提交
     * @return {[type]} [description]
     */
    formLuffyZhao: function(fields, is_ajax) {
        var me = $(this);

        if (typeof(is_ajax) == 'undefined') {
            is_ajax = true;
        }

        $(this).submit(function(event) {
            $.buttonObject = $(this).find('button[type="submit"]');

            var action = $(this).attr('action'),
                method = $(this).attr('method'),
                data = $(this).serialize();

            // 按钮禁用
            $.buttonDisabled();

            if (typeof method == 'undefined' || method.length == 0) {
                method = 'get';
            }

            if (typeof action == 'undefined' || action.length == 0) {
                $.messageError('内部错误！');
                // 启用按钮
                $.buttonEnable();
            } else {
                var validator = new FormValidator(me.attr('name'), fields, function(errors) {
                    if (errors.length > 0) {
                        for (variable in errors) {
                            for (var i = 0; i < errors[variable].messages.length; i++) {
                                $.messageError(errors[variable].messages[i]);
                            }
                            // $.messageError(errors[variable].message);
                        }
                    }
                });
                // 表单验证
                if (validator.errors.length !== 0) {
                    $.buttonEnable();
                    return false;
                }
                // event.preventDefault();
                if (is_ajax == true) {
                    event.preventDefault();
                    $.ajax({
                            url: action,
                            type: method,
                            dataType: 'json',
                            data: data,
                        })
                        .done($.ajaxDoneResult)
                        .fail($.ajaxFailResult);
                }
                // ajax提交                
            }
        });
    }
});
