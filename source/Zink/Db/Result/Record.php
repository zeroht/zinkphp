<?php
/**
 * 数据记录类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/13 @thu: 创建；
 */

namespace Zink\Db\Result;

use Common\Constant;
use Zink\Core\DbSupport;
use Zink\Db\Condition\Equal;
use Zink\Db\DB;
use Zink\Db\Where\WhereAnd;
use Zink\Widget\ArrayObject;
use Zink\Widget\Json;

class Record extends ArrayObject
{
    use DbSupport;
    
    /**
     * 扩展方式
     */
    const EXT_JSON = 'json';            // json字符串转成数组
    const EXT_SPLIT = 'split';          // 字符串扩切分扩展成数组，默认切分符为','
    const EXT_SYMBOL = 'symbol';        // 标识扩展为文字描述，如状态码
    const EXT_IMAGE = 'image';          // oss,id=>url
    const EXT_FUNCTION = 'function';    // 自定义方法扩展，方法名约定为：_extend_{$field}

    /**
     * @type string 主键名,约定所有表都有递增主键id
     */
    protected $_pk = 'id';

    /**
     * @type array 修改过的值
     */
    protected $_changes = array();

    protected $_fromDb = false;

    /**
     * 数据表默认存在 created_at 和 updated_at 字段
     * @var bool 自动管理created_at和updated_at
     */
    protected $_timestamps = true;

    /**
     * 自动扩展属性列表
     * [$field, $rule, $argv]
     * @type array
     */
    protected $_extends = [
        ['status', self::EXT_SYMBOL],
        ['image_id', self::EXT_IMAGE]
    ];

    /**
     * 字段值符号对应文本
     * @var array
     */
    protected static $_symbol2text = [];

    /**
     * Record constructor.
     * @param null $data
     * @param bool $fromDb
     */
    public function __construct($data = null, $fromDb = false)
    {
        $class_segs = array_reverse(explode("\\", get_called_class()));
        $className = $class_segs[0];

        if (!$this->_table && $className != 'Record') {
            // 默认为orm类名，可以在子类中重新赋值
            $this->_table = $className;
        }

        if (!$this->_dbName && $class_segs[2] == 'Pdo') {
            // 默认小写,与子命名空间同名，可以在子类中重新赋值
            $this->_dbName = strtolower($class_segs[1]);
        }

        $this->_initDB();
        
        $this->_fromDb = $fromDb;
        if ($fromDb) {
            parent::__construct($data);
            // 数据库数据自动扩展属性方便读取
            $this->_autoExtends();
        }else {
            // 新创建的数据才设置默认值
            $this->_setDefault();
            parent::__construct($data);
        }
    }

    /**
     * 放一些默认值操作，子类继承定义自己的默认值
     */
    protected function _setDefault()
    {
        // TODO: Nothing.
        $now = date('Y-m-d H:i:s');
        $this->set('created_at', $now);
        $this->set('updated_at', $now);

        $fields = $this->_db->getFields();
        foreach ($fields as $field => $info){
            if ($info['type'] == 'text'){
                // text 新增数据时,字段必须设置默认值
                $this->set($field, '');
            }
        }
    }
    
    public function autoExtends()
    {
        $this->_autoExtends();
    }

    /**
     * 自动扩展一些属性，子类通过重新定义 $_autoExtends变量来配置自动扩展属性的规则
     * 扩展规则(默认function):
     *  1. json: 转成数组覆盖原字段值;
     *  2. symbol: 新字段名为 原字段名 加 '_text'后缀
     *  3. 其它, 新字段名为 原字段名 加 '_ext'后缀(数据库字段应避免使用此后缀)
     */
    protected function _autoExtends()
    {
        foreach ($this->_extends as $extend){

            $field = array_key_exists(0, $extend) ? $extend[0] : null;
            $function = array_key_exists(1, $extend) ? $extend[1] : null;
            $argv = array_key_exists(2, $extend) ? $extend[2] : null;
            if ($function != self::EXT_FUNCTION && !$this->isExisted($field)){
                continue;
            }

            $newKey = "{$field}_ext";
            $newValue = $this->get($field);
            switch ($function){
                case self::EXT_JSON :{
                    $newValue = Json::json2array($newValue);
                    break;
                }
                case self::EXT_SPLIT :{
                    // 默认','分隔数据
                    $delimiter = $argv ? $argv : ',';
                    $newValue = $newValue ? explode($delimiter, $newValue) : [];
                    break;
                }
                case self::EXT_SYMBOL :{
                    $newKey = "{$field}_text";
                    $newValue = self::symbol2text($field, $newValue);
                    break;
                }
                case self::EXT_IMAGE : {
                    $newKey = "{$field}_ext";
                    $newValue = $newValue ? Constant::get('OSS_IMAGE_HOST').$newValue : "";
                    break;
                }
                case self::EXT_FUNCTION :
                default:{
                    /**
                     * 自定义方法处理，命名约定为 protected _extend_{$field}
                     * 方法为定义会报错(程序员bug)
                     */
                    $method = '_extend_'.$field;
                    if (is_callable($argv)){
                        $newValue = $argv($newValue);
                    }else if (method_exists($this, $method)){
                        $newValue = $this->$method();
                    }
                    
                    break;
                }
            }

            parent::set($newKey, $newValue);
        }
    }

    /**
     * 获取符号对应文本描述;如获取状态码对一个的中文描述
     * @param $field
     * @param $symbol
     * @return string
     */
    public static function symbol2text($field, $symbol)
    {
        if (isset(self::$_symbol2text[$field][$symbol])){
            return self::$_symbol2text[$field][$symbol];
        }

        return $symbol;
    }

