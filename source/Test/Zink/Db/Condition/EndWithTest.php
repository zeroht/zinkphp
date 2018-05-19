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
use Zink\Db\Condition\EndWith;

class EndWithTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = EndWith::class;
        
        parent::setUp();
    }

    public function dataProvider()
    {
        return [
            ["name", "市", null, "`name` LIKE :name", [':name' => '%市']],
            ["name", "市", "A", "A.name LIKE :A_name", [':A_name' => '%市']]
        ];
    }
}
