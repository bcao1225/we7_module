<?php

global $_W,$_GPC;

if($_W['ispost']){
    pdo_update('ims_machine_feedback_system_setting',[
        'open_video'=>$_GPC['open_video'],
        'open_audit'=>$_GPC['open_audit']
    ],['id'=>$_GPC['id']]);
    message('保存成功',$this->createWebUrl('system_setting'),'success');
}

$system_setting = pdo_getall('ims_machine_feedback_system_setting')[0];


include_once $this->template('system_setting');
