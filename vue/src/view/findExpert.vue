<template>
   
      <div class="findExpert">
        <mysearch></mysearch>
          <div class="findExpert-top">
            <router-link to="/category/1" class="findExpert-top-right">
                <span>职场</span>
            </router-link>
            <router-link to="/category/2" class="findExpert-top-left">
                <span>互联网</span>
            </router-link>
          </div>

            <div class="findExpert-rank" v-for="(rankItem,key) in list">
              <div class="findExpert-rank-title">{{rankItem.listName}}榜</div>
              <ul>
                <li v-for="(item,index) in rankItem.data">
                  <router-link :to="'/tutorDetails/'+item.id">
                    <div class="findExpert-avatar">
                      <img :src="$sourceUrl+'/img/'+item.head_img">
                      <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true" v-if="item.is_real==2">
                    </div>
                    <div class="findExpert-content">
                      <p class="username">{{item.real_name}}</p>
                      <p class="description">{{item.identity}}</p>
                      <p class="description" v-show="rankItem.listType == 'newList'">{{item.introduce}}</p>
                      <p class="description" v-show="rankItem.listType == 'hotList'">
                        <span>{{item.answer_num}}</span>
                        <span>个回答，</span>
                        <span>{{item.listen_num}}</span>
                        <span>人收听</span>
                      </p>
                    </div>
                  </router-link>
                  <div class="findExpert-fll" v-if="info.vip_id!=item.id">
                    <div class="findExpert-follow" @click="hasFollowed(key,index)" v-show="!rankItem.followed[index]">
                      <img src="../assets/img/findExpert-listen.png">
                      <span>收听</span>
                    </div>
                    <div class="findExpert-followed" v-show="rankItem.followed[index]">
                      已收听
                    </div>
                  </div>
                </li>
                <li class="findExpert-more">
                  <router-link :to="'/completeList/'+rankItem.listType">查看更多{{rankItem.listName}}答主</router-link>
                </li>
              </ul>
            </div>
      </div>
</template>

<script type="es6">

import mysearch from '@/components/search'
import {mapState,mapMutations,mapGetters} from 'vuex';
import { Indicator } from 'mint-ui'
export default {
  data () {
    return {
      list:[
        {listType:'newList',listName:'新晋',data:[],followed:[],listen_list:[]},{listType:'hotList',listName:'人气',data:[],followed:[],listen_list:[]}
        ], //榜单列表
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
      //获取专家列表
      Indicator.open();
      this.$http
        .get('vip',{params:{type:'newList',id:this.info.user_id}})
        .then(rtnData=>{
          for(var i=0;i<3;i++){
            this.list[0].data.push(rtnData.data[0][i]);

          }
          for(var i=0;i<rtnData.data[1].length;i++){
            this.list[0].listen_list.push(rtnData.data[1][i].vip_id);
          }
          for(var i=0;i<this.list[0].data.length;i++){
            if(this.list[0].listen_list.indexOf(this.list[0].data[i].id) == -1){
              this.list[0].followed.push(false);
            }else{
              this.list[0].followed.push(true);
            }
          }
      });

      this.$http
        .get('vip',{params:{type:'hotList',id:this.info.user_id}})
        .then(rtnData=>{
          for(var i=0;i<3;i++){
            this.list[1].data.push(rtnData.data[0][i]);
          }
          for(var i=0;i<rtnData.data[1].length;i++){
            this.list[1].listen_list.push(rtnData.data[1][i].vip_id);
          }
          for(var i=0;i<this.list[1].data.length;i++){
            if(this.list[1].listen_list.indexOf(this.list[1].data[i].id) == -1){
              this.list[1].followed.push(false);
            }else{
              this.list[1].followed.push(true);
            }
          }
          Indicator.close();
      });
      //Indicator.close();
    },
    hasFollowed:function(key,index){
      if(this.isLogin){
        this.$http
        .put('/vipListen/'+this.list[key].data[index].id,{followed:!this.list[key].followed[index],uid:this.info.user_id});
        this.$http
        .put('/vip/'+this.list[key].data[index].id,{followed:this.list[key].followed[index]});
        this.$set(this.list[key].followed,index,true)
      }else{
        this.$router.push('/login');
      }
    }
  },
  components:{
    mysearch
  }
   
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  @import '../assets/css/findExpert.css';
  


</style>