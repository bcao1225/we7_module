<?php

global $_W, $_GPC;

if ($_W['ispost'] == true) {
    pdo_update($this->system_setting,
        [
            "company_img" => $_GPC['company_img'],
            "company_particulars" => htmlspecialchars_decode($_GPC['company_particulars'])
        ],
        [
            "id" => $_GPC['id']
        ]
    );

    message("保存成功", $this->createWebUrl('company_setting'));
}

$system_setting = pdo_getall($this->system_setting)[0];

include $this->template('company_setting');