<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/5/005
 * Time: 15:10
 */
namespace app\admin\controller;

use think\Controller;
use \org\Auth;
use think\Request;
use think\Db;
class Common extends Controller
{
    //当任何函数加载时候  会调用此函数
    public function _initialize(){//默认的方法  会自动执行 特征有点像构造方法
        $request = Request::instance();
        $uid = session('user')['id'];
        if(empty($uid)){
            echo '<script>alert("没有登陆");location.href="'.url('admin/login/index').'"</script>';
        }

        $AUTH = new Auth();
        // MODULE_NAME(index).'/'.CONTROLLER_NAME(index).'/'.ACTION_NAME(index)==index/index/index
//        if(!$AUTH->check($request->module().'/'.$request->controller().'/'.$request->action(), session('user')['id'])){
//        if(!$AUTH->check($request->controller().'/'.$request->action(), session('user')['id'])){
////            echo '<script>location.href="'.url('admin/login/check_error').'"</script>';
//            $this->error("没有权限！",url('/admin/index/welcome'),2);
//        }
    }
    //分类列表获取
    protected function get_category_list(){
        header('content-type:text/html;charset=utf-8');
        $categoryArr=Db::name('goods_type')->order(['sort'=>'desc','id'])->select();
        //调用递归函数
        return getone($categoryArr);
//        return $categoryArr;
    }
    //fileinput bootstrop  多图上传
    public function add_images($files){
        // $files = request()->file('files');
        // 移动到框架应用根目录/public/static/upload 目录下
        if($files){
            $item  = [];
            foreach($files as $k => $file){
                $info = $file->move(ROOT_PATH.'/public/static/upload');
                if($info){
                    $data['filepath'] = str_replace(ROOT_PATH.'/public', '', $info->getPathname());
                    $result = Db::name('goods_files')->insertGetId($data);
                    if($result){
                        $item = ['id'=>$result,'imagepath'=>$data['filepath']];
                    }else{
                        $item = '';
                    }
                }else{
                    $this->error($file->getError());
                }
            }
            return json_encode($item);

        }  
    } 
    //fileinput bootstrop删除图片
    public function del_img($id){
        $result=Db::table('goods_files')->delete($id);
        if($result){
            echo 1;
        }else{
            echo 0;
        }
    }
    public function read_dir($dir){
        $files = array();
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if($file != '..' && $file != '.'){
                if(is_dir($dir.'/'.$file)){
                    $files[$file] = $this->read_dir($dir.'/'.$file);
                }else{
                    $files[] = $file;
                }
            }
        }
        closedir($handle);
        return $files;
    }

}
