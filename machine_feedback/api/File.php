<?php

require_once __DIR__ . '/Api.php';
defined('IN_IA') or exit('Access Denied');

class File extends Api{
    /*图片上传*/
    public function upload_img(){
        load()->func('file');
        /*图片名称*/
        $img_name = time().'.jpeg';
        //保存上传文件
        file_move($_FILES['upload_img']['tmp_name'], MODULE_ROOT . '/lib/web/upload_img/'.$img_name);
        $this->result(0,'上传成功',MODULE_URL.'lib/web/upload_img/'.$img_name);
    }

    /*视频上传*/
    public function upload_video(){
        load()->func('file');
        $temp_arr = explode(".", $_FILES['upload_video']['name']);
        $file_ext = array_pop($temp_arr);
        $file_ext = trim($file_ext);
        $file_ext = strtolower($file_ext);

        /*视频名称*/
        $img_name = time().'.'.$file_ext;
        //保存上传文件
        file_move($_FILES['upload_video']['tmp_name'], MODULE_ROOT . '/lib/web/upload_video/'.$img_name);
        $this->result(0,'上传成功',MODULE_URL.'lib/web/upload_video/'.$img_name);
    }
}