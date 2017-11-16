<template>
      <div class="tutorDetails">
        <div class="tutorDetails-top">
          <div class="tutorDetails-top-header">
            <div class="tutorDetails-avatar">
              <img :src="vip_details.head_img">
              <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true">
            </div>
            <div class="tutorDetails-wrap" v-model="followed">
              <div class="tutorDetails-wrap-nickname">{{vip_details.real_name}}</div>
              <div class="tutorDetails-wrap-followers-count">
                <span>{{vip_details.listen_num}}</span>
                <span>人收听</span>
              </div>
            </div>
            <div v-if="info.vip_id!=id">
              <div class="tutorDetails-follow" @click="showIt()" v-show="!followed"></div>
              <div class="tutorDetails-followed" @click="showIt()" v-show="followed"></div>
            </div>
          </div>
          <div class="tutorDetails-top-title">
            <p class="title">{{vip_details.identity}}</p>
            <p class="introduction">{{vip_details.introduce}}</p>
            <div class="answer-probability"><span>暂无回答概率</span></div>
          </div>
          <div class="tutorDetails-tags">
            <router-link :to="'/category/'+vip_details.vip_cate_id">{{vip_details.cate_name}}</router-link>
          </div>
          <div  v-if="info.vip_id!=id" class="tutorDetails-question" @click="toAsk()">¥ {{vip_details.price}}提问</div>
        </div>
        <ul class="tutorDetails-speeches" v-show="vip_smalltalk.length != 0">
          <li class="tutorDetails-speeches-title">小讲</li>
          <li class="tutorDetails-smalltalk" v-for="item in vip_smalltalk">
             <router-link :to="'/stalkteacher/'+item.id">
              <div class="speech-title">{{item.title}}</div>
              <div class="speech-participants-count">
                <span>{{item.join_num}}</span>
                <span>人参加</span>
              </div>
            </router-link>
          </li>
        </ul>
        <ul class="tutorDetails-questions">
          <li class="tutorDetails-questions-title">
            <span>问答</span>
            <div class="tutorDetails-questions-top">
              <!-- <a href="" class="tutorDetails-questions-search"></a> -->
              <div class="drop-wrap" @click="actived = !actived">
                <span class="drop-btn">{{dropTip[dropTipNum]}}</span>
                <i class="icon-arrow" :class="{'active':actived}"></i>
                <div class="drop-menu" v-show="actived">
                  <div class="arrow-up"></div>
                  <div class="drop-item" :class="{'current':dropTipNum == index}" v-for="(item,index) in dropTip" @click="chooseIt(index)">{{item}}</div>
                </div>
              </div>
            </div>
          </li>
          <li class="tutorDetails-statistics">
            <span class="tutorDetails-item">
              <span>问答</span>
              <span>{{ask_num}}</span>
            </span>
            <span class="tutorDetails-item">
              <span>听过</span>
              <span>{{ask_listen_num}}</span>
            </span>
          </li>
          <li class="tutorDetails-questions-item" v-if="vip_headline != null">
            <router-link :to="'/headline-details/'+vip_headline.id+'?type=headline'">
              <div class="Headlines">
                <img src="../assets/img/tutorDetails-headlines.png" class="avatar">
                <p class="Headlines-content content">{{vip_headline.title}}</p>
              </div>
              <div class="question-answer">
                <img :src="vip_details.head_img" class="question-answer-avatar avatar">
                <p class="question-answer-content content" :class="{'fold': tip == '全文'}">{{vip_headline.summary}}</p>
              </div>
            </router-link>
            <!-- <span class="fold-tip" @click="foldTip()">{{tip}}</span> -->
            <div class="question-footer">
              <span><timeago :since="parseInt(vip_headline.create_time)*1000"></timeago></span>
              <span @click="toHeadline()">
                Ta的头条
                <i class="icon-arrow"></i>
              </span>
            </div>
          </li>
          <li class="a-question" v-for="(item,index) in vip_ask">
            <mt-popup
             v-model="likeUp"
             popup-transition="popup-fade">问题还没听过!
            </mt-popup>
            <div class="Headlines">
                <img :src="$sourceUrl+'/img/'+item.head_img" class="avatar">
                <router-link :to="'/problemDetails/'+item.id">
                  <p class="Headlines-content content">{{item.ask_content}}</p>
                  <p class="offer">￥{{item.price}}</p>
                </router-link>
            </div>
            <div class="question-answer">
              <img :src="vip_details.head_img" class="avatar">
              <div class="audio-answer" v-if="item.answer_flag == 1">
                <a @click="payToListen(item.id,item.user_id)" class="bubble bubble-green" v-show="!listened[index]">
                  <span class="bubble-tail"></span>
                  <span class="wave3"></span>
                  <span class="bubble-tip">1元偷偷听</span>
                </a>
                <myyuyin v-show="listened[index]" :index="index" :isplay="isplay" :showDuration="showDuration"  @stopPlay="stopPlay" :src="$sourceUrl+'/audio/'+item.answer_content"></myyuyin>
              </div>
              <div class="text-answer" v-if="item.answer_flag == 2">
                <a @click="payToListen(item.id,item.user_id)" v-show="!listened[index]">
                  <span class="pay-look">1元偷偷看</span>
                </a>
                <my-text v-show="listened[index]" :flag="item.answer_flag" :content="item.answer_content"></my-text>
              </div>
            </div>
            <div class="question-footer">
              <span class="time"><timeago :since="parseInt(item.create_time)*1000"></timeago></span>
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
        <div class="tutorDetails-layui" v-show="cancel">
          <mt-popup
            v-model="ask"
            popup-transition="popup-fade">问题不能为空哦!
          </mt-popup>
          <div class="layui-m-layermain">
            <div class="layui-m-layersection">
              <div class="layui-m-layercont">
                <div class="postLayer">
                  <div class="tutor-question-form">
                    <div class="btn-wrap">
                      <span class="btn-cancel" @click="cancel = false">取消</span>
                      <span class="btn-submit" @click.stop="answerToSubmit()">提交</span>
                    </div>
                    <div class="form" >
                     <textarea placeholder="提问可选公开或私密，公开提问的回答每被偷听1次，你分成¥0.5。" maxlength="80" rows="8" id="askIt" @input="descInput" v-model="textContent"></textarea>
                      <div class="footer">
                        <span class="price">
                          <span>¥ </span>
                          <span>{{vip_details.price}}</span>
                        </span>
                        <div class="footer-right">
                          <span class="btn-image">
                            <input type="file" name="file">
                          </span>
                          <span class="count">
                            <span>{{textNum}}</span><span>/</span><span>80</span>
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="items">
                      <div class="item">
                        <span>公开提问</span>
                        <mt-switch v-model="isPublic" size="small"></mt-switch>
                      </div>
                      <div class="item with-arrow">
                        <span>提问须知</span>
                        <i class="footer-item-icon"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
      tip:'全文',     //全文、收缩
      actived:false,  //默认
      listened:[], //点赞
      followed:false, //收听
      cancel:false,   //取消
      ask:false,      //提问
      isPublic:true,  //是否公开提问
      textNum:0,      //字数
      textContent:'', //文本内容
      vip_details:{},
      vip_smalltalk:[],
      vip_headline:{},
      vip_ask:[],
      ask_num:0,
      ask_listen_num:0,
      //like:false,
      vip_id: '',
      isplay:-1,                    //-1：音频暂停
      showDuration:false,           //显示时长
      dropTip:['默认','最新','热门'], //排序
      dropTipNum:0,
      like:[],
      likeUp:false, //弹窗
      id:'',
      //listen_num:0,
      flag:[]
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
    isplay:function(newVal){
      if(this.flag[newVal] == false){
        this.$http
        .post('askListen',{uid:this.info.user_id,askId:this.vip_ask[newVal].id,vip_id:this.$route.params.id})
        .then(()=>{
          this.vip_ask[newVal].listen_num = ++this.vip_ask[newVal].listen_num;
          this.$set(this.vip_ask,newVal,this.vip_ask[newVal])
          this.$set(this.flag,newVal,true);
          this.ask_listen_num = ++this.ask_listen_num;
        });
      }
    }
  },
  methods:{
    init:function(){
      Indicator.open();
      this.id = this.$route.params.id;
      this.$http
      .get('/vip/'+this.$route.params.id,{params:{user_id:this.info.user_id}})
      .then(rtnData=>{
        this.vip_details = rtnData.data[0];
        this.vip_details.head_img = this.$sourceUrl+'/img/'+this.vip_details.head_img;
        this.vip_id = this.vip_details.id;
        if(rtnData.data[1]){
          this.followed = true;
        }
        document.title = this.vip_details.real_name;
      });
      this.$http
        .get('/smalltalk/'+this.$route.params.id,{params:{type:'vip'}})
        .then(rtnData=>{
          this.vip_smalltalk = rtnData.data;
      });
      this.$http
        .get('/headline/'+this.$route.params.id,{params:{type:'vip'}})
        .then(rtnData=>{
          this.vip_headline = rtnData.data;
      });
      this.$http
      .get('/ask/'+this.$route.params.id,{params:{type:'vip',uid:this.info.user_id,vid:this.info.vip_id}})
      .then(rtnData=>{
        this.vip_ask = rtnData.data;
        this.ask_num = this.vip_ask.length;
        var num=0;
        for(var i=0;i<this.vip_ask.length;i++){

          num += this.vip_ask[i].listen_num;
          if(this.isLogin){
            let index = i;
            if(this.vip_ask[i].vip_id == this.info.vip_id){
              this.$set(this.listened,i,true)
            }else if(this.vip_ask[i].user_id == this.info.user_id){
              this.$set(this.listened,i,true)
            }else{
              this.$http
              .get('/purchasebuy/'+this.vip_ask[i].id,{params:{uid:this.info.user_id}})
              .then(rtnData=>{
                if(rtnData.data == null){
                  this.$set(this.listened,index,false)
                }else{
                  this.$set(this.listened,index,true)
                }
              });
            }
            this.$http
            .get('/askListen/'+this.vip_ask[index].id,{params:{uid:this.info.user_id}})
            .then(rtnData=>{
              if(rtnData.data == null){
                this.$set(this.flag,index,false)
              }else{
                this.$set(this.flag,index,true)
              }
            });
            this.$http
            .get('/askLike/'+this.vip_ask[i].id,{params:{uid:this.info.user_id}})
            .then(rtnData=>{
              if(rtnData.data == null){
                this.$set(this.like,index,false)
              }else{
                this.$set(this.like,index,true)
              }
            });
          }
        }
        this.ask_listen_num = num;
        Indicator.close();
      });
    },
    showIt:function(){
      //console.log(this.$route.params.id)
      if(this.isLogin){
        this.$http
        .put('/vip/'+this.$route.params.id,{followed:this.followed});
        this.$http
        .put('/vipListen/'+this.$route.params.id,{uid:this.info.user_id,followed:!this.followed});
        if(!this.followed){
          this.vip_details.listen_num+=1;
        }else{
          this.vip_details.listen_num-=1;
        }
        this.followed = !this.followed;
      }else{
        this.$router.push('/login')
      }
    },
    // foldTip:function(){
    //   if(this.tip == '全文'){
    //     this.tip = '收起';
    //   }else{
    //     this.tip = '全文';
    //   }
    // },
    toLike:function(index){
      if(this.listened[index]){
        if(this.like[index]){
          return ;
        }
        // console.log(this.vip_ask[index]);
        this.vip_ask[index].like_num +=1;
        this.$set(this.like,index,true);
        this.$http
          .post('askLike',{uid:this.info.user_id,askId:this.vip_ask[index].id});
        this.$http
          .put('/ask/'+this.vip_ask[index].id)
          .then(rtnData=>{});  
      }else{
        this.likeUp = true;
      }
    },
   /* askSubmit:function(n){
      if(this.textNum == 0){
          this.ask = true;
      }else{
        this.$router.push('/answerToSubmit/'+n);
      }
    },*/
    answerToSubmit:function(){
      if(this.textNum == 0){
        this.ask = true;
      }else{
        this.$router.push({path:'/answerToSubmit',query:{id:this.$route.params.id,type:'ask',price:this.vip_details.price,is_public:this.isPublic,ask_content:this.textContent}}) 
      }     
    },
    descInput:function(){
      this.textNum = this.textContent.length;
    },
    toHeadline:function(){
      this.$router.push('/headline/'+this.vip_id);
    },
    toAsk(){
      if(this.isLogin){
        this.cancel = true;
      }else{
        this.$router.push('/login');
      }
    },
    //切换语音
    stopPlay:function(index){
      this.isplay = index;
    },
    chooseIt:function(index){
      this.dropTipNum = index;
      this.$http
        .get('/ask/'+this.$route.params.id,{params:{type:this.dropTip[index],uid:this.info.user_id,vid:this.info.vip_id}})
        .then(rtnData=>{
          this.vip_ask = rtnData.data;
        })
    },
    payToListen:function(id,uid){
      if(this.isLogin){
        this.$router.push({path:'/answerToSubmit',query:{type:'askbuy',price:1,ask_id:id,vip_id:this.$route.params.id,ask_uid:uid}})  
      }else{
        this.$router.push('/login');
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  @import '../assets/css/tutorDetails.css';


</style>