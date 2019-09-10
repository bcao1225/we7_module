<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

if (!function_exists('m')) {
	function m($name = '')
	{
		static $_modules = array();
		if (isset($_modules[$name])) {
			return $_modules[$name];
		}
		$model = MODEL_CORE .'model/' . strtolower($name) . '.php';
		if (!is_file($model)) {
			exit(' Model ' . $name . ' Not Found!');
		}
		require_once $model;
		$class_name = ucfirst($name) . '_Model';
		$_modules[$name] = new $class_name();
		return $_modules[$name];
	}
}

if (!function_exists('d')) {
	function d($name = '')
	{
		static $_modules = array();
		if (isset($_modules[$name])) {
			return $_modules[$name];
		}
		$model = MODEL_CORE . 'data/' . strtolower($name) . '.php';
		if (!is_file($model)) {
			exit(' Data Model ' . $name . ' Not Found!');
		}
		require_once MODEL_INC . 'data_model.php';
		require_once $model;
		$class_name = ucfirst($name) . '_DataModel';
		$_modules[$name] = new $class_name();
		return $_modules[$name];
	}
}






if (!function_exists('com')) {
	function com($name = '')
	{
		static $_coms = array();

		if (isset($_coms[$name])) {
			return $_coms[$name];
		}

		$model = MODEL_CORE . 'com/' . strtolower($name) . '.php';

		if (!is_file($model)) {
			return false;
		}

		require_once MODEL_CORE . 'inc/com_model.php';
		require_once $model;
		$class_name = ucfirst($name) . '_ComModel';
		$_coms[$name] = new $class_name($name);

		if ($name == 'perm') {
			return $_coms[$name];
		}

		if (com('perm')->check_com($name)) {
			return $_coms[$name];
		}

		return false;
	}
}


if (!function_exists('get_last_day')) {
	function get_last_day($year, $month)
	{
		return date('t', strtotime($year . '-' . $month . ' -1'));
	}
}

if (!function_exists('show_message')) {
	function show_message($msg = '', $url = '', $type = '')
	{
		$site = new Page();
		$site->tomessage($msg, $url, $type);
		exit();
	}
}

if (!function_exists('show_json')) {
	function show_json($status = 1, $return = NULL)
	{
		$ret = array('status' => $status, 'result' => $status == 1 ? array('url' => referer()) : array());

		if (!is_array($return)) {
			if ($return) {
				$ret['result']['message'] = $return;
			}

			exit(json_encode($ret));
		}
		else {
			$ret['result'] = $return;
		}

		if (isset($return['url'])) {
			$ret['result']['url'] = $return['url'];
		}
		else {
			if ($status == 1) {
				$ret['result']['url'] = referer();
			}
		}

		exit(json_encode($ret));
	}
}





if (!function_exists('b64_encode')) {
	function b64_encode($obj)
	{
		if (is_array($obj)) {
			return urlencode(base64_encode(json_encode($obj)));
		}

		return urlencode(base64_encode($obj));
	}
}

if (!function_exists('b64_decode')) {
	function b64_decode($str, $is_array = true)
	{
		$str = base64_decode(urldecode($str));

		if ($is_array) {
			return json_decode($str, true);
		}

		return $str;
	}
}

if (!function_exists('create_image')) {
	function create_image($img)
	{
		$ext = strtolower(substr($img, strrpos($img, '.')));

		if ($ext == '.png') {
			$thumb = imagecreatefrompng($img);
		}
		else if ($ext == '.gif') {
			$thumb = imagecreatefromgif($img);
		}
		else {
			$thumb = imagecreatefromjpeg($img);
		}

		return $thumb;
	}
}


if (!function_exists('template_compile')) {
	function template_compile($from, $to, $inmodule = false)
	{
		$path = dirname($to);

		if (!is_dir($path)) {
			load()->func('file');
			mkdirs($path);
		}

		$content = shop_template_parse(file_get_contents($from), $inmodule);
		if ((IMS_FAMILY == 'x') && !preg_match('/(footer|header|account\\/welcome|login|register)+/', $from)) {
			$content = str_replace('微擎', '系统', $content);
		}

		file_put_contents($to, $content);
	}
}

if (!function_exists('shop_template_parse')) {
	function shop_template_parse($str, $inmodule = false)
	{
		global $_W;
		$str = template_parse($str, $inmodule);
		if (strexists($_W['siteurl'], 'merchant.php') || strexists($_W['siteurl'], 'r=merch.mmanage')) {
			if (p('merch')) {
				$str = preg_replace('/{ifp\\s+(.+?)}/', '<?php if(mcv($1)) { ?>', $str);
				$str = preg_replace('/{ifpp\\s+(.+?)}/', '<?php if(mcp($1)) { ?>', $str);
				$str = preg_replace('/{ife\\s+(\\S+)\\s+(\\S+)}/', '<?php if( mce($1 ,$2) ) { ?>', $str);
				return $str;
			}
		}

		if (strexists($_W['siteurl'], 'newstoreant.php')) {
			if (p('newstore')) {
				$str = preg_replace('/{ifp\\s+(.+?)}/', '<?php if(mcv($1)) { ?>', $str);
				$str = preg_replace('/{ifpp\\s+(.+?)}/', '<?php if(mcp($1)) { ?>', $str);
				$str = preg_replace('/{ife\\s+(\\S+)\\s+(\\S+)}/', '<?php if( mce($1 ,$2) ) { ?>', $str);
				$str = preg_replace('/{ifs\\s+(.+?)}/', '<?php if( mcs($1) ) { ?>', $str);
				return $str;
			}
		}

		$str = preg_replace('/{ifp\\s+(.+?)}/', '<?php if(cv($1)) { ?>', $str);
		$str = preg_replace('/{ifpp\\s+(.+?)}/', '<?php if(cp($1)) { ?>', $str);
		$str = preg_replace('/{ife\\s+(\\S+)\\s+(\\S+)}/', '<?php if( ce($1 ,$2) ) { ?>', $str);
		return $str;
	}
}

