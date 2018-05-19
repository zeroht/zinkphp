<?php
/**
 *  
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2017/3/6 @thu: 创建；
 *
 */

namespace Zink\Widget;

class Folder
{
    public static function getFolderList($path)
    {
        $fileArray = NULL;
        if (false != ($handle = opendir($path))) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($path . $file)) {
                        $fileArray [] = $file;
                    }
                    
                }
            }
// 关闭句柄
            closedir($handle);
        }

        return $fileArray;
    }
}

/* End of file Folder.php */