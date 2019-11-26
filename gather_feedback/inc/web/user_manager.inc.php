<?php

global $_GPC, $_W;

$user_list = [];

switch ($_GPC['action']) {
    case 'query':
        if ($_GPC['type'] === '0') {
            $user_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_user WHERE submit_id is null');
        } else {
            $user_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_user WHERE submit_id is not null');
        }
        break;
    case 'delete':
        pdo_delete('ims_gather_feedback_user', ['id' => $_GPC['id']]);
        break;
    default:
        $user_list = pdo_getall('ims_gather_feedback_user');
        break;
}

foreach ($user_list as $key => $user) {
    /*获取提交信息，如果没有返回false*/
    $submit_data = pdo_get('ims_gather_feedback_submit', ['user_id' => $user['id']]);

    if ($submit_data !== false) {
        $submit_data['data'] = iunserializer($submit_data['data']);
        $arr = [];
        /*开始遍历每个问题的答案*/
        foreach ($submit_data['data'] as $submit) {
            $child_data = [];

            $parent = pdo_get('ims_gather_feedback_question', ['id' => $submit['parent_id']]);

            /*判断是否有click_children_id属性*/
            if (array_key_exists('click_children_id', $submit)) {
                if (is_array($submit['click_children_id'])) {
                    $str = '';
                    foreach ($submit['click_children_id'] as $children_id) {
                        $str = $str.pdo_get('ims_gather_feedback_children_question', ['id' => $children_id])['title'].',';
                    }
                    $child_data['select'] = $str;
                } else {
                    $child_data['select'] = pdo_get('ims_gather_feedback_children_question', ['id' => $submit['click_children_id']])['title'];
                }
            }

            /*判断是否有备注*/
            if (array_key_exists('remake', $submit)) {
                $child_data['remake'] = $submit['remake'];
            }

            /*判断是否有描述*/
            if (array_key_exists('desc', $submit)) {
                $child_data['desc'] = $submit['desc'];
            }

            $arr[$parent['title']] = $child_data;
        }

        /*整理后的数据*/
        $user_list[$key]['message_data'] = $arr;
    }
    $user_list[$key]['submit_data'] = $submit_data;
}

include_once $this->template('user/user_manager');


