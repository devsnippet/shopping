<?php
namespace app\admin\controller\auth;

use \think\Session;

/**
 * 验证类
 */
trait Auth
{
    protected $userId = false;
    public function _initialize()
    {
        $userId = Session::set('user.id');
        if ($userId == false) {
            $this->redirect('main/login');
        }
    }
}
