<?php
namespace app\admin\model;

use think\Model;

class Role extends Model
{
    protected $autoTimeField = ['create_time', 'update_time'];
    protected $insert        = ['create_time', 'update_time'];
    protected $update        = ['update_time'];

    protected $dateFormat = 'Y/m/d';
    protected $type       = [
        'id'          => 'integer',
        'status'      => 'integer',
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
}
