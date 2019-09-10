<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}
define('IN_GW', true);
define('MODEL_NAME','monai_market');
require_once IA_ROOT . '/addons/'.MODEL_NAME.'/application/bootstrap.php';
class Monai_marketModuleSite extends WeModuleSite
{

    public function doWebWeb()
    {
        Application::run();
    }

    /**
     * 独立187登录
     */
    public function doMobileLogin()
    {
        global $_W,$_GPC;

        load()->model('user');
        load()->model('message');
        load()->classs('oauth2/oauth2client');
        load()->model('setting');

        $setting = $_W['setting'];
        $_GPC['login_type'] = !empty($_GPC['login_type']) ? $_GPC['login_type'] : (!empty($_W['setting']['copyright']['login_type']) ? 'mobile': 'system');
        $login_urls = user_support_urls();
        if (checksubmit() || $_W['isajax']) {
            $this->_login('');
        }
        include $this->template('index');
    }

    /**
     * 退出
     * @param string $forward
     */
    public function  doMobileLogout(){
        global $_GPC, $_W;
        isetcookie('__session', '', -10000);
        isetcookie('__switch', '', -10000);
        $forward = "/app/index.php?c=entry&do=login&m=monai_market&i={$_GPC['i']}";
        header('location: ' . $forward);
    }

    public function _login($forward=''){
        global $_GPC, $_W;
        if (empty($_GPC['login_type'])) {
            $_GPC['login_type'] = 'system';
        }

        if (empty($_GPC['handle_type'])) {
            $_GPC['handle_type'] = 'login';
        }


        if ($_GPC['handle_type'] == 'login') {
            $member = OAuth2Client::create($_GPC['login_type'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appid'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appsecret'])->login();
        } else {
            $member = OAuth2Client::create($_GPC['login_type'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appid'], $_W['setting']['thirdlogin'][$_GPC['login_type']]['appsecret'])->bind();
        }
        if (is_error($member)) {
            itoast($member['message'], url('user/login'), '');
        }
        $record = user_single($member);
        if (!empty($record)) {
            if ($record['status'] == USER_STATUS_CHECK || $record['status'] == USER_STATUS_BAN) {
                itoast('您的账号正在审核或是已经被系统禁止，请联系网站管理员解决?', url('user/login'), '');
            }
            $_W['uid'] = $record['uid'];
            $_W['isfounder'] = user_is_founder($record['uid']);
            $_W['user'] = $record;
            if (empty($_W['isfounder'])) {
                if (!empty($record['endtime']) && $record['endtime'] < TIMESTAMP) {
                    itoast('您的账号有效期限已过，请联系网站管理员解决！', '', '');
                }
            }
            if (!empty($_W['siteclose']) && empty($_W['isfounder'])) {
                itoast('站点已关闭，关闭原因:'. $_W['setting']['copyright']['reason'], '', '');
            }
            $cookie = array();
            $cookie['uid'] = $record['uid'];
            $cookie['lastvisit'] = $record['lastvisit'];
            $cookie['lastip'] = $record['lastip'];
            $cookie['hash'] = md5($record['password'] . $record['salt']);
            $session = authcode(json_encode($cookie), 'encode');
            isetcookie('__session', $session, !empty($_GPC['rember']) ? 7 * 86400 : 0, true);
            $status = array();
            $status['uid'] = $record['uid'];
            $status['lastvisit'] = TIMESTAMP;
            $status['lastip'] = CLIENT_IP;
            user_update($status);

            $forward = "/web/index.php?c=account&a=display&do=switch&module=monai_market&uniacid={$_GPC['i']}";

            if ($record['uid'] != $_GPC['__uid']) {
                isetcookie('__uniacid', '', -7 * 86400);
                isetcookie('__uid', '', -7 * 86400);
            }
            $failed = pdo_get('users_failed_login', array('username' => trim($_GPC['username']), 'ip' => CLIENT_IP));
            pdo_delete('users_failed_login', array('id' => $failed['id']));
            header('location: ' . $forward);
        } else {
            if (empty($failed)) {
                pdo_insert('users_failed_login', array('ip' => CLIENT_IP, 'username' => trim($_GPC['username']), 'count' => '1', 'lastupdate' => TIMESTAMP));
            } else {
                pdo_update('users_failed_login', array('count' => $failed['count'] + 1, 'lastupdate' => TIMESTAMP), array('id' => $failed['id']));
            }
            $forward = "/app/index.php?c=entry&do=login&m=monai_market&i={$_GPC['i']}&message=true";
            header('location: ' . $forward);
        }
    }
}
?>