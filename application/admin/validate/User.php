<?php
namespace app\admin\validate;

use \think\Validate;

class User extends Validate
{
    use \app\common\validate\Validate;

    protected $rule = [
        'name'        => ['require', 'unique:user', 'length:3,25'],
        'email'       => ['require', 'email', 'unique:user'],
        'email_login' => ['require', 'email'],
        'password'    => ['require', 'length:6,20'],
        'repassword'  => ['require', 'confirm:password'],
        'sex'         => ['in:0,1,2'],
        'birthday'    => ['dateFormat:y-m-d'],
        'role_id'     => ['exist:role,id'],
        'status'      => ['in:0,1'],
    ];

    protected $message = [
        'name.require'    => '用户名必须填写',
        'name.unique'     => '用户名已存在',
        'name.length'     => '用户名必须大于3个字符小于25个字符',
        'email.require'   => '邮箱地址必须填写',
        'email.unique'    => '邮箱地址已存在',
        'email.email'     => '邮箱地址格式不正确',
        'role_id.require' => '所属角色必须填写',
        'role_id.exist'   => '所属角色不存在',
        'status.in'       => '状态值不可用',
    ];

    protected $scene = [
        'add'          => ['name', 'email', 'password', 'role_id', 'sex', 'birthday'],
        'edit'         => ['role_id', 'status', 'sex', 'birthday'],
        'editpassword' => ['password', 'repassword'],
        'editstatus'   => ['status'],
        'login'        => ['email_login', 'password'],
    ];
}