if (!function_exists('cv')) {
    function cv($permtypes = '')
    {
        $perm = com_run('perm::check_perm', $permtypes);
        return $perm;
    }
}
if (!function_exists('com_run')) {
    function com_run($name = '')
    {
        $names = explode('::', $name);
        $com = com($names[0]);

        if (!$com) {
            return false;
        }

        if (!method_exists($com, $names[1])) {
            return false;
        }

        $func_args = func_get_args();
        $args = array_splice($func_args, 1);
        return call_user_func_array(array($com, $names[1]), $args);
    }
}
if (!function_exists('webUrl')) {
	function webUrl($do = '', $query = array(), $full = true)
	{
		global $_W;
		global $_GPC;
		$dos = explode('/', trim($do));
		$routes = array();
		$routes[] = $dos[0];

		if (isset($dos[1])) {
			$routes[] = $dos[1];
		}

		if (isset($dos[2])) {
			$routes[] = $dos[2];
		}

		if (isset($dos[3])) {
			$routes[] = $dos[3];
		}

		$r = implode('.', $routes);

		if (!empty($r)) {
			$query = array_merge(array('r' => $r), $query);
		}

		$query = array_merge(array('do' => 'web'), $query);
		$query = array_merge(array('m' => MODEL_NAME), $query);

		if ($full) {
			return $_W['siteroot'] . 'web/' . substr(wurl('site/entry', $query), 2);
		}

		return wurl('site/entry', $query);
	}
}

if (!function_exists('rc')) {
    function rc($plugin = '')
    {
        global $_W;
        global $_GPC;
        $domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
        $ip = gethostbyname($_SERVER['HTTP_HOST']);
        $setting = setting_load('site');
        $id = (isset($setting['site']['key']) ? $setting['site']['key'] : '0');
        $auth = get_auth();
        load()->func('communication');
        $resp = ihttp_request(MODEL_AUTH_URL, array('ip' => $ip, 'id' => $id, 'code' => $auth['code'], 'domain' => $domain, 'plugin' => $plugin), NULL, 1);
        $result = @json_decode($resp['content'], true);

        if (!empty($result['status'])) {
            return true;
        }

        return false;
    }
}

if (!function_exists('get_auth')) {
    function get_auth()
    {
        global $_W;
//        $set = pdo_fetch('select sets from ' . tablename('ewei_shop_sysset') . ' order by id asc limit 1');
//        $sets = iunserializer($set['sets']);
//
//        if (is_array($sets)) {
//            return is_array($sets['auth']) ? $sets['auth'] : array();
//        }

        return array();
    }
}

if (!function_exists('dump')) {
	function dump()
	{
		$args = func_get_args();

		foreach ($args as $val) {
			echo '<pre style="color: red">';
			var_dump($val);
			echo '</pre>';
		}
	}
}

if (!function_exists('my_scandir')) {
	$my_scenfiles = array();
	function my_scandir($dir)
	{
		global $my_scenfiles;

		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if (($file != '..') && ($file != '.')) {
					if (is_dir($dir . '/' . $file)) {
						my_scandir($dir . '/' . $file);
					}
					else {
						$my_scenfiles[] = $dir . '/' . $file;
					}
				}
			}

			closedir($handle);
		}
	}
}

if (!function_exists('cut_str')) {
	function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
	{
		if ($code == 'UTF-8') {
			$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
			preg_match_all($pa, $string, $t_string);

			if ($sublen < (count($t_string[0]) - $start)) {
				return join('', array_slice($t_string[0], $start, $sublen));
			}

			return join('', array_slice($t_string[0], $start, $sublen));
		}

		$start = $start * 2;
		$sublen = $sublen * 2;
		$strlen = strlen($string);
		$tmpstr = '';
		$i = 0;

		while ($i < $strlen) {
			if (($start <= $i) && ($i < ($start + $sublen))) {
				if (129 < ord(substr($string, $i, 1))) {
					$tmpstr .= substr($string, $i, 2);
				}
				else {
					$tmpstr .= substr($string, $i, 1);
				}
			}

			if (129 < ord(substr($string, $i, 1))) {
				++$i;
			}

			++$i;
		}

		return $tmpstr;
	}
}



if (!function_exists('array_column')) {
	function array_column($input, $column_key, $index_key = NULL)
	{
		$arr = array();

		foreach ($input as $d) {
			if (!isset($d[$column_key])) {
				return NULL;
			}

			if ($index_key !== NULL) {
				return array($d[$index_key] => $d[$column_key]);
			}

			$arr[] = $d[$column_key];
		}

		if ($index_key !== NULL) {
			$tmp = array();

			foreach ($arr as $ar) {
				$tmp[key($ar)] = current($ar);
			}

			$arr = $tmp;
		}

		return $arr;
	}
}

if (!function_exists('is_utf8')) {
	function is_utf8($str)
	{
		return preg_match("%^(?:\r\n            [\\x09\\x0A\\x0D\\x20-\\x7E]              # ASCII\r\n            | [\\xC2-\\xDF][\\x80-\\xBF]             # non-overlong 2-byte\r\n            | \\xE0[\\xA0-\\xBF][\\x80-\\xBF]         # excluding overlongs\r\n            | [\\xE1-\\xEC\\xEE\\xEF][\\x80-\\xBF]{2}  # straight 3-byte\r\n            | \\xED[\\x80-\\x9F][\\x80-\\xBF]         # excluding surrogates\r\n            | \\xF0[\\x90-\\xBF][\\x80-\\xBF]{2}      # planes 1-3\r\n            | [\\xF1-\\xF3][\\x80-\\xBF]{3}          # planes 4-15\r\n            | \\xF4[\\x80-\\x8F][\\x80-\\xBF]{2}      # plane 16\r\n            )*\$%xs", $str);
	}
}

if (!function_exists('price_format')) {
	function price_format($price)
	{
		$prices = explode('.', $price);

		if (intval($prices[1]) <= 0) {
			$price = $prices[0];
		}
		else {
			if (isset($prices[1][1]) && ($prices[1][1] <= 0)) {
				$price = $prices[0] . '.' . $prices[1][0];
			}
		}

		return $price;
	}
}



