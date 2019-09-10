<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/26
 * Time: 16:29
 */
if (!(defined('IN_IA')))
{
    exit('Access Denied');
}

//上传图片
class upimg
{
    public static  $instance;
    public static  function Instance(){
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function index($file)
    {
        global $_GPC, $_W;
//        $file = $_FILES['file'];//得到传输的数据
        if($file['error']==1){
            $ift=ini_set('upload_max_filesize', '60M');
            if(!$ift){
                exit(json_encode(['errno'=>500,'message'=>'上传图片最大不能超过'.ini_get('upload_max_filesize').'!','data'=>'']));
            }
            $file = $_FILES['file'];//得到传输的数据
        }
        //得到文件名称
        $name = $file['name'];
        $type = strtolower(substr($name,strrpos($name,'.')+1)); //得到文件类型，并且都转化成小写
        $allow_type = array('jpg','jpeg','gif','png'); //定义允许上传的类型
//判断文件类型是否被允许上传
        if(!in_array($type, $allow_type)){
            //如果不被允许，则直接停止程序运行
            exit(json_encode(['errno'=>500,'message'=>'请上传\'jpg\',\'jpeg\',\'gif\',\'png\'类型的图片!','data'=>'']));
        }
        $imgname=$_GPC['names'].md5(random(5).time().$_GPC['uid'].$_W['uniacid']);
        load()->func('file');
        if (empty($_W['setting']['remote']['type'])) {
            $files=file_upload($file, 'image', 'images/market/'.$_W['uniacid'].'/'.$_GPC['uid'].'/'.$_GPC['names'].'/'.$imgname.'.'.$type);
            $re=['all'=>tomedia($files['path']),'short'=>$files['path']];
            exit(json_encode(['errno'=>0,'message'=>'上传成功！','data'=>$re]));
        }
        $files=file_upload($file, 'image', 'images/market/'.$_W['uniacid'].'/'.$_GPC['uid'].'/'.$_GPC['names'].'/'.$imgname.'.'.$type);
        @file_remote_upload($files['path']);
        $re=['all'=>tomedia($files['path']),'short'=>$files['path']];

        exit(json_encode(['errno'=>0,'message'=>'上传成功！','data'=>$re]));
    }
    //删除图片
    public function delImg($imgurl)
    {
        global  $_W;
        //拆分url链接去除开头，传值删除
        $fileurl=str_replace($_W['siteroot'].'attachment/','',$imgurl);
        load()->func('file');
        if (empty($_W['setting']['remote']['type'])) {
            if(file_is_image($fileurl)){
                @file_delete($fileurl);
            }
            return true;
        }
        $arr=explode('/',$imgurl);
        file_remote_delete($arr[count($arr)-1]);
        return true;
    }

}