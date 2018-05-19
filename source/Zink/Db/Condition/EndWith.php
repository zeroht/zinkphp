<?php
/**
 * Sql Condition 'LIKE %?'
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/15 @thu: 创建；
 */

namespace Zink\Db\Condition;


use Zink\Db\DB;

class EndWith extends SqlCondition
{

    public function __construct($key, $value, $alias = null)
    {
        parent::__construct($key, $value, $alias, self::CDN_LK);

        $value = DB::escapeLike($value);
        $this->_binds[$this->_placeholder] = "%{$value}";
    }
}