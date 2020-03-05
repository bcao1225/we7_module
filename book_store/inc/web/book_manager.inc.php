<?php

global $_W, $_GPC;

/*书本管理*/

switch ($_GPC['action']) {
    case 'add':
        pdo_insert('ims_book_store_book', [
            'id' => $_GPC['book_id'],
            'guild_id' => cache_read('guild_id'),
            'bookrack_id' => $_GPC['bookrack_id'],
            'type' => $_GPC['book_type']
        ]);

        pdo_query('UPDATE ims_book_store_bookrack SET book_num = book_num+1 WHERE id=' . $_GPC['bookrack_id'] . ' AND guild_id=' . cache_read(['guild_id']));

        message('保存成功', $this->createWebUrl('bookrack_manager'), 'success');
        break;
}
