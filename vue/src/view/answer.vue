<template>
  <div class="answer">
    <div class="list-nav container">
      <a :class="{'current':item.show==true}" @click="selected_title(item)" v-for="item in title_list">{{item.value}}</a>
      <div class="drop-wrap"  v-if="selected_num==1" @click="show_list()">
        <span v-html="selected_value"></span>
        <img src="../assets/img/answer1.png" alt="">
        <ul v-if="off">
          <li v-for="item in list" 
          @click="selected(item)" 
          :class="{'current':item.show==true}">{{item.value}}</li>
        </ul>
      </div>
    </div>

    <div class="main" v-if="selected_num==1">
      
      <div class="question-list">
        <ul v-if="length>0">
          <li v-for="item in ask_list" @click="question(item.id,item.status)">
            <div class="container">
              <div class="question-header">
                <img :src="$sourceUrl+'/img/'+item.head_img" alt="">
                <span class="user-name">{{item.user_name}}</span>
                <span class="price">￥{{item.price}}</span>
                <span class="state" v-if="item.status==1">待回答</span>
                <span class="state" v-if="item.status==2">已回答</span>
                <span class="state" v-if="item.status==3">已过期</span>
                <span class="state" v-if="item.status==4">已拒绝</span>
              </div>
              <p class="ques">{{item.ask_content}}</p>
              <div class="question-footer">
                <span class="time">
                  <timeago :since="parseInt(item.create_time)*1000" class="fr"></timeago>
                </span>
              </div>
            </div>
          </li>
        </ul>
        <div class="quickask" v-if="length==0">
          <div class="none-info">
            <img src="../assets/img/answer4.jpg" alt="">
            <p>未找到相关问题</p>
          </div>
        </div>
      </div>
    </div>

    <div class="quickask" v-if="selected_num==2">
      <div class="none-info" v-if="qa_length==0">
        <img src="../assets/img/answer4.jpg" alt="">
        <button class="btn" @click="gotoQA()">去抢答</button>
        <p>你还没有抢答过快问哦</p>
      </div>
      <ul class="qa-list" v-if="qa_length>0">
          <li class="a-question" v-for="(item,index) in qa_list">
            <div class="Headlines">
              <img :src="$sourceUrl+'/img/'+item.u_img" class="avatar">
              <router-link :to="'/reward-details/'+item.id">
                <p class="Headlines-content content">{{item.question}}</p>
                <p class="offer">￥{{item.price}}</p>
              </router-link>
            </div>
            <div class="question-answer">
              <img :src="$sourceUrl+'/img/'+item.v_img" class="avatar">
              <!-- <a href="" class="bubble bubble-green">
                <span class="bubble-tail"></span>
                <span class="wave3"></span>
                <span class="bubble-tip">点击播放</span>
              </a> -->
              <myyuyin v-if="item.answer_flag == 1" :index="index" :isplay="isplay" @stopPlay="stopPlay" :src="$sourceUrl+'/audio/'+item.content"></myyuyin>
              <my-text v-if="item.answer_flag == 2" :flag="item.answer_flag" :content="item.content"></my-text>
            </div>
            <div class="question-footer">
              <span class="time">
                <timeago :since="parseInt(item.create_time)*1000" class="fr"></timeago>
              </span>
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
  </div>
    
</template>

<script type="es6">
import myyuyin from '@/components/yuyin';
import {mapState,mapMutations,mapGetters} from 'vuex'
 import { Toast } from 'mint-ui' 
 import { MessageBox } from 'mint-ui'
