<?php

global $_GPC, $_W;

/*所有用户提交填空题的集合*/
$text_list = [];

/*所有题目备注的集合*/
$remark_list = [];

/*获取填空题的id，并初始化$text_list*/
$question_text_list = pdo_getall('ims_gather_feedback_question', ['type' => 3]);
foreach ($question_text_list as $question_text) {
    $text_list[$question_text['id']] = [];
}

/*获取所有已提交人的信息*/
$user_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_user WHERE is_submit=1 and activity_id=' . $_GPC['activity_id']);

/*通过提交人信息获取提交数据*/
foreach ($user_list as $user_key => $user) {
    $submit = pdo_get('ims_gather_feedback_submit', ['user_id' => $user['id'], 'activity_id' => $_GPC['activity_id']]);
    $submit['data'] = iunserializer($submit['data']);

    foreach ($submit['data'] as $submit_select) {
        /*如果其中一个数组中存在desc字段，表示这个是一个填空题提交的数据*/
        if (array_key_exists('desc', $submit_select)) {
            if ($submit_select['desc'] !== '') {
                array_push($text_list[$submit_select['parent_id']], $submit_select['desc']);
            }
            /*否则就是一个选择题，获取选择题的备注*/
        } else {
            /*判断对应存储备注的数组是否初始化*/
            if (!array_key_exists($submit_select['parent_id'], $remark_list)) {
                $remark_list[$submit_select['parent_id']] = [];
            }

            /*备注是否为空*/
            if ($submit_select['remake'] !== '') {
                /*存储题目名称*/
                $title = '';

                /*判断click_children_id是否是一个数组，如果是则是一个多选题*/
                if (is_array($submit_select['click_children_id'])) {
                    foreach ($submit_select['click_children_id'] as $click_children_id) {
                        /*获取指定的选项*/
                        $select = pdo_get('ims_gather_feedback_children_question', ['id' => $click_children_id]);
                        $title = $title . $select['title'] . '，';
                    }
                } else {
                    /*获取指定的选项*/
                    $select = pdo_get('ims_gather_feedback_children_question', ['id' => $submit_select['click_children_id']]);
                    $title = $select['title'];
                }

                array_push($remark_list[$submit_select['parent_id']], [
                    /*提交人头像*/
                    'avatar' => $user['avatar'],
                    /*提交人昵称*/
                    'nickname' => $user['nickname'],
                    /*选项标题*/
                    'title' => $title,
                    /*备注内容*/
                    'remake' => $submit_select['remake']
                ]);
            }
        }
    }
}

/*通过日期统计用户提交的次数*/
$submit_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_submit WHERE activity_id=' . $_GPC['activity_id'] . ' ORDER BY create_time');
$create_for_submit_list = [];

foreach ($submit_list as $submit) {
    $create_time = date('Y-m-d', intval($submit['create_time']));
    if (!array_key_exists($create_time, $create_for_submit_list)) {
        $create_for_submit_list[$create_time] = 1;
    } else {
        $create_for_submit_list[$create_time] = $create_for_submit_list[$create_time] + 1;
    }
}

/*获取题目和对应的选项*/
$parent_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question WHERE activity_id=' . $_GPC['activity_id'] . ' ORDER BY sort');
foreach ($parent_list as $parentKey => $parent) {
    $parent_list[$parentKey]['children'] = pdo_fetchall('SELECT * FROM ims_gather_feedback_children_question WHERE parent_id = ' . $parent['id'] . ' ORDER BY select_sort desc');
}

include_once $this->template('total/total');
