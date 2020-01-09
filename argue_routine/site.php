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

    /*通过活动id获取正反方百分比对比信息*/
    function percent($activity_id)
    {
        //获取正反方百分比对比数据
        $argue = pdo_fetch('SELECT COUNT(*) as count FROM ims_argue_routine_user WHERE activity_id=' . $activity_id . ' AND viewpoint=1')['count'];
        $no_argue = pdo_fetch('SELECT COUNT(*) as count FROM ims_argue_routine_user WHERE activity_id=' . $activity_id . ' AND viewpoint=0')['count'];
        //总数
        $count = $argue + $no_argue;

        if ($count === 0) {
            return ['argue' => 0, 'no_argue' => 0];
        }

        $argue = number_format($argue / $count * 100, 0);

        return ['argue' => $argue, 'no_argue' => 100 - $argue];
    }

    //网络请求，主要作用于现金红包发放
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
        $params[CURLOPT_SSLCERT] = MODULE_ROOT . '/lib/apiclient_cert.pem';
        $params[CURLOPT_SSLKEYTYPE] = 'PEM';
        $params[CURLOPT_SSLKEY] = MODULE_ROOT . '/lib/apiclient_key.pem';

        curl_setopt_array($ch, $params); //传入curl参数

        $content = curl_exec($ch); //执行
        curl_close($ch); //关闭连接
        return $content;
    }

    /**
     * 发送红包
     * @param int|string $activity_id 指定活动id
     * @param string $openid 用户的openid
     * @param int $money 发送红包金额，单位为元
     * @return array|mixed|string
     */

    public function send_redpacket($activity_id, $openid, $money)
    {
        global $_W;

        $activity = pdo_get('ims_argue_routine_activity', ['id' => $activity_id]);

        /*获取签名*/
        $arr = $this->setSign(
            [
                'nonce_str' => random(32),
                'mch_billno' => random(28, true),
                /*商户id*/
                'mch_id' => $_W['uniaccount']['setting']['payment']['wechat']['mchid'],
                /*公众号appid*/
                'wxappid' => $_W['uniaccount']['key'],
                /*商户名称*/
                'send_name' => $activity['bonus_name'],
                /*用户openid*/
                're_openid' => $openid,
                /*发放金额，单位为分*/
                'total_amount' => $money * 100,
                /*总人数*/
                'total_num' => 1,
                /*祝福语*/
                'wishing' => $activity['bonus_desc'],
                /*ip地址*/
                'client_ip' => CLIENT_IP,
                /*活动名称*/
                'act_name' => '猜灯谜抢红包活动',
                /*备注*/
                'remark' => '猜越多得越多，快来抢！',
                'scene_id' => 'PRODUCT_1'
            ]
        );
        $xml = array2xml($arr);
        $content = $this->postData('https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack', $xml);
        return xml2array($content);
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