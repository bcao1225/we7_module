<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Notice_Index extends Web_Base

{



    /*

    * 公告列表

     */

	public function notice() 

	{

       global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $title=$_GPC['title'];

        $where='';

        if($title!='')

        {

            $where = "and title like'%".$title."%'" ;

        }        

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $list=pdo_fetchall("select * from ".tablename('monai_market_notice')." where `uniacid`='$uniacid' $where order by id desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_notice')." where `uniacid`='$uniacid' $where order by id desc");

        $pager = pagination2($total, $pindex, $psize);

        $i = ($pindex - 1) * $psize+1;

        include $this->template();

	}

    /*

    * 添加公告

     */

    public function notice_add() 

	{

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        load()->func('tpl');

        if($_W['ispost']){

            $data=array();

            $data['content']=$_GPC['content'];

            $data['desc']=$_GPC['desc'];

            $data['uniacid']=$uniacid;

            $data['create_time']=time();

            $res=pdo_insert('monai_market_notice',$data);            

            if($_GPC['jixu'])

            {

                //继续添加

                $this->success('添加成功','',2);

            }

            else

            {

                $this->success('添加成功','notice/index/notice');

            }

        }

        include $this->template();

	}

    /*

    * 修改公告

     */

    public function notice_edit()

    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=pdo_get('monai_market_notice',array('id'=>$id,'uniacid'=>$uniacid));

        

        if($_W['ispost']){

            $data=array();

            $data['content']=$_GPC['content'];

            $data['desc']=$_GPC['desc'];

            $res=pdo_update('monai_market_notice',$data,array('id'=>$id));

            $this->success('修改成功','notice/index/notice');

        }

        $product_type=pdo_fetchall("select * from ".tablename('monai_market_notice')." where `uniacid`='$uniacid'");

        include $this->template();

    }

    /*

    * 删除公告

     */

    public function notice_del()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $id=$_GPC['id'];

            $data=pdo_delete('monai_market_notice',array('id'=>$id,'uniacid'=>$uniacid));

            //message('删除成功',$this->createWebUrl('news'));

            //$this->success('删除成功','notice/index/notice');
            if ($data) {
               echo "1";
            }
        }

    }

    /*

     * 去除富文本内的字体

     * 2018年1月19日13:22:00

     */

    public function fwb_del_font($str)

    {

        $str = htmlspecialchars_decode($str);

        $re = preg_replace("/font-family:.*;/i",'',$str);

        $re = preg_replace("/font-family:.*\"/i",'"',$re);

        return $re;

    }

}

?>