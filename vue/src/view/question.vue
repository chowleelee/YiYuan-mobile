<template>
  <div class="question">
    <div class="question-list">
      <div class="container">
        <div class="question-header">
          <img :src="$sourceUrl+'/img/'+ask_info.head_img" alt="">
          <span class="user-name">{{ask_info.user_name}}</span>
          <span class="price">￥{{ask_info.price}}</span>
        </div>
        <div class="content">
          <span class="public" v-if="public">{{public}}</span>
          <p class="question-c">{{ask_info.ask_content || ask_info.content}}</p>
        </div>
        <div class="question-refuse" v-if="type=='ask'">
          <span @click="refuse()">婉拒</span>
        </div>
        <div class="question-footer">
          <span class="time">
            <timeago :since="parseInt(ask_info.create_time)*1000" class="fr"></timeago>
          </span>
        </div>
      </div>
    </div>
    <!-- <div class="audio">
      <div class="hearder">
        <span class="start" @click="showbox()">
          <input type="file" @change='onUpload'>
        </span>
        <span class="restart">重录</span>
      </div>
      <p>点击开始录音，最多可录制60"</p>
      <span class="btn" @click="do_restart()">确定发送</span>
    </div> -->

    <div class="answer-method">
      <h3>请选择回答方式</h3>
      <mt-navbar v-model="selected">
        <div class="method-type">
          <mt-tab-item id="yuyin">语音</mt-tab-item>
          <mt-tab-item id="text">文字</mt-tab-item>
        </div>
      </mt-navbar>
      <mt-tab-container v-model="selected">
        <mt-tab-container-item id="yuyin" class="yuyin">
          <div class="audio">
            <div class="hearder">
              <span class="start" @click="showbox()">
                <input type="file" @change='onUpload'>
              </span>
              <!-- <span class="restart">重录</span> -->
            </div>
            <p>点击开始录音，最多可录制60"</p>
          </div>
        </mt-tab-container-item>
        <mt-tab-container-item id="text" class="text">
          <div class="text-method">
            <textarea class="content" v-model="textContent" @input="descInput" maxlength="100" name="textContent" placeholder="请输入你要回答的内容"></textarea>
            <div class="count">
              <span>{{textNum}}</span><span>/</span><span>100</span>
            </div>
          </div>
        </mt-tab-container-item>
      </mt-tab-container>
      <span class="btn" @click="do_restart()">确定发送</span>
    </div>


    <div class="downapp" v-if="showdown" @click="hidevip()">
      <div class="appdata">
        <div class="app_box">
          <div class="app_hint" style="color:#ccc;font-size:1.5rem;">
            只有在分答APP才能开通答主哦
          </div>
          <div class="app_down" @click="hidevip()" style="color:red">
            去APP
          </div>
        </div>
      </div>
    </div>
    <div class="tips">
      <mt-popup
        v-model="show"
        popup-transition="popup-fade">{{tip}}
      </mt-popup>
    </div>
  </div>
    
</template>

<script type="es6">
 import {mapMutations,mapState} from 'vuex'
 import { Toast } from 'mint-ui' 
 import { MessageBox } from 'mint-ui'

export default {
  data () {
    return {
        selected:'yuyin',//选择回答方式
        textContent:'',//文字回答的文字内容
        textNum:0,//文字回答的文字内容长度
　　　　showdown:false,
        ask_info:{},
        public:'',
        audio:null,
        show:false,
        tip:''
    }
  },
  components:{

  },
  computed:{
    ...mapState(['info'])
  },
  created(){
    this.type = this.$route.query.type;
    this.id = this.$route.query.id;
  },
  mounted(){
    this.$http
    .get("ask",{params:{type:this.type,id:this.id}})
    .then(rtnData => {
      this.ask_info = rtnData.data;
      if(this.type=='ask'){
        if(this.ask_info.is_public==1){
          this.public = '公开';
        }else{
          this.public = '私密';
        }
      }
      if(this.ask_info.is_anonymous==2){
        this.ask_info.user_name = '匿名用户';
      }
    })
  },
  methods:{
    do_restart:function(){
      this.$http.interceptors.request.eject(this.$myInterceptor);
      let fd = new FormData(); 
      if(this.selected == 'yuyin'){
        if(this.audio!=null){
          fd.append('file', this.audio);
          fd.append('answer_flag', 1);
        }else{
          this.show = true;
          this.tip = '请先录音';
          return;
        }
      }else if(this.selected == 'text'){
        if(this.textNum == 0){
          this.show = true;
          this.tip = "回答内容不能为空";
        }else{
          fd.append('answer_content',this.textContent);
          fd.append('answer_flag', 2);
        }
      }
      fd.append('type', this.type);
      fd.append('answer_method',this.selected);
      fd.append('id', this.id);
      fd.append('price', this.ask_info.price);
      fd.append('vip_id', this.info.vip_id);

      this.$http({
        method: "post",
        url:'ask',
        data: fd,
        processData: false
      }).then(()=>{
        this.show = true;
        this.tip = "发送成功";
        if(this.type=='ask'){
          setTimeout(()=>{
            this.$router.push('/problemDetails/'+this.id);
          }, 1000) 
        }else{
          setTimeout(()=>{
            this.$router.push('/reward-details/'+this.id)
          }, 1000)
        }
      });
    },
    onUpload(e){
      this.audio = e.target.files[0];
    },
    showbox:function(){
      //this.showdown=true;
    },
    hidevip:function(){
      this.showdown=false;
    },
    //统计输入评论内容的长度
    descInput:function(){
      this.textNum = this.textContent.length;
    },
    refuse:function(){
      this.$http
      .put("ask/"+this.id,{type:this.type})
      .then(rtnData => {
        this.show = true;
        this.tip = "已拒绝";
          setTimeout(()=>{
            this.$router.push('/answer')
          }, 1000)
      })
    }
  }
      
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/question.css';

</style>