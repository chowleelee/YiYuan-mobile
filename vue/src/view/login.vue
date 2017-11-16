
<template>
  <div class="login container" >
    <h3>手机号验证登录</h3>
    <mt-field label="手机号码" placeholder="+86" v-model="phone" :disabled="phone_dis"></mt-field>
    <mt-field label="图形验证码" v-model="imgCap">
      <img :src="imgUrl" @click="changeCap()">
    </mt-field>
    <mt-field label="手机验证码" placeholder="输入你收到的验证码" v-model="phoneCap">
      <button class="timerBtn" :class="{'active':flag}" :disabled="disabled || time > 0" @click="sendCap">
        {{text}}
      </button>
    </mt-field>
    <p>未注册过的手机号将自动注册为分答用户</p>
    <mt-button type="danger" @click.native="doLogin()" size="large">
      登录
    </mt-button>
    <div class="tips">
      <mt-popup
        v-model="show"
        popup-transition="popup-fade">{{tip}}
      </mt-popup>
    </div>
  </div>
</template>

<script type="es6">
import { Popup } from 'mint-ui';
import {Toast} from 'mint-ui'
import { Indicator } from 'mint-ui';
import smithButton from '../components/SmithButton.vue'
import {mapMutations} from 'vuex';
export default {
  data () {
    return {
      phone:'',
      phoneCap:'',
      imgCap:'',
      show:false,
      tip:'',
      captcha:'',
      disabled: false,
      time:0,
      timer:null,
      imgUrl:'',
      rand:1,
      phone_dis:false,   //当发送验证码时不能修改手机号
      flag:false
    }
  },
  mounted(){
    this.imgUrl = this.$captUrl + '?id='+this.rand;
  },
  components:{
    smithButton
  },
  computed: {
      text: function () {
        if(this.time > 0){
          this.flag = true;
        }else{
          this.flag = false;
        }
        return this.time > 0 ? this.time + '秒' : '获取验证码';
      }
  },
  methods:{
    ...mapMutations(['setInfo']),//把vuex要用到的方法映射出来 
    doLogin:function () {
      if(!this.phone){
        this.show = true;
        this.tip = '请填写手机号';
      }else if(!(/^1[3|4|5|8][0-9]\d{8}$/.test(this.phone))){
        this.show = true;
        this.tip = '手机号格式不正确';
      }else if(!this.imgCap || !this.phoneCap){
        this.show = true;
        this.tip = '请填写验证码';
      }else{
        this.$http
        .get("capt",{params:{phone:this.phone,phoneCap:this.phoneCap}})
        .then(rtnData => {
            if (rtnData.data == 1) {
              this.$http
              .post("user",{type:'login',phone:this.phone})
              .then(rtnData => {
                // 调用vuex中mutations的方法
                localStorage.setItem('info',JSON.stringify(rtnData.data));
                //localStorage.setItem('isLogin',true);
                this.setInfo(rtnData.data); 
                //Indicator.open();
                this.$router.push('/uhome');
              })
            }else{
              this.show = true;
              this.tip = '验证码错误';
              this.phone_dis = false;
              this.phoneCap = '';
              this.imgCap = '';
              this.changeCap();
              this.disabled = false;
              this.time = 0;
              this.$http
                  .get("capt",{params:{phone:this.phone,type:'delete'}});
              clearInterval(this.timer);
            }
        })
      }
    },
    sendCap: function () {
      if(!this.phone){
        this.show = true;
        this.tip = '请填写手机号';
      }else if(!(/^1[3|4|5|8][0-9]\d{8}$/.test(this.phone))){
        this.show = true;
        this.tip = '手机号格式不正确';
      }else if(!this.imgCap){
        this.show = true;
        this.tip = '请填写验证码';
      }
      else{
        this.$http
        .post("capt",{phone:this.phone,imgCap:this.imgCap,rand:this.rand})
        .then(rtnData => {
            if(rtnData.data==0){
              this.show = true;
              this.tip = '图形验证码不正确';
              this.changeCap();
              this.imgCap = '';
            }else{
              this.disabled = true;
              this.phone_dis = true;
              this.time = 60;
              this.timer = setInterval(()=>{
                this.time--;
                if (this.time == 0) {
                  this.disabled = false;
                  this.phone_dis = false;
                  this.changeCap();
                  this.imgCap = '';
                  this.$http
                  .get("capt",{params:{phone:this.phone,type:'delete'}});
                  clearInterval(this.timer);
                }
              },1000);
              this.phoneCap = rtnData.data;
            }
        })
      }
    },
    changeCap:function(){
      this.rand = Math.random(1,10000);
      this.imgUrl = this.$captUrl + '?id='+this.rand;
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
  @import '../assets/css/login.css';
</style>