<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Validate;
use \app\admin\model\Goods as GoodsM;
class Goods extends Common
{
    public function product_category()
    {
        return $this->fetch();
    }
    public function product_category_add(){
        $get_category_list=$this->get_category_list();
        $this->assign('data',$get_category_list);
        return $this->fetch();
    }
    //添加商品页
    public function product_add(){
        $get_category_list=$this->get_category_list();
        $this->assign('data',$get_category_list);
        return $this->fetch();
    }
    public function product_list(){
        //分类列表查询
        $get_category_list=$this->get_category_list();
        $this->assign('classify',$get_category_list);
        return $this->fetch();
    }
    //ajax 获取商品列表信息
    public function product_list_ajax(){
        $pageNumber = input('post.pageNumber');
        $pageSize = input('post.pageSize');
        $search_text = input('post.search_text');
        $search_classify = input('post.search_classify');
        $datemin = input('post.datemin');
        $datemax = input('post.datemax');
        if($datemax == ''){
            $datemax = date('Y-m-d H:i:s',time());
        }
        if($search_classify != ''){
            $resArr = $this->getchildren($search_classify);
            $where["tid"] = ['in',$resArr];
        }
        $where["goodsname"] = ['like','%'.$search_text.'%'];
        if($datemin != '' || $datemax != ''){
//            $where['create_date'] = array('between',array($datemin,$datemax));
        }
        $data = Db::name('goods')->where($where)->select();
//        dump(Db::name('goods')->getLastSql());
        return $data;
    }
    //带参查询商品
    public function product_list_type(){
        if (\request()->isAjax()){
            $data = input('post.');
            $res = Db::table('goods')->where('tid='.$data['id'].' or tpid='.$data['id'])->select();
            $Total = Db::table('goods')->where('tid='.$data['id'].' or tpid='.$data['id'])->count();
            return $res;
        }
    }
    //商品编辑页
    public function product_edit_goods(){
        $goods = Db::table('goods')->find(input('id'));

        $images=explode(',',$goods['imagepath']);
        $i=Db::name('goods_files');
        $image=[];
        foreach($images as $v){
            array_push($image,$i->find($v));
        }
        $this->assign("image",$image);
        $this->assign('goods',$goods);
        header('content-type:text/html;charset=utf-8');
        $categoryArr=db::name('goods_type')->select();
        //调用递归函数
        $data=getone($categoryArr);
        $this->assign('data',$data);
        return $this->fetch();
    }
    //fileinput bootstrop  多图上传
    public function product_add_images(){
        $files = request()->file('files');
        return $this->add_images($files);
    }
    //fileinput bootstrop  图片删除
    public function product_del_images(){
        $id = $_GET['id'];
        return $this->del_img($id);
    }
    //添加商品页信息
    public function product_add_goods(){
        $data['goodsname'] = input('post.goodsname');
        $data['attributes'] = input('post.attributes');
        $tid = explode(',',input('tid'));
        $data['tid'] =$tid[0];
        $data['tpid'] =$tid[1];
        $data['unit'] = input('post.unit');
        $data['number'] = input('post.number');
        $data['barcode'] = input('post.barcode');
        $data['curprice'] = input('post.curprice');
        $data['oriprice'] = input('post.oriprice');
        $data['cosprice'] = input('post.cosprice');
        $data['inventory'] = input('post.inventory');
        $data['paylimit'] = input('post.paylimit');
        $data['already'] = input('post.already');
        $data['freight'] = input('post.freight');
        $data['status'] = input('post.status');
        $data['reorder'] = input('post.reorder');
        $data['text'] = input('post.editorValue');
        if(empty($_POST['imagepath'])){
            $data['imagepath']='';
        }else{
            $data['imagepath']=implode(',', $_POST['imagepath']);
        }
        $m = Db::name('goods');
        $ressult = $m->insert($data);
        if($ressult){
            return $this->success('添加商品成功！','product_list','',2);
        }else{
            return $this->error('添加失败','product_list','',2);
        }
    }
    //修改商品页信息
    public function product_edit_goods_new(){
        $data['id'] = input('id');
        $data['goodsname'] = input('post.goodsname');
        $data['attributes'] = input('post.attributes');
        $tid = explode(',',input('tid'));
        $data['tid'] =$tid[0];
        $data['tpid'] =$tid[1];
        $data['unit'] = input('post.unit');
        $data['number'] = input('post.number');
        $data['barcode'] = input('post.barcode');
        $data['curprice'] = input('post.curprice');
        $data['oriprice'] = input('post.oriprice');
        $data['cosprice'] = input('post.cosprice');
        $data['inventory'] = input('post.inventory');
        $data['paylimit'] = input('post.paylimit');
        $data['already'] = input('post.already');
        $data['freight'] = input('post.freight');
        $data['status'] = input('post.status');
        $data['reorder'] = input('post.reorder');
        $data['text'] = input('post.editorValue');
        if(empty($_POST['imagepath'])){
            $data['imagepath']='';
        }else{
            $data['imagepath']=implode(',', $_POST['imagepath']);
        }

        $ressult = Db::table('goods')->where('id',$data['id'])->update($data);
        if($ressult){
            return $this->success('修改商品成功！','product_list','',2);
        }else{
            return $this->error('修改失败','product_list','',2);
        }
    }
    //删除商品页信息
    public function product_del_goods(Request $request){

        if ($request->isPost()){
            $data = input('post.');
            $goods = new GoodsM();
            $res = $goods::destroy($data['id']);
            return json_encode($res);
        }
    }
    //获取分类数据
    public function product_category_ajax(){
        $m = Db::name('goods_type');
        $data = $m->field('id,pid,name')->select();
        echo json_encode($data);
    }
    //添加分类信息到数据库
    public function goods_type_add(){
        $data['name'] = input('post.name');
        $data['pid'] = input('post.pid');
        $m = Db::name('goods_type');
        if($data['name'] != '' && $data['pid'] != 0){
            $path = $m->find($data['pid']);
            $data['path'] = $path['path'];
            $data['level'] = substr_count($data['path'],',');
            $insID = $m->insertGetId($data);
            $path['id'] = $insID;
            $data['path'] = $path['path'].','.$insID;
            $data['level'] = substr_count($data['path'],',');
            $res = $m->where('id',$insID)->update($data);
            if($res){
                echo '<script>alert("添加成功！");parent.location.href="product_category"</script>';
            }else{
                echo '<script>alert("添加失败！");parent.location.href="product_category"</script>';
            }
        }else if($data['name'] != '' && $data['pid']==0){
            $data['path'] = $data['pid'];
            $data['level'] = 1;
            $insID = $m->insertGetId($data);
            $path['id'] = $insID;
            $path['path'] = $data['path'].','.$insID;
            $res = $m->where('id',$insID)->update($data);
            if($res){
                echo '<script>alert("添加成功！");parent.location.href="product_category"</script>';
            }else{
                echo '<script>alert("添加失败！");parent.location.href="product_category"</script>';
            }
        }else{
            echo '<script>alert("添加失败，内容不能为空！");parent.location.href="product_category"</script>';
        }
    }
    //删除分类信息
    public function product_category_del(){
        $id = $_GET['id'];
        $m = $m = Db::name('goods_type');
        $data = $m->where('pid='.$id)->find();
        if($data){
            $str = '分类下面还有子分类，不允许删除';
            echo json_encode($str);
        }else{
            $re = $m->delete($id);
            if ($re){
                echo 1;
            }
        }
    }

//    //分类列表获取
//    protected function get_category_list(){
//        header('content-type:text/html;charset=utf-8');
//        $categoryArr=Db::name('goods_type')->select();
//        //调用递归函数
//        return getone($categoryArr);
//    }
    //执行函数
    public function getchildren($search_classify){
        //取出所有分类
        $data=Db::name('goods_type')->select();
        //每次执行前将$extis设为true  函数就会清空$res
        $res= $this->_getchildren($data,$search_classify,true);
        return $res;
    }
    //递归数据
    public function _getchildren($data,$catid,$extis=false){

        if($extis){
            $resu=[];
        }
        static $resu=array();
        foreach($data as $k=>$v){
            //判断该条数据的pid 是否等于当前 id
            if($v['pid']==$catid){
                //将该条数据保存在静态变量中
                $resu[]=$v['id'];
                //继续将查询出的数据中id代入递归中继续操作
                $this->_getchildren($data,$v['id']);
            }
        }
        return $resu;
    }
}
