<?php
namespace app\admin\controller;

use app\admin\controller\base\Base;
use \think\Loader;

class Main extends Base
{
    /**
     * 登录
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-06T16:30:12+0800
     * @return   [type]                   [description]
     */
    public function login()
    {
        return $this->fetch();
    }
    /**
     * [doLogin description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-06T16:30:31+0800
     * @return   [type]                   [description]
     */
    public function doLogin()
    {
        if (!IS_POST) {
            return $this->login();
        }

        $data = input('post.');

        $validate = Loader::validate('User');
        $result   = $validate->scene('login')->batch()->check($data);

        if ($result === false) {
            return $this->iframeReturn($validate->getError());
        }

    }
}
