<?php

if (!(defined('IN_IA'))) 

{

	exit('Access Denied');

}

class Web_Productclass extends Web_Base

{

    public function class_list() 

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        $pindex = max(1, intval($_GPC['page']));

        $psize = 8;

        $list=pdo_fetchall("select * from ".tablename('monai_market_class')." where `uniacid`='$uniacid' order by sort desc LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        

        $total = pdo_fetchcolumn("select count(*) from ".tablename('monai_market_class')." where `uniacid`='$uniacid' order by sort desc");

        $pager = pagination2($total, $pindex, $psize);

        $i = ($pindex - 1) * $psize+1;

        include $this->template();

    }

    public function class_add() 

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        //echo $uniacid;exit;

        load()->func('tpl');

        if($_W['ispost']){

            $data=array();

            $data['name']=$_GPC['name'];

            $data['brand_icon']=$_GPC['brand_icon'];

            $data['sort']=$_GPC['sort'];

            $data['uniacid']=$uniacid;

            $res=pdo_insert('monai_market_class',$data);  

            if($_GPC['jixu'])

            {

                //继续添加

                $this->success('添加成功','',2);

            }

            else

            {

                $this->success('添加成功','productclass/class_list');

            }

        }

        include $this->template();

    }

    /*

    * 修改分类

     */

    public function class_edit()

    {

        global $_W,$_GPC;

        load()->func('tpl');

        $id=$_GPC['id'];

        $uniacid=$_W['uniacid'];

        $data=pdo_get('monai_market_class',array('id'=>$id,'uniacid'=>$uniacid));

        

        if($_W['ispost']){

            $data=array();

            $data['name']=$_GPC['name'];

            $data['brand_icon']=$_GPC['brand_icon'];

            $data['sort']=$_GPC['sort'];

            $res=pdo_update('monai_market_class',$data,array('id'=>$id));

            $this->success('修改成功','productclass/class_list');

        }

        

        include $this->template();

    }

    /*

    * 删除分类

     */

    public function class_del()

    {

        global $_W,$_GPC;

        $uniacid=$_W['uniacid'];

        if($_GPC['id']){

            $id=$_GPC['id'];

            $data=pdo_delete('monai_market_class',array('id'=>$id,'uniacid'=>$uniacid));

            if ($data) {
                echo  1;
            }else
            {
                echo 2;
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

     /*

    * 获取首字母

     */

    public function getFirstCharter($str)

    {

        if (empty($str)) {

            return '';

        }

        $fchar = ord($str{0});

        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});

        $s1 = iconv('UTF-8', 'gb2312', $str);

        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;

        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;

        if ($asc >= -20319 && $asc <= -20284) return 'A';

        if ($asc >= -20283 && $asc <= -19776) return 'B';

        if ($asc >= -19775 && $asc <= -19219) return 'C';

        if ($asc >= -19218 && $asc <= -18711) return 'D';

        if ($asc >= -18710 && $asc <= -18527) return 'E';

        if ($asc >= -18526 && $asc <= -18240) return 'F';

        if ($asc >= -18239 && $asc <= -17923) return 'G';

        if ($asc >= -17922 && $asc <= -17418) return 'H';

        if ($asc >= -17417 && $asc <= -16475) return 'J';

        if ($asc >= -16474 && $asc <= -16213) return 'K';

        if ($asc >= -16212 && $asc <= -15641) return 'L';

        if ($asc >= -15640 && $asc <= -15166) return 'M';

        if ($asc >= -15165 && $asc <= -14923) return 'N';

        if ($asc >= -14922 && $asc <= -14915) return 'O';

        if ($asc >= -14914 && $asc <= -14631) return 'P';

        if ($asc >= -14630 && $asc <= -14150) return 'Q';

        if ($asc >= -14149 && $asc <= -14091) return 'R';

        if ($asc >= -14090 && $asc <= -13319) return 'S';

        if ($asc >= -13318 && $asc <= -12839) return 'T';

        if ($asc >= -12838 && $asc <= -12557) return 'W';

        if ($asc >= -12556 && $asc <= -11848) return 'X';

        if ($asc >= -11847 && $asc <= -11056) return 'Y';

        if ($asc >= -11055 && $asc <= -10247) return 'Z';

        return null;

    }

}

?>