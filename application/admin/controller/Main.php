<?php
namespace app\admin\controller;

use app\admin\controller\base\Base;
use \think\Input;
use \think\Loader;
use \think\Session;
use \think\Url;

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
            return ['status' => 0, 'data' => '未知错误,请联系管理员！'];
        }

        $data = Input::param();

        $validate = Loader::validate('User');
        $result   = $validate->scene('login')->batch()->check($data);

        if ($result === false) {
            return ['status' => 0, 'data' => $validate->getError()];
        }

        $userModel = Loader::model('User');

        $userRow = $userModel->login([
            'email'    => $data['email_login'],
            'password' => $data['password'],
        ]);

        if ($userRow === false) {
            return ['status' => 0, 'data' => $userModel->getError()];
        }

        Session::set('U', $userRow);

        return ['status' => 1, 'url' => Url::build('admin/index/index')];
    }

    /**
     * [logout description]
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-10T12:54:49+0800
     * @return   [type]                   [description]
     */
    public function logout()
    {
        Session::clear();

        return $this->success('退出成功！', 'login');
    }
}
