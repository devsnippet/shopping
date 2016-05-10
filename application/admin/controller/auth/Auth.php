<?php
namespace app\admin\controller\auth;

use app\admin\model\User;
use \think\Config;
use \think\Session;
/**
 * 验证类
 */
trait Auth
{
    protected $userId = false;

    protected function _initAuth()
    {
        //判断登录
        $islogin = Session::has('U.id');
        if ($islogin == false) {
            $this->redirect('main/login');
        }

        $name = CONTROLLER_NAME . '/' . ACTION_NAME;

        $saveSesssion = Config::get('auth.auth_session');

        $authList = array();
        if ($saveSesssion == true) {
            if (Session::has('A')) {
                $authList = Session::get('A');

                if (isset($authList[md5($name)])) {
                    return true;
                }
            }
        }

        $userId = Session::get('U.id');

        $loginUserModel = User::find($userId);

        $roleModel = $loginUserModel->role;

        if ($roleModel->status == 0) {
            Session::clear();
            echo $this->error('用户组不可用.', '/admin/main/login');
            exit;
        }

        $rule = $roleModel->rules()->where('name', $name)->find();

        if ($rule === false) {
            echo $this->error('没有操作权限.', '/admin/index/main');
            exit;
        }

        if ($saveSesssion == true) {
            $authList[md5($name)] = $name;
            Session::set('A', $authList);
        }

    }
}
