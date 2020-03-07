<?php

global $_W, $_GPC;

/*书本管理*/
switch ($_GPC['action']) {
    /*更改书本类型*/
    /**
     * 更改书本类型，ajax请求
     * $guild_id 馆别id
     * bookrack_id 书架id
     * book_id 图书的id
     * type_id 需要修改的type的id
     */
    case 'update_type':

        $arr = ['guild_id' => cache_read('guild_id'), 'bookrack_id' => $_GPC['bookrack_id'], 'id' => $_GPC['book_id']];
        $book = pdo_get('ims_book_store_book', $arr);

        /*如果当前图书存在类型*/
        if ($book['type']) {
            pdo_update('ims_book_store_book', ['type' => null], $arr);
            $this->result(0, '取消成功', ['type' => ['color' => '#fff', 'id' => null]]);
        }

        pdo_update('ims_book_store_book', ['type' => $_GPC['type_id']], $arr, 'AND');
        $this->result(0, '保存成功', ['type' => pdo_get('ims_book_store_type', ['id' => $_GPC['type_id']])]);
        break;
}