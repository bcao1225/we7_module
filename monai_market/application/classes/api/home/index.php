<?php

class Api_Home_Index extends WeModuleWxapp
{
	public function home()
	{
		global $_GPC, $_W;
		//轮播图

		$banner_sql = "select * from " . tablename('monai_market_image') . " where uniacid={$_W['uniacid']}  and `type`=1 order by  `sort` desc";

		$banner = pdo_fetchall($banner_sql);
		foreach ($banner as &$item) {

			$item['img_patch'] = tomedia($item['img_patch']);

		}
		//公告

		$notice_sql = "select * from " . tablename('monai_market_notice') . " where uniacid={$_W['uniacid']}  order by create_time desc";

		$notice = pdo_fetchall($notice_sql);


		//公司信息

		$market_info_sql = "select `name`,`recom`,`plate_type` from " . tablename('monai_market_info') . " where uniacid={$_W['uniacid']}";

		$info_name = pdo_fetch($market_info_sql);

		$res = [
			'banner' => $banner,

			'notice' => $notice,

			'market' => $this->market_class(),  //汽车分类

			'brand' => $this->market_brand(),

			'info_name' => $info_name

		];

		return $this->result('', '', $res);

	}

	//汽车分类
	public function market_class()
	{

		global $_GPC, $_W;

		$class_sql = "select * from " . tablename('monai_market_class') . " where uniacid={$_W['uniacid']}  order by  `sort` desc";

		$market = pdo_fetchall($class_sql);

		return $market;

	}

	//汽车品牌

	public function market_brand()
	{

		global $_GPC, $_W;

		$brand_sql = "select * from " . tablename('monai_market_brand') . " where uniacid={$_W['uniacid']}  order by  `sort` desc";

		$brand = pdo_fetchall($brand_sql);


		foreach ($brand as &$items) {

			$items['brand_icon'] = tomedia($items['brand_icon']);

		}

		return $brand;

	}

