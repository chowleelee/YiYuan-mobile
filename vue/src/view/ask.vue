<template>
  <div class="ask container">
    <div class="ask_box" v-if="step==1">
      <mt-field  placeholder="请输入您的问题，分答将为你推荐合适的专家答主" type="textarea" rows="4" v-model="ask_content" class="textarea_con" style="font-size: 12px;"></mt-field>
      <div class="mt-2 mb-2 check_btn clearfix">
        <mt-switch v-model="is_public" class="switch_btn floatLeft"></mt-switch>
        <span class="floatLeft">公开提问</span> 
        <p class="floatRight">！提问须知</p>
      </div>
      <mt-button class="recommend_btn" :class="{'heightline':ask_content}" :disabled="ask_content==''" @click="next_step()">为我推荐答主</mt-button>
    </div>
    <div class="find_zj" v-if="step==2">
      <div class="find_zj_nav">
        <a @click="step=1"> 返回 </a>
        <p class="search_result" @click="step=1">{{ask_content}}</p>
      </div>
      <ul>
        <li v-for="(item,index) in vip_list" @click="go_vip_page(item.id)" class="tutor_list">
          <div class="vip_gaiyao">
            <div class="head_samll">
              <img class="head_min" :src="$sourceUrl+'/img/'+item.head_img">
              <img class="dav_min" src="../assets/img/daV.jpg">
            </div>
            <div class="name_int">
              <p class="vip_name">{{item.real_name}}</p>
              <p class="vip_int">{{item.identity}}</p>
            </div>
            <div class="price_ask">
              <span class="vip_price">￥{{item.price}}</span>
              <button v-if="info.vip_id!=item.id" class="ask_btn" @click.stop="answerToSubmit(item.id,item.price)">提问</button>
            </div>
          </div>
          <div class="vip_databox">
            <span class="vip_circle">{{item.cate_name}}</span>
            <span>
            回答：{{answer_num[index]}}人
            <span>
            ,今日回答的概率：{{answer_rate[index]}}%
          </span>
          </span>
          </div>
        </li>
      </ul>
    </div>  
  </div>
</template>

<script type="es6">
  import{mapState,mapMutations,mapGetters} from 'vuex'
  import { Toast } from 'mint-ui'
  import { Indicator } from 'mint-ui'

  export default {
    data () {
      return {
        ask_content:'',
        step:1,
        vip_list:[],
        is_public:true,
        answer_num:[],
        answer_rate:[]
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    created(){
      //this.init()
    },
    watch:{
      ask_content: function (nvalue) {
        this.setAskContent(nvalue)
      }
    },
    methods:{
      ...mapMutations(['setAskContent']),
      init:function(){
        if(this.isLogin){
          Indicator.open();
          this.$http
          .get('vip',{params:{type:'hotList',id:this.info.user_id}})
          .then(rtnData=>{
              for(var i=0;i<rtnData.data[0].length;i++){
                if(rtnData.data[0][i].user_id == this.info.user_id)
                  continue;
                this.vip_list.push(rtnData.data[0][i]); 
              }
              
              var nowDay = new Date();
              for(var i=0;i<this.vip_list.length;i++){
                this.$set(this.answer_num,i,0);
                this.$set(this.answer_rate,i,0);
                let index = i;
                this.$http
                    .get('/ask/'+this.vip_list[i].id,{params:{type:'vip'}})
                    .then(rtnData=>{
                      var today_ask_num = 0;
                      var today_answer_num=0;
                      for(var i=0;i<rtnData.data.length;i++){
                        //console.log(rtnData.data[i].answer_content)
                        if(rtnData.data[i].answer_content != null){
                          this.$set(this.answer_num,index,this.answer_num[index]+1);
                        }
                        let d = new Date(parseInt(rtnData.data[i].create_time) * 1000);
                        
                        //console.log(nowDay.getTime());
                        if(d.getFullYear() == nowDay.getFullYear() && d.getMonth() ==nowDay.getMonth() && d.getDate() == nowDay.getDate()){
                          today_ask_num++;
                          if(rtnData.data[i].answer_content != null){
                            today_answer_num++;
                          }
                        }
                      }
                      if(today_ask_num!=0){
                        this.$set(this.answer_rate,index,parseInt(today_answer_num/today_ask_num*100));
                      }
                    })
              }
              Indicator.close();
            })
        }else{
          this.$router.push('/login');
        }
      },
      next_step:function(){
        this.step=2;
        this.init();
      },
      go_vip_page:function (n) {
        this.$router.push('/tutorDetails/'+n);      
      },
      answerToSubmit:function(id,price){
        this.$router.push({path:'/answerToSubmit',query:{id:id,type:'ask',price:price,is_public:this.is_public,ask_content:this.ask_content}})      
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
  @import '../assets/css/ask.css';
</style>