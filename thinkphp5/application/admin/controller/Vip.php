<?php
namespace app\admin\controller;

class Vip extends \think\Controller
{
    public function index()
    {
        $vip_list = model('Vip')->getVipList();
        $this->assign('vip_list', $vip_list);

        $vpcate_list = db('vip_cate')->select();
        $this->assign('vpcate_list', $vpcate_list);

        $this->assign('vip_name', input('vip_name'));
        $this->assign('vip_cate', input('vip_cate'));
        $this->assign('floor_price', input('floor_price'));
        $this->assign('ceil_price', input('ceil_price'));
        
        return $this->fetch();
    }


    public function add()
    {
    	$vpcate_list = db('vip_cate')->select();
        $this->assign('vpcate_list', $vpcate_list);

        return $this->fetch();
    }

    public function totrue()
    {       
        $vip_info = db('vip')
                    ->field('id,real_name')
                    ->where("id=".input('id'))
                    ->find();

        $this->assign('vip_info', $vip_info);        
        return $this->fetch();
    }

    public function pass()
    {       
        db('vip')->where("id=".input('vip_id'))->update(['is_real'=>2]);

        $this->success('审核通过！！','index');
    }

    public function reject()
    {
        db('vip')->where("id=".input('vip_id'))->update(['is_real'=>0]);

        $the_reason = input('reason')?input('reason'):"您的申请没有通过!!!";
        db('reject')->insert(['vip_id'=>input('vip_id'),'reason'=>$the_reason]);
        $this->error('审核不通过！！','index');
    }

    public function edit()
    {   
        $vip_info = model('Vip')->getVipInfo();
        $this->assign('vip_info',$vip_info);

        $vpcate_list = db('vip_cate')->select();
        $this->assign('vpcate_list', $vpcate_list);

        return $this->fetch();
    }

    public function delete()
    {
       db("vip")->delete(input());
       $this->success('删除成功','index');
    }

    public function update()
    {
       db("vip")->update(input());
       $this->success('更新成功','index');
    }    

    public function save()
    {
        model('Vip')->addUser();
        $this->success('添加成功', 'index');
    }
}
