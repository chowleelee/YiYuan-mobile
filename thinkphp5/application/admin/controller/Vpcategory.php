<?php
namespace app\admin\controller;

class Vpcategory extends \think\Controller
{
    public function index()
    {
    	$vpcate_list = db('vip_cate')
    				->paginate(8);
    	$this->assign('vpcate_list',$vpcate_list);
        return $this->fetch();
    }

    public function delete()
    {
    	db('vip_cate')->delete(input());
    	
        $this->success('删除成功','index');
    }

    public function add()
    {
        return $this->fetch();
    }

    public function edit()
    {   
        $vpcate_info = db('vip_cate')->find(input());
        $this->assign('vpcate_info',$vpcate_info);

        return $this->fetch();
    }

    public function update()
    {
       db("vip_cate")->update(input());
       $this->success('更新成功','index');
    }    

    public function save()
    {
        db("vip_cate")->insert(input());
        $this->success('添加成功', 'index');
    }
}
