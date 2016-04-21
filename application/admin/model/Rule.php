<?php
namespace app\admin\model;

use think\Model;

class Rule extends Model
{

    protected $type = [
        'id'        => 'integer',
        'parent_id' => 'integer',
        'islink'    => 'integer',
        'sort'      => 'integer',
    ];

    /**
     * 获取状态
     * @author luffy<luffyzhao@vip.126.com>
     * @dateTime 2016-04-19T16:00:40+0800
     * @param    string                   $value [description]
     * @return   [type]                          [description]
     */
    public function getIslinkAttr($islink)
    {
        $islinks = [0 => '操作', 1 => '菜单'];
        return $islinks[$islink];
    }

}
