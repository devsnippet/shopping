<?php
namespace app\admin\model;

use think\Model;

class User extends Model
{
    protected $autoTimeField = ['create_time', 'update_time'];
    protected $insert        = ['create_time', 'password', 'update_time'];
    protected $update        = ['update_time', 'password'];

    protected $dateFormat = 'Y-m-d';
    protected $type       = [
        'id'          => 'integer',
        'role_id'     => 'integer',
        'status'      => 'integer',
        'sex'         => 'integer',
        'birthday'    => 'datetime',
        'create_time' => 'datetime',
        'update_time' => 'datetime',
    ];

    /**
     * 获取状态
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-19T16:00:40+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    public function getStatusAttr($value)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$value];
    }

    /**
     * 获取性别
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-19T16:00:40+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    public function getSexAttr($value)
    {
        $status = [0 => '保密', 1 => '男', 2 => '女'];
        return $status[$value];
    }

    /**
     * 设置密码
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-19T15:58:11+0800
     * @param    [type]                   $value [description]
     */
    protected function setPasswordAttr($password)
    {
        return md5($password);
    }

}
