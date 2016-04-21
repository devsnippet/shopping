<?php
namespace app\common\validate;

use \think\Db;

trait Validate
{
    /**
     * 验证是否存在！
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-19T09:48:57+0800
     * @param    [type]                   $roleId [description]
     * @return   [type]                           [description]
     */
    public function exist($value, $rule, $data)
    {
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
