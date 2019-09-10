<?php
/**
 * 本破解程序由易福网提供
 * 易福网www.efwww.com
 * 承接网站建设、公众号搭建、小程序建设、企业网站
 */

defined('IN_IA') or exit('Access Denied');


define('MODEL_NAME', 'monai_market');

require_once IA_ROOT . '/addons/' . MODEL_NAME . '/application/bootstrap.php';

class Monai_marketModuleWxapp extends WeModuleWxapp
{


    public function doPageApi()
    {
        Application::api();
    }


    //执行卖车上架操作


    public function doPagepaysale()

    {


        global $_GPC, $_W;

        //获取用户基本信息


        $uid = $_GPC['uid'];


        $uniacid = $_W['uniacid'];


        $carid = $_GPC['car'];

        //获取用户信息，防止直接跳转当前页面，没有member表信息，直接查询fans表


        $fans = pdo_get('mc_mapping_fans', array('uniacid' => $uniacid, 'uid' => $uid));


        if (!$fans) {


            return $this->result(400, '请允许获取用户信息！');


        }


        $car = pdo_get('monai_market_car_detail', array('id' => $carid, 'uid' => $uid));


        if (!$car) {


            return $this->result(500, '获取当前汽车信息失败', '');


        }


        $infosql = pdo_fetch('SELECT release_money FROM' . tablename('monai_market_info') . ' WHERE uniacid=' . $uniacid);


        $fee = $infosql['release_money'] ? $infosql['release_money'] : 0;

        //创建订单


        $inFinancedb = [


            'uid' => $uid,


            'pay_type' => 0,


            'pay_for' => 0,


            'status' => 0,


            'uniacid' => $uniacid,


            'pay_by_id' => $car['id'],


            'pay_by_table' => 'monai_market_car_detail',


            'order_money' => $fee,


            'create_time' => time()


        ];


        if ($fee <= 0) {


            $inFinancedb['status'] = 1;


            $inFinancedb['pay_time'] = time();


            $inFinancedb['pay_money'] = 0;


        }


        pdo_begin();


        $inFinance = pdo_insert('monai_market_finance', $inFinancedb);


        if (!$inFinance) {


            pdo_rollback();


            return $this->result(505, '创建订单失败，请稍候重试！');


        }


        $Financeid = pdo_insertid();

        //判断当前订单金额0元直接返回


        if ($fee <= 0) {

            $pay_params['pay'] = 2;

            $pay_params['money'] = $fee;

            $pay_params['orderid'] = $Financeid;

            pdo_commit();

            return $this->result(0, '1000', $pay_params);

        }

        //返回支付参数

        $order = array(

            'tid' => $Financeid,

            'user' => $fans['openid'], //用户OPENID

            'fee' => floatval($fee), //金额

            'title' => '汽车上架！',

        );

        pdo_commit();

        $pay_params = $this->pay($order);

        if (is_error($pay_params)) {

            return $this->result(1, '支付失败，请重试');

        }

        $pay_params['money'] = $fee;

        $pay_params['orderid'] = $Financeid;

        return $this->result(0, '', $pay_params);

    }

    //执行置顶操作

    public function doPagepaysettop()

    {

        global $_GPC, $_W;

        //获取用户基本信息

        $uid = $_GPC['uid'];

        $uniacid = $_W['uniacid'];

        $carid = $_GPC['car'];

        //获取用户信息，防止直接跳转当前页面，没有member表信息，直接查询fans表

        $fans = pdo_get('mc_mapping_fans', array('uniacid' => $uniacid, 'uid' => $uid));

        if (!$fans) {

            return $this->result(400, '请允许获取用户信息！');

        }

        $car = pdo_get('monai_market_car_detail', array('id' => $carid, 'uid' => $uid));

        if (!$car) {

            return $this->result(500, '获取当前汽车信息失败', '');

        }

        if ($car['status'] != 3) {

            return $this->result(500, '需要汽车上架以后才可以置顶操作', '');

        }

        $infosql = @pdo_fetch('SELECT top_money,top_cycle FROM' . tablename('monai_market_info') . ' WHERE uniacid=' . $uniacid);

        $fee = $infosql['top_money'] ? $infosql['top_money'] : 0;

        //创建订单

        $inFinancedb = [

            'uid' => $uid,

            'pay_type' => 0,

            'pay_for' => 1,

            'status' => 0,
            'pay_money' => $fee,
            'uniacid' => $uniacid,

            'pay_by_id' => $car['id'],

            'pay_by_table' => 'monai_market_car_detail',

            'order_money' => $fee,

            'create_time' => time()

        ];

        if ($fee <= 0) {

            $inFinancedb['status'] = 1;

            $inFinancedb['pay_time'] = time();

            $inFinancedb['pay_money'] = 0;

        }

        pdo_begin();

        $inFinance = pdo_insert('monai_market_finance', $inFinancedb);

        if (!$inFinance) {

            pdo_rollback();

            return $this->result(505, '创建订单失败，请稍候重试！');

        }

        $Financeid = pdo_insertid();

        //判断当前订单金额0元直接返回

        if ($fee <= 0) {

            $pay_params['pay'] = 2;

            $pay_params['money'] = $fee;

            $pay_params['orderid'] = $Financeid;

            pdo_commit();

            return $this->result(0, '1000', $pay_params);

        }

        //返回支付参数

        $order = array(

            'tid' => $Financeid,

            'user' => $fans['openid'], //用户OPENID

            'fee' => floatval($fee), //金额

            'title' => '汽车置顶！',

        );

        pdo_commit();

        $pay_params = $this->pay($order);

        if (is_error($pay_params)) {

            return $this->result(1, '支付失败，请重试', $pay_params);

        }

        $pay_params['money'] = $fee;

        $pay_params['orderid'] = $Financeid;

        return $this->result(0, '', $pay_params);

    }

