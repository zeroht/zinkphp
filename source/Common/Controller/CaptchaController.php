<?php

/**
 * Captcha Controller
 */

namespace Common\Controller;

use Common\Service\Captcha\CaptchaService;
use Zink\Core\App;
use Zink\Core\Controller;
use Zink\Core\Session;
use Zink\Widget\Str;

class CaptchaController extends Controller
{

    public function imageAction()
    {
        // web站点利用 sessionid作为key
        $sid = Session::getSessionId();
        // 生成验证码并存入缓存里面
        $captcha = Str::random(6);
        CaptchaService::setImageText($sid, $captcha, 300);
        CaptchaService::outputCaptchaImg($captcha);
        App::finish();
    }

}
