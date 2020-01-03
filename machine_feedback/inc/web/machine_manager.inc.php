<?php

global $_W, $_GPC;

switch ($_GPC['action']) {
    /*添加或修改机器*/
    case 'add_and_update':
        if ($_W['ispost']) {
            $arr = [
                'id' => $_GPC['id'],
                'name' => $_GPC['name'],
                'type' => $_GPC['type'],
                //技术配置文件
                'option_file'=>$_GPC['option_file'],
                'option_imgs'=>iserializer($_GPC['option_imgs']),
                //操作说明书
                'operation_file'=>$_GPC['operation_file'],
                'operation_imgs'=>iserializer($_GPC['operation_imgs']),
                //电器原理图
                'theory_file'=>$_GPC['theory_file'],
                'theory_imgs'=>iserializer($_GPC['theory_imgs']),

                'delivery_time' => $_GPC['delivery_time'],
                'imgs' => iserializer($_GPC['imgs']),
                'create_time' => time()
            ];

            pdo_insert('ims_machine_feedback_machine', $arr, true);
            message('保存成功', $this->createWebUrl('machine_manager'), 'success');
        }

        $machine = pdo_get('ims_machine_feedback_machine', ['id' => $_GPC['id']]);
        $machine['imgs'] = iunserializer($machine['imgs']);

        include_once $this->template('machine/add_machine');
        break;
    /*删除机器*/
    case 'delete':
        pdo_delete('ims_machine_feedback_machine', ['id' => $_GPC["id"]]);
        message('删除成功', $this->createWebUrl('machine_manager'), 'success');
        break;
    /*文件上传，这是一个ajax接口，必须返回json字符串*/
    case 'file_upload':
        load()->func('file');
        $temp_arr = explode(".", $_FILES['file']['name']);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);
        /*文件名称*/
        $img_name = time() . '.' . $file_ext;
        //保存上传文件
        file_move($_FILES['file']['tmp_name'], MODULE_ROOT . '/lib/web/' . $_GPC['folder_name'] . '/' . $img_name);
        exit(json_encode(['img_url' => MODULE_URL . 'lib/web/' . $_GPC['folder_name'] . '/' . $img_name]));
        break;
    //查看所有机器
    default:
        $machine_list = pdo_getall('ims_machine_feedback_machine');
        foreach ($machine_list as $key => $machine) {
            $response = $this->account_api->getCodeUnlimit('id=' . $machine['id'], 'page/index/index', 150);

            $machine_list[$key]['qrcode'] = base64_encode($response);

            //通过机器id获取每个机器的反馈
            $count = pdo_fetch('SELECT COUNT(1) as count FROM ims_machine_feedback_submit WHERE machine_id=' . $machine['id'])['count'];
            $machine_list[$key]['count'] = $count;

            $imgs = iunserializer($machine['imgs']);

            foreach ($imgs as $img_key => $img) {
                $imgs[$img_key] = tomedia($img);
            }
        }
        $machine_list[$key]['imgs'] = $imgs;
        include_once $this->template('machine/machine_manager');
}

