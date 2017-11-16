<?php
namespace app\api\controller;

class SmalltalkAudio extends \think\Controller
{
	function index()
	{
		//讲单语音表
		$smalltalkcontent_id=input('index');
		$list = db("smalltalk_audio")
			->field('id,audio_name,audio,duration,shiting,smalltalk_content_id')
			->where("smalltalk_content_id='$smalltalkcontent_id'")
			->select();
			// var_dump($list);
			// exit();shiting,
		return json($list);
	}
	function read()
	{
		$smalltalkaudio_id=input('id');
		$list = db("smalltalk_audio")
			->field('id,audio_name,audio,duration,smalltalk_content_id,shiting,like_num')
			->where("id=$smalltalkaudio_id")
			->find(); //shiting,
		return json($list);
	}
	
}


?>