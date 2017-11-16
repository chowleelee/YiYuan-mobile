<template>
  <div class="set">
    <ul class="help_list">
      <li class="set_nav" @click="editTel()">
        <div class="container">
          <span class="set_phone">手机号</span>
          <span class="phone_num">{{phone}}</span>
        </div>
      </li>
      <li class="set_nav" @click="uhomehelp()">
        <div class="container">
          <span class="set_about">关于</span>
        </div>
      </li>
      <li class="set_nav logout" @click="exit()">
        退出登录
      </li>
    </ul>
  </div>
</template>

<script type="es6">
 import {mapState,mapMutations} from 'vuex' 

export default {
  data () {
    return {
      phone:''
    }
  },
  components:{

  },
  computed:{
    ...mapState(['info'])
  },
  mounted(){
    this.$http
    .get("user",{params:{type:'reset',uid:this.info.user_id}})
    .then(rtnData => {
      this.phone = rtnData.data;
    })
  },
  methods:{
  	...mapMutations(['logOut']),
    exit:function(){
      this.logOut();
      this.$router.push('/login');
    },
    editTel(){
      this.$router.push('/reset-phone');
    },
    uhomehelp(){
      
    }
  }
    	
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/set.css';
 
</style>