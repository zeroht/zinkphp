<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */


namespace App\Admin\Controller\App;

use App\Admin\Controller\BaseAdminController;
use App\Admin\Controller\SearchAction;
use Common\Model\AppSuggestModel;
use Zink\Db\Condition\Equal;
use Zink\Db\Where\WhereAnd;

class SuggestController extends BaseAdminController
{
    use SearchAction;
    public function getRules()
    {
        return [
        ];
    }
    
    protected function _search()
    {
        $condition = new WhereAnd([
            new Equal('mobile', $this->get('mobile')),
            new Equal('cid', $this->get('cid')),
            new Equal('platform', $this->get('platform')),
            new Equal('version_no', $this->get('version_no')),
            new Equal('pid', $this->get('pid'))
        ]);

        $model = AppSuggestModel::getInstance();
        return $model->getListInPager($condition, $this->_p, $this->_pn, $this->_sort);
    }
}


/* End of file SuggestController.php */
