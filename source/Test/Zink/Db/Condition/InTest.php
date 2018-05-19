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

use Zink\Db\Condition\In;

class InTest extends SqlConditionTest
{
    public function setUp()
    {
        $this->_conditionClass = In::class;

        parent::setUp(); 
    }

    public function emptyDataProvider()
    {
        return [
            ["", true],
            [null, true],
            [false, false],
            [0, false],
            [true, false],
            [[""], true],
            [[1], false],
            [[0], false]
        ];
    }

    public function dataProvider()
    {
        return [
            ["id", "1", null, "`id` = :id", [':id' => '1']],
            ["id", "1", "A", "A.id = :A_id", [':A_id' => '1']],
            ["id", ["1","2"], null, "`id` IN (:id_i0,:id_i1)", [
                ':id_i0' => '1',
                ':id_i1' => '2'
            ]],
            ["id", ["1","2"], "A", "A.id IN (:A_id_i0,:A_id_i1)", [
                ':A_id_i0' => '1',
                ':A_id_i1' => '2'
            ]]
        ];
    }
}
