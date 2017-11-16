<template>
  <div class="help container">
        <ul class="help_list">
          <li class="uhome_nav" @click="openvip()">
            <span class="uhome_text">开通答主</span>
            <span class="rignt_sub"></span>
          </li>
           <li class="uhome_nav" @click="uhomehelp()">
            <span class="uhome_text">帮助</span>
            <span class="rignt_sub"></span>
          </li>
           <li class="uhome_nav" @click="uhomeset()">
            <span class="uhome_text">设置</span>
            <span class="rignt_sub"></span>
          </li>
        </ul>   
    <!-- <mt-button @click.native="exit()" v-if="tishicon">退出</mt-button> -->
  </div>
</template>

<script type="es6">
 import {mapMutations} from 'vuex'
 import { Toast } from 'mint-ui' 
 import { MessageBox } from 'mint-ui'

export default {
  data () {
    return {
      tishicon:true,
      showdown:true
    }
  },
  components:{

  },
  methods:{
  	...mapMutations(['logout']),
  	exit:function(){
  		this.logout()
  		this.$router.push('/login')
  	},
    openvip:function () {
      this.showdown=false;
    },
    hidevip:function(){
      this.showdown=true;
    },
    downfd:function(){
      console.log(1)
    }
    }
    	
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
/*@import '../assets/css/help.css';*/
 
</style>