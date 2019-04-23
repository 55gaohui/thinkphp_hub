<?php
/**
 * Created by PhpStorm.
 * User: gaohui
 * Date: 2018/11/27
 * Time: 下午9:59
 */
namespace app\index\controller;
use think\Controller;
use think\Db;

class Common extends Controller{
    public function getAll($model, $where=array(), $order = '', $page_num = 0){
        $data = Db::name($model)->where($where)->order($order)->page($page_num)->select();
        return $data;
    }
    //获取某个分类下所有子分类
    public function _getChilds($data,$id){
        static $tidArr = array();
        foreach ( $data as $val) {
            if($val['pid'] == $id){
                $tidArr[]=$val['id'];
                $tidArr = array_merge($tidArr,$this->_getChilds($data,$val['id']));
            }
        }
        return $tidArr;
    }
    //面包屑导航（获取父级分类）
    public function _getParents($id){
        static $ParArr = array();
        $info = Db::name('goods_type')->field('id,pid,name')->find($id);
        $ParArr[] = $info;
        if($info['pid']>0){//pid大于0  则一定是下级分类
            $this->_getParents($info['pid']);//递归
        }
        return $ParArr;
    }   
        //导航列表
    public function nav_list(){
        $type = Db::table('goods_type')->where('pid=0')->select();
        $type2 = array();
        foreach ($type as $key=>$val){
            $type[$key]['child'] = array();
            $type2 =  Db::table('goods_type')->where('pid='.$val['id'])->select();
            foreach ($type2 as $ke=>$va){
                $type3 = array();
                array_push($type[$key]['child'],$va);
                $type[$key]['child'][$ke]['child'] = array();
                $type3 =  Db::table('goods_type')->where('pid='.$va['id'])->select();
                foreach ($type3 as $k=>$v){
                    array_push($type[$key]['child'][$ke]['child'],$v);
                }
            }
        }
        return $type;
    }
    //侧边导航
    protected function side_nav($id=""){
        # code...
        $res = Db::name('goods_type')->where('id',$id)->find();
        $res_list = Db::name('goods_type')->where('pid',$id)->select();
        $this->assign('side_nav_list',$res_list);
        $this->assign('side_nav_title',$res['name']);
    }
}