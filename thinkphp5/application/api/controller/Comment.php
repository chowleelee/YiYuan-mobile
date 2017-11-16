<?php
namespace app\api\controller;

class Comment extends \think\Controller
{
	function index()
	{
		//小讲评论表
		$smalltalk_id=input('smalltalk_id');
		// echo $smalltalk_comment_id;
		$list = db("comment")
			->alias('c')
			->join('user u','u.id=c.user_id')
			->field('c.id,c.content,c.sort,c.create_time,c.like_num,c.user_id,u.head_img')
			->where("smalltalk_id=$smalltalk_id")
			->select();
		return json($list);
	}

	function save(){
		$data['smalltalk_id'] = input('smalltalk_id');
		$data['content'] = input('content');
		$data['user_id']= input('uid');
		$data['create_time']  = time();
		$list = db('comment')
			->insert($data);
	}

	function update(){
		$comment_id = input('id');
		$list = db("comment")
		->where('id',$comment_id)
		->setInc('like_num');
	}
	
}


?>