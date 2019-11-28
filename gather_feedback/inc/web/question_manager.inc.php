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
    /*添加题目*/
    case 'add':
        if ($_W['ispost']) {
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
        $question['children_list'] = pdo_getall('ims_gather_feedback_children_question',['parent_id'=>$question['id']]);

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
        foreach ($_GPC as $key=>$post_data){
            if(strpos($key,'selectSort') !== false){
                $id = explode('_',$key)[1];
                pdo_update('ims_gather_feedback_children_question',['select_sort'=>$post_data],['id'=>$id]);
            }
        }
        message('保存成功',$this->createWebUrl('question_manager'),'success');
        break;
    default:
        $question_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_question ORDER BY sort');
        foreach ($question_list as $key => $question) {
            $children_list = pdo_fetchall('SELECT * FROM ims_gather_feedback_children_question WHERE parent_id = '.$question['id'].' ORDER BY select_sort');
            /*增加前缀*/
            $question_list[$key]['children_list'] = $this->addPrefix($children_list, $question['select_type']);
        }
        include_once $this->template('question/question_manager');
        break;
}



