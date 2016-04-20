<?php
namespace app\admin\validate;

use \think\Validate;

class Role extends Validate
{
    use \app\common\validate\Validate;

    protected $rule = [
        'name'   => ['require', 'unique:role', 'length:3,25'],
        'status' => ['require', 'in:0,1'],
        'remark' => ['max:250'],
    ];

    protected $message = [
        'name.require'  => '角色名称必须填写',
        'name.unique'   => '角色名称已存在',
        'name.length'   => '角色名称必须大于3个字符小于25个字符',
        'remark.remark' => '角色说明长度不能超过250个字符',
        'status.in'     => '状态值不可用',
    ];

    protected $scene = [
        'add'        => ['name', 'status', 'remark'],
        'edit'       => ['name', 'status', 'remark'],
        'editstatus' => ['status'],
    ];
}
