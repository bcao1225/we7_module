<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    case 'get_all':
        $this->result(0, '获取成功', pdo_getall('ims_book_store_bookrack', ['guild_id' => $_GPC['guild_id']]));
        break;
}