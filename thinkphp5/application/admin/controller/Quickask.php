<?php
namespace app\admin\controller;

class Quickask extends \think\Controller
{
    public function index()
    {
        $user_name = input('user_name');
        $where = '';
        if($user_name){
            $where = "user_name like '%$user_name%'";
        }
        $quickask_list = db('quickask')
                    ->alias('q')
                    ->join('user u','u.id=q.user_id')
                    ->field('u.user_name,q.*')
                    ->where($where)
                    ->paginate(8,false,['query'=>['user_name'=>$user_name]]);

        $this->assign('user_name',$user_name);
    	$this->assign('quickask_list',$quickask_list);
        return $this->fetch();
    }

    public function delete()
    {
    	//删除quickask
    	db('quickask')->delete(input());

        //删除quickask_answer
        db('quickask_answer')->where("quickask_id=".input('id'))->delete();
    	
        $this->success('删除成功','index');
    }
}
