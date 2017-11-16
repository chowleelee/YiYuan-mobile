<?php
namespace app\admin\controller;

class Answer extends \think\Controller
{
    public function index()
    {
        $answer_list = model('Answer')->getAnswerList();
        $this->assign('answer_list',$answer_list);

        $this->assign('real_name', input('real_name'));
        $this->assign('quickask_id', input('quickask_id'));
        $this->assign('is_select', input('is_select'));
        
        return $this->fetch();
    }

    public function delete()
    {
    	db('quickask_answer')->delete(input());
    	
        $this->success('删除成功','index');
    }
}
