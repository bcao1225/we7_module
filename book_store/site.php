<?php
/**
 * gather_feedback模块微站定义
 *
 * @author QQRPazWaNPgW
 * @url
 */

defined('IN_IA') or exit('Access Denied');

class Book_storeModuleSite extends WeModuleSite
{
    /**
     * @param $type 0表示没问题，1表示出现错误
     * @param string $message
     * @param array $data
     */
    public function result($type, $message = '', $data = [])
    {
        exit(json_encode(['type' => $type, 'message' => $message, 'data' => $data]));
    }

    /*生成二维码*/
    public function make_qrcode($url = '')
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
    
    /**
     * 创建(导出)Excel数据表格
     * @param  array   $list 要导出的数组格式的数据
     * @param  string  $filename 导出的Excel表格数据表的文件名
     * @param  array   $header Excel表格的表头
     * @param  array   $index $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值)
     * 比如: $header = array('编号','姓名','性别','年龄');
     *       $index = array('id','username','sex','age');
     *       $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24));
     * @return [array] [数组]
     */
    protected function createtable($list,$filename,$header=array(),$index = array()){
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename=".$filename.".xls");
        $teble_header = implode("\t",$header);
        $strexport = $teble_header."\r";
        foreach ($list as $row){
            foreach($index as $val){
                $strexport.=$row[$val]."\t";
            }
            $strexport.="\r";

        }
        $strexport=iconv('UTF-8',"GB2312//IGNORE",$strexport);
        exit($strexport);
    }
}