	public function carListVip(){
        global $_GPC, $_W;

        $uid = $_GPC['uid'];
        $loadtype = $_GPC['loadtype'];

        $userinfo = pdo_get('monai_market_member',array('uid'=>$uid,'uniacid'=>$_W['uniacid']));
        if(empty($userinfo)||$userinfo['is_vip']!=1 || $userinfo['end_time']<time()){
            $result = array(
                'cardlist' => array(),
                'isvip' => 0
            );
            //var_dump($car_detail);die;
            return $this->result(0, '', $result);
        }

        $pageId = $_GPC['leftid'] ? $_GPC['leftid'] : 1;

        $cur_page = ($pageId - 1) * 6;

        $lat = $_GPC['latitude'];
        $lng = $_GPC['longitude'];


        $limit = " limit " . $cur_page . "," . 6;

        $whereStr = "";

        $whereStr .= isset($_GPC['fenlei_id']) && $_GPC['fenlei_id'] > 0 ? " and d.`class`={$_GPC['fenlei_id']} " : "";//分类id

        $whereStr .= isset($_GPC['pinpai_id']) && $_GPC['pinpai_id'] > 0 ? " and d.brand = '{$_GPC['pinpai_id']}' " : "";//品牌id

        $whereStr .= $_GPC['province'] ? " and d.province = '{$_GPC['province']}' " : ""; // 汽车所在省份

        $whereStr .= $_GPC['city'] ? " and d.city = '{$_GPC['city']}' " : ""; // 汽车所在省份

        $whereStr .= $_GPC['district'] ? " and d.district = '{$_GPC['district']}' " : ""; // 汽车所在省份

        $nianxian_id = isset($_GPC['nianxian_id']) && $_GPC['nianxian_id'] > 0 ? $_GPC['nianxian_id'] : 0;//年限index

        $km_id = isset($_GPC['km_id']) && $_GPC['km_id'] > 0 ? $_GPC['km_id'] : 0; //公里数index，


        if ($nianxian_id == 1) {//年限

            $whereStr .= " and d.agelimit=0";

        } elseif ($nianxian_id == 2) {

            $whereStr .= " and d.agelimit between 1 and 3 ";

        } elseif ($nianxian_id == 3) {

            $whereStr .= " and d.agelimit between 3 and 5 ";

        } elseif ($nianxian_id == 4) {

            $whereStr .= " and d.agelimit between 5 and 9 ";

        } elseif ($nianxian_id == 5) {

            $whereStr .= " and d.agelimit  > 9 ";

        }

        if ($km_id == 1) {//公里数

            $whereStr .= " and d.price <= 3";

        } elseif ($km_id == 2) {

            $whereStr .= " and d.price  between 3 and  5";

        } elseif ($km_id == 3) {

            $whereStr .= " and d.price  between 5 and  10";

        } elseif ($km_id == 4) {

            $whereStr .= " and d.price  between 10 and  15";

        } elseif ($km_id == 5) {

            $whereStr .= " and d.price  between 15 and 20";

        } elseif ($km_id == 6) {

            $whereStr .= " and d.price  between 20 and 30";

        } elseif ($km_id == 7) {

            $whereStr .= " and d.price  between 30 and 50";

        } elseif ($km_id >= 8) {

            $whereStr .= " and d.price  >= 50";

        }
        if($loadtype=='fine'){
            $whereStr .= " AND d.isfine=1";
        }elseif ($loadtype=='vip'){
            $whereStr .= " AND d.isvip=1";
        }

        $time = time();
        $distance = "";

        $sort_id = $_GPC['sort_id']; //排序id

        if ($sort_id == 1) {

            $whereStr .= " order by d.price asc";//价格

        } elseif ($sort_id == 2) {

            $whereStr .= " order by d.price desc";//价格

        } elseif ($sort_id == 3) {

            $whereStr .= " order by d.`km` asc";

        } elseif ($sort_id == 4) {

            $whereStr .= " order by d.`agelimit` asc";


        } else if ($sort_id == 5) {
            $whereStr .= " order by `distance` asc";
        } else {

            $whereStr .= " order by d.expiry_time desc ,d.`create_time`  desc";

        }



        $car_detail_sql = "select  d.*,u.head_image,u.nickname as usersname,u.is_vip,u.end_time," .
            "ROUND(6378.138*2*ASIN(SQRT(POW(SIN(({$lat}*PI()/180-`x`*PI()/180)/2),2)+COS({$lat}*PI()/180)*COS({$lat}*PI()/180)*POW(SIN(({$lng}*PI()/180-`y`*PI()/180)/2),2)))*1000) " .
            "AS `distance` from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_member') .
            " as u on  u.uid=d.uid where d.uniacid={$_W['uniacid']} and u.uniacid={$_W['uniacid']} and u.`status`=0 and d.`status`=3 and  d.delete_time=0 {$whereStr} {$limit}";

        $car_detail = pdo_fetchall($car_detail_sql);


        foreach ($car_detail as &$item) {

            $item['top_status'] = $item['expiry_time'] > time() ? true : false;//置顶 显示状态

            $item['timestr'] = $this->timeStr($item['create_time']);
            $item['is_vip'] = ($item['is_vip'] == 1 && $item['end_time'] > $_SERVER['REQUEST_TIME']) ? 1 : 2;
            $item['distance'] = round($item['distance'] / 1000, 2);

            $sql = "select img_patch from " . tablename('monai_market_image') . "  where uniacid={$_W['uniacid']} and product_id={$item['id']} and `type` > 1";

            $img_patch = pdo_fetchall($sql);


            $item['carimg'] = tomedia($item['carimg']);

            foreach ($img_patch as &$items) {

                $items['img_patch'] = tomedia($items['img_patch']);

            }

            $item['image'] = $img_patch;

        }
        unset($item);

        $result = array(
            'cardlist' => $car_detail,
            'isvip' => 1
        );
        //var_dump($car_detail);die;
        return $this->result('', '', $result);
    }

