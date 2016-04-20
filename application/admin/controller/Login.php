<?php
namespace app\admin\controller;

use \app\admin\validate\Role as Validate;
use \think\Controller;

class Login extends Controller
{
    public function index()
    {
        header("Content-type:text/html;charset=utf-8");

        $data = [
            'name'   => '网站管理1员',
            'status' => 1,
            'remark' => '网站管理员',
        ];
        $role = model('Role');

        $data = $role->validate('Role')->isUpdate(false)->save($data);

        if (!$data) {
            dump($role->getError());
        }

        // dump($data->status);
    }
}
