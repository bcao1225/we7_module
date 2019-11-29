<?php

global $_GPC, $_W;

/*存储查询到的结果*/
$user_list = [];

switch ($_GPC['action']) {
    case 'delete':
        pdo_delete('ims_gather_feedback_user', ['id' => $_GPC['id']]);
        /*删除对应提交的数据*/
        pdo_delete('ims_gather_feedback_submit', ['user_id' => $_GPC['id']]);
        break;
    default:
        /*总用户量*/
        switch ($_GPC['type']){
            case '0':
                $total = pdo_fetch('SELECT count(1) as total FROM ims_gather_feedback_user WHERE submit_id IS NULL')['total'];
                break;
            case '1':
                $total = pdo_fetch('SELECT count(1) as total FROM ims_gather_feedback_user WHERE submit_id IS NOT NULL')['total'];
                break;
            default:
                $total = pdo_fetch('SELECT count(1) as total FROM ims_gather_feedback_user')['total'];
                break;
        }

        /*每页数量*/
        $page_num = 7;

        $page_arr = [];
        for ($i = 1; $i <= ceil($total / $page_num); $i++) {
            array_push($page_arr, $i);
        }

        /*如果是点击左侧菜单进入*/
        if($_GPC['page']===null){
            $sql = "SELECT * FROM ims_gather_feedback_user ORDER BY id DESC LIMIT 0,$page_num";
        }else{
            /*获取传过来的条数*/
            $currentIndex = ($_GPC['page'] - 1) * $page_num;
            $sql = '';
            switch ($_GPC['type']){
                case '0':
                    /*未提交*/
                    $sql = "SELECT * FROM ims_gather_feedback_user WHERE submit_id IS NULL ORDER BY id DESC LIMIT $currentIndex,$page_num";
                    break;
                case '1':
                    /*已提交*/
                    $sql = "SELECT * FROM ims_gather_feedback_user WHERE submit_id IS NOT NULL ORDER BY id DESC LIMIT $currentIndex,$page_num";
                    break;
                default:
                    /*全部*/
                    $sql = "SELECT * FROM ims_gather_feedback_user ORDER BY id DESC LIMIT $currentIndex,$page_num";
                    break;
            }
        }
        $user_list = pdo_fetchall($sql);
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
                        $str = $str . pdo_get('ims_gather_feedback_children_question', ['id' => $children_id])['title'] . ',';
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


