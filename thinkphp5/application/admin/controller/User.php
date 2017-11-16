<?php
namespace app\admin\controller;

class User extends \think\Controller
{
    public function index()
    {
        $user_list = model('User')->getUserList();
        $this->assign('user_list', $user_list);

        $this->assign('user_name', input('user_name'));
        $this->assign('phone', input('phone'));

        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function edit()
    {   
        $user_info = model('User')->getUserInfo();
        $this->assign('user_info',$user_info);
        return $this->fetch();
    }

    public function delete()
    {
       db("user")->delete(input());
       $this->success('删除成功','index');
    }

    public function update()
    {
        model('User')->updateUser();
       $this->success('更新成功','index');
    }    

    public function save()
    {
        model('User')->addUser();
        $this->success('添加成功', 'index');
    }

    public function vip()
    {       
        $user_info = db('tovip')
                    ->alias('tv')
                    ->join('vip_cate vc','vc.id=tv.vip_cate_id')
                    ->where("user_id=".input('id'))
                    ->find();

        $this->assign('user_info', $user_info);        
        return $this->fetch();
    }

    public function pass()
    {       
        db('user')->where("id=".input('user_id'))->update(['status'=>3]);

        model('User')->addVip();
        $this->success('审核通过！！','index');
    }    

    public function reject()
    {
        db('user')->where("id=".input('user_id'))->update(['status'=>1]);
        $the_reason = input('reason')?input('reason'):"您的申请没有通过!!!";
        db('reject_tovip')->insert(['user_id'=>input('user_id'),'reason'=>$the_reason]);
        $this->error('审核不通过！！','index');
    }

}
