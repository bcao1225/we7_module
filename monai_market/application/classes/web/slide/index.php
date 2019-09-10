<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Slide_Index extends Web_Base

{



	public function slide()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        //$list=pdo_fetchall("select `id`,`url`,`sort` from ".tablename('miniapp_company_image')." where `admin_id`='$uniacid' order by sort desc");



        $pindex = max(1, intval($_GPC['page']));

        $psize = 16;

        $list=pdo_fetchall("select * from ".tablename('monai_market_image')." where `uniacid`='$uniacid' and (`type`=1 or `type`=4 or `type`=5 or `type`=6) order by sort desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_image')." where `uniacid`='$uniacid' and (`type`=1 or `type`=4 or `type`=5 or `type`=6) order by sort desc");

        $pager = pagination2($total, $pindex, $psize);

        $i = ($pindex - 1) * $psize+1;

        include $this->template();

    }

    /*

     * 批量处理产品

     */

    public function slide_editall()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if(empty($_GPC['xuanze']))

        {

            $this->error('未选择数据','',0);

        }

        if($_GPC['piliang'] == 1)

        {

            //批量删除

            foreach ($_GPC['xuanze'] as $key => $value) {

                $data['id']=$value;

                $data['uniacid'] = $uniacid;

                pdo_delete('monai_market_image',$data);

            }

            $this->success('删除成功','',2);

        }

    }

    /*

    * 添加幻灯片

     */

    public function slide_add()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $_W['page']['title'] = '添加轮播';

        load()->func('tpl');

        $list=pdo_fetchall("select * from ".tablename('monai_market_car_detail')." where `uniacid`='$uniacid' and `status`=3 and `delete_time`=0 order by id desc");

        if($_W['ispost']){

            //var_dump($_GPC['type']);exit;

            $data=array();

            $list = pdo_get('monai_market_car_detail',array('id'=>$_GPC['product_id'],'uniacid'=>$uniacid));

            $data['sort']=$_GPC['sort'];

            $data['img_patch']=$_GPC['img_patch'];

            $data['product_id']=$_GPC['product_id'];

            $data['car_title']=$list['name'];

            $data['uniacid']=$uniacid;

            $data['type']=$_GPC['type'];

            $data['create_time']=time();

            $res=pdo_insert('monai_market_image',$data);

            if($_GPC['jixu'])

            {

                //继续添加

                $this->success('添加成功','',2);

            }

            else

            {

                $this->success('添加成功','slide/index/slide');

            }

        }

        include $this->template();

    }

    /*

    * 修改幻灯片

     */

    public function slide_edit()

    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        //var_dump($id);exit;

        $uniacid=$_W['uniacid'];

        $_W['page']['title'] = '修改轮播';

        $data=pdo_get('monai_market_image',array('id'=>$id,'uniacid'=>$uniacid));

        $list=pdo_fetchall("select * from ".tablename('monai_market_car_detail')." where `uniacid`='$uniacid' and `status`=3 and `delete_time`=0 order by id desc");

        if($_W['ispost']){

            $data=array();

            $list = pdo_get('monai_market_car_detail',array('id'=>$_GPC['product_id'],'uniacid'=>$uniacid));

            $data['sort']=$_GPC['sort'];

            $data['img_patch']=$_GPC['img_patch'];

            $data['product_id']=$_GPC['product_id'];

            $data['car_title']=$list['name'];

            $data['type']=$_GPC['type'];

            $res=pdo_update('monai_market_image',$data,array('id'=>$id));

            //echo  "成功";exit;

           $this->success('修改成功','slide/index/slide');

        }

        include $this->template();

    }

    /*

    * 删除幻灯片

     */

    public function slide_del($id=0){

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $id=$_GPC['id'];

            $data=pdo_delete('monai_market_image',array('id'=>$id,'uniacid'=>$uniacid));
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