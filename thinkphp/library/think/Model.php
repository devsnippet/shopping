<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use think\Cache;
use think\Db;
use think\db\Query;
use think\Loader;
use think\model\Relation;

abstract class Model implements \JsonSerializable, \ArrayAccess
{

    // 当前实例
    private static $instance;
    // 数据库对象池
    private static $links = [];
    // 数据库配置
    protected static $connection;
    // 数据表名称
    protected static $tableName;
    // 回调事件
    protected static $event = [];

    // 数据表主键 复合主键使用数组定义
    protected $pk = 'id';
    // 错误信息
    protected $error;
    // 当前模型名称
    protected $name;
    // 字段验证规则
    protected $validate;

    // 字段属性
    protected $field = [];
    // 数据信息
    protected $data = [];
    // 缓存数据
    protected $cache = [];
    // 记录改变字段
    protected $change = [];

    // 保存自动完成列表
    protected $auto = [];
    // 新增自动完成列表
    protected $insert = [];
    // 更新自动完成列表
    protected $update = [];
    // 自动写入的时间戳字段列表
    protected $autoTimeField = ['create_time', 'update_time', 'delete_time'];
    // 时间字段取出后的时间格式
    protected $dateFormat = 'Y-m-d H:i:s';

    // 字段类型或者格式转换
    protected $type = [];
    // 是否为更新数据
    protected $isUpdate = false;
    // 当前执行的关联对象
    protected $relation;
    // 当前模型的父模型
    protected $parent;

    /**
     * 初始化过的模型.
     *
     * @var array
     */
    protected static $initialized = [];

    /**
     * 架构函数
     * @access public
     * @param array|object $data 数据
     */
    public function __construct($data = [])
    {
        if (is_object($data)) {
            $this->data = get_object_vars($data);
        } else {
            $this->data = $data;
        }
        $this->name = basename(str_replace('\\', '/', get_class($this)));

        $this->initialize();
        $this->relation = new Relation($this);
    }

    /**
     *  初始化模型
     *
     * @return void
     */
    protected function initialize()
    {
        $class = get_class($this);
        if (!isset(static::$initialized[$class])) {
            static::$initialized[$class] = true;
            static::init();
        }
    }

    /**
     * 初始化处理
     *
     * @return void
     */
    protected static function init()
    {}

    // JsonSerializable
    public function jsonSerialize()
    {
        return $this->data;
    }

    // ArrayAccess
    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    public function offsetExists($name)
    {
        return $this->__isset($name);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }

    public function offsetGet($name)
    {
        return $this->__get($name);
    }

