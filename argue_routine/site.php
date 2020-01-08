<?php
/**
 * argue_routine模块微站定义
 *
 * @author QQRPazWaNPgW
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Argue_routineModuleSite extends WeModuleSite
{

    public $key = "cccda8e15f42a59ce335add985575479";

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

    function postData($url, $postfields)
    {
        global $_W;

        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $postfields;
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        //以下是证书相关代码
        $params[CURLOPT_SSLCERTTYPE] = 'PEM';
        /*$params[CURLOPT_SSLCERT] = $_W['uniaccount']['setting']['payment']['wechat']['wechat_refund']['cert'];*/
        $params[CURLOPT_SSLCERT] = MODULE_ROOT . '/lib/apiclient_cert.pem';
        $params[CURLOPT_SSLKEYTYPE] = 'PEM';
        $params[CURLOPT_SSLKEY] = MODULE_ROOT . '/lib/apiclient_key.pem';

        curl_setopt_array($ch, $params); //传入curl参数

        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }

    //获取零时路径的ssl
    public function getTmpPathByContent($content)
    {
        static $tmpFile = null;
        $tmpFile = tmpfile();
        fwrite($tmpFile, $content);
        $tempPemPath = stream_get_meta_data($tmpFile);
        return $tempPemPath['uri'];
    }

    /**
     * 获取带签名的数组，获取签名
     * @param array $arr
     * @return array
     */
    public function setSign($arr)
    {
        global $_W;
        //去除空值
        $arr = array_filter($arr);
        if (isset($arr['sign'])) {
            unset($arr['sign']);
        }
        //按照键名字典排序
        ksort($arr);
        //生成url格式的字符串
        $str = urldecode(http_build_query($arr)) . '&key=' . $_W['uniaccount']['setting']['payment']['wechat']['apikey'];
        $arr['sign'] = strtoupper(md5($str));
        return $arr;
    }
}