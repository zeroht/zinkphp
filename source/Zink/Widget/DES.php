<?php

/**
 *  http://blog.csdn.net/ghj1976/article/details/6447793
 * PHP，Java，ObjectC统一的方法
 * linux下要安装libmcrypt，windows下找到php.ini文件里extension=php_mcrypt.dll
 */
namespace Zink\Widget;
class DES
{

    public static function encrypt($string, $key)
    {
        $key = substr(md5($key), 0, 8);
        $ivArray = array(0x12, 0x34, 0x56, 0x78, 0x90, 0xAB, 0xCD, 0xEF);
        $iv = null;
        foreach ($ivArray as $element)
            $iv.=CHR($element);


        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $string = self::pkcs5Pad($string, $size);

        $data = mcrypt_encrypt(MCRYPT_DES, $key, $string, MCRYPT_MODE_CBC, $iv);

        return $data;
    }

    public static function decrypt($string, $key)
    {
        $key = substr(md5($key), 0, 8);
        $ivArray = array(0x12, 0x34, 0x56, 0x78, 0x90, 0xAB, 0xCD, 0xEF);
        $iv = null;
        foreach ($ivArray as $element)
            $iv.=CHR($element);

        //echo("****");
        //echo($string);
        //echo("****");
        $result = mcrypt_decrypt(MCRYPT_DES, $key, $string, MCRYPT_MODE_CBC, $iv);
        $result = self::pkcs5Unpad($result);

        return $result;
    }

    public static function encrypt_base64($pt, $key)
    {
        $key = substr(md5($key), 0, 8);
        return base64_encode(self::encrypt($pt, $key));
    }

    public static function decrypt_base64($pt, $key)
    {
        $key = substr(md5($key), 0, 8);
        return self::decrypt(base64_decode($pt), $key);
    }

    private static function pkcs5Pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private static function pkcs5Unpad($text)
    {
        $pad = ord($text {strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, - 1 * $pad);
    }

}

