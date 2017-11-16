<template>
  <div class="stalkcon">
    
      <div class="stalkhead">
        <div class="headback">
          <router-link to="/allspeech"><span>&lt; 全部专题</span></router-link>
        </div>
        <div class="foottitle">
          <h2>{{speech_list.title}}</h2>
          <p>{{smalltalk_length}}期系列小讲·{{join_num}}次参加</p>
        </div>
      </div>
      <div class="container">
        <div class="conlist" v-if="speech_list.introduce">
          <div class="content_line">
            <p class="conline_one">
              {{speech_list.introduce}}
            </p>
            <p class="conline_two" v-if="conlineshow">
            </p>
            <span v-if="conlineshow" @click="hideline()">收起</span>
          </div>
          <div class="seeall_btn" v-if="!conlineshow">
            <span @click="showalline()">查看全部</span>
          </div>
        </div>
      <div class="class_list">
        <h2>课程列表</h2>
        <div class="teacher" @click="toTutor(speech_list.vip_id)">
          <div class="headpic">
            <img src="../assets/img/teacher1.jpg">
          </div>
          <div class="tea_name">
            <p class="name">主讲：{{speech_list.real_name}}</p>
            <p class="profession">{{speech_list.identity}}</p>
          </div>
        </div>
        <ul class="class_topic">
          <li v-for="item in smalltalk_list" class="classcon_list" @click="openclass(item.id)">
            <p class="caption">{{item.title}}</p>
            <span class="join_num">{{item.join_num}}人</span>
          </li>
        </ul>
      </div>
      <div class="stalk_price">
       <span class="price">￥{{price}}</span>
       <span class="sub_btn" @click="joinbtn(price)">参加全部</span>
      </div>
    </div>
  </div>
</template>

<script type="es6">


  export default {
    data () {
      return {
        conlineshow:false,
        speech_list:[],   //专题列表
        smalltalk_list:[],   //当前小讲列表
        smalltalk_length:'',   //专题小讲的期数
        vip_list:[],    //答主信息列表
        join_num:0,
        price:0
      }
    },
    computed:{

    },
    created(){
      this.init()
    },
    methods:{  
      init:function(){
        let smalltalk_special_id=this.$route.params.id
        // console.log(smalltalk_special_id)
        this.$http
        .get('Special/'+smalltalk_special_id)
        .then(rtnData=>{
          // console.log(rtnData)
          this.speech_list = rtnData.data
          // console.log(this.speech_list)
        })
        // .then(()=>{
        //   let vip_id=this.speech_list.vip_id
        //   //console.log(vip_id)
        //   this.$http
        //   .get('/Vip/'+vip_id)
        //   .then(rtnData=>{
        //     // console.log(rtnData.data)
        //     this.vip_list=rtnData.data[0]
        //   })
        // }),
        this.$http
        .get('api/Smalltalk/specialwen',{params:{smalltalk_special_id}})
        .then(rtnData=>{
          // console.log(rtnData.data)
          this.smalltalk_list = rtnData.data
          for(var i=0;i<this.smalltalk_list.length;i++){
            this.join_num+=this.smalltalk_list[i].join_num;
            this.price+=this.smalltalk_list[i].price;
          }
          this.smalltalk_length=rtnData.data.length
          //console.log(this.smalltalk_list)
          // console.log(this.smalltalk_length) 
        })
      },
      showalline:function(){
        this.conlineshow=true;
      },
      hideline:function(){
        this.conlineshow=false;
      },
      joinbtn:function(n){
         // this.$router.push('/answerToSubmit/'+n)
         //this.$router.push({path:'/answerToSubmit/'+n})
      },
      openclass:function(n){
        this.$router.push('/stalkteacher/'+n)
      },
      toTutor:function(n){
        this.$router.push('/tutorDetails/'+n)

      }
    }
  }
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

  @import '../assets/css/stalkcon.css';
  
</style>