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
}