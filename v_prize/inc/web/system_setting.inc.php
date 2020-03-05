<?php

global $_W, $_GPC;

if ($_W['ispost'] == true) {
    if ($_GPC['id'] == null) {
        pdo_insert($this->system_setting,
            [
                "title" => $_GPC['title'],
                "slideshow" => iserializer($_GPC['slideshow']),
                "phone" => $_GPC['phone'],
                "appid" => $_GPC['appid'],
                "appsecret" => $_GPC['appsecret']
            ]
        );
    } else {
        pdo_update($this->system_setting,
            [
                "title" => $_GPC['title'],
                "slideshow" => iserializer($_GPC['slideshow']),
                "phone" => $_GPC['phone'],
                "appid" => $_GPC['appid'],
                "appsecret" => $_GPC['appsecret']
            ], ["id" => $_GPC['id']]
        );
    }

    message("保存成功", $this->createWebUrl('system_setting'), "success");
}

$system_setting = pdo_getall($this->system_setting)[0];
$system_setting['slideshow'] = iunserializer($system_setting['slideshow']);

include_once $this->template('system_setting');