	public function carList()
	{

		global $_GPC, $_W;

		$pageId = $_GPC['leftid'] ? $_GPC['leftid'] : 1;

		$cur_page = ($pageId - 1) * 6;

        $lat = $_GPC['latitude'];
        $lng = $_GPC['longitude'];


		$limit = " limit " . $cur_page . "," . 6;

		$whereStr = "";

		$whereStr .= isset($_GPC['fenlei_id']) && $_GPC['fenlei_id'] > 0 ? " and d.`class`={$_GPC['fenlei_id']} " : "";//分类id

		$whereStr .= isset($_GPC['pinpai_id']) && $_GPC['pinpai_id'] > 0 ? " and d.brand = '{$_GPC['pinpai_id']}' " : "";//品牌id

        $whereStr .= $_GPC['province'] ? " and d.province = '{$_GPC['province']}' " : ""; // 汽车所在省份

        $whereStr .= $_GPC['city'] ? " and d.city = '{$_GPC['city']}' " : ""; // 汽车所在省份

        $whereStr .= $_GPC['district'] ? " and d.district = '{$_GPC['district']}' " : ""; // 汽车所在省份

		$nianxian_id = isset($_GPC['nianxian_id']) && $_GPC['nianxian_id'] > 0 ? $_GPC['nianxian_id'] : 0;//年限index

		$km_id = isset($_GPC['km_id']) && $_GPC['km_id'] > 0 ? $_GPC['km_id'] : 0; //公里数index，


		if ($nianxian_id == 1) {//年限

			$whereStr .= " and d.agelimit=0";

		} elseif ($nianxian_id == 2) {

			$whereStr .= " and d.agelimit between 1 and 3 ";

		} elseif ($nianxian_id == 3) {

			$whereStr .= " and d.agelimit between 3 and 5 ";

		} elseif ($nianxian_id == 4) {

			$whereStr .= " and d.agelimit between 5 and 9 ";

		} elseif ($nianxian_id == 5) {

			$whereStr .= " and d.agelimit  > 9 ";

		}

		if ($km_id == 1) {//公里数

			$whereStr .= " and d.price <= 3";

		} elseif ($km_id == 2) {

			$whereStr .= " and d.price  between 3 and  5";

		} elseif ($km_id == 3) {

			$whereStr .= " and d.price  between 5 and  10";

		} elseif ($km_id == 4) {

			$whereStr .= " and d.price  between 10 and  15";

		} elseif ($km_id == 5) {

			$whereStr .= " and d.price  between 15 and 20";

		} elseif ($km_id == 6) {

			$whereStr .= " and d.price  between 20 and 30";

		} elseif ($km_id == 7) {

			$whereStr .= " and d.price  between 30 and 50";

		} elseif ($km_id >= 8) {

			$whereStr .= " and d.price  >= 50";

		}


		$time = time();
        $distance = "";

		$sort_id = $_GPC['sort_id']; //排序id

		if ($sort_id == 1) {

			$whereStr .= " order by d.price asc";//价格

		} elseif ($sort_id == 2) {

			$whereStr .= " order by d.price desc";//价格

		} elseif ($sort_id == 3) {

			$whereStr .= " order by d.`km` asc";

		} elseif ($sort_id == 4) {

			$whereStr .= " order by d.`agelimit` asc";


		} else if ($sort_id == 5) {
            $whereStr .= " order by `distance` asc";
        } else {

			$whereStr .= " order by d.expiry_time desc ,d.`create_time`  desc";

		}


		$car_detail_sql = "select  d.*,u.head_image,u.nickname as usersname,u.is_vip,u.end_time," .
            "ROUND(6378.138*2*ASIN(SQRT(POW(SIN(({$lat}*PI()/180-`x`*PI()/180)/2),2)+COS({$lat}*PI()/180)*COS({$lat}*PI()/180)*POW(SIN(({$lng}*PI()/180-`y`*PI()/180)/2),2)))*1000) " .
            "AS `distance` from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_member') .
            " as u on  u.uid=d.uid where d.uniacid={$_W['uniacid']} and u.uniacid={$_W['uniacid']} and u.`status`=0 and d.`status`=3 and  d.delete_time=0 AND d.isvip=0 AND d.isfine=0 {$whereStr} {$limit}";

		$car_detail = pdo_fetchall($car_detail_sql);


		foreach ($car_detail as &$item) {

			$item['top_status'] = $item['expiry_time'] > time() ? true : false;//置顶 显示状态

			$item['timestr'] = $this->timeStr($item['create_time']);
			$item['is_vip'] = ($item['is_vip'] == 1 && $item['end_time'] > $_SERVER['REQUEST_TIME']) ? 1 : 2;
            $item['distance'] = round($item['distance'] / 1000, 2);

			$sql = "select img_patch from " . tablename('monai_market_image') . "  where uniacid={$_W['uniacid']} and product_id={$item['id']} and `type` > 1";

			$img_patch = pdo_fetchall($sql);


			$item['carimg'] = tomedia($item['carimg']);

			foreach ($img_patch as &$items) {

				$items['img_patch'] = tomedia($items['img_patch']);

			}

			$item['image'] = $img_patch;

		}
		unset($item);

    //var_dump($car_detail);die;
		return $this->result('', '', $car_detail);

	}

	private function timeStr($time)

	{

		$str = '';

		$thistime = time() - $time;


		if ($thistime < 60) {

			$str .= '刚刚';

		} elseif ($thistime < 3600) {

			$str .= floor($thistime / 60) . '分钟前';

		} elseif ($thistime < 86400) {

			$str .= floor($thistime / 3600) . '小时前';

		} elseif ($thistime < 259200) {//3天内

			$str .= floor($thistime / 86400) . '天前';

		} else {

			$str .= date('Y.m.d', $time);

		}

		return $str;

	}


//        汽车单个信息

