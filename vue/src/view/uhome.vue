<template>
  <div class="uhomedd">
    <div class="uhome">
      <div class="uhome_one">
        <div class="container">
          <div class="flex_box" @click="edit()">
            <div class="headbg">
              <img :src="user_info.head_img" class="img">
              <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true" v-if="user_info.is_real==2">
            </div>
            <div class="p">
              <p>{{user_info.user_name}}</p>
              <p class="check">查看并编辑个人资料</p> 
            </div>
            <span class="rignt_sub"></span>
          </div>
          <p class="uhome_price">
            总收入￥{{user_info.total_income}}
            ，总收益￥{{user_info.total_profit}}
          </p>
          <span class="price_distribut">收入90%归你，每夜自动领取，入库微信钱包</span>
        </div>
      </div>
      <div class="uhome_two">
        <div class="container">
          <ul>
            <li class="uhome_nav" @click="myhome()" v-if="user_info.status==3">
              <span class="uhomebg bgpic_1"></span>
              <span class="uhome_text">我的答主页</span>
              <span class="rignt_sub"></span>
            </li>
            <li class="uhome_nav" @click="myanswer()" v-if="user_info.status==3">
              <span class="uhomebg bgpic_4"></span>
              <span class="uhome_text">我答</span>
              <span class="rignt_sub"></span>
            </li>
            <li class="uhome_nav" @click="totrue()" v-if="user_info.is_real<2">
              <span class="uhomebg bgpic_5"></span>
              <span class="uhome_text">申请答主认证</span>
              <span class="rignt_sub"></span>
            </li>
            <li class="uhome_nav" @click="openvip()" v-if="user_info.status<3">
              <span class="uhomebg bgpic_1"></span>
              <span class="uhome_text">开通答主</span>
              <span class="rignt_sub"></span>
            </li>
            <li class="uhome_nav" @click="publish()" v-if="user_info.is_real==2">
              <span class="uhomebg bgpic_5"></span>
              <span class="uhome_text">添加头条</span>
              <span class="rignt_sub"></span>
            </li>
            <li class="uhome_nav" @click="addnovel()" v-if="user_info.is_real==2">
              <span class="uhomebg bgpic_5"></span>
              <span class="uhome_text">添加小讲</span>
              <span class="rignt_sub"></span>
            </li>
             <li class="uhome_nav" @click="uhomehelp()">
              <span class="uhomebg bgpic_2"></span>
              <span class="uhome_text">帮助</span>
              <span class="rignt_sub"></span>
            </li>
             <li class="uhome_nav" @click="uhomeset()">
              <span class="uhomebg bgpic_3"></span>
              <span class="uhome_text">设置</span>
              <span class="rignt_sub"></span>
            </li>
          </ul>
        </div>
        
      </div>
    </div>
  </div>
</template>

<script type="es6">
 import {mapState,mapMutations} from 'vuex'
 import { Toast } from 'mint-ui' 
 import { MessageBox } from 'mint-ui'
 import { Indicator } from 'mint-ui'

export default {
  data () {
    return {
      tishicon:true,
      user_info:{},
      //imgUrl:''
    }
  },
  computed:{
    ...mapState(['info'])
  },
  mounted(){
    Indicator.open();
    this.$http
    .get("user",{params:{type:'uhome',uid:this.info.user_id}})
    .then(rtnData => {
      this.user_info = rtnData.data;
      this.user_info.head_img = this.$sourceUrl+'/img/'+this.user_info.head_img;
      Indicator.close();
      //this.imgUrl = this.info.head_img ? this.info.head_img : '/static/img/headbg.png';
    })
  },
  methods:{
    myhome:function(){
      this.$router.push('/tutorDetails/'+this.info.vip_id);
    },
    myanswer:function(){
      this.$router.push('/answer');
    },
    edit:function(){
      this.$router.push('/edit');
    },
    openvip:function () {
      this.$router.push('/openvip');
    },
    /*myheadline:function(){
      this.$router.push('/headline/self');
    },*/
    publish:function(){
      this.$router.push('/publish');
    },
    addnovel:function(){
      this.$router.push('/novel');
    },
    totrue:function(){
      this.$router.push('/totrue');
    },
    hidevip:function(){
      this.showdown=true;
    },
    downfd:function(){
      console.log(1)
    },
    uhomehelp:function(){
      //this.$router.push('/help')
    },
    uhomeset:function(){
      this.$router.push('/set')
    }
    
  }
    	
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/uhome.css';
</style>