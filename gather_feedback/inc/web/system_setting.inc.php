<?php

global $_W, $_GPC;

$arr = [
    'title'=>$_GPC['title'],
    'image'=>$_GPC['image']
];

if ($_GPC['id'] === null) {
    pdo_insert('ims_gather_feedback_system_setting',$arr);
}else{
    pdo_update('ims_gather_feedback_system_setting',$arr,['id'=>$_GPC['id']]);
}

$system_setting = pdo_getall('ims_gather_feedback_system_setting')[0];

include_once $this->template('system_setting');
