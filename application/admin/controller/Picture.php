<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 8:29
 */
namespace app\admin\controller;
use think\Db;
class Picture extends Common{
    public function picture_list(){
        $total = Db::name('web_img')->count();
        $this->assign('Total',$total);
        return $this->fetch();
    }
    public function picture_list_ajax(){
    	$data = Db::name('web_img')->order(['type','sort'=>'desc','id'])->select();
    	return $data;
    }
    public function picture_show(){
        return $this->fetch();
    }
    public function picture_add(){
        return $this->fetch();
    }
    public function picture_edit(){
        $data = Db::table('web_img')->where('id',input('id'))->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function picture_del(){
        $id = input('post.id');
        $res = Db::table('web_img')->delete($id);
        return json_encode($res);
    }
    //添加图片信息
    public function picture_add_image(){
        $data['details'] = input('post.details');
        $data['type'] = input('post.type');
        $data['sort'] = input('post.sort');
        $data['status'] = input('post.status');
        if(empty($_POST['imagepath'])){
            $data['']='';
        }else{
            $data['imagepath']=implode(',', $_POST['imagepath']);
        }
        $data['time'] = date('Y-m-d H:i:s',time());;
        $res = Db::table('web_img')->insert($data);
        if($res){
            return $this->success('新增图片成功', 'picture_list','',2);
        }else{
            return $this->error('新增图片失败','picture_list','',2);
        }
    }
    //修改图片信息
    public function picture_edit_image(){
        $data['id'] = input('post.id');
        $data['details'] = input('post.details');
        $data['type'] = input('post.type');
        $data['sort'] = input('post.sort');
        $data['status'] = input('post.status');
        if(empty($_POST['imagepath'])){
            $data['imagepath']='';
        }else{
            $data['imagepath']=implode(',', $_POST['imagepath']);
        }
        $data['time'] = date('Y-m-d H:i:s',time());;
        $res = Db::table('web_img')->where('id',$data['id'])->update($data);
        if($res){
            return $this->success('修改图片成功', 'picture_list','',2);
        }else{
            return $this->error('修改图片失败','picture_list','',2);
        }
    }
    //图片状态修改
    public function picture_state(){
    	if(request()->isPost()){
    		$data = input('post.');
    		if($data['status'] == 0){
    			$res = Db::table('web_img')->where('id',$data['id'])->update(['status'=>1]);
    		}else{
    			$res = Db::table('web_img')->where('id',$data['id'])->update(['status'=>0]);
    		}
    	}
    	return json_encode($res);
    }
    //fileinput bootstrop  多图上传
    public function picture_add_images(){
        $files = request()->file('files');
        return $this->add_images($files);
    }
    //fileinput bootstrop  删除图片
    public function picture_del_images(){
        $id = $_GET['id'];
        return $this->del_img($id);
    }
}
    