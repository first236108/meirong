<?php

namespace app\admin\controller;

use think\Db;
use Qiniu\Auth;

class Index extends Base
{
    public function index()
    {
        //dump([session('admin'),session('auth'),session('adminGroup')]);
        $this->assign('show_small_chat');
        return view();
    }

    public function systemInfo()
    {
        if ($this->request->isPost()) {
            $input = input('post.');
            $data  = [];
            foreach ($input as $key => $value) {
                $data['name']  = $key;
                $data['value'] = $value;
            }
            Db::name('config')->cache('config')->update($data);

        }
        $config = Db::name('config')->column('value', 'name');
        $this->assign('config', $config);
        return view();
    }

    public function adminList()
    {
        if ($this->request->isPost()) {
            $list  = Db::name('admin a')
                ->join('__AUTH_GROUP_ACCESS__ b', 'a.id=b.uid')
                ->join('__AUTH_GROUP__ c', 'b.group_id=c.id')
                ->field("a.id,a.name,a.nickname,a.create_time,a.status,c.title,c.id as group_id")
                ->where('is_delete', 0)
                ->select();
            $group = Db::name('auth_group')->column('title', 'id');

            return ['succ' => 0, 'data' => ['list' => $list, 'group' => $group]];
        }
        return view();
    }

    public function adminAddEdit()
    {
        $id = input('id/d', 0);
        if ($this->request->isPost()) {
            $data     = input('param.');
            $validate = new \app\admin\validate\Admin();
            $result   = $validate->check($data);
            if ($result !== true) {
                return ['succ' => 1, 'msg' => $validate->getError()];
            }

            if ($data['id'] > 0) {
                if (!$data['password'])
                    unset($data['password']);
                else {
                    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                }
                $flag        = Db::name('admin')->field('name,nickname,password,phone,email')->update($data);
                $flag2       = Db::name('auth_group_access')->update(['uid' => $data['id'], 'group_id' => $data['group_id']]);
                $res['succ'] = ($flag || $flag2) ? 0 : 1;
                $res['msg']  = $res['succ'] ? '修改失败' : '修改完成';
            } else {
                if (!$data['password'])
                    $data['password'] = '123456';
                $data['password']    = password_hash($data['password'], PASSWORD_BCRYPT);
                $data['create_time'] = time();
                $flag                = Db::name('admin')->field('group_id', true)->insertGetId($data);
                $flag2               = Db::name('auth_group_access')->insert(['uid' => $flag, 'group_id' => $data['group_id']]);
                $res['succ']         = ($flag && $flag2) ? 0 : 1;
                $res['msg']          = $res['succ'] ? '添加失败' : '添加完成';
            }
            return $res;
        }

        $info['user'] = Db::name('admin a')->join('auth_group_access b', 'a.id=b.uid')->where('a.id', $id)->find();
        return ['succ' => 0, 'info' => $info];
    }

    public function adminDel()
    {
        $id = input('post.id/d', 0);
        if ($id == session('admin.id'))
            return ['succ' => 1, 'msg' => '不能删除自已哦...'];
        $flag = Db::name('admin')->where('id', $id)->setField('is_delete', 1);

        return ['succ' => !$flag, 'msg' => $flag ? '已删除' : '删除失败'];
    }

    public function group()
    {
        if ($this->request->isPost()) {
            $list = Db::name('auth_group')->select();
            $rule = Db::name('auth_rule')->field('id,title,cate')->select();
            $rule = $this->fixRuleCate($rule);
            return ['succ' => 0, 'data' => $list, 'rule' => $rule];
        }

        return view();
    }

    public function groupAddEdit()
    {
        $id = input('id/d', 0);
        if ($this->request->isPost()) {
            $data     = input('post.');
            $validate = new \app\admin\validate\Group();
            $result   = $validate->check($data);
            if ($result !== true) {
                return ['succ' => 1, 'msg' => $validate->getError()];
            }

            $data['rules'] = implode(',', $data['rules']);
            if ($id > 0)
                $flag = Db::name('auth_group')->update($data);
            else
                $flag = Db::name('auth_group')->insert($data);
            $res['succ'] = $flag ? 0 : 1;
            $res['msg']  = $res['succ'] ? '操作失败' : '操作完成';
            return $res;
        }

        $info = Db::name('auth_group')->find($id);
        $info && $info['rules'] = explode(',', $info['rules']);
        $rule = Db::name('auth_rule')->field('id,title,cate')->select();
        if ($info)
            foreach ($rule as $index => $item) {
                $rule[$index]['enable'] = in_array($item['id'], $info['rules']);
            }
        $rule = $this->fixRuleCate($rule);
        return ['succ' => 0, 'info' => ['group' => $info, 'rule' => $rule]];
    }

