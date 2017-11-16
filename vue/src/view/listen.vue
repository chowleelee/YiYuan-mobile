<template>
    <div class="listen">
      <mt-navbar v-model="selected" :fixed="true">
        <mt-tab-item id="listen-content">内容</mt-tab-item>
        <mt-tab-item id="listen-answer">答主</mt-tab-item>
      </mt-navbar>

      <!-- tab-container -->
      <mt-tab-container v-model="selected">
        <mt-tab-container-item id="listen-content" v-infinite-scroll="loadMore"  infinite-scroll-distance="10">
          <ul class="listen-content" ref="tes">
            <li class="a-feed" v-for="(item,index) in ask_list">
              <div class="header">
                <router-link :to="'/tutorDetails/'+item.vip_id">
                  <img :src="$sourceUrl+'/img/'+item.head_img" class="listen-content-avatar">
                </router-link>
                <router-link :to="'/problemDetails/'+item.id"><span class="header-title">{{item.real_name}}推荐话题：分答精选</span></router-link>
              </div>
              <router-link :to="'/problemDetails/'+item.id"><p class="audio-question-content">{{item.ask_content}}</p></router-link>

              <div class="footer">
                <div class="footer-answer" v-if="item.answer_flag == 1">
                  <a @click="payToListen(item.id,item.user_id,item.vip_id)" class="bubble bubble-green" v-show="!item.listened">
                    <span class="bubble-tail"></span>
                    <span class="wave3"></span>
                    <span class="bubble-tip">1元偷偷听</span>
                  </a>
                  <myyuyin v-show="item.listened" :index="index" :isplay="isplay" :showDuration="showDuration" :src="$sourceUrl+'/audio/'+item.answer_content" @stopPlay="stopPlay"></myyuyin>
                   
                </div>
                <div class="answer-text" v-if="item.answer_flag == 2">
                  <a @click="payToListen(item.id,item.user_id,item.vip_id)"  v-show="!item.listened">
                    <span class="pay-look">1元偷偷看</span>
                  </a>
                  <my-text v-show="item.listened" :flag="item.answer_flag" :content="item.answer_content"></my-text>
                </div>
                <span class="tips-right" :class="{'tips-text':item.answer_flag == 2&&item.listened}">听过 {{item.listen_num}}</span>
              </div>
            </li>
          </ul>
        </mt-tab-container-item>
        
        <mt-tab-container-item id="listen-answer" v-if="isLogin == false">
          <div class="tips">
            <p>本页面需要登录后才能查看</p>
            <router-link to="/login" class="tip-btn">
              现在登录
            </router-link>
          </div>
        </mt-tab-container-item>
        <mt-tab-container-item id="listen-answer" v-if="isLogin == true">
          <ul class="leader-list">
            <li class="recommend" v-for="item in listen_list">
              <router-link :to="'/tutorDetails/'+item.id" class="listen-answer-info">
                <div class="avatar">
                  <img :src="$sourceUrl+'/img/'+item.head_img">
                  <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true" v-if="item.is_real==2">
                </div>
                <div class="info-wrap">
                  <p class="nickname">{{item.real_name}}</p>
                  <p>{{item.identity}}</p>
                  <p>
                    <span>
                      <span><!-- 123132</span>
                      <span>个回答， -->收听 {{item.listen_num}}</span>
                    </span>
                  </p>
                </div>
              </router-link>
            </li>
          </ul>
          <div class="recomend-title-bgc">
            <div class="recomend-title">为您推荐</div>
          </div>
          <ul class="recomend-list">
            <li class="recomend" v-for="(item,index) in hot_vip_list">
              <router-link :to="'/tutorDetails/'+item.id" class="recomend-list-info">
                <div class="avatar">
                  <img :src="$sourceUrl+'/img/'+item.head_img">
                  <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true" v-if="item.is_real==2">
                </div>
                <div class="info-wrap">
                  <p class="nickname">{{item.real_name}}</p>
                  <p>{{item.identity}}</p>
                  <p>
                    <span>
                      <span><!-- 895</span>
                      <span>个回答， -->收听</span>
                      <span>{{item.listen_num}}</span>
                    </span>
                  </p>
                </div>
              </router-link>
              <div class="listen-follow" @click="hasFollowed(index)" v-show="!followed[index]"></div>
              <div class="listen-followed" @click="hasFollowed(index)" v-show="followed[index]"></div>
            </li>
          </ul>
        </mt-tab-container-item>
      </mt-tab-container>
    </div>
</template>

