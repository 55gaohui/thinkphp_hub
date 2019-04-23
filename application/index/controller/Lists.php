<?php
namespace app\index\controller;
use think\Db;
use think\process\pipes\Windows;
/**
 * 
 */
class Lists extends Common{
    public function lists(){
        $type = Db::name('goods_type')->where('id',input('id'))->find();
        dump(111);
        dump($type);
        switch ($type['type'] ){
            case 'product':
                $nav_list = $this->nav_list();
                $this->assign('type',$nav_list);
                $id = input('id');
                $where = array();
                $data = Db::name('goods_type')->select();
                $where['tid'] = array('in',implode(',',$this->_getChilds($data,$id)));
                $where['status'] = 1;
                $page_num = 1;
                $lists = $this->getAll('goods',$where,'id desc',$page_num);
                foreach ($lists as &$val){
                    $img_first = explode(',',$val['imagepath']);
                    $goods_img = Db::name('goods_files')->where('id',$img_first[0])->find();
                    $val['img_first'] = $goods_img['filepath'];
                }
                $this->assign('lists_goods',$lists);
                //面包屑导航
                $Bre_nav = array_reverse($this->_getParents($id));
                $this->assign('bre_nav',$Bre_nav);

                $side_nav = Db::name('goods_type')->where('pid',$id)->find();
                if(!$side_nav){
                    $pid = Db::name('goods_type')->where('id',$id)->find();
                    $this->side_nav($pid['pid']);
                }else{
                    $this->side_nav($id);
                }
                return $this->fetch('lists');
                break;
            case 'pages':
                return $this->fetch('list');
                break;
        }

    }
}
?>