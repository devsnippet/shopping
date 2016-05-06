$._messengerDefaults = {
    extraClasses: 'messenger-fixed messenger-on-top messenger-on-right',
    'theme':"air"
}

/**
 * [iframeReturn description]
 * @author luffy<luffyzhao@vip.126.com>
 * @dateTime 2016-05-06T17:19:26+0800
 * @param    {[type]}                 result [description]
 * @return   {[type]}                        [description]
 */
function iframeReturn (result) {
     if(result.status == 1){

     }else{
        for(i in result.data){
            messageError(result.data[i]);
        }
     }
}
/**
 * 成功信息提示
 * @author luffy<luffyzhao@vip.126.com>
 * @dateTime 2016-05-06T17:43:57+0800
 * @param    {[type]}                 info [description]
 * @return   {[type]}                      [description]
 */
function messageSuccess(info) {
     alienAttemptMsg = $.globalMessenger().post({
          message: info,
          type: 'success'
    });
}
/**
 * 错误信息提示
 * @author luffy<luffyzhao@vip.126.com>
 * @dateTime 2016-05-06T17:43:43+0800
 * @param    {[type]}                 info [description]
 * @return   {[type]}                      [description]
 */
function messageError(info) {
     alienAttemptMsg = $.globalMessenger().post({
          message: info,
          type: 'error'
    });
}