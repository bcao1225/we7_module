<?php

global $_GPC, $_W;

$arr = [];

foreach ($_GPC as $key => $value) {
    $id = explode('-', $key)[1];

    /*判断是否是单选字段或多选字段*/
    if (strpos($key, 'radio') !== false || strpos($key, 'check') !== false || strpos($key, 'text') !== false) {
        array_push($arr, [
            'id' => $id,
            'value' => $value
        ]);
    }

    /**
     * 判断是否是备注字段
     * 说明：遇到是备注字段时，首先遍历已经存储到数组的数据，
     *      通过判断每个item的id是否与当前备注字段的id相等，存储的相应的item中
     */
    if (strpos($key, 'remark') !== false) {
        foreach ($arr as $dataKey => $dataValue) {
            if ($dataValue['id'] === $id) {
                $arr[$dataKey]['remake'] = $value;
            }
        }
    }
}

$user_id = pdo_get('ims_gather_feedback_user',['openid'=>$_W['fans']['openid']],['id']);

/*将处理好的数据保存到数据库中*/
pdo_insert('ims_gather_feedback_submit', [
    'user_id'=>$user_id['id'],
    'data'=>iserializer($arr)
]);

/*将当前用户标注为已添加*/
pdo_update('ims_gather_feedback_user',['is_submit'=>'1'],['id'=>$user_id['id']]);