if (!function_exists('redis')) {
	function redis()
	{
		global $_W;
		static $redis;

		if (is_null($redis)) {
			if (!extension_loaded('redis')) {
				return error(-1, 'PHP 未安装 redis 扩展');
			}

			if (!isset($_W['config']['setting']['redis'])) {
				return error(-1, '未配置 redis, 请检查 data/config.php 中参数设置');
			}

			$config = $_W['config']['setting']['redis'];

			if (empty($config['server'])) {
				$config['server'] = '127.0.0.1';
			}

			if (empty($config['port'])) {
				$config['port'] = '6379';
			}

			$redis_temp = new Redis();

			if ($config['pconnect']) {
				$connect = $redis_temp->pconnect($config['server'], $config['port'], $config['timeout']);
			}
			else {
				$connect = $redis_temp->connect($config['server'], $config['port'], $config['timeout']);
			}

			if (!$connect) {
				return error(-1, 'redis 连接失败, 请检查 data/config.php 中参数设置');
			}

			if (!empty($config['requirepass'])) {
				$redis_temp->auth($config['requirepass']);
			}

			try {
				$ping = $redis_temp->ping();
			}
			catch (ErrorException $e) {
				return error(-1, 'redis 无法正常工作，请检查 redis 服务');
			}

			if ($ping != '+PONG') {
				return error(-1, 'redis 无法正常工作，请检查 redis 服务');
			}

			$redis = $redis_temp;
		}
		else {
			try {
				$ping = $redis->ping();
			}
			catch (ErrorException $e) {
				$redis = NULL;
				$redis = redis();
				$ping = $redis->ping();
			}

			if ($ping != '+PONG') {
				$redis = NULL;
				$redis = redis();
			}
		}

		return $redis;
	}
}

if (!function_exists('logg')) {
	function logg($name, $data)
	{
		global $_W;
		$data = (is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data);
		file_put_contents(IA_ROOT . '/' . $name, $data);
	}
}

if (!function_exists('is_wxerror')) {
	function is_wxerror($data)
	{
		if (!is_array($data) || !array_key_exists('errcode', $data) || (array_key_exists('errcode', $data) && ($data['errcode'] == 0))) {
			return false;
		}

		return true;
	}
}

if (!function_exists('set_wxerrmsg')) {
	function set_wxerrmsg($data)
	{
		$errors = array(-1 => '系统繁忙，此时请稍候再试', 0 => '请求成功', 40001 => '获取access_token时AppSecret错误，或者access_token无效。请认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口', 40002 => '不合法的凭证类型', 40003 => '不合法的OpenID，请确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID', 40004 => '不合法的媒体文件类型', 40005 => '不合法的文件类型', 40006 => '不合法的文件大小', 40007 => '不合法的媒体文件id', 40008 => '不合法的消息类型', 40009 => '不合法的图片文件大小', 40010 => '不合法的语音文件大小', 40011 => '不合法的视频文件大小', 40012 => '不合法的缩略图文件大小', 40013 => '不合法的AppID，请检查AppID的正确性，避免异常字符，注意大小写', 40014 => '不合法的access_token，请认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口', 40015 => '不合法的菜单类型', 40016 => '不合法的按钮个数', 40017 => '不合法的按钮个数', 40018 => '不合法的按钮名字长度', 40019 => '不合法的按钮KEY长度', 40020 => '不合法的按钮URL长度', 40021 => '不合法的菜单版本号', 40022 => '不合法的子菜单级数', 40023 => '不合法的子菜单按钮个数', 40024 => '不合法的子菜单按钮类型', 40025 => '不合法的子菜单按钮名字长度', 40026 => '不合法的子菜单按钮KEY长度', 40027 => '不合法的子菜单按钮URL长度', 40028 => '不合法的自定义菜单使用用户', 40029 => '不合法的oauth_code', 40030 => '不合法的refresh_token', 40031 => '不合法的openid列表', 40032 => '不合法的openid列表长度', 40033 => '不合法的请求字符，不能包含\\uxxxx格式的字符', 40035 => '不合法的参数', 40038 => '不合法的请求格式', 40039 => '不合法的URL长度', 40050 => '不合法的分组id', 40051 => '分组名字不合法', 40117 => '分组名字不合法', 40118 => 'media_id大小不合法', 40119 => 'button类型错误', 40120 => 'button类型错误', 40121 => '不合法的media_id类型', 40132 => '微信号不合法', 40137 => '不支持的图片格式', 40155 => '请勿添加其他公众号的主页链接', 41001 => '缺少access_token参数', 41002 => '缺少appid参数', 41003 => '缺少refresh_token参数', 41004 => '缺少secret参数', 41005 => '缺少多媒体文件数据', 41006 => '缺少media_id参数', 41007 => '缺少子菜单数据', 41008 => '缺少oauth code', 41009 => '缺少openid', 42001 => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明', 42002 => 'refresh_token超时', 42003 => 'oauth_code超时', 42007 => '用户修改微信密码，accesstoken和refreshtoken失效，需要重新授权', 43001 => '需要GET请求', 43002 => '需要POST请求', 43003 => '需要HTTPS请求', 43004 => '需要接收者关注', 43005 => '需要好友关系', 43019 => '需要将接收者从黑名单中移除', 44001 => '多媒体文件为空', 44002 => 'POST的数据包为空', 44003 => '图文消息内容为空', 44004 => '文本消息内容为空', 45001 => '多媒体文件大小超过限制', 45002 => '消息内容超过限制', 45003 => '标题字段超过限制', 45004 => '描述字段超过限制', 45005 => '链接字段超过限制', 45006 => '图片链接字段超过限制', 45007 => '语音播放时间超过限制', 45008 => '图文消息超过限制', 45009 => '接口调用超过限制', 45010 => '创建菜单个数超过限制', 45011 => 'API调用太频繁，请稍候再试', 45015 => '回复时间超过限制', 45016 => '系统分组，不允许修改', 45017 => '分组名字过长', 45018 => '分组数量超过上限', 45047 => '客服接口下行条数超过上限', 46001 => '不存在媒体数据', 46002 => '不存在的菜单版本', 46003 => '不存在的菜单数据', 46004 => '不存在的用户', 47001 => '解析JSON/XML内容错误', 48001 => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限', 48002 => '粉丝拒收消息（粉丝在公众号选项中，关闭了“接收消息”）', 48004 => 'api接口被封禁，请登录mp.weixin.qq.com查看详情', 48005 => 'api禁止删除被自动回复和自定义菜单引用的素材', 48006 => 'api禁止清零调用次数，因为清零次数达到上限', 50001 => '用户未授权该api', 50002 => '用户受限，可能是违规后接口被封禁', 61451 => '参数错误(invalid parameter)', 61452 => '无效客服账号(invalid kf_account)', 61453 => '客服帐号已存在(kf_account exsited)', 61454 => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid   kf_acount length)', 61455 => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in     kf_account)', 61457 => '无效头像文件类型(invalid   file type)', 61450 => '系统错误(system error)', 61500 => '日期格式错误', 65301 => '不存在此menuid对应的个性化菜单', 65302 => '没有相应的用户', 65303 => '没有默认菜单，不能创建个性化菜单', 65304 => 'MatchRule信息为空', 65305 => '个性化菜单数量受限', 65306 => '不支持个性化菜单的帐号', 65307 => '个性化菜单信息为空', 65308 => '包含没有响应类型的button', 65309 => '个性化菜单开关处于关闭状态', 65310 => '填写了省份或城市信息，国家信息不能为空', 65311 => '填写了城市信息，省份信息不能为空', 65312 => '不合法的国家信息', 65313 => '不合法的省份信息', 65314 => '不合法的城市信息', 65316 => '该公众号的菜单设置了过多的域名外跳（最多跳转到3个域名的链接）', 65317 => '不合法的URL', 9001001 => 'POST数据参数不合法', 9001002 => '远端服务不可用', 9001003 => 'Ticket不合法', 9001004 => '获取摇周边用户信息失败', 9001005 => '获取商户信息失败', 9001006 => '获取OpenID失败', 9001007 => '上传文件缺失', 9001008 => '上传素材的文件类型不合法', 9001009 => '上传素材的文件尺寸不合法', 9001010 => '上传失败', 9001020 => '帐号不合法', 9001021 => '已有设备激活率低于50%，不能新增设备', 9001022 => '设备申请数不合法，必须为大于0的数字', 9001023 => '已存在审核中的设备ID申请', 9001024 => '一次查询设备ID数量不能超过50', 9001025 => '设备ID不合法', 9001026 => '页面ID不合法', 9001027 => '页面参数不合法', 9001028 => '一次删除页面ID数量不能超过10', 9001029 => '页面已应用在设备中，请先解除应用关系再删除', 9001030 => '一次查询页面ID数量不能超过50', 9001031 => '时间区间不合法', 9001032 => '保存设备与页面的绑定关系参数错误', 9001033 => '门店ID不合法', 9001034 => '设备备注信息过长', 9001035 => '设备申请参数不合法', 9001036 => '查询起始值begin不合法');

		if (array_key_exists($data['errcode'], $errors)) {
			$data['errmsg'] = $errors[$data['errcode']];
		}

		return $data;
	}
}

