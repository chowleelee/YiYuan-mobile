<template>
  <div class="comments" :class="{'overflow':inputText}">
    
      <div class="headback">
        <span @click="backstalk(smalltalk_id)">&lt; 返回小讲</span>
      </div>

<!--     <div class="container"> -->
     <div class="jaingring">
       <span class="samllring">小讲圈</span>
       <!-- <span class="reply">32次主讲回复</span> -->
       <ul class="ring_comment">
         <li v-for="(item,index) in comment_list">
           <div class="studenthead">
             <!-- <router-link to='/tutorDetails'> --><img class="common_head" :src="$sourceUrl+'/img/'+item.head_img" alt=""><!-- </router-link> -->
             <div class="student_content">
               <h6>
               {{comment_name[item.user_id]}}
              </h6>
               <p class="common_con" :style="comment_isLong[index]&& comment_show_status[index]?'max-height:100%;':'overflow:hidden;'"> 
           <!--       <span class="common_top">置顶</span>
                 <span class="common_essence">精</span> -->
                 <span class="common_content">
                  {{item.content}}
                  
                 </span>
                 <span class="showallcommon" @click="showcomment(index)" v-if="comment_isLong[index] && !comment_show_status[index]">全文</span>
                 <span class="hidecomment" @click="hidecomment(index)" v-if="comment_isLong[index] && comment_show_status[index]">收起</span>
               </p>
               <div class="time_assist">
                 <span class="time"><timeago :since="parseInt(item.create_time)*1000"></timeago></span>
                 <span class="acon_box">
                   <span class="assist" :class="{'islike':comment_isLike[index]}"  @click="assisting(index)">{{comment_likeNum[index]}}</span>
                    <span class="common" @click="commoning(item.id,0)"></span>
                 </span>
               </div>
               <div class="commonbox" v-if="reply_num[item.id]>0">
                 <p v-for="reply_item in reply_cut_list[item.id]">
                  <span class="common_writer">
                  {{reply_name[reply_item.user_id]}}
                  </span>
                  <a v-if="reply_item.parent_id>0">
                    回复
                    <span class="common_writer">
                      <!-- {{reply_item.id}} -->
                      <!-- {{reply_parent_userid[reply_item.id]}} -->
                      {{reply_parent_name[reply_parent_userid[reply_item.parent_id]]}}
                    </span>
                  </a>
                  :
                  <span class="comment_con" @click="commoning(item.id,reply_item.id)">
                    {{reply_item.content}}
                  </span>
                </p>
                 <p class="show_art" @click="showart(item.id)" v-if="reply_num[item.id]>2&&reply_cut_list[item.id].length!=reply_list[item.id].length">展开剩余{{reply_num[item.id]-2}}条讨论</p>
                  <p class="show_art" @click="hideart(item.id)" v-if="reply_num[item.id]>2&&reply_cut_list[item.id].length==reply_list[item.id].length">收起讨论</p>
               </div>
               <!-- <div class="commonbox" v-if="reply_num[item.id]==0">
                 暂无评论
               </div> -->
             </div>
           </div>
         </li>
       </ul>
      </div>
      <mt-popup
        v-model="promptpay"
        class="boughtcalss">
        <div class="boughtjoin">
          <div class="boughtbox">
            <p class="paytitle">支付 {{smalltalk_list.price}} 参加小讲</p>
            <p class="paycontent">参加后您将获得本场小讲中的：</p>
            <p class="paydetail">全部知识音频</p>
            <p class="paydetail">与主讲互动机会</p>
            <p class="paydetail">与高品质用户社群交流机会</p>
          </div>
          <div class="payorno">
            <span class="cancel" @click="cancelpay()">取消</span>
            <span class="sure">购买</span>
          </div>
        </div>
      </mt-popup>

      
      <!-- 评论回复输入弹出框 -->
      <mt-popup v-model="inputText"  class="input-popup">
        <div class="btn-wrap">
          <span class="btn-cancel" @click="inputText = false">取消</span>
          <span class="btn-submit" @click="inputComment()">提交</span>
        </div>
        <textarea class="content" v-model="textContent" @input="descInput" maxlength="100" placeholder="请输入你要评论的内容"></textarea>
        <div class="count">
          <span>{{textNum}}</span><span>/</span><span>100</span>
        </div>
      </mt-popup>
    <!-- </div> -->
  </div>
</template>

