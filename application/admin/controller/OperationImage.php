<?php
namespace app\admin\controller;
use think\Db;
/**
 * 
 */
class OperationImage extends Common{
	public function image_list()
	{
		$imageArr = $this->read_dir(ROOT_PATH."public/static/upload");
		// dump($imageArr);
		$this->assign('imageArr',$imageArr);
		return $this->fetch();
	}
}
?>