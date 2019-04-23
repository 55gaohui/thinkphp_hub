<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 11:35
 */
namespace app\admin\model;
use think\Model;

class AdminUser extends Model{

    /**
     * 修改和删除管理员
     * @param $data 数据
     * @param string $where 条件
     * @return false|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function operate_user($data,$where=''){
        $valid_name = false;
        $ph_count = 2;
        if (empty($where)){
            $valid_name = $this->where('admin_name',$data['admin_name'])->select();
        }else{
            $phone = $this->where('id',$data['id'])->find();
            if ($phone['phone'] == $data['phone']){
                $ph_count=3;
            }
        }
        $valid_phone = $this->where('phone',$data['phone'])->count();
        if($valid_name){
            return 'valid_name';
        }elseif($valid_phone>$ph_count){
            return 'valid_phone';
        }else{
            $res = $this->allowField(true)->save($data,$where);
            return $res;
        }
    }

}
