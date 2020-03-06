<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    /**
     * 通过书架获取书架上的书籍信息
     * @guild_id 馆别id
     * @bookrack_id 书架id
     */
    case 'get_all':
        $book_list = pdo_getall('ims_book_store_book',
            [
                'guild_id' => $_GPC['guild_id'],
                'bookrack_id' => $_GPC['bookrack_id']
            ]);

        foreach ($book_list as $index => $book) {
            $book_list[$index]['type'] = pdo_get('ims_book_store_type', ['id' => $book['type']]);
        }
        $this->result(0, '获取成功', $book_list);
        break;
    /**
     * 更改当前书本的类型
     * @guild_id 馆别id
     * @bookrack_id 书架id
     * @book_id 书本id
     * @type_id 类型id
     */
    case 'update_type':
        $arr = [
            'guild_id' => $_GPC['guild_id'],
            'bookrack_id' => $_GPC['bookrack_id'],
            'id' => $_GPC['book_id']
        ];

        /*判断需要做更改的书本是否已经存在了类型*/
        $book = pdo_get('ims_book_store_book', $arr);

        if ($book['type']) {
            pdo_update('ims_book_store_book', ['type' => null], $arr);
            $type = pdo_get('ims_book_store_type', $book['type']);
            $type['color'] = '#fff';
            $this->result(0, '取消成功', $type);
        }

        pdo_update('ims_book_store_book', ['type' => $_GPC['type_id']], $arr, 'AND');

        $this->result(0, '更改成功', pdo_get('ims_book_store_type', ['id' => $_GPC['type_id']]));
        break;
}