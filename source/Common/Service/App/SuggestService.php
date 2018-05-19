<?php
/**
 *
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/19 @thu: 创建；
 */

namespace Common\Service\App;

use Common\Model\AppSuggestModel;
use Common\Model\Pdo\app_suggest;
use Zink\Db\Condition\Equal;
use Zink\Db\Condition\GreaterThanOrEqual;
use Zink\Db\Where\WhereAnd;

class SuggestService
{

    /**
     * 增加意见反馈
     * @param $data
     * @return bool
     */
    public static function addSuggest($data)
    {
        if (empty($data['content'])) {
            return FALSE;
        }

        $suggest = new app_suggest($data);
        $sign = $suggest->createSign();

        $suggestModel = AppSuggestModel::getInstance();
        $existedSuggest = $suggestModel->getSuggestBySign($sign);
        if ($existedSuggest) {
            return TRUE;
        }
        
        return $suggest->save();
    }

    /**
     * 根据cid获取意见的条数
     * @param $cid
     * @return int
     */
    public static function getSuggestCountByCid($cid)
    {
        $suggestModel = AppSuggestModel::getInstance();
        $where = new WhereAnd([
            new Equal('cid', $cid),
            new GreaterThanOrEqual('created_at',date('Y-m-d 00:00:00'))
        ]);

        return $suggestModel->count($where);
    }

    /**
     * @desc 根据id获取反馈记录
     * @param $id
     * @return bool|null|\Common\Model\Pdo\app_suggest
     */
    public static function getSuggestById($id)
    {
        return AppSuggestModel::getInstance()->getFirstById($id);
    }
}
