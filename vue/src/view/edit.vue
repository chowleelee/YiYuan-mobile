<template>
  <div class="edit">
    <!-- <form action="" enctype="multipart/form-data"> -->
      <div class="avatar">
        <div class="container">
          <label>头像</label>
          <img :src="imgUrl">
          <input type="file" @change='onUpload'>
        </div>
      </div>
      <div class="nickname">
        <div class="container">
          <label>昵称</label>
          <input type="text" v-if="user_info.flag==1" v-model="user_info.user_name" placeholder="请填写昵称">
          <input type="text" v-if="user_info.flag!=1" v-model="user_info.real_name" placeholder="请填写昵称">
        </div>
      </div>
      <div class="field" v-if="user_info.flag!=1">
        <div class="container"> 
          <label>领域</label>
          <select v-model="user_info.vip_cate_id">
            <option value="0">请选择</option>
            <option v-for="item in vip_cate" :value="item.id">{{item.cate_name}}</option>
          </select>
        </div>
      </div>
      <div class="title ta" v-if="user_info.flag!=1">
        <div class="container"> 
          <label>头衔</label>
          <textarea maxlength="18" rows="2" placeholder="（18个字以内）运营经理；电台DJ；Google早期员工；周游过列国的斜杠青年；心理咨询师……" @input="titleNum = user_info.identity.length" v-model="user_info.identity"></textarea>
          <span class="counter">
            <i>{{titleNum}}</i>
            <span>/18</span>
          </span>
        </div>
      </div>
      <div class="intro ta" v-if="user_info.flag!=1">
        <div class="container">
          <label>简介</label>
          <textarea maxlength="100" rows="4" placeholder="（100个字以内）关于这些，尽情问我：投资理财、迎取白富美；如何优雅参加前任婚礼；和异性出差时如何管理自我冲动……或者给你唱个晚安小曲吧。" @input="introNum = user_info.introduce.length" v-model="user_info.introduce"></textarea>
          <span class="counter">
            <i>{{introNum}}</i>
            <span>/100</span>
          </span>
        </div>
      </div>
      <div class="askme" v-if="user_info.flag!=1">
        <div class="container">
          <label>向我提问需要支付</label>
          <input type="text" v-model="user_info.price">
          <label>元</label>
        </div>
      </div>
      <button class="btn" @click="save()">保存</button>
      <div class="tips">
        <mt-popup
          v-model="show"
          popup-transition="popup-fade">{{tip}}
        </mt-popup>
      </div>
  </div>
</template>

<script type="es6">
 import { Toast } from 'mint-ui' 
 import { MessageBox } from 'mint-ui'
 import { mapState } from 'vuex'
 import { Indicator } from 'mint-ui'

export default {
  data () {
    return {
      tishicon:true,
      user_info:{},
      img:null,
      imgUrl:'',
      vip_cate:null,
      show:false,
      tip:'',
      introNum:0,
      titleNum:0
    }
  },
  created(){
    this.init();
  },
  computed:{
    ...mapState(['info'])
  },
  methods:{
    init(){
      Indicator.open();
      this.$http
      .get("user",{params:{type:'edit',uid:this.info.user_id}})
      .then(rtnData => {
        this.user_info = rtnData.data;
        this.imgUrl = this.$sourceUrl+'/img/'+this.user_info.head_img;
        if(this.user_info.flag != 1){
          this.titleNum = this.user_info.identity.length;
          this.introNum = this.user_info.introduce.length;
        }
        Indicator.close();
        //this.imgUrl = this.info.head_img ? this.info.head_img : '/static/img/headbg.png';
      })
      this.$http
      .get("vpcategory")
      .then(rtnData => {
        this.vip_cate = rtnData.data;
      })
    },
    save(){
      if(this.user_info.flag == 1){
        if(!this.user_info.user_name){
          this.show = true;
          this.tip = '请输入昵称';
          return;
        }
      }else{
        if(!this.user_info.real_name){
          this.show = true;
          this.tip = '请输入昵称';
          return;
        }else if(this.user_info.vip_cate_id==0){
          this.show = true;
          this.tip = '请选择领域';
          return;
        }else if(!this.user_info.identity){
          this.show = true;
          this.tip = '请输入头衔';
          return;
        }else if(!this.user_info.introduce){
          this.show = true;
          this.tip = '介绍下自己吧';
          return;
        }
        else if(this.user_info.price < 1){
          this.show = true;
          this.tip = '价格不低于1元';
          return;
        }
      }
      this.$http.interceptors.request.eject(this.$myInterceptor);

      let fd = new FormData(); 
      if(this.img){
        fd.append('file', this.img);
      }
      fd.append('type', 'edit');
      fd.append('info', JSON.stringify(this.user_info));

      this.$http({
        method: "post",
        url:'user',
        data: fd,
        processData: false
      })
      .then(()=>{
        this.show = true;
        this.tip = "保存成功"
        setTimeout(()=>{
          this.$router.push('/uhome');
        }, 1000)
      })
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

@import '../assets/css/edit.css';
</style>