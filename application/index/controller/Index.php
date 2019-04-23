<?php
namespace app\index\controller;
use think\Db;
use think\process\pipes\Windows;

class Index extends Common
{
    public function index()
    {
        $nav_list = $this->nav_list();
        $this->assign('type',$nav_list);
        return $this->fetch();
    }


    
}
