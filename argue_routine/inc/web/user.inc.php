<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    case 'red_packet':
        /*获取签名*/
        $arr = $this->setSign(
            [
                'nonce_str' => random(32),
                'mch_billno' => random(28, true),
                /*商户id*/
                'mch_id' => $_W['uniaccount']['setting']['payment']['wechat']['mchid'],
                /*公众号appid*/
                'wxappid' => $_W['uniaccount']['key'],
                /*商户名称*/
                'send_name' => '测试商户名称',
                /*用户openid*/
                're_openid' => 'oSIfwjhMKaC0fTEcvK4qLSlF382Q',
                /*发放金额，单位为分*/
                'total_amount' => 100,
                /*总人数*/
                'total_num' => 1,
                /*祝福语*/
                'wishing' => '感谢您参加猜灯谜活动，祝您元宵节快乐！',
                /*ip地址*/
                'client_ip' => '120.79.7.173',
                /*活动名称*/
                'act_name' => '猜灯谜抢红包活动',
                /*备注*/
                'remark' => '猜越多得越多，快来抢！',
                'scene_id' => 'PRODUCT_1'
            ]
        );
        $xml = array2xml($arr);
        $content = $this->postData('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack', $xml);

        exit(var_dump($content));
        break;
    default:
        $argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=1');
        $no_argue_list = pdo_fetchall('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $_GPC['activity_id'] . ' AND viewpoint=0');

        $user_list = [$argue_list, $no_argue_list];
        include_once $this->template('user/user_manager');
        break;
}