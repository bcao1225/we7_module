<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Web_User_User extends Web_Base
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$status = $_GPC['status'];
		$condition = ' and u.uniacid = :uniacid and u.deleted=0 and u.uid<>' . $_W['uid'];
		$params = array(':uniacid' => $_W['uniacid']);
		if (!(empty($_GPC['keyword']))) 
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and ( u.realname like :keyword or u.username like :keyword or u.mobile like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		if ($_GPC['roleid'] != '') 
		{
			$condition .= ' and u.roleid=' . intval($_GPC['roleid']);
		}
		if ($_GPC['status'] != '') 
		{
			$condition .= ' and u.status=' . intval($_GPC['status']);
		}
		//echo $condition;die;
		$list = pdo_fetchall('SELECT u.*,r.rolename FROM ' . tablename(MODEL_NAME.'_perm_user') . ' u  ' . ' left join ' . tablename(MODEL_NAME.'_perm_role') . ' r on u.roleid =r.id  ' . ' WHERE 1 ' . $condition . ' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename(MODEL_NAME.'_perm_user') . ' u  ' . ' left join ' . tablename(MODEL_NAME.'_perm_role') . ' r on u.roleid =r.id  ' . ' WHERE 1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		$roles = pdo_fetchall('select id,rolename from ' . tablename(MODEL_NAME.'_perm_role') . ' where uniacid=:uniacid and deleted=0', array(':uniacid' => $_W['uniacid']));
		include $this->template();
	}
	public function add() 
	{
		$this->post();
	}
	public function edit() 
	{
		$this->post();
	}
	protected function post() 
	{
		global $_W;
		global $_GPC;
		load()->model('user');
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT * FROM ' . tablename(MODEL_NAME.'_perm_user') . ' WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		$perms = Permission::instance()->formatPerms();
		$user_perms = array();
		$role_perms = array();
		if (!(empty($item))) 
		{
			if ($item['uid'] == $_W['uid']) 
			{
				$this->message('无法修改自己的权限！', referer(), 'error');
			}
			$role = pdo_fetch('SELECT * FROM ' . tablename(MODEL_NAME.'_perm_role') . ' WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $item['roleid']));
			if (!(empty($role))) 
			{
				$role_perms = explode(',', $role['perms2']);
			}
			$user_perms = explode(',', $item['perms2']);
		}
		if ($_W['ispost']) 
		{
			$data = array('uniacid' => $_W['uniacid'], 'username' => trim($_GPC['username']), 'realname' => trim($_GPC['realname']), 'mobile' => trim($_GPC['mobile']), 'roleid' => intval($_GPC['roleid']), 'status' => intval($_GPC['status']), 'perms2' => (is_array($_GPC['perms']) ? implode(',', $_GPC['perms']) : ''), 'openid' => trim($_GPC['openid']));
			if (!(empty($_GPC['password']))) 
			{
				$password = trim($_GPC['password']);
				if (strlen($password) < 8) 
				{
                    $this->success('密码长度至少8位','','0');
				}
				$score = 0;
				if (preg_match('/[0-9]+/', $password)) 
				{
					++$score;
				}
				if (preg_match('/[a-z]+/', $password)) 
				{
					++$score;
				}
				if (preg_match('/[A-Z]+/', $password)) 
				{
					++$score;
				}
				if (preg_match('/[_|\\-|+|=|*|!|@|#|$|%|^|&|(|)]+/', $password)) 
				{
					++$score;
				}
				if ($score < 2) 
				{
                    $this->success('密码必须包含大小写字母、数字、标点符号的其中两项','','0');
				}
			}
			if (!(empty($item['id']))) 
			{
				$user = user_single(array('username' => $item['username']));
				$data['uid'] = $user['uid'];
				if (!(empty($_GPC['password']))) 
				{
					$data['password'] = $user['password'];
				}
				user_update(array('uid' => $item['uid'], 'password' => $_GPC['password'], 'salt' => $user['salt']));
				pdo_update(MODEL_NAME.'_perm_user', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
				//plog('perm.user.edit', '编辑操作员 ID: ' . $id . ' 用户名: ' . $data['username'] . ' ');
			}
			else 
			{
				if (user_check(array('username' => $data['username']))) 
				{
					if (!(user_check(array('username' => $data['username'], 'password' => $_GPC['password'])))) 
					{
                        $this->success('此用户为系统存在用户，但是您输入的密码不正确，无法添加','','0');
					}
					$user = user_single(array('username' => $item['username']));
					$data['uid'] = $user['uid'];
					$data['password'] = $user['password'];
				}
				else 
				{
					$data['uid'] = user_register(array('username' => $data['username'], 'password' => $_GPC['password']));
					pdo_insert('uni_account_users', array('uid' => $data['uid'], 'uniacid' => $data['uniacid'], 'role' => 'operator'));
				}
				pdo_insert(MODEL_NAME.'_perm_user', $data);
				//$id = pdo_insertid();
				//plog('perm.user.add', '添加操作员 ID: ' . $id . ' 用户名: ' . $data['username'] . ' ');
			}
            $this->success('添加成功','user.user.index');
		}
        $roles = pdo_fetchall('select id,rolename from ' . tablename(MODEL_NAME.'_perm_role') . ' where uniacid=:uniacid and deleted=0', array(':uniacid' => $_W['uniacid']));
		include $this->template();
	}
	public function delete() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
        if (empty($id))
        {
            $this->success('参数错误','',2);
        }
		$items = pdo_fetchall('SELECT id,username FROM ' . tablename(MODEL_NAME.'_perm_user') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item ) 
		{
			pdo_delete(MODEL_NAME.'_perm_user', array('id' => $item['id']));
			//plog('perm.user.delete', '删除操作员 ID: ' . $item['id'] . ' 操作员名称: ' . $item['username'] . ' ');
		}
        $this->success('删除成功', referer(),2);
	}

	public function status()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) 
		{
			$id = ((is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0));
		}
		$status = intval($_GPC['status']);
		$items = pdo_fetchall('SELECT id,username FROM ' . tablename(MODEL_NAME.'_perm_user') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item ) 
		{
            $user = pdo_get(MODEL_NAME.'_perm_user', array('id' => $item['id']));
            $status = ($user['status'] == 1) ? 0 : 1;
			pdo_update(MODEL_NAME.'_perm_user', array('status' => $status), array('id' => $item['id']));
			//plog('perm.user.edit', '修改操作员状态 ID: ' . $item['id'] . ' 操作员名称: ' . $item['username'] . ' 状态: ' . (($status == 0 ? '禁用' : '启用')));
		}
		show_json(1, array('url' => referer()));
	}
	
}
?>