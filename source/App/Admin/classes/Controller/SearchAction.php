<?php
/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */
namespace App\Admin\Controller;

use Common\Constant;
use Common\ErrorCode;
use Zink\Core\Controller;
use Zink\Core\View;
use Zink\Db\DB;
use Zink\Db\Result\Collection;
use Zink\Widget\Pager;
use Zink\Widget\Request;

/**
 * Class SearchAction
 * @package Zink\Core
 */
trait SearchAction
{
    protected $_p = 1;
    protected $_pn = 50;
    protected $_sort = null;
    
    /**
     * 数据查询方法,子类有search功能需要重写此方法
     * @return bool|Pager|Collection
     */
    protected function _search()
    {
        return FALSE;
    }
    
    /**
     * 查询数据接口
     * @return \Zink\View\AbstractView
     */
    public function searchAction()
    {
        $this->_p = $this->get('p', 'intval', 1);
        $this->_pn = $this->get('pn', 'intval', Constant::get('PAGER_PN'));
        $sort = $this->get('sort');
        if (preg_match('/^[\w-]+/', $sort)){
            // 过滤掉非法的排序词,防攻击
            $asc = $this->get('asc', 'intval', 0);
            $this->_sort = [$sort => $asc];
            if ($sort == "created_at") {
                $this->_sort['id'] = $asc;
            }
        }
        
        $result = $this->_search();
        if (ErrorCode::isValidCode($result)) {
            return Controller::json($result);
        }else if ($result === false || !($result instanceof Pager)){
            return Controller::json(ErrorCode::ERR_500);
        }
        
        $data = [
            'total' => $result->getTotal(),
            'p' => $result->getPage(),
            'pn' => $result->getPageNum(),
            'result' => $result->getResult(true)
        ];
        if($result->getExtend() !== null) {
            $data['extend'] = $result->getExtend();
        }

        return $this->adminView($data, true);
    }

    private function _checkExport()
    {
        $this->_p = 1;
        $this->_pn = 1;
        $pager = $this->_search();
        $maxCount = Constant::get('MAX_DB_QUERY_COUNT');
        if (!$pager || $pager->isEmpty()){
            return Controller::json(ErrorCode::err500('无符合条件的数据'));
        }else if ($pager->getTotal() > $maxCount){
            return Controller::json(ErrorCode::err500('导出数据超出系统上限'.($maxCount/10000).'万条，请修改导出条件或与技术人员联系。'));
        }else if (Request::isAjaxJson()){
            return $this->success(['download' => Request::getUrl()]);
        }

        return TRUE;
    }

    protected function _getExportData($result){
        /*
         * return [
         *  "header":["姓名","手机号"],
         *  "data":[
         *     ["张三", "130*****"],
         *      ["李四", "130*****"],
         *  ],
         *  "file":"老师信息"
         * ]
         */
        return FALSE;
    }

    /**
     * 数据删除Action方法
     * @return \Zink\View\AbstractView
     */
    public function exportAction()
    {
        $ret = $this->_checkExport();
        if (TRUE !== $ret){
            return $ret;
        }

        $maxCount = Constant::get('MAX_DB_QUERY_COUNT');
        $this->_p = 1;
        $this->_pn = $maxCount;
        if(!$this->_sort){
            $this->_sort = ['created_at' => DB::SORT_TYPE_DESC];
        }

        $pager = $this->_search();
        $data = $this->_getExportData($pager->getResult(true));
        
        return View::getExcelView($data['file'], $data);
    }

}


/* End of file SearchAction.php */
