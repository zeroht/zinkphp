<?php
/**
 *
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/5/19 @thu: 创建；
 */

namespace Common\Service\Captcha;

use Zink\Core\Cache;
use Zink\Exception\RuntimeException;

class CaptchaService
{

    /**
     * 设置图片形验证码
     * @param $id web使用sessionid；api使用cid
     * @param $text
     * @param int $timeout
     * @return mixed
     */
    public static function setImageText($id, $text, $timeout = 300)
    {
        $key = APP_NAME . ".captcha.image.".md5($id);
        return Cache::getMemcached()->set($key, $text, $timeout);
    }

    /**
     * 输出验证码图片流
     * @param $text
     * @param int $width
     * @param int $height
     * @throws \Zink\Exception\RuntimeException
     */
    public static function outputCaptchaImg($text, $width = 70, $height = 20)
    {
        ob_end_clean();
        header("Content-type:image/png");

        //header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        $nwidth = $width > 50 ? $width : 50;
        $nheight = $height > 15 ? $height : 15;

        $im = imagecreate($width, $nheight);
        if (!$im){
            throw new RuntimeException("Can't initialize new GD image stream");
        }

        $background_color = imagecolorallocate($im, rand(160, 255),
            rand(160, 255), rand(160, 255));
        $text_color = imagecolorallocate($im, rand(50, 120), rand(50, 120),
            rand(50, 120));
        imagefilledrectangle($im, 0, 0, $nwidth - 1, $nheight - 1,
            $background_color);
        for ($i = 0; $i < 20; $i++) {
            $randcolor = imagecolorallocate($im, rand(130, 235), rand(130, 235),
                rand(130, 235));
            imageline($im, 0, rand() % 20, 69, rand() % 20, $randcolor);
        }
        imagestring($im, 5, 14, 2, $text, $text_color);

        //$randcolor = imagecolorallocate ( $im, rand(50,120), rand(50,120), rand(50,120));
        //imageline($im, rand()%15, rand(5,15), 55+rand()%15, rand(5,15), $text_color);
        //imageline($im, rand()%15, rand(10,18), 55+rand()%15, rand(15,20), $text_color);

        imagepng($im);
        imagedestroy($im);
        ob_start();
    }
}