import myText from '@/components/text';
import { Indicator } from 'mint-ui'
export default {
  data () {
    return {
      title_list:[
        {value: '我答', show: true},
        {value: '快问', show: false}
      ],
      list: [
　　　　{value: '全部', show: true},
　　　　{value: '待回答', show: false},
　　　　{value: '已回答', show: false},
　　　　{value: '未过审', show: false},
        {value: '已过期', show: false},
        {value: '已拒绝', show: false}
　　　],
      selected_value:"全部",
      off:false,
      selected_num:1,
      ask_list:[],
      //imgUrl:'',
      answered_list:[],
      unanswer_list:[],
      overdue_list:[],
      refuse_list:[],
      length:0,
      qa_list:[],
      qa_length:0,
      flag:[],
      like:[],
      isplay:-1
    }
  },
  components:{
    myyuyin,
    myText
  },
  watch:{
    isplay:function(newVal){
      if(this.flag[newVal] == false){
        this.$http
        .post('quickaskListen',{uid:this.info.user_id,quickask_answer_id:this.qa_list[newVal].qas_id,vip_id:this.qa_list[newVal].vip_id})
        .then(()=>{
          this.qa_list[newVal].listen_num = ++this.qa_list[newVal].listen_num;
          this.$set(this.qa_list,newVal,this.qa_list[newVal])
          this.$set(this.flag,newVal,true);
        });
      }
    }
  },
  computed:{
    ...mapState(['info']), //把vuex要用到的状态映射出来 
    ...mapGetters(['isLogin']) 
  },
  mounted(){
    Indicator.open();
    this.$http
    .get("ask",{params:{type:'answer',vid:this.info.vip_id}})
    .then(rtnData => {
      this.ask_list = rtnData.data;
      for (let i = rtnData.data.length - 1; i >= 0; i--) {
        if(rtnData.data[i].status==1){
          this.unanswer_list.push(rtnData.data[i]);
        }else if(rtnData.data[i].status==2){
          this.answered_list.push(rtnData.data[i]);
        }else if(rtnData.data[i].status==3){
          this.overdue_list.push(rtnData.data[i]);
        }else if(rtnData.data[i].status==4){
          this.refuse_list.push(rtnData.data[i]);
        };
      }
      this.ask_list = this.unanswer_list.concat(this.answered_list,this.overdue_list,this.refuse_list);
      this.length = this.ask_list.length;
      Indicator.close();
    })
    this.$http
    .get("quickaskAnswer",{params:{type:'qanswer',vid:this.info.vip_id}})
    .then(rtnData => {
      this.qa_list = rtnData.data;
      this.qa_length = this.qa_list.length;
      for(var i=0;i<this.qa_list.length;i++){
        if(this.isLogin){
          let index = i;
          
          if(this.qa_list[i].is_anonymous == 1){
            this.qa_list[i].u_img = '/static/img/headbg.png';
            console.log(12);
          }

          this.$http
          .get('/quickaskListen/'+this.qa_list[index].qas_id,{params:{uid:this.info.user_id}})
          .then(rtnData=>{
            if(rtnData.data == null){
              this.$set(this.flag,index,false)
            }else{
              this.$set(this.flag,index,true)
            }
          });
          this.$http
          .get('/quickaskLike/'+this.qa_list[index].qas_id,{params:{uid:this.info.user_id}})
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
  methods:{
  	show_list:function(){
      if(this.off==false){
        this.off=true
      }
      else{
        this.off=false
      }
    },
    selected_title:function(data){
      for(var i=0; i<this.title_list.length; i++){
        this.title_list[i].show=false
      }
      if(data.value=='我答'){
        this.selected_num=1
      }else{
        this.selected_num=2
        this.off = false;
      }
      data.show=true;
    },
    selected:function(data){
      for(var i=0; i<this.list.length; i++){
        this.list[i].show=false
      }
      data.show=true;
      this.selected_value=event.target.innerText;
      if(data.value=='全部'){
        this.ask_list = this.unanswer_list.concat(this.answered_list,this.overdue_list,this.refuse_list);
      }else if(data.value=='待回答'){
        this.ask_list = this.unanswer_list;
      }else if(data.value=='已回答'){
        this.ask_list = this.answered_list;
      }else if(data.value=='已过期'){
        this.ask_list = this.overdue_list;
      }else if(data.value=='已拒绝'){
        this.ask_list = this.refuse_list;
      }
      this.length = this.ask_list.length;
    },
    question:function(id,status){
      if(status==1){
        this.$router.push({path:'/question/',query:{id:id,type:'ask'}}) 
      }else if(status==2){
        this.$router.push('/problemDetails/'+id);
      }
    },
    gotoQA:function(){
      this.$router.push('/ask-quickly');
    },
    toLike:function(index){
      if(this.like[index]){
        return;
      }
      this.qa_list[index].like_num +=1;
      this.$set(this.like,index,true);
      this.$http
        .post('quickaskLike',{uid:this.info.user_id,quickask_answer_id:this.qa_list[index].qas_id});
    },
    //切换语音
    stopPlay:function(index){
      this.isplay = index;
    }
  }   	
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
@import '../assets/css/answer.css';

</style>