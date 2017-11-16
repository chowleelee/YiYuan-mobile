<template>
  <div class="answerToSubmit">
    <div class="pay">
      <div class="pay-details">
        <div class="offer">
          <span>￥</span>
          <span>{{price}}</span>
        </div>
        <div class="type">
          <span>分答提问</span>
        </div>
      </div>
      <div class="pay-method">
        <div class="item selected">
          <img class="icon" src="../assets/img/answerToSubmit-zhifubao.png">
          <span>支付宝支付</span>
        </div>  
      </div>
      <div class="pay-submit" @click="pay()">
        <button>支付 ¥{{price}}</button>
      </div>
    </div>
    <div class="tips">
      <mt-popup
        v-model="show"
        popup-transition="popup-fade">支付成功
      </mt-popup>
    </div>
  </div>
</template>

<script type="es6">
import {mapState,mapMutations,mapGetters} from 'vuex';
import { Toast } from 'mint-ui'
export default {
  data () {
    return {
      price:0,
      show:false
    }
  },
  created(){
     this.init();
  },
  computed:{
    ...mapState(['info']),
    ...mapGetters(['isLogin']) 
  },
  methods:{
    init:function(){
      this.price = this.$route.query.price;
    },
    pay:function(){
      if(this.isLogin){
        var type = this.$route.query.type;
        if(type == 'ask'){
          this.$http
            .post('ask',{uid:this.info.user_id,vip_id:this.$route.query.id,ask_content:this.$route.query.ask_content,is_public:this.$route.query.is_public,price:this.$route.query.price})
            .then(rtnData=>{
              this.show = true;
              setTimeout(()=>{
                this.$router.push('/')
              }, 1000)
            })
        }else if(type == 'quickask'){
          this.$http
            .post('quickask',{uid:this.info.user_id,content:this.$route.query.content,quickask_cate_id:this.$route.query.quickask_cate_id,is_anonymous:this.$route.query.is_anonymous,price:this.$route.query.price})
            .then(rtnData=>{
              this.show = true;
              setTimeout(()=>{
                this.$router.push('/')
              }, 1000)
            })
        }else if(type == 'askbuy' || type == 'asklisten'){
          this.$http
            .post('purchaseask',{uid:this.info.user_id,ask_id:this.$route.query.ask_id,ask_id:this.$route.query.ask_id,user_id:this.$route.query.ask_uid})
            .then(rtnData=>{
              this.show = true;
              setTimeout(()=>{
                if(type == 'askbuy'){
                  this.$router.push('/tutorDetails/'+this.$route.query.vip_id);
                }
                else{
                  this.$router.push('/listen');
                }
              }, 1000)
            })
        }else if(type == 'smalltalk'){
          this.$http
            .post('purchaseorder',{uid:this.info.user_id,smalltalk_id:this.$route.query.smalltalk_id})
            .then(rtnData=>{
              this.show = true;
              setTimeout(()=>{
                this.$router.push('/stalkteacher/'+this.$route.query.smalltalk_id);
              }, 1000)
            })
        }
      }else{
        //Toast("请先登录");
        //this.$router.push('login')
      }
     
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
 
  @import '../assets/css/answerToSubmit.css';
  


</style>