if (!function_exists('tpl_form_field_image2')) {
	function tpl_form_field_image2($name, $value = '', $default = '', $options = array())
	{
		global $_W;

		if (empty($default)) {
			$default = '../addons/'.MODEL_NAME.'/static/images/nopic.png';
		}

		$val = $default;

		if (!empty($value)) {
			$val = tomedia($value);
		}
		else {
			$val = '../addons/'.MODEL_NAME.'/static/images/nopic.png';
		}

		if (!empty($options['global'])) {
			$options['global'] = true;
		}
		else {
			$options['global'] = false;
		}

		if (empty($options['class_extra'])) {
			$options['class_extra'] = '';
		}

		if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
			if (!preg_match('/^\\w+([\\/]\\w+)?$/i', $options['dest_dir'])) {
				exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
			}
		}

		$options['direct'] = true;
		$options['multiple'] = false;

		if (isset($options['thumb'])) {
			$options['thumb'] = !empty($options['thumb']);
		}

		$options['fileSizeLimit'] = intval($GLOBALS['_W']['setting']['upload']['image']['limit']) * 1024;
		$s = '';

		if (!defined('TPL_INIT_IMAGE')) {
			$s = "\r\n\t\t<script type=\"text/javascript\">\r\n\t\t\tfunction showImageDialog(elm, opts, options) {\r\n\t\t\t\trequire([\"util\"], function(util){\r\n\t\t\t\t\tvar btn = \$(elm);\r\n\t\t\t\t\tvar ipt = btn.parent().prev();\r\n\t\t\t\t\tvar val = ipt.val();\r\n\t\t\t\t\tvar img = ipt.parent().next().children();\r\n\t\t\t\t\toptions = " . str_replace('"', '\'', json_encode($options)) . ";\r\n\t\t\t\t\tutil.image(val, function(url){\r\n\t\t\t\t\t\tif(url.url){\r\n\t\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\t\timg.get(0).src = url.url;\r\n\t\t\t\t\t\t\t\timg.closest(\".input-group\").show();\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\tipt.val(url.attachment);\r\n\t\t\t\t\t\t\tipt.attr(\"filename\",url.filename);\r\n\t\t\t\t\t\t\tipt.attr(\"url\",url.url);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t\tif(url.media_id){\r\n\t\t\t\t\t\t\tif(img.length > 0){\r\n\t\t\t\t\t\t\t\timg.get(0).src = \"\";\r\n\t\t\t\t\t\t\t}\r\n\t\t\t\t\t\t\tipt.val(url.media_id);\r\n\t\t\t\t\t\t}\r\n\t\t\t\t\t}, options);\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t\tfunction deleteImage(elm){\r\n\t\t\t\trequire([\"jquery\"], function(\$){\r\n\t\t\t\t\t\$(elm).prev().attr(\"src\", \"../addons/".MODEL_NAME."/static/images/default-pic.jpg\");\r\n\t\t\t\t\t\$(elm).parent().prev().find(\"input\").val(\"\");\r\n\t\t\t\t});\r\n\t\t\t}\r\n\t\t</script>";
			define('TPL_INIT_IMAGE', true);
		}

		$s .= "\r\n\t\t<div class=\"input-group " . $options['class_extra'] . "\">\r\n\t\t\t<input type=\"text\" name=\"" . $name . '" value="' . $value . '"' . ($options['extras']['text'] ? $options['extras']['text'] : '') . " class=\"form-control\" autocomplete=\"off\">\r\n\t\t\t<span class=\"input-group-btn\">\r\n\t\t\t\t<button class=\"btn btn-primary\" type=\"button\" onclick=\"showImageDialog(this);\">选择图片</button>\r\n\t\t\t</span>\r\n\t\t</div>";
		$s .= '<div class="input-group ' . $options['class_extra'] . '" style="margin-top:.5em;"><img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" ' . ($options['extras']['image'] ? $options['extras']['image'] : '') . " width=\"150\" />\r\n                <em class=\"close\" style=\"position:absolute; top: 0px; right: -14px;\" title=\"删除这张图片\" onclick=\"deleteImage(this)\">×</em>\r\n            </div>";
		return $s;
	}
}

