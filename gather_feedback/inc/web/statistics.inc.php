<?php

global $_GPC,$_W;

/*所有用户提交填空题的集合*/
$text_list = [];

/*获取提交人的信息*/
$user_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_user WHERE submit_id IS NOT NULL');

foreach ($user_list as $user_key => $user){
    $submit = pdo_get('ims_gather_feedback_submit',['id'=>$user['submit_id']]);
    $submit['data'] = iunserializer($submit['data']);

    foreach ($submit['data'] as $submit_select){
        /*每个选项中如果存在desc字段，表示这个是一个填空题提交的数据*/
        if(array_key_exists('desc',$submit_select)){
            array_push($text_list,$submit_select);
        }
    }
}

/*获取题目和对应的选项*/
$parent_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question ORDER BY sort');

foreach ($parent_list as $parentKey=>$parent){
    $parent_list[$parentKey]['children'] = pdo_getall('ims_gather_feedback_children_question',['parent_id'=>$parent['id']]);
}

include_once $this->template('total/total');
