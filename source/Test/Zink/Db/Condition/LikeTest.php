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

use Zink\Db\Condition\Like;
class LikeTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = Like::class;

        parent::setUp(); 
    }

    public function dataProvider()
    {
        return [
            ["name", "中", null, "`name` LIKE :name", [':name' => '%中%']],
            ["name", "中", "A", "A.name LIKE :A_name", [':A_name' => '%中%']]
        ];
    }
}
