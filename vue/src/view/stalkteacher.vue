<template>
  <div class="stalkteacher" :class="{'overflow':inputText}">
      <div class="stalkhead" :style="'background-image:url('+smalltalk_list.smalltalk_img+')'">
        <div class="headback">
          <span @click="backstalk()">&lt; 全部小讲</span>
          <span>小讲指南？</span>
        </div>
        <div class="foottitle">
          <h2>{{smalltalk_list.title}} </h2>
          <div class="footlist">
            <span class="pro_grow">{{smalltalkcate.cate_name}}</span>
            <span class="peo_joinnum">{{smalltalk_list.join_num}}人参加</span>
          </div>
        </div>
      </div>
      <div class="container">
          <div class="teachercar">
            <span>☑{{smalltalk_list.duration}}分钟语音</span>
            <span>☑主讲互动</span>
            <span>☑小讲圈交流</span>
          </div>
          <div class="tutorpro">
            <router-link  :to="'/tutorDetails/'+vip_list.id">
              <div class="tea_name">
                <p class="name">主讲：{{vip_real_name}}</p>
                <p class="profession">{{vip_identity}}</p>
              </div>
            </router-link>
            <div v-if="info.vip_id!=vip_list.id">
              <div class="tutorDetails-follow" @click="showIt()" v-show="!followed"></div>
              <div class="tutorDetails-followed" @click="showIt()" v-show="followed"></div>
            </div>
          </div>

          <div class="nav_list_box">
            <h2 class="nav_list">简介</h2>
            <div class="content_line" :style="conlineshow?'max-height:100%;':''">
              <p class="show_all" v-if="!conlineshow" @click="showalline()">
                <span>...查看全部</span> 
              </p>
              <p class="conline_one" v-html="smalltalk_list.summary "></p>
              <p class="hide_all" v-if="conlineshow" @click="hideline()">收起</p>
              
            </div>
          </div>
          <h2 class="nav_list_menu">
            <span>讲单</span>
            <a href="javascript:void(0)" class="app_download">
              <i class="down_btn"></i><span>打开APP连播</span>
            </a>
          </h2>
          <div class="class_list">
            <ul class="class_topic">
              <li v-for="(item,index) in smalltalkcontent_list" class="classcon_list">
                <p class="caption">{{index+1}}. {{item.title}}</p>
                <div class="content_on" v-for="(audio_item,key) in smalltalkaudio_list[index]" @click="shiting(smalltalkid,audio_item.id,audio_item.shiting)">
                  <div class="gre_title">
                    <span class="blue" v-if="audio_item.shiting>0&&!boughtstatus">试听</span>
                    <span class="title" :class="{audio_listen:smalltalk_audio_listen[index][key]}" >{{audio_item.audio_name}}</span>
                  </div>
                  <div class="timeapp">
                    <span class="shu"></span>
                    <span class="bofang"></span>
                    <!-- <span class="time">{{audio_item.duration}}</span> -->
                  </div>
                </div> 
              </li>
            </ul>
          </div>
      </div>
      <div class="jaingring" v-if="comment_num>0">  
       <span class="samllring">小讲圈</span><!-- <span class="reply">32次主讲回复</span> -->
       <ul class="ring_comment">
         <li v-for="(item,index) in comment_list">
           <div class="studenthead">
             <!-- <router-link to='/tutorDetails'> --><img class="common_head" :src="$sourceUrl+'/img/'+item.head_img" alt=""><!-- </router-link> -->
             <div class="student_content">
               <h6>
               {{comment_name[item.user_id]}}
              </h6>
               <p class="common_con" :style="comment_isLong[index]&& comment_show_status[index]?'max-height:100%;':'overflow:hidden;'">
               <!--   <span class="common_top">置顶</span>
                 <span class="common_essence">精</span> -->
                 <span class="common_content">
                  {{item.content}}
                  
                 </span>
                 <span class="showallcommon" @click="showcomment(index)" v-if="comment_isLong[index] && !comment_show_status[index]">全文</span>
                 <span class="hidecomment" @click="hidecomment(index)" v-if="comment_isLong[index] && comment_show_status[index]">收起</span>
               </p>
               <div class="time_assist">
                 <span class="time"><timeago :since="parseInt(item.create_time)*1000"></timeago></span></span>
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
                        <!-- {{reply_item}} -->
                      <!-- {{reply_parent_userid[reply_item.parent_id]}} -->
                      
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
       <p class="join_cue" v-if="showCommunity">参加后可查看全部<span class="red_num">{{comment_num}}</span>条小讲圈</p>
       <router-link :to="'/SmalltalkComments/'+smalltalkid" v-if="!showCommunity">
         <p class="smalltalk-comments">~ 查看所有小讲圈 ~</p>
       </router-link>
      </div>
      <div class="jaingring" v-if="comment_num==0">
        <span class="samllring">小讲圈</span>
        <div class="no-comment">暂时还没有小讲圈</div>
      </div>
      <!-- 专题 如果不是专题要隐藏-->
      <div class="subject_albums" v-if="talk_special_list.length!=0">
        <h2>本小讲收录于专题</h2>
        <div class="subhead_box" @click="toStalkcon(talk_special_list.id)">
          <img :src="$sourceUrl+'/img/'+talk_special_list.special_img" class="subhead_pic">
          <div class="subname">
            <p>
              <span class="sub_lable">专题</span>
              <span class="class_name">{{talk_special_list.title}}</span>
            </p>
            <p class="zhujiang">{{talk_special_list.introduce}}</p>
            <p class="sub_num">
              <span>共{{special_smalltalk_list.length+1}}讲</span>
              <span>{{special_join_num}}次参加</span>
            </p>
          </div>
        </div>
      </div>
      <div class="other_sub">
        <div class="other_head">
          <!-- <div class="one_ti"> -->
            <div class="shead_box">
              <img :src="vip_head_img" class="shead_pic">
              <img src="../assets/img/daV.jpg" v-if="vip_is_real==2" class="dav_pic">
            </div>
            <span class="artname">{{vip_real_name}}的其他小讲{{smalltalk_special_id}}</span>
          <!-- </div> -->
          <router-link :to="'/tutorDetails/'+vip_list.id"><span class="one_on">一对一提问></span></router-link>
        </div>
        <ul class="other_class_list">
          <li class="other_class" v-for="item in special_smalltalk_list" @click='toOtherStalk(item.id)'>
            <h2 class="class_list_name">{{item.title}}</h2>
            <p class="class_join_num">{{item.join_num}}人参加</p>
          </li>
        </ul>
      </div> 
      <div class="stalk_price" v-if="!giftshow&&!boughtstatus">
        <a class="giveta" @click="givegift()">
          <span>赠送</span>
        </a> 
        <a class="test_ting"  @click="shiting(smalltalkid,smalltalkaudio_list[0][0].id,smalltalkaudio_list[0][0].shiting)">
          <span>试听</span>
        </a> 
       <span class="sub_btn" @click="joinbtn(price)">￥{{price}} 参加</span>
      </div>
      <div class="stalk_price stalk_buy" v-if="!giftshow&&boughtstatus || info.vip_id==vip_list.id">
        <a class="giveta" @click="givegift()">
          <span>赠送</span>
        </a> 
        <a class="ting"  @click="playUnlisten()">
          <span></span>
        </a> 
        <a class="comment_btn" @click="toComment()">
          <span>评论</span>
        </a> 
      </div>

      <!-- 赠送点击弹出框 -->
      <mt-popup
        v-model="giftshow"
        position="bottom"
        class="giveclass">
        <div class="gift_menu_nav">
          <h2 class="gift_title">赠送好友</h2>
          <div class="gift_box">
            <span class="zengsong">赠送数量</span><span class="error_box"></span>
            <div class="fenshu">
              <span :class="number==1?'numbox':'numone'" @click="songfen(1)">1份</span>
              <span :class="number==2?'numbox':'numone'" @click="songfen(2)">2份</span>
              <span :class="number==5?'numbox':'numone'" @click="songfen(5)">5份</span>
              <span :class="number==10?'numbox':'numone'" @click="songfen(10)">10份</span>
              <!-- <span class="numone">10份</span>
              <div class="custom_num">
                <span class="haosong">豪爽赠送</span>
                <input class="songnum" type="number" name="giftnumber" placeholder="1-200">份
              </div> -->
            </div>
            <div class="sharenum"> 
             
              <div class="custom_num">
                <span class="haosong">豪爽赠送</span>
                <input class="songnum" v-model="number" type="number" name="giftnumber" placeholder="1-200">份
              </div>
            </div>
            <div class="allprice">
              <span class="zhongse">共计：</span>{{allprice}}
            </div>
            <p class="tishi">好友可免费领取，购买90天内名额有效，过期不退款</p>
          </div>
          <div class="giveto_gift" @click="givefriend(allprice)">
            <span>赠送</span>
          </div>
        </div>
      </mt-popup>
      <!-- 未购买无法评论 -->
      <mt-popup
        v-model="promptpay"
        class="boughtcalss">
        <div class="boughtjoin">
          <div class="boughtbox">
            <p class="paytitle">支付 {{price}} 参加小讲</p>
            <p class="paycontent">参加后您将获得本场小讲中的：</p>
            <p class="paydetail">全部知识音频</p>
            <p class="paydetail">与主讲互动机会</p>
            <p class="paydetail">与高品质用户社群交流机会</p>
          </div>
          <div class="payorno">
            <span class="cancel" @click="cancelpay()">取消</span>
            <span class="sure"  @click="joinbtn(price)">购买</span>
          </div>
        </div>
      </mt-popup>
      <!-- 评论回复输入弹出框 -->
      <mt-popup v-model="inputText" class="input-popup">
        <div class="btn-wrap">
          <span class="btn-cancel" @click="inputText = false">取消</span>
          <span class="btn-submit" @click="inputComment()">提交</span>
        </div>
        <textarea class="content" v-model="textContent" @input="descInput" maxlength="100" placeholder="请输入你要评论的内容"></textarea>
        <div class="count">
          <span>{{textNum}}</span><span>/</span><span>100</span>
        </div>
      </mt-popup>
      <mt-popup class="tip-popup"
            v-model="textContentNull"
            popup-transition="popup-fade">评论不能为空哦!
      </mt-popup>
  </div>
