<?php
/**
 * JavaScript脚本类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/17 @thu: 创建；
 */


namespace Zink\Widget;

class JavaScript
{
    private $_msgs = array();

    /**
     * 追加日志打印代码
     * @param $log
     * @return $this
     */
    public function appendLog($msg)
    {
        $this->_msgs[] = $msg;
        return $this;
    }

    /**
     * 返回js代码
     * @return string
     */
    public function toString()
    {
        $scripts = [];
        foreach ($this->_msgs as $msg){
            if (is_array($msg)){
                $json = Json::array2json($msg);
                $scripts[] = "window.console.log({$json});";
            }else {
                $msg = addslashes($msg, "'");
                $scripts[] = "window.console.log('{$msg}');";
            }

        }

        $scripts = implode("\n", $scripts);
        
        return <<<Javascript
        <script type="text/javascript">
            $scripts
        </script>
Javascript;
    }

    /**
     * 输出js代码
     */
    public function display()
    {
        echo $this->toString();
    }
}
