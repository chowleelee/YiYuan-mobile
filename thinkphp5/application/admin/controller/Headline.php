<?php
namespace app\admin\controller;

class Headline extends \think\Controller
{
    public function index()
    {
        $real_name = input('real_name');
        $where = '';
        if($real_name){
            $where = "real_name like '%$real_name%'";
        }
        $headline_list = db('headline')
                    ->alias('h')
                    ->join('vip v','v.id=h.vip_id')
                    ->field('v.real_name,h.*')
                    ->where($where)
                    ->paginate(8,false,['query'=>['real_name'=>$real_name]]);

        $this->assign('real_name',$real_name);

    	$this->assign('headline_list',$headline_list);
        return $this->fetch();
    }

    public function delete()
    {
    	//删除headline
    	db('headline')->delete(input());

        //删除headline_read
        db('headline_read')->where("headline_id=".input('id'))->delete();
    	
        $this->success('删除成功','index');
    }
}
