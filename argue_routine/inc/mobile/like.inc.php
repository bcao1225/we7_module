<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    /*点击喜欢和不喜欢*/
    case 'like_or_dislike':
        $item = pdo_get('ims_argue_routine_like',
            [
                'openid' => $_W['fans']['openid'],
                'activity_id' => $_GPC['activity_id'],
                'user_id' => $_GPC['user_id']
            ]
        );

        if ($item != null) {
            pdo_delete('ims_argue_routine_like', ['id' => $item['id']]);
        }

        pdo_insert('ims_argue_routine_like', [
            'openid' => $_W['fans']['openid'],
            'like_or_dislike' => $_GPC['like_or_dislike'],
            'user_id' => $_GPC['user_id'],
            'activity_id' => $_GPC['activity_id'],
            'create_time' => time()
        ]);

        exit('');
    /*通过评论获取当前喜欢和不喜欢的数字*/
    case 'get_like_and_dislike':
        $like = pdo_getall('ims_argue_routine_like', ['user_id' => $_GPC['user_id'], 'like_or_dislike' => 1]);
        $dislike = pdo_getall('ims_argue_routine_like', ['user_id' => $_GPC['user_id'], 'like_or_dislike' => 0]);
        exit(json_encode(['like' => $like, 'dislike' => $dislike]));
}