<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/5/005
 * Time: 15:10
 */
	//计算某个类别所属的类别层数
	function getcatelayer($cateid,$flag=1){
        $cates = M('Gcategory');
        $ini['cate_id'] = $cateid;
        $arr = $cates->where($ini)->find();
        if($arr['parent_id']!=0){
            $flag = getcatelayer($arr['parent_id'],$flag+1);
        }
        return  $flag;
    }
//类别递归(单层)
function getone($arr,$id=0,$level=1){
    //迭代
    $task=array($id);//任务数组
    $tree=array();//结果数组
    while (!empty($task)){
        $flg=false;
        foreach ($arr as $k=>$v){
            if ($v['pid']==$id) {
                $tree[]=array(
                    'id'=>$v['id'],
                    'name'=>$v['name'],
                    'pid'=>$v['pid'],
                    'level'=>$level,
                    'sort'=>$v['sort'],
                    'display'=>$v['display'],
                );
                array_push($task, $v['id']);
                $id=$v['id'];
                $level=$level+1;
                unset($arr[$k]);
                $flg=true;
                break;
            }

        }
        if ($flg==false) {
            array_pop($task);
            $id=end($task);
            $level=$level-1;
        }
    }
    return $tree;

}
//类别递归(多层)
function getCates($arr,$pid=0)
{
    for($i=0; $i<count($arr); $i++){
        if($arr[$i]['parent_id']==$pid){
            $newArr[] = array(
                "id"=>$arr[$i]['cate_id'],
                "name"=>$arr[$i]['cate_name'],
                'son'=>getCates($arr,$arr[$i]['cate_id']),
            );
        }
    }
    return $newArr;
}

//查询类别所属级
function getlayer($cateid,$flg=1)
{
    $gcg=M("Gcategory");
    $ini["cate_id"]=$cateid;
    $cateArr=$gcg->where($ini)->find();
    if($cateArr['parent_id']!=0)
    {
        $flg=getlayer($cateArr['parent_id'],$flg+1);
    }
    return $flg;
}

//查询数组某个字符串是否存在
function get_array_repeats(array $array,$string){
    $count = array_count_values($array);
    if(array_key_exists($string,$count)){
        return $count[$string];
    }else{
        return 0;
    }
}