    /**
     * 认证支付
     * @param $log
     */
    public function doPagePayvip()
    {
        global $_GPC, $_W;

        $fans = pdo_get('mc_mapping_fans', array('uniacid' => $_W['uniacid'], 'uid' => $_GPC['uid']));
        if (!$fans) {
            return $this->result(400, '请允许获取用户信息！');
        }
        $detail = pdo_get('monai_market_enter', ['uniacid' => $_W['uniacid']]);
        if (!$detail) {
            return $this->result(2, '没有此支付套餐', $detail);
        }
        if ($detail['price'] <= 0) {
            pdo_update('monai_market_member', array('is_vip' => 1, 'end_time' => ($_SERVER['REQUEST_TIME'] + ($detail['cycle'] * 86400))), array('uid' => $_GPC['uid']));
            return $this->result(0, '支付成功', '1');
        }
        $data = [
            'uid' => $_GPC['uid'],
            'pay_type' => 0,
            'pay_for' => 2,
            /*奖励积分插入*/
            'integral'=>$detail['integral'],
            'status' => 0,
            'uniacid' => $_W['uniacid'],
            'pay_by_id' => $detail['id'],
            'pay_by_table' => 'monai_market_enter',
            'order_money' => $detail['price'],
            'create_time' => time()
        ];
        $result = pdo_insert('monai_market_finance', $data);
        if (empty($result)) {
            return $this->result(2, '支付失败', $detail);
        }
        $Financeid = pdo_insertid();
        //返回支付参数
        $order = array(
            'tid' => $Financeid,
            'user' => $fans['openid'], //用户OPENID
            'fee' => floatval($detail['price']), //金额
            'title' => '认证店铺',
        );
        $pay_params = $this->pay($order);
        if (is_error($pay_params)) {
            return $this->result(1, '支付失败，请重试', $pay_params);
        }

        return $this->result(0, '', $pay_params);
    }

    //支付回调

    public function payResult($log)
    {
        // 付款失败处理
        if (!$log || $log['result'] != 'success' || $log['type'] != "wxapp") {
            pdo_update('monai_market_finance', array('status' => 2), array('id' => $log['tid']));
            return;
        }

        // 支付信息
        $detail = pdo_get('monai_market_finance', array('id' => $log['tid']));

        // 入驻处理
        if ($detail['pay_for'] == 2) {
            $enter = pdo_get('monai_market_enter', ['id' => $detail['pay_by_id']]);
            //pdo_update('monai_market_member', array('is_vip'=>1,'end_time'=> ($_SERVER['REQUEST_TIME']+ ($enter['cycle']*86400))), array('uid'=>$detail['uid']));
        }

        /*获取用户设置的入驻规则*/
        $current_enter = pdo_get("monai_market_enter",['id'=>$detail['pay_by_id']]);

        //更新订单状态
        pdo_update('monai_market_finance', array('pay_time' => time(), 'pay_money' => isset($log['fee']) ? $log['fee'] : '0', 'status' => 1), array('id' => $log['tid']));
        // 分佣处理
        $userDetail = pdo_get('monai_market_member', ['uid' => $detail['uid'], 'uniacid' => $detail['uniacid']]);
        $sale = pdo_get('monai_market_saleinfo', ['uniacid' => $detail['uniacid']]);
        if ($userDetail['parent_uid'] && $sale && $sale['status'] == 1 && $sale['scale'] > 0) {
            $data = [
                'uid' => $detail['uid'],
                'uniacid' => $detail['uniacid'],
                'parent_uid' => $userDetail['parent_uid'],
                'content' => $detail['pay_for'] == 2 ? "入驻" : "发布",
                'account' => $log['fee'],
                'brokerage' => $log['fee'] * ($sale['scale'] / 100),
                'create_time' => $_SERVER['REQUEST_TIME']
            ];
            pdo_insert('monai_market_account', $data);
            $parentDetail = pdo_get('monai_market_member', ['uid' => $userDetail['parent_uid'], 'uniacid' => $detail['uniacid']]);
            pdo_update('monai_market_member', ['balance' => $parentDetail['balance'] + $data['brokerage']]);
        }

        /*更新积分*/
        pdo_update("ims_monai_market_member",
            ['weizhang_num'=>intval($userDetail['weizhang_num'])+intval($current_enter['integral'])],['uid'=>$detail['uid']]);

    }

