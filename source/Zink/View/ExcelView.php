<?php
/**
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2017/4/8 @thu: 创建；
 *
 */

/**
 * Json视图类
 * 输出json格式字符串
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

use Zink\Widget\Json;

require_once ROOT_PATH . '/Lib/phpexcel/classes/PHPExcel.php';

class ExcelView extends AbstractView
{
    public function display()
    {
        $file = $this->_tpl;
        $header = $this->_data['header'];
        $data = $this->_data['data'];
        if (!is_array($header)){
            echo "文件数据错误";
            return;
        }

        //创建对象
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        //填充表头信息
        foreach ($this->_data['header'] as $i => $title) {
            $sheet->setCellValueByColumnAndRow($i, 1, $title);
        }

        //填充表格信息
        foreach ($data as $i => $line){
            $col = 0;
            foreach ($line as $key => $value){
                $sheet->setCellValueExplicitByColumnAndRow($col++, $i + 2, $value);
            }
        }

        //创建Excel输入对象
        //$write = new \PHPExcel_Writer_Excel5($excel);   // 用于其他版本格式
        $write = new \PHPExcel_Writer_Excel2007($excel);    // 用于 2007 格式
        //$write->setOffice2003Compatibility(true);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        $fileName = $file.'.xlsx';
        header("Content-Disposition:attachment;filename=$fileName");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }

    public function fetch()
    {
        return $this->_data;
    }
}

/* End of file JsonView.php */