if (!function_exists('tpl_form_field_multi_image2')) {
	function tpl_form_field_multi_image2($name, $value = array(), $options = array())
	{
		global $_W;
	$options['multiple'] = true;
	$options['direct'] = false;
	$options['fileSizeLimit'] = intval($GLOBALS['_W']['setting']['upload']['image']['limit']) * 1024;
	if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
		if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
			exit('图片上传目录错误,只能指定最多两级目录,如: "we7_store","we7_store/d1"');
		}
	}
	$s = '';
	if (!defined('TPL_INIT_MULTI_IMAGE')) {
		$s = '
<script type="text/javascript">
	function uploadMultiImage(elm) {
		var name = $(elm).next().val();
		util.image( "", function(urls){
			$.each(urls, function(idx, url){
				$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.attachment+\'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>\');
			});
		}, ' . json_encode($options) . ');
	}
	function deleteMultiImage(elm){
		$(elm).parent().remove();
	}
</script>';
		define('TPL_INIT_MULTI_IMAGE', true);
	}

	$s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default btn-primary" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
	if (is_array($value) && count($value) > 0) {
		foreach ($value as $row) {
			$s .= '
<div class="multi-item">
	<img src="' . tomedia($row) . '" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="' . $name . '[]" value="' . $row . '" >
	<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
</div>';
		}
	}
	$s .= '</div>';

	return $s;
	}
}

if (!function_exists('pagination2')) {
	function pagination2($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '', 'callbackfuncname' => ''))
	{
		global $_W;
		$pdata = array('tcount' => 0, 'tpage' => 0, 'cindex' => 0, 'findex' => 0, 'pindex' => 0, 'nindex' => 0, 'lindex' => 0, 'options' => '');


		$html = '<div style="float:right"><ul class="pagination-centered"  style="display: inline-block;"><li><span style="color: #a978d1;float: left;">共' . $total . '条记录&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></li></ul>';

		if (!empty($total)) {
			$pdata['tcount'] = $total;
			$pdata['tpage'] = empty($pageSize) || ($pageSize < 0) ? 1 : ceil($total / $pageSize);

			if (1 < $pdata['tpage']) {
				$html .= '<ul class="pagination pagination-centered">';
				$cindex = $pageIndex;
				$cindex = min($cindex, $pdata['tpage']);
				$cindex = max($cindex, 1);
				$pdata['cindex'] = $cindex;
				$pdata['findex'] = 1;
				$pdata['pindex'] = 1 < $cindex ? $cindex - 1 : 1;
				$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
				$pdata['lindex'] = $pdata['tpage'];

			 if ($url) {
					$pdata['jump'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
					$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
					$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
					$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
					$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
				} else {
					$jump_get = $_GET;
					$jump_get['page'] = '';
					$pdata['jump'] = 'href="' . $_W['script_name'] . '?' . http_build_query($jump_get) . $pdata['cindex'] . '" data-href="' . $_W['script_name'] . '?' . http_build_query($jump_get) . '"';
					$_GET['page'] = $pdata['findex'];
					$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
					$_GET['page'] = $pdata['pindex'];
					$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
					$_GET['page'] = $pdata['nindex'];
					$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
					$_GET['page'] = $pdata['lindex'];
					$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
				}

				if (1 < $pdata['cindex']) {
					$html .= '<li><a ' . $pdata['faa'] . ' class="pager-nav">首页</a></li>';
					$html .= '<li><a ' . $pdata['paa'] . ' class="pager-nav">&laquo;上一页</a></li>';
				}

				if (!$context['before'] && ($context['before'] != 0)) {
					$context['before'] = 5;
				}

				if (!$context['after'] && ($context['after'] != 0)) {
					$context['after'] = 4;
				}

				if (($context['after'] != 0) && ($context['before'] != 0)) {
					$range = array();
					$range['start'] = max(1, $pdata['cindex'] - $context['before']);
					$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);

					if (($range['end'] - $range['start']) < ($context['before'] + $context['after'])) {
						$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
						$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
					}

					$i = $range['start'];

					while ($i <= $range['end']) {
						if ($context['isajax']) {
							$aa = 'href="javascript:;" page="' . $i . '" ' . ($callbackfunc ? 'onclick="' . $callbackfunc . '(\'' . $url . '\', \'' . $i . '\', this);return false;"' : '');
						}
						else if ($url) {
							$aa = 'href="?' . str_replace('*', $i, $url) . '"';
						}
						else {
							$_GET['page'] = $i;
							$aa = 'href="?' . http_build_query($_GET) . '"';
						}

						$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : '<li><a ' . $aa . '>' . $i . '</a></li>');
						++$i;
					}
				}

				if ($pdata['cindex'] < $pdata['tpage']) {
					$html .= '<li><a ' . $pdata['naa'] . ' class="pager-nav">下一页&raquo;</a></li>';
					$html .= '<li><a ' . $pdata['laa'] . ' class="pager-nav">尾页</a></li>';
				}

				$html .= '</ul>';

				if (5 < $pdata['tpage']) {
					$html .= '<ul class="pagination pagination-centered">';
					$html .= '<li><span style="padding: 0" class="input"><input style="width: 60px;text-align: center;border: none;" value=\'' . $pdata['cindex'] . '\' type=\'tel\'/></span></li>';
					$html .= '<li><a ' . $pdata['jump'] . ' class="pager-nav pager-nav-jump">跳转</a></li>';
					$html .= '</ul>';
					$html .= '<script>$(function() {$(".pagination .input input").bind("input propertychange", function() {var val=$(this).val(),elm=$(this).closest("ul").find(".pager-nav-jump"),href=elm.data("href");elm.attr("href", href+val)}).on("keydown", function(e) {if (e.keyCode == "13") {var val=$(this).val(),elm=$(this).closest("ul").find(".pager-nav-jump"),href=elm.data("href"); location.href=href+val;}});})</script>';
				}
			}
		}

		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('tpl_form_field_editor')) {
	function tpl_form_field_editor($params = array(), $callback = NULL)
	{
		$html = '<span class="form-editor-group">';
		$html .= '<span class="form-control-static form-editor-show">';
		$html .= '<a class="form-editor-text">' . $params['value'] . '</a>';
		$html .= '<a class="text-primary form-editor-btn">修改</a>';
		$html .= '</span>';
		$html .= '<span class="input-group form-editor-edit">';
		$html .= '<input class="form-control form-editor-input" value="' . $params['value'] . '" name="' . $params['name'] . '"';

		if (!empty($params['placeholder'])) {
			$html .= 'placeholder="' . $params['placeholder'] . '"';
		}

		if (!empty($params['id'])) {
			$html .= 'id="' . $params['id'] . '"';
		}

		if (!empty($params['data-rule-required']) || !empty($params['required'])) {
			$html .= ' data-rule-required="true"';
		}

		if (!empty($params['data-msg-required'])) {
			$html .= ' data-msg-required="' . $params['data-msg-required'] . '"';
		}

		$html .= ' /><span class="input-group-btn">';
		$html .= '<span class="btn btn-default form-editor-finish"';

		if ($callback) {
			$html .= 'data-callback="' . $callback . '"';
		}

		$html .= '><i class="icow icow-wancheng"></i></span>';
		$html .= '</span>';
		$html .= '</span>';
		return $html;
	}
}

