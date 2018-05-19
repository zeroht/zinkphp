<?php
/**
 *
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/6/23 @thu: 创建；
 */

namespace Test\Zink\Db\Where;


use Zink\Db\Condition\Equal;
use Zink\Db\Condition\In;
use Zink\Db\Condition\SqlCondition;
use Zink\Db\Driver;
use Zink\Db\Where\Where;

class WhereTest extends \PHPUnit_Framework_TestCase
{
    public function constructDataProvider()
    {
        Driver::clearPlaceholderCounter();
        return [
            [new Equal('name', 'test'), true, false],
            [new Equal('name', 'test'), false, false],
            [new Equal('name', ''), true, true],
            [new Equal('name', ''), false, false]
        ];
    }

    /**
     * @dataProvider constructDataProvider
     */
    public function testIsEmpty(SqlCondition $condition, $ignoreEmpty, $assert)
    {
        $where = new Where($condition, $ignoreEmpty);
        $this->assertSame($where->isEmpty(), $assert);
    }

    public function conditionDataProvider()
    {
        Driver::clearPlaceholderCounter();
        return [
            [
                [
                    new Equal('name', 'test'),
                    'AND',
                    new In('nick', ['hello','world']),
                    'OR',
                    new Equal('gender', 'male')
                ],
                [
                    '`name` = :name AND `nick` IN (:nick_i0,:nick_i1) OR `gender` = :gender',
                    [':name' => 'test', ':nick_i0' => 'hello', ':nick_i1' => 'world', ':gender' => 'male']
                ]
            ]
        ];
    }

    private function _conditionsToWhere(array $conditions)
    {
        $where = new Where(array_shift($conditions));
        $cnd = array_shift($conditions);

        while ($cnd){
            if ($cnd == Where::LINK_AND){
                $where->andCondition(array_shift($conditions));
            } else if ($cnd == Where::LINK_OR){
                $where->orCondition(array_pop($conditions));
            }

            $cnd = array_shift($conditions);
        }

        return $where;
    }

    /**
     * @dataProvider conditionDataProvider
     */
    public function testAndOrCondition(array $conditions, $query)
    {
        $hasMultiConditions = count($conditions) > 1 ? true : false;
        $where = $this->_conditionsToWhere($conditions);

        $this->assertSame($query, $where->toQuery());
        $this->assertSame($hasMultiConditions, $where->hasMultiConditions());
        return $where;
    }

    public function whereDataProvider()
    {
        Driver::clearPlaceholderCounter();
        return [
            [
                [
                    [
                        new Equal('gender', 'male')
                    ],
                    'AND',
                    [
                        new Equal('name', 'test1'),
                        'OR',
                        new Equal('nick', 'hello')
                    ],
                    'OR',
                    [
                        new Equal('name', 'test2'),
                        'AND',
                        new Equal('nick', 'hello')
                    ]
                ],
                [
                    '`gender` = :gender AND (`name` = :name OR `nick` = :nick) OR (`name` = :name_1 AND `nick` = :nick_1)',
                    [':gender' => 'male', ':name' => 'test1', ':nick' => 'hello',
                        ':name_1' => 'test2', ':nick_1' => 'hello']
                ],
                true
            ]
        ];
    }

    /**
     * @dataProvider whereDataProvider
     */
    public function testAndOrWhere(array $whereConditions, $query, $hasMultiConditions)
    {
        $wheres = [];
        foreach ($whereConditions as $conditions){
            if (is_array($conditions)) {
                $wheres[] = $this->_conditionsToWhere($conditions);
            }else {
                $wheres[] = $conditions;
            }
        }

        $where = array_shift($wheres);

        $cnd = array_shift($wheres);
        while ($cnd){
            if ($cnd == Where::LINK_AND){
                $where->andWhere(array_shift($wheres));
            } else if ($cnd == Where::LINK_OR){
                $where->orWhere(array_pop($wheres));
            }

            $cnd = array_shift($wheres);
        }

        $this->assertSame($query, $where->toQuery());
        $this->assertSame($hasMultiConditions, $where->hasMultiConditions());
    }
}
