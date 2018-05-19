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
use Zink\Db\Condition\StartWith;

class StartWithTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = StartWith::class;

        parent::setUp();
    }

    public function dataProvider()
    {
        return [
            ["name", "北京", null, "`name` LIKE :name", [':name' => '北京%']],
            ["name", "北京", "A", "A.name LIKE :A_name", [':A_name' => '北京%']]
        ];
    }
}