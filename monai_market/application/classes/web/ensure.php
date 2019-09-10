<?php

if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Web_Ensure extends Web_Base
{
    public function index()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        $pindex = max(1, intval($_GPC['page']));
        $psize = 8;
        $list=pdo_fetchall("select * from ".tablename('monai_market_ensure')." where `uniacid`='$uniacid' order by id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_ensure')." where `uniacid`='$uniacid' order by id desc");
        //var_dump($list);exit;
        $pager = pagination2($total, $pindex, $psize);
        $i = ($pindex - 1) * $psize+1;
        include $this->template();
    }
    public function index_add() 
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        load()->func('tpl');
        
        if($_W['ispost']){
            $data=array();
            $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_ensure')." where `uniacid`='$uniacid' order by id desc");
            if ($total>=4) {
                $this->success('最多添加四条数据哦','ensure/index');
            }
            $data['name']=$_GPC['name'];
            $data['image']=$_GPC['image'];
            $data['sort']=$_GPC['sort'];
            $data['uniacid']=$uniacid;
            $res=pdo_insert('monai_market_ensure',$data);  
            if($_GPC['jixu'])
            {
                //继续添加
                $this->success('添加成功','',2);
            }
            else
            {
                $this->success('添加成功','ensure/index');
            }
        }
        include $this->template();
    }
    /*
    * 修改分类
     */
    public function index_edit()
    {
        global $_W,$_GPC;
        load()->func('tpl');
        $id=$_GPC['id'];
        $uniacid=$_W['uniacid'];
        $data=pdo_get('monai_market_ensure',array('id'=>$id,'uniacid'=>$uniacid));
        if($_W['ispost']){
            $data=array();
            $data['name']=$_GPC['name'];
            $data['image']=$_GPC['image'];
            $data['sort']=$_GPC['sort'];
            $res=pdo_update('monai_market_ensure',$data,array('id'=>$id));
            $this->success('修改成功','ensure/index');
        }
        include $this->template();
    }
    /*
    * 删除分类
     */
    public function index_del()
    {
        global $_W,$_GPC;
        $uniacid=$_W['uniacid'];
        if($_GPC['id']){
            $id=$_GPC['id'];
            $data=pdo_delete('monai_market_ensure',array('id'=>$id,'uniacid'=>$uniacid));
            if ($data) {
                echo  1;
            }else
            {
                echo 2;
            }
        }
    }
}
?>