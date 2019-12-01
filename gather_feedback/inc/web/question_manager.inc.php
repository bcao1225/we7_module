<?php

global $_GPC, $_W;

$arr = [
    'title' => $_GPC['title'],
    'type' => $_GPC['type'],
    'is_required' => $_GPC['is_required'],
    'is_open_remark' => $_GPC['is_open_remark'],
    'sort' => $_GPC['sort'],
    'select_type' => $_GPC['select_type']
];

switch ($_GPC['action']) {
    /*创建活动*/
    case 'add_activity':
        pdo_insert('ims_gather_feedback_activity', ['title' => $_GPC['title'], 'create_time' => time()]);
        message('创建成功', $this->createWebUrl('question_manager'));
        break;
    /*删除活动*/
    case 'delete_activity':
        /*获取问题对应的选项并删除*/
        $question_list = pdo_getall('ims_gather_feedback_question', ['activity_id' => $_GPC['activity_id']]);
        foreach ($question_list as $question) {
            /*删除问题选项*/
            pdo_delete('ims_gather_feedback_children_question', ['parent_id' => $question['id']]);
        }
        /*删除的问题*/
        pdo_delete('ims_gather_feedback_question', ['activity_id' => $_GPC['activity_id']]);
        /*最后删除活动*/
        pdo_delete('ims_gather_feedback_activity', ['id' => $_GPC['activity_id']]);
        /*删除submit表中的用户提交数据*/
        pdo_delete('ims_gather_feedback_submit',['activity_id'=>$_GPC['activity_id']]);
        /*删除用户表中的当前活动对应的用户信息*/
        pdo_delete('ims_gather_feedback_user',['activity_id'=>$_GPC['activity_id']]);

        message('删除成功', $this->createWebUrl('question_manager'), 'success');
        break;
    /*添加题目*/
    case 'add':
        if ($_W['ispost']) {
            $arr['activity_id'] = $_GPC['activity_id'];
            /*然后插入问题列表*/
            pdo_insert('ims_gather_feedback_question', $arr);
            /*获取刚刚插入的父标题的id*/
            $parent_id = pdo_insertid();
            /*判断当前提交的问题的类型*/
            if ($_GPC['type'] === '1') {
                foreach ($_GPC['radio_question'] as $radio) {
                    pdo_insert('ims_gather_feedback_children_question', ['title' => $radio, 'parent_id' => $parent_id]);
                }
            }
            if ($_GPC['type'] === '2') {
                foreach ($_GPC['check_question'] as $check) {
                    pdo_insert('ims_gather_feedback_children_question', ['title' => $check, 'parent_id' => $parent_id]);
                }
            }
            message('添加成功', $this->createWebUrl('question_manager'), 'success');
        }
        include_once $this->template('question/add_question');
        break;
    /*修改*/
    case 'update':
        if ($_W['ispost']) {
            pdo_update('ims_gather_feedback_question', $arr, ['id' => $_GPC['id']]);
            message('修改成功', $this->createWebUrl('question_manager'), 'success');
        }

        $question = pdo_get('ims_gather_feedback_question', ['id' => $_GPC['id']]);
        $question['children_list'] = pdo_getall('ims_gather_feedback_children_question', ['parent_id' => $question['id']]);

        include_once $this->template('question/add_question');
        break;
    /*删除*/
    case 'delete':

        /*删除题目*/
        pdo_delete('ims_gather_feedback_question', ['id' => $_GPC['id']]);
        /*删除选项*/
        pdo_delete('ims_gather_feedback_children_question', ['parent_id' => $_GPC['id']]);
        message('删除成功', $this->createWebUrl('question_manager'), 'success');
        break;
    /*排序选项*/
    case 'select_sort':
        foreach ($_GPC as $key => $post_data) {
            if (strpos($key, 'selectSort') !== false) {
                $id = explode('_', $key)[1];
                pdo_update('ims_gather_feedback_children_question', ['select_sort' => $post_data], ['id' => $id]);
            }
        }
        message('保存成功', $this->createWebUrl('question_manager'), 'success');
        break;
    default:
        /*获取活动列表*/
        $activity_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_activity ORDER BY create_time DESC');

        foreach ($activity_list as $activity_key => $activity) {
            $mobile_url = $_W['siteroot'].'app/'.$this->createMobileUrl('index').'&activity_id='.$activity['id'];
            /*设置手机端每个活动的二维码*/
            $activity_list[$activity_key]['qrcode'] = make_qrcode($mobile_url);
            $activity_list[$activity_key]['mobile_url'] = $mobile_url;

            /*获取活动对应的题目列表*/
            $question_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question WHERE activity_id=' . $activity['id'] . ' ORDER BY sort');
            foreach ($question_list as $key => $question) {
                $children_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_children_question WHERE parent_id = ' . $question['id'] . ' ORDER BY select_sort');
                /*增加前缀*/
                $question_list[$key]['children_list'] = $this->addPrefix($children_list, $question['select_type']);
            }
            /*将题目列表插入到活动列表中*/
            $activity_list[$activity_key]['question_list'] = $question_list;
        }
        include_once $this->template('question/question_manager');
        break;
}

/*生成二维码*/
function make_qrcode($url = '')
{
    load()->library('qrcode');
    //由于phpQrcode类直接返回到浏览器，所以需要利用php缓冲器阻止他直接返回到浏览器，然后捕捉到二维码的图片流
    ob_start();//开启缓冲区
    QRcode::png($url, false, 'L', 10, 1);//生成二维码
    header('Content-Type:text/html'); //生成二维码后设置响应头
    $img = ob_get_contents();//获取缓冲区内容
    ob_end_clean();//清除缓冲区内容
    return 'data:image/jpg;base64,' . chunk_split(base64_encode($img));
}





