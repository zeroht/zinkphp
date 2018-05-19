<?php

namespace Zink\Widget;
class Postfix
{
    private static $_postfixMap = array(
        // ��׺  => content-type
        'mp3'    => 'audio/mp3',
        'png'    => 'image/png',
        'jpg'    => 'image/jpeg',
        'jpeg'   => 'image/jpeg',
    );

    public static function postfixExist($postfix){
        return isset(self::$_postfixMap[$postfix]);
    }

    public static function postfix2contentType($postfix){
        $postfix = strtolower($postfix);
        static $_postfixMap = array();
        if (empty($_postfixMap)){
            foreach (self::$_postfixMap as $_k => $_v){
                $_postfixMap[$_k] = $_v;
            }
        }
        return $_postfixMap[$postfix];
    }
}
