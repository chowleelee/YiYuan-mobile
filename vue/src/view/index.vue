<template>
  <div class="index" :class="{'overflow':isSearch}">
    <mysearch v-on:child-say="listenToChild"></mysearch>
    <mt-swipe>
      <mt-swipe-item v-for="item in slider">
        <img :src="$sourceUrl+'/img/'+item.slider_img" @click="toSmallTalkDetails(item.smalltalk_id)" alt="">
      </mt-swipe-item>
    </mt-swipe>
    <div class="content">
      <div class="menu">
        <ul>
          <li><router-link to='/findExpert'><img src="../assets/img/index_expert.png" alt=""><span>找专家</span></router-link></li>
          <li><router-link to='/ask-quickly'><img src="../assets/img/index_ask.png" alt=""><span>快问</span></router-link></li>
          <li><router-link to='/talk'><img src="../assets/img/index_talk.png" alt=""><span>小讲</span></router-link></li>
        </ul>
      </div>
      <div class="topLine">
        <div class="container">
          <h4 class="headline">分答头条 | 免费</h4>
          <ul class="topLine-list">
            <li @click="toHeadlineDetails(item.id)" v-for="(item,key) in headline_list">

              <img class="topLine-img" :src="$sourceUrl+'/img/'+item.heading_img" alt="" v-if="key == 0">
              <img class="topLine-play" src="../assets/img/index_play.png" alt="" v-if="key != 0">
              <div class="topLine-basicInfo">
                <p class="topLine-title">{{item.title}}</p>
                <span class="topLine-author" v-if="key == 0">{{item.real_name}} | {{item.identity}}</span> 
              </div>
            </li>
          </ul>
           <router-link to="/headline/all"><p class="unread">今日未读<span>{{nowday_headline_noreadnum}}</span></p></router-link>
         
        </div>
      </div>
      <div class="smallTalk">
        <div class="container">
          <h4 class="headline">小讲 | 30分钟精品语音干货</h4>
          <div class="smallTalk-content">
            <mt-navbar v-model="selected" class="smallTalk-cate">
              <mt-tab-item :id="'tab_'+key"   v-for="(item,key) in cat_list">{{item.cate_name}}</mt-tab-item>
            </mt-navbar>
            <!-- tab-container -->
            <mt-tab-container v-model="selected"   v-infinite-scroll="loadMore"  infinite-scroll-distance="10">
              <mt-tab-container-item :id="'tab_'+key" v-for="(item,key) in cat_list">
                <mt-cell v-for="(item_class,index) in class_list[key]">
                  <div class="smallTalk-details" @click="toSmallTalkDetails(item_class.id)">
                    <div class="head-img">
                      <img :src="$sourceUrl+'/img/'+item_class.head_img" alt="">
                    </div>
                    <div class="info">
                      <h5 class="title"><span class="new" v-if="isNew[key][index]">新</span>{{item_class.title}}</h5>
                      <div class="tutor">
                        <span>{{item_class.real_name}}  {{item_class.identity}}</span>
                      </div>              
                      <div class="join_number">
                        <span v-if="key == 0">{{item_class.cate_name}}</span>{{item_class.join_num}}人参加
                      </div>
                    </div>
                  </div>
                </mt-cell>
              </mt-tab-container-item>
            </mt-tab-container>
            <router-link to="/talk"> <p class="more" v-if="selected == 'tab_0'">查看更多<span>></span></p></router-link>
          </div>


        </div>
      </div>

    </div>


  </div>
</template>

