<?php

global $_GPC, $_W;

$signPackage = $_W['account']['jssdkconfig'];//微擎封装好的jssdk签名包的内容

/*获取粉丝信息*/
if (empty($_W['fans']['nickname'])) {
    mc_oauth_userinfo();
}

$system_setting = pdo_getall('ims_gather_feedback_system_setting')[0];

/*题目列表*/
$question_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question ORDER BY sort');
foreach ($question_list as $key => $question) {
    $children_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_children_question WHERE parent_id = '.$question['id'].' ORDER BY select_sort');
    $question_list[$key]['children_list'] = $this->addPrefix($children_list, $question['select_type']);
}

$user_data = [
    'avatar' => $_W['fans']['avatar'],
    'nickname' => $_W['fans']['nickname'],
    'openid' => $_W['fans']['openid'],
    'address' => $_W['fans']['tag']['province'] . '-' . $_W['fans']['tag']['city']
];

$user = pdo_get('ims_gather_feedback_user', ['openid' => $_W['fans']['openid']]);
/*判断用户是否存储在数据库中*/
if ($user) {
    pdo_update('ims_gather_feedback_user', $user_data, ['id' => $user['id']]);
    /*判断是否提交数据*/
    if ($user['submit_id'] !== null) {
        include_once $this->template('submit_complete');
    }else{
        include_once $this->template('index');
    }
} else {
    /*将粉丝信息保存*/
    pdo_insert('ims_gather_feedback_user', $user_data);
    include_once $this->template('index');
}




