<template>
  <div class="reward-wrap">
    <div class="wrap-content">
        <div class="banner">
          <!-- 情感小烦恼 -->{{title[0]}}
          <img :src="imgUrl" alt="">
          <!-- 专家来开导 -->{{title[1]}}
          
        </div>
        <div class="wrap-main">
          <textarea class="content" placeholder="请详细描述您的情感状态、背景信息以及具体问题。匿名提问后如需要进一步咨询，请告知前次咨询内容以便对应。48小时内无人抢答，将按支付路径全额退款。" maxlength="300"  @input="descInput" v-model="textContent">
          </textarea>
          <span class="counter">
            <i class="num">{{textNum}}</i>
            <span>/</span>
            <span>300</span>
          </span>
        </div>
        <div class="image-wrap">
          <img src="../assets/img/reward_wrap1.jpg" alt="">
          <span class="price">￥10</span>
        </div>
        <div class="input-wrap">
          <span class="user">匿名</span>
          <span class="switch">
            <label class="mint-switch">
              <input type="checkbox" class="mint-switch-input" checked="checked" v-model="is_anonymous"> 
              <span class="mint-switch-core"></span> 
              <div class="mint-switch-label"></div>
            </label>
          </span>
        </div>
        <div class="rule-wrap">
          <span class="declare">问答细则及责任声明</span>
          <span>></span>
        </div>
        <div class="btn-wrap">
          <button type="button" @click="askSubmit()">发布</button>
        </div>
    </div>
    <div class="tips">
      <mt-popup
        v-model="show"
        popup-transition="popup-fade">{{tip}}
      </mt-popup>
    </div>
  </div>
</template>

<script type="es6">

  import Vue from 'vue'
  import {Toast} from 'mint-ui'
  import {mapState,mapMutations,mapGetters} from 'vuex';

  export default {
    data () {
      return {
        textNum:0,       //字数
        textContent:'',  //文本内容
        title:[],
        imgUrl:"",
        price:10,
        is_anonymous:true,
        show:false,
        tip:''
      }
    },
    created(){
      this.$http.get("quickaskCate").then(rtnData=>{
          this.ask_quickly_cate=rtnData.data;
          this.title=this.ask_quickly_cate[this.$route.params.id-1].cate_name.split("，");
          this.imgUrl=this.$sourceUrl+'/img/'+this.ask_quickly_cate[this.$route.params.id-1].cate_img;
      })
    },
    watch:{
      is_anonymous:function(val){
        console.log(val);
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    methods:{
      askSubmit:function(){
        if(this.isLogin){
          if(this.textNum == 0){
            //Toast("请输入问题");
            //this.ask = true;
            this.show = true;
            this.tip = "请输入问题";
          }else{
            this.$router.push({path:'/answerToSubmit',query:{type:'quickask',price:this.price,content:this.textContent,is_anonymous:this.is_anonymous,quickask_cate_id:this.$route.params.id}})
          }
        }else{
          this.$router.push('/login')
        }
      },
      descInput:function(){
        this.textNum = this.textContent.length;
      }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/reward-wrap.css';


</style>