</template>

<script type="es6">
import {mapState,mapMutations,mapGetters} from 'vuex';
import { Popup } from 'mint-ui';
import { Indicator } from 'mint-ui';
  export default {
    data () {
      return {
        id:1,
        conlineshow:false,
        shoutingstatus:0,
        giftshow:false,
        number:1,
        price:0,
        boughtstatus:false,
        vip_listen_num:'',
        vip_real_name:'',
        vip_is_real:'',
        vip_identity:'',
        vip_introduce:'',
        vip_head_img:'',
        smalltalk_special_id:'', //小讲专题id
        promptpay:false,  //没支付、没试听的权限  提示用户需要购买才能听
        comment_list:[],  //评论列表
        vip_list:[],  //Vip个人信息列表
        smalltalk_list:'',  //小讲内容
        smalltalkcate:'', //小讲分类    
        smalltalkcontent_list:[], //小讲导语部分
        smalltalkaudio_list:[],   // 小讲语音部分
        smalltalk_audio_listen:[], //小讲每个语音是否听完过
        comment_num:'',  //评论数量
        reply_list:[],  //回复列表
        reply_cut_list:[],   //回复列表未展开前显示2条
        reply_num:[],   //回复数量
        replynum_status:'',  //回复数量状态
        reply_name:[],   //回复用户名
        comment_name:[],   //评论用户名
        special_smalltalk_list:[],   //同专题小讲
        talk_special_list:[],   //本小讲的专题所在
        special_join_num:0,//本小讲的专题的参加人数
        smalltalkid:'',   //小讲id
        reply_parent_name:[],   //回复评论用户的名
        reply_parent_list:[],  //回复评论用户的列表
        reply_parent_content:[],
        reply_parent_userid:[],
        followed:false,
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
        comment_likeNum:[],//评论的点赞数量
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']),
      plus:function(){
        return this.number*this.price;
      },
      allprice:function(){
        return this.number*this.price;
      },
      showCommunity: function(){
        if(this.boughtstatus){
          return false;
        }else if(this.info.vip_id==this.vip_list.id){
          return false;
        }else{
          return true;
        }
      }
    },
    watch:{
      '$route' (to, from) {
        //同一页面路由跳转只会执行再init()，部分数据没有重新初始化
        this.init()
      }
    },
    created(){
      this.init()
    },
    methods:{  
      init:function(){ 
        Indicator.open();
        this.conlineshow = false;
        this.special_join_num = 0;
        this.smalltalkid=this.$route.params.id
        //用户是否购买该小讲
        if(this.isLogin){
            this.$http
           .get('/purchaseorder/'+this.smalltalkid,{params:{uid:this.info.user_id}})
           .then(rtnData=>{
              if(rtnData.data == null){
                this.boughtstatus = false;
              }else{
                this.boughtstatus = true;
              }

            })
            
        }
     
        this.commentInit();
        this.$http
        .get('smalltalkContent',{params:{smalltalk_id:this.smalltalkid}})
        .then(rtnData=>{
          this.smalltalkcontent_list=rtnData.data
        })
        .then(()=>{
          let smalltalkcontent=this.smalltalkcontent_list
          for (var i = 0; i < smalltalkcontent.length; i++) {
            let index=i;
            this.$http
            .get('smalltalkAudio',{params:{'index':smalltalkcontent[i].id}})
            .then(rtnData=>{
              this.$set( this.smalltalkaudio_list,index,rtnData.data)
              //先给小讲每个语音收听记录初始化
              this.$set(this.smalltalk_audio_listen,index,[false])
              //登录后根据小讲语音id访问语音收听表获取是否有听完过的id
              if(this.isLogin){
                for(var i=0;i<this.smalltalkaudio_list[index].length;i++){
                  let key = i;
                  this.$http
                  .get('smalltalkAudioListen/'+this.smalltalkaudio_list[index][i].id,{params:{uid:this.info.user_id}})
                  .then(rtnData=>{
                      if(rtnData.data == null){
                          this.$set(this.smalltalk_audio_listen[index],key,false) 
                      }else{                  
                          this.$set(this.smalltalk_audio_listen[index],key,true)
                      }
                  })
                 } 
              }
              
              
            })
          }
        }),
        //小讲内容
        this.$http
        .get('smalltalk/'+this.smalltalkid,{params:{type:'smalltalk'}})
        .then(rtnData=>{
          this.smalltalk_list = rtnData.data;
          this.price = rtnData.data.price;
          this.smalltalk_list.smalltalk_img = this.$sourceUrl+'/img/'+this.smalltalk_list.smalltalk_img;
        })
        .then(()=>{
          let vip_id=this.smalltalk_list.vip_id
           //Vip个人信息列表
          this.$http
            .get('/vip/'+vip_id,{params:{user_id:this.info.user_id}})
            .then(rtnData=>{
              this.vip_list = rtnData.data[0]
              this.vip_listen_num=rtnData.data[0].listen_num
              this.vip_real_name=rtnData.data[0].real_name
              this.vip_identity=rtnData.data[0].identity
              this.vip_introduce=rtnData.data[0].introduce
              this.vip_head_img=this.$sourceUrl+'/img/'+rtnData.data[0].head_img
              this.vip_is_real=rtnData.data[0].is_real
              if(rtnData.data[1]){
                this.followed = true;
              }
            })
        })
        .then(()=>{
          let smalltalk_cate_id=this.smalltalk_list.smalltalk_cate_id
          this.$http
          .get('smalltalkCate/'+smalltalk_cate_id)
          .then(rtnData=>{
            this.smalltalkcate = rtnData.data
          })
        })
        .then(()=>{
          let smalltalk_special_id=this.smalltalk_list.special_id
          this.$http
          .get('smalltalk/specialwen',{params:{smalltalk_special_id}})
          .then(rtnData=>{
            this.special_smalltalk_list=rtnData.data
            let special_smalltalk_list=rtnData.data
            let index = -1;
            for (var i = 0; i < special_smalltalk_list.length; i++) {
              this.special_join_num +=special_smalltalk_list[i].join_num; 
              if (special_smalltalk_list[i].id==this.smalltalkid) {
                index = i;
              }
            }
            this.special_smalltalk_list.splice(index,1);
          })
        })
        .then(()=>{
          let smalltalk_special_id=this.smalltalk_list.special_id 
          this.$http
          .get('special/'+smalltalk_special_id)
          .then(rtnData=>{
            this.talk_special_list=rtnData.data || []
            Indicator.close();
          })
        })
                    
      },
      //获取评论数据
      commentInit:function(){
        let smalltalk_id =this.$route.params.id  //小讲id
        this.comment_show_status = [];
        this.comment_isLong = [];
        this.comment_list = [];
        this.comment_isLike = [];
        // 小讲评论

        this.$http
        .get('comment',{params:{smalltalk_id}})
        .then(rtnData=>{
          this.comment_list = rtnData.data
          this.comment_num=rtnData.data.length
          if (this.comment_num>2) {
            this.comment_list=this.comment_list.slice(0, 2)
          } 

          for(var i=0;i<this.comment_list.length;i++){
            this.comment_show_status.push(false);
            if(this.comment_list[i].content.length > this.comment_maxShowAllLength){
              this.comment_isLong.push(true);
            }else{
              this.comment_isLong.push(false);
            }
            this.$set(this.comment_likeNum,i,this.comment_list[i].like_num); 
            this.comment_isLike.push(false);
            if(this.isLogin){
              let index = i;
              this.$http
              .get('/commentLike/'+this.comment_list[i].id,{params:{uid:this.info.user_id}})
              .then(rtnData=>{
                if(rtnData.data!=null){
                  this.$set(this.comment_isLike,index,true)
                }
              })
            }
          }
        })
        .then(()=>{
          this.replyInit();
        })
      },
      //获取回复数据
      replyInit:function(){
         let conmontlist=this.comment_list
          for (var i = 0; i < conmontlist.length; i++) {
            let user_id=conmontlist[i].user_id;
            this.$http
            .get('/user/'+user_id)
            .then(rtnData=>{
              this.$set(this.comment_name,user_id,rtnData.data.user_name)
            })  
          }
          for (var i = 0; i < conmontlist.length; i++) {
            let reply_id=parseInt(conmontlist[i].id) 
            this.$http
            .get('reply',{params:{reply_id}})
            .then(rtnData=>{
                this.$set(this.reply_list,reply_id,rtnData.data);
                this.$set(this.reply_num,reply_id,this.reply_list[reply_id].length);
                this.$set(this.reply_cut_list,reply_id,this.reply_list[reply_id]);
                if (this.reply_num[reply_id]>2) {
                  this.$set(this.reply_cut_list,reply_id,this.reply_list[reply_id].slice(0, 2));
                }
                // 用户回复评论
                for (var i = 0; i < this.reply_list[reply_id].length; i++) {
                  if (this.reply_list[reply_id][i].parent_id>0) {
                    let parent_id=this.reply_list[reply_id][i].parent_id
                    this.$http
                    .get('reply/'+parent_id)
                    .then(rtnData=>{
                     this.$set(this.reply_parent_userid,parent_id,rtnData.data.user_id);
                    })

                    .then(()=>{
                      let user_id=this.reply_parent_userid[parent_id]
                      this.$http
                      .get('/user/'+user_id)
                      .then(rtnData=>{
                        this.$set(this.reply_parent_name,user_id,rtnData.data.user_name);
                      })
                    })                
                  }
                  let user_id=this.reply_list[reply_id][i].user_id
                  this.$http
                  .get('/user/'+user_id)
                  .then(rtnData=>{
                    this.$set(this.reply_name,user_id,rtnData.data.user_name);
                  })
                }
            })
          }
      },
      showalline:function(){
        this.conlineshow=true;
      },
      hideline:function(){
        this.conlineshow=false;
      },
      joinbtn:function(price){
        if(this.isLogin){
          this.$router.push({path:'/answerToSubmit',query:{smalltalk_id:this.smalltalkid,type:'smalltalk',price:price}})  
        }else{
          this.$router.push('/login')  
        }
      },
      showIt:function(){
        if(this.isLogin){
          this.$http
          .put('/vip/'+this.smalltalk_list.vip_id,{followed:this.followed});
          this.$http
          .put('/vipListen/'+this.smalltalk_list.vip_id,{uid:this.info.user_id,followed:!this.followed});
          this.followed = !this.followed;
        }else{
          this.$router.push('/login');
        }
      },
      backstalk:function(){
        this.$router.push('/talk')
      },
      showcomment:function(index){
        this.$set(this.comment_show_status,index,true)
      },
      hidecomment:function(index){
         this.$set(this.comment_show_status,index,false)
      },
      shiting:function(id,n,a){
        if (this.boughtstatus || a>0 || this.info.vip_id==this.vip_list.id){
          this.$router.push({path:'/shiting',query: {id:id,n:n}})
        }
        else{
          this.promptpay=true;
        }
       
      },
      playUnlisten:function(){
        if(this.info.vip_id != this.vip_list.id){
          var playIndexI = -1;
          var playIndexJ = -1;

          for(var i=0;i<this.smalltalk_audio_listen.length;i++){
            if(!this.smalltalk_audio_listen[i]){
              continue;
            }
            for(var j=0;j<this.smalltalk_audio_listen[i].length;j++){
              if(!this.smalltalk_audio_listen[i][j]){
                playIndexI = i;
                playIndexJ = j;
                break;
              }
            }
            if(playIndexI!= -1){
              break;
            }
          }
          if(playIndexI == -1){
              playIndexI = 0;
              playIndexJ = 0;
          }
          
          this.$router.push({path:'/shiting',query: {id:this.smalltalkid,n:this.smalltalkaudio_list[playIndexI][playIndexJ].id}})
        }else{
          this.$router.push({path:'/shiting',query: {id:this.smalltalkid,n:this.smalltalkaudio_list[0][0].id}})
        }
      },
      // 没有试听的权限 也没有购买
      unshiting:function(){
        if(this.boughtstatus==false){
          this.promptpay=true;
        }
      },
      givegift:function(){
        this.giftshow=true;
      },
      givefriend:function(n){
      },

      songfen:function(n){
        this.number=n;
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
        if(this.boughtstatus==true || this.info.vip_id == this.vip_list.id){
          this.inputText = true;
          this.inputCommentId = commentId;
          this.inputReplyId = replyId;
        }else{
          this.promptpay=true;
        }
      },
      // 取消购买课程
      cancelpay:function(){
        this.promptpay=false;
      },
      toStalkcon:function(n) {
        this.$router.push('/stalkcon/'+n)
      },
      toOtherStalk:function(n){
        this.$router.push('/stalkteacher/'+n)
      },
      //展开所有讨论
      showart:function(index){
        this.$set(this.reply_cut_list,index,this.reply_list[index])
      },
      //隐藏部分讨论
      hideart:function(index){
         this.$set(this.reply_cut_list,index,this.reply_list[index].slice(0, 2))
      },
      //统计输入评论内容的长度
      descInput:function(){
        this.textNum = this.textContent.length;
      },
      //点击评论弹出评论框
      toComment:function(){
        this.inputText = true;
        this.inputCommentId = 0;
      },
      //提交评论
      inputComment:function(){
        if(this.textContent.length != 0){
          if(this.inputCommentId == 0){
            this.$http
            .post('comment',{smalltalk_id:this.smalltalkid,content:this.textContent,uid:this.info.user_id})
            .then(rtnData=>{
              this.textContent = '';
              this.inputText = false;
              this.textNum = 0;
              this.commentInit();  
            })
          }else{
            this.$http
              .post('reply',{comment_id:this.inputCommentId,content:this.textContent,parent_id:this.inputReplyId,uid:this.info.user_id})
              .then(rtnData=>{
                this.textContent = '';
                this.inputText = false;
                this.textNum = 0;
                this.replyInit();
              })
          }
        }else{
          this.textContentNull = true;
        }
        
      }

    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style >

  @import '../assets/css/stalkteacher.css';
  
</style>