    //购买违章查询次数
    public function doPagepaynum()
    {
        global $_GPC, $_W;
        //获取用户基本信息
        $uid = $_GPC['uid'];
        $uniacid = $_W['uniacid'];
        //获取用户信息，防止直接跳转当前页面，没有member表信息，直接查询fans表
        $fans = pdo_get('mc_mapping_fans', array('uid' => $uid));
        if (!$fans) {
            return $this->result(400, '请允许获取用户信息！');
        }
        $infosql = pdo_fetch('SELECT weizhang_money FROM' . tablename('monai_market_info') . ' WHERE uniacid=' . $uniacid);

        $fee = $infosql['weizhang_money'] ? $infosql['weizhang_money'] : 0;

        //创建订单
        $inFinancedb = [
            'uid' => $uid,
            'pay_type' => 0,
            'pay_for' => 3,
            'status' => 0,
            'uniacid' => $uniacid,
            'pay_by_id' => 0,
            'pay_by_table' => 'monai_market_withdraw',
            'order_money' => $fee,
            'create_time' => time()
        ];
        if ($fee <= 0) {
            $inFinancedb['status'] = 1;
            $inFinancedb['pay_time'] = time();
            $inFinancedb['pay_money'] = 0;
        }

        pdo_begin();
        $inFinance = pdo_insert('monai_market_finance', $inFinancedb);
        if (!$inFinance) {
            pdo_rollback();
            return $this->result(505, '创建订单失败，请稍候重试！');
        }
        $Financeid = pdo_insertid();
        //判断当前订单金额0元直接返回
        if ($fee <= 0) {
            $pay_params['pay'] = 2;
            $pay_params['money'] = $fee;
            $pay_params['orderid'] = $Financeid;
            pdo_commit();
            return $this->result(0, '1000', $pay_params);
        }
        //返回支付参数
        $order = array(
            'tid' => $Financeid,
            'user' => $fans['openid'], //用户OPENID
            'fee' => floatval($fee), //金额
            'title' => '购买违章查询次数',
        );
        pdo_commit();
        $pay_params = $this->pay($order);
        if (is_error($pay_params)) {
            return $this->result(1, '支付失败，请重试');
        }
        $pay_params['money'] = $fee;
        $pay_params['orderid'] = $Financeid;
        return $this->result(0, '', $pay_params);
    }

    /**
     * 汽配订单支付
     */
    public function doPagepayPartOrder()
    {
        global $_GPC, $_W;

        $id = $_GPC['id'];
        $uid = $_GPC['uid'];
        $uniacid = $_W['uniacid'];

        //获取用户信息，防止直接跳转当前页面，没有member表信息，直接查询fans表
        $info = pdo_get('mc_mapping_fans', array('uniacid' => $uniacid, 'uid' => $uid));
        if (empty($info)) {
            return $this->result(400, '请允许获取用户信息！');
        }

        // 获取订单
        $order = pdo_get('monai_market_part_order', array('id' => $id, 'uniacid' => $uniacid, 'uid' => $uid, 'status' => 2));
        if (empty($order)) {
            return $this->result(500, '汽配订单不存在！');
        }

        // 获取地址
        $address = pdo_get('monai_market_member_address', array('uniacid' => $uniacid, 'uid' => $uid));
        if (empty($address)) {
            return $this->result(600, '请填写配送地址！');
        }

        pdo_begin();

        // 更新汽配订购订单
        $data = array(
            'receive_name' => $address['name'],
            'receive_tel' => $address['tel'],
            'receive_address' => $address['address'],
            'formid' => $_GPC['formid']
        );
        if ($order['part_price'] <= 0) {
            $data['status'] = 3;
            $data['save_time'] = time();
        }
        $res1 = pdo_update('monai_market_part_order', $data, array('id' => $order['id']));

        //创建支付订单
        $data = array(
            'uniacid' => $uniacid,
            'uid' => $uid,
            'pay_type' => 0,
            'pay_for' => 4,
            'status' => 0,
            'pay_by_id' => $order['id'],
            'pay_by_table' => 'monai_market_part_order',
            'order_money' => $order['part_price'],
            'create_time' => time()
        );
        if ($order['part_price'] <= 0) {
            $data['status'] = 1;
            $data['pay_time'] = time();
            $data['pay_money'] = 0;
        }
        $res2 = pdo_insert('monai_market_finance', $data);
        $Financeid = pdo_insertid();

        if (!($res1 && $res2)) {
            pdo_rollback();
            return $this->result(505, '创建订单失败，请稍候重试！');
        }

        pdo_commit();

        //判断当前订单金额0元直接返回
        if ($order['part_price'] <= 0) {
            return $this->result(0, 'OK');
        }

        //返回支付参数
        $order = array(
            'tid' => $Financeid,
            'user' => $info['openid'], //用户OPENID
            'fee' => floatval($order['part_price']), //金额
            'title' => '汽配订购支付',
        );
        $pay_params = $this->pay($order);
        if (is_error($pay_params)) {
            return $this->result(1, '支付失败，请重试');
        }

        $pay_params['fid'] = $Financeid;

        return $this->result(0, '', $pay_params);
    }
}
