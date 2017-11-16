<template>
  <div class="my-quickask">
      <div class="list-nav" v-if="reward_length>0">
        <div class="container">
          <a @click="cancel()">取消</a>
          <a class="fabu" @click="yes()">确定</a>
        </div>
      </div>
      <div class="title-content">
          <div class="main-content">
            <p>{{ask_quickly_list.content}}</p>
          </div>
          <div class="footer">
            <span v-if="ask_quickly_list.status==1">还剩{{remainTime}}小时</span>
            <span v-if="ask_quickly_list.status==2">已解决</span>
            <span v-if="ask_quickly_list.status==3">已过期</span>
            <span v-if="ask_quickly_list.status==4">已关闭</span>
            <span v-if="ask_quickly_list.status==1"> | 已有{{ask_quickly_answer.length}}人抢答</span>
            <span v-if="ask_quickly_list.status==2"> | {{ask_quickly_answer.length}}人被选中</span>
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
            <div class="a-top">
              <router-link :to="'/tutorDetails/'+item.vip_id" class="img">
                <img :src="$sourceUrl+'/img/'+item.head_img" alt="">
              </router-link>
              <p>{{item.real_name}} | {{item.identity}}</p>
              <a class="top-rewards " :class="{'top-rewards-yes':reward[index]}" @click="rewards(index,item.id,item.vip_id)" v-if="ask_quickly_list.status==1">悬赏</a>
              <span class="top-price" v-if="ask_quickly_list.status!=1">￥{{item.price}}</span>
            </div>
            <div class="a-mid">
                <myyuyin :index="index" :isplay="isplay" :showDuration="showDuration" @stopPlay="stopPlay" v-if="item.answer_flag == 1" :src="$sourceUrl+'/audio/'+item.content"></myyuyin>
                <my-text v-if="item.answer_flag == 2" :flag="item.answer_flag" :content="item.content"></my-text>
            </div>
            <div class="a-btm">
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
          </li>
        </ul>
      </div>

      <div class="tips">
        <mt-popup
          v-model="show"
          popup-transition="popup-fade">悬赏人数不超过3人
        </mt-popup>
      </div>
  </div>
</template>

<script type="es6">

import myyuyin from '@/components/yuyin';
import {mapState,mapMutations,mapGetters} from 'vuex'
import myText from '@/components/text';

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
        reward:[],
        reward_length:0,
        show:false,
        data:[],
        price:'',
        flag:[],
        like:[]
      }
    },
    components:{
      myyuyin,
      myText
    },
    watch:{

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
        console.log(newVal);
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
        this.$http.get("/quickask/"+this.$route.params.id,{params:{type:'quickask'}}).then(rtnData=>{
          this.ask_quickly_list=rtnData.data;
          this.cate_id = this.ask_quickly_list.quickask_cate_id;

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
          for(let i = 0; i < this.ask_quickly_answer.length; i++){
            this.reward[i] = false;
            if(this.isLogin){
              let index = i;
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
      rewards:function(id,answer_id,vip_id){
        this.$set(this.reward,id,!this.reward[id]);
        if(this.reward[id] == true){
          this.reward_length++;
          this.data.push({
            answer_id:answer_id,
            vip_id:vip_id
          })
        }else{
          this.reward_length--;
          this.data.slice(id,1)
        }
        console.log(this.reward_length);
        if(this.reward_length>3){
          this.show = true;
          this.reward_length--;
          this.data.slice(id,1);
          this.$set(this.reward,id,!this.reward[id]);
        }
      },
      cancel:function(){
        this.reward_length = 0;
        for(let i = 0; i < this.reward.length; i++){
          this.reward[i] = false;
        }
      },
      yes:function(){
        this.$http.post("/quickaskAnswer",{data:JSON.stringify(this.data),quickask_id:this.ask_quickly_list.id}).then(rtnData=>{
            this.reward_length = 0;
            this.$http.get("/quickaskAnswer/"+this.$route.params.id).then(rtnData=>{
              for(var i = 0; i < rtnData.data.length; i++){
                this.$set(this.ask_quickly_answer,i,rtnData.data[i]);
              }
            //this.ask_quickly_answer=rtnData.data;
          })
        })
      },
      toLike:function(index){
        if(this.like[index]){
          return;
        }
        this.ask_quickly_answer[index].like_num +=1;
        this.$set(this.like,index,true);
        this.$http
          .post('quickaskLike',{uid:this.info.user_id,quickask_answer_id:this.ask_quickly_answer[index].id});
      }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/my-quickask.css';


</style>