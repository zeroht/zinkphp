<?php
/**
 * excel相关操作
 *
 * @author jhyang
 */
require_once ROOT_PATH . '/lib/phpexcel/classes/PHPExcel.php';

class ExcelObject
{
    /**
     * 将数据data导入到excel中提供下载
     * @param $letter
     * @param $tableheader
     * @param $data
     */
    public function downloadExcel($letter, $tableheader, $data,$filename)
    {
        //创建对象
        $excel = new \PHPExcel();
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $sheet->setCellValue("$letter[$i]1","$tableheader[$i]");
        }

        //填充表格信息
        for ($i = 2; $i <= count($data) + 1; $i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $sheet->setCellValueExplicit("$letter[$j]$i","$value",\PHPExcel_Cell_DataType::TYPE_STRING);
                $j++;
            }
        }
        //创建Excel输入对象
        //$write = new \PHPExcel_Writer_Excel5($excel);    // 用于其他版本格式
        $write = new \PHPExcel_Writer_Excel2007($excel);    // 用于 2007 格式
        //$write->setOffice2003Compatibility(true);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        $fileName=iconv("utf-8","gb2312",$filename.'.xlsx');
        header("Content-Disposition:attachment;filename=$fileName");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
        exit;
    }

}
