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
use Zink\Db\Condition\GreaterThan;

class GreaterThanTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = GreaterThan::class;

        parent::setUp(); 
    }

    public function dataProvider()
    {
        return [
            ["id", "100", null, "`id` > :id", [':id' => '100']],
            ["id", "100", "A", "A.id > :A_id", [':A_id' => '100']]
        ];
    }
}