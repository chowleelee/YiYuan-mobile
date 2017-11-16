<?php 
namespace app\admin\model;
	
class Order extends \think\Model{
	public function getList(){
		$comment_list = db("smalltalk_buy")
                        ->alias('stb')
                        ->field('stb.id,st.title,u.user_name,st.price')
                        ->join('smalltalk st','stb.smalltalk_id=st.id')
                        ->join('user u','stb.user_id=u.id')
                        ->order('id asc')
                        ->paginate(5);

		return $comment_list;
	}	

}
?>