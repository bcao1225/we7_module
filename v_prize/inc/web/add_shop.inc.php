<?php

global $_W,$_GPC;

if($_W['ispost']){

    pdo_insert("ims_v_prize_shop",
        [
            'id'=>$_GPC['id'],
            "name"=>$_GPC['name'],
            "imgs"=>iserializer($_GPC['imgs']),
            'price'=>$_GPC['price'],
            'inventory'=>$_GPC['inventory'],
            'trace'=>$_GPC['trace'],
            'particulars'=>$_GPC['particulars']
        ],true);

    message("保存成功",$this->createWebUrl("shop_manager"),"success");
}

$shop = pdo_get("ims_v_prize_shop",['id'=>$_GPC['id']]);
$shop['imgs'] = iunserializer($shop['imgs']);

include_once $this->template("add_shop");