	public function car_info()
	{

		global $_GPC, $_W;


		$id = $_GPC['id'];


		$car_detail_sql = "select  * from " . tablename('monai_market_car_detail') . " where uniacid={$_W['uniacid']}  and   id={$id} and   delete_time=0  and `status`=3 ";


		$car_detail = pdo_fetch($car_detail_sql);

		if (!$car_detail) {

			return $this->result(500, '', false);

		}

		if(empty($car_detail['cardes'])){
            $car_detail['cardes'] = '';
        }


		$up_sql = "update " . tablename('monai_market_car_detail') . " set browse=browse+1  where uniacid={$_W['uniacid']} and   delete_time=0 and id={$id}  and `status`=3 ";

		pdo_query($up_sql);

		//关注表

		$car_detail_log_sql = "select uid  from " . tablename('monai_market_follow_logs') . " where uniacid={$_W['uniacid']}  and   ucar_id={$car_detail['id']}  AND `status`=1 ";

		$follow = @pdo_fetchall($car_detail_log_sql);

		$car_detail['follow'] = $follow;


		$car_detail['timestr'] = $this->timeStr($car_detail['create_time']);

		$sql_img = "select img_patch,intro from " . tablename('monai_market_image') . "  where uniacid={$_W['uniacid']} and product_id={$id} and `type`>1 ";

		$car_detail['image'] = @pdo_fetchall($sql_img);

		foreach ($car_detail['image'] as &$item) {

			$item['img_patch'] = tomedia($item['img_patch']);

		}


//         汽车品牌

		$brand_sql = "select id,`name` from " . tablename('monai_market_brand') . " where uniacid={$_W['uniacid']} and id={$car_detail['brand']}";

		$car_detail['brand'] = @pdo_fetch($brand_sql);


//         汽车分类


		$brand_sql = "select `name` from " . tablename('monai_market_class') . " where uniacid={$_W['uniacid']} and id={$car_detail['class']}";

		$car_detail['class_name'] = @pdo_fetch($brand_sql);


		//平台

		$market_info_sql = "select `remind` from " . tablename('monai_market_info') . " where uniacid={$_W['uniacid']}";

		$car_detail['market'] = @pdo_fetch($market_info_sql);


//         汽车推荐列表

//         $car_list_sql = "select  * from " . tablename('monai_market_car_detail') . " where uniacid={$_W['uniacid']} and  brand={$car_detail['brand']['id']} and   delete_time=0 and id not in($id) AND audit_type=1 and `status`=3 limit 8";

//         $car_detail['car_list'] = pdo_fetchall($car_list_sql);

		$car_list_sql = "select  d.*,u.head_image,u.nickname as usersname from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_member') . " as u on  u.uid=d.uid   where   d.uniacid={$_W['uniacid']} and u.`status`=0 and u.uniacid={$_W['uniacid']} and d.`status`=3 and  d.delete_time=0  limit  8";

		$car_detail['car_list'] = pdo_fetchall($car_list_sql);


		foreach ($car_detail['car_list'] as &$item) {

			$brand_sql_1 = "select id,`name` from " . tablename('monai_market_brand') . " where uniacid={$_W['uniacid']} and id={$item['brand']}";

			$item['brand'] = @pdo_fetch($brand_sql_1);

//         汽车分类

			$brand_sql_1 = "select `name` from " . tablename('monai_market_class') . " where uniacid={$_W['uniacid']} and id={$item['class']}";

			$item['class_name'] = @pdo_fetch($brand_sql_1);

			$item['carimg'] = tomedia($item['carimg']);

			$item['timestr'] = $this->timeStr($item['create_time']);

		}
        $sql='SELECT * FROM '.tablename('monai_market_ensure').' WHERE uniacid='.$_W['uniacid'].' order by sort desc LIMIT 4 ';
        $ensure = pdo_fetchall($sql);
        if(!empty($ensure))
        {
            foreach ($ensure as &$vt)
            {
                @$vt['image'] = tomedia($vt['image']);
            }
        }
        else
        {
            $ensure = array(
                0=>array(
                    'image'=>'/pages/image/shouhou_icn.png',
                    'name'=>'售后保障'
                ),
                1=>array(
                    'image'=>'/pages/image/chekuang_icn.png',
                    'name'=>'车况保障'
                ),
                2=>array(
                    'image'=>'/pages/image/price_icon.png',
                    'name'=>'交易保障'
                ),
                3=>array(
                    'image'=>'/pages/image/shouchedj_btn.png',
                    'name'=>'价格公道'
                ),
            );
        }
        $car_detail['ensure'] = $ensure;

        //----------------------
        $listtemp = pdo_getall('monai_market_yclist',array('uniacid'=>$_W['uniacid'],'type'=>1));
        $grouplist = array();
        unset($item);
        foreach($listtemp as $item){
            $infotemp = iunserializer($item['con']);
            $infotemp['qrcode'] = tomedia($infotemp['qrcode']);
            array_unshift($grouplist,$infotemp);
        }
        $car_detail['group'] = $grouplist;

        $car_detail['brand'] = pdo_get("ims_monai_market_brand",['id'=>$car_detail['brand']['id']]);
        $car_detail['brand2'] = pdo_get("ims_monai_market_brand",['id'=>$car_detail['brand2']]);
        $car_detail['brand3'] = pdo_get("ims_monai_market_brand",['id'=>$car_detail['brand3']]);

        /*推荐车辆的二三级分类信息*/
        foreach ($car_detail['car_list'] as $key=>$value){
            $car_detail['car_list'][$key]['brand2'] = pdo_get("ims_monai_market_brand",['id'=>$value['brand2']]);
            $car_detail['car_list'][$key]['brand3'] = pdo_get("ims_monai_market_brand",['id'=>$value['brand3']]);
        }

		return $this->result('', '', $car_detail);
	}


