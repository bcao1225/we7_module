<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    //发表评论
    case 'add_comment':
        pdo_update('ims_argue_routine_user', ['comment' => $_GPC['comment']],
            [
                'openid' => $_W['fans']['openid'],
                'activity_id' => $_GPC['activity_id']
            ]);
        exit(json_encode(['user' => get_user($_GPC['activity_id'])]));
    //添加一条观点
    case 'add_viewpoint':
        pdo_insert('ims_argue_routine_user', [
            'avatar' => $_W['fans']['avatar'],
            'nickname' => $_W['fans']['nickname'],
            'openid' => $_W['fans']['openid'],
            'activity_id' => $_GPC['activity_id'],
            'viewpoint' => $_GPC['viewpoint'],
            'create_time' => time()
        ]);
        $user = get_user($_GPC['activity_id']);
        $user['percent'] = $this->percent($_GPC['activity_id']);
        exit(json_encode(['user' => $user]));
    //获得百分比
    case 'get_percent':
        exit(json_encode($this->percent($_GPC['activity_id'])));
    //获取所有评论
    case 'get_comment_list':
        $sql = "SELECT * FROM ims_argue_routine_user WHERE activity_id=" . $_GPC['activity_id'] . " AND viewpoint=1 AND comment!='' ORDER BY like_num DESC";
        $comment_list = pdo_fetchall($sql);
        $sql = "SELECT * FROM ims_argue_routine_user WHERE activity_id=" . $_GPC['activity_id'] . " AND viewpoint=0 AND comment!='' ORDER BY like_num DESC";
        $no_comment_list = pdo_fetchall($sql);
        exit(json_encode(['square' => $comment_list, 'no_square' => $no_comment_list]));
    //获取当前用户的在当前活动中的所有数据
    case 'get_user':
        exit(json_encode(['user' => get_user($_GPC['activity_id'])]));
}

function get_user($activity_id)
{
    global $_W;
    return pdo_fetch('SELECT * FROM ims_argue_routine_user WHERE activity_id=' . $activity_id . " AND openid='" . $_W['fans']['openid'] . "'");
}