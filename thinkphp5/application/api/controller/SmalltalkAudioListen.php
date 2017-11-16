<?php
namespace app\api\controller;

class SmalltalkAudioListen extends \think\Controller
{
	function index()
	{
		$id = input('id');
            $list = db("smalltalk_audio_listen")->alias('sal')
                  ->where('sal.user_id',$id)
                  ->join('user u','u.id = sal.user_id')
                  ->field('sal.smalltalk_audio_id')
                  ->select();
            return json($list);
	}
      function read()
      {
            $id = input('id');
            $uid = input('uid');
            //echo $id;
            $list = db("smalltalk_audio_listen")->alias('sal')
                  ->where("sal.smalltalk_audio_id=$id and sal.user_id = $uid")
                  ->field('id')
                  ->find();
            return json($list);
      }
      function save() 
      {
            $smalltalk_audio_id = input('smalltalk_audio_id');
            $user_id = input('uid');
            $data = ['smalltalk_audio_id' => $smalltalk_audio_id,'user_id' => $user_id];
            $list = db("smalltalk_audio_listen")
                  ->insert($data);     
      }
	
}


?>