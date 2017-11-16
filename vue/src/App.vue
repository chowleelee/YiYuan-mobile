<template>
  <div id="app">

    <router-view></router-view>
    
    <!-- 这里相当于mvc的入口文件，劲量不要加布局。放到组件目录components  -->

      <mhfooter> </mhfooter>
  </div>
</template>

<script type="es6">
import Vue from 'vue'
import {selfAdaption,menu} from '@/assets/js/common.js';
import mhfooter from '@/components/footer'
export default {
  data(){
    return {
    
    }
  },
  created(){
    selfAdaption(),
    menu()
  },
  components:{
    mhfooter
  }
}
</script>

<style> 

@import 'assets/css/common.css';

#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  height: 100%;
  
}
</style>