<template>
  <div class="talk container">
    <div class="smallTalk">
      <div class="container">
        <h4 class="subject">
          专题
          <router-link to='/allspeech'><span class="allsub">全部专题</span></router-link>
        </h4>
        <ul class="sub_pic">
          <!-- <li class="pichild" v-for="index in subimg"></li> -->
          <li v-for="(item,key) in speechimg" @click="toStalkcon(item.id)">
            <img :src="$sourceUrl+'/img/'+item.special_img" class="speechimg">
          </li>
        </ul>
        <div class="smallTalk-content">
          <mt-navbar v-model="selected" class="smallTalk-cate">
            <mt-tab-item :id="'tab_'+key" v-for="(item,key) in cat_list">{{item.cate_name}}</mt-tab-item>
          </mt-navbar>
          <!-- tab-container -->
          <mt-tab-container v-model="selected" v-infinite-scroll="loadMore"  infinite-scroll-distance="10">

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
        </div>

      </div>
    </div>  
  </div>
</template>

<script type="es6">
//import { InfiniteScroll } from 'mint-ui';
import { Indicator } from 'mint-ui'
export default {
  data () {
    return {
      speechimg:[],
      // subimg : [
      //   {
      //     img:'../assets/img/stalk1.jpg'
      //   },
      //   {
      //     img:'../assets/img/stalk2.jpg'
      //   },
      //   {
      //     img:'../assets/img/stalk3.jpg'
      //   },
      //   {
      //     img:'../assets/img/stalk4.jpg'
      //   }
      // ],
      selected : 'tab_0',//当前是哪个导航
      cat_page : [],//各分类的页码
      cat_list : [{cate_name:'推荐'}],
      class_list : [],//各分类下的课程列表
      lock : true, //异步锁
      allLoaded:false,
      isNew:[],
      newDay:3,//规定发表几天内的小讲是新的
    }
  },

  components:{

  },
  created(){
    this.init()
  },
  watch:{
    selected:function (newValue) {
      this.changeCate(newValue.split("_")[1],'watch')
    }
  },
  methods:{
    init:function(){
      this.$http
       .get('special',{params:{type:'recommend_special'}})
       .then(rtnDatab=>{
        this.speechimg=rtnDatab.data.slice(0,4)
      })
      this.$http
        .get('/smalltalkCate')
        .then(rtnData=>{
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
          let cat_id = this.cat_list[index]['id']
          //console.log(cat_id);
          Indicator.open();
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
              //this.$set(this.class_list,index, rtnData.data) 
              Indicator.close();    
            })
        }
      }

    },
    loadMore:function () {
      // 判断异步ajax到底有没有请求数据完毕
      
      let index = this.selected.split("_")[1]
      //console.log(index)
      // 无限滚动的
      if(typeof(this.class_list[index]) == 'undefined'){
        return;
      }
      //console.log(this.cat_page[index].page)
      if (this.cat_page[index].page >=1 && this.lock == true) {
        //console.log(2)
        this.changeCate(index,'loadmore')
      }
      
      
    },
    toSmallTalkDetails:function(id){
      this.$router.push('/stalkteacher/'+id)
    },
    stalkhome:function(n){
      //console.log(1)
      this.$router.push('/stalkcon/'+n)
    },
    toStalkcon:function(n){
      console.log(1)
      this.$router.push('/stalkcon/'+(n))
    },
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
  /*@import '../assets/css/common.css';*/
  @import '../assets/css/stalk.css';
</style>