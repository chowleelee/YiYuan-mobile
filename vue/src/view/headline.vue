<template>

  <div class="headline" v-infinite-scroll="loadMore"  infinite-scroll-distance="10">
    <h3 class="headline-title">全部头条</h3>
    <div class="everyday-headline" v-for="(item,key) in headline_day">
      <p class="headline-day">{{item}}</p>
      <ul class="headline-list">
        <li @click="toDetails(index,key)"  v-for="(item_headline,index) in headline_list[key]">
         <div class="headline-name">
           <img src="../assets/img/index_play.png"  alt=""><span :class="{'isRead':item_headline.isRead}">{{item_headline.title}}</span>
         </div>
         <p class="headline-author">{{item_headline.real_name}} {{item_headline.identity}}</p>
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
        is_read:false,
        headline_list:[],
        headline_day:[],
        headline_read_list_id:[],
        headline_page:1,
        lock : true, //异步锁
        headline_num:-1
      }
    },
    created(){
      this.init()
    },
    watch:{

    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    methods:{
      init:function(){
        this.vip_id = this.$route.params.id || 'all';
        if(this.isLogin){
          if(this.vip_id == 'all'){
            this.$http
             .get('/headlineRead',{params:{id:this.info.user_id,type:'all'}})
             .then(rtnData=>{
                for(var i=0;i<rtnData.data.length;i++){
                 this.headline_read_list_id.push(rtnData.data[i].id);
               }
             }).then(()=>{
              this.getHeadline();
            });
          }else{
            this.getHeadline();
          }
        }else{
          this.getHeadline();
        }
      },
      getHeadline:function(){
        this.lock = false;
        Indicator.open();
        this.$http
            .get('/headline',{params:{page:this.headline_page,id:this.vip_id}})
            .then(rtnData=>{
               if (this.headline_page >= rtnData.data.last_page) {
                this.headline_page = -1;
              }
              this.lock = true;
              let nowDay = new Date();
              let time=''
              for(let i=0;i<rtnData.data.data.length;i++){
                if(this.headline_read_list_id.indexOf(rtnData.data.data[i].id)!=-1){
                  rtnData.data.data[i].isRead =true; 
                }else{
                  rtnData.data.data[i].isRead =false; 
                }
                let d = new Date(parseInt(rtnData.data.data[i].create_time) * 1000)
                if(d.getFullYear() == nowDay.getFullYear() && d.getMonth() ==nowDay.getMonth() && d.getDate() == nowDay.getDate()){
                  time= '今天'
                }else if(d.getFullYear() == nowDay.getFullYear() && d.getMonth() ==nowDay.getMonth() && d.getDate() == nowDay.getDate()-1){
                  time= '昨天'
                }else{
                  time =  (d.getFullYear()) + '-' + (d.getMonth() + 1 > 9 ? d.getMonth() + 1 : '0' + (d.getMonth() + 1)) + '-' + (d.getDate() > 9 ? d.getDate() : '0' + d.getDate())
                }
                
                if(this.headline_day.indexOf(time)==-1){
                  this.headline_num++
                  this.headline_day.push(time)   
                }
               
                if(typeof(this.headline_list[this.headline_num]) == 'undefined'){
                    //rtnData.data.data[i].isRead =this.headline_read_list[i]; 
                    this.headline_list.push([rtnData.data.data[i]])
                }else{
                    this.headline_list[this.headline_num].push(rtnData.data.data[i])
                }
              }
              Indicator.close();
            })
      },
      loadMore:function(){
        if(this.headline_page>=1 && this.lock == true && this.headline_list.length!=0){
          this.headline_page++;
          this.getHeadline();
        }
      },
      toDetails:function(index,key){
        var id = this.headline_list[key][index].id;
        if(!this.headline_list[key][index].isRead && this.isLogin){
          this.$http
            .post('/headlineRead',{'uid':this.info.user_id,'id':id})
            .then(rtnData=>{
            })
          }
        this.$router.push('/headline-details/'+id)
      }
    }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >


@import '../assets/css/headline.css';


</style>