<script type="es6">
  import {mapState,mapMutations,mapGetters} from 'vuex';
  import mysearch from '@/components/search'
  import { Indicator } from 'mint-ui'
  export default {
    data () {
      return {
        headline_list:[],
        selected : 'tab_0',//当前是哪个导航
        cat_page : [],//各分类的页码
        cat_list : [{cate_name:'推荐'}],
        class_list : [],//各分类下的课程列表
        lock : true, //异步锁
        allLoaded:false,
        nowday_headline_noreadnum:0,
        isNew:[],
        newDay:3,//规定发表几天内的小讲是新的
        isSearch:false,
        slider:[]
      }
    },
    created(){
      this.init()
    },
    watch:{
      selected:function (newValue) {
        this.changeCate(newValue.split("_")[1],'watch')
      }
    },
    computed:{
      ...mapState(['info']),
      ...mapGetters(['isLogin']) 
    },
    methods:{
      init:function(){
        Indicator.open();
        this.$http.get('slider').then(rtnData=>{
          this.slider = rtnData.data;
        });
        this.$http
          .get('/headline',{params:{id:'all'}})
          .then(rtnData=>{
               //console.log(rtnData)
              var nowday_headline_list_id = [];
              let nowDay = new Date();
              for(let i=0;i<rtnData.data.data.length;i++){
                let d = new Date(parseInt(rtnData.data.data[i].create_time) * 1000)
                
                //console.log(nowDay.getTime());
                if(d.getFullYear() == nowDay.getFullYear() && d.getMonth() ==nowDay.getMonth() && d.getDate() == nowDay.getDate()){
                  nowday_headline_list_id.push(rtnData.data.data[i].id)
                }
                if(i<3){
                   this.$set(this.headline_list,i,rtnData.data.data[i])
                }
               
              }
              if(this.isLogin){
                 //console.log(nowday_headline_list_id);
                 var nowday_headline_num = nowday_headline_list_id.length;
                 this.$http
                 .get('/headlineRead',{params:{id:this.info.user_id,type:'today'}})
                 .then(rtnData=>{
                  //console.log(rtnData.data)
                  for(let i=0;i<rtnData.data.length;i++){
                    if(nowday_headline_list_id.indexOf(rtnData.data[i].id)!=-1){
                      nowday_headline_num -- ;
                    }
                  }
                  //console.log(nowday_headline_num);
                  this.nowday_headline_noreadnum = nowday_headline_num;
                });
              }
              Indicator.close();
             
          });  
        this.$http
          .get('/smalltalkCate')
          .then(rtnData=>{
               //console.log(rtnData)
              for(let i=0;i<rtnData.data.length;i++){
                this.$set(this.cat_list,i+1,rtnData.data[i])
              }
              for (var i=0;i<this.cat_list.length+1;i++) {
                this.cat_page.push({page:1})
              }
          }).then(()=>{
             this.changeCate(0)
          })
      },
      changeCate:function (index,handType) {
        var nowTime = new Date().getTime();
        if (typeof(this.class_list[index]) == 'undefined' || handType =='loadmore') {
          this.lock = false
          if(index == 0){
            Indicator.open();
           this.$http
            .get('/smalltalk',{params:{page:this.cat_page[index].page}})
            .then(rtnData=>{
              //console.log(rtnData.data.data)
              //console.log(this.cat_page[index].page )
              if (this.cat_page[index].page == 1) {
                this.$set(this.class_list,index,rtnData.data.data)
              }else{
                this.class_list[index].push(...rtnData.data.data) 
              }

              //获取每个小讲是否是新的
              for(var i=0;i<rtnData.data.data.length;i++){
                // console.log(parseInt(nowTime/1000), rtnData.data.data[i].create_time)
                var time = parseInt((nowTime- rtnData.data.data[i].create_time*1000)/1000/60/60/24);
                //console.log(time)
               
                if (this.cat_page[index].page == 1) {
                  if(typeof(this.isNew[index]) == 'undefined'){
                    if(time<this.newDay){
                      this.$set(this.isNew,index,[true])
                    }else{
                      this.$set(this.isNew,index,[false])
                    }
                  }else{
                    if(time<this.newDay){
                      this.isNew[index].push(true)
                    }else{
                      this.isNew[index].push(false)
                    }
                  }
                }else{
                  if(time<this.newDay){
                    this.isNew[index].push(true)
                  }else{
                    this.isNew[index].push(false)
                  }
                }
              }

              //console.log(this.class_list[index]);
              ++this.cat_page[index].page;

              // 没有数据的时候，则无需loadmore
              if (this.cat_page[index].page > rtnData.data.last_page) {
                this.cat_page[index].page = -1
              }
              this.lock = true
              //this.$set(this.class_list,index, rtnData.data)     
              Indicator.close();
            })  
          }else{
            Indicator.open();
            let cat_id = this.cat_list[index]['id']
            //console.log(cat_id);
            this.$http
              .get('/smalltalk/'+cat_id,{params:{page:this.cat_page[index].page,type:'cate'}})
              .then(rtnData=>{
                if (this.cat_page[index].page == 1) {
                  this.$set(this.class_list,index, rtnData.data.data)
                }else{
                  this.class_list[index].push(...rtnData.data.data) 
                }

                for(var i=0;i<rtnData.data.data.length;i++){

                  var time = parseInt((nowTime- rtnData.data.data[i].create_time*1000)/1000/60/60/24);
                  if (this.cat_page[index].page == 1) {
                    if(typeof(this.isNew[index]) == 'undefined'){
                      if(time<this.newDay){
                        this.$set(this.isNew,index,[true])
                      }else{
                        this.$set(this.isNew,index,[false])
                      }
                    }else{
                      if(time<this.newDay){
                        this.isNew[index].push(true)
                      }else{
                        this.isNew[index].push(false)
                      }
                    }
                  }else{
                    if(time<this.newDay){
                      this.isNew[index].push(true)
                    }else{
                      this.isNew[index].push(false)
                    }
                  }
                }

                ++this.cat_page[index].page;

                // 没有数据的时候，则无需loadmore
                if (this.cat_page[index].page > rtnData.data.last_page) {
                  this.cat_page[index].page = -1
                }
                this.lock = true
                Indicator.close();
                //this.$set(this.class_list,index, rtnData.data)     
              })
          }
        }

      },
      loadMore:function () {
        // 判断异步ajax到底有没有请求数据完毕
        
        let index = this.selected.split("_")[1]
        //console.log(index)
        // 无限滚动的
        //console.log(this.cat_page[index].page)
        if(index == 0){
          return;
        }
        if (this.cat_page[index].page >=1 && this.lock == true) {
          //console.log(2)
          this.changeCate(index,'loadmore')
        }
        
        
      },
      toHeadlineDetails:function(id){
        if(this.isLogin){
          this.$http
             .get('/headlineRead/'+id,{params:{uid:this.info.user_id}})
             .then(rtnData=>{
              //console.log(rtnData.data)
              if(rtnData.data == null){
                this.$http
                .post('/headlineRead',{'uid':this.info.user_id,'id':id})
                .then(rtnData=>{
                })
              }
            })
        }
        this.$router.push('/headline-details/'+id)
      },
      toSmallTalkDetails:function(id){
        this.$router.push('/stalkteacher/'+id)
      },
      listenToChild:function(isSearch){
        this.isSearch = isSearch;
      }
    },
    components:{
      mysearch
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/index.css';

</style>