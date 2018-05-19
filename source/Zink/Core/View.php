<?php

/**
 * 视图工具类 
 */

namespace Zink\Core;

use Zink\View\ViewFactory;
use Zink\View\AbstractView;

class View
{

    public static function getFileView($filename)
    {
        return ViewFactory::createView(AbstractView::TYPE_FILE, null, $filename);
    }

    public static function getJsView($data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_JS, $data, null);
    }
    
    public static function getTextView($data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_TEXT, $data, null);
    }

    public static function getJsonView($data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_JSON, $data, null);
    }

    public static function getXmlView($data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_XML, $data, null);
    }

    public static function getSmartyView($tpl, $data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_SMARTY, $data, $tpl);
    }

    public static function getSmartyJsonView($tpl, $data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_SMARTY_JSON, $data, $tpl);
    }

    public static function getSmartyXmlView($tpl, $data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_SMARTY_XML, $data, $tpl);
    }

    public static function getSmartyJsView($tpl, $data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_SMARTY_JS, $data, $tpl);
    }

    public static function getExcelView($tpl, $data = null)
    {
        return ViewFactory::createView(AbstractView::TYPE_EXCEL, $data, $tpl);
    }
}

/* End of file View.php */
