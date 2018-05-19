<?php
/**
 * App 发送邮件工具类
 * @version: 1.0.0
 */
namespace Zink\Widget;

use Common\Constant;
use Zink\Core\Exception;
use Zink\Core\Log;

// 引入第三方类库
require_once LIB_PATH . 'vendor/autoload.php';

class Email{

    //邮件服务对象
    private $_mailer = null;

    //发件人地址可指定，默认使用username
    public $sender;

    /**
     * Email constructor
     * @param string $host
     * @param string $username
     * @param string $password
     * @param int $port
     */
    public function __construct($host = '', $username = '', $password = '', $port = 25)
    {
        $this->_mailer = new \PHPMailer();
        $this->_mailer->isSMTP();
        $this->_mailer->SMTPAuth = TRUE;
        $this->_mailer->CharSet = 'utf-8';
        $this->_mailer->Host = $host ? $host : Constant::get('EMAIL_SUPPORT_HOST');
        $this->_mailer->Username = $username ? $username : Constant::get('EMAIL_SUPPORT_ADDR');
        $this->_mailer->Password = $password ? $password : Constant::get('EMAIL_SUPPORT_PWD');
        //$this->_mailer->SMTPSecure = 'ssl'; //163邮箱不支持ssl
        $this->_mailer->Port = $port;
        $this->_mailer->setFrom($this->sender ? $this->sender : $this->_mailer->Username);
    }

    /**
     * Create a message and send it.
     * Uses the sending method specified by $Mailer.
     * @param {string}   subject  邮件的主题
     * @param {string}   content  邮件的内容，支持格式[文本，HTML]
     * @param {string}   from     发件人地址
     * @param {array|string}  to  收件人地址
     * @param {array}  attachment 附件指定附件资源的绝对地址
     * @return boolean false on error - See the ErrorInfo property for details of the error.
     */
    public function send($subject, $content, $sendTo, $attachment = []){
        if(!$subject || !$content || !$sendTo){
            return FALSE;
        }
        if(is_array($sendTo)){
            foreach ($sendTo as $reve){
                $this->_mailer->addAddress($reve);
            }
        }else{
            $this->_mailer->addAddress($sendTo);
        }

        $this->_mailer->Subject = $subject;
        $this->_mailer->Body    = $content;
        $this->_mailer->isHTML(preg_match('/\s?<\s?html\s?>/i', $this->_mailer->Body) == 0 ? FALSE : TRUE);

        //增加附件
        if(is_array($attachment) && count($attachment) > 0){
            foreach ($attachment as $atta){
                if(is_file($atta)){
                    $this->_mailer->addAttachment($atta,basename($atta));
                }
            }
        }

        try{
            return $this->_mailer->send();
        }catch(Exception $exc){
            Log::getLogger()->fatal('SendMailException:'.$exc->getMessage().'--with Emails : '.is_array($sendTo) ? Json::array2json($sendTo) : $sendTo);
            return FALSE;
        }

    }
}