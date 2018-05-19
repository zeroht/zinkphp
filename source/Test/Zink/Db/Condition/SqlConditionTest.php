<?php
/**
 * SqlCondition测试的基类
 * 
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/6/4 @thu: 创建；
 */

namespace Test\Zink\Db\Condition;

use Zink\Db\Driver;

class SqlConditionTest extends \PHPUnit_Framework_TestCase
{
    protected $_conditionClass = null;

    public function emptyDataProvider()
    {
        return [
            ["", true],
            [null, true],
            [false, false],
            [0, false],
            [true, false]
        ];
    }

    /**
     * 数据构造器
     * @return array
     */
    public function dataProvider()
    {
        return [['','','','','',[]]];
    }

    /**
     * @dataProvider emptyDataProvider
     */
    public function testIsEmptyValue($value, $return)
    {
        if (!class_exists($this->_conditionClass)) {
            $this->assertTrue(true);
            return;
        }

        $condition = new $this->_conditionClass('key', $value, null);
        $result = $condition->isEmptyValue();

        $testClass = get_called_class();
        if ($return){
            $this->assertTrue($result, "Called by {$testClass}:");
        }else {
            $this->assertFalse($result, "Called by {$testClass}");
        }
    }

    /**
     * @dataProvider dataProvider
     */
    public function testToQuery($key, $value, $alias, $statement, $binds)
    {
        if (!class_exists($this->_conditionClass)) {
            $this->assertTrue(true);
            return;
        }

        Driver::clearPlaceholderCounter();
        $condition = new $this->_conditionClass($key, $value, $alias);
        $query = $condition->toQuery();

        $testClass = get_called_class();
        $this->assertSame($statement, $query[0], "Called by {$testClass}");
        $this->assertSame($binds, $query[1], "Called by {$testClass}");
    }
}
