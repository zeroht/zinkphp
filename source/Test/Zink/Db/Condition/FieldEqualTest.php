<?php
/**
 *
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/6/4 @thu: 创建；
 */

namespace Test\Zink\Db\Condition;

use Zink\Db\Condition\FieldEqual;

class FieldEqualTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = FieldEqual::class;

        parent::setUp();
    }

    public function emptyDataProvider()
    {
        return [
            ["", false],
            [null, false],
            [false, false],
            [0, false],
            [true, false]
        ];
    }
    
    public function dataProvider()
    {
        return [
            ["id", "uid", null, "`id` = `uid`", []],
            ["id", "uid", ["A"], "A.id = `uid`", []],
            ["id", "uid", ["A", "B"], "A.id = B.uid", []]
        ];
    }
}
