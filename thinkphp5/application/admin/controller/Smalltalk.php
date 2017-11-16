<?php 
namespace app\admin\controller;

class Smalltalk extends \think\Controller
{
	
	// 列表
	 public function index()
    {
    	// 查询列表
    	$smalltalk_list = model('smalltalk')->getList();

    	$this->assign("smalltalk_list",$smalltalk_list); 
        return $this->fetch();
    }

	// 详情
    public function details()
    {

        $stc_list = model('smalltalk')->getDetails();
        
        $this->assign('stc_list',$stc_list);
        return $this->fetch();
    }

    // 删除 
     public function delete()
    {

        db("smalltalk")->where("id=".input('id'))->delete();

        $this->success("删除成功","index");

    }
}

?>