    public function groupDel()
    {
        $id    = input('post.id/d', 0);
        $count = Db::name('auth_group_access')->where('group_id', $id)->count();
        if ($count)
            return ['succ' => 1, 'msg' => '该角色有所属管理员，请确认！'];
        $flag = Db::name('auth_group')->where('id', $id)->delete();
        return ['succ' => !$flag, 'msg' => $flag ? '已删除' : '删除失败'];
    }

    public function rule()
    {
        if (input('?post.controller')) {
            $controller = input('post.controller');
            $actions    = get_class_methods('app\\admin\\controller\\' . $controller);
            $base       = get_class_methods('app\\admin\\controller\\Base');
            return json(array_diff($actions, $base));
        }

        if ($this->request->isPost()) {
            $planList = [];
            $planPath = env('app_path') . 'admin/controller';
            $dirRes   = opendir($planPath);
            if ($dirRes) {
                while ($dir = readdir($dirRes)) {
                    if (!in_array($dir, ['.', '..', '.svn', 'git', '.idea'])) {
                        $planList[] = basename($dir, '.php');
                    }
                }
            }
            $planList = array_diff($planList, ['Login', 'Base','Ueditor']);

            $list = Db::name('auth_rule')
                ->field('id,name,title,CASE cate WHEN 1 THEN "系统设置" WHEN 2 THEN "运营管理" ELSE "内容维护" END AS cate')
                ->select();
            return ['succ' => 0, 'data' => $list, 'planList' => array_reverse($planList)];
        }

        return view();
    }

    public function ruleAddEdit()
    {
        $id = input('id/d', 0);
        if ($this->request->isPost()) {
            $data   = input('param.');
            $result = $this->validate($data, [
                'title' => 'require',
                'name'  => 'require',
                'cate'  => 'require|gt:0'
            ], [
                'title' => '权限名称要填写哦...',
                'name'  => '权限路径必填...',
                'cate'  => '请选择权限分类...'
            ]);
            if ($result !== true)
                return json(['succ' => 1, 'msg' => $result]);

            if ($id > 0)
                $flag = Db::name('auth_rule')->update($data);
            else
                $flag = Db::name('auth_rule')->insert($data);

            $res['succ'] = $flag ? 0 : 1;
            $res['msg']  = $res['succ'] ? '操作失败' : '操作完成';
            return $res;
        }

        $info = Db::name('auth_rule')->find($id);
        return json(['succ' => 0, 'info' => $info]);
    }

    public function ruleDel()
    {
        $id   = input('post.id/d', 0);
        $flag = Db::name('auth_rule')->where('id', $id)->delete();
        return ['succ' => !$flag, 'msg' => $flag ? '已删除' : '删除失败'];
    }

    public function password()
    {
        if ($this->request->isPost()) {
            $data   = input('post.');
            $result = $this->validate($data, [
                'password' => 'require|alphaNum|min:6',
                'confirm'  => 'require|confirm:password'
            ], [
                'password.require'  => '密码必须填写哦',
                'password.alphaNum' => '密码只能是字母或数字',
                'password.min'      => '密码至少6位哦...',
                'confirm.require'   => '确认密码必填',
                'confirm.confirm'   => '两次输入密码不一致',
            ]);
            if ($result !== true) {
                return ['succ' => 1, 'msg' => $result];
            }

            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

            Db::name('admin')->where('id', session('admin.id'))->update(['password' => $data['password']]);
            return ['succ' => 0, 'msg' => '密码已更新...'];
        }
        return view();
    }

    private function fixRuleCate($rule)
    {
        $arr = [];
        foreach ($rule as $index => $item) {
            switch ($item['cate']) {
                case 1:
                    $arr['系统设置'][] = $item;
                    break;
                case 2:
                    $arr['运营管理'][] = $item;
                    break;
                default:
                    $arr['内容维护'][] = $item;
            }
        }
        return $arr;
    }

    public function changeTableVal()
    {
        $table    = input('table', '');
        $id_name  = input('id_name', '');
        $id_value = input('id_value', 0);
        $field    = input('field', '');
        $value    = input('value', 0);
        if (!$table || !$id_name || !$id_value || !$field)
            return null;
        $flag = Db::name($table)->where($id_name, 'in', $id_value)->setField($field, $value);
        return $flag ? $flag : null;
    }
}
