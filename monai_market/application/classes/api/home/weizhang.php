<?phpclass Api_Home_Weizhang extends WeModuleWxapp{    //违章首页    public function index()    {        global $_GPC, $_W;        $uniacid = $_W['uniacid'];        $uid = $_GPC['uid'];        $list = pdo_getall('monai_market_weizhang',['uniacid'=>$uniacid,'uid'=>$uid]);        //查询剩余可查询次数        if(empty($list))        {            $market = pdo_get('monai_market_info',['uniacid'=>$uniacid]);            pdo_update('monai_market_member',['weizhang_num'=>$market['weizhang_num']],['uniacid'=>$uniacid,'uid'=>$uid]);        }        $user_info = pdo_get('monai_market_member',['uniacid'=>$uniacid,'uid'=>$uid]);        $res['list'] = $list;        $res['weizhang_num'] = $user_info['weizhang_num'];        return $this->result('', '', $res);    }    //查询可用查询次数    public function weizhang_num()    {        global $_GPC, $_W;        $uniacid = $_W['uniacid'];        $uid = $_GPC['uid'];        $user_info = pdo_get('monai_market_member',['uniacid'=>$uniacid,'uid'=>$uid]);        $res['weizhang_num'] = $user_info['weizhang_num'];        return $this->result('', '', $res);    }	public function addcar()	{   //添加违章车辆		global $_GPC, $_W;        $uniacid = $_W['uniacid'];		$carid = $_GPC['carid'];		$uid = $_GPC['uid'];		if(empty($carid))		{            $car_info = 0;        }        else        {            $car_info = pdo_get('monai_market_weizhang',['uniacid'=>$uniacid,'id'=>$carid,'uid'=>$uid]);        }        $sheng = ['京','沪','川','鄂','琼','浙','闽','冀','吉','贵','新','辽','鲁','豫','苏','湘','陕','青','皖','渝','赣','津','桂'];        $res = [            'car_info'=>$car_info,            'sheng'=>$sheng,        ];		return $this->result('', '', $res);	}	public function addcar_in()    {        //添加违章车辆        global $_GPC, $_W;        $uniacid = $_W['uniacid'];        $carid = $_GPC['carid'];        $uid = $_GPC['uid'];        //查询城市缩写        $market = pdo_get('monai_market_info',['uniacid'=>$uniacid]);        $car_city = $_GPC['mendian'].substr($_GPC['car_num'] , 0 , 1);        $getCity = new juhe($market['juhe_appkey']);        $city_arr = $getCity->getCity_one($car_city);        if(empty($city_arr['result']['city_code']))        {            return $this->result(2, '暂不支持当前城市');        }                if(empty($carid) || $carid=='undefined')        {            //添加            $array = [                'sheng'=>$_GPC['mendian'],                'jia'=>$_GPC['jia'],                'car_num'=>$_GPC['car_num'],                'fa'=>$_GPC['fa'],                'uniacid'=>$uniacid,                'uid'=>$uid,                'city_val'=>$city_arr['result']['city_code'],                'time'=>time()            ];            $res = pdo_insert('monai_market_weizhang',$array);        }        else        {            //修改            $array = [                'sheng'=>$_GPC['mendian'],                'jia'=>$_GPC['jia'],                'car_num'=>$_GPC['car_num'],                'city_val'=>$city_arr['result']['city_code'],                'fa'=>$_GPC['fa'],            ];            $res = pdo_update('monai_market_weizhang',$array,['uniacid'=>$uniacid,'id'=>$carid,'uid'=>$uid]);        }        return $this->result('', '', $res);    }    //违章查询页    public function select_car()    {        global $_GPC, $_W;        $uniacid = $_W['uniacid'];        $uid = $_GPC['uid'];        $carid = $_GPC['carid'];        //查询query        $car_info = pdo_get('monai_market_weizhang',['uniacid'=>$uniacid,'id'=>$carid,'uid'=>$uid]);        $market = pdo_get('monai_market_info',['uniacid'=>$uniacid]);        $getCity = new juhe($market['juhe_appkey']);        $city_arr = $getCity->query($car_info['city_val'],$car_info['sheng'].$car_info['car_num'],$car_info['fa'],$car_info['jia']);        /*$city_arr = array(        "resultcode"=>"200",        "reason"=>  "查询成功",        "result"=>  [            "province"=>     "SD",            "city"=>     "SD_LY",            "hphm"=>     "鲁Q316T2",            "hpzl"=>     "02",            "lists"=>    [                  0=>       [                        "date"=>        "2017-08-29 18:32:17",                        "area"=>        "祊河路与府右路交汇",                        "act"=>         "违反信号灯规定",                        "code"=>        "1625",                        "fen"=>         "6",                        "wzcity"=>      "山东临沂",                        "money"=>       "200",                        "handled"=>      "0",                        "archiveno"=>    "",                  ],                  1=>   [                                "date"=>         "2017-06-17 07:42:30",                                "area"=>         "滨河西路与北京路交汇",                                "act"=>         "违反信号灯规定",                                "code"=>         "1625",                                "fen"=>         "6",                                "wzcity"=>         "山东临沂",                                "money"=>         "200",                                "handled"=>         "0",                                "archiveno"=>         "",                  ],                  2=>   [                                "date"=>         "2017-05-07 10:28:00",                                "area"=>         "兰山区",                                "act"=>         "不按规定停车",                                "code"=>         "1039",                                "fen"=>         "0",                                "wzcity"=>         "山东临沂",                                "money"=>         "100",                                "handled"=>         "0",                                "archiveno"=>         "",                  ],                  3=>   [                                "date"=>         "2017-05-03 16:00:15",                                "area"=>        "郯城北环路与205国道交汇口",                                "act"=>         "违反信号灯规定",                                "code"=>         "1625",                                "fen"=>         "6",                                "wzcity"=>         "山东临沂",                                "money"=>         "200",                                "handled"=>         "0",                                "archiveno"=>         "",                  ],                  4=>   [                                "date"=>        "2017-01-19 18:34:00",                                "area"=>         "郯城G205 与北环路交汇南口 西东方向",                                "act"=>         "不按行进方向驶入导向车道",                                "code"=>         "12080",                                "fen"=>         "2",                                "wzcity"=>        "山东临沂",                                "money"=>         "100",                                "handled"=>         "0",                                "archiveno"=>         "",                  ]            ]        ],        "error_code"=>  0        );*/        if($city_arr['resultcode']==200 || $city_arr['resultcode']==	203606)        {            //扣除用户的使用次数            @pdo_query("update ".tablename('monai_market_member')." set weizhang_num = weizhang_num-1 where uniacid=".$uniacid." and uid=".$uid);        }        $res['car'] = $car_info;        $res['weizhang'] = $city_arr;        //查询剩余可查询次数 和一查询次数        return $this->result('', '', $res);    }    //汽车信息上架操作    public function paynum()    {        global $_GPC, $_W;        $uniacid=$_W['uniacid'];        $uid = $_GPC['uid'];        //给用户的数据增加        pdo_query("update ".tablename('monai_market_member')." set weizhang_num = weizhang_num+10 where uniacid=".$uniacid." and uid=".$uid);        $order_info = pdo_get('monai_market_finance',['uniacid'=>$uniacid,'uid'=>$uid,'id'=>$_GPC['orderid']]);        //修改订单状态已支付        pdo_update('monai_market_finance',            ['status'=>1,'pay_time'=>time(),'pay_money'=>$order_info['order_money']],            ['uniacid'=>$uniacid,'uid'=>$uid,'id'=>$_GPC['orderid']]        );        return $this->result(0,'购买成功','');    }}