<?php
namespace app\admin\controller;

use app\admin\controller\auth\Auth;
use app\admin\controller\base\Base;
use \think\Loader;

class Index extends Base
{
    use Auth;

    public function index()
    {
        $roleModel = Loader::model('Role');
        $menuData  = $roleModel->getMenu();

        dump($menuData);exit;

        return $this->fetch();
    }
}
