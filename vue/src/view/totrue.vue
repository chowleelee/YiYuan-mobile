<template>
  <div class="totrue">
      <div class="header-tip" v-if="user_info.is_real==1 || user_info.reason">{{tip}}</div>
      <button class="btn" :class="{'disabled':flag}" @click="totrue()" :disabled="disabled">申请认证</button>
      <div class="tips">
        <mt-popup
          v-model="show"
          popup-transition="popup-fade">已申请认证，请等待
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
      user_info:'',
      disabled:false,
      tip:'',
      show:false,
      flag:false
    }
  },
  computed:{
    ...mapState(['info']),
  },
  mounted(){
    this.$http
    .get("user",{params:{type:'totrue',uid:this.info.user_id}})
    .then(rtnData => {
      this.user_info = rtnData.data;
      if(this.user_info['is_real']==1){
        this.disabled = true;
        this.flag = true;
        this.tip = '答主认证已申请，申请需要3-5个工作日，请耐心等待。';
      }else if(this.user_info['is_real']==0 && this.user_info['reason']){
        this.tip = '驳回理由：'+this.user_info['reason'];
      }
    })
  },
  methods:{
    totrue(){
      this.$http
      .post("user",{
        type:'totrue',
        uid:this.info.user_id
      })
      .then(()=>{
        this.show = true;
        setTimeout(()=>{
          this.$router.push('/uhome');
        }, 1000)
      })
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/totrue.css';

</style>