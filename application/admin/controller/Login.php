<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/5/005
 * Time: 14:39
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

class   Login   extends    Controller
{
    public function index(){
        return  $this->fetch();
    }

//
    public function dologin(){
        $data['verifyCode'] = input('post.verifyCode');
        if(!captcha_check($data['verifyCode'])) {
            // 校验失败
            $this->error('验证码不正确！');
        }
        $m=Db::table('admin_user');
        $data=$m->where("admin_name='".$_POST['admin_name']."' and admin_password='".md5($_POST['admin_password'])."'")->find();
        if($data){
            session('user',$data);
            return $this->success("登陆成功",url('/admin/index/index'));
        }else{
            return $this->error("用户名或密码有误！");
        }

    }
//
//    public function check_error(){
//        return $this->error("没有权限！",url('/admin/index/welcome'),2);
//    }


}
