<template>

  <div class="headline-details">
    <div class="headline-details-content ">
      <div class="container">
        <h3 class="headline-details-title">
        {{headline_details.title}}</h3>
        <div class="headline-details-author">
          <div class="head">
            <router-link :to="'/tutorDetails/'+headline_details.vip_id">
              <img :src="headline_details.head_img" class="head-img">
              <img src="../assets/img/true.jpg" class="true" alt="" v-if="headline_details.is_real==2">
            </router-link>
          </div>
          <div class="nameAndidentity">
            <p class="name">{{headline_details.real_name}}</p>
            <span class="identity">{{headline_details.identity}}</span>
          </div>
          <div class="headline-listen" v-if="info.vip_id!=headline_details.vip_id">
            <div class="tutorDetails-follow" @click="showIt()" v-show="!followed"></div>
            <div class="tutorDetails-followed" @click="showIt()" v-show="followed"></div>
          </div>
        </div>
        <!-- <audio class="headline-details-music"  controls="controls"
        src="/static/audio/yjm.mp3"></audio> -->

        <p class="summary">{{headline_details.summary}}</p>
        <div class="headline-details-article" v-html="headline_details.content">
        </div>
      </div>
     
    </div>
  
    <div class="other-headline">
      <div class="headline-titleAndall">
        <p class="other-headline-title">其他头条</p>
        <router-link to="/headline/all" class="to-allheadline"><span>查看全部 &gt;</span></router-link>
      </div>
      
      <ul class="headline-list">
        <li v-for="item in other_headline_list" @click="toDetails(item.id)">
         <div class="headline-name">
           <img src="../assets/img/index_play.png"  alt=""><span>{{item.title}}</span>
         </div>
         <p class="headline-author">{{item.real_name}} {{item.identity}}</p>
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
        headline_id:0,
        isListenClass:"",
        headline_details:{},
        other_headline_list:[],
        followed:false
      }
    },
    created(){
      this.init()
    },
    watch:{
      '$route' (to, from) {
        this.init()
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    methods:{
      init:function(){
        Indicator.open();
        this.headline_id = this.$route.params.id 
        this.$http
          .get('/headline/'+this.headline_id,{params:{type:'headline'}})
          .then(rtnData=>{
               //console.log(rtnData)
                this.headline_details = rtnData.data;
                this.headline_details.head_img = this.$sourceUrl+'/img/'+this.headline_details.head_img;
                //console.log(this.headline_details);
                this.$http
                  .get('/vip/'+this.headline_details.vip_id,{params:{user_id:this.info.user_id}})
                  .then(rtnData=>{
                    this.vip_details = rtnData.data[0];
                    // console.log(this.vip_details)
                    if(rtnData.data[1]){
                      this.followed = true;
                    }
                  });
                  Indicator.close();
              });
        this.$http
          .get('/headline/'+this.headline_id,{params:{type:'other_headline'}})
          .then(rtnData=>{
               //console.log(rtnData.data)
               this.other_headline_list=rtnData.data;
          });
      },
      showIt:function(){
        //console.log(this.$route.params.id)
        if(this.isLogin){
          this.$http
          .put('/vip/'+this.headline_details.vip_id,{followed:this.followed});
          this.$http
          .put('/vipListen/'+this.headline_details.vip_id,{uid:this.info.user_id,followed:!this.followed});
        }
        this.followed = !this.followed;
      },
      toDetails:function(id){
        this.$router.push('/headline-details/'+id)
      }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/headline-details.css';


</style>