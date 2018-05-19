<?php
/**
 * 视图工厂类
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *   1. 2016/5/8 @thu: 创建；
 */

namespace Zink\View;

use Zink\Exception\RuntimeException;

class ViewFactory
{

    /**
     * @param string $type
     * @param string|array $data
     * @param string $tpl
     * @return AbstractView
     * @throws \Zink\Exception\RuntimeException
     */
    public static function createView($type, $data = null, $tpl = null)
    {
        switch ($type) {
            case AbstractView::TYPE_FILE: {
                    return new FileView($data, $tpl);
                }
            case AbstractView::TYPE_TEXT: {
                    return new TextView($data, $tpl);
                }
            case AbstractView::TYPE_JS: {
                    return new JsView($data, $tpl);
                }
            case AbstractView::TYPE_JSON: {
                    return new JsonView($data, $tpl);
                }
            case AbstractView::TYPE_XML: {
                    return new XmlView($data, $tpl);
                }
            case AbstractView::TYPE_SMARTY: {
                    return new SmartyView($data, $tpl);
                }
            case AbstractView::TYPE_SMARTY_JSON: {
                    return new SmartyJsonView($data, $tpl);
                }
            case AbstractView::TYPE_SMARTY_XML: {
                    return new SmartyXmlView($data, $tpl);
                }
            case AbstractView::TYPE_SMARTY_JS: {
                    return new SmartyJsView($data, $tpl);
                }
            case AbstractView::TYPE_EXCEL: {
                return new ExcelView($data, $tpl);
            }
            default : {
                    throw new RuntimeException('Invalid View Type: ' . $type);
                }
        }
    }

}

/* End of file ViewFactory.php */
    