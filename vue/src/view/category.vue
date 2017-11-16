<template>
      <div class="findExpert">
        <div class="findExpert-rank">
          <div class="category-rank-title">{{category_list.cate_name}}</div>
          <ul>
            <li v-for="(item,index) in vip_list">
              <router-link :to="'/tutorDetails/'+item.id">
                <div class="findExpert-avatar">
                  <img :src="$sourceUrl+'/img/'+item.head_img">
                  <img src="../assets/img/listen-answer-avatar-right.jpg" class="avatar-true" v-if="item.is_real==2">
                </div>
                <div class="findExpert-content">
                  <p class="username">{{item.real_name}}</p>
                  <p class="description">{{item.identity}}</p>
                  <p class="description">{{item.introduce}}</p>
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
      vip_list:[],
      category_list:{},
      listen_list:[],   //收听答主列表
      followed:[]
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
      //获取答主列表
      Indicator.open();
      this.$http
      .get('vip',{params:{type:'vpcate',cate_id:this.$route.params.id,user_id:this.info.user_id}})
      .then(rtnData=>{
        this.vip_list = rtnData.data[0];
        for(var i=0;i<rtnData.data[1].length;i++){
            this.listen_list.push(rtnData.data[1][i].vip_id);
        }

        for(var i=0;i<this.vip_list.length;i++){
          if(this.listen_list.indexOf(this.vip_list[i].id) == -1){
            this.followed.push(false);
          }else{
            this.followed.push(true);
          }
        }
        Indicator.close();
      });
      //获取分类列表
      this.$http
        .get('/vpcategory/'+this.$route.params.id)
        .then(rtnData=>{
          this.category_list = rtnData.data;
        })
    },
    hasFollowed:function(index){
        if(this.isLogin){
          this.$http
          .put('/vipListen/'+this.vip_list[index].id,{followed:!this.followed[index],uid:this.info.user_id});
          this.$http
          .put('/vip/'+this.vip_list[index].id,{followed:this.followed[index]});
          this.$set(this.followed,index,true)
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