<template>
  <div class="openvip">
    <!-- <form action="" enctype="multipart/form-data"> -->
      <div class="header-tip" v-if="user_info.flag!=1">{{tips}}
      </div>
      <div class="avatar">
        <div class="container">
          <label>头像</label>
          <img :src="head_img">
          <input type="file" @change='onUpload' :disabled="disabled">
        </div>
      </div>
      <div class="nickname">
        <div class="container">
          <label>昵称</label>
          <input type="text" v-if="user_info.flag==1" v-model="real_name" placeholder="请填写昵称">
          <input type="text" v-if="user_info.flag!=1" :disabled="disabled" v-model="real_name" placeholder="请填写昵称">
        </div>
      </div>
      <div class="field">
        <div class="container"> 
          <label>领域</label>
          <select v-model="vip_cate_id" :disabled="disabled">
            <option value="0">请选择</option>
            <option v-for="item in vip_cate" :value="item.id">{{item.cate_name}}</option>
          </select>
        </div>
      </div>
      <div class="title ta">
        <div class="container"> 
          <label>头衔</label>
          <textarea maxlength="18" rows="2" placeholder="（18个字以内）运营经理；电台DJ；Google早期员工；周游过列国的斜杠青年；心理咨询师……" @input="titleNum = identity.length" v-model="identity" :disabled="disabled"></textarea>
          <span class="counter">
            <i>{{titleNum}}</i>
            <span>/18</span>
          </span>
        </div>
      </div>
      <div class="intro ta">
        <div class="container">
          <label>介绍下自己吧</label>
          <textarea maxlength="100" rows="4" placeholder="（100个字以内）关于这些，尽情问我：投资理财、迎取白富美；如何优雅参加前任婚礼；和异性出差时如何管理自我冲动……或者给你唱个晚安小曲吧。" @input="introNum = introduce.length" v-model="introduce" :disabled="disabled"></textarea>
          <span class="counter">
            <i>{{introNum}}</i>
            <span>/100</span>
          </span>
        </div>
      </div>
      <div class="askme">
        <div class="container">
          <label>向我提问需要支付</label>
          <input type="text" v-model="price" placeholder="￥1-500" :disabled="disabled">
          <label>元</label>
        </div>
      </div>
      <button class="btn" @click="save()" :class="{'disabled':flag}" :disabled="disabled">开通我的「分答」</button>
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
 import {mapState} from 'vuex'
export default {
  data () {
    return {
      tishicon:true,
      user_info:{},
      vip_cate:null,
      img:null,
      //imgUrl:'',
      head_img:'',
      real_name:'',
      identity:'',
      introduce:'',
      price:3.14,
      vip_cate_id:0,
      show:false,
      tip:'',
      disabled:false,
      flag:false,
      tips:'',
      titleNum:0,
      introNum:0  
    }
  },
  computed:{
    ...mapState(['info'])
  },
  mounted(){
    this.$http
    .get("user",{params:{type:'openvip',uid:this.info.user_id}})
    .then(rtnData => {
      this.user_info = rtnData.data;
      //this.imgUrl = this.user_info.head_img ? this.user_info.head_img : '/static/img/headbg.png';
      this.head_img = this.$sourceUrl+'/img/'+this.user_info.head_img;
      if(this.user_info.flag == 1){
        this.real_name = this.user_info.user_name;
      }else{
        this.real_name = this.user_info.real_name;
        this.identity = this.user_info.identity;
        this.introduce = this.user_info.introduce;
        this.price = this.user_info.price;
        this.vip_cate_id = this.user_info.vip_cate_id;

        if(!this.user_info['reason']){
          this.disabled = true;
          this.flag = true;
          this.tips = '资料修改已提交审核，审核需要3-5个工作日，请耐心等待。';
        }else if(this.user_info['reason']){
          this.tips = '驳回理由：'+this.user_info['reason'];
        }
      }
      
    });
    this.$http
    .get("vpcategory")
    .then(rtnData => {
      this.vip_cate = rtnData.data;
    })
  },
  computed:{
    ...mapState(['info'])
  },
  methods:{
    save(){
      if(!this.real_name){
        this.show = true;
        this.tip = '请输入昵称';
      }else if(this.vip_cate_id==0){
        this.show = true;
        this.tip = '请选择领域';
      }else if(!this.identity){
        this.show = true;
        this.tip = '请输入头衔';
      }else if(!this.introduce){
        this.show = true;
        this.tip = '介绍下自己吧';
      }else{
        this.$http.interceptors.request.eject(this.$myInterceptor);
        let fd = new FormData(); 
        if(this.img){
          fd.append('file', this.img);
        }else{
          fd.append('head_img', this.user_info.head_img);
        }
        fd.append('type', 'openvip');
        fd.append('real_name', this.real_name);
        fd.append('identity', this.identity);
        fd.append('introduce', this.introduce);
        fd.append('price', this.price);
        fd.append('vip_cate_id', this.vip_cate_id);
        fd.append('uid', this.info.user_id);

        this.$http({
          method: "post",
          url:'user',
          data: fd,
          processData: false
        }).then(()=>{
          this.show = true;
          this.tip = "已申请开通答主，请等待"
          setTimeout(()=>{
            this.$router.push('/uhome');
          }, 1000)
        });
      }
    },
    onUpload(e){
      this.img = e.target.files[0];
      this.head_img = window.URL.createObjectURL(this.img);
    } 
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/openvip.css';

</style>