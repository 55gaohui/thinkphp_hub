<?php
/**
 * Created by PhpStorm.
 * Goodstype: Administrator
 * Date: 2018/9/28/028
 * Time: 20:49
 */
namespace app\app_extend\controller\miaosha;
use think\Controller;
class Index extends Controller{
    public function index(){
        return $this->fetch();
    }
}