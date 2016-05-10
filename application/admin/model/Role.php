<?php
namespace app\admin\model;

use \think\Model;
use \think\Session;

class Role extends Model
{

    protected $autoTimeField = ['create_time', 'update_time'];
    protected $insert        = ['create_time', 'update_time'];
    protected $update        = ['update_time'];

    protected $dateFormat = 'Y-m-d';
    protected $type       = [
        'id'          => 'integer',
        'status'      => 'integer',
        'create_time' => 'datetime',
        'update_time' => 'datetime',
    ];

    /**
     * 关联菜单&权限表
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-21T16:27:30+0800
     * @return   [type]                   [description]
     */
    public function rules()
    {
        return $this->belongsToMany('Rule', 'role_rule');
    }

    /**
     * 获取登录用户的菜单
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-05-10T15:44:31+0800
     * @return   [type]                   [description]
     */
    public function getMenu()
    {
        $roleId = Session::get('U.role_id');

        //sql : SELECT rule.*,pivot.role_id AS pivot__role_id,pivot.rule_id AS pivot__rule_id FROM `rule` INNER JOIN role_rule pivot ON pivot.role_id=rule.id WHERE pivot.role_id = 1
        $this->get($roleId)->rules;
        // sql : SELECT * FROM `rule` WHERE `islink` = 1
        // $this->get($roleId)->rules()->where('islink', 1)->select();

        exit;

        $menuRows = Role::get($roleId)->rules()->where('islink', 1)->select();

        $menuData = array();

        foreach ($menuRows as $value) {
            if ($value->parent_id > 0) {
                $menuData[$value->parent_id]['sub'] = $value->toArray;
            } else {
                $menuData[$value->id] = $value->toArray;
            }
        }

        return $menuData;
    }

    /**
     * 获取状态
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-19T16:00:40+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '禁用', 1 => '启用'];
        return $status[$data['status']];
    }

}
