<?php

/*书架管理*/
global $_W, $_GPC;

cache_write('guild_id', $_GPC['guild_id']);

$guild_id = cache_read('guild_id');

switch ($_GPC['action']) {
    /*清空书架*/
    case 'empty':
        pdo_update('ims_book_store_book', ['type' => null], ['guild_id' => $guild_id, 'bookrack_id' => $_GPC['bookrack_id']]);
        message('清空成功', $this->createWebUrl('bookrack_manager'), 'success');
        break;
    /*批量添加书架*/
    case 'batch':
        for ($i = $_GPC['min']; $i <= $_GPC['max']; $i++) {
            pdo_insert('ims_book_store_bookrack', ['id' => $i, 'guild_id' => $guild_id]);

            /*添加书架后，直接在书架中填充100本书*/
            for ($j = 1; $j <= 100; $j++) {
                pdo_insert('ims_book_store_book',
                    [
                        'guild_id' => $guild_id,
                        'bookrack_id' => $i,
                        'id' => $j
                    ]
                );
            }
        }

        message('保存成功', $this->createWebUrl('bookrack_manager'));
        break;
    /*添加书架*/
    case 'add':
        if ($_W['ispost']) {
            pdo_insert('ims_book_store_bookrack', ['id' => $_GPC['id'], 'guild_id' => $guild_id]);

            /*添加书架后，直接在书架中填充100本书*/
            for ($i = 1; $i <= 100; $i++) {
                pdo_insert('ims_book_store_book',
                    [
                        'guild_id' => $guild_id,
                        'bookrack_id' => $_GPC['id'],
                        'id' => $i
                    ]
                );
            }

            message('保存成功', $this->createWebUrl('bookrack_manager'));
        }
        include_once $this->template('bookrack/add_bookrack');
        break;
    /*删除书架*/
    case 'delete':
        /*先删除当前书架对应的书*/
        pdo_delete('ims_book_store_book', ['guild_id' => $guild_id, 'bookrack_id' => $_GPC['id']]);
        /*再删除书架*/
        pdo_delete('ims_book_store_bookrack', ['guild_id' => $guild_id, 'id' => $_GPC['id']]);

        message('删除成功', $this->createWebUrl('bookrack_manager'), 'success');
        break;
    default:
        $bookrack_list = pdo_getall('ims_book_store_bookrack', ['guild_id' => $guild_id]);

        $guild = pdo_get('ims_book_store_guild', ['id' => $guild_id]);

        /*获取书架中所有图书*/
        foreach ($bookrack_list as $index => $bookrack) {
            $books = pdo_getall('ims_book_store_book', ['guild_id' => $guild_id, 'bookrack_id' => $bookrack['id']]);

            /*获取类型*/
            foreach ($books as $book_key => $book) {
                $books[$book_key]['type'] = pdo_get('ims_book_store_type', ['id' => $book['type']]);
            }

            $bookrack_list[$index]['books'] = $books;
        }

        $type_list = pdo_getall('ims_book_store_type');

        include_once $this->template('bookrack/bookrack_manager');
}