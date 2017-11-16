<template>
      <div class="findExpert">
        <div class="findExpert-rank">
          <div class="category-rank-title">{{list_name}}</div>
          <ul>
            <li v-for="(item,index) in vip_list">
              <router-link :to="'/tutorDetails/'+item.id">
                <div class="findExpert-avatar">
                  <img :src="$sourceUrl+'/img/'+item.head_img">
                  <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true" v-if="item.is_real">
                </div>
                <div class="findExpert-content">
                  <p class="username">{{item.real_name}}</p>
                  <p class="description">{{item.identity}}</p>
                  <p class="description" v-show="list_name == '新晋榜单'">{{item.introduce}}</p>
                      <p class="description" v-show="list_name == '人气榜单'">
                        <span>{{item.answer_num}}</span>
                        <span>个回答，</span>
                        <span>{{item.listen_num}}</span>
                        <span>人收听</span>
                      </p>
                </div>
              </router-link>
              <div class="findExpert-fll" v-if="info.vip_id!=item.id">
                <div class="findExpert-follow" @click="hasFollowed(index)" v-show="!followed[index]">
                  <img src="../assets/img/findExpert-listen.png">
                  <span>收听</span>
                </div>
                <div class="findExpert-followed" v-show="followed[index]">
                  已收听
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
      list_name:'',     //榜单名字
      vip_list:[],      //答主列表
      listen_list:[],   //收听答主列表
      followed:[]
    }
  },
  computed:{
    ...mapState(['info']),
    ...mapGetters(['isLogin']) 
  },
  created(){
    this.init();
  },
  methods:{
    init:function(){
      Indicator.open();
      this.$http
        .get('vip',{params:{type:this.$route.params.type,id:this.info.user_id}})
        .then(rtnData=>{
          // console.log(rtnData.data)
          this.vip_list = rtnData.data[0];
          // this.listen_list = rtnData.data[1];
          // console.log(this.vip_list);
          for(var i=0;i<rtnData.data[1].length;i++){
            this.listen_list.push(rtnData.data[1][i].vip_id);
          }
          for(var i=0;i<this.vip_list.length;i++){
            //console.log(this.listen_list.indexOf(this.vip_list[i].id))
            if(this.listen_list.indexOf(this.vip_list[i].id) == -1){
              this.followed.push(false);
            }else{
              this.followed.push(true);
            }
          }
          Indicator.close();
        });
      if(this.$route.params.type == 'newList'){
        this.list_name = '新晋榜单';
      }else if(this.$route.params.type == 'hotList'){
        this.list_name = '人气榜单';
      };
    },
    hasFollowed:function(index){
      if(this.isLogin){
        this.$http
        .put('/vipListen/'+this.vip_list[index].id,{followed:!this.followed[index],uid:this.info.user_id});
        this.$http
        .put('/vip/'+this.vip_list[index].id,{followed:this.followed[index]});

        this.followed[index] = true;
        this.$set(this.followed,index,this.followed[index]);
      }else{
        this.$router.push('/login');
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
 
  @import '../assets/css/findExpert.css';
  


</style>