if (!function_exists('tpl_form_field_position')) {
	function tpl_form_field_position($field, $value = array())
	{
		$s = '';

		if (!defined('TPL_INIT_COORDINATE')) {
			$s .= "<script type=\"text/javascript\">\r\n                    function showCoordinate(elm) {\r\n                        \r\n                            var val = {};\r\n                            val.lng = parseFloat(\$(elm).parent().prev().prev().find(\":text\").val());\r\n                            val.lat = parseFloat(\$(elm).parent().prev().find(\":text\").val());\r\n                            val = biz.BdMapToTxMap(val.lat,val.lng);\r\n                            biz.map(val, function(r){\r\n                                var address_label = \$(\"#address_label\");\r\n                                if (address_label.length>0)\r\n                                {\r\n                                    address_label.val(r.label);\r\n                                }\r\n                                r = biz.TxMapToBdMap(r.lat,r.lng);\r\n                                \$(elm).parent().prev().prev().find(\":text\").val(r.lng);\r\n                                \$(elm).parent().prev().find(\":text\").val(r.lat);\r\n                            },\"" . MODEL_NAME_URL . 'template/web/util/area/map.html' . "\");\r\n    }\r\n    \r\n                </script>";
			define('TPL_INIT_COORDINATE', true);
		}

		$s .= "\r\n            <div class=\"row row-fix\">\r\n                <div class=\"col-xs-4 col-sm-4\">\r\n                    <input type=\"text\" name=\"" . $field . '[lng]" value="' . $value['lng'] . "\" placeholder=\"地理经度\"  class=\"form-control\" />\r\n                </div>\r\n                <div class=\"col-xs-4 col-sm-4\">\r\n                    <input type=\"text\" name=\"" . $field . '[lat]" value="' . $value['lat'] . "\" placeholder=\"地理纬度\"  class=\"form-control\" />\r\n                </div>\r\n                <div class=\"col-xs-4 col-sm-4\">\r\n                    <button onclick=\"showCoordinate(this);\" class=\"btn btn-default\" type=\"button\">选择坐标</button>\r\n                </div>\r\n            </div>";
		return $s;
	}
}


function auth_user($siteid, $domain) 
{
	//$ret = cloud_upgrade('user', array('website' => $siteid, 'domain' => $domain));
	$ret['status']=1;
	return $ret;
}
function auth_checkauth($auth)
{
	//$ret = cloud_upgrade('checkauth', array('website' => $auth['id'], 'domain' => $auth['domain'], 'code' => $auth['code']));
	$ret['status']=1;return $ret;
}
function auth_grant($data)
{
	//$ret = cloud_upgrade('grant', array('website' => $data['siteid'],'domain' => $data['domain'], 'code' => $data['code']));
	$ret['status']=1;return $ret;
}
function auth_check($auth, $version)
{
	//$ret = cloud_upgrade('check', array('ip' => $auth['ip'],'domain' => $auth['domain'],'code' => $auth['code'],'version' => $version));
	$ret['status']=1;return $ret;
}
function auth_download($auth, $path)
{
	//$ret = cloud_upgrade('download', array('ip' => $auth['ip'],'domain' => $auth['domain'],'code' => $auth['code'],'path' => $path));
	$ret['status']=1;return $ret;
}
function auth_downaddress($auth) 
{
	//$ret = cloud_upgrade('downaddress', array('code' => $auth['code']));
	$ret['status']=1;return $ret;
}
function auth_upaddress($auth, $data) 
{
	//$ret = cloud_upgrade('upaddress', array('code' => $auth['code'], 'data' => $data));
	$ret['status']=1;return $ret;
}
function cloud_upgrade($type, $post_data = array(), $timeout = 60) 
{
	global $_W;
	load()->func("communication");
	$domain = trim(preg_replace('/http(s)?:\\/\\//', '', rtrim($_W['siteroot'], '/')));
	$extra['CURLOPT_REFERER'] = $domain; 
	$post_data['type'] = $type; 
	$post_data['module'] = $_W['current_module']['name']; 
	$resp = ihttp_request(constant(strtoupper($_W['current_module']['name']) . '_AUTH_URL'), $post_data, $extra, $timeout);
	$ret = @json_decode($resp['content'], true);
	$ret['status']=1;return $ret;
}



