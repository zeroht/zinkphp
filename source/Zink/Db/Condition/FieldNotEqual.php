<?php
/**
 * Sql Field Condition '!='
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/15 @thu: 创建；
 */

namespace Zink\Db\Condition;


use Zink\Db\DB;

class FieldNotEqual extends FieldEqual
{
    public function __construct($field1, $field2, $alias = null)
    {
        if (is_array($alias)){
            $field1 = isset($alias[0]) ? "{$alias[0]}.{$field1}" : $field1;
            $field2 = isset($alias[1]) ? "{$alias[1]}.{$field2}" : $field2;
        }

        $field1 = DB::parseKey($field1);
        $field2 = DB::parseKey($field2);
        $this->_statement = "{$field1} != {$field2}";

        //parent::__construct($key, $value, $alias);
    }
}