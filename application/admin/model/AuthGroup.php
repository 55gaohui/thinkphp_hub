<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/28/028
 * Time: 10:40
 */
namespace app\admin\model;
use think\Model;
class AuthGroup extends Model{
    public function operate_root($data,$where=''){
        $valid_title = false;
        if (empty($where)){
            $valid_title = $this->where('title',$data['title'])->select();
        }
        if ($valid_title){
            return 'valid_title';
        }else{
            $res = $this->save($data,$where);
            return $res;
        }
    }
}