$GLOBALS['_W']['config']['db']['tablepre'] = empty($GLOBALS['_W']['config']['db']['tablepre']) ? $GLOBALS['_W']['config']['db']['master']['tablepre'] : $GLOBALS['_W']['config']['db']['tablepre'];

function db_table_schema_ab($db, $tablename = '') {
	$result = $db->fetch("SHOW TABLE STATUS LIKE '" . trim($db->tablename($tablename), '`') . "'");
	if(empty($result) || empty($result['Create_time'])) {
		return array();
	}
	$ret['tablename'] = $result['Name'];
	$ret['charset'] = $result['Collation'];
	$ret['engine'] = $result['Engine'];
	$ret['increment'] = $result['Auto_increment'];
	$result = $db->fetchall("SHOW FULL COLUMNS FROM " . $db->tablename($tablename));
	foreach($result as $value) {
		$temp = array();
		$type = explode(" ", $value['Type'], 2);
		$temp['name'] = $value['Field'];
		$pieces = explode('(', $type[0], 2);
		$temp['type'] = $pieces[0];
		$temp['length'] = rtrim($pieces[1], ')');
		$temp['null'] = $value['Null'] != 'NO';
										$temp['signed'] = empty($type[1]);
		$temp['increment'] = $value['Extra'] == 'auto_increment';
		$ret['fields'][$value['Field']] = $temp;
	}
	$result = $db->fetchall("SHOW INDEX FROM " . $db->tablename($tablename));
	foreach($result as $value) {
$ret['indexes'][$value['Key_name']]['name'] = $value['Key_name'];

		$ret['indexes'][$value['Key_name']]['type'] = ($value['Key_name'] == 'PRIMARY') ? 'primary' : ($value['Non_unique'] == 0 ? 'unique' : ($value['Index_type'] == 'FULLTEXT'?"FULLTEXT":"index"));
		$ret['indexes'][$value['Key_name']]['fields'][] = $value['Column_name'];
		if(!empty($value['Sub_part'])){
			$ret['indexes'][$value['Key_name']]['length'] = $value['Sub_part'];
		}
	}
	return $ret;
}



function db_table_serialize_ab($db, $dbname) {
	$tables = $db->fetchall('SHOW TABLES');
	if (empty($tables)) {
		return '';
	}
	$struct = array();
	foreach ($tables as $value) {
		$structs[] = db_table_schema_ab($db, substr($value['Tables_in_' . $dbname], strpos($value['Tables_in_' . $dbname], '_') + 1));
	}
	return iserializer($structs);
}


function db_table_create_sqll_ab($schema) {
	$pieces = explode('_', $schema['charset']);
	$charset = $pieces[0];
	$engine = $schema['engine'];
	$schema['tablename'] = str_replace('ims_', $GLOBALS['_W']['config']['db']['tablepre'], $schema['tablename']);
	$sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
	foreach ($schema['fields'] as $value) {
		$piece = _db_build_field_sql_ab($value);
		$sql .= "`{$value['name']}` {$piece},\n";
	}
	foreach ($schema['indexes'] as $value) {
		$fields = implode('`,`', $value['fields']);
	
		if($value['type'] == 'index') {
			if(!empty($value['length'])){
				$sql .= "KEY `{$value['name']}` (`{$fields}`({$value['length']})),\n";
			}else{
				$sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
			}
			
		}
		if($value['type'] == 'unique') {
			$sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
		}
		if($value['type'] == 'primary') {
			$sql .= "PRIMARY KEY (`{$fields}`),\n";
		}
		if($value['type'] == 'FULLTEXT') {
			$sql .= "FULLTEXT KEY `{$value['name']}` (`{$fields}`),\n";
		}
	}
	
	$sql = rtrim($sql);
	$sql = rtrim($sql, ',');
	
	$sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
	return $sql;
}


function db_schema_comparel_ab($table1, $table2) {
	$table1['charset'] == $table2['charset'] ? '' : $ret['diffs']['charset'] = true;
	
	$fields1 = array_keys($table1['fields']);
	$fields2 = array_keys($table2['fields']);
	$diffs = array_diff($fields1, $fields2);
	if(!empty($diffs)) {
		$ret['fields']['greater'] = array_values($diffs);
	}
	$diffs = array_diff($fields2, $fields1);
	if(!empty($diffs)) {
		$ret['fields']['less'] = array_values($diffs);
	}
	$diffs = array();
	$intersects = array_intersect($fields1, $fields2);
	if(!empty($intersects)) {
		foreach($intersects as $field) {
			if($table1['fields'][$field] != $table2['fields'][$field]) {
				$diffs[] = $field;
			}
		}
	}
	if(!empty($diffs)) {
		$ret['fields']['diff'] = array_values($diffs);
	}

	$indexes1 = array_keys($table1['indexes']);
	$indexes2 = array_keys($table2['indexes']);
	$diffs = array_diff($indexes1, $indexes2);
	if(!empty($diffs)) {
		$ret['indexes']['greater'] = array_values($diffs);
	}
	$diffs = array_diff($indexes2, $indexes1);
	if(!empty($diffs)) {
		$ret['indexes']['less'] = array_values($diffs);
	}
	$diffs = array();
	$intersects = array_intersect($indexes1, $indexes2);
	if(!empty($intersects)) {
		foreach($intersects as $index) {
			if($table1['indexes'][$index] != $table2['indexes'][$index]) {
				$diffs[] = $index;
			}
		}
	}
	if(!empty($diffs)) {
		$ret['indexes']['diff'] = array_values($diffs);
	}

	return $ret;
}

