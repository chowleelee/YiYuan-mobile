<?php
namespace app\admin\controller;

class Follow extends \think\Controller
{
    public function index()
    {
        $vplisten_list = db('vip_listen')
                    ->alias('vl')
                    ->join('user u','u.id=vl.user_id')
                    ->join('vip v','v.id=vl.vip_id')
                    ->field('u.user_name,v.real_name,vl.*')
                    ->paginate(8);

    	$this->assign('vplisten_list',$vplisten_list);
        return $this->fetch();
    }

    public function delete()
    {
    	db('vip_listen')->delete(input());
    	
        $this->success('删除成功','index');
    }
}
