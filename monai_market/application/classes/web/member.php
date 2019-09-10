<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Member extends Web_Base

{

    /**
     *  保存积分
     */
    public function save_integral(){
       global $_W,$_GPC;
       pdo_update("ims_monai_market_member",['weizhang_num'=>$_GPC['weizhang_num']],['uid'=>$_GPC['uid']]);
       exit($_GPC['weizhang_num']);
    }

    /*删除指定uid*/
    public function delete(){
        global $_W,$_GPC;
        pdo_delete("ims_monai_market_member",['uid'=>$_GPC['uid']]);
        message("删除成功",webUrl('member/index'),"success");
    }

    /*
     *   用户列表
     */
    public function index()
	{
        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];
        $name=$_GPC['name'];
        $status = $_GPC['status'];
        $where='';
        if($name!='')
        {
            //$where = " and nickname like'%".$name."%'";
            $where = " and (`nickname` like'%".$name."%' or `phone` like'%".$name."%' )";
            
        }
        if ($status==1) {

            $where = " and is_vip =".$status." and end_time >".time();

        }else if ($status==2) {

            $where = " and end_time <".time();
        }
        $pindex = max(1, intval($_GPC['page']));

        $psize = 10;

        $list=pdo_fetchall("select * from ".tablename('monai_market_member')."  where `uniacid`='$uniacid' and `uid`>0 $where order by user_id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_member')."  where `uniacid`='$uniacid' and `uid`>0 $where");
        foreach ($list as $k => $v)
        {
            $list[$k]['chenum'] = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_car_detail')."  where `uniacid`='$uniacid' and delete_time='0' and uid=".$v['uid']);
            if($v['parent_uid']==0){
                $list[$k]['parent'] = 0;
            }else{
                $list[$k]['parent'] = pdo_get('ims_monai_market_member',['uid'=>$v['parent_uid']]);
            }
            
        }
        $pager = pagination2($total, $pindex, $psize);

        $i=($pindex - 1) * $psize+1;

        include $this->template();
	}
    /*
    * 会员下级信息
     */
    public function member_list()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $uid=$_GPC['uid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $list=pdo_fetchall("select * from ".tablename('monai_market_member')."  where `uniacid`='$uniacid' and `uid`>0 and `parent_uid`={$uid} LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_member')."  where `uniacid`='$uniacid' and `uid`>0 and `parent_uid`={$uid}");
        foreach ($list as $k => $v)
        {
            $list[$k]['chenum'] = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_car_detail')."  where `uniacid`='$uniacid' and uid=".$v['uid']);
        }


        $pager = pagination2($total, $pindex, $psize);
        $i=($pindex - 1) * $psize+1;
        include $this->template();
    }
    /*
    * 拉黑会员
     */
    public function member_lahei()
    {
        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=array();
        //var_dump($_GPC['status']);exit;
        $data['status']=1;

        $res=pdo_update('monai_market_member',$data,array('user_id'=>$id,'uniacid'=>$uniacid));

        if ($res) {
            echo  1;
        }else
        {
            echo 2;
        }
    }
    /*
    * 拉黑会员
     */
    public function member_huifu()
    {
        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=array();
        //var_dump($_GPC['status']);exit;
        $data['status']=0;

        $res=pdo_update('monai_market_member',$data,array('user_id'=>$id,'uniacid'=>$uniacid));

        if ($res) {
            echo  1;
        }else
        {
            echo 2;
        }
    }
    /*
    * 拉黑会员
     */
    public function member_tuijian()
    {
        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=array();
        //var_dump($_GPC['status']);exit;
        $data['is_recom']=1;

        $res=pdo_update('monai_market_member',$data,array('user_id'=>$id,'uniacid'=>$uniacid));

        if ($res) {
            echo  1;
        }else
        {
            echo 2;
        }
    }
    /*
    * 拉黑会员
     */
    public function member_quxiao()
    {
        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=array();
        //var_dump($_GPC['status']);exit;
        $data['is_recom']=0;

        $res=pdo_update('monai_market_member',$data,array('user_id'=>$id,'uniacid'=>$uniacid));

        if ($res) {
            echo  1;
        }else
        {
            echo 2;
        }
    }
    /*
    * 认证店铺
     */
    public function know()
    {
        global $_W,$_GPC;
        load()->func('tpl');
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $userinfo = pdo_get('monai_market_member',array('user_id'=>$id,'uniacid'=>$uniacid));
        if ($_W['ispost']) {
            $data=array();
            //var_dump($_GPC['status']);exit;
            $data['is_vip']=1;
            $data['end_time']=strtotime($_GPC['end_time']);
            $res=pdo_update('monai_market_member',$data,array('user_id'=>$id,'uniacid'=>$uniacid));
            $this->success('认证成功','member/know');
        }
        include $this->template();
    }

    public function nickname_save() {
        global $_W, $_GPC;

        $id = $_GPC['id'];
        $uniacid = $_W['uniacid'];

        if ($_W['ispost']) {
            $data = array(
                'nickname' => $_GPC['nickname']
            );
            $res = pdo_update('monai_market_member', $data, array('user_id'=>$id, 'uniacid'=>$uniacid));
            if ($res === false) {
                $this->success('更新失败', '', 0);
            } else {
                $this->success('更新成功', '', 0);
            }
        }

        $line = pdo_get('monai_market_member', array('user_id'=>$id, 'uniacid'=>$uniacid), array('nickname'));

        include $this->template();
    }


    /**
     *  支付凭证
     */
    public function payorder(){
        global $_W,$_GPC;
        $uniacid = $_W['uniacid'];

        $list = pdo_getall('monai_market_payorder',array('uniacid'=>$uniacid));
        foreach($list as &$item){
            $item['img'] = tomedia($item['img']);
            $item['createtime'] = date('Y-m-d H:i:s',$item['createtime']);
        }
        include $this->template();
    }
}

?>