<template>
      <div class="problemDetails">
        <div class="problemDetails-question">
          <div class="question-title">
            <div class="question-title-left">
              <!-- <router-link to="/tutorDetails"> -->
                <img :src="ask_list.head_img" class="avatar">
              <!-- </router-link> -->
              <span class="question-name">{{ask_list.user_name}}</span>
            </div>
            <span class="question-price">￥{{ask_list.price}}</span>
          </div>
          <div class="question-content">
            {{ask_list.ask_content}}
          </div>
          <div class="question-answer">
            <router-link :to="'/tutorDetails/'+vip.id">
              <div class="problemDetails-avatar">
                <img :src="vip.head_img">
                <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true">
              </div>
            </router-link>
            <div class="audio-answer" v-if="ask_list.answer_flag == 1">
              <a @click="payToListen(ask_list.id,ask_list.user_id,ask_list.vip_id)" class="bubble bubble-green" v-show="!listened">
                <span class="bubble-tail"></span>
                <span class="wave1"></span>
                <span class="wave2"></span>
                <span class="wave3"></span>
                <span class="bubble-tip">1元偷偷听</span>
              </a>
              <!-- <myyuyin v-show="listened" :index="0" :isplay="isplay" @stopPlay="stopPlay"></myyuyin> -->
              <myyuyin :index="0" :src="$sourceUrl+'/audio/'+ask_list.answer_content" :isplay="isplay" :showDuration="showDuration"  @stopPlay="stopPlay" v-show="listened"></myyuyin>
            </div>
            <div class="text-answer" v-if="ask_list.answer_flag == 2">
              <a @click="payToListen(ask_list.id,ask_list.user_id,ask_list.vip_id)" v-show="!listened">
                <span class="pay-look">1元偷偷看</span>
              </a>
              <my-text v-show="listened" :flag="ask_list.answer_flag" :content="ask_list.answer_content"></my-text>
            </div>
          </div>
          <div class="question-footer">
            <span class="time"><timeago :since="parseInt(ask_list.create_time)*1000"></timeago></span>
            <div class="footer-count">
              <span class="count">
                <span>听过</span>
                <span>{{ask_list.listen_num}}</span>
              </span>
              <span class="count" @click="toLike()">
                <i class="like-icon" v-show="!like"></i>
                <i class="like-icon-zan" v-show="like"></i>
                <span>{{ask_list.like_num}}</span>
              </span>
              <mt-popup
                v-model="likeUp"
                popup-transition="popup-fade">偷偷听之后才能点哦!
              </mt-popup>
            </div>
          </div>
        </div>
        <div class="tutor-card">
          <router-link :to="'/tutorDetails/'+vip.id">
            <div class="tutor-content">
              <p class="tutor-content-top">
                <span class="tutor-name">{{vip.real_name}}</span>
                <span class="count">
                  <span>{{vip.listen_num}}</span>
                  <span>人收听</span>
                </span>
              </p>
              <p class="tutor-title">{{vip.identity}}</p>
            </div>
          </router-link>
        </div>
        <div class="randomQuestion">
          <h3 class="title">值得一听</h3>
          <router-link :to="'/problemDetails/'+other_ask.id">
            <div class="question-normal">
              <p class="content">{{other_ask.ask_content}}</p>
              <p class="leader">
                <span>{{other_ask.real_name}} | {{other_ask.identity}}</span>
              </p>
              <p class="footer">
                <span><timeago :since="parseInt(other_ask.create_time)*1000"></timeago>回答</span>
                <span>
                  <span>听过</span>
                  <span>{{other_ask.listen_num}}</span>
                </span>
              </p>
            </div>
          </router-link>
        </div>
      </div>
</template>

<script type="es6">
import myyuyin from '@/components/yuyin';
import {mapState,mapMutations,mapGetters} from 'vuex';
import myText from '@/components/text';
import { Indicator } from 'mint-ui'
export default {
  data () {
    return {
      listened:false,
      ask_list:{},
      vip:{},
      other_ask:{},
      other_vip:{},
      like:false,
      likeUp:false, //弹窗
      flag:false,
      isplay:-1,                    //-1：音频暂停
      yuyinSrc:'../../static/audio/8310.mp3',//语音路径
      showDuration:true,
    }
  },
  created(){
    this.init();
  },
  components:{
    myyuyin,
    myText
  },
  computed:{
    ...mapState(['info']),
    ...mapGetters(['isLogin']) 
  },
  watch:{
    '$route' (to, from) {
      this.init()
    },
    isplay:function(newVal){
      console.log(this.flag);
      if(this.flag == false){
        this.$http
        .post('askListen',{uid:this.info.user_id,askId:this.$route.params.id,vip_id:this.ask_list.vip_id})
        .then(()=>{
          this.ask_list.listen_num = ++this.ask_list.listen_num;
          //this.$set(this.vip_ask,newVal,this.vip_ask[newVal])
          //this.$set(this.flag,newVal,true);
          this.flag = true;
        });
      }
    }
  },
  methods:{
    init:function(){
      Indicator.open();
      this.$http
        .get('/ask/'+this.$route.params.id,{params:{type:'ask'}})
        .then(rtnData=>{
          this.ask_list = rtnData.data;
          this.ask_list.head_img = this.$sourceUrl+'/img/'+this.ask_list.head_img;
          this.$http
            .get('/vip/'+this.ask_list.vip_id)
            .then(rtnData=>{
              this.vip = rtnData.data[0];
              this.vip.head_img = this.$sourceUrl+'/img/'+this.vip.head_img;
            })
          if(this.isLogin){
            if(this.ask_list.vip_id == this.info.vip_id){
              this.listened = true;
            }else if(this.ask_list.user_id == this.info.user_id){
              this.listened = true;
            }else{
              this.$http
              .get('/purchasebuy/'+this.ask_list.id,{params:{uid:this.info.user_id}})
              .then(rtnData=>{
                if(rtnData.data == null){
                  this.listened = false;
                  console.log(this.listened);
                }else{
                  this.listened = true;
                }
              });
            }
            this.$http
            .get('/askLike/'+this.ask_list.id,{params:{uid:this.info.user_id}})
            .then(rtnData=>{
              if(rtnData.data == null){
                this.like = false;
              }else{
                this.like = true;
              }
            });
            this.$http
            .get('/askListen/'+this.ask_list.id,{params:{uid:this.info.user_id}})
            .then(rtnData=>{
              if(rtnData.data == null){
                this.flag = false;
              }else{
                this.flag = true;
              }
            });
          }
          Indicator.close();
        })
      this.$http
        .get('/ask/'+this.$route.params.id,{params:{type:'other_ask'}})
        .then(rtnData=>{
          this.other_ask= rtnData.data;
        })
    },
    //切换语音
    stopPlay:function(){
      this.isplay = 0;
    },
    toLike:function(){
      if(this.listened){
        if(this.like){
          return;
        }
        this.ask_list.like_num +=1;
        this.like = true;
        this.$http
          .post('askLike',{uid:this.info.user_id,askId:this.ask_list.id});
        this.$http
          .put('/ask/'+this.ask_list.id)
          .then(rtnData=>{});  
      }else{
        this.likeUp = true;
      }
    },
    payToListen:function(id,uid,vid){
      if(this.isLogin){
        this.$router.push({path:'/answerToSubmit',query:{type:'asklisten',price:1,ask_id:id,ask_uid:uid}})  
      }else{
        this.$router.push('/login');
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>

  @import '../assets/css/problemDetails.css';
  


</style>