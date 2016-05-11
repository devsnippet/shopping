<?php
namespace app\admin\controller\base;

use \think\Config;
use \think\Controller;
use \think\Session;
use \Tp5Tool\Auth\Auth;

class Base extends Controller
{
    /**
     * 不登录可以访问的模块
     */
    const NO_AUTH = 'main';

    public function _initialize()
    {
        if (IS_AJAX) {
            Config::set('default_return_type', 'json');
        }

        if (strtolower(CONTROLLER_NAME) != self::NO_AUTH) {
            if (!Session::has('U.id')) {
                echo 1;
                exit;
            }
            $userId = Session::get('U.id');

            $loginUserModel = \app\admin\model\User::find($userId);

            $name = strtolower(CONTROLLER_NAME) . '/' . strtolower(ACTION_NAME);

            $void = (new Auth($loginUserModel))->can([$name], true);

            if ($void === false) {
                echo 2;
                exit;
            }
        }
    }
}
