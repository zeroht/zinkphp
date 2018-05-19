<?php
/**
 * Sql Condition 'NOT IN'
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/15 @thu: 创建；
 */

namespace Zink\Db\Condition;

use Zink\Db\DB;
use Zink\Db\Driver;

class NotIn extends SqlCondition
{
    /**
     * @type int 比较集个数
     */
    protected $_isEmpty = true;

    public function __construct($key, $value, $alias = null, $cdn = self::CDN_NIN)
    {
        $key = $alias ? "{$alias}.{$key}" : $key;
        $placeholder = Driver::keyToPlaceholder($key);

        $inPlaceholders = array();
        if (is_array($value)){
            $value = array_unique($value);
            foreach ($value as $i => $val){
                $ph = $placeholder.'_i'.$i;

                $inPlaceholders[] = $ph;
                $this->_binds[$ph] = $val;
            }
        }else {
            $inPlaceholders[] = $placeholder;
            $this->_binds[$placeholder] = $value;
        }


        $key = DB::parseKey($key);
        if (count($inPlaceholders) > 1){
            $this->_statement = "{$key} {$cdn} (".implode(',', $inPlaceholders).")";
            $this->_isEmpty = false;
        }else if ($inPlaceholders){
            $this->_statement = "{$key} != ".$inPlaceholders[0];
            $value = $this->_binds[$inPlaceholders[0]];
            $this->_isEmpty = ($value === '' || $value === null);
        }

        //parent::__construct($key, $value, $alias);
    }

    public function isEmptyValue()
    {
        return $this->_isEmpty;
    }
}