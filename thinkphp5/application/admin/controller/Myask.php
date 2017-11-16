<?php
namespace app\admin\controller;

class Myask extends \think\Controller
{
    public function index()
    {
        $ask_list = model('Myask')->getMyaskList();
        $this->assign('ask_list',$ask_list);

        $this->assign('user_name', input('user_name'));
    	$this->assign('vip_name', input('vip_name'));
        $this->assign('is_public', input('is_public'));
    	
        return $this->fetch();
    }

    public function delete()
    {
    	//删除ask
    	db('ask')->delete(input());

    	//删除ask_buy
    	db('ask_buy')->where("ask_id=".input('id'))->delete();
    	
        $this->success('删除成功','index');
    }
}
