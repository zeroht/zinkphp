<?php
/**
 * Smarty操作类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/9 @thu: 创建；
 */

namespace Zink\Core;
class Smarty extends Singleton
{
    private $_tpl = null;
    
    protected function __construct()
    {
        $cfg_smarty = Config::loadSmarty();
        Autoloader::import(LIB_PATH.'smarty3/Smarty.class.php');

        $tpl = new \Smarty();
        $tpl->template_dir = $cfg_smarty['template_dir'];
        $tpl->compile_dir = $cfg_smarty['compile_dir'];
        $tpl->left_delimiter = $cfg_smarty['left_delimiter'];
        $tpl->right_delimiter = $cfg_smarty['right_delimiter'];
        $tpl->debugging = $cfg_smarty['debugging'];
        $tpl->compile_check = true;
        
        $tpl->assign($cfg_smarty['default_val']);

        if (Debugger::isDevelopMode() && $tpl->debugging) {
            $tpl->debug_tpl = LIB_PATH.'smarty3/debug.tpl';
            $tpl->assign('__POST', $_POST);
            $tpl->assign('__GET', $_GET);
            $tpl->assign('__COOKIE', $_COOKIE);
            $tpl->assign('__SESSION', $_SESSION);
            $tpl->assign('__HEADER', getallheaders());
            $tpl->assign('__RESPONSE', apache_response_headers());
        }
        
        $this->_tpl = $tpl;
    }
    
    public function __call($name, $arguments)
    {
        if ($this->_tpl && method_exists($this->_tpl, $name)){
            return call_user_func_array(array($this->_tpl, $name), $arguments);
        }
        
        return null;
    }
    
    public function getTemplateDir()
    {
        return $this->_tpl->template_dir[0];
    }

    public function tplExist(&$tplFile){
        $filepath = $this->_tpl->template_dir[0].$tplFile;
        if (!is_file($filepath)){
            // linux 区分大小写，此处为了兼容文件名的大小写
            $pos = strrpos($filepath, "/");
            $dir = substr($filepath,0, $pos);
            $tpl = strtolower(substr($filepath, $pos+1));

            $files = scandir($dir);
            foreach ($files as $f){
                if (strtolower($f) == $tpl){
                    $tplFile = substr($tplFile, 0, -strlen($tpl)).$f;
                    return TRUE;
                }
            }

            return FALSE;
        }else {
            return TRUE;
        }
    }

    /**
     * 重置smarty的数据
     * @param $dir
     */
    public function reset($dir = null)
    {
        $this->_tpl->template_dir = $dir ? $dir : $this->_tpl->template_dir;
        $this->_tpl->tpl_vars = array();
    }
    
    public function getTplVals()
    {
        return $this->_tpl->tpl_vars;
    }
}

/* End of file Smarty.php */
