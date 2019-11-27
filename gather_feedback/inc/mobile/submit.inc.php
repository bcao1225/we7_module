<?php

global $_GPC, $_W;

/*系统设置*/
$system_setting = pdo_getall('ims_gather_feedback_system_setting')[0];
$system_setting['title'] = '提交完成';

$arr = [];

foreach ($_GPC as $key => $value) {
    $id = explode('-', $key)[1];

    /*判断是否是单选字段或多选字段*/
    if (strpos($key, 'radio') !== false || strpos($key, 'check') !== false) {
        array_push($arr, [
            'parent_id' => $id,
            'click_children_id' => $value
        ]);

        if(is_array($value)){
            foreach ($value as $click_children_id){
                /*多选题次数加1*/
                pdo_query('UPDATE ims_gather_feedback_children_question SET select_num = select_num + 1 WHERE id = ' . $click_children_id);
            }
        }else{
            /*单选题次数加1*/
            pdo_query('UPDATE ims_gather_feedback_children_question SET select_num = select_num + 1 WHERE id = ' . $value);
        }
    }

    /*判断是否是填空题*/
    if (strpos($key, 'text') !== false) {
        array_push($arr, [
            'parent_id' => $id,
            'desc' => $value
        ]);
    }

    /**
     * 判断是否是备注字段
     * 说明：遇到是备注字段时，首先遍历已经存储到数组的数据，
     *      通过判断每个item的id是否与当前备注字段的id相等，存储的相应的item中
     */
    if (strpos($key, 'remark') !== false) {
        foreach ($arr as $dataKey => $dataValue) {
            if ($dataValue['parent_id'] === $id) {
                $arr[$dataKey]['remake'] = $value;
            }
        }
    }
}

$user_id = pdo_get('ims_gather_feedback_user', ['openid' => $_W['fans']['openid']], ['id']);

/*将处理好的数据保存到数据库中*/
pdo_insert('ims_gather_feedback_submit', [
    'user_id' => $user_id['id'],
    'data' => iserializer($arr),
    'create_time' => time()
]);

/*设置submit_id*/
pdo_update('ims_gather_feedback_user', ['submit_id' => pdo_insertid()], ['id' => $user_id['id']]);

include_once $this->template('submit_complete');


