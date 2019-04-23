<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/21
 * Time: 8:29
 */
namespace app\admin\controller;
use \app\admin\model\GoodsType;
use think\Db;

class System extends Common{
    public function system_category(){
        $get_category_list=$this->get_category_list();
        $this->assign('get_category_list',$get_category_list);
        return $this->fetch();
    }
    public function system_category_ajax(){
        $get_category=$this->get_category_list();
        return $get_category;
    }
    public function system_category_add(){
        $get_category_list=$this->get_category_list();
        $this->assign('get_category_list',$get_category_list);

        return $this->fetch();
    }
    public function system_category_edit(){
        $get_category_list=$this->get_category_list();
        $this->assign('get_category_list',$get_category_list);
        $Good_type = new GoodsType();
        $data = $Good_type->where('id',input('id'))->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    //添加ajax处理
    public function system_category_add_ajax(){
        $data = array();
        $role = [

        ];
        $msg = [

        ];
        if(request()->isPost()) {
            $Good_type = new GoodsType();

            $Good_type -> data([
                'pid' => input('pid'),
                'name' => input('name'),
                'type' => input('type'),
                'display' => input('display'),
                'sort' => input('sort'),
            ]);
            if($Good_type->save()){
                return json_encode(array('success'=>true,'msg'=>'添加栏目成功'));
            }else{
                return json_encode(array('success'=>false,'msg'=>'添加栏目失败'));
            }
        }
    }
    //修改ajax处理
    public function system_category_edit_ajax(){
        $data = array();
        $role = [

        ];
        $msg = [

        ];
        if(request()->isPost()) {
            $Good_type = new GoodsType();
            $data['pid'] = input('pid');
            $data['name'] = input('name');
            $data['type'] = input('type');
            $data['display'] = input('display');
            $data['sort'] = input('sort');
            $data['id'] = input('id');
            $where = ['id'=>$data['id']];
            $res = $Good_type->save($data,$where);
            if($res){
                return json_encode(array('success'=>true,'msg'=>'修改成功'));
            }else{
                return json_encode(array('success'=>false,'msg'=>'修改失败'));
            }
        }
    }
    //删除ajax处理
    public function system_category_del_ajax(){
        if(request()->isPost()) {
            $id = input('id');
            $res = Db::name('goods_type')->where('pid',$id)->find();
            $goods_res = Db::name('goods')->where('tid',$id)->find();
            if($res || $goods_res){
                return json_encode(array('success'=>false,'msg'=>'删除失败，分类有子分类或有商品'));
            }else{
                $del = Db::name('goods_type')->delete($id);
                if($del){
                    return json_encode(array('success'=>true,'msg'=>'删除成功'));
                }else{
                    return json_encode(array('success'=>false,'msg'=>'删除失败，操作有误'));
                }
            }
        }
    }
}