<?php
/**
 * Pdo Model Class AppSuggestModel
 * @author:  \Zink\Widget\FileGenerator
 * @version: 1.0.0
 * @change:
 *   1. 2016/05/19 @FileGenerator: 创建；
 */

namespace Common\Model;

use Common\Model\Pdo\app_suggest;

class AppSuggestModel extends AbstractSingleTableModel
{
    protected function __construct()
    {
        $this->_table = app_suggest::TABLE;
        parent::__construct();
    }

    /**
     * 根据sign获取意见反馈
     * @param $sign
     * @return null|app_suggest
     */
    public function getSuggestBySign($sign)
    {
        return $this->getFirstByKey('sign', $sign);
    }

}

/* End of file AppSuggestModel.php */