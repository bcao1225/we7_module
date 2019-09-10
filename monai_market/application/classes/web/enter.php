<?php

if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}

class Web_Enter extends Web_Base
{
    /*
     * 入驻期限
     */
    public function enter()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];

        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $list=pdo_fetchall("select * from ".tablename('monai_market_enter')." where `uniacid`='$uniacid' order by sort desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_enter')." where `uniacid`='$uniacid' order by id desc");
        $pager = pagination($total, $pindex, $psize);
        $i=($pindex - 1) * $psize+1;
        include $this->template();
    }
    /*
    * 添加期限
     */
    public function enter_add()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        if($_W['ispost']){
            $data=array();
            $data['status']=$_GPC['status'];
            $data['cycle']=$_GPC['cycle'];
            $data['name']=$_GPC['name'];
            $data['price']=$_GPC['price'];
            $data['sort']=$_GPC['sort'];
            $data['uniacid']=$_W['uniacid'];
            $res=pdo_insert('monai_market_enter',$data);
            if ($res) {
                $this->success('添加成功','enter/enter');
            }else
            {
                $this->error('添加失败','enter/enter');
            }
        }
        include $this->template();
    }
    /*
     * 修改期限
     */
    public function enter_edit()
    {
        global $_W,$_GPC;
        load()->func('tpl');
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data=pdo_get('monai_market_enter',array('id'=>$id,'uniacid'=>$uniacid));
        
        if($_W['ispost']){
            $data=array();
            $data['status']=$_GPC['status'];
            $data['sort']=$_GPC['sort'];
            $data['name']=$_GPC['name'];
            $data['price']=$_GPC['price'];
            $data['cycle']=$_GPC['cycle'];
            $res=pdo_update('monai_market_enter',$data,array('id'=>$id));
            if ($res) {
                $this->success('修改成功','enter/enter');
            }else
            {
                $this->error('修改失败','enter/enter');
            }
        }
        include $this->template();
    }
    /*
     * 修改期限
     */
    public function enter_set()
    {
        global $_W;
        global $_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        $data=pdo_fetch("SELECT * FROM ".tablename('monai_market_enter')." where `uniacid`='$uniacid' order by id desc limit 1");
        if($_W['ispost']){
            $data1=array();
            $data1['status']=$_GPC['status'];
            $data1['cycle']=$_GPC['cycle'];
            $data1['name']=$_GPC['name'];
            $data1['price']=$_GPC['price'];
            $data1['uniacid']=$_W['uniacid'];
            if(!empty($data)){
                $res=pdo_update('monai_market_enter',$data1,array('uniacid'=>$uniacid));
            }else{
                $res=pdo_insert('monai_market_enter',$data1);
            }
            $this->success('修改成功','enter/enter_set');
        }
        include $this->template();
    }
    /*
    * 删除
     */
    public function enter_del()
    {
        global $_W,$_GPC;
        exit;
        $uniacid=$_W['uniacid'];
        if($_GPC['id']){
            $id=$_GPC['id'];
            $data=pdo_delete('monai_market_enter',array('id'=>$id,'uniacid'=>$uniacid));
            if ($data) {
                $this->success('删除成功','store/index/enter');
            }else
            {
                $this->error('删除失败','store/index/enter');
            }
        }
    }
    /*
    * 修改状态
     */
    public function enter_ajax_recom()
    {
        global $_W,$_GPC;
        load()->func('tpl');
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data=pdo_get('monai_market_enter',array('id'=>$id,'uniacid'=>$uniacid));
        $sale = $data['status'] == '1'? '0' : '1';
        $array = array('status'=>$sale);
        $res=pdo_update('monai_market_enter',$array,array('id'=>$id,'uniacid'=>$uniacid));
        echo $sale; 
        exit;
    }
}

?>