    /**
     * 设置数据对象值
     * @access public
     * @param mixed $data 数据
     * @return $this
     */
    public function data($data = '')
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        } elseif (!is_array($data)) {
            throw new Exception('data type invalid', 10300);
        }
        $this->data = $data;
        return $this;
    }

    /**
     * 获取对象原始数据
     * @access public
     * @param string $name 字段名 留空获取全部
     * @return array
     */
    public function getData($name = '')
    {
        return isset($this->data[$name]) ? $this->data[$name] : $this->data;
    }

    /**
     * 转换当前模型对象为数组
     * @access public
     * @return array
     */
    public function toArray()
    {
        return !empty($this->data) ? $this->data : [];
    }

    /**
     * 修改器 设置数据对象的值
     * @access public
     * @param string $name 名称
     * @param mixed $value 值
     * @return void
     */
    public function __set($name, $value)
    {
        if (is_null($value) && in_array($name, $this->autoTimeField)) {
            // 自动写入的时间戳字段
            $value = NOW_TIME;
        } else {
            // 检测修改器
            $method = 'set' . Loader::parseName($name, 1) . 'Attr';
            if (method_exists($this, $method)) {
                $value = $this->$method($value, $this->data);
            } elseif (isset($this->type[$name])) {
                // 类型转换
                $type = $this->type[$name];
                switch ($type) {
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'float':
                        $value = (float) $value;
                        break;
                    case 'boolean':
                        $value = (bool) $value;
                        break;
                    case 'datetime':
                        $value = strtotime($value);
                        break;
                    case 'object':
                        if (is_object($value)) {
                            $value = json_encode($value, JSON_FORCE_OBJECT);
                        }
                        break;
                    case 'array':
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        break;
                }
            }
        }

        // 标记字段更改
        if (!isset($this->data[$name]) || ($this->data[$name] != $value && !in_array($name, $this->change))) {
            $this->change[] = $name;
        }
        // 设置数据对象属性
        $this->data[$name] = $value;
    }

    /**
     * 获取器 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name)
    {
        $value = isset($this->data[$name]) ? $this->data[$name] : null;

        // 检测属性获取器
        $method = 'get' . Loader::parseName($name, 1) . 'Attr';
        if (method_exists($this, $method)) {
            return $this->$method($value, $this->data);
        } elseif (!is_null($value) && isset($this->type[$name])) {
            // 类型转换
            $type = $this->type[$name];
            switch ($type) {
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'float':
                    $value = (float) $value;
                    break;
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'datetime':
                    $value = date($this->dateFormat, $value);
                    break;
                case 'array':
                    $value = json_decode($value, true);
                    break;
                case 'object':
                    $value = json_decode($value);
                    break;
            }
        } elseif (is_null($value) && method_exists($this, $name)) {
            // 获取关联数据
            $value = $this->relation->getRelation($name);
            // 保存关联对象值
            $this->data[$name] = $value;
        }
        return $value;
    }

    /**
     * 检测数据对象的值
     * @access public
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * 销毁数据对象的值
     * @access public
     * @param string $name 名称
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * 获取当前模型对象的主键
     * @access public
     * @param string $tableName 数据表名
     * @return mixed
     */
    public function getPk($tableName = '')
    {
        if (!$this->pk) {
            $this->pk = self::db()->getTableInfo($tableName, 'pk');
        }
        return $this->pk;
    }

    /**
     * 判断一个字段名是否为主键字段
     * @access public
     * @param string $key 名称
     * @return bool
     */
    protected function isPk($key)
    {
        $pk = $this->getPk();
        if (is_string($pk) && $pk == $key) {
            return true;
        } elseif (is_array($pk) && in_array($key, $pk)) {
            return true;
        }
        return false;
    }

    /**
     * 保存当前数据对象
     * @access public
     * @param array $data 数据
     * @param array $where 更新条件
     * @return integer
     */
    public function save($data = [], $where = [], $getInsertId = true)
    {
        if (!empty($data)) {
            // 数据对象赋值
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
            if (!empty($where)) {
                $this->isUpdate = true;
            }
        }
        // 数据自动验证
        if (!$this->validateData()) {
            return false;
        }

        // 检测字段
        if (!empty($this->field)) {
            foreach ($data as $key => $val) {
                if (!in_array($key, $this->field)) {
                    unset($data[$key]);
                }
            }
        }

        // 数据自动完成
        $this->autoCompleteData($this->auto);

        // 事件回调
        if (false === $this->trigger('before_write', $this)) {
            return false;
        }

        if ($this->isUpdate) {
            // 自动更新
            $this->autoCompleteData($this->update);
            // 事件回调
            if (false === $this->trigger('before_update', $this)) {
                return false;
            }

            // 去除没有更新的字段
            foreach ($this->data as $key => $val) {
                if (!in_array($key, $this->change) && !$this->isPk($key)) {
                    unset($this->data[$key]);
                }
            }

            $result = self::db()->where($where)->update($this->data);

            // 更新回调
            $this->trigger('after_update', $this);
        } else {
            // 自动写入
            $this->autoCompleteData($this->insert);

            if (false === $this->trigger('before_insert', $this)) {
                return false;
            }

            $result = self::db()->insert($this->data);

            // 获取自动增长主键
            if ($result && $getInsertId) {
                $insertId = self::db()->getLastInsID();
                if (is_string($this->pk) && $insertId) {
                    $this->data[$this->pk] = $insertId;
                }
            }
            // 新增回调
            $this->trigger('after_insert', $this);
        }
        // 写入回调
        $this->trigger('after_write', $this);

        // 标记为更新
        $this->isUpdate = true;
        // 清空change
        $this->change = [];
        return $result;
    }

    /**
     * 保存多个数据到当前数据对象
     * @access public
     * @param array $data 数据
     * @return integer
     */
    public function saveAll($dataSet)
    {
        foreach ($dataSet as $data) {
            $result = $this->isUpdate(false)->save($data, [], false);
        }
        return $result;
    }

    /**
     * 设置允许写入的字段
     * @access public
     * @param bool $update
     * @return $this
     */
    public function allowField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 是否为更新数据
     * @access public
     * @param bool $update
     * @return $this
     */
    public function isUpdate($update = true)
    {
        $this->isUpdate = $update;
        return $this;
    }

    /**
     * 数据自动完成
     * @access public
     * @param array $auto 要自动更新的字段列表
     * @return void
     */
    protected function autoCompleteData($auto = [])
    {
        foreach ($auto as $field => $value) {
            if (is_integer($field)) {
                $field = $value;
                $value = null;
            }
            if (!in_array($field, $this->change)) {
                $this->__set($field, isset($this->data[$field]) ? $this->data[$field] : $value);
            }
        }
    }

    /**
     * 删除当前的记录
     * @access public
     * @return integer
     */
    public function delete()
    {
        if (false === $this->trigger('before_delete', $this)) {
            return false;
        }

        $result = self::db()->delete($this->data);

        $this->trigger('after_delete', $this);
        return $result;
    }

    /**
     * 设置自动完成的字段
     * @access public
     * @param array $fields 需要自动完成的字段（ 规则通过修改器定义）
     * @return $this
     */
    public function auto($fields)
    {
        $this->auto = $fields;
        return $this;
    }

    /**
     * 设置字段验证
     * @access public
     * @param array|bool $rule 验证规则 true表示自动读取验证器类
     * @param array $msg 提示信息
     * @return $this
     */
    public function validate($rule = true, $msg = [])
    {
        if (is_array($rule)) {
            $this->validate = [
                'rule' => $rule,
                'msg'  => $msg,
            ];
        } else {
            $this->validate = true === $rule ? $this->name : $rule;
        }
        return $this;
    }

    /**
     * 自动验证当前数据对象值
     * @access public
     * @return bool
     */
    public function validateData()
    {
        if (!empty($this->validate)) {
            $info = $this->validate;
            if (is_array($info)) {
                $validate = Loader::validate(Config::get('default_validate'));
                $validate->rule($info['rule']);
                $validate->message($info['msg']);
            } else {
                $name = is_string($info) ? $info : $this->name;
                if (strpos($name, '.')) {
                    list($name, $scene) = explode('.', $name);
                }
                $validate = Loader::validate($name);
                if (!empty($scene)) {
                    $validate->scene($scene);
                }
            }
            if (!$validate->check($this->data)) {
                $this->error = $validate->getError();
                return false;
            }
            $this->validate = null;
        }
        return true;
    }

    /**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 注册回调方法
     * @access public
     * @param string $event 事件名
     * @param callable $callback 回调方法
     * @param bool $override 是否覆盖
     * @return void
     */
    public static function event($event, $callback, $override = false)
    {
        if ($override) {
            static::$event[$event] = [];
        }
        static::$event[$event][] = $callback;
    }

    /**
     * 触发事件
     * @access protected
     * @param string $event 事件名
     * @param mixed $params 传入参数（引用）
     * @return bool
     */
    protected function trigger($event, &$params)
    {
        if (isset(static::$event[$event])) {
            foreach (static::$event[$event] as $callback) {
                if (is_callable($callback)) {
                    $result = call_user_func_array($callback, [ & $params]);
                    if (false === $result) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * 写入数据
     * @access public
     * @param array $data 数据数组
     * @return $this
     */
    public static function create($data = [])
    {
        $model = new static();
        $model->isUpdate(false)->save($data);
        return $model;
    }

    /**
     * 更新数据
     * @access public
     * @param array $data 数据数组
     * @param array $where 更新条件
     * @return $this
     */
    public static function update($data = [], $where = [])
    {
        $model = new static();
        $model->isUpdate(true)->save($data, $where);
        return $model;
    }

    /**
     * 查找单条记录
     * @access public
     * @param mixed $data 主键值或者查询条件（闭包）
     * @param string $with 关联预查询
     * @param bool $cache 是否缓存
     * @return \think\Model
     */
    public static function get($data = '', $with = [], $cache = false)
    {
        $db = self::db();
        if ($data instanceof \Closure) {
            call_user_func_array($data, [ & $db]);
            $data = [];
        } elseif ($data instanceof Query) {
            return $data->with($with)->cache($cache)->find();
        }

        $result = self::with($with)->cache($cache)->find($data);

        return $result;
    }

    /**
     * 查找所有记录
     * @access public
     * @param mixed $data 主键列表或者查询条件（闭包）
     * @param string $with 关联预查询
     * @return array|false|string
     */
    public static function all($data = [], $with = [])
    {
        $db = self::db();
        if ($data instanceof \Closure) {
            call_user_func_array($data, [ & $db]);
            $data = [];
        } elseif ($data instanceof Query) {
            return $data->with($with)->select();
        }
        return self::with($with)->select($data);
    }

    /**
     * 删除记录
     * @access public
     * @param mixed $data 主键列表 支持闭包查询条件
     * @return integer
     */
    public static function destroy($data)
    {
        $db = self::db();
        if ($data instanceof \Closure) {
            call_user_func_array($data, [ & $db]);
            $data = [];
        }
        $resultSet = $db->select($data);
        if ($resultSet) {
            foreach ($resultSet as $data) {
                $result = $data->delete();
            }
        }
        return $result;
    }

    /**
     * 命名范围
     * @access public
     * @param string|Closure $name 命名范围名称 逗号分隔
     * @param mixed $params 参数调用
     * @return \think\Model
     */
    public static function scope($name, $params = [])
    {
        $model = new static();
        $class = self::db();
        if ($name instanceof \Closure) {
            call_user_func_array($name, [ & $class, $params]);
        } elseif ($name instanceof Query) {
            return $name;
        } else {
            $names = explode(',', $name);
            foreach ($names as $scope) {
                $method = 'scope' . $scope;
                if (method_exists($model, $method)) {
                    $model->$method($class, $params);
                }
            }
        }
        return $model;
    }

    /**
     * 解析模型的完整命名空间
     * @access public
     * @param string $model 模型名（或者完整类名）
     * @return string
     */
    protected function parseModel($model)
    {
        if (false === strpos($model, '\\')) {
            $path = explode('\\', get_called_class());
            array_pop($path);
            array_push($path, Loader::parseName($model, 1));
            $model = implode('\\', $path);
        }
        return $model;
    }

    /**
     * 查询当前模型的关联数据
     * @access public
     * @param string|array $relations 关联名
     * @return $this
     */
    public function relationQuery($relations)
    {
        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }
        foreach ($relations as $relation) {
            $this->data[$relation] = $this->relation->getRelation($relation);
        }
        return $this;
    }

    /**
     * 获取当前关联信息
     * @access public
     * @param string $name 关联信息
     * @return array|string|integer
     */
    public function getRelationInfo($name = '')
    {
        return $this->relation->getRelationInfo($name);
    }

    /**
     * 预载入关联查询 返回数据集
     * @access public
     * @param array $resultSet 数据集
     * @param string $relation 关联名
     * @return array
     */
    public function eagerlyResultSet($resultSet, $relation)
    {
        return $this->relation->eagerlyResultSet($resultSet, $relation);
    }

    /**
     * 预载入关联查询 返回模型对象
     * @access public
     * @param Model $result 数据对象
     * @param string $relation 关联名
     * @return \think\Model
     */
    public function eagerlyResult($result, $relation)
    {
        return $this->relation->eagerlyResult($result, $relation);

    }

    /**
     * HAS ONE 关联定义
     * @access public
     * @param string $model 模型名
     * @param string $foreignKey 关联外键
     * @param string $localKey 关联主键
     * @return \think\db\Query|string
     */
    public function hasOne($model, $foreignKey = '', $localKey = '')
    {
        // 记录当前关联信息
        $model      = $this->parseModel($model);
        $localKey   = $localKey ?: $this->pk;
        $foreignKey = $foreignKey ?: Loader::parseName($this->name) . '_id';
        return $this->relation->hasOne($model, $foreignKey, $localKey);
    }

    /**
     * BELONGS TO 关联定义
     * @access public
     * @param string $model 模型名
     * @param string $localKey 关联主键
     * @param string $foreignKey 关联外键
     * @return \think\db\Query|string
     */
    public function belongsTo($model, $localKey = '', $foreignKey = '')
    {
        // 记录当前关联信息
        $model      = $this->parseModel($model);
        $foreignKey = $foreignKey ?: $this->pk;
        $localKey   = $localKey ?: Loader::parseName(basename(str_replace('\\', '/', $model))) . '_id';
        return $this->relation->belongsTo($model, $foreignKey, $localKey);
    }

    /**
     * HAS MANY 关联定义
     * @access public
     * @param string $model 模型名
     * @param string $foreignKey 关联外键
     * @param string $localKey 关联主键
     * @return \think\db\Query|string
     */
    public function hasMany($model, $foreignKey = '', $localKey = '')
    {
        // 记录当前关联信息
        $model      = $this->parseModel($model);
        $localKey   = $localKey ?: $this->pk;
        $foreignKey = $foreignKey ?: Loader::parseName($this->name) . '_id';
        return $this->relation->hasMany($model, $foreignKey, $localKey);
    }

    /**
     * BELONGS TO MANY 关联定义
     * @access public
     * @param string $model 模型名
     * @param string $table 中间表名
     * @param string $localKey 当前模型关联键
     * @param string $foreignKey 关联模型关联键
     * @return \think\db\Query|string
     */
    public function belongsToMany($model, $table = '', $localKey = '', $foreignKey = '')
    {
        // 记录当前关联信息
        $model      = $this->parseModel($model);
        $table      = $table ?: Db::name(strtolower($this->name . '_' . basename(str_replace('\\', '/', $model))))->getTableName();
        $localKey   = $localKey ?: Loader::parseName(basename(str_replace('\\', '/', $model))) . '_id';
        $foreignKey = $foreignKey ?: strtolower($this->name) . '_id';

        return $this->relation->belongsToMany($model, $table, $localKey, $foreignKey);
    }

    /**
     * 初始化数据库对象
     * @access public
     * @return \think\db\Driver
     */
    public static function db()
    {
        $model = get_called_class();

        if (!isset(self::$links[$model])) {
            self::$links[$model] = Db::connect(static::$connection);
        }
        self::$links[$model]->setTable(static::$tableName);
        $name = basename(str_replace('\\', '/', $model));
        self::$links[$model]->name($name);
        // 设置当前模型 确保查询返回模型对象
        self::$links[$model]->model($model);
        // 返回当前数据库对象
        return self::$links[$model];
    }

    public function __call($method, $args)
    {
        if (method_exists($this, 'scope' . $method)) {
            // 动态调用命名范围
            $method = 'scope' . $method;
            $class  = self::db();
            array_unshift($args, $class);
            call_user_func_array([$this, $method], $args);
            return $this;
        } else {
            throw new Exception(__CLASS__ . ':' . $method . ' method not exist');
        }
    }

    public static function __callStatic($method, $params)
    {
        return call_user_func_array([self::db(), $method], $params);
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    /**
     * 解序列化后处理
     */
    public function __wakeup()
    {
        $this->initialize();
    }

}