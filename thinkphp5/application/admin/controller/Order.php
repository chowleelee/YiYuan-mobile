<?php 
namespace app\admin\controller;

class Order extends \think\Controller
{
	
	// 列表
	 public function index()
    {
    	// 查询列表
    	$order_list = model('order')->getList();

    	$this->assign("order_list",$order_list); 
        return $this->fetch();
    }

    // 删除 
     public function delete()
    {

        db("smalltalk_buy")->where("id=".input('id'))->delete();

        $this->success("删除成功","index");

    }
}

?>