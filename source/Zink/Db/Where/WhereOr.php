<?php
/**
 * Sql Or Conditions
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */

namespace Zink\Db\Where;

use Zink\Db\Condition\SqlCondition;
use Zink\Exception\UnexpectetException;

class WhereOr extends Where
{
    /**
     * WhereAnd constructor.
     * @param array $conditions SqlCondition数组
     * @param bool $ignoreEmpty
     * @throws UnexpectetException
     */
    public function __construct(array $conditions, $ignoreEmpty = true)
    {
        $condition = array_shift($conditions);
        if (!($condition instanceof SqlCondition) && !($condition instanceof Where)) {
            throw new UnexpectetException('WhereAnd constructor need SqlCondition Or Where list');
        }

        parent::__construct($condition, $ignoreEmpty);

        foreach ($conditions as $condition){
            if ($condition instanceof SqlCondition) {
                $this->_appendCondition($condition, self::LINK_OR);
            } else if ($condition instanceof Where) {
                $this->_appendWhere($condition, self::LINK_OR);
            }
        }
    }
}

/* End of file WhereOr.php */