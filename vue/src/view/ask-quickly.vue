<template>
  <div class="ask-quickly">
    <div class="reward-tags">
      <ul class="reword-tags-content">
          <li v-for="(item,index) in ask_quickly_cate">
          <a>
            <div @click="to_rewards(item.id)" class="rewards-main">
              <img :src="$sourceUrl+'/img/'+item.cate_img" alt=""> 
              <p class="title">{{item.cate_name}}</p>
              <p class="info">{{item.point}}</p>
            </div>
              
            <div class="to-reward-wrap"> 
              <span class="ask-btn" @click="to_reward_wrap(item.id)">提问</span>
            </div>
          </a>
        </li>
      </ul>
    </div>
    <div class="rewards-ques">
      <div class="rewards-title">
        <span :class="{selected:selected==1}" @click="select(1)">最新</span>
        <span class="line">|</span>
        <span :class="{selected:selected==2}"  @click="select(2)">已解答</span>
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
                <span v-if="item.status==1">还剩{{[item.create_time,item.duration]|remainTime}}小时</span>
                <span v-if="item.status==2">已解决</span>
                <span v-if="item.status==3">已过期</span>
                <span v-if="item.status==4">已关闭</span>
                <span v-if="item.status==1">{{ask_quickly_answer_num[quickly_status_index[index]]}}人已抢答</span>
                <span v-if="item.status==2">{{ask_quickly_answer_num[quickly_status_index[index]]}}人被选中</span>
                <!-- <span class="replay"  @click="answer(item.id)" v-if="info.vip_id">抢答</span> -->
              </div>
              <div class="rewards-tip" v-if="ask_quickly_answer_num[quickly_status_index[index]] !=0">
                <div class="info" v-for="(answer_item,k) in ask_quickly_answer">
                   <span class="tutor-info" v-if="k == index">{{answer_item.real_name}}｜ {{answer_item.identity}} </span>
                    <span class="qiang" v-if="k == index">{{ask_quickly_answer_time[index]}}抢答</span>   
                </div>
              </div>
          </li>
      </ul>
    </div>
  </div>
</template>

<script type="es6">
import {mapState,mapMutations,mapGetters} from 'vuex';
import { Indicator } from 'mint-ui'

  export default {
    data () {
      return {
        selected:1,
        quickly_status_index:[],
        ask_quickly_cate:[],
        ask_quickly_list:[],
        all_quickly:[],
        answered_quickly:[],
        ask_quickly_answer:[],
        ask_quickly_answer_num:[],
        ask_quickly_answer_time:[],
        ask_quickly_page:1,
        ask_quickly_num:0,
        lock : true, //异步锁
        vip_list:[],
        ask_answer_list:[],
        answer_id:0,
        nTime:0
      }
    },
    created(){
      this.$http.get("quickaskCate").then(rtnData=>{
          this.nTime=parseInt(new Date().getTime()/1000);
          this.ask_quickly_cate=rtnData.data;
      })
      this.getAskQuickly();
    },
    filters: {
      remainTime:function([value,duration]){
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
        this.$http.get("quickask",{params:{page:this.ask_quickly_page}}).then(rtnData=>{
          if(typeof(this.all_quickly[0]) == 'undefined'){
            this.ask_quickly_num = 0;
          }else{
             this.ask_quickly_num = this.all_quickly[0].length
          }
          for(var i=0;i<rtnData.data.data.length;i++){
            if(typeof(this.all_quickly[0]) == 'undefined'){
              this.all_quickly[0]=[rtnData.data.data[i]];
              this.all_quickly[1]=[this.ask_quickly_num+i];
            }else{
              this.all_quickly[0].push(rtnData.data.data[i]);
              this.all_quickly[1].push(this.ask_quickly_num+i);
            }
            if(rtnData.data.data[i].status == 2){
              if(typeof(this.answered_quickly[0]) == 'undefined'){
                this.answered_quickly[0]=[rtnData.data.data[i]];
                this.answered_quickly[1]=[this.ask_quickly_num+i];
              }else{
                this.answered_quickly[0].push(rtnData.data.data[i]);
                this.answered_quickly[1].push(this.ask_quickly_num+i);
              }
            }
            if (this.ask_quickly_page >= rtnData.data.last_page) {
              this.ask_quickly_page = -1;
            }
            this.lock = true;
            this.ask_quickly_answer_num.push(0)
            this.ask_quickly_answer_time.push(0)
            let index = i+this.ask_quickly_num;
            this.$http.get("/quickaskAnswer/"+rtnData.data.data[i].id).then(rtnData=>{   
                if(rtnData.data.length!=0){
                  this.$set(this.ask_quickly_answer,index,rtnData.data[0])
                  this.$set(this.ask_quickly_answer_num,index,rtnData.data.length)
                  var time = parseInt((rtnData.data[0].create_time - this.all_quickly[0][index].create_time))*1000;
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
          }
          //if(this.ask_quickly_page == 1){
            if(this.selected == 1){
              this.ask_quickly_list = this.all_quickly[0];
            
              this.quickly_status_index = this.all_quickly[1]
            }else{
              this.ask_quickly_list = this.answered_quickly[0]
              
              this.quickly_status_index = this.answered_quickly[1]
            }

          //}
          for(let i = 0; i < this.ask_quickly_list.length; i++){
            if(this.ask_quickly_list[i].user_id == this.info.user_id){
              this.ask_quickly_list[i].user_name = '我'
            }else{
              if(this.ask_quickly_list[i].is_anonymous==1){
                this.ask_quickly_list[i].user_name = '匿名用户'
              }
            }
          }
          Indicator.close();
        })
      },
      loadMore:function(){
        if(this.ask_quickly_page>=1 && this.lock == true){
          this.ask_quickly_page++;
          this.getAskQuickly();
        }
      },
      select:function(index){
        this.selected = index;
        if(index == 1){
          this.ask_quickly_list = this.all_quickly[0] || [];
          this.quickly_status_index =  this.all_quickly[1] || [];
        }else if(index == 2){
          this.ask_quickly_list = this.answered_quickly[0] || [];

          this.quickly_status_index =  this.answered_quickly[1] || [];
        }
      },
      to_reward_wrap:function(n){
        if(this.isLogin){
          this.$router.push('/reward-wrap/'+n)
        }else{
          this.$router.push('/login')
        }
      },
      to_reward_details:function(id,n){
        if(this.info.user_id == id){
          this.$router.push('/my-quickask/'+n)
        }else{
          this.$router.push('/reward-details/'+n)
        }
      },
      to_rewards:function(n){
        this.$router.push('/rewards/'+n)
      }
      /*answer:function(id){ 
        if(this.isLogin){
          this.$router.push({path:'/question/',query:{id:id,type:'quickask'}}) 
          //this.$router.push('/question/'+id);
        }else{
          this.$router.push('/login')
        }
      }*/
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/ask-quickly.css';

</style>