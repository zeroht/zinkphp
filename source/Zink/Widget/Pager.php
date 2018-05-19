<?php
/**
 * 数据分页类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */

namespace Zink\Widget;


class Pager extends IteratorSupport
{
    /**
     * @type int 页码
     */
    private $_page = 1;

    /**
     * @type int 每页数量
     */
    private $_pageNum = 20;

    /**
     * @type int 总页码
     */
    private $_pageCount = 0;

    /**
     * @type int 数据开始位置
     */
    private $_start = 0;

    /**
     * @type int 总数据量
     */
    private $_total = 0;

    /**
     * @type mixed 和data节点同级，挂载data之外的信息
     */
    private $_extend = null;

    public static function createEmptyPager()
    {
        static $_pager = null;
        if ($_pager == null){
            $_pager = new Pager();
        }
        
        return $_pager;
    }
    
    /**
     * Pager constructor.
     * @param int $p 页码
     * @param int $pn 每页数量
     */
    public function __construct($total = 0, $p = 1, $pn = 20)
    {
        $total = intval($total);
        $p = intval($p);
        $pn = intval($pn);

        $this->_total = $total > 0 ? $total : 0;
        $this->_page = $p > 0 ? $p : 1;
        $this->_pageNum = $pn > 0 ? $pn : $this->_pageNum;
        $this->_start = ($this->_page - 1) * $this->_pageNum;
        $this->_pageCount = $this->_pageNum ? ceil($total / $this->_pageNum) : 0;
    }

    /**
     * 设置当前页记录集
     * @param $result
     */
    public function setResult($result)
    {
        if ($result){
            $this->_arrData = $result;
        }
    }

    /**
     * 获取数据总量
     * @return int
     */
    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * 获取分页数据记录集
     * @param bool $toArray 是否返回纯数组
     * @return array
     */
    public function getResult($toArray = false)
    {
        return $toArray ? parent::toArray() : $this->_arrData;
    }

    /**
     * 获取第一条记录的索引值
     * @return int
     */
    public function getStart()
    {
        return $this->_start;
    }

    /**
     * 获取当前的页码
     * @return int
     */
    public function getPage()
    {
        return $this->_page;
    }
    
    /**
     * 获取数据总页数
     * @return int
     */
    public function getPageNum()
    {
        return $this->_pageNum;
    }

    /**
     * 当前页记录集是否空
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->_arrData);
    }

    /**
     * 页码是否有效
     * @return bool
     */
    public function isValidPage()
    {
        return ($this->_page > 0 && $this->_page <= $this->_pageCount);
    }

    /**
     * 返回数组数据
     * @return array
     */
    public function toArray(array $filter = null)
    {
        $hasMore = $this->_start + $this->_pageNum < $this->_total;
        return array(
            'page' => $this->_page,
            'pageNum' => $this->_pageNum,
            'pageCount' => $this->_pageCount,
            'start' => $this->_start,
            'total' => $this->_total,
            'result' => parent::toArray(),
            'count' => parent::count(),
            'hasMore' => $hasMore
        );
    }

    /**
     * 扩展数据(汇总等)
     * @param $extendInfo
     */
    public function setExtend($extendInfo) {
        $this->_extend = $extendInfo;
    }

    /**
     * 返回额外的extend信息
     * @return mixed
     */
    public function getExtend() {
        return $this->_extend;
    }
}

/* End of file Pager.php */