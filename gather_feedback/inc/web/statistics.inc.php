<?php

global $_GPC,$_W;

/*获取用户提交的数据*/

/*获取题目和对应的选项*/
$parent_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question ORDER BY sort');

foreach ($parent_list as $parentKey=>$parent){
    $parent_list[$parentKey]['children'] = pdo_get('ims_gather_feedback_children_question',['parent_id'=>$parent['id']]);
}

include_once $this->template('statistics');
