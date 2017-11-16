<?php 
namespace app\admin\model;
	
class Comment extends \think\Model{
	public function getList(){
		$comment_list = db("comment")
                    ->alias('c')
                    ->field('c.id,u.user_name,c.content,st.title')
                    ->join('user u','u.id=c.user_id')
                    ->join('smalltalk st','st.id=c.smalltalk_id')
                    ->order('id asc')
                    ->paginate(5);

		return $comment_list;
	}	

	public function getDetails(){
		$details_list = db("reply")
            ->alias('r')
            ->field('r.id,u.user_name,r.content')
            ->join('user u','u.id=r.user_id')
            ->where('comment_id='.input('id'))
            ->order('id asc')
            ->paginate(5);
                    
		return $details_list;
	}
}
?>