	public function follow_logs()
	{

		global $_GPC, $_W;


		$uid = $_GPC['uid'];

		$id = $_GPC['ucar_id'];

		$type = $_GPC['type'];// 1 关注， 2收藏商品


		if ($type == 1) {

			$ucar_id = $uid;

		} else {

			$ucar_id = $id;

		}


		$brand_sql = "select * from " . tablename('monai_market_follow_logs') . " where uniacid={$_W['uniacid']} and ucar_id={$id} and uid={$uid} ";

		$follow = pdo_fetch($brand_sql);

		if (!empty($follow)) {//如果 已关注 删除

			$brand_sql = "delete  from " . tablename('monai_market_follow_logs') . " where uniacid={$_W['uniacid']} and ucar_id={$id} and uid={$uid}";

			$res = pdo_query($brand_sql);

			if ($res) {

				return $this->result('', '100', $follow);

			}

			return $this->result('', '150', $follow);

		} else {//如果 已关注 成功

			$data = [

				'uniacid' => $_W['uniacid'],

				'type' => $type,

				'ucar_id' => $ucar_id,

				'uid' => $uid

			];

			$follow_logs = pdo_insert('monai_market_follow_logs', $data);

			$insertid = pdo_insertid();

			return $this->result('', '200', $insertid);

		}
	}

	public function search_detail()
	{

		global $_GPC, $_W;

		$sql = "select d.`name`,d.id,b.`name` as brand_name,u.nickname as unickname  from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_brand') . " as b  on  b.id=d.brand  left join " . tablename('monai_market_member') . "  as u  on  u.uid=d.uid  WHERE d.uniacid ={$_W['uniacid']} and u.uniacid={$_W['uniacid']}  and b.uniacid={$_W['uniacid']} AND d.`status`=3 and u.`status`=0 and   d.delete_time=0  order by d.create_time desc";

		$shop = @pdo_fetchall($sql);

		return $this->result('', '', $shop);

	}


	public function search_index()
	{

		global $_GPC, $_W;

		$page = 8;

		$pageId = $_GPC['leftid'] ? $_GPC['leftid'] : 1;

		$cur_page = ($pageId - 1) * $page;

		$search_name = $_GPC['name'];


		$limit = " limit " . $cur_page . "," . $page;

		//$sql_all = "select d.*,b.`name` as brand_name ,u.is_vip,u.end_time from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_brand') .
        // " as b  on  b.id=d.brand left join " . tablename('monai_market_member') .
        // "  as u   on  u.uid=d.uid    WHERE d.uniacid ={$_W['uniacid']}  and u.uniacid={$_W['uniacid']}  and b.uniacid={$_W['uniacid']}
        // AND d.`status`=3  and u.`status`=0 and  d.delete_time=0 and ((d.`name` like '%{$search_name}%') or(u.nickname like '%$search_name%') or (b.`name` like '%$search_name%') OR (d.username LIKE '%$search_name%') or (d.km like '%$search_name%')) group by (d.`name`) order by   d.top_time  desc, d.create_time desc {$limit}";
        $sql_all = "select d.*,b.`name` as brand_name ,u.is_vip,u.end_time from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_brand') . " as b  on  b.id=d.brand left join " . tablename('monai_market_member') . "  as u   on  u.uid=d.uid    WHERE d.uniacid ={$_W['uniacid']} AND d.`status`=3  and u.`status`=0 and  d.delete_time=0 and ((d.`name` like '%{$search_name}%')  or (b.`name` like '%$search_name%')) order by   d.top_time  desc, d.create_time desc {$limit}";
		$shop = @pdo_fetchall($sql_all);


		foreach ($shop as &$item) {
			$item['is_vip'] = ($item['is_vip'] == 1 && $item['end_time'] > $_SERVER['REQUEST_TIME']) ? 1 : 2;
			$item['top_status'] = $item['expiry_time'] > time() ? true : false;//置顶 显示状态


//            用户头像

			$user_sql = "select head_image,nickname  from " . tablename('monai_market_member') . "   WHERE uniacid ={$_W['uniacid']} and uid={$item['uid']}";

			$item['head_image'] = pdo_fetch($user_sql);


			//品牌

			$sql_brand = "select `name`  from  " . tablename('monai_market_class') . "   WHERE uniacid ={$_W['uniacid']} and id={$item['brand']}";

			$brand = @pdo_fetch($sql_brand);

			$item['cars_type_name'] = $brand['name'];
			$item['carimg'] = tomedia($item['carimg']);

			$item['timestr'] = $this->timeStr($item['create_time']);

			$sql = "select img_patch from " . tablename('monai_market_image') . "  where uniacid={$_W['uniacid']} and product_id={$item['id']} and `type`>1  limit 3";

			$item['image'] = pdo_fetchall($sql);

			foreach ($item['image'] as &$items) {

				$items['img_patch'] = tomedia($items['img_patch']);

			}

		}

		return $this->result('', '', $shop);

	}


