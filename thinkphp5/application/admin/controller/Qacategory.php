<?php
namespace app\admin\controller;

class Qacategory extends \think\Controller
{
    public function index()
    {
    	$qacate_list = db('quickask_cate')
    				->paginate(8);
    	$this->assign('qacate_list',$qacate_list);
        return $this->fetch();
    }

    public function delete()
    {
    	db('quickask_cate')->delete(input());
    	
        $this->success('删除成功','index');
    }

    public function add()
    {
        return $this->fetch();
    }

    public function edit()
    {   
        $qacate_info = db('quickask_cate')->find(input());
        $this->assign('qacate_info',$qacate_info);

        return $this->fetch();
    }

    public function update()
    {
        if(!empty($_FILES['cate_img']['tmp_name'])){
            $cate_img = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'cate_img');
        }else{
            $cate_img = input('img');
        }
        db("quickask_cate")->where('id='.input('id'))->update([
            'cate_img'=>$cate_img,
            'cate_name'=>input('cate_name'),
            'point'=>input('point')
        ]);
       $this->success('更新成功','index');
    }    

    public function save()
    {
        $cate_img = saveAndgetSrc(ROOT_PATH."public/static/api/img/",'cate_img');
        db("quickask_cate")->insert([
            'cate_img'=>$cate_img,
            'cate_name'=>input('cate_name'),
            'point'=>input('point')
        ]);
        $this->success('添加成功', 'index');
    }
}
