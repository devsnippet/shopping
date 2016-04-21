<?php
namespace app\admin\validate;

use \think\Validate;

class User extends Validate
{
    use \app\common\validate\Validate;

    protected $rule = [
        'name'      => ['require', 'unique:rule', 'length:3,25'],
        'title'     => ['require', 'unique:rule', 'length:3,25'],
        'parent_id' => ['require', 'existPId:rule,id'],
        'islink'    => ['in:0,1'],
        'sort'      => ['number', 'between:0,255'],
    ];

    protected $message = [
        'name.require'       => '权限&菜单必须填写',
        'name.unique'        => '权限&菜单已存在',
        'name.length'        => '权限&菜单必须大于3个字符小于25个字符',
        'title.require'      => '权限菜单名称必须填写',
        'title.unique'       => '权限菜单名称已存在',
        'title.length'       => '权限菜单名称必须大于3个字符小于25个字符',
        'parent_id.require'  => '上级菜单必须填写',
        'parent_id.existPId' => '上级菜单选值不正确',
        'islink.in'          => '是否菜单选值不正确',
        'sort.number'        => '排序只能是一个数字',
        'sort.between'       => '排序范围值只能在0-255之间',
    ];

    protected $scene = [
        'add'        => ['name', 'title', 'parent_id', 'islink', 'sort'],
        'edit'       => ['name', 'title', 'parent_id', 'islink', 'sort'],
        'editstatus' => ['sort'],
    ];

    /**
     * PID是否存在
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-21T15:57:26+0800
     * @param    [type]                   $value [description]
     * @param    [type]                   $rule  [description]
     * @param    [type]                   $data  [description]
     * @return   [type]                          [description]
     */
    public function existPId($value, $rule, $data)
    {
        if ($value == 0) {
            return true;
        }
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }
        $db    = Db::table($rule[0]);
        $field = isset($rule[1]) ? $rule[1] : 'id';
        $map   = [$field => $value];

        if ($db->where($map)->field($field)->find()) {
            return true;
        }
        return false;
    }
}

/**
id:自增ID 主键
parent_id:父ID
name:菜单&权限
title:菜单&权限名称
icon:图标
islink:是否菜单
sort:排序
 */