	public function word()
	{//留言提交

		global $_GPC, $_W;


		$uid = $_GPC['uid'];

		$car_uid = $_GPC['car_uid'];

		$car_id = $_GPC['car_id'];

		$content = $_GPC['content'];

		$feedback_type = $_GPC['feedback_type'];

		$data = [

			'uniacid' => $_W['uniacid'],

			'car_uid' => $car_uid,

			'uid' => $uid,

			'content' => $content,

			'create_time' => time(),

			'feedback_type' => $feedback_type,//1留言   2  举报

			'car_info' => $_GPC['car_info'],







			'car_id' => $car_id

		];


		$sql = "select nickname,head_image from " . tablename('monai_market_member') . " where  uniacid={$_W['uniacid']} and uid={$uid}";

		$member = pdo_fetch($sql);


		if (!$member['nickname']) {

			$members_sql = "select nickname,avatar from " . tablename('mc_members') . " where  uniacid={$_W['uniacid']} and uid={$uid}";

			$members = pdo_fetch($members_sql);

			$sql_1 = "update  " . tablename('monai_market_member') . " set   nickname='{$members['nickname']}' where uniacid={$_W['uniacid']} and uid={$uid}";

			pdo_query($sql_1);


		} else if (!$member['head_image']) {

			$members_sql = "select nickname,avatar from " . tablename('mc_members') . " where  uniacid={$_W['uniacid']} and uid={$uid}";

			$members = pdo_fetch($members_sql);

			$sql_1 = "update  " . tablename('monai_market_member') . " set  head_image='{$members['avatar']}'  where uniacid={$_W['uniacid']} and uid={$uid}";

			pdo_query($sql_1);

		} else {

			if (!$_GPC['uid']) {

				$members_sql = "select nickname,avatar from " . tablename('mc_members') . " where  uniacid={$_W['uniacid']} and uid={$uid}";

				$members = pdo_fetch($members_sql);

				$data1 = [

					'uniacid' => $_W['uniacid'],

					'uid' => $uid,

					'nickname' => $members['nickname'],

					'head_image' => $members['avatar'],

					'create_time' => time()

				];


				$user = pdo_insert('monai_market_member', $data1);

			}

		}

//        && $member['head_image'])

		$insert = pdo_insert('monai_market_feedback', $data);

		pdo_insertid();

		return $this->result('', '', $members);

	}


	public function wordList()
	{//留言列表

		global $_GPC, $_W;

		$car_uid = $_GPC['car_uid'];

		$pageId = $_GPC['leftid'] ? $_GPC['leftid'] : 1;

		$cur_page = ($pageId - 1) * 4;


		$limit = " limit " . $cur_page . "," . 10;


		$sql = "select k.*,u.nickname,u.head_image from " . tablename('monai_market_feedback') . " as k left join " . tablename('monai_market_member') . " as u on u.uid=k.uid where k.uniacid={$_W['uniacid']} and k.car_uid={$car_uid}   and u.uniacid={$_W['uniacid']}  and k.`feedback_type`=1  order by k.create_time desc {$limit}";

		$list = pdo_fetchall($sql);

		foreach ($list as &$item) {

			$item['time'] = $this->timeStr($item['create_time']);

		}

		return $this->result('', '', $list);

	}