<script type="es6">
import {mapState,mapMutations,mapGetters} from 'vuex';
import { Actionsheet } from 'mint-ui'
import { Popup } from 'mint-ui'
import { Indicator } from 'mint-ui'
  export default {
    data () {
      return {
        boughtstatus:true,
        promptpay:false,
        smalltalk_id:'',      //小讲id
        smalltalk_list:'',   //小讲列表
        comment_list:[],  //评论列表
        comment_num:'',  //评论数量
        comment_name:[],   //评论用户名
        reply_list:[],  //回复列表
        reply_cut_list:[],   //回复列表未展开前显示2条
        reply_num:[],   //回复数量
        replynum_status:'',  //回复数量状态
        reply_name:[],   //回复用户名
        reply_user_id:'',  //用户回复id
        audio_play_list:[],   //音频播放列表
        content_list:[],   //内容列表
        content_play_id:[],    //音频绑定内容id
        reply_parent_name:[],   //回复评论用户的名
        reply_parent_list:[],  //回复评论用户的列表
        reply_parent_content:[],
        reply_parent_userid:[],
        inputText:false,//评论回复弹出框的显示隐藏控制
        textNum:0,//评论输入文字个数
        textContent:'',//评论内容
        inputCommentId:-1,//根据用户点击的哪个弹出的评论框，如果是直接评论小讲就为0，如果是回复就是会回复评论的id
        inputReplyId:0,  //如果是回复的回复，这里是回复父亲的id
        textContentNull:false,//如果提交时输入为空，弹出提示框
        comment_maxShowAllLength:50,//评论显示全文的最大长度
        comment_isLong:[],//评论文字是否太长而出现全文收起功能
        comment_show_status:[],//评论显示状态是全文还是收起
        comment_isLike:[],//评论的点赞状态
        comment_likeNum:[]//评论的点赞数量
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    created(){
      this.init()
    },
    watch:{
    },
    methods:{  
      init:function(){
        Indicator.open();
        this.smalltalk_id=this.$route.params.id   //小讲id
        this.$http
          .get('/smalltalk/'+this.smalltalk_id,{params:{type:'smalltalk'}})
          .then(rtnData=>{
            // console.log(rtnData.data)
            this.smalltalk_list=rtnData.data
            // console.log(this.smalltalk_list)
            Indicator.close();
          }),
        this.commentInit();
      },
      //获取评论数据
      commentInit:function(){
        let smalltalk_id =this.$route.params.id  //小讲id
        this.comment_show_status = [];
        this.comment_isLong = [];
        this.comment_list = [];
        //this.reply_cut_list = [];
        //this.reply_list = [];
        this.comment_isLike = [];
        // 小讲评论
        this.$http
        .get('Comment',{params:{smalltalk_id}})
        .then(rtnData=>{
          this.comment_list = rtnData.data
          // console.log(this.comment_list) 
          this.comment_num=rtnData.data.length
          // if (this.comment_num>2) {
          //   this.comment_list=this.comment_list.slice(0, 2)
          // } 

          for(var i=0;i<this.comment_list.length;i++){
            this.comment_show_status.push(false);
            //console.log(this.comment_list[i].content.length)
            if(this.comment_list[i].content.length > this.comment_maxShowAllLength){
              this.comment_isLong.push(true);
            }else{
              this.comment_isLong.push(false);
            }
            this.$set(this.comment_likeNum,i,this.comment_list[i].like_num); 
            this.comment_isLike.push(false);
            if(this.isLogin){
              let index = i;
              //console.log(this.comment_list[i].id)
              this.$http
                .get('/commentLike/'+this.comment_list[i].id,{params:{uid:this.info.user_id}})
                .then(rtnData=>{
                  if(rtnData.data!=null){
                    this.$set(this.comment_isLike,index,true)
                  }
                })
            }
            
          }
         
         //console.log(this.comment_isLong)
        })
        .then(()=>{
          this.replyInit();
        })
        //console.log(this.reply_cut_list,this.reply_list);
      },
      //获取回复数据
      replyInit:function(){
         let conmontlist=this.comment_list
          // console.log(conmontlist)
          for (var i = 0; i < conmontlist.length; i++) {
            // console.log(conmontlist[i].user_id)
            let user_id=conmontlist[i].user_id;
            this.$http
            .get('/User/'+user_id)
            .then(rtnData=>{
              // console.log(rtnData.data.user_name)
              //this.comment_name[user_id]=rtnData.data.user_name
              this.$set(this.comment_name,user_id,rtnData.data.user_name)
              // console.log(this.comment_name[user_id])
            })  
          }
          for (var i = 0; i < conmontlist.length; i++) {
            let reply_id=parseInt(conmontlist[i].id) 
            this.$http
            .get('Reply',{params:{reply_id}})
            .then(rtnData=>{
                //this.reply_list[reply_id]=rtnData.data
                this.$set(this.reply_list,reply_id,rtnData.data);
                //this.reply_num[reply_id]=this.reply_list[reply_id].length
                this.$set(this.reply_num,reply_id,this.reply_list[reply_id].length);
                this.$set(this.reply_cut_list,reply_id,this.reply_list[reply_id]);
                // console.log(this.reply_list[reply_id])
                // console.log(this.reply_num[reply_id])
                if (this.reply_num[reply_id]>2) {
                  //this.reply_cut_list[reply_id]=this.reply_list[reply_id]
                  
                  //this.reply_cut_list[reply_id]=this.reply_list[reply_id].slice(0, 2)
                  this.$set(this.reply_cut_list,reply_id,this.reply_list[reply_id].slice(0, 2));
                }
                // 用户回复评论
                for (var i = 0; i < this.reply_list[reply_id].length; i++) {
                  if (this.reply_list[reply_id][i].parent_id>0) {
                    let parent_id=this.reply_list[reply_id][i].parent_id
                    //console.log(parent_id)
                    this.$http
                    .get('Reply/'+parent_id)
                    .then(rtnData=>{
                      //this.reply_parent_userid[parent_id]=rtnData.data.user_id
                     this.$set(this.reply_parent_userid,parent_id,rtnData.data.user_id);
                    })

                    .then(()=>{
                      let user_id=this.reply_parent_userid[parent_id]

                      //console.log(user_id)
                      this.$http
                      .get('/User/'+user_id)
                      .then(rtnData=>{
                        // console.log(rtnData.data)
                        //this.reply_parent_name[user_id]=rtnData.data.user_name
                        this.$set(this.reply_parent_name,user_id,rtnData.data.user_name);
                        //console.log(rtnData.data.user_name)
                      })
                    })                
                  }
                  let user_id=this.reply_list[reply_id][i].user_id
                  // console.log(user_id)
                  this.$http
                  .get('/User/'+user_id)
                  .then(rtnData=>{
                    // console.log(rtnData.data.user_name)
                    //this.reply_name[user_id]=rtnData.data.user_name
                    this.$set(this.reply_name,user_id,rtnData.data.user_name);
                    // console.log(this.reply_name[user_id])
                  })
                }
            })
          }
      },
    
      backstalk:function(n){
        this.$router.push('/stalkteacher/'+n)
      },
      showcomment:function(index){
        this.$set(this.comment_show_status,index,true)
      },
      hidecomment:function(index){
         this.$set(this.comment_show_status,index,false)
      },
    
      // 点赞弹出购买窗口
      assisting:function(index){
        if(this.boughtstatus==false){
          this.promptpay=true;
        }else{
          if(!this.comment_isLike[index]){
            this.$http
              .post('commentLike',{id:this.comment_list[index].id,uid:this.info.user_id})
              .then(rtnData=>{      
                  this.$set(this.comment_isLike,index,true)
                  this.comment_likeNum[index]++;
                  this.$http
                    .put('comment/'+this.comment_list[index].id)
                    .then(rtnData=>{      
                    })
              })

          }
        }
      },
      // 没购买评论弹出购买窗口，已购买弹出评论框
      commoning:function(commentId,replyId){
        if(this.boughtstatus==false){
          this.promptpay=true;
        }else{
          this.inputText = true;
          this.inputCommentId = commentId;
          this.inputReplyId = replyId;
        }
      },
      //统计输入评论内容的长度
      descInput:function(){
        this.textNum = this.textContent.length;
      },
      // 取消购买课程
      cancelpay:function(){
        this.promptpay=false;
      },
      
      //展开所有讨论
      showart:function(index){
        //this.reply_cut_list=this.reply_list
        this.$set(this.reply_cut_list,index,this.reply_list[index])
      },
      //隐藏部分讨论
      hideart:function(index){
         this.$set(this.reply_cut_list,index,this.reply_list[index].slice(0, 2))
      },
      //提交评论
      inputComment:function(){
        if(this.textContent.length != 0){
          this.$http
          .post('reply',{comment_id:this.inputCommentId,content:this.textContent,parent_id:this.inputReplyId,uid:this.info.user_id})
          .then(rtnData=>{
            this.textContent = '';
            this.inputText = false;
            this.textNum = 0;
            this.replyInit();
          })
        }else{
          this.textContentNull = true;
        }
        
      }
    }
  }
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

  @import '../assets/css/smalltalkComments.css';
  
</style>