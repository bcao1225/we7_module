<?php

global $_GPC, $_W;

/*获取粉丝信息*/
if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();
}

$user_data = [
    'avatar'=>$_W['fans']['avatar'],
    'nickname' => $_W['fans']['nickname'],
    'openid' => $_W['fans']['openid'],
    'address' => $_W['fans']['tag']['province'] . '-' . $_W['fans']['tag']['city']
];

/*将粉丝信息保存*/
$user = pdo_get('ims_gather_feedback_user', ['openid' => $_W['fans']['openid']]);
if ($user) {
    pdo_update('ims_gather_feedback_user', $user_data, ['id' => $user['id']]);
} else {
    pdo_insert('ims_gather_feedback_user', $user_data);
}

/*系统设置*/
$system_setting = pdo_getall('ims_gather_feedback_system_setting')[0];

/*题目列表*/
$question_list = pdo_getall('ims_gather_feedback_question');
foreach ($question_list as $key => $value) {
    $question_list[$key]['question'] = iunserializer($value['question']);
}

include_once $this->template('index');


