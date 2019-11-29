<?php

global $_W, $_GPC;

$system_setting = pdo_getall('ims_gather_feedback_system_setting')[0];

switch ($_GPC['action']) {
    /*题目设置*/
    case 'question':
        if ($_W['ispost']) {
            $data = [
                'radio_hint' => $_GPC['radio_hint'],
                'check_hint' => $_GPC['check_hint'],
                'text_hint' => $_GPC['text_hint']
            ];

            pdo_update('ims_gather_feedback_system_setting', $data, ['id' => $system_setting['id']]);
            message('保存成功',$this->createWebUrl('system_setting').'&action=question','success');
        }

        include_once $this->template('setting/question_setting');
        break;
    /*分享设置*/
    case 'share':
        if ($_W['ispost']) {
            $data = [
                'share_title' => $_GPC['share_title'],
                'share_desc' => $_GPC['share_desc'],
                'share_img' => $_GPC['share_img']
            ];

            pdo_update('ims_gather_feedback_system_setting', $data, ['id' => $system_setting['id']]);
            message('保存成功', $this->createWebUrl('system_setting') . '&action=share', 'success');
        }
        include_once $this->template('setting/share_setting');
        break;
    default:
        /*提交*/
        if ($_W['ispost']) {

            pdo_update('ims_gather_feedback_system_setting', [
                'title' => $_GPC['title'],
                'image' => $_GPC['image'],
                'last_submit_text' => $_GPC['last_submit_text']
            ], ['id' => $system_setting['id']]);

            message('保存成功', $this->createWebUrl('system_setting'), 'success');
        }
        include_once $this->template('setting/basic_setting');
        break;
}







