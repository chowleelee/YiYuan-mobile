<template>
  <div class="reward-details">
      <div class="title-content">
          <div class="header">
            <img :src="ask_quickly_list.head_img" alt="">
            <span class="user">{{ask_quickly_list.user_name}}</span>
            <span class="price">赏金￥{{ask_quickly_list.price}}</span>
          </div>
          <div class="main-content">
            <p>{{ask_quickly_list.content}}</p>
          </div>
          <div class="reply-panel" v-if="ask_quickly_list.status==1">
            <a class="reply-btn replyBtn " :class="{'access-to-answer':info.vip_id}" @click="answer(ask_quickly_list.id)" v-if="!hasAnswered">抢答</a>
            <a class="reply-btn replyBtn " v-if="hasAnswered">已抢答</a>
          </div>
          <div class="footer">
            <p class="disable-reason" v-if="!info.vip_id">答主可答</p>
            <p v-if="ask_quickly_list.status==1">还剩{{remainTime}}小时</p>
            <p v-if="ask_quickly_list.status==2">已解决</p>
            <p v-if="ask_quickly_list.status==3">已过期</p>
            <p v-if="ask_quickly_list.status==1">已有{{ask_quickly_answer.length}}人抢答，抢答被选中者可获得赏金。</p>
            <p v-if="ask_quickly_list.status!=1">{{ask_quickly_answer.length}}人被选中</p>
          </div>
      </div>

      <div class="reward-link">
        <div class="link-content">
          <div @click="to_rewards(cate_id)">
            <img :src="imgUrl" alt="">
            <p class="link-title">{{cate_title}}</p>
            <p class="info">{{cate_info}}</p> 
            <span>></span>
          </div>
        </div>
      </div>

      <div class="replies">
        <ul class="relies-content">
          <li class="a-relay" v-for="(item,index) in ask_quickly_answer">
            <div class="a-left">
               <router-link :to="'/tutorDetails/'+item.vip_id">
                <img :src="$sourceUrl+'/img/'+item.head_img" alt="">
              </router-link>
            </div>
            <div class="a-right">
              <div class="a-top">
                <p>{{item.real_name}} | {{item.identity}}</p>
                <span class="top-price" v-if="item.status==2 || item.status==3">￥{{item.price}}</span>
              </div>
              <div class="a-middle">
                <myyuyin :index="index" :src="$sourceUrl+'/audio/'+item.content" :isplay="isplay" :showDuration="showDuration"  @stopPlay="stopPlay" v-if="item.answer_flag == 1"></myyuyin>
                <my-text v-if="item.answer_flag == 2" :flag="item.answer_flag" :content="item.content"></my-text>
              </div>
              <div class="a-info">
                <span><timeago :since="parseInt(item.create_time)*1000"></timeago></span>
                <div class="footer-count">
                  <span class="count">
                    <span>听过</span>
                    <span>{{item.listen_num}}</span>
                  </span>
                  <span class="count" @click="toLike(index)">
                    <i class="like-icon" v-show="!like[index]"></i>
                    <i class="like-icon-zan" v-show="like[index]"></i>
                    <span>{{item.like_num}}</span>
                  </span>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
  </div>
</template>

<script type="es6">

