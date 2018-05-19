<?php

/* RC4 symmetric cipher encryption/decryption
 * Copyright (c) 2006 by Ali Farhadi.
 * released under the terms of the Gnu Public License.
 * see the GPL for details.
 *
 * Email: ali[at]farhadi[dot]ir
 * Website: http://farhadi.ir/
 */

/**
 * Encrypt given plain text using the key with RC4 algorithm.
 * All parameters and return value are in binary format.
 *
 * @param string key - secret key for encryption "aibangrc4"
 * @param string pt - plain text to be encrypted
 * @return string
 */
namespace Zink\Widget;
class RC4 {
    public static function encrypt($pt, $key) {
        $pt = strval($pt);
        $s = array();
        for ($i = 0; $i < 256; $i++) {
            $s[$i] = $i;
        }
        $j = 0;
        $x;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
        }
        $i = 0;
        $j = 0;
        $ct = '';
        $y;
        for ($y = 0; $y < strlen($pt); $y++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            $x = $s[$i];
            $s[$i] = $s[$j];
            $s[$j] = $x;
            $ct .= $pt[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
        }
        return $ct;
    }

    public static function decrypt($pt, $key) {
        return self::encrypt($pt, $key);
    }
    
    public static function encrypt_hex($pt, $key) {
        return bin2hex(self::encrypt($pt, $key));
    }

    public static function encrypt_base64($pt, $key) {
        return  base64_encode(self::encrypt(strval($pt), $key));
    }
    
    public static function decrypt_base64($pt, $key) {
        return  self::encrypt(base64_decode($pt), $key);
    }
    
    public static function decrypt_hex($pt, $key) {
        //$pt = hex2bin($pt){$len = strlen($pt); return pack("H" . $len, $pt)};
        $pt = strval($pt);
        $len = strlen($pt);
        $pt = pack("H" . $len, $pt);
        return self::encrypt($pt, $key);
    }
}
/*
$n = 123;
$n = "300快内(和平东桥-和平东桥)";
$a = RC4Util::encrypt_hex($n);
echo $a." : ".RC4Util::decrypt_hex($a);
 * 
 */