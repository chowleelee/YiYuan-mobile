<?php
namespace app\admin\controller;

class Listen extends \think\Controller
{
    public function index()
    {
        $user_name = input('user_name');
        $where = '';
        if($user_name){
            $where = "user_name like '%$user_name%'";
        }
        $askbuy_list = db('ask_buy')
                    ->alias('ab')
                    ->join('user u','u.id=ab.user_id')
                    ->join('ask a','a.id=ab.ask_id')
                    ->field('u.user_name,a.ask_content,ab.id')
                    ->where($where)
                    ->paginate(8,false,['query'=>['user_name'=>$user_name]]);

        $this->assign('user_name',$user_name);
    	$this->assign('askbuy_list',$askbuy_list);
        return $this->fetch();
    }

    public function delete()
    {
    	//删除ask_buy
    	db('ask_buy')->delete(input());
    	
        $this->success('删除成功','index');
    }
}