import myyuyin from '@/components/yuyin';
import {mapState,mapMutations,mapGetters} from 'vuex'
import myText from '@/components/text';
import { Indicator } from 'mint-ui'
  export default {
    data () {
      return {
        isplay:-1,                    //-1：音频暂停
        bubbleTip:[],                 //语音文字提示
        showDuration:true,
        ask_quickly_list:[],
        ask_quickly_cate:[],
        ask_quickly_answer:[],
        content:"",
        cate_title:"",
        cate_info:"",
        cate_id:0,
        imgUrl:"",
        remainTime:0,
        flag:[],
        like:[],
        yuyinSrc:'../../static/audio/8310.mp3',//语音路径
        hasAnswered: false
      }
    },
    components:{
      myyuyin,
      myText
    },
    computed:{
        ...mapState(['info']), //把vuex要用到的状态映射出来 
        ...mapGetters(['isLogin']) 
    },
    created(){
      this.init()
    },
    watch:{
      isplay:function(newVal){
        if(this.flag[newVal] == false){
          this.$http
          .post('quickaskListen',{uid:this.info.user_id,quickask_answer_id:this.ask_quickly_answer[newVal].id,vip_id:this.ask_quickly_answer[newVal].vip_id})
          .then(()=>{
            this.ask_quickly_answer[newVal].listen_num = ++this.ask_quickly_answer[newVal].listen_num;
            this.$set(this.ask_quickly_answer,newVal,this.ask_quickly_answer[newVal])
            this.$set(this.flag,newVal,true);
          });
        }
      }
    },
    methods:{
      init:function(){
        Indicator.open();
        this.$http.get("/quickask/"+this.$route.params.id,{params:{type:'quickask'}}).then(rtnData=>{
          this.ask_quickly_list=rtnData.data;
          if(this.ask_quickly_list.is_anonymous == 1){
            //this.ask_quickly_list.head_img = '/static/img/headbg.png';
            this.ask_quickly_list.head_img = 'headbg.png';
            this.ask_quickly_list.user_name = '匿名用户'
          }
          this.cate_id = this.ask_quickly_list.quickask_cate_id;
          this.ask_quickly_list.head_img = this.$sourceUrl+'/img/'+this.ask_quickly_list.head_img;

          var nowTime = new Date().getTime();
          var duration  = this.ask_quickly_list.duration;
          var time = parseInt((nowTime - this.ask_quickly_list.create_time*1000)/1000/60/60);
          var remainTime = duration - time;

          this.remainTime = remainTime>0?remainTime:0;
          
        })
        .then(()=>{
          this.$http.get("/quickaskCate/"+this.cate_id).then(rtnData=>{
            this.ask_quickly_cate=rtnData.data;
            this.imgUrl=this.$sourceUrl+'/img/'+this.ask_quickly_cate[0].cate_img;
            this.cate_title=this.ask_quickly_cate[0].cate_name;
            this.cate_info=this.ask_quickly_cate[0].point;
          })
        })
        this.$http.get("/quickaskAnswer/"+this.$route.params.id).then(rtnData=>{
          this.ask_quickly_answer=rtnData.data;
          for(var i=0;i<this.ask_quickly_answer.length;i++){
            if(this.isLogin){
              let index = i;

              //判断该答主是否回答过此问题
              if(this.ask_quickly_answer[index].vip_id == this.info.vip_id){
                this.hasAnswered = true;
              }

              this.$http
              .get('/quickaskListen/'+this.ask_quickly_answer[index].id,{params:{uid:this.info.user_id}})
              .then(rtnData=>{
                if(rtnData.data == null){
                  this.$set(this.flag,index,false)
                }else{
                  this.$set(this.flag,index,true)
                }
              });
              this.$http
              .get('/quickaskLike/'+this.ask_quickly_answer[index].id,{params:{uid:this.info.user_id}})
              .then(rtnData=>{
                if(rtnData.data == null){
                  this.$set(this.like,index,false)
                }else{
                  this.$set(this.like,index,true)
                }
              });
            }
          }
          Indicator.close();
        })
      },
      //切换语音
      stopPlay:function(index){
        this.isplay = index;
      },
      to_rewards:function(n){
        this.$router.push('/rewards/'+n)
      },
      answer:function(id){ 
        if(this.isLogin){
          this.$router.push({path:'/question/',query:{id:id,type:'quickask'}}) 
          //this.$router.push('/question/'+id);
        }else{
          this.$router.push('/login')
        }
      },
      toLike:function(index){
        if(this.isLogin){
          if(this.like[index]){
            return;
          }
          this.ask_quickly_answer[index].like_num +=1;
          this.$set(this.like,index,true);
          this.$http
            .post('quickaskLike',{uid:this.info.user_id,quickask_answer_id:this.ask_quickly_answer[index].id});
          }else{
            this.$router.push('/login')
          }
      }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/reward-details.css';


</style>