<?php

global $_W, $_GPC;

/*书本管理*/
switch ($_GPC['action']) {
    /*更改书本类型*/
    case 'update_type':
        pdo_update('ims_book_store_book', ['type' => $_GPC['type_id']], [
            'guild_id' => cache_read('guild_id'),
            'bookrack_id' => $_GPC['bookrack_id'],
            'id' => $_GPC['book_id']
        ],'AND');
        $this->result(0, '保存成功', ['type' => pdo_get('ims_book_store_type', ['id' => $_GPC['type_id']])]);
        break;
}