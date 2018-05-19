<?php
/**
 * ORM app_suggest
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/19 @thu: 创建；
 */

namespace Common\Model\Pdo;

use Zink\Db\Result\Record;
use Zink\Widget\Str;

class app_suggest extends Record
{
    const TABLE = 'app_suggest';

    public function createSign()
    {
        $string = new Str();
        $sign = $string->append($this->cid)
                ->append($this->pid)
                ->append($this->content)
                ->toMd5();
        $this->set('sign', $sign);
        return $sign;
    }
}
/* End of file app_suggest.php */