    /**
     * 获取主键key名
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->_pk;
    }

    /**
     * 获取主键key的值
     * @return null
     */
    public function getPrimaryKeyValue()
    {
        if (!$this->_pk) {
            return NULL;
        }

        return parent::get($this->_pk);
    }

    /**
     * 魔法函数,可以直接通过->访问记录属性值;如果属性值不存在,
     * 可以调用自定义的"_join{$key}"方法获取自定义属性值
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = parent::__get($key);
        // 可以在子类中定义 protected 方法,自定义数据来源
        $joinMethod = "_join{$key}";
        if ($value === null && method_exists($this, $joinMethod)){
            // 关系查询
            $value = $this->$joinMethod();
            // 缓存起来
            parent::set($key, $value);
        }

        return $value;
    }

    /**
     * 修改属性值
     * @param $key
     * @param $value
     * @param bool $toJson 是否保存为json格式
     */
    public function set($key, $value, $toJson = false)
    {
        if ($toJson){
            $value = empty($value) ? '' : Json::array2json($value);
        }

        parent::set($key, $value);

        if ($key != $this->_pk) {
            $this->_changes[$key] = $value;
        }
    }

    /**
     * @param array|null $forceRestrictList
     * @return bool
     * @throws \Zink\Exception\RuntimeException
     */
    public function save(array $forceRestrictList = null)
    {
        $pk = $this->getPrimaryKey();
        $pkValue = $this->getPrimaryKeyValue();
        if ($this->_fromDb) {
            // 主键值存在,update
            if ($this->_timestamps && !isset($this->_changes['updated_at'])) {
                $this->_changes['updated_at'] = date('Y-m-d H:i:s');
            }
            
            if (!$forceRestrictList) {
                return $this->_db->where(new Equal($pk, $pkValue))->update($this->_changes);
            }

            $condition = new WhereAnd([
                new Equal($pk, $pkValue)
            ]);

            foreach ($forceRestrictList as $forceRestrictKey => $forceRestrictValue) {
                $condition->andCondition(new Equal($forceRestrictKey, $forceRestrictValue));
            }

            return $this->_db->where($condition)->update($this->_changes, true);
        } else if ($this->_db->insert($this->toArray())) {
            // 插入
            parent::__set($pk, $this->_db->insertId());
            return TRUE;
        }

        return FALSE;
    }

    /**
     * @return bool
     * @throws \Zink\Exception\RuntimeException
     */
    public function remove()
    {
        if (!$this->_fromDb){
            return FALSE;
        }

        $pk = $this->getPrimaryKey();
        $pkValue = $this->getPrimaryKeyValue();
        return $this->_db->where(new Equal($pk, $pkValue))->delete();
    }

    /**
     * 1对1关联查询
     *
     * @param  string  $table 关联表名
     * @param  string  $foreignKey 关联表外健,默认为 '本表表名_id'
     * @param  string  $localKey 本表外健,默认为本表主键
     * @param string $otherFields 连表查询字段
     * @return Record
     */
    protected function _hasOne($table, $foreignKey = null, $localKey = null, $otherFields = '*')
    {
        $foreignKey = $foreignKey ? $foreignKey : "{$this->_table}_{$this->_pk}";
        $localKey = $localKey ? $localKey : $this->_pk;
        $localValue = parent::__get($localKey);

        $db = DB::create($table, $this->_dbName);
        return $db->where(new Equal($foreignKey, $localValue))->field($otherFields)->first();
    }

    /**
     * 1对1关联查询(与_hasOne相反)
     *
     * @param string  $table 关联表名
     * @param string  $foreignKey 本表外健,默认为 '关联表名_id'
     * @param string  $otherKey 关联表外健,默认为'id'
     * @param string $otherFields 连表查询字段
     * @return Record
     */
    protected function _belongsTo($table, $foreignKey = null, $otherKey = null, $otherFields = '*')
    {
        $foreignKey = $foreignKey ? $foreignKey : "{$table}_{$this->_pk}";
        $otherKey = $otherKey ? $otherKey : $this->_pk;
        $foreignValue = parent::__get($foreignKey);

        $db = DB::create($table, $this->_dbName);
        return $db->where(new Equal($otherKey, $foreignValue))->field($otherFields)->first();
    }

    /**
     * 1对多关联查询
     *
     * @param string  $table 关联表名
     * @param string  $sort 排序
     * @param mixed $limit 分页,默认 0,1000
     * @param string  $foreignKey 关联表外健,默认为 '本表表名_id'
     * @param string  $localKey 本表外健,默认为本表主键
     * @param string $otherFields 连表查询字段
     * @return Collection
     */
    protected function _hasMany($table, $sort = null, $limit = 1000, $foreignKey = null,
            $localKey = null, $otherFields = '*')
    {
        $foreignKey = $foreignKey ? $foreignKey : "{$this->_table}_{$this->_pk}";
        $localKey = $localKey ? $localKey : $this->_pk;
        $localValue = parent::__get($localKey);

        $db = DB::create($table, $this->_dbName);
        return $db->where(new Equal($foreignKey, $localValue))->field($otherFields)
            ->orderby($sort)->limit($limit)->select();
    }

    /**
     * 删除改变的属性值
     * @param $key
     */
    public function removeChangeSet($key)
    {
        if ($key != $this->_pk) {
            unset($this->_changes[$key]);
        }
    }
}

/* End of file Record.php */
