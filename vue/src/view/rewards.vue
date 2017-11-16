<template>
  <div class="rewards-container">
    <div class="rewards-header">
      <div class="banner">
        <!-- 情感小烦恼 -->{{title[0]}}
        <img :src="imgUrl" alt="">
        <!-- 专家来开导 -->{{title[1]}}
      </div>
      <a @click="to_reward_wrap()" class="askBtn"><span class="icon-pencil"></span>我也来问</a>
      <div class="nav-list">
        <ul>
          <li><img src="../assets/img/reward1.jpg" alt="">保护隐私</li>
          <li><img src="../assets/img/reward1.jpg" alt="">多角度分析</li>
          <li><img src="../assets/img/reward1.jpg" alt="">快速回复</li>
        </ul>
      </div>
      
    </div>
     <ul class="rewards-list" v-infinite-scroll="loadMore"  infinite-scroll-distance="10">
          <li v-for="(item,index) in ask_quickly_list">
              <div class="rewards-head">
                <span class="user">{{item.user_name}}</span>
                <span class="price">￥{{item.price}}</span>
              </div>
              <div class="rewards-content">
                <p @click="to_reward_details(item.user_id,item.id)">{{item.content}}</p>
              </div>
              <div class="rewards-footer">
                <span v-if="item.status==1">还剩{{[item.create_time,item.duration] | remainTime}}小时</span>
                <span v-if="item.status==2">已解决</span>
                <span v-if="item.status==3">已过期</span>
                <span v-if="item.status==1">{{ask_quickly_answer_num[index]}}人已抢答</span>
                <span v-if="item.status!=1">{{ask_quickly_answer_num[index]}}人被选中</span>
                <!-- <span class="replay"  @click="to_login()">抢答</span> -->
              </div>

              <div class="rewards-tip" v-if="ask_quickly_answer_num[index] !=0">
                 
                <div class="info" v-for="(answer_item,k) in ask_quickly_answer">

                   <span class="tutor-info" v-if="k == index">{{answer_item.real_name}}｜ {{answer_item.identity}} </span>
                    <span class="qiang" v-if="k == index">{{ask_quickly_answer_time[index]}}抢答</span>   
                </div>
              </div>
          </li>
      </ul>
 
  </div>
</template>

<script type="es6">
import {mapState,mapMutations,mapGetters} from 'vuex';
import { Indicator } from 'mint-ui'
  
  export default {
    data () {
      return {
        ask_quickly_answer:[],
        ask_quickly_answer_num:[],
        ask_quickly_answer_time:[],
        ask_quickly_page:1,
        ask_quickly_num:0,
        lock : true, //异步锁
        title:[],
        ask_quickly_cate:[],
        ask_quickly_list:[],
        cate_id:0,
        nTime:0,
        imgUrl: ""
      }
    },
    created(){
      this.nTime=parseInt(new Date().getTime()/1000);
      this.$http.get("quickaskCate").then(rtnData=>{
          this.ask_quickly_cate=rtnData.data;
          this.cate_id=this.$route.params.id-1;
          this.title=this.ask_quickly_cate[this.$route.params.id-1].cate_name.split("，");
          this.imgUrl=this.$sourceUrl+'/img/'+this.ask_quickly_cate[this.$route.params.id-1].cate_img;
      })
      this.getAskQuickly()
     
    },
    watch:{

    },
    filters: {
      remainTime:function([value,duration]){
        //console.log(value,duration)
        var nowTime = new Date().getTime();
        var time = parseInt((nowTime - value*1000)/1000/60/60);
        var remainTime = duration - time;
        return remainTime = remainTime>0?remainTime:0;
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    methods:{
      getAskQuickly:function(){
        Indicator.open();
        this.lock = false
     
        this.$http.get("/quickask/"+this.$route.params.id,{params:{page:this.ask_quickly_page,type:'cate'}}).then(rtnData=>{
          this.ask_quickly_num = this.ask_quickly_list.length;
          for(var i=0;i<rtnData.data.data.length;i++){
            this.ask_quickly_list.push(rtnData.data.data[i]);
            if (this.ask_quickly_page >= rtnData.data.last_page) {
              this.ask_quickly_page = -1;
            }
            this.lock = true;
            this.ask_quickly_answer_num.push(0)
            this.ask_quickly_answer_time.push(0)
            this.$http.get("/quickaskAnswer/"+rtnData.data.data[i].id).then(rtnData=>{     
              if(rtnData.data.length!=0){
                for(var i=this.ask_quickly_num;i<this.ask_quickly_list.length;i++){

                  if(this.ask_quickly_list[i].id == rtnData.data[0].quickask_id){
                    var index = i;
                    break;
                  }
                }
                this.$set(this.ask_quickly_answer,index,rtnData.data[0])
                this.$set(this.ask_quickly_answer_num,index,rtnData.data.length)
                var time = parseInt((rtnData.data[0].create_time -  this.ask_quickly_list[index].create_time))*1000;
                var answerTime = '';
                if(time/1000 > 60){
                  if(time/1000/60>60){
                    if(time/1000/60/60>24){
                      answerTime = Math.ceil(time/1000/60/60/24)+"天内";
                    }else{
                      answerTime = Math.ceil(time/1000/60/60)+"小时内";
                    }                   
                  }else{
                    answerTime = Math.ceil(time/1000/60)+'分钟内';
                  }
                }else{
                  answerTime = '1分钟内';
                }
                this.$set(this.ask_quickly_answer_time,index,answerTime);
              }
            })
            Indicator.close();
          }
          for(let i = 0; i < this.ask_quickly_list.length; i++){
            if(this.ask_quickly_list[i].user_id == this.info.user_id){
              this.ask_quickly_list[i].user_name = '我'
            }else{
              if(this.ask_quickly_list[i].is_anonymous==1){
                this.ask_quickly_list[i].user_name = '匿名用户'
              }
            }
          }
        })
      },
      loadMore:function(){
        //console.log(1)
        if(this.ask_quickly_page>=1 && this.lock == true){
          this.ask_quickly_page++;
          this.getAskQuickly();
        }
      },
      to_reward_details:function(id,n){
        if(this.info.user_id == id){
          this.$router.push('/my-quickask/'+n)
        }else{
          this.$router.push('/reward-details/'+n)
        }
      },
      to_login:function(){
        this.$router.push('/login')
      },
      to_reward_wrap:function(){
        if(this.isLogin){
          this.$router.push('/reward-wrap/'+this.$route.params.id)
        }else{
          this.$router.push('/login')
        }
      }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/rewards.css';


</style>