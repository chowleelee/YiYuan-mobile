<?php
namespace app\index\controller;
use \think\Controller;


class Index extends Controller
{
    public function index()
    {
    	// 1、return 不能忘   
    	// 2、使用fetch必修继承父类，父类是通过使用它的命名空间+类名引入的
        return $this->fetch();
    }
}
