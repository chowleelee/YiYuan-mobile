<?php 
namespace app\admin\controller;

class Comment extends \think\Controller
{
	
	// 列表
	 public function index()
    {
    	// 查询列表
    	$comment_list = model('comment')->getList();

    	$this->assign("comment_list",$comment_list);

        return $this->fetch();
    }

	// 详情
    public function details()
    {

        $reply_list = model('comment')->getDetails();

        $this->assign('reply_list',$reply_list);
        return $this->fetch();

    }
    // 删除 
     public function delete()
    {

        db(input('table'))->where("id=".input('id'))->delete();

        $this->success("删除成功",'index');

    }
}

?>