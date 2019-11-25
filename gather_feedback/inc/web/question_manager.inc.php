<?php

global $_GPC, $_W;

switch ($_GPC['action']) {
    /*添加问题*/
    case 'add':
        if ($_W['ispost']) {
            pdo_insert('ims_gather_feedback_question', tidyPostData($_GPC));
            message('添加成功', $this->createWebUrl('question_manager'), 'success');
        }
        include_once $this->template('question/add_question');
        break;
    /*修改*/
    case 'update':
        if ($_W['ispost']) {
            pdo_update('ims_gather_feedback_question', tidyPostData($_GPC), ['id' => $_GPC['id']]);
            message('修改成功', $this->createWebUrl('question_manager'), 'success');
        }
        $question = pdo_get('ims_gather_feedback_question', ['id' => $_GPC['id']]);
        $question['question'] = iunserializer($question['question']);
        include_once $this->template('question/add_question');
        break;
    /*删除*/
    case 'delete':
        pdo_delete('ims_gather_feedback_question',['id'=>$_GPC['id']]);
        message('删除成功',$this->createWebUrl('question_manager'),'success');
        break;
    /*问题总览*/
    default:
        $question_list = pdo_getall('ims_gather_feedback_question');

        foreach ($question_list as $key => $item) {
            $question_list[$key]['question'] = iunserializer($item['question']);
        }

        include_once $this->template('question/question_manager');
        break;
}

function tidyPostData($_GPC){
    $arr = [
        'title' => $_GPC['title'],
        'type' => $_GPC['type'],
        'is_required' => $_GPC['is_required'],
        'is_open_remark' => $_GPC['is_open_remark'],
        'sort' => $_GPC['sort']
    ];

    /*判断当前提交的问题的类型*/
    if ($_GPC['type'] === '1') {
        $arr['question'] = iserializer($_GPC['radio_question']);
    }
    if ($_GPC['type'] === '2') {
        $arr['question'] = iserializer($_GPC['check_question']);
    }

    return $arr;
}



