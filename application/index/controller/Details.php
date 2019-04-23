<?php
namespace app\index\controller;
use think\Db;
use think\process\pipes\Windows;
/**
 * 
 */
class Details extends Common
{

	public function details(){
		$nav_list = $this->nav_list();
        $this->assign('type',$nav_list);
		$id = input('id');
		
		$goods = Db::name('goods')->where('id',$id)->find();
		$rotation_img = Db::name('goods_files')->where('id','in',$goods['imagepath'])->select();
		$this->assign('rotation_img',$rotation_img);
		if($goods){
			$this->assign('goods',$goods);
		}else{

		}
		//面包屑导航
        $Bre_nav = array_reverse($this->_getParents($goods['tid']));
        $this->assign('bre_nav',$Bre_nav);
        return $this->fetch();
    }
}
?>