<script type="es6">
import {mapState,mapMutations,mapGetters} from 'vuex';
import myyuyin from '@/components/yuyin';
import myText from '@/components/text';
import { Indicator } from 'mint-ui'
export default {

  data () {
    return {
      selected:'listen-content',    //切换选项卡
      followed:[],       //收听
      isplay:-1,                    //-1：音频暂停
      showDuration:true,           //显示时长
      ask_list:[],                  //提问列表
      listen_list:[],                //收听用户列表
      hot_vip_list:[],              //推荐答主列表
      ask_page:1,
      lock : true, //异步锁
      //listened:[], //点赞
      yuyinSrc:'../../static/audio/8310.mp3',//语音路径
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
      if(this.ask_list[newVal].flag == false){
        this.$http
        .post('askListen',{uid:this.info.user_id,askId:this.ask_list[newVal].id,vip_id:this.ask_list[newVal].vip_id})
        .then(()=>{
          this.ask_list[newVal].listen_num = ++this.ask_list[newVal].listen_num;
          this.ask_list[newVal].flag = true;
          this.$set(this.ask_list,newVal,this.ask_list[newVal])
        });
      }
    }
  },
  methods:{
    init:function(){
      //console.log(this.uinfo)
      this.getAsk();
      if(this.isLogin){
        this.$http
        .get('vipListen',{params:{id:this.info.user_id}})
        .then(rtnData=>{
          // console.log(rtnData.data)
          this.listen_list = rtnData.data;
          this.$http
            .get('vip',{params:{type:'hotList',id:this.info.user_id}})
            .then(rtnData=>{
                var listen_list_vip_id = [];
                for(var i=0;i<rtnData.data[1].length;i++){
                  listen_list_vip_id.push(rtnData.data[1][i].vip_id)
                }
                var hot_vip_list_num = 0;
                for(var i=0;i<rtnData.data[0].length;i++){
                  if(listen_list_vip_id.indexOf(rtnData.data[0][i].id)==-1){
                    this.hot_vip_list.push(rtnData.data[0][i]);
                    hot_vip_list_num++;
                    this.followed.push(false)
                  }
                  if(hot_vip_list_num>=3){
                    break;
                  }
                }
            })
        })
      }
    },
    hasFollowed:function(index){
      if(this.isLogin){
        this.$http
          .put('/vipListen/'+this.hot_vip_list[index].id,{followed:!this.followed[index],uid:this.info.user_id});
        this.$http
          .put('/vip/'+this.hot_vip_list[index].id,{followed:this.followed[index]});
        this.$set(this.followed,index,!this.followed[index])
      }else{
        this.$router.push('/login')
      }
    },
    //切换语音
    stopPlay:function(index){
      this.isplay = index;
    },
    getAsk:function(){
      this.lock = false
      if(typeof(this.ask_page) == 'undefined'){
        this.ask_page = 1;
      }
      Indicator.open();
      this.$http
        .get('ask',{params:{page:this.ask_page}})
        .then(rtnData=>{
          var list = rtnData.data.data;
          for(var i=0;i<list.length;i++){
            if(this.isLogin){
             let index = i;
             this.$http
               .get('/purchasebuy/'+list[index].id,{params:{uid:this.info.user_id}})
               .then(rtnData=>{
                  if(rtnData.data == null){
                    //this.$set(this.listened,index,false)
                    list[index].listened = false;
                    this.$set(list, index, list[index])
                  }else{
                    //this.$set(this.listened,index,true)
                    list[index].listened = true;
                    this.$set(list, index, list[index])
                    this.$http
                     .get('/askListen/'+list[index].id,{params:{uid:this.info.user_id}})
                     .then(rtnData=>{
                        if(rtnData.data == null){
                          //this.$set(this.flag,index,false)
                          list[index].flag = false;
                          this.$set(list, index, list[index])
                        }else{
                          //this.$set(this.flag,index,true)
                          list[index].flag = true;
                          this.$set(list, index, list[index])
                        }
                      });
                  }
                  this.ask_list.push(list[index]);
              });
            }else{
              this.ask_list.push(list[i]);
            }
          }
          /*if(this.ask_page == 1){
            this.ask_list = list;
          }else{
            this.ask_list.push(...list);
          }*/
          if (this.ask_page >= rtnData.data.last_page) {
            this.ask_page = -1;
          }
          this.lock = true

          Indicator.close();
        });
    },
    loadMore:function () {
      if(this.ask_page>=1 && this.lock == true){
        this.ask_page++;
        this.getAsk();
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
  @import '../assets/css/listen.css';

</style>