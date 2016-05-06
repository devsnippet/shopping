<?php
namespace app\admin\controller\base;

use \think\Controller;

class Base extends Controller
{
    /**
     * [iframeReturn description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-06T17:00:29+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    protected function iframeReturn(array $data, $status = 0)
    {
        $result = array(
            'status' => $status,
            'data'   => $data,
        );
        $result = json_encode($result);
        return '<script type="text/javascript">window.parent.iframeReturn(' . $result . ');</script>';
    }
}