function db_table_fix_sql_ab($schema1, $schema2, $strict = false) {
	if(empty($schema1)) {
		return array(db_table_create_sqll_ab($schema2));
	}
	$diff = $result = db_schema_comparel_ab($schema1, $schema2);
	if(!empty($diff['diffs']['tablename'])) {
		return array(db_table_create_sqll_ab($schema2));
	}
	$sqls = array();
	if(!empty($diff['diffs']['engine'])) {
		$sqls[] = "ALTER TABLE `{$schema1['tablename']}` ENGINE = {$schema2['engine']}";
	}

	if(!empty($diff['diffs']['charset'])) {
		$pieces = explode('_', $schema2['charset']);
		$charset = $pieces[0];
		$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DEFAULT CHARSET = {$charset}";
	}

	if(!empty($diff['fields'])) {
		if(!empty($diff['fields']['less'])) {
			foreach($diff['fields']['less'] as $fieldname) {
				$field = $schema2['fields'][$fieldname];
				$piece = _db_build_field_sql_ab($field);
				if(!empty($field['rename']) && !empty($schema1['fields'][$field['rename']])) {
					$sql = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['rename']}` `{$field['name']}` {$piece}";
					unset($schema1['fields'][$field['rename']]);
				} else {
					if($field['position']) {
						$pos = ' ' . $field['position'];
					}
					$sql = "ALTER TABLE `{$schema1['tablename']}` ADD `{$field['name']}` {$piece}{$pos}";
				}
								$primary = array();
				$isincrement = array();
				if (strexists($sql, 'AUTO_INCREMENT')) {
					$isincrement = $field;
					$sql =  str_replace('AUTO_INCREMENT', '', $sql);
					foreach ($schema1['fields'] as $field) {
						if ($field['increment'] == 1) {
							$primary = $field;
							break;
						} 
					}
					if (!empty($primary)) {
						$piece = _db_build_field_sql_ab($primary);
						if (!empty($piece)) {
							$piece = str_replace('AUTO_INCREMENT', '', $piece);
						}
						$sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$primary['name']}` `{$primary['name']}` {$piece}";
					}
				}
				$sqls[] = $sql;
			}
		}
		if(!empty($diff['fields']['diff'])) {
			foreach($diff['fields']['diff'] as $fieldname) {
				$field = $schema2['fields'][$fieldname];
				$piece = _db_build_field_sql_ab($field);
				if(!empty($schema1['fields'][$fieldname])) {
					$sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$field['name']}` `{$field['name']}` {$piece}";
				}
			}
		}
		if($strict && !empty($diff['fields']['greater'])) {
			foreach($diff['fields']['greater'] as $fieldname) {
				if(!empty($schema1['fields'][$fieldname])) {
					$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$fieldname}`";
				}
			}
		}
	}

	if(!empty($diff['indexes'])) {
		
		if(!empty($diff['indexes']['less'])) {
			foreach($diff['indexes']['less'] as $indexname) {
				$index = $schema2['indexes'][$indexname];
				$piece = _db_build_index_sql_ab($index);
				$sqls[] = "ALTER TABLE `{$schema1['tablename']}` ADD {$piece}";
			}
		}
		if(!empty($diff['indexes']['diff'])) {
			foreach($diff['indexes']['diff'] as $indexname) {
				$index = $schema2['indexes'][$indexname];
				
				$piece = _db_build_index_sql_ab($index);
				
				$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP ".($indexname == 'PRIMARY' ? " PRIMARY KEY " :($index['type']=="FULLTEXT"? "FULLTEXT ":"INDEX {$indexname}" )).", ADD {$piece}";
			}
			 
		}
		if($strict && !empty($diff['indexes']['greater'])) {
			foreach($diff['indexes']['greater'] as $indexname) {
				$sqls[] = "ALTER TABLE `{$schema1['tablename']}` DROP `{$indexname}`";
			}
		}
	}
	if (!empty($isincrement)) {
		$piece = _db_build_field_sql_ab($isincrement);
		$sqls[] = "ALTER TABLE `{$schema1['tablename']}` CHANGE `{$isincrement['name']}` `{$isincrement['name']}` {$piece}";
	}
	return $sqls;
}

function _db_build_index_sql_ab($index) {
	$piece = '';
	$fields = implode('`,`', $index['fields']);
	
	
	if($index['type'] == 'index') {
		if(!empty($index['length'])){
				$piece .= "KEY `{$index['name']}` (`{$fields}`({$index['length']}))";
			}else{
				$piece .= "KEY `{$index['name']}` (`{$fields}`)";
			}
		//$piece .= " INDEX `{$index['name']}` (`{$fields}`)";
	}
	if($index['type'] == 'unique') {
		$piece .= "UNIQUE `{$index['name']}` (`{$fields}`)";
	}
	if($index['type'] == 'primary') {
		$piece .= "PRIMARY KEY (`{$fields}`)";
	}
	if($value['type'] == 'FULLTEXT') {
			$$piece .= "FULLTEXT KEY `{$index['name']}` (`{$fields[0]}`)";
	}
	return $piece;
}

function _db_build_field_sql_ab($field) {
	if(!empty($field['length'])) {
		$length = "({$field['length']})";
	} else {
		$length = '';
	}

	$signed  = empty($field['signed']) ? ' unsigned' : '';
	if(empty($field['null'])) {
		$null = ' NOT NULL';
	} else {
		$null = '';
	}
	if(isset($field['default'])) {
		$default = " DEFAULT '" . $field['default'] . "'";
	} else {
		$default = '';
	}
	if($field['increment']) {
		$increment = ' AUTO_INCREMENT';
	} else {
		$increment = '';
	}
	return "{$field['type']}{$length}{$signed}{$null}{$default}{$increment}";
}

?>
