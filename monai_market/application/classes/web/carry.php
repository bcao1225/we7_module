<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Carry extends Web_Base

{
    /*

    * 产品

     */

    public function index11() 
	{
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $name=$_GPC['name'];
        $status = $_GPC['status'];
        $where='';
        if($name!='')
        {
            $where = " and a.name like'%".$name."%'";
        }
        if ($status=='1') {
            $where = " and a.uid =0";
        }
        if ($status=='2') {
            $where = " and a.uid >0";
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 12;
        $list=pdo_fetchall("select a.*,b.goods_img from ".tablename('monai_seckill_cut_order')."  as a Left Join" .tablename('monai_seckill_goods')." as b On a.goods_id=b.goods_id where a.`uniacid`='$uniacid' order by a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_seckill_cut_order')."  as a Left Join" .tablename('monai_seckill_goods')." as b On a.goods_id=b.goods_id where a.`uniacid`='$uniacid' order by a.id desc");
        $pager = pagination2($total, $pindex, $psize);
        $i=($pindex - 1) * $psize+1;
        include $this->template();

	}
    public function order_audit()
    {
        global $_W,$_GPC;
        load()->func('tpl');
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data=pdo_get('monai_seckill_cut_order',array('id'=>$id,'uniacid'=>$uniacid));
        include $this->template();

    }
    public function index()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $list=pdo_fetchall("select a.*,b.nickname from ".tablename('monai_market_withdraw')."  as a Left Join" .tablename('mc_mapping_fans')." as b On a.uid=b.uid where a.`uniacid`='$uniacid' order by a.status asc,a.id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_withdraw')."  as a Left Join" .tablename('mc_mapping_fans')." as b On a.uid=b.uid where a.`uniacid`='$uniacid' order by a.status asc,a.id desc");
        //var_dump($list);exit;
        $pager = pagination2($total, $pindex, $psize);
        $i = ($pindex - 1) * $psize+1;
        include $this->template();
    }
    public function index_list()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $id=$_GPC['id'];
        $data=pdo_get('monai_market_withdraw',array('id'=>$id,'uniacid'=>$uniacid));
        $member=pdo_get('mc_mapping_fans',array('uid'=>$data['uid'],'uniacid'=>$uniacid));
        if ($_W['ispost']) {
            //$data=array();
            if ($_GPC['status']==1) {
                pdo()->begin();
                $member_tx=pdo_get('monai_market_member',array('uid'=>$data['uid'],'uniacid'=>$uniacid));

                $data1['status']=$_GPC['status'];
                $data1['pay_time']=time();
                $res1=pdo_update('monai_market_withdraw',$data1,array('id'=>$id,'uniacid'=>$uniacid));

                $data2['account']=$member_tx['account']-$data['account'];
                $res2=pdo_update('monai_market_member',$data2,array('uid'=>$data['uid'],'uniacid'=>$uniacid));
                if (!$res1 || !$res2) {
                    pdo()->rollback();
                    $this->error('修改失败','carry/index');
                }
                //发放
                $openid = $member['openid'];
                //金额
                $actual_money = $data['account'];
                //微信支付
                $setting = uni_setting_load('payment', $_W['uniacid']);
                $refund_setting = $setting['payment']['wechat_refund'];
                
                $cert = authcode($refund_setting['cert'], 'DECODE');
                $key = authcode($refund_setting['key'], 'DECODE');
                
                file_put_contents(ATTACHMENT_ROOT . $_W['uniacid'] . 'apiclient_cert.pem', $cert);
                file_put_contents(ATTACHMENT_ROOT . $_W['uniacid'] . 'apiclient_key.pem', $key);
                
                $paykey = $setting['payment']['wechat']['signkey'];
                
                $params = array(
                    'openid' =>$openid,
                    'payAmount'=>$actual_money,
                    'outTradeNo'=>date('ymdHis',time()).'A'.$_GPC['id'].'z',
                    'apiclient_cert'=>ATTACHMENT_ROOT . $_W['uniacid'] . 'apiclient_cert.pem',
                    'apiclient_key'=>ATTACHMENT_ROOT . $_W['uniacid'] . 'apiclient_key.pem',
                    'money_desc'=>'导购奖励金',
                );
                
                $wx_pay = new wxpay();
                $res =  $wx_pay->wx_date($setting['payment']['wechat']['mchid'], $_W['account']['key'], $setting['payment']['wechat']['signkey'], $params);
                if(!$res['code'])
                {
                    pdo()->rollback();
                    $this->error('企业付款：'.$res['msg'],'',0);
                }
                pdo()->commit();
                $this->success('提现成功','carry/index');
            }else
            {
                $member_tx=pdo_get('monai_market_member',array('uid'=>$data['uid'],'uniacid'=>$uniacid));

                $data1['status']=$_GPC['status'];
                $data1['pay_time']=time();
                $res1=pdo_update('monai_market_withdraw',$data1,array('id'=>$id,'uniacid'=>$uniacid));

                $data2['account']=$member_tx['account']-$data['account'];
                $data2['balance']=$member_tx['balance']+$data['account'];
                $res2=pdo_update('monai_market_member',$data2,array('uid'=>$data['uid'],'uniacid'=>$uniacid));
                $this->success('拒绝成功','carry/index');
            }
        }
        include $this->template();
    }
}
?>