	public function stay()
	{//留言列表

		global $_GPC, $_W;

		$car_id = $_GPC['car_id'];

		$pageId = $_GPC['leftid'] ? $_GPC['leftid'] : 1;

		$cur_page = ($pageId - 1) * 4;

		$limit = " limit " . $cur_page . "," . 4;

		$sql = "select k.*,u.nickname,u.head_image from " . tablename('monai_market_feedback') . " as k left join " . tablename('monai_market_member') . " as u on u.uid=k.uid where k.uniacid={$_W['uniacid']}  and u.uniacid={$_W['uniacid']} and k.car_id={$car_id} and k.`feedback_type`=1  order by k.create_time desc {$limit}";

		$list = pdo_fetchall($sql);

		foreach ($list as &$item) {

			$item['time'] = $this->timeStr($item['create_time']);

		}
		return $this->result('', '', $list);
	}

	public function is_home()
	{//首页推荐
		global $_GPC, $_W;
		$sql = "select * from " . tablename('monai_market_member') . "  where uniacid={$_W['uniacid']}   and `is_recom`=1 ";
		$list = pdo_fetchall($sql);
		return $this->result(0, '', $list);
	}

	public function home1()
	{
		global $_GPC, $_W;
		//轮播图

		$banner_sql = "select * from " . tablename('monai_market_image') . " where uniacid={$_W['uniacid']}  and `type`  in(1,4,5,6) order by  `sort` desc";

		$banner = pdo_fetchall($banner_sql);
		foreach ($banner as &$item) {

			$item['img_patch'] = tomedia($item['img_patch']);

		}
		//公告

		$notice_sql = "select * from " . tablename('monai_market_notice') . " where uniacid={$_W['uniacid']}  order by create_time desc";

		$notice = pdo_fetchall($notice_sql);


		//公司信息

		$market_info_sql = "select * from " . tablename('monai_market_info') . " where uniacid={$_W['uniacid']}";

		$info_name = pdo_fetch($market_info_sql);

		$pageId = $_GPC['leftid'] ? $_GPC['leftid'] : 1;

		$cur_page = ($pageId - 1) * 6;

				$lat = $_GPC['latitude'];
				$lng = $_GPC['longitude'];


		$limit = " limit " . $cur_page . "," . 6;

		$whereStr = "";

		$whereStr .= isset($_GPC['fenlei_id']) && $_GPC['fenlei_id'] > 0 ? " and d.`class`={$_GPC['fenlei_id']} " : "";//分类id

		$whereStr .= isset($_GPC['pinpai_id']) && $_GPC['pinpai_id'] > 0 ? " and d.brand = '{$_GPC['pinpai_id']}' " : "";//品牌id

				$whereStr .= $_GPC['province'] ? " and d.province = '{$_GPC['province']}' " : ""; // 汽车所在省份

				$whereStr .= $_GPC['city'] ? " and d.city = '{$_GPC['city']}' " : ""; // 汽车所在省份

				$whereStr .= $_GPC['district'] ? " and d.district = '{$_GPC['district']}' " : ""; // 汽车所在省份

		$nianxian_id = isset($_GPC['nianxian_id']) && $_GPC['nianxian_id'] > 0 ? $_GPC['nianxian_id'] : 0;//年限index

		$km_id = isset($_GPC['km_id']) && $_GPC['km_id'] > 0 ? $_GPC['km_id'] : 0; //公里数index，


		if ($nianxian_id == 1) {//年限

			$whereStr .= " and d.agelimit=0";

		} elseif ($nianxian_id == 2) {

			$whereStr .= " and d.agelimit between 1 and 3 ";

		} elseif ($nianxian_id == 3) {

			$whereStr .= " and d.agelimit between 3 and 5 ";

		} elseif ($nianxian_id == 4) {

			$whereStr .= " and d.agelimit between 5 and 9 ";

		} elseif ($nianxian_id == 5) {

			$whereStr .= " and d.agelimit  > 9 ";

		}

		if ($km_id == 1) {//公里数

			$whereStr .= " and d.price <= 3";

		} elseif ($km_id == 2) {

			$whereStr .= " and d.price  between 3 and  5";

		} elseif ($km_id == 3) {

			$whereStr .= " and d.price  between 5 and  10";

		} elseif ($km_id == 4) {

			$whereStr .= " and d.price  between 10 and  15";

		} elseif ($km_id == 5) {

			$whereStr .= " and d.price  between 15 and 20";

		} elseif ($km_id == 6) {

			$whereStr .= " and d.price  between 20 and 30";

		} elseif ($km_id == 7) {

			$whereStr .= " and d.price  between 30 and 50";

		} elseif ($km_id >= 8) {

			$whereStr .= " and d.price  >= 50";

		}


		$time = time();
				$distance = "";

		$sort_id = $_GPC['sort_id']; //排序id

		if ($sort_id == 1) {

			$whereStr .= " order by d.price asc";//价格

		} elseif ($sort_id == 2) {

			$whereStr .= " order by d.price desc";//价格

		} elseif ($sort_id == 3) {

			$whereStr .= " order by d.`km` asc";

		} elseif ($sort_id == 4) {

			$whereStr .= " order by d.`agelimit` asc";


		} else if ($sort_id == 5) {
						$whereStr .= " order by `distance` asc";
				} else {

			$whereStr .= " order by d.expiry_time desc ,d.`create_time`  desc";

		}
		$time = time();

		$car_detail_sql = "select  d.*,u.head_image,u.nickname as usersname,u.is_vip,u.end_time" .
						" from " . tablename('monai_market_car_detail') . " as d left join " . tablename('monai_market_member') ." as u on  u.uid=d.uid where d.uniacid={$_W['uniacid']} and u.uniacid={$_W['uniacid']} and u.`status`=0 and d.`status`=3 and  d.delete_time=0 and expiry_time>={$time}";



		$car_detail = pdo_fetchall($car_detail_sql);
				//pdo_debug();
		//var_dump($car_detail);die;

		foreach ($car_detail as &$item) {

			$item['top_status'] = $item['expiry_time'] > time() ? true : false;//置顶 显示状态

			$item['timestr'] = $this->timeStr($item['create_time']);
			$item['is_vip'] = ($item['is_vip'] == 1 && $item['end_time'] > $_SERVER['REQUEST_TIME']) ? 1 : 2;
						$item['distance'] = round($item['distance'] / 1000, 2);

			$sql = "select img_patch from " . tablename('monai_market_image') . "  where uniacid={$_W['uniacid']} and product_id={$item['id']} and `type` > 1";

			$img_patch = pdo_fetchall($sql);


			$item['carimg'] = tomedia($item['carimg']);

			foreach ($img_patch as &$items) {

				$items['img_patch'] = tomedia($items['img_patch']);

			}

			$item['image'] = $img_patch;

		}
		unset($item);

		$res = [
			'banner' => $banner,

			'notice' => $notice,

			'market' => $this->market_class(),  //汽车分类

			'brand' => $this->market_brand(),

			'info_name' => $info_name,
			'tj_car' => $car_detail

		];

		return $this->result('', '', $res);

	}

