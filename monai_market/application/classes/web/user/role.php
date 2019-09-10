<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Web_User_Role extends Web_Base
{

	public function index()
	{
        global $_W;
        global $_GPC;
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $condition = ' and uniacid = :uniacid and deleted=0';
        $params = array(':uniacid' => $_W['uniacid']);
        if (!(empty($_GPC['keyword'])))
        {
            $keyword = trim($_GPC['keyword']);
            $condition .= ' and rolename like :keyword';
            $params[':keyword'] = '%' . $keyword . '%';
        }
        if ($_GPC['status'] != '')
        {
            $status = intval($_GPC['status']);
            $condition .= ' and status=' . $status;
        }
        $list = pdo_fetchall('SELECT *  FROM ' . tablename(MODEL_NAME.'_perm_role') . ' WHERE 1 ' . $condition . ' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
        foreach ($list as &$row )
        {
            $row['usercount'] = pdo_fetchcolumn('select count(*) from ' . tablename(MODEL_NAME.'_perm_user') . ' where roleid=:roleid limit 1', array(':roleid' => $row['id']));
        }
        unset($row);
        $total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename(MODEL_NAME.'_perm_role') . '  WHERE 1 ' . $condition . ' ', $params);
        $pager = pagination2($total, $pindex, $psize);
        include $this->template();
	}

    // 用户信息
    public function info(){
        global $_W;
        $user = user_single($_W['uid']);
        $user['last_visit'] = date('Y-m-d H:i:s', $user['lastvisit']);
        $user['joindate'] = date('Y-m-d H:i:s', $user['joindate']);

        $profile = pdo_get('users_profile', array('uid' => $_W['uid']));

        $profile = user_detail_formate($profile);
        //dump($profile);die;
        include $this->template();
    }
    // 添加角色
    protected function post()
    {
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $item = pdo_fetch('SELECT * FROM ' . tablename(MODEL_NAME.'_perm_role') . ' WHERE id =:id and deleted=0 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
        $perms = Permission::instance()->formatPerms();
        $role_perms = array();
        $user_perms = array();
        if (!(empty($item)))
        {
            $role_perms = explode(',', $item['perms2']);
        }
        $user_perms = explode(',', $item['perms2']);
        if ($_W['ispost'])
        {
            $data = array(
                'uniacid' => $_W['uniacid'], 
                'rolename' => trim($_GPC['rolename']), 
                'status' => intval($_GPC['status']), 
                'perms2' => (is_array($_GPC['perms']) ? implode(',', $_GPC['perms']) : '')
            );
            if (!(empty($id)))
            {
                pdo_update(MODEL_NAME.'_perm_role', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
              //  plog('perm.role.edit', '修改角色 ID: ' . $id);
              $this->success('修改成功','user.role.index');
            }
            else
            {
                pdo_insert(MODEL_NAME.'_perm_role', $data);
                $id = pdo_insertid();
                //plog('perm.role.add', '添加角色 ID: ' . $id . ' ');
                $this->success('添加成功','user.role.index');
            }
        }
        include $this->template();
    }
    public function  add(){
        $this->post();
    }
    public function  edit(){
        $this->post();
    }
    public function  delete(){
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        if (empty($id))
        {
            $this->success('参数错误','',2);
        }
        pdo_delete(MODEL_NAME.'_perm_role', array('id' => $id, 'uniacid' => $_W['uniacid']));
        $this->success('删除成功','',2);
    }
    public function  edit_status(){
        global $_W;
        global $_GPC;
        $id = intval($_GPC['id']);
        $status = pdo_getcolumn(MODEL_NAME.'_perm_role', array('id' => $id, 'uniacid' => $_W['uniacid']),'status');
        $data = array(
            'status' => $status==1?0:1,
        );
        pdo_update(MODEL_NAME.'_perm_role', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
        echo 1;
    }

    /**
     * 获取角色权限
     */
    public function query()
    {
        global $_GPC;
        global $_W;
        $id= trim($_GPC['id']);
        $params = array();
        $params[':uniacid'] = $_W['uniacid'];
        $condition = ' and uniacid=:uniacid and deleted=0';
        if (!(empty($id)))
        {
            $condition .= ' AND `id` = :id';
            $params[':id'] = $id;
        }
        $ds = pdo_fetch('SELECT id,rolename,perms2 FROM ' . tablename(MODEL_NAME.'_perm_role') . ' WHERE status=1 ' . $condition , $params);
        echo json_encode($ds);
        exit();
    }
}
?>