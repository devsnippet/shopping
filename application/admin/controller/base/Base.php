<?php
namespace app\admin\controller\base;

use \think\Config;
use \think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        if (IS_AJAX) {
            Config::set('default_return_type', 'json');
        }

        // 控制器初始化
        if (method_exists($this, '_initAuth')) {
            $this->_initAuth();
        }

    }
}
