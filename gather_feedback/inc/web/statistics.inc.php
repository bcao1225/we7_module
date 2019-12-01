<?php

global $_GPC, $_W;

/*所有用户提交填空题的集合*/
$text_list = [];
/*获取填空题的id，并初始化$text_list*/
$question_text_list = pdo_getall('ims_gather_feedback_question',['type'=>3]);
foreach ($question_text_list as $question_text){
    $text_list[$question_text['id']] = [];
}

/*获取所有已提交人的信息*/
$user_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_user WHERE is_submit=1 and activity_id='.$_GPC['activity_id']);

/*通过提交人信息获取提交数据*/
foreach ($user_list as $user_key => $user) {
    $submit = pdo_get('ims_gather_feedback_submit', ['id' => $user['submit_id'],'activity_id'=>$_GPC['activity_id']]);
    $submit['data'] = iunserializer($submit['data']);

    foreach ($submit['data'] as $submit_select) {
        /*如果其中一个数组中存在desc字段，表示这个是一个填空题提交的数据*/
        if (array_key_exists('desc', $submit_select)) {
            if($submit_select['desc']!==''){
                array_push($text_list[$submit_select['parent_id']], $submit_select['desc']);
            }
        }
    }
}

/*获取题目和对应的选项*/
$parent_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question WHERE activity_id='.$_GPC['activity_id'].' ORDER BY sort');
foreach ($parent_list as $parentKey => $parent) {
    $parent_list[$parentKey]['children'] = pdo_fetchall('SELECT * FROM ims_gather_feedback_children_question WHERE parent_id = ' . $parent['id'] . ' ORDER BY select_sort desc');
}

include_once $this->template('total/total');