	public function info_set()
	{
		global $_GPC, $_W;
		$market_info_sql = "select * from " . tablename('monai_market_info') . " where uniacid={$_W['uniacid']}";
		$info_name = pdo_fetch($market_info_sql);
		$area_set = pdo_get('monai_market_info', array('uniacid'=>$_W['uniacid']), array('area_set'));
		$info_name['area_set'] = $area_set['area_set'];
		if(!empty($info_name['ad_one'])){
		    $info_name['ad_one'] = tomedia($info_name['ad_one']);
        }
        if(!empty($info_name['ad_two'])){
            $info_name['ad_two'] = tomedia($info_name['ad_two']);
        }
        if(!empty($info_name['ad_three'])){
            $info_name['ad_three'] = tomedia($info_name['ad_three']);
        }
        if(!empty($info_name['ad_buycar'])){
            $info_name['ad_buycar'] = tomedia($info_name['ad_buycar']);
        }
        if(!empty($info_name['ad_salecar'])){
            $info_name['ad_salecar'] = tomedia($info_name['ad_salecar']);
        }
        if(!empty($info_name['ad_detail'])){
            $info_name['ad_detail'] = tomedia($info_name['ad_detail']);
        }
        if(!empty($info_name['deposit_img'])){
            $info_name['deposit_img'] = tomedia($info_name['deposit_img']);
        }
        if(!empty($info_name['pag_vipresult'])){
            $info_name['pag_vipresult'] = tomedia($info_name['pag_vipresult']);
        }

        $img=pdo_get('monai_market_saleinfo',array('uniacid'=>$_W['uniacid']));
        $info_name['kf_qrcode'] = $img['kf_qrcode']?tomedia($img['kf_qrcode']):'';

		return $this->result('', '', $info_name);
	}

    /**
     * 根据经纬度获取省市区
     */
    public function get_area() {
        global $_GPC, $_W;

        $line = pdo_get('monai_market_info', array('uniacid'=>$_W['uniacid']), array('map_key'));

        $data = array();
        if ($line['map_key']) {
            $res = file_get_contents("http://apis.map.qq.com/ws/geocoder/v1/?location={$_GPC['lat']},{$_GPC['lng']}&key={$line['map_key']}");
            $arr = json_decode($res, true);
            $data = array(
                $arr['result']['address_component']['province'],
                $arr['result']['address_component']['city'],
                ''
            );
        }

        return $this->result(0, '', $data);
    }
}
