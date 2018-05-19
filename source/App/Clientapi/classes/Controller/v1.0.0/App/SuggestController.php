<?php

/**
 *
 * @author:  thu
 * @version: 1.1.0
 * @change:
 * 1. 2016/5/23 @thu: 创建；
 */

namespace App\Clientapi\Controller\App;

use App\Clientapi\Controller\BaseClientapiController;
use Common\Model\AppSuggestModel;
use Common\Model\Pdo\app_suggest;
use Common\Service\SessionService;
use Zink\Widget\Ip;
use App\Clientapi\ActionCode;

class SuggestController_1_0_0 extends BaseClientapiController
{
    /*
     * @api {post} /app/suggest/v1.0.0/submit 1.意见反馈
     * @apiGroup App_Suggest
     * @apiVersion 1.0.0
     * @apiDescription App意见反馈
     *
     * @apiParam {string} content 反馈内容
     * @apiParam {string} os_version  操作系统版本
     * @apiParam {string} phone_model 手机型号:iPhone 7
     * @apiParam {string} [image_ids] 上传的图片id，多个以逗号分隔
     *
     * @apiUse status
     */
    public function submitAction()
    {
        if (!$this->_checkParams('content,phone_model,os_version')) {
            return $this->missingParameter();
        }
        
        $suggest = new app_suggest([
            'uid' => SessionService::getUid(),
            'mobile' => $this->get('phone'),
            'content' => $this->get('content'),
            'phone_model' => $this->get('phone_model'),
            'os_version' => $this->get('os_version'),
            'network' => HEADER_NETWORK,
            'pid' => HEADER_PID,
            'platform' => HEADER_PLATFORM,
            'cid' => HEADER_CID,
            'version_no' => HEADER_VID,
            'ip' => Ip::getClientIp()
        ]);
        
        if (!$suggest->save()){
            return ActionCode::err50000("提交失败，请稍后再试");
        }

        return ActionCode::SUCCESS;
    }

    public function listAction()
    {
        $data = AppSuggestModel::getInstance()->getAll();
        $this->assign('data', $data);
        return self::AS_SUCCESS;
    }
}