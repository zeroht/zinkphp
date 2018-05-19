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

use Zink\Db\Condition\NotIn;
class NotInTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = NotIn::class;

        parent::setUp();
    }

    public function dataProvider()
    {
        return [
            ["id", "1", null, "`id` != :id", [':id' => '1']],
            ["id", "1", "A", "A.id != :A_id", [':A_id' => '1']],
            ["id", ["1","2"], null, "`id` NOT IN (:id_i0,:id_i1)", [
                ':id_i0' => '1',
                ':id_i1' => '2'
            ]],
            ["id", ["1","2"], "A", "A.id NOT IN (:A_id_i0,:A_id_i1)", [
                ':A_id_i0' => '1',
                ':A_id_i1' => '2'
            ]]
        ];
    }
}
