<?php
namespace app\admin\controller;

use \think\Controller;

class Login extends Controller
{
    public function index()
    {
        header("Content-type:text/html;charset=utf-8");
        $data = [
            'name'       => 'Luffy Zhao',
            'email'      => 'luffyzhao@vip.126.com',
            'password'   => '123456',
            'repassword' => '123456',
            'sex'        => 0,
            'birthday'   => '2016-04-05',
            'role_id'    => 1,
            'status'     => 0,
        ];
        $result = $this->validate($data, 'User.add');

        // if ($result === false) {
        dump($result);
        // }
        unset($data['repassword']);
        $users = \app\admin\model\User::get(2);
        dump($users);
    }
}
