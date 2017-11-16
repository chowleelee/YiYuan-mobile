<template>
  <div class="publish">
    <div class="list-nav">
      <div class="container">
        <a @click="to_uhome()">取消</a>
        <a class="fabu" @click="dopublish()">发布</a>
      </div>
    </div>
    <div class="article-title">
      <div class="container">
        <input type="text" placeholder="请输入文章标题" v-model="title">
      </div>
    </div>
    <div class="article-title article-summary">
      <div class="container">
        <input type="text" placeholder="请输入导语" v-model="summary">
      </div>
    </div>
    <div class="article-img">
      <div class="container">
        <img :src="imgUrl" title="＋点击请添加导图">
        <input type="file" @change='onUpload'>
      </div>
    </div>
    <div class="article-content">
      <div class="container">
        <textarea placeholder="请输入文章内容" type="textarea" rows="10" v-model="content"></textarea>
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
 import { Field } from 'mint-ui'

export default {
  data () {
    return {
　　　　title:"",
        summary:'',
        content:"",
        img:null,
        imgUrl:'',
        show:false,
        tip:''
    }
  },
  computed:{
    ...mapState(['info'])
  },
  methods:{
    to_uhome:function(){
      this.$router.push('/uhome')
    },
    dopublish:function(){
      if(!this.title){
        this.show = true;
        this.tip = '请输入标题';
      }else if(!this.summary){
        this.show = true;
        this.tip = '请输入导语';
      }else if(!this.img){
        this.show = true;
        this.tip = '请添加导图';
      }else if(!this.content){
        this.show = true;
        this.tip = '请输入内容';
      }else{
        this.$http.interceptors.request.eject(this.$myInterceptor);
        let fd = new FormData();
        fd.append('file', this.img);
        //fd.append('type', 'publish');
        fd.append('title', this.title);
        fd.append('summary', this.summary);
        fd.append('content', this.content);
        fd.append('vip_id', this.info.vip_id);

        this.$http({
          method: "post",
          url:'headline',
          data: fd,
          processData: false
        }).then(rtnData=>{
          this.show = true;
          this.tip = "发布成功";
          setTimeout(()=>{
            this.$router.push('/headline-details/'+rtnData.data);
          }, 1000)
        });
      }
    },
    onUpload(e){
      this.img = e.target.files[0];
      this.imgUrl = window.URL.createObjectURL(this.img);
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/publish.css';
  
</style>