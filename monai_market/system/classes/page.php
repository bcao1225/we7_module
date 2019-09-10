<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Page extends WeModuleSite
{

	public function template($filename = '', $type = TEMPLATE_INCLUDEPATH, $account = false)
	{
		global $_W;
		global $_GPC;
		if (empty($filename)) {
			$filename = str_replace('.', '/', $_W['routes']);
		}

		if (($_GPC['do'] == 'web') || defined('IN_SYS')) {
			$filename = str_replace('/add', '/post', $filename);
			$filename = str_replace('/edit', '/post', $filename);
			$filename_default = str_replace('/add', '/post', $filename);
			$filename_default = str_replace('/edit', '/post', $filename_default);
			$filename = 'web/' . $filename_default;
		}
		$name = MODEL_NAME;
		$moduleroot = IA_ROOT . '/addons/'.MODEL_NAME;
		if (defined('IN_SYS')) {
			$compile = IA_ROOT . '/data/tpl/web/' . $_W['template'] . '/' . $name . '/' . $filename . '.tpl.php';
			$source = $moduleroot . '/template/' . $filename . '.html';
			if (!is_file($source)) {
                $compile = IA_ROOT . '/data/tpl/web/' . $_W['template'] . '/' . $name . '/web/index.tpl.php';
				$source = $moduleroot . '/template/' . $filename . '/index.html';
			}
            //echo '<br>'.$compile;die;

        }
		if (!is_file($source)) {
			exit('Error: template source \'' . $filename . '\' is not exist!');
		}
		if (DEVELOPMENT || !is_file($compile) || (filemtime($compile) < filemtime($source))) {
			template_compile($source, $compile, true);
		}
		return $compile;
	}
    /*
	 * 成功返回
	 * 接收值
	 * $msg 提示
	 * $url 跳转url 不跳转不填
	 * $type 跳转类型 0无跳转 1跳转页面 2刷新页面 3后退一步
	 */
    public function success($msg, $url='',$type=1)
    {
        echo json_encode(array('msg'=>$msg,'url'=>webUrl($url),'type'=>$type,'state'=>'success'));
        exit;
    }
    /*
      * 成功返回
      * 接收值
      * $msg 提示
      * $url 跳转url 不跳转不填
      * $type 跳转类型 0无跳转 1跳转页面 2刷新页面 3后退一步
      */
    public function tomessage($message, $url='')
    {
        global $_W;
        if($_W['ispost'])
        {
            $this->error('你没有相应的权限操作','',0);
        }
        else
        {
            include $this->template('message');
            exit;
        }
        
    }
    /*
     * 失败返回
     * 接收值
     * $msg 提示
     * $url 跳转url 不跳转不填
     * $type 跳转类型 0无跳转 1跳转页面 2刷新页面 3后退一步
     * 返回值
     * state 0 失败 1成功
     */
    public function error($msg, $url='',$type=1)
    {
        echo json_encode(array('msg'=>$msg,'url'=>webUrl($url),'type'=>$type,'state'=>'danger'));
        exit;
    }
}

?>
