<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/5/005
 * Time: 13:36
 */
namespace app\admin\controller;
use think\Controller;
use \org\Auth;
use think\Db;
use \app\admin\model\AdminUser;
use \app\admin\model\AuthGroup;
use think\Validate;
use think\Request;
class Userroot extends Common
{
    public function admin_add()
    {
        $group = Db::table('auth_group')->where('status','1')->select();
        $this->assign('group',$group);
        return $this->fetch();
    }
    public function admin_edit()
    {
        $group = Db::table('auth_group')->where('status','1')->select();
        $this->assign('group',$group);
        $data = Db::table('admin_user')->where('id',input('get.id'))->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function admin_list()
    {
        return $this->fetch();
    }
    public function admin_list_ajax(){
        $pageNumber = input('post.pageNumber');
        $pageSize = input('post.pageSize');
        $search_text = input('post.search_text');
        $datemin = input('post.datemin');
        $datemax = input('post.datemax');
        if($datemax == ''){
            $datemax = date('Y-m-d H:i:s',time());
        }
        $where["admin_name"] = ['like','%'.$search_text.'%'];
        if($datemin != '' || $datemax != ''){
            $where['create_date'] = array('between',array($datemin,$datemax));
        }
        $data = Db::table('admin_user')->where($where)->order('id asc')->page($pageNumber,$pageSize)->select();
        foreach ($data as &$value){
            $group_title = Db::table('auth_group')->where('id='.$value['role'])->field('title')->find();
            $value['title'] = $group_title['title'];
        }
        $Total = Db::table('admin_user')->where($where)->count();
        return array('total'=>$Total,'rows'=>$data);
    }
    public function admin_permission()
    {
        return $this->fetch();
    }
    public function admin_role()
    {
        $group = Db::table('auth_group')
            ->alias('g')
            ->join('admin_user u',"g.html_rules like concat('%,',u.role,',%')")
            ->join('auth_rule r',"g.html_rules like concat('%,',r.id,',%')")
//            ->field('g.*,group_concat(u.admin_name) as name_list')
            ->field('group_concat(distinct u.admin_name) as name_list,group_concat(distinct r.title) as r_title,g.*')   //group_concat合并    distinct去重
            ->group('g.id')
            ->select();
        $Total = Db::name('auth_group')->count();
        $this->assign('Total',$Total);
        $this->assign('group',$group);
        return $this->fetch();
    }
    public function admin_role_add()
    {
        //管理员管理
        $Userroot_rule = Db::name('auth_rule')->where('rule_group=1 and pid=0' )->select();
        $User2 = array();
        foreach ($Userroot_rule as $key=>$val){
            $Userroot_rule[$key]['child'] = array();
            $User2 =  Db::table('auth_rule')->where('rule_group=1 and pid='.$val['id'])->select();
            foreach ($User2 as $ke=>$va){
                array_push($Userroot_rule[$key]['child'],$va);
            }
        }
        $this->assign('Userroot_rule',$Userroot_rule);
        //商品管理
        $Goods_rule = Db::name('auth_rule')->where('rule_group=2 and pid=0' )->select();
        $Goods2 = array();
        foreach ($Goods_rule as $key=>$val){
            $Goods_rule[$key]['child'] = array();
            $Goods2 =  Db::table('auth_rule')->where('rule_group=2 and pid='.$val['id'])->select();
            foreach ($Goods2 as $ke=>$va){
                array_push($Goods_rule[$key]['child'],$va);
            }
        }
        $this->assign('Goods_rule',$Goods_rule);
        return $this->fetch();
    }
    public function admin_role_edit()
    {
        //数据库信息
        $data = Db::table('auth_group')->where('id',input('get.id'))->find();
        $rules_arr = explode(',',$data['rules']);
        $this->assign('data',$data);
        //管理员管理
        $Userroot_rule = Db::name('auth_rule')->where('rule_group=1 and pid=0' )->select();
        foreach ($Userroot_rule as $key=>$val){
            $Userroot_rule[$key]['checked']=get_array_repeats($rules_arr,$val['id']);
        }
        $User2 = array();
        foreach ($Userroot_rule as $key=>$val){
            $Userroot_rule[$key]['child'] = array();
            $User2 =  Db::table('auth_rule')->where('rule_group=1 and pid='.$val['id'])->select();
            foreach ($User2 as $ke=>$va){
                $User2[$key]['checked']=get_array_repeats($rules_arr,$va['id']);
            }
            foreach ($User2 as $k=>$v){
                array_push($Userroot_rule[$key]['child'],$v);
            }
        }
        $this->assign('Userroot_rule',$Userroot_rule);
        //商品管理
        $Goods_rule = Db::name('auth_rule')->where('rule_group=2 and pid=0' )->select();
        foreach ($Goods_rule as $key=>$val){
            $Goods_rule[$key]['checked']=get_array_repeats($rules_arr,$val['id']);
        }
        $Goods2 = array();
        foreach ($Goods_rule as $key=>$val){
            $Goods_rule[$key]['child'] = array();
            $Goods2 =  Db::table('auth_rule')->where('rule_group=2 and pid='.$val['id'])->select();
            foreach ($Goods2 as $ke=>$va){
                $Goods2[$key]['checked']=get_array_repeats($rules_arr,$va['id']);
            }
            foreach ($Goods2 as $ke=>$va){
                array_push($Goods_rule[$key]['child'],$va);
            }
        }
        $this->assign('Goods_rule',$Goods_rule);
        return $this->fetch();
    }


    //添加角色
    public function admin_role_add_ajax(){
        $data = array();
        $data['title'] = input('post.title');
        $data['descr'] = input('post.descr');
        $data['rules'] = implode(',',input('post.rules/a'));
//        return $data['rules'];
//        exit();
        $rule = [
            'title' => 'require|min:2|max:16',
            'descr' => 'max:200',
        ];
        $msg= [
            'title.require' => '角色名称不能为空！',
            'title.min' => '角色名称不能小于2位！',
            'title.max' => '角色名称不能大于16位！',
            'descr' => '描述不能大于200位！',
        ];
        $validate = new Validate($rule,$msg);
        if(!$validate->check($data)){
            return json_encode(array('msg'=>$validate->getError(),'success'=>false),JSON_UNESCAPED_UNICODE);
        }else{
            $auth_group = new AuthGroup();
            $res = $auth_group->operate_root($data);
            if($res == 'valid_title'){
                return json_encode(array('msg'=>'角色名称已存在！','success'=>false),JSON_UNESCAPED_UNICODE);
            }else{
                return json_encode(array('msg'=>$res,'success'=>true),JSON_UNESCAPED_UNICODE);
            }
        }
    }
    //编辑角色
    public function admin_role_edit_ajax(){
        $data = array();
        $data['id'] = input('post.id');
        $data['title'] = input('post.title');
        $data['descr'] = input('post.descr');
        $data['rules'] = implode(',',input('post.rules/a'));
        $rule = [
            'title' => 'require|min:2|max:16',
            'descr' => 'max:200',
        ];
        $msg= [
            'title.require' => '角色名称不能为空！',
            'title.min' => '角色名称不能小于2位！',
            'title.max' => '角色名称不能大于16位！',
            'descr' => '描述不能大于200位！',
        ];
        $validate = new Validate();
        if(!$validate->check($data)){
            return json_encode(array('msg'=>$validate->getError(),'success'=>false),JSON_UNESCAPED_UNICODE);
        }else{
            $auth_group = new AuthGroup();
            $where = ['id'=>$data['id']];
            $res = $auth_group->operate_root($data,$where);
            if($res == false){
                return json_encode(array('msg'=>'未修改任何数据！','success'=>false),JSON_UNESCAPED_UNICODE);
            }else{
                return json_encode(array('msg'=>$res,'success'=>true),JSON_UNESCAPED_UNICODE);
            }
        }
    }
    //删除角色
    public function admin_del_role(Request $request){
        if($request->isPost()){
            $data=input('post.');
            $auth_group = new AuthGroup();
            $res = $auth_group::destroy($data['id']);
            return json_encode($res);
        }
    }
    //修改管理员状态
    public function admin_edit_state(Request $request){
        if($request->isPost()){
            $data=input('post.');
            if($data['state'] == 0){
                $res = Db::table('admin_user')->where('id',$data['id'])->update(['state'=>1]);
            }else{
                $res = Db::table('admin_user')->where('id',$data['id'])->update(['state'=>0]);
            }
            return json_encode($res);
        }

    }
    //添加管理员
    public function admin_add_admin(){
        $data = array();
        $rule = [
            'admin_name'  => 'require|min:2|max:16',
            'admin_password'   => 'require|min:6',
            'password2'   => 'require|confirm:admin_password',
            'phone' => "require|regex:1[3-8]{1}[0-9]{9}",
            'email' =>  'require|email',
        ];
        $msg = [
            'admin_name.require'  =>  '用户名不能为空！',
            'admin_name.min'  =>  '用户名不得小于两位！',
            'admin_name.max'  =>  '用户名不得大于16位！',
            'admin_password.require'  =>  '密码不得为空！',
            'admin_password.min'  =>  '密码不得小于6位！',
            'password2'  =>  '确认密码必须与密码一致！',
            'phone.require'  =>  '手机号不得为空！',
            'phone.regex'  =>  '手机号格式有误！',
            'email.require' =>  '邮箱格式不得为空！',
            'email.email' =>  '邮箱格式有误！',
        ];
        $validate = new Validate($rule, $msg);
        $data['admin_name'] = input('post.admin_name');
        $data['admin_password'] = md5(input('post.admin_password'));
        $data['password2'] = md5(input('post.password2'));
        $data['phone'] = input('post.phone');
        $data['email'] = input('post.email');
        $data['role'] = input('post.role');
        $data['create_date'] = date('Y-m-d H:i:s',time());
        if(!$validate->check($data)){
            return json_encode(array('msg'=>$validate->getError(),'success'=>false),JSON_UNESCAPED_UNICODE);
        }else{
            unset($data['password2']);
            $admin_user = new AdminUser();
            $res = $admin_user->operate_user($data);
//            return json_encode(array('msg'=>$res,'success'=>true),JSON_UNESCAPED_UNICODE);
            if ($res ==  'valid_name'){
                return json_encode(array('msg'=>'用户名已存在！','success'=>false),JSON_UNESCAPED_UNICODE);
            }elseif($res ==  'valid_phone'){
                return json_encode(array('msg'=>'一个手机号只限注册三个！','success'=>false),JSON_UNESCAPED_UNICODE);
            }else{
                return json_encode(array('msg'=>$res,'success'=>true),JSON_UNESCAPED_UNICODE);
            }

        }
    }
    //修改管理员
    public function admin_edit_admin(){

        $data = array();
        $rule = [
            'admin_password'   => 'min:6',
            'password2'   => 'require|confirm:admin_password',
            'phone' => "require|regex:1[3-8]{1}[0-9]{9}",
            'email' =>  'require|email',
        ];
        $msg = [
            'admin_password.min'  =>  '密码不得小于6位！',
            'password2'  =>  '确认密码必须与密码一致！',
            'phone.require'  =>  '手机号不得为空！',
            'phone.regex'  =>  '手机号格式有误！',
            'email.require' =>  '邮箱格式不得为空！',
            'email.email' =>  '邮箱格式有误！',
        ];
        $validate = new Validate($rule, $msg);
        $data['id'] = input('post.id');
        $data['admin_name'] = input('post.admin_name');
        $data['admin_password'] = md5(input('post.admin_password'));
        $data['password2'] = md5(input('post.password2'));
        if (input('post.admin_password') == ''){
            $data['admin_password'] = input('post.pri_pass');
            $data['password2'] = input('post.pri_pass');
        }
        $data['phone'] = input('post.phone');
        $data['email'] = input('post.email');
        $data['role'] = input('post.role');
        if(!$validate->check($data)){
            return json_encode(array('msg'=>$validate->getError(),'success'=>false),JSON_UNESCAPED_UNICODE);
        }else{
            $admin_user = new AdminUser();
            $where = ['id'=> $data['id']];
            $res = $admin_user->operate_user($data,$where);
            if($res==false){
                return json_encode(array('msg'=>'未修改任何数据！','success'=>false),JSON_UNESCAPED_UNICODE);
            }elseif($res ==  "valid_phone"){
                return json_encode(array('msg'=>'一个手机号只限注册三个！','success'=>false),JSON_UNESCAPED_UNICODE);
            }else{
                return json_encode(array('msg'=>$res,'success'=>true),JSON_UNESCAPED_UNICODE);
            }

        }
    }
    //删除管理员
    public function admin_del_admin(Request $request){
        if($request->isPost()){
            $data=input('post.');
            $admin_user = new AdminUser();
            $res = $admin_user::destroy($data['id']);
            return json_encode($res);
        }
    }

    //管理员登录次数
    public function login_count(){
        $data = Db::name('admin_user')->field('admin_name,login_count,role')->select();
        return json_encode($data);
    }
}
