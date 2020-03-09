<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*清空指定书架*/
    case 'empty':
        $book_list = pdo_getall('ims_book_store_book', ['guild_id' => $_GPC['guild_id'], 'bookrack_id' => $_GPC['bookrack_id']]);

        foreach ($book_list as $book) {
            pdo_update('ims_book_store_book', ['type' => null], ['id' => $book['id']]);
        }

        $this->result(0, '清空成功', pdo_getall('ims_book_store_book', ['guild_id' => $_GPC['guild_id'], 'bookrack_id' => $_GPC['bookrack_id']]));
        break;
    case 'get_all':
        $this->result(0, '获取成功', pdo_getall('ims_book_store_bookrack', ['guild_id' => $_GPC['guild_id']]));
        break;
}