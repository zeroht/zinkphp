<?php
/**
 * 文件生成工具
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/6/13 @thu: 创建；
 */

namespace Zink\Widget;
use Zink\Exception\NotFoundException;

class FileGenerator
{
    /**
     * @param $tpl_file
     * @param array|null $tpl_data
     * @return mixed
     * @throws NotFoundException
     */
    public static function getTemplateContent($tpl_file, array $tpl_data = null)
    {
        if (!is_file($tpl_file)){
            throw new NotFoundException("Template File <$tpl_file> Not Existed");
        }

        $content = file_get_contents($tpl_file);
        if ($tpl_data) {
            foreach ($tpl_data as $key => $value) {
                $content = str_replace('{{$' . $key . '}}', $value, $content);
            }
        }

        return $content;
    }

    /**
     * 利用模版生成文件
     * @param $dir 模版/文件目录
     * @param $filename 文件名
     * @param null $data 模版数据
     * @param string $ext 文件扩展名
     * @return bool
     * @throws NotFoundException
     */
    public static function createFileByTemplate($dir, $filename, array $data = [], $ext = '.php')
    {
        $filepath = $dir.$filename.$ext;
        if (is_file($filepath)){
            return TRUE;
        }

        $template = $dir.'.template';
        $data['date'] = date('Y/m/d');
        $data['class'] = $filename;

        $content = self::getTemplateContent($template, $data);
        return file_put_contents($filepath, $content);
    }

}

/* End of file FileGenerator.php */