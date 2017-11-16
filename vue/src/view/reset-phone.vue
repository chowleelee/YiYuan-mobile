<template>
  <div class="reset-phone container">
    <div class="nav">
      <span class="step step1-done">验证原号码</span>
      <span class="line" :class="{'line-done':done}"></span>
      <span class="step" :class="{'step2-done':done,'step2':!done}">验证新号码</span>
    </div>

    <p v-if="!done">将向原手机号码 {{phone}} 发送验证码</p>

    <mt-field label="新手机号" placeholder="+86" v-model="new_phone" v-if="done" :disabled="phone_dis">
    </mt-field>
    <mt-field label="图形验证码" v-model="imgCap">
      <img :src="imgUrl" @click="changeCap()">
    </mt-field>
    <mt-field label="手机验证码" placeholder="输入你收到的验证码" v-model="phoneCap">
      <button class="timerBtn" :class="{'active':flag}" :disabled="disabled || time > 0" @click="sendCap">
        {{text}}
      </button>
    </mt-field>
    <mt-button type="danger" @click.native="next()" size="large" v-if="!done">
        下一步
    </mt-button>
    <mt-button type="danger" @click.native="confirm()" size="large" v-if="done">
        确认更换
    </mt-button>
    <p class="contact" v-if="!done">若原手机号码无法收取验证码，可<span>联系分答客服</span></p>
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
import smithButton from '../components/SmithButton.vue'
import {mapMutations,mapState} from 'vuex'

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
      done:false,
      phone_dis:false,   //当发送验证码时不能修改手机号
      flag:false,
      new_phone:''
    }
  },
  mounted(){
    this.imgUrl = this.$captUrl + '?id='+this.rand;
    this.$http
    .get("user",{params:{type:'reset',uid:this.info.user_id}})
    .then(rtnData => {
      this.phone = rtnData.data;
    })
  },
  components:{
    smithButton
  },
  computed: {
    ...mapState(['info']),
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
    next:function () {
      if(!this.imgCap || !this.phoneCap){
        this.show = true;
        this.tip = '请填写验证码';
      }else{
        this.$http
        .get("capt",{params:{phone:this.phone,phoneCap:this.phoneCap}})
        .then(rtnData => {
            this.phoneCap = '';
            this.imgCap = '';
            this.changeCap();
            this.time = 0;
            this.disabled = false;
            console.log(this.disabled);
            this.$http
                .get("capt",{params:{phone:this.phone,type:'delete'}});
            clearInterval(this.timer);

            if (rtnData.data == 1) {
              this.done = true;
              //this.phone = '';
            }else{
              this.show = true;
              this.tip = '验证码错误';
            }
            
        })
      }
      this.phone_dis = false;
    },
    confirm:function(){
      if(!this.new_phone){
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
        .get("capt",{params:{phone:this.new_phone,phoneCap:this.phoneCap}})
        .then(rtnData => {
            if (rtnData.data == 1) {
              this.$http
              .post("user",{type:'reset',phone:this.new_phone,uid:this.info.user_id})
              .then(rtnData => {
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
                  .get("capt",{params:{phone:this.new_phone,type:'delete'}});
              clearInterval(this.timer);
            }
        })
      }
    },
    sendCap: function () {
      if(this.done == true){
        if(!this.new_phone){
          this.show = true;
          this.tip = '请填写手机号';
          return;
        }else if(!(/^1[3|4|5|8][0-9]\d{8}$/.test(this.phone))){
          this.show = true;
          this.tip = '手机号格式不正确';
          return;
        }
      }
      if(!this.imgCap){
        this.show = true;
        this.tip = '请填写验证码';
      }
      else{
        if(this.done == true){
          this.$http
          .get("user",{params:{type:'reset_phone',phone:this.new_phone}})
          .then(rtnData => {
            if(rtnData.data == 1){
              this.show = true;
              this.tip = '该手机已注册过';
              this.changeCap();
              this.imgCap = '';
              this.new_phone = '';
            }else{
              this.sendPhoneCap(this.new_phone);
            }
          })
        }else{
          this.sendPhoneCap(this.phone)
        }
      }
    },
    sendPhoneCap:function(phone){
      this.$http
      .post("capt",{phone:phone,imgCap:this.imgCap,rand:this.rand})
      .then(rtnData => {
          if(rtnData.data==0){
            this.show = true;
            this.tip = '图形验证码不正确';
            this.changeCap();
            this.imgCap = '';
          }else{
            this.phone_dis = true;
            this.disabled = true;
            this.time = 60;
            this.timer = setInterval(()=>{
              this.time--;
              if (this.time == 0) {
                this.phone_dis = false;
                this.disabled = false;
                this.changeCap();
                this.imgCap = '';
                this.$http
                .get("capt",{params:{phone:phone,type:'delete'}});
                clearInterval(this.timer);
              }
            },1000);
            this.phoneCap = rtnData.data;
          }
      })
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
   @import '../assets/css/reset-phone.css';
</style>