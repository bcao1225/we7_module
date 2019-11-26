<?php

global $_W, $_GPC;

$arr = [
    'title'=>$_GPC['title'],
    'image'=>$_GPC['image']
];

/*提交*/
if($_W['ispost']){
    if ($_GPC['id'] === '') {
        pdo_insert('ims_gather_feedback_system_setting',$arr);
    }else{
        pdo_update('ims_gather_feedback_system_setting',$arr,['id'=>$_GPC['id']]);
    }
    message('保存成功',$this->createWebUrl('system_setting'),'success');
}

$system_setting = pdo_getall('ims_gather_feedback_system_setting')[0];

include_once $this->template('system_setting');
