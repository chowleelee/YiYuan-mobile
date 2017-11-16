 <template >
   <div class="footer">
     <mt-tabbar v-model="selected" :fixed="true" >
       <mt-tab-item class="footer-index"> 
         <router-link to="/"><i class="index-icon"></i>首页</router-link>
       </mt-tab-item>
       <mt-tab-item>
         <router-link to="/listen"><i class="listen-icon"></i>收听</router-link>
       </mt-tab-item>
       <mt-tab-item class="footer-ask">
         <router-link to="/ask" class="ask">问</router-link>
       </mt-tab-item>
        <mt-tab-item>
         <router-link to="/purchase"><i class="purchase-icon"></i>已购</router-link>
       </mt-tab-item>
       <mt-tab-item v-if="isLogin==false">
         <router-link to="/login"><i class="uhome-icon"></i>登录</router-link>
       </mt-tab-item>

       <mt-tab-item v-if="isLogin==true">
         <router-link to="/uhome"><i class="uhome-icon"></i>我的</router-link>
       </mt-tab-item>
      

     </mt-tabbar>
   </div>

 </template>

 <script type="es6">
 import {mapState,mapGetters} from 'vuex'
export default {
  data(){
    return {
      selected:true
    }
  },
  created(){
    this.$http.get('/monitor').then(rtnData=>{})
  },
  computed:{
    //...mapState(['info']), //把vuex要用到的状态映射出来
    ...mapGetters(['isLogin']) 
  }
}
</script>
<style>
@import '../assets/css/footer.css';
 
</style>