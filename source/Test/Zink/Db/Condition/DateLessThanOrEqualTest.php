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

use Zink\Db\Condition\DateLessThanOrEqual;

class DateLessThanOrEqualTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = DateLessThanOrEqual::class;

        parent::setUp();
    }

    public function dataProvider()
    {
        return [
            ["created_at", "2016-06-01", null, "`created_at` <= :created_at", [':created_at' => '2016-06-01 23:59:59']],
            ["created_at", "2016-06-01", "A", "A.created_at <= :A_created_at", [':A_created_at' => '2016-06-01 23:59:59']]
        ];
    }
}
