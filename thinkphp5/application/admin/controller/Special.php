<?php 
namespace app\admin\controller;

class Special extends \think\Controller
{
	
	// 列表
	 public function index()
    {
    	// 查询列表
    	$special_list = db("special")->order('id asc')->paginate(5);
    	$this->assign("special_list",$special_list); 

       return $this->fetch();
    }

	// 添加
	function add()
	{
    	return $this->fetch();
	}

	public function save()
	{
		db("special")
            ->data([
                'title'=>input('title'),
                'introduce'=>input('introduce')
            ])
            ->insert();

        $this->success("添加成功","index");
	}

	public function edit()
	{
		// 进入编辑页面
        // 获取当前编辑的信息
        $info = db("special")->where("id=".input('id'))->find();
        $this->assign("info",$info);
        return $this->fetch();
	}

	// 处理编辑
    public function update()
    {

        db("special")->update(input());//input直接获取到主键

        $this->success("修改成功","index");

    }
    // 删除 
     public function delete()
    {

        db("special")
            ->where("id=".input('id'))
            ->delete();

        $this->success("删